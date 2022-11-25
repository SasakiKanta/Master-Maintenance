<?php

namespace App\Http\Requests;

use App\Enums\SupplierType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class SupplierEditRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:50'],
            'supplier_type' => ['required', new Enum(SupplierType::class)],
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
            'name'     => '取引先名',
            'supplier_type'    => '取引先区分',
        ];
    }

}
