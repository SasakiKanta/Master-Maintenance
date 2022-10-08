<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
     * 検索画面初期表示
     *
     * @param Request $request
     * @return view
     */
    public function index(Request $request)
    {
        if ($request->session()->has($this->SESSION_KEY)) {
            return $this->doSearch($request);
        }

        return view('users', [
            'name'  =>'',
            'email' => '',
        ]);
    }

    /**
     * クリアボタン
     *
     * @param Request $request
     * @return route
     */
    public function clear(Request $request)
    {
        // セッション削除
        $this->clearSession($request);
        // indexへリダイレクト
        return redirect()->route('users.index');
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

        // 各データ設定後、編集画面にリダイレクト
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
        $page = $request->input('page', 0);
        $searchSort = $request->input('sort', []);

        if ($page) {
            $request->session()->put("{$this->SESSION_KEY}.PAGE", $page);
        }
        if ($searchSort) {
            $request->session()->put("{$this->SESSION_KEY}.SORT", $searchSort);
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
        if ($request->session()->has($this->SESSION_KEY)) {
            // セッションから復元
            $searchName = $request->session()->get("{$this->SESSION_KEY}.NAME", '');
            $searchEmail = $request->session()->get("{$this->SESSION_KEY}.EMAIL", '');
            $searchSort = $request->session()->get("{$this->SESSION_KEY}.SORT", []);
        } else {
            // リスエストパラメータから取得
            $searchName = $request->name;
            $searchEmail = $request->email;
            $searchSort = [];

            $request->session()->put("{$this->SESSION_KEY}.NAME", $searchName);
            $request->session()->put("{$this->SESSION_KEY}.EMAIL", $searchEmail);
        }

        $query = User::query();

        // 画面の検索条件を設定
        if ($searchName <> '') {
            // 氏名
            $query->where('name', 'like binary', '%' . parent::escapeLikeQuery($searchName) . '%');
        }
        if ($searchEmail <> '') {
            // email
            $query->where('email', 'like binary', '%' . parent::escapeLikeQuery($searchEmail) . '%');
        }

        // 並び順の設定
        foreach ($this->SORT_TARGET as $target) {
            if (array_key_exists($target, $searchSort)) {
                $users = $query->orderBy($target, $searchSort[$target] === 'asc' ? 'asc' : 'desc');
            }
        }
        // 第2ソート
        $users = $query->orderBy('id');

        // ページネーション
        $users = $users->paginate(config('app.settings.page_limit'));

        // 各値を設定し、画面に返却
        return view('users', [
            'name'  => $searchName,
            'email' => $searchEmail,
            'sort'   => $searchSort,
            'users' => $users,
        ]);
    }

    /**
     * セッションクリア
     *
     * @param Request $request
     */
    protected function clearSession($request) {
        $request->session()->forget($this->SESSION_KEY);
    }
}
