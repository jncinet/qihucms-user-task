<?php

namespace Qihucms\UserTask\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'user_task_id' => ['required', 'exists:user_tasks,id'],
        ];
    }

    public function attributes()
    {
        return trans('user-task::order');
    }
}