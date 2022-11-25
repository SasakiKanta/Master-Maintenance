<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class SupplierCodeRule implements Rule
{
    protected $supplierType;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($reqest)
    {
        //リクエストパラーメータから情報を取得
        $this->supplierType = $reqest->all();

        //supplier_typeのみを代入
        $this->supplierType = $this->supplierType['supplier_type'];
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        //取引先区分が得意先の場合、先頭が１、取引先の場合は先頭が2
        if($this->supplierType == 1){

            //return preg_match('/^1/', $value);

            $prefix = '1';

        }elseif($this->supplierType == 2){

            //return preg_match('/^2/', $value);

            $prefix = '2';

        }else{
            return true;
        }
        $ret = str_starts_with($value, $prefix);
        return $ret;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        if($this->supplierType == 1){

            return '取引先区分が「得意先」の場合、取引先コードは「1」始まりのみ可能です。';

        }elseif($this->supplierType == 2){

            return '取引先区分が「仕入先」の場合、取引先コードは「2」始まりのみ可能です。';

        }
    }
}
