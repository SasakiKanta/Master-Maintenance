<?php

namespace App\Models;

use App\Enums\SupplierType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
     * 利用者 登録、更新
     *
     * @param $id ID
     * @param array $inputAll
     * @return object
     */
    public static function upsertData($id, $inputAll) {

        $supplier = Supplier::firstOrNew(['id' => $id]);

        // 値が設定されている場合のみ、登録・更新として設定
        $supplier->fill($inputAll);

        // 取引先区分
        if($inputAll['supplier_type'] == '1') {
            // 得意先時
            $supplier->supplier_type = SupplierType::CUSTOMER->value;
        } elseif($inputAll['supplier_type'] == '2') {
            // 仕入れ先時
            $supplier->supplier_type = SupplierType::SUPPLIER->value;
        }

        // 登録・更新項目の設定
        $supplier->save();

        return $supplier;
    }

}
