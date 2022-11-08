<?php

namespace App\Http\Controllers;

use App\Enums\SupplierType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Http\Requests\CustomerEntryRequest;
use App\Http\Requests\CustomerEditRequest;
use App\Models\Customer;

/**
 * 取引先マスタ検索 コントローラークラス
 */
class CustomersController extends Controller
{
    /** セッションキー */
    protected $SESSION_KEY = 'CUSTOMERS';

    /** ソートカラム */
    protected $SORT_TARGET = ['id', 'full_name', 'gender', 'addr', 'email'];

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
        return redirect()->route('customers.index');
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

        return view('customers');
    }

    /**
     * 新規登録画面表示
     *
     * @param Request $request
     * @return view
     */
    public function entry(Request $request)
    {
        $suppliers = DB::table('suppliers')
                        ->select('id', 'name')
                        ->where('supplier_type' , '=', SupplierType::CUSTOMER)
                        ->get();

        // 画面に初期値を設定
        return view('customer_detail', [
            'id'  => 0,
            'surname' => '',
            'name'  => '',
            'surname_kana' => '',
            'name_kana' => '',
            'gender' => '9',
            'birthday' => '1900-01-01',
            'zip' => '',
            'prefcode' => '',
            'addr_1' => '',
            'addr_2' => '',
            'addr_3' => '',
            'tel' => '',
            'email' => '',
            'supplier_id' => '',
            'position' => '',
            'remark' => '',
            'suppliers' => $suppliers,
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
        $suppliers = DB::table('suppliers')
                        ->select('id', 'name')
                        ->where('supplier_type' , '=', SupplierType::CUSTOMER)
                        ->get();

        $user = Customer::findOrFail($id);

        // 各値を設定し、画面に返却
        return view('customer_detail', [
            'id'  => $id,
            'surname' => $user->surname,
            'name'  => $user->name,
            'surname_kana' => $user->surname_kana,
            'name_kana' => $user->name_kana,
            'gender' => $user->gender,
            'birthday' => $user->birthday,
            'zip' => $user->zip,
            'prefcode' => $user->prefcode,
            'addr_1' => $user->addr_1,
            'addr_2' => $user->addr_2,
            'addr_3' => $user->addr_3,
            'tel' => $user->tel,
            'email' => $user->email,
            'supplier_id' => $user->supplier_id,
            'position' => $user->position,
            'remark' => $user->remark,
            'suppliers' => $suppliers,
        ]);
    }

    /**
     * 登録処理
     *
     * @param CustomerEntryRequest $request
     * @param $id ID
     * @return route
     */    
    public function insert(CustomerEntryRequest $request, $id = 0)
    {
        $prefectureName = Customer::conversionPrefectureName($request->prefcode);
        $request->merge(['addr' => $prefectureName . $request->addr_1 . $request->addr_2 . $request->addr_3]);
        $request->merge(['full_name' => $request->surname . $request->name]);
        $request->merge(['full_name_kana' => $request->surname_kana . $request->name_kana]);

        // リクエストパラメータの取得
        $inputAll = $request->all();

        // 登録内部処理
        $user = $this->upsert($id, $inputAll);
        $userId = $user->id;

        // メッセージの作成(messages.phpより本文を取得)
        $completeMessage = trans('messages.regist.complete');

        // 各データ設定後、編集画面にリダイレクト
        return redirect()->route('customers.edit', [
            'id' => $userId,
        ])->with('flash_message', $completeMessage);
    }

    /**
     * 更新処理
     *
     * @param $id ID
     * @param CustomerEditRequest $request
     * @return route
     */    
    public function update($id, CustomerEditRequest $request)
    {
        $prefectureName = Customer::conversionPrefectureName($request->prefcode);
        $request->merge(['addr' => $prefectureName . $request->addr_1 . $request->addr_2 . $request->addr_3]);
        $request->merge(['full_name' => $request->surname . $request->name]);
        $request->merge(['full_name_kana' => $request->surname_kana . $request->name_kana]);

        // リクエストパラメータの取得
        $inputAll = $request->all();

        // 更新内部処理
        $this->upsert($id, $inputAll);

        // メッセージの作成(messages.phpより本文を取得)
        $completeMessage = trans('messages.update.complete');

        // 各データ設定後、編集画面にリダイレクト
        return redirect()->route('customers.edit', [
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
            $user = Customer::upsertData($id, $inputAll);
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
        Customer::findOrFail($id)->delete();

        // メッセージの作成(messages.phpより本文を取得)
        $completeMessage = trans('messages.delete.complete');

        // 各データ設定後、一覧画面にリダイレクト
        return redirect()->route('customers.index')
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
            $id = Session::get("{$this->SESSION_KEY}.ID", '');
            $full_name = Session::get("{$this->SESSION_KEY}.FULL_NAME", '');
            $full_name_kana = Session::get("{$this->SESSION_KEY}.FULL_NAME_KANA", '');
            $gender = Session::get("{$this->SESSION_KEY}.GENDER", '9');
            $addr = Session::get("{$this->SESSION_KEY}.ADDR", '');
            $tel = Session::get("{$this->SESSION_KEY}.TEL", '');
            $email = Session::get("{$this->SESSION_KEY}.EMAIL", '');
            $supplier_name = Session::get("{$this->SESSION_KEY}.SUPPLIER_NAME", '');
            $sort = Session::get("{$this->SESSION_KEY}.SORT", []);
            $page = Session::get("{$this->SESSION_KEY}.PAGE", 0);
        } else {
            // リスエストパラメータから取得
            $id = $request->id;
            $full_name = $request->full_name;
            $full_name_kana = $request->full_name_kana;
            $gender = $request->gender;
            $addr = $request->addr;
            $tel = $request->tel;
            $email = $request->email;
            $supplier_name = $request->supplier_name;
            
            $sort = [];
            $page = 0;

            Session::put("{$this->SESSION_KEY}.ID", $id);
            Session::put("{$this->SESSION_KEY}.FULL_NAME", $full_name);
            Session::put("{$this->SESSION_KEY}.FULL_NAME_KANA", $full_name_kana);
            Session::put("{$this->SESSION_KEY}.GENDER", $gender);
            Session::put("{$this->SESSION_KEY}.ADDR", $addr);
            Session::put("{$this->SESSION_KEY}.TEL", $tel);
            Session::put("{$this->SESSION_KEY}.EMAIL", $email); 
            Session::put("{$this->SESSION_KEY}.SUPPLIER_NAME", $supplier_name); 
        }

        $query = Customer::query();
        
        // 取引先テーブルと内部結合
        $query->leftjoin('suppliers', 'suppliers.id', '=', 'supplier_id')
            ->select('customers.id', 'customers.full_name', 'customers.gender', 'customers.addr', 'customers.email', 'suppliers.name');

        // 画面の検索条件を設定
        if ($full_name <> '') {
            // 顧客名
            $query->where('customers.full_name', 'like', '%' . parent::escapeLikeQuery($full_name) . '%');
        }
        if ($full_name_kana <> '') {
            // 顧客名(フリガナ)
            $query->where('customers.full_name_kana', 'like', '%' . parent::escapeLikeQuery($full_name_kana) . '%');
        }
        if ($gender <> '') {
            // 性別
            $query->where('customers.gender', '=', $gender);
        }
        if ($addr <> '') {
            // 顧客住所
            $query->where('customers.addr', 'like', '%' . parent::escapeLikeQuery($addr) . '%');
        }
        if ($tel <> '') {
            // 顧客電話番号
            $query->where('customers.tel', 'like', '%' . parent::escapeLikeQuery($tel) . '%');
        }
        if ($email <> '') {
            // 顧客e-mail
            $query->where('customers.email', 'like', '%' . parent::escapeLikeQuery($email) . '%');
        }
        if ($supplier_name <> '') {
            // 取引先名
            $query->where('suppliers.name', 'like', '%' . parent::escapeLikeQuery($supplier_name) . '%');
        }

       
        // 並び順の設定
        foreach ($this->SORT_TARGET as $target) {
            if (array_key_exists($target, $sort)) {
                $customers = $query->orderBy($target, $sort[$target] === 'asc' ? 'asc' : 'desc');
            }
        }
        // 第2ソート
        $customers = $query->orderBy('customers.id');

        // ページネーション
        $pageLimit = config('app.settings.page_limit');
        $customers = $customers->paginate($pageLimit, ['*'], 'page', $page)->withPath('/customers/search');
        
        // 各値を設定し、画面に返却
        return view('customers', [
            'id' => $id,
            'full_name'  => $full_name,
            'full_name_kana' => $full_name_kana,
            'gender'   => $gender,
            'addr' => $addr,
            'email'   => $email,
            'tel' => $tel,
            'supplier_name' => $supplier_name,
            'sort'   => $sort,
            'customers' => $customers,
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
