<?php
namespace App\Helpers;

/**
 * Blade用ヘルパークラス
 */
class ViewHelper
{
    public static function sortButton($name, $label, $sorts = []) {

        $order = 'desc';
        $orderClass = '';

        if (array_key_exists($name, $sorts)) {
          $order = ($sorts[$name] == 'desc')? 'asc': 'desc';
          $orderClass = ($sorts[$name] == 'desc')? 'sort_button_down': 'sort_button_up';
        }

        $tag = "<button name='sort[$name]' class='sort_button $orderClass') sort_button_down @endif' value='$order'}>$label</button>";
        return $tag;
    }
}
