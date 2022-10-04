<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

/**
 * 利用者マスタ検索 コントローラークラス
 */
class UsersController extends Controller
{
    /**
     * 初期表示
     *
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
     */
    public function index(Request $request)
    {
        // 初期化
        return view('users', $this->initPageObject($request));
    }

    /**
     * 新規登録
     *
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
     */
    public function entry(Request $request)
    {
        // 初期化
        return view('user_detail', []);
    }
    /**
     * 参照
     *
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
     */
    public function edit(Request $request)
    {
        // 初期化
        return view('user_detail', []);
    }
    /**
     * 一覧画面表示
     * @return view
     */
    public function search(Request $request)
    {
        $query = User::query();
        $users = $query->orderBy('id')->paginate(10);

        $st1 = $request->st1;
        $st2 = $request->st2;
        $st3 = $request->st3;

        $pagenateParams = [];
        $pagenateParams['st1'] = $request->st1;
        $pagenateParams['st1'] = $request->st1;
        $pagenateParams['st1'] = $request->st1;

        return view('users', [
            'name'  => '',
            'email' => '',
            'pagenateParams'  => $pagenateParams,
            'users' => $users,
            'st1'   => $st1,
            'st2'   => $st2,
            'st3'   => $st3,
        ]);
    }
    /**
     * 画面オブジェクト初期化
     *
     * @return string[]
     */
    private function initPageObject()
    {
        return [
            'name'  => '',
            'email' => '',
        ];
    }
}
