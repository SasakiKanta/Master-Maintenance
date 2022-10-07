<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserEditRequest extends FormRequest
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
        $id = $this->get('id');
        return [
            // バリデーションの設定
            'id' => ['string', 'required'],
            'name' => ['string', 'max:255'],
            'email' => ['email', 'max:255', "unique:users,email,${id},id,is_deleted,0"],
            'password' => ['sometimes', 'nullable', 'min:8'],
        ];
    }

    /**
     * Get the validation messages that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
        return [
            // メッセージの設定(共通メッセージはValidation.phpで設定)
        ];
    }

    /**
     * Get the validation attributes that apply to the request.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            // 項目名称の設定
            'name'     => '氏名',
            'email'    => 'メールアドレス',
            'password' => 'パスワード',
        ];
    }

    // /**
    //  * バリデーション実行後エラーを振替
    //  *
    //  */
    // public function withValidator(Validator $validator)
    // {
    //     // 以下のエラーをExceptionに振り替える
    //     $validator->sometimes('password', 'present|min:8', function () {
    //         return 'present|min:8';
    //     });
    // }
}
