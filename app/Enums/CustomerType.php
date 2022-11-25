<?php

namespace App\Enums;


/**
*
*
*
 */
enum CustomerType: string
{
    case INDIVIDUAL = '1';
    case CORPORATION   = '2';
    /**
     * 表示用のテキストを取得
     */
    public function label(): string
    {
        return match($this) {
            self::INDIVIDUAL => '個人',
            self::CORPORATION   => '法人',
        };
    }
}

