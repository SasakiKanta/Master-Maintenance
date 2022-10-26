<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Enums\SupplierType;

class Supplier extends Model
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
        'supplierTypeLabel' // 取引先区分名
    ];

    /**
     * 取引先区分のラベルを返します。
     *
     * @return string　取引先区分名
     */
    public function getSupplierTypeLabelAttribute() {
        
        $e = SupplierType::tryFrom($this->supplier_type);
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

        $user = Supplier::firstOrNew(['id' => $id]);

        // 値が設定されている場合のみ、登録・更新として設定
        $user->fill($inputAll);

        // 登録・更新項目の設定
        $user->save();

        return $user;
    }

}
