<?php

namespace Qihucms\UserTask\Controllers\Api;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Qihucms\Currency\Currency;
use Qihucms\UserTask\Models\UserTask;
use Qihucms\UserTask\Models\UserTaskOrder;
use Qihucms\UserTask\Requests\StoreRequest;
use Qihucms\UserTask\Resources\UserTaskCollection;
use Qihucms\UserTask\Resources\UserTask as UserTaskResource;

class TaskController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api')->except(['index', 'show', 'findTaskByQ']);
    }

    /**
     * 后台选择任务
     *
     * @param Request $request
     * @return mixed
     */
    public function findTaskByQ(Request $request)
    {
        $q = $request->query('q');
        return UserTask::where('title', 'like', '%' . $q . '%')->select('id', 'title as text')->paginate();
    }

    /**
     * 我的任务
     *
     * @param Request $request
     * @return UserTaskCollection
     */
    public function userIndex(Request $request)
    {
        $limit = $request->get('limit', 15);

        $condition = [['user_id', '=', Auth::id()]];

        if ($request->has('status')) {
            $condition[] = ['status', '=', $request->get('status')];
        }

        if ($request->has('pay_status')) {
            $condition[] = ['pay_status', '=', $request->get('pay_status')];
        }

        $tasks = UserTask::where($condition)->withCount([
            // 领取数
            'user_task_orders',
            // 完成数
            'user_task_orders as completed_user_task_orders_count' => function (Builder $query) {
                $query->where('status', 1);
            },
            // 待审核数
            'user_task_orders as audit_user_task_orders_count' => function (Builder $query) {
                $query->where('status', 2);
            },
        ])->orderBy('id', 'desc')->paginate($limit);

        return new UserTaskCollection($tasks);
    }

    /**
     * 任务列表
     *
     * @param Request $request
     * @return UserTaskCollection
     */
    public function index(Request $request)
    {
        $limit = $request->get('limit', 15);

        $condition = [
            ['status', '=', 1],
            ['pay_status', '=', 1],
        ];

        if ($request->has('user_id')) {
            $condition[] = ['user_id', '=', $request->get('user_id')];
        }

        $tasks = UserTask::where($condition)->orderBy('id', 'desc')->paginate($limit);

        return new UserTaskCollection($tasks);
    }

    /**
     * 我的任务详细
     *
     * @param $id
     * @return UserTaskResource
     */
    public function userShow($id)
    {
        $task = UserTask::where('user_id', Auth::id())->where('id', $id)->with('user_task_orders')->first();

        return new UserTaskResource($task);
    }

    /**
     * 任务详细
     *
     * @param $id
     * @return UserTaskResource
     */
    public function show($id)
    {
        $task = UserTask::where('status', 1)->where('id', $id)->first();

        return new UserTaskResource($task);
    }

    /**
     * 发布任务
     *
     * @param StoreRequest $request
     * @return UserTaskResource|JsonResponse
     */
    public function store(StoreRequest $request)
    {
        $data = $request->only([
            'title', 'thumbnail', 'start_time', 'end_time', 'stock',
            'currency_type_id', 'amount', 'content', 'btn_text', 'link'
        ]);
        $data['user_id'] = Auth::id();
        $data['pay_status'] = 1;
        $data['status'] = 0;
        if (!isset($data['start_time']) || empty($data['start_time'])) {
            $data['start_time'] = now();
        }
        if (!isset($data['end_time']) || empty($data['end_time'])) {
            $data['end_time'] = now()->addDays(3);
        }

        $result = UserTask::create($data);

        if ($result) {
            // 需要托管的金额
            $fee = bcmul($data['stock'], $data['amount'], 2);

            if ($fee > 0) {
                // 读取会员账户托管任务奖金
                $currency_result = Currency::expend(
                    Auth::id(),
                    $data['currency_type_id'],
                    $fee,
                    'create_task',
                    $result->id,
                    __('user-task::message.create_task')
                );

                if ($currency_result !== 100) {
                    // 付款失败，删除订单
                    $result->delete();

                    $msg = __('currency::currency.message.' . $currency_result);
                    return $this->jsonResponse([$msg], '', 422);
                }
            }

            return new UserTaskResource($result);
        }

        return $this->jsonResponse([__('user-task::message.send_fail')], '', 422);
    }

    /**
     * 提前结束任务｜延长时间
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $data = $request->only(['end_time', 'status']);

        if ($request->has('end_time') && Carbon::parse($data['end_time'])->lte(Carbon::now())) {
            return $this->jsonResponse([__('user-task::message.datetime_error')], '', 422);
        }

        if ($request->has('status')) {
            $data['status'] = 2;
        }

        $task = UserTask::where('user_id', Auth::id())->where('id', $id)->first();

        if (isset($data['end_time'])) {
            $task->end_time = $data['end_time'];
        }

        if (isset($data['status'])) {
            $task->status = $data['status'];
        }

        if ($task->save()) {
            // 查询是否还有未结算的
            if (isset($data['status']) && $data['status'] == 2 && $task->pay_status == 1) {
                // 已完成的任务数
                $residue_count = UserTaskOrder::where('user_task_id', $id)->where('status', 1)->count();
                // 未完成的数量
                $residue_count = $task->stock - $residue_count;
                // 退款金额
                $refund_fee = bcmul($residue_count, $task->amount, 2);
                // 退款
                $currency_result = Currency::entry(
                    Auth::id(),
                    $task->currency_type_id,
                    $refund_fee,
                    'cancel_task_refund',
                    $task->id,
                    __('user-task::message.cancel_task_refund')
                );
                if ($currency_result !== 100) {
                    // 退回原状态
                    $task->status = 1;
                    $task->save();

                    $msg = __('currency::currency.message.' . $currency_result);
                    return $this->jsonResponse([$msg], '', 422);
                }
            }

            return $this->jsonResponse(['id' => $id, 'status' => $task->status]);
        }

        return $this->jsonResponse([__('user-task::message.submit_fail')], '', 422);
    }

    /**
     * 删除任务
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $task = UserTask::where('user_id', Auth::id())->where('id', $id)->first();

        if ($task->user_task_orders && $task->user_task_orders->count() > 0) {
            return $this->jsonResponse(
                [__('user-task::message.delete_fail_for_started')],
                '',
                422
            );
        }

        // 已托管奖金需退回
        if ($task->pay_status == 1) {
            // 退还金额
            $refund_fee = bcmul($task->stock, $task->amount, 2);

            $currency_result = Currency::entry(
                Auth::id(),
                $task->currency_type_id,
                $refund_fee,
                'cancel_task_refund',
                $task->id,
                __('user-task::message.cancel_task_refund')
            );

            if ($currency_result !== 100) {
                $msg = __('currency::currency.message.' . $currency_result);
                return $this->jsonResponse([$msg], '', 422);
            }
        }

        if ($task->delete()) {
            return $this->jsonResponse(['id' => $id]);
        }

        return $this->jsonResponse([__('user-task::message.delete_fail')], '', 422);
    }
}