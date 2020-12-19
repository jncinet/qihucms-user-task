<?php

namespace Qihucms\UserTask\Controllers\Admin;

use App\Models\User;
use Qihucms\Currency\Models\CurrencyType;
use Qihucms\UserTask\Models\UserTask;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class TaskController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '任务管理';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new UserTask());

        $grid->model()->orderBy('id', 'desc');

        $grid->filter(function ($filter) {

            // 去掉默认的id过滤器
            $filter->disableIdFilter();

            // 在这里添加字段过滤器
            $filter->like('title', __('user-task::task.title'));
            $filter->between('start_time', __('user-task::task.start_time'))->datetime();
            $filter->between('end_time', __('user-task::task.end_time'))->datetime();
            $filter->equal('pay_status', __('user-task::task.pay_status.label'))
                ->select(__('user-task::task.pay_status.value'));
            $filter->equal('status', __('user-task::task.status.label'))
                ->select(__('user-task::task.status.value'));
        });

        $grid->column('id', __('user-task::task.id'));
        $grid->column('user.username', __('user-task::task.user_id'));
        $grid->column('title', __('user-task::task.title'));
        $grid->column('start_time', __('user-task::task.start_time'));
        $grid->column('end_time', __('user-task::task.end_time'));
        $grid->column('stock', __('user-task::task.stock'));
        $grid->column('currency_type.name', __('user-task::task.currency_type_id'));
        $grid->column('amount', __('user-task::task.amount'));
        $grid->column('pay_status', __('user-task::task.pay_status.label'))
            ->using(__('user-task::task.pay_status.value'));
        $grid->column('status', __('user-task::task.status.label'))
            ->using(__('user-task::task.status.value'));

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
        $show = new Show(UserTask::findOrFail($id));

        $show->field('id', __('user-task::task.id'));
        $show->field('user', __('user-task::task.user_id'))->as(function () {
            return $this->user ? $this->user->username : trans('user-task::message.record_does_not_exist');
        });
        $show->field('title', __('user-task::task.title'));
        $show->field('thumbnail', __('user-task::task.thumbnail'))->image();
        $show->field('start_time', __('user-task::task.start_time'));
        $show->field('end_time', __('user-task::task.end_time'));
        $show->field('stock', __('user-task::task.stock'));
        $show->field('currency_type_id', __('user-task::task.currency_type_id'))->as(function () {
            return $this->currency_type ? $this->currency_type->name : trans('user-task::message.record_does_not_exist');
        });
        $show->field('amount', __('user-task::task.amount'));
        $show->field('content', __('user-task::task.content'))->unescape();
        $show->field('btn_text', __('user-task::task.btn_text'));
        $show->field('link', __('user-task::task.link'));
        $show->field('pay_status', __('user-task::task.pay_status.label'))
            ->using(__('user-task::task.pay_status.value'));
        $show->field('status', __('user-task::task.status.label'))
            ->using(__('user-task::task.pay_status.value'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new UserTask());

        $form->select('user_id', __('user-task::task.user_id'))
            ->options(function ($use_id) {
                $model = User::find($use_id);
                if ($model) {
                    return [$model->id => $model->username];
                }
            })
            ->ajax(route('admin.api.users'))
            ->rules('required');

        $form->text('title', __('user-task::task.title'));
        $form->image('thumbnail', __('user-task::task.thumbnail'))
            ->removable()->uniqueName()->move('task');
        $form->datetime('start_time', __('user-task::task.start_time'))
            ->default(now()->toDateTimeString());
        $form->datetime('end_time', __('user-task::task.end_time'))
            ->default(now()->addMonths()->toDateTimeString());
        $form->number('stock', __('user-task::task.stock'))->default(1);
        $form->select('currency_type_id', __('user-task::task.currency_type_id'))
            ->options(CurrencyType::all()->pluck('name', 'id'));
        $form->currency('amount', __('user-task::task.amount'))
            ->symbol('-');
        $form->UEditor('content', __('user-task::task.content'));
        $form->text('btn_text', __('user-task::task.btn_text'));
        $form->text('link', __('user-task::task.link'));
        $form->select('pay_status', __('user-task::task.pay_status.label'))
            ->options(__('user-task::task.pay_status.value'));
        $form->select('status', __('user-task::task.status.label'))
            ->options(__('user-task::task.status.value'));

        return $form;
    }
}