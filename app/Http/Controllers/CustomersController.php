<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomerRequest;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Models\Supplier;

/**
 * 利用者マスタ検索 コントローラークラス
 */
class CustomersController extends Controller
{
    /** セッションキー */
    protected $SESSION_KEY = 'CUSTOMER';

    /** ソートカラム */
    protected $SORT_TARGET = ['id','full_name', 'gender', 'addr', 'email', 'name'];

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
        $supplier = Supplier::select('id', 'name')->orderBy('id')->get();
        // 画面に初期値を設定
        return view('customer_detail', [
            'id'            =>          0,
            'surname'       =>          '',
            'name'          =>          '',
            'surname_kana'  =>          '',
            'name_kana'     =>          '',
            'gender'        =>          '',
            'birthday'      =>          '',
            'zip'           =>          '',
            'prefcode'      =>          '',
            'addr_1'        =>          '',
            'addr_2'        =>          '',
            'addr_3'        =>          '',
            'tel'           =>          '',
            'email'         =>          '',
            'supplier_id'   =>          '',
            'position'      =>          '',
            'remark'        =>          '',
            'customer_type' =>          '',
            'suppliers'     =>      $supplier,
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
        $customer = Customer::findOrFail($id);
        $supplier = Supplier::select('id', 'name')->orderBy('id')->get();

        // 各値を設定し、画面に返却
        return view('customer_detail', [
            'id'            =>      $id,
            'surname'       =>      $customer->surname,
            'name'          =>      $customer->name,
            'surname_kana'  =>      $customer->surname_kana,
            'name_kana'     =>      $customer->name_kana,
            'gender'        =>      $customer->gender,
            'birthday'      =>      $customer->birthday,
            'zip'           =>      $customer->zip,
            'prefcode'      =>      $customer->prefcode,
            'addr_1'        =>      $customer->addr_1,
            'addr_2'        =>      $customer->addr_2,
            'addr_3'        =>      $customer->addr_3,
            'tel'           =>      $customer->tel,
            'email'         =>      $customer->email,
            'supplier_id'   =>      $customer->supplier_id,
            'position'      =>      $customer->position,
            'remark'        =>      $customer->remark,
            'customer_type' =>      $customer->customer_type,
            'suppliers'      =>     $supplier,
        ]);
    }

    /**
     * 登録処理
     *
     * @param CustomerEntryRequest $request
     * @param $id ID
     * @return route
     */
    public function insert(CustomerRequest $request, $id = 0,)
    {
        // リクエストパラメータの取得
        $inputAll = $request->all();

        // 登録内部処理
        $customer = $this->upsert($id, $inputAll);
        $customerId = $customer->id;

        // メッセージの作成(messages.phpより本文を取得)
        $completeMessage = trans('messages.regist.complete');

        // 各データ設定後、編集画面にリダイレクト
        return redirect()->route('customers.edit', [
            'id' => $customerId,
        ])->with('flash_message', $completeMessage);
    }

    /**
     * 更新処理
     *
     * @param $id ID
     * @param UserEditRequest $request
     * @return route
     */
    public function update($id, CustomerRequest $request)
    {
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
            $customer = Customer::upsertData($id, $inputAll);
            return $customer;
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
            $name           =       Session::get("{$this->SESSION_KEY}.NAME", '');
            $name_kana      =       Session::get("{$this->SESSION_KEY}.NAME_KANA", '');
            $gender         =       Session::get("{$this->SESSION_KEY}.GENDER", '');
            $addr           =       Session::get("{$this->SESSION_KEY}.ADDR", '');
            $email          =       Session::get("{$this->SESSION_KEY}.EMAIL", '');
            $supplier_name  =       Session::get("{$this->SESSION_KEY}.SUPPLIER_NAME", '');
            $sort           =       Session::get("{$this->SESSION_KEY}.SORT", []);
            $page           =       Session::get("{$this->SESSION_KEY}.PAGE", 0);
        } else {
            // リスエストパラメータから取得
            $name           =       $request->name;
            $name_kana      =       $request->name_kana;
            $gender         =       $request->gender;
            $addr           =       $request->addr;
            $email          =       $request->email;
            $supplier_name  =       $request->supplier_name;
            $sort           =       [];
            $page           =       0;

            Session::put("{$this->SESSION_KEY}.NAME", $name);
            Session::put("{$this->SESSION_KEY}.NAME_KANA", $name_kana);
            Session::put("{$this->SESSION_KEY}.GENDER", $gender);
            Session::put("{$this->SESSION_KEY}.ADDR", $addr);
            Session::put("{$this->SESSION_KEY}.EMAIL", $email);
            Session::put("{$this->SESSION_KEY}.SUPPLIER_NAME", $supplier_name);
        }

        $query = Customer::query();

        /*$query->select([
            'c.*',
            's.name',
        ])->from('customers as c')->leftjoin('suppliers as s', 'c.supplier_id' , '=' , 's.id');
        */

        $query->select([
            'customers.id',
            'customers.full_name',
            'customers.gender',
            'customers.addr',
            'customers.email',
            'suppliers.name',
        ])->from('customers')->leftjoin('suppliers', function ($join) {
            $join->on('customers.supplier_id', '=', 'suppliers.id')
        ->whereNull('suppliers.deleted_at');
        });

        // 画面の検索条件を設定
        if ($name <> '') {
            // 名前
            $query->where('customers.full_name', 'like', '%' . parent::escapeLikeQuery($name) . '%');
        }
        if ($name_kana <> '') {
            // フリガナ
            $query->where('customers.full_name_kana', 'like', '%' . parent::escapeLikeQuery($name_kana) . '%');
        }
        if ($gender <> '') {
            // 性別
            $query->where('customers.gender', '=', $gender);
        }
        if ($addr <> '') {
            // 住所
            $query->where('customers.addr', 'like', '%' . parent::escapeLikeQuery($addr) . '%');
        }
        if ($email <> '') {
            // メールアドレス
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
            'name'          =>      $name,
            'name_kana'     =>      $name_kana,
            'gender'        =>      $gender,
            'addr'          =>      $addr,
            'email'         =>      $email,
            'supplier_name' =>      $supplier_name,
            'sort'          =>      $sort,
            'customers'     =>      $customers,
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



    /**
     * csvファイルダウンロード
     *
     * @param Request $request
     */
    public function csv(Request $request){
        $customers = Customer::all();
        $f = fopen('php://temp', 'w');
        $arr = array('id', 'customer_type', 'full_name', '');

    }
}
