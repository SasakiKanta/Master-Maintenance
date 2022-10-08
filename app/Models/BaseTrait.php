<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;

/**
 * 基底Traitクラス
 *
 */
trait BaseTrait {
    /**
     * Event Hooks
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();
        /**
         * insert前処理
         */
        static::creating(function ($model) {
            if (Auth::user() != null) {
                $model->updated_by = Auth::user()->id;
            }
        });
        /**
         * update前処理
         */
        static::updating(function ($model) {
            if (Auth::user() != null) {
                $model->updated_by = Auth::user()->id;
            }
        });
        /**
         * deleted処理
         */
        static::deleted(function ($model) {
            if (Auth::user() != null) {
                $model->updated_by = Auth::user()->id;
                $model->save();
            }
        });
    }
}
