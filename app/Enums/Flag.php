<?php

namespace App\Enums;

/**
 * フラグ定義
 *
 * @access public
 */
enum Flag: string
{
    case ON = '1';
    case OFF = '0';

    /**
     * 表示用のテキストを取得
     */
    public function label(): string
    {
        return match($this) {
            self::ON => 'ON',
            self::OFF => 'OFF',
        };
    }
}
