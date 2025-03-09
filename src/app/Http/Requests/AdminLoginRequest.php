<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class AdminLoginRequest extends FormRequest
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
            'email' => ['required','exists:admin_users,email'],
            'password' => ['required'],
        ];
    }

    public function messages()
    {
        return[
            'email.required' => 'メールアドレスを入力してください',
            'email.exists' => 'ログイン情報が登録されていません。',
            'password.required' =>'パスワードを入力してください',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if (!$this->checkCredentials()) {
                $validator->errors()->add('email', 'ログイン情報が登録されていません。');
            }
        });
    }

    private function checkCredentials()
    {
        return Auth::guard('admin')->validate([
            'email' => $this->input('email'),
            'password' => $this->input('password'),
        ]);
    }
}
