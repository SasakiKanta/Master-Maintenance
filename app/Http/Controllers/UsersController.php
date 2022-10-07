<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserEntryRequest;
use App\Http\Requests\UserEditRequest;
use App\Http\Requests\UserRequest;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * 利用者マスタ検索 コントローラークラス
 */
class UsersController extends Controller
{
    /**
     * 検索画面初期表示
     *
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
     */
    public function index(Request $request)
    {
        // 初期化
        $this->clearSession($request);
        return view('users', $this->initPageObject($request));
    }

    /**
     * 新規登録画面表示
     *
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
     */
    public function entry(Request $request)
    {
        // 画面に初期値を設定
        return view('user_detail', [
            'name'  => '',
            'email' => '',
        ]);
    }

    /**
     * 参照画面表示
     *
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
     */
    public function edit(Request $request)
    {
        $id = $request->id;
        $user = User::find($id);

        // 各値を設定し、画面に返却
        return view('user_detail', [
            'id'    => $id,
            'name'  => $user->name,
            'email' => $user->email,
        ]);
    }

    /**
     * 登録処理
     *
     * @param UserEntryRequest $request
     * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
     */    
    public function insert(UserEntryRequest $request)
    {
        // リクエストパラメータの取得
        $inputAll = $request->all();

        // 登録内部処理
        $user = $this->upsert($inputAll);
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
     * @param UserEditRequest $request
     * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
     */    
    public function update(UserEditRequest $request)
    {
        // リクエストパラメータの取得
        $inputAll = $request->all();

        // 更新内部処理
        $user = $this->upsert($inputAll);

        // メッセージの作成(messages.phpより本文を取得)
        $completeMessage = trans('messages.update.complete');

        // 各データ設定後、編集画面にリダイレクト
        return redirect()->route('users.edit', [
            'id' => $inputAll['id'],
        ])->with('flash_message', $completeMessage);
    }

    /**
     * 登録・更新内部処理
     *
     * @param $inputAll
     * @return $user
     */
    public function upsert($inputAll)
    {
        // ログイン者IDの設定
        $loginStaffId = Auth::id();

        try {
            // トランザクション開始
            DB::beginTransaction();

            // データの登録・更新
            $user = User::upsertData($inputAll, $loginStaffId);

            // コミット
            DB::commit();

            return $user;
        // エラー発生時処理
        } catch (Exception $e) {
            // ロールバック
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * 削除処理
     *
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
     */
    public function delete(Request $request)
    {
        // ログイン者IDの設定
        $loginStaffId = Auth::id();

        // リクエストパラメータの取得
        $inputAll = $request->all();

        try {
            // トランザクション開始
            DB::beginTransaction();

            // データの削除
            User::deleteData($inputAll['id'], $loginStaffId);

            // コミット
            DB::commit();

            // メッセージの作成(messages.phpより本文を取得)
            $completeMessage = trans('messages.delete.complete');

            // 各データ設定後、編集画面にリダイレクト
            return redirect()->route('users.index')
                ->with('flash_message', $completeMessage);

        // エラー発生時処理
        } catch (Exception $e) {
            // ロールバック
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * 一覧画面表示
     * 
     * @param Request $request
     * @return view
     */
    public function search(Request $request)
    {
        // ページ情報の取得
        $page = $request->page;

        // ページングの有無により設定元を制御
        if (!isset($page)) {
            // リクエストパラメータより取得
            $searchName = $request->name;
            $searchEmail = $request->email;

            // セッションに設定
            $request->session()->put('USER_NAME', $searchName);
            $request->session()->put('USER_EMAIL', $searchEmail);
        } else {
            // セッションより取得
            $searchName = $request->session()->get('USER_NAME');
            $searchEmail = $request->session()->get('USER_EMAIL');

            // セッションに設定
            $request->session()->put('USER_PAGE', $page);
        }

        // 並び順の設定値を取得
        $st1 = $request->st1;
        $st2 = $request->st2;
        $st3 = $request->st3;

        $query = User::getList();

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
        if (!empty($st1)) {
            // ID
            $users = $query->orderBy('id', $st1 === 'up' ? 'asc' : 'desc');
        }
        if (!empty($st2)) {
            // 氏名
            $users = $query->orderBy('name', $st2 === 'up' ? 'asc' : 'desc');
        }
        if (!empty($st3)) {
            // メールアドレス
            $users = $query->orderBy('email', $st3 === 'up' ? 'asc' : 'desc');
        }

        // 並び順指定がない場合の通常設定
        $users = $query->orderBy('id');

        // ページネーション
        $users = $users->paginate(10);
        $pagenateParams = [];
        $pagenateParams['st1'] = $request->st1;
        $pagenateParams['st2'] = $request->st2;
        $pagenateParams['st3'] = $request->st3;

        // 各値を設定し、画面に返却
        return view('users', [
            'name'  => $searchName,
            'email' => $searchEmail,
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

    /**
     * セッションを初期化
     *
     * @param [type] $request
     * @return void
     */
    private function clearSession($request)
    {
        $request->session()->forget('USER_NAME');
        $request->session()->forget('USER_EMAIL');
        $request->session()->forget('USER_PAGE');
    }
}
