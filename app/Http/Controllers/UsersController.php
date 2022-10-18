<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Enums\Flag;
use App\Http\Requests\UserEntryRequest;
use App\Http\Requests\UserEditRequest;
use App\Models\User;

/**
 * 利用者マスタ検索 コントローラークラス
 */
class UsersController extends Controller
{
    /** セッションキー */
    protected $SESSION_KEY = 'USERS';

    /** ソートカラム */
    protected $SORT_TARGET = ['id', 'name', 'email'];

    /**
     * 初期化
     *
     * @param Request $request
     * @return route
     */
    public function init(Request $request)
    {
        // セッション削除
        $this->clearSession($request);
        // indexへリダイレクト
        return redirect()->route('users.index');
    }

    /**
     * 検索画面表示
     *
     * @param Request $request
     * @return view
     */
    public function index(Request $request)
    {
        // Facase記法 に変更してます（$request->session()->has）
        if (Session::has($this->SESSION_KEY)) {
            return $this->doSearch($request);
        }

        return view('users');
    }

    /**
     * 新規登録画面表示
     *
     * @param Request $request
     * @return view
     */
    public function entry(Request $request)
    {
        // 画面に初期値を設定
        return view('user_detail', [
            'id'  => 0,
            'name'  => '',
            'email' => '',
            'isLocked' => Flag::OFF,
        ]);
    }

    /**
     * 参照画面表示
     *
     * @param $id ID
     * @param Request $request
     * @return view
     */
    public function edit($id, Request $request)
    {
        $user = User::findOrFail($id);

        // 各値を設定し、画面に返却
        return view('user_detail', [
            'id'      => $id,
            'name'    => $user->name,
            'email'   => $user->email,
            'isLocked' => $user->is_locked,
        ]);
    }

    /**
     * 登録処理
     *
     * @param UserEntryRequest $request
     * @param $id ID
     * @return route
     */    
    public function insert(UserEntryRequest $request, $id = 0)
    {
        // リクエストパラメータの取得
        $inputAll = $request->all();

        // 登録内部処理
        $user = $this->upsert($id, $inputAll);
        $userId = $user->id;

        // メッセージの作成(messages.phpより本文を取得)
        $completeMessage = trans('messages.regist.complete');

        // 各データ設定後、編集画面にリダイレクト
        return redirect()->route('users.edit', [
            'id' => $userId,
        ])->with('flash_message', $completeMessage);
    }

    /**
     * 更新処理
     *
     * @param $id ID
     * @param UserEditRequest $request
     * @return route
     */    
    public function update($id, UserEditRequest $request)
    {
        // リクエストパラメータの取得
        $inputAll = $request->all();

        // 更新内部処理
        $this->upsert($id, $inputAll);

        // メッセージの作成(messages.phpより本文を取得)
        $completeMessage = trans('messages.update.complete');

        // 各データ設定後、編集画面にリダイレクト
        return redirect()->route('users.edit', [
            'id' => $id,
        ])->with('flash_message', $completeMessage);
    }

    /**
     * 登録・更新内部処理
     *
     * @param $id ID
     * @param $inputAll
     * @return $user
     */
    public function upsert($id, $inputAll)
    {
        return DB::transaction(function () use ($id, $inputAll) {
            // データの登録・更新
            $user = User::upsertData($id, $inputAll);
            return $user;
        });
    }

    /**
     * 削除処理
     *
     * @param $id ID
     * @param Request $request
     * @return route
     */
    public function delete($id, Request $request)
    {
        // データの削除
        User::findOrFail($id)->delete();

        // メッセージの作成(messages.phpより本文を取得)
        $completeMessage = trans('messages.delete.complete');

        // 各データ設定後、一覧画面にリダイレクト
        return redirect()->route('users.index')
            ->with('flash_message', $completeMessage);
    }

    /**
     * 検索
     * 
     * @param Request $request
     * @return view
     */
    public function search(Request $request)
    {
        // セッション削除
        $this->clearSession($request);
        // 検索
        return $this->doSearch($request);
    }

    /**
     * ソート、ページング
     * 
     * @param Request $request
     * @return view
     */
    public function paging(Request $request)
    {
        if ($request->has('sort')) {
            // ソートパラメータがある場合
            Session::put("{$this->SESSION_KEY}.SORT", $request->sort);
            Session::put("{$this->SESSION_KEY}.PAGE", '');
        } elseif ($request->has('page')) {
            // ページパラメータがある場合
            Session::put("{$this->SESSION_KEY}.PAGE", $request->page);
        }

        return $this->doSearch($request);
    }

    /**
     * 検索処理
     * 
     * @param Request $request
     * @return view
     */
    protected function doSearch(Request $request)
    {
        // 検索条件取得
        if (Session::has($this->SESSION_KEY)) {
            // セッションから復元
            $name = Session::get("{$this->SESSION_KEY}.NAME", '');
            $email = Session::get("{$this->SESSION_KEY}.EMAIL", '');
            $sort = Session::get("{$this->SESSION_KEY}.SORT", []);
            $page = Session::get("{$this->SESSION_KEY}.PAGE", 0);
        } else {
            // リスエストパラメータから取得
            $name = $request->name;
            $email = $request->email;
            $sort = [];
            $page = 0;

            Session::put("{$this->SESSION_KEY}.NAME", $name);
            Session::put("{$this->SESSION_KEY}.EMAIL", $email);
        }

        $query = User::query();

        // 画面の検索条件を設定
        if ($name <> '') {
            // 名前
            $query->where('name', 'like', '%' . parent::escapeLikeQuery($name) . '%');
        }
        if ($email <> '') {
            // email
            $query->where('email', 'like', '%' . parent::escapeLikeQuery($email) . '%');
        }

        // 並び順の設定
        foreach ($this->SORT_TARGET as $target) {
            if (array_key_exists($target, $sort)) {
                $users = $query->orderBy($target, $sort[$target] === 'asc' ? 'asc' : 'desc');
            }
        }
        // 第2ソート
        $users = $query->orderBy('id');

        // ページネーション
        $pageLimit = config('app.settings.page_limit');
        $users = $users->paginate($pageLimit, ['*'], 'page', $page);

        // 各値を設定し、画面に返却
        return view('users', [
            'name'  => $name,
            'email' => $email,
            'sort'   => $sort,
            'users' => $users,
        ]);
    }

    /**
     * セッションクリア
     *
     * @param Request $request
     */
    protected function clearSession($request) {
        Session::forget($this->SESSION_KEY);
    }
}
