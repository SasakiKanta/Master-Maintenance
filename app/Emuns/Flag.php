<?php

namespace App\Enums;

/**
 * フラグ定義
 *
 * @access public
 */
final class Flag
{
    const ON  = '1';
    const OFF = '0';

    /**
     * Keyリストを返す。
     *
     * @return array
     */
    public static function keys(): array
    {
        return [
            self::ON,
            self::OFF
        ];
    }
}
