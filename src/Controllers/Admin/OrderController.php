<?php

namespace Qihucms\UserTask\Controllers\Admin;

use App\Models\User;
use Qihucms\UserTask\Models\UserTask;
use Qihucms\UserTask\Models\UserTaskOrder;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class OrderController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '任务领取';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new UserTaskOrder());

        $grid->model()->orderBy('id', 'desc');

        $grid->filter(function ($filter) {

            // 去掉默认的id过滤器
            $filter->disableIdFilter();

            // 在这里添加字段过滤器
            $filter->like('name', __('user-task::order.name'));

        });

        $grid->column('id', __('user-task::order.id'));
        $grid->column('name', __('user-task::order.name'));

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(UserTaskOrder::findOrFail($id));

        $show->field('id', __('user-task::order.id'));
        $show->field('name', __('user-task::order.name'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new UserTaskOrder());

        $form->select('user_id', __('user-task::order.user_id'))
            ->options(function ($use_id) {
                $model = User::find($use_id);
                if ($model) {
                    return [$model->id => $model->username];
                }
            })
            ->ajax(route('admin.api.users'))
            ->rules('required');

        $form->select('user_task_id', __('user-task::order.user_task_id'))
            ->options(function ($user_task_id) {
                $model = UserTask::find($user_task_id);
                if ($model) {
                    return [$model->id => $model->title];
                }
            })
            ->ajax(route('api.task.select'))
            ->rules('required');

        $form->multipleImage('files', __('user-task::order.files'))
            ->removable()->uniqueName()->move('task');
        $form->UEditor('remark', __('user-task::order.remark'));
        $form->select('status', __('user-task::order.status.label'))
            ->options(__('user-task::order.status.value'));

        return $form;
    }
}