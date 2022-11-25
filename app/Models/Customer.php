<?php

namespace App\Models;

use App\Enums\Gender;
use App\Enums\Pref;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Customer extends Model
{

    use SoftDeletes;

    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $appends = [
        'genderLabel',  // 性別区分
    ];

    /**
     * 都道府県コードを都道府県名ラベルとして返します。
     *
     * @return string　都道府県名
     */
    public static function conversionPrefectureName($precode) {

        $e = Pref::tryFrom($precode);
        if ($e) {
            return $e->label();
        } else {
            return "";
        }
    }

    /**
     * 性別のラベルを返します。
     *
     * @return string　性別
     */
    public function getGenderLabelAttribute() {

        $e = Gender::tryFrom($this->gender);
        if ($e) {
            return $e->label();
        } else {
            return "";
        }
    }

    /**
     * 利用者 登録、更新
     *
     * @param $id ID
     * @param array $inputAll
     * @return object
     */
    public static function upsertData($id, $inputAll) {

        $customer = Customer::firstOrNew(['id' => $id]);

        // 値が設定されている場合のみ、登録・更新として設定
        $customer->fill($inputAll);

        //フルネームを設定
        $customer->full_name = $inputAll['surname'] . $inputAll['name'];

        //フルネーム（フリガナ）を設定
        $customer->full_name_kana = $inputAll['surname_kana'] . $inputAll['name_kana'];

        //住所を設定
        $customer->addr = Pref::from($inputAll['prefcode'])->label() . $inputAll['addr_1'] . $inputAll['addr_2'] . $inputAll['addr_3'];

        // 登録・更新項目の設定
        $customer->save();

        return $customer;
    }
}
