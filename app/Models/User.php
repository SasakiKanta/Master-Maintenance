<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Enums\Flag;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are not mass assignable.
     *
     * @var array
     */
    protected $guarded = ['created_at', 'updated_at'];

    // /**
    //  * The attributes that are mass assignable.
    //  *
    //  * @var array<int, string>
    //  */
    // protected $fillable = [
    //     'name',
    //     'email',
    //     'password',
    // ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * 一覧検索
     * @return $query
     */
    public static function getList()
    {
        $query = User::query()
            ->where('is_deleted' ,'=', Flag::OFF);

        return $query;
    }

    /**
     * 利用者 登録、更新
     *
     * @param [type] $inputAll
     * @param [type] $loginStaffId
     * @return object
     */
    public static function upsertData($inputAll, $loginStaffId) {
        if (!isset($inputAll['id'])) {
            // 登録の場合
            $user = new User;
            $user->created_by = $loginStaffId;
        } else {
            // 修正の場合
            $user = User::find($inputAll['id']);
        }

        // 値が設定されている場合のみ、登録・更新として設定
        if(isset($inputAll['name'])) {
            $user->name = $inputAll['name'];
        }
        if(isset($inputAll['email'])) {
            $user->email = $inputAll['email'];
        }
        if(isset($inputAll['password'])) {
            $user->password = Hash::make($inputAll['password']);
        }
        if(isset($inputAll['is_locked'])) {
            $user->is_locked = $inputAll['is_locked'];
        }

        // 登録・更新項目の設定
        $user->email_verified_at = Carbon::now();
        $user->updated_by = $loginStaffId;
        $user->save();

        return $user;
    }

    /**
     * 利用者の論理削除
     *
     * @param [type] $id
     * @param [type] $loginStaffId
     * @return object
     */
    public static function deleteData($id, $loginStaffId) {
        $user = User::find($id);
        $user->is_deleted = true;
        $user->updated_by = $loginStaffId;
        $user->save();
        return $user;
    }
}
