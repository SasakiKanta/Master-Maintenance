<?php

namespace App\Http\Controllers;

use App\Enums\CustomerType;
use App\Enums\Gender;
use App\Enums\Pref;
use App\Http\Requests\CustomerRequest;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Models\Supplier;
use App\Models\Zipcode;
use Exception;
use Facade\FlareClient\View;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Enum;


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

        return view('customers',[
            'is_upload' =>  "",
        ]);
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
            'is_upload'     =>      '',
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
        //セッションから値を復元
        $name           =       Session::get("{$this->SESSION_KEY}.NAME", '');
        $name_kana      =       Session::get("{$this->SESSION_KEY}.NAME_KANA", '');
        $gender         =       Session::get("{$this->SESSION_KEY}.GENDER", '');
        $addr           =       Session::get("{$this->SESSION_KEY}.ADDR", '');
        $email          =       Session::get("{$this->SESSION_KEY}.EMAIL", '');
        $supplier_name  =       Session::get("{$this->SESSION_KEY}.SUPPLIER_NAME", '');
        $sort           =       Session::get("{$this->SESSION_KEY}.SORT", []);

        //csvファイルに必要なクエリの作成
        $customers = Customer::query();
        $customers->select([
            'customers.id',
            'customers.customer_type',
            'customers.surname',
            'customers.name as c_name',
            'customers.surname_kana',
            'customers.name_kana',
            'customers.full_name',
            'customers.full_name_kana',
            'customers.gender',
            'customers.birthday',
            'customers.zip',
            'customers.prefcode',
            'customers.addr_1',
            'customers.addr_2',
            'customers.addr_3',
            'customers.addr',
            'customers.tel',
            'customers.email',
            'customers.supplier_id',
            'customers.position',
            'suppliers.name',
        ])->from('customers')->leftjoin('suppliers', function ($join) {
            $join->on('customers.supplier_id', '=', 'suppliers.id');
        });

        if ($name <> '') {
            // 名前
            $customers->where('customers.full_name', 'like', '%' . parent::escapeLikeQuery($name) . '%');
        }
        if ($name_kana <> '') {
            // フリガナ
            $customers->where('customers.full_name_kana', 'like', '%' . parent::escapeLikeQuery($name_kana) . '%');
        }
        if ($gender <> '') {
            // 性別
            $customers->where('customers.gender', '=', $gender);
        }
        if ($addr <> '') {
            // 住所
            $customers->where('customers.addr', 'like', '%' . parent::escapeLikeQuery($addr) . '%');
        }
        if ($email <> '') {
            // メールアドレス
            $customers->where('customers.email', 'like', '%' . parent::escapeLikeQuery($email) . '%');
        }
        if ($supplier_name <> '') {
            // 取引先名
            $customers->where('suppliers.name', 'like', '%' . parent::escapeLikeQuery($supplier_name) . '%');
        }

        // 並び順の設定
        foreach ($this->SORT_TARGET as $target) {
            if (array_key_exists($target, $sort)) {
                $customers = $customers->orderBy($target, $sort[$target] === 'asc' ? 'asc' : 'desc');
            }
        }
        // 第2ソート
        $customers = $customers->orderBy('customers.id');
        $customers = $customers->get();

        $stream = fopen('php://temp', 'w');
        //csvのヘッダーを作成
        $headline = "ID,\"顧客区分CD\",\"顧客区分\",\"姓\",\"名\",\"姓（フリガナ）\",\"名（フリガナ）\",\"性別CD\",\"性別\",\"生年月日\",\"郵便番号\",\"都道府県CD\",\"都道府県\",\"市区群町村\",\"番地・町名\",\"マンション・建物名など\",\"電話番号\",\"メールアドレス\",\"取引先コード\",\"取引先名\",\"肩書\"\n";
        fwrite($stream, $headline);

        //csvの内容の作成
        foreach ($customers as $customer) {
            $out = "";
            $cnt = 1;
            $arrInfo = array(
                'ID'                        =>  $customer->id,
                '顧客区分CD'                =>  $customer->customer_type,
                '顧客区分'                  =>  $customer->customer_type_label,
                '姓'                        =>  $customer->surname,
                '名'                        =>  $customer->c_name,
                '姓（フリガナ）'            =>  $customer->surname_kana,
                '名（フリガナ）'            =>  $customer->name_kana,
                '性別CD'                    =>  $customer->gender,
                '性別'                      =>  $customer->gender_label,
                '生年月日'                  =>  $customer->birthday,
                '郵便番号'                  =>  $customer->zip,
                '都道府県CD'                =>  $customer->prefcode,
                '都道府県'                  =>  $customer->pref_label,
                '市区群町村'                =>  $customer->addr_1,
                '番地・町名'                =>  $customer->addr_2,
                'マンション・建物名など'    =>  $customer->addr_3,
                '電話番号'                  =>  $customer->tel,
                'メールアドレス'            =>  $customer->email,
                '取引先コード'              =>  $customer->supplier_id,
                '取引先名'                  =>  $customer->name,
                '肩書'                      =>  $customer->position,
            );

            //囲み文字を入れる処理
            foreach($arrInfo as $key => $value) {
                if($key == 'ID'){
                    $out .= $value;
                }else{
                    $out .= "\"" . $value . "\"";
                }
                if($cnt< count($arrInfo)){
                    $out .= ",";
                } else {
                    $out .= "\n";
                }
                $cnt++;
            }
            fwrite($stream, $out);
        };
        rewind($stream);
        $csv =str_replace(PHP_EOL, "\n", stream_get_contents($stream));
        $csv = mb_convert_encoding($csv, 'UTF-8');
        fclose($stream);
        $headers = array(
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=顧客データ_YYYYMMDDHHMMSS.csv'
        );
        return response()->make($csv, 200, $headers);
    }

    /**
     * zipcode検索
     *
     * @param Request $request
     */
    protected function zip(Request $request) {
        $zip = $request->all();
        $zip = $zip['zip'];
        $zip = str_replace('-', "", $zip);
        $addresses = Zipcode::select(
                            'pref_name',
                            'municipality_name',
                            'town_name',
                            )->where('zip_cd', '=', "$zip")->first();
        $json = $addresses;
        return response()->json($json);
    }



    /**
     * アップロードファイルからの登録処理
     *
     * @param Request $request
     */
    public function upload(Request $request){
        $values = [];
        $columu = [
            'id',
            'customer_type',
            'customer_type_label',
            'surname',
            'name',
            'surname_kana',
            'name_kana',
            'gender',
            'gender_label',
            'birthday',
            'zip',
            'prefcode',
            'prefcode_label',
            'addr_1',
            'addr_2',
            'addr_3',
            'tel',
            'email',
            'supplier_id',
            'supplier_name',
            'position',
        ];

        //ファイル名を取得
        $file_name = $request->csvfile->getClientOriginalName();

        //ファイルを保存
        $request->csvfile->storeAs('public/csv', $file_name);

        //ファイル内容を取得
        $csv = Storage::disk('local')->get("public/csv/{$file_name}");

        //配列に変換
        $csv = str_replace("\"", '', $csv);
        $array = explode("\n", $csv);
        $array = array_filter($array, "strlen");
        $array = array_values($array);

        //DBに登録できる形に変更
        foreach ($array as $row) {
            $arr = explode(",", $row);
            if (!(count($arr) == count($columu))) {
                Storage::append("public/csv/{$file_name}", "項目が足りません。");
                return view('customers',[
                    'is_upload' => false,
                    'file_name' => $file_name,
                ]);
            }
            $arr = array_combine($columu, $arr);
            array_push($values, $arr);
        }

        //ヘッダーを削除
        array_shift($values);


        //入力チェック
        $is_upload = $this->validation($values, $file_name);

        //入力チェックが成功したら登録
        if (is_array($is_upload)) {
            return view('customers', [
                'is_upload' => $is_upload[0],
                'file_name' => $is_upload[1],
            ]);
        } elseif ($is_upload === true) {
            //登録処理
            foreach ($values as $value) {
                $id = $value['id'];
                $inputAll = $value;
                $this->upsert($id, $inputAll);
            }
            return view('customers', [
                'is_upload' => $is_upload,
            ]);
        }
    }

    /**
     * バリデーションチェック
     * @param $values, $file_name
     */
    public function validation($values, $file_name){
        //csvErrorへ渡す変数を用意
        $errors = [];

        //エラーメッセージ
        $messages = [
            'required' => ':attributeは必ず指定してください。',
            'string' => ':attributeは文字列を入力して下さい。',
            'max'    => [
                'string'  => ':attributeは、:max文字以下で指定してください。',
            ],
            'enum' => ':attributeの登録がありません。',
            'email'  => 'メールアドレスの形式で入力してください。',
            'unique' => ':attributeは既に使用されています。',
            'regex' => ':attributeはハイフンなし数字7桁で入力してください。',
            'exists' => ':attributeの登録がありません。',
        ];

        //属性値
        $attributes =[
            'surname'       =>      '姓',
            'name'          =>      '名',
            'surname_kana'  =>      '姓（フリガナ）',
            'name_kana'     =>      '名（フリガナ）',
            'gender'        =>      '性別',
            'birthday'      =>      '生年月日',
            'email'         =>      'メールアドレス',
            'zip'           =>      '郵便番号',
            'addr_1'        =>      '市区群町村',
            'addr_2'        =>      '番地・町域',
            'addr_3'        =>      'マンション・建物名',
            'supplier_id'   =>      '取引先',
            'position'      =>      '肩書',
        ];

        //入力チェック処理
        foreach ($values as $value) {
            $id = $value['id'];
            $validator = Validator::make($value, [
                'surname'       =>      ['required', 'string', 'max:25'],
                'name'          =>      ['required', 'string', 'max:25'],
                'surname_kana'  =>      ['string', 'max:50', 'nullable'],
                'name_kana'     =>      ['string', 'max:50', 'nullable'],
                'gender'        =>      [new Enum(Gender::class)],
                'birthday'      =>      ['required'],
                'email'         =>      ['required', 'string', 'email', 'max:254', "unique:customers,email,${id},id,deleted_at,NULL"],
                'zip'           =>      ['regex:/^[0-9]{7}$/', 'nullable'],
                'addr_1'        =>      ['max:100'],
                'addr_2'        =>      ['max:100'],
                'addr_3'        =>      ['max:100'],
                'supplier_id'   =>      ['required', 'exists:suppliers,id'],
                'position'      =>      ['max:100'],
            ], $messages, $attributes);

            //エラー内容をcavに追加
            $error = $validator->errors()->all();
            $error = implode(",", $error);
            $errorCsv = implode(",", $value);
            $errorCsv .= "," . $error;
            $errorCsv = explode(",", $errorCsv);
            array_push($errors, $errorCsv);
        }

        //エラーがあった場合、csvErrorへ
        if ($validator->fails()){
            $file_name = $this->csvError($errors, $file_name);
            $is_upload = false;
            $is_upload = array($is_upload, $file_name);
            return $is_upload;
        }
        $is_upload = true;
        return $is_upload;
    }


    /**
     * バリデーションエラー時のcsv作成
     *
     * @param $errors, $file_name
     *
     */
    public function csvError($errors, $file_name){
        $stream = fopen("../storage/app/public/csv/$file_name", 'w');

        //csvのヘッダーを作成
        $headline = "ID,\"顧客区分CD\",\"顧客区分\",\"姓\",\"名\",\"姓（フリガナ）\",\"名（フリガナ）\",\"性別CD\",\"性別\",\"生年月日\",\"郵便番号\",\"都道府県CD\",\"都道府県\",\"市区群町村\",\"番地・町名\",\"マンション・建物名など\",\"電話番号\",\"メールアドレス\",\"取引先コード\",\"取引先名\",\"肩書\"\n";
        fwrite($stream, $headline);

        //csvの内容の作成
        foreach ($errors as $error) {
            $out = "";
            $cnt = 1;

            //囲み文字を入れる処理
            foreach($error as $key => $value) {
                if($key == 0){
                    $out .= $value;
                } else {
                    $out .= "\"" . $value . "\"";
                }
                if($cnt< count($error)){
                    $out .= ",";
                } else {
                    $out .= "\n";
                }
                $cnt++;
            }
            fwrite($stream, $out);
        }
        rewind($stream);
        fclose($stream);
        return $file_name;
    }


    /**
     *エラー時のcsvファイルダウンロード
     *
     * @param Request $request
     */
    public function errorCsv(Request $request){
        //ファイルの名前を取得
        $name = $request->all();
        $name = $name["name"];

        //csvファイルをダウンロード
        return Storage::download("public/csv/$name");
    }
}
