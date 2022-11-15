<?php

namespace App\Rules;

use App\Enums\SupplierType;
use Illuminate\Contracts\Validation\Rule;

/**
 * 取引先コードの独自バリデーション
 */
class SupplierCodeRule implements Rule
{
    /**
     * コンストラクタ―
     *
     * @param object $requests リクエストパラメータ
     */
    public function __construct($requests)
    {
        $this->inputs = $requests->all();
    }

    /**
     * バリデーション
     * 
     * 取引先区分が[得意先]の場合、取引先コードは先頭1始まりのみ可能。
     * 取引先区分が[仕入先]の場合、取引先コードは先頭2始まりのみ可能。
     *
     * @param string $attribute 属性
     * @param string $value 入力値
     * @return boolean 正常：true、以上：false
     */
    public function passes($attribute, $value)
    {
        // 取引先区分を取得
        $supplierType = $this->inputs['supplier_type'];

        // 取引先区分に対応するEnumを取得
        $e = SupplierType::tryFrom($supplierType);
        if (!$e) {
            // 取引先区分が空、もしくは該当がない場合はチェックしない
            return true;
        }

        $this->label = $e->label();
        $this->prefix = $e->prefix();

        // 指定のコードで始まっているかチェック
        return str_starts_with($value, $this->prefix);
    }

    /**
     * メッセージを返す
     *
     * @return string エラーメッセージ
     */
    public function message()
    {
        return "取引先区分が{$this->label}の場合、取引先コードは「{$this->prefix}」始まりのみ可能です。";
    }
}