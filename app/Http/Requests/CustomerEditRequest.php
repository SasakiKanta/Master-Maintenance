<?php

namespace App\Http\Requests;

use App\Enums\Gender;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Foundation\Http\FormRequest;

class CustomerEditRequest extends FormRequest
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
        $id = $this->route('id');
        return [
            // バリデーションの設定
            'surname' => ['required', 'string', 'max:25'],
            'name' => ['required', 'string', 'max:25'],
            'surname_kana' => ['nullable','string', 'max:50'],
            'name_kana' => ['nullable','string', 'max:50'],
            'gender' => [new Enum(Gender::class)],
            'birthday' => ['required'],
            'email' => ['required', 'email', 'max:254', "unique:customers,email,${id},id,deleted_at,NULL"],
            'zip' => ['nullable', 'numeric', 'digits:7'],
            'addr_1' => ['max:100'],
            'addr_2' => ['max:100'],
            'addr_3' => ['max:100'],
            'position' => ['max:100'],
            'supplier_id' => [
                'numeric','nullable', "exists:suppliers,id,deleted_at,NULL"
            ],
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
            'surname' => '姓',
            'name' => '名',
            'surname_kana' => '姓(フリガナ)',
            'name_kana' => '名(フリガナ)',
            'gender' => '性別',
            'birthday' => '生年月日',
            'email' => 'メールアドレス',
            'zip' => '郵便番号',
            'addr_1' => '市区郡町村',
            'addr_2' => '番地・町域',
            'addr_3' => 'マンション・建物名',
            'supplier_id' => '取引先名',
            'position' => '肩書',
        ];
    }
}
