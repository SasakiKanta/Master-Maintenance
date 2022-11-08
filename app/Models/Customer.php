<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Enums\Gender;
use App\Enums\Pref;

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
     * 取引先区分のラベルを返します。
     *
     * @return string　性別区分名
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
     * 取引先 登録、更新
     *
     * @param $id ID
     * @param array $inputAll
     * @return object
     */
    public static function upsertData($id, $inputAll) {

        $user = Customer::firstOrNew(['id' => $id]);

        // 値が設定されている場合のみ、登録・更新として設定
        $user->fill($inputAll);

        // 登録・更新項目の設定
        $user->save();

        return $user;
    }
}
