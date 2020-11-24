<?php

namespace Qihucms\UserTask\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
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
            'title' => ['required', 'max:255'],
            'thumbnail' => ['thumbnail:255'],
            'stock' => ['required', 'min:1'],
            'currency_type_id' => ['required', 'exists:currency_types,id'],
            'amount' => ['required', 'min:1'],
            'btn_text' => ['max:255'],
            'link' => ['max:255'],
        ];
    }

    public function attributes()
    {
        return trans('user-task::task');
    }
}