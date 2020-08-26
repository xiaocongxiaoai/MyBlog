<?php

namespace App\Http\Requests;

use App\Rules\phone;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class UserInfoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * 验证用户是否登录  此处因中间件会做验证所以直接给予验证通过
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     * 验证规则
     * @return array
     */
    public function rules()
    {
        return [
            'username' => 'required|min:3',
            'email' => 'email',
            'phoneNum' => new phone
        ];
    }

    /**
     * 获取被定义验证规则的错误消息
     *
     * @return array
     * @translator laravelacademy.org
     */
    public function messages(){
        return [
            'username.required' => '用户名必填！',
            'username.min'  => '用户名必须大于3个字！',
            'email.email'  => 'email格式错误！'
        ];
    }
    /**
     * 验证失败的异常处理
     * @param Validator $validator
     */
    protected function failedValidation(Validator $validator)
    {
        $data['message'] = $validator->errors()->first();


        return $data;
        //throw new SfoException(40013, ['params' => $validator->messages()->first()]);

    }
}
