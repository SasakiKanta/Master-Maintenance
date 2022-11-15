<?php

namespace App\Enums;

/**
 * 取引先区分定義
 */
enum SupplierType: string
{
    case CUSTOMER = '1';
    case SUPPLIER = '2';

    /**
     * 表示用のテキストを取得
     */
    public function label(): string
    {
        return match($this) {
            self::CUSTOMER => '得意先',
            self::SUPPLIER => '仕入先',
        };
    }

    /**
     * 取引先コード用Prefix
     */
    public function prefix(): string
    {
        return match($this) {
            self::CUSTOMER => '1',
            self::SUPPLIER => '2',
        };
    }
}
