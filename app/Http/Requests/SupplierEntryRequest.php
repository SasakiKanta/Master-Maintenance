<?php

namespace App\Http\Requests;

use App\Enums\SupplierType;
use App\Rules\SupplierCodeRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class SupplierEntryRequest extends FormRequest
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
            'code' => ['required', 'alpha_num', 'max:10', "unique:suppliers,code,${id},id,deleted_at,NULL", new SupplierCodeRule($this->request)],
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
            'code'     => '取引先コード',
            'name'    => '取引先名',
            'supplier_type' => '取引先区分',
        ];
    }
}
