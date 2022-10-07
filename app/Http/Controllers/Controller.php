<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    // ***********************************
    // 共通メソッド定義
    // ***********************************
    /**
     * Like検索用にパラメータをエスケープします.
     */
    protected function escapeLikeQuery($value) {
        return str_replace(array('\\', '%', '_'), array('\\\\', '\%', '\_'), $value);
    }
}
