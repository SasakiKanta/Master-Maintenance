<?php

namespace App\Enums;

/**
 * 性別定義
 */
enum Gender: string
{
    case FEMALE = '1';
    case MALE   = '2';
    case OTHER  = '3';

    /**
     * 表示用のテキストを取得
     */
    public function label(): string
    {
        return match($this) {
            self::FEMALE => '女性',
            self::MALE   => '男性',
            self::OTHER  => 'その他',
        };
    }
}
