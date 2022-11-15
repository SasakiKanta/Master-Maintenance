<?php

namespace App\Http\Requests;

use App\Enums\SupplierType;
use App\Rules\SupplierCodeRule;
use Illuminate\Foundation\Http\FormRequest;
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
            'code' => ['required', 'string', 'max:10', "unique:suppliers,code,${id},id,deleted_at,NULL", new SupplierCodeRule($this->request)],
            'name' => ['required', 'string', 'max:50'],
            'supplier_type' => ['required', 'string', new Enum(SupplierType::class)],
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
            'name'    => '名前',
            'code'    => '取引先コード',
            'supplier_type' => '取引先区分',
        ];
    }

    /**
     *  このメソッドを追記
     * @param $validator
     */
    // public function withValidator($validator)
    // {
    //     // バリデーション完了後
    //     $validator->after(function ($validator) {   

    //         // 取引先区分に対応するEnumを取得
    //         $e = SupplierType::tryFrom($this->supplier_type);
    //         if (!$e) {
    //             // 取引先区分が空、もしくは該当がない場合はチェックしない
    //             return;
    //         }

    //         $prefix = $e->prefix();
    //         $label = $e->label();

    //         // 指定のコードで始まっているかチェック
    //         $ret = str_starts_with($this->code, $prefix);
    //         if (!$ret) {
    //             $validator->errors()->add('code', "取引先区分が{$label}の場合、取引先コードは「{$prefix}」始まりのみ可能です。");
    //         }            
    //     });
    // }
}
