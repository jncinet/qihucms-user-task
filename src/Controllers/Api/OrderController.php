<?php

namespace Qihucms\UserTask\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Qihucms\Currency\Currency;
use Qihucms\UserTask\Models\UserTask;
use Qihucms\UserTask\Models\UserTaskOrder;
use Qihucms\UserTask\Requests\StoreOrderRequest;
use Qihucms\UserTask\Resources\UserTaskOrder as UserTaskOrderResource;
use Qihucms\UserTask\Resources\UserTaskOrderCollection;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * 任务订单记录（任务发布者可查看）
     *
     * @param Request $request
     * @return UserTaskOrderCollection|JsonResponse
     */
    public function index(Request $request)
    {
        $limit = $request->get('limit', 15);

        $task = UserTask::find($request->get('user_task_id'));

        // 验证当前用户是否任务发布者
        if ($task && $task->user_id != Auth::id()) {
            return $this->jsonResponse([__('user-task::message.invalid_parameter')], '', 422);
        } else {
            $condition = [['user_task_id', '=', $task->id]];
        }

        if ($request->has('status')) {
            $condition[] = ['status', '=', $request->get('status')];
        }

        $items = UserTaskOrder::where($condition)->latest()->paginate($limit);

        return new UserTaskOrderCollection($items);
    }

    /**
     * 订单详细
     *
     * @param $id
     * @return UserTaskOrderResource|JsonResponse
     */
    public function show($id)
    {
        $item = UserTaskOrder::find($id);

        // 任务发布者和任务完成者可查看任务详细
        if ($item && ($item->user_id == Auth::id() || $item->user_task->user_id == Auth::id())) {
            return new UserTaskOrderResource($item);
        }

        return $this->jsonResponse([__('user-task::message.invalid_parameter')], '', 422);
    }

    /**
     * 审核任务订单
     *
     * @param Request $request
     * @param $id
     * @return JsonResponse|UserTaskOrderResource
     */
    public function audit(Request $request, $id)
    {
        $item = UserTaskOrder::find($id);

        // 当前用户是否任务发布者,且任务已经提交审核
        if ($item && $item->user_task && $item->user_task->user_id == Auth::id() && $item->status == 2) {
            $item->status = $request->input('status', 1) == 1 ? 1 : 3;
            if ($item->save()) {
                // 发放任务奖励
                $currency_result = Currency::entry(
                    Auth::id(),
                    $item->user_task->currency_type_id,
                    $item->user_task->amount,
                    'completed_task_reward',
                    $item->id,
                    __('user-task::message.completed_task_reward')
                );
                if ($currency_result !== 100) {
                    // 退回原状态
                    $item->status = 2;
                    $item->save();

                    $msg = __('currency::currency.message.' . $currency_result);
                    return $this->jsonResponse([$msg], '', 422);
                }

                return $this->jsonResponse(['id' => $id, 'status' => $item->status]);
            }
        }

        return $this->jsonResponse([__('user-task::message.invalid_parameter')], '', 422);
    }

    /**
     * 领取任务
     *
     * @param StoreOrderRequest $request
     * @return \Illuminate\Http\JsonResponse|UserTaskOrderResource
     */
    public function store(StoreOrderRequest $request)
    {
        $user_task_id = $request->input('user_task_id');
        $task = UserTask::find($user_task_id);
        if ($task->stock >= UserTaskOrder::where('user_task_id', $task->id)->where('status', 1)->count()) {
            // 如果任务状态为正常，但已完成则更新任务状态
            if ($task->status == 1) {
                $task->status = 2;
                $task->save();
            }

            return $this->jsonResponse([trans('user-task::message.task_end')], '', 422);
        }

        if (UserTaskOrder::where('user_task_id', $task->id)->where('user_id', Auth::id())->exists()) {
            return $this->jsonResponse([trans('user-task::message.task_received')], '', 422);
        }

        $data['user_task_id'] = $task->id;
        $data['user_id'] = Auth::id();
        $data['status'] = 0;
        $order = UserTaskOrder::create($data);

        return new UserTaskOrderResource($order);
    }

    /**
     * 完成任务
     *
     * @param StoreOrderRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(StoreOrderRequest $request, $id)
    {
        $data = $request->only(['user_task_id', 'files', 'remark']);
        $data['status'] = 2;

        $result = UserTaskOrder::where('id', $id)->where('user_id', Auth::id())->update($data);

        if ($result) {
            return $this->jsonResponse(['id' => $id]);
        }

        return $this->jsonResponse([__('user-task::message.submit_fail')], '', 422);
    }

    /**
     * 只能删除未完成任务
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        if (UserTaskOrder::where('id', $id)->where('user_id', Auth::id())->where('status', 0)->delete()) {
            return $this->jsonResponse(['id' => $id]);
        }

        return $this->jsonResponse([trans('user-task::message.delete_fail')], '', 422);
    }
}