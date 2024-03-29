<?php

namespace App\Http\Controllers;

use App\Enums\SupplierType;
use App\Http\Requests\SupplierEditRequest;
use App\Http\Requests\SupplierEntryRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Models\Supplier;
use App\Models\User;

/**
 * 利用者マスタ検索 コントローラークラス
 */
class SuppliersController extends Controller
{
    /** セッションキー */
    protected $SESSION_KEY = 'SUPPLIER';

    /** ソートカラム */
    protected $SORT_TARGET = ['id','code', 'name', 'supplier_type'];

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
        return redirect()->route('suppliers.index');
    }

    /**
     * 検索画面表示
     *
     * @param Request $request
     * @return view
     */
    public function index(Request $request)
    {
        // Facade記法 に変更してます（$request->session()->has）
        if (Session::has($this->SESSION_KEY)) {
            return $this->doSearch($request);
        }

        return view('suppliers');
    }

    /**
     * 新規登録画面表示
     *
     * @param Request $request
     * @return view
     */
    public function entry(Request $request)
    {
        $user = User::select('id', 'name')->orderBy('id')->get();
        // 画面に初期値を設定
        return view('supplier_detail', [
            'id'  => 0,
            'code'  => '',
            'name' => '',
            'supplier_type' => SupplierType::CUSTOMER->value,
            'users' => $user,
            'user_id' => '',
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
        $supplier = Supplier::findOrFail($id);
        $user = User::select('id', 'name')->orderBy('id')->get();

        // 各値を設定し、画面に返却
        return view('supplier_detail', [
            'id'      => $id,
            'code'    => $supplier->code,
            'name'   => $supplier->name,
            'supplier_type' => $supplier->supplier_type,
            'users' => $user,
            'user_id' => $supplier->user_id,
        ]);
    }

    /**
     * 登録処理
     *
     * @param SupplierEntryRequest $request
     * @param $id ID
     * @return route
     */
    public function insert(SupplierEntryRequest $request, $id = 0,)
    {
        // リクエストパラメータの取得
        $inputAll = $request->all();

        // 登録内部処理
        $supplier = $this->upsert($id, $inputAll);
        $supplierId = $supplier->id;

        // メッセージの作成(messages.phpより本文を取得)
        $completeMessage = trans('messages.regist.complete');

        // 各データ設定後、編集画面にリダイレクト
        return redirect()->route('suppliers.edit', [
            'id' => $supplierId,
        ])->with('flash_message', $completeMessage);
    }

    /**
     * 更新処理
     *
     * @param $id ID
     * @param UserEditRequest $request
     * @return route
     */
    public function update($id, SupplierEditRequest $request)
    {
        // リクエストパラメータの取得
        $inputAll = $request->all();

        // 更新内部処理
        $this->upsert($id, $inputAll);

        // メッセージの作成(messages.phpより本文を取得)
        $completeMessage = trans('messages.update.complete');

        // 各データ設定後、編集画面にリダイレクト
        return redirect()->route('suppliers.edit', [
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
            $supplier = Supplier::upsertData($id, $inputAll);
            return $supplier;
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
        Supplier::findOrFail($id)->delete();

        // メッセージの作成(messages.phpより本文を取得)
        $completeMessage = trans('messages.delete.complete');

        // 各データ設定後、一覧画面にリダイレクト
        return redirect()->route('suppliers.index')
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
            $code = Session::get("{$this->SESSION_KEY}.CODE", '');
            $name = Session::get("{$this->SESSION_KEY}.NAME", '');
            $supplierType = Session::get("{$this->SESSION_KEY}.SUPPLIER_TYPE", '');
            $sort = Session::get("{$this->SESSION_KEY}.SORT", []);
            $page = Session::get("{$this->SESSION_KEY}.PAGE", 0);
        } else {
            // リスエストパラメータから取得
            $code = $request->code;
            $name = $request->name;
            $supplierType = $request->supplier_type;
            $sort = [];
            $page = 0;

            Session::put("{$this->SESSION_KEY}.CODE", $code);
            Session::put("{$this->SESSION_KEY}.NAME", $name);
            Session::put("{$this->SESSION_KEY}.SUPPLIER_TYPE", $supplierType);
        }

        $query = Supplier::query();

        // 画面の検索条件を設定
        if ($code <> '') {
            // 取引先コード
            $query->where('code', 'like', '%' . parent::escapeLikeQuery($code) . '%');
        }
        if ($name <> '') {
            // 取引先名
            $query->where('name', 'like', '%' . parent::escapeLikeQuery($name) . '%');
        }
        if ($supplierType <> '') {
            // 取引先区分
            $query->where('supplier_type', '=', $supplierType);
        }

        // 並び順の設定
        foreach ($this->SORT_TARGET as $target) {
            if (array_key_exists($target, $sort)) {
                $suppliers = $query->orderBy($target, $sort[$target] === 'asc' ? 'asc' : 'desc');
            }
        }
        // 第2ソート
        $suppliers = $query->orderBy('id');

        // ページネーション
        $pageLimit = config('app.settings.page_limit');
        $suppliers = $suppliers->paginate($pageLimit, ['*'], 'page', $page)->withPath('/suppliers/search');

        // 各値を設定し、画面に返却
        return view('suppliers', [
            'code' => $code,
            'name'  => $name,
            'supplier_type' => $supplierType,
            'sort'   => $sort,
            'suppliers' => $suppliers,
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
