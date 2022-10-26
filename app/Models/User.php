<?php

namespace App\Models;

use App\Enums\Flag;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    use SoftDeletes;
    use BaseTrait;

    /**
     * The attributes that are not mass assignable.
     *
     * @var array
     */
    protected $guarded = [
        'id',
        'created_at', 
        'updated_at',
        'deleted_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'created_at', 
        'updated_at',
        'deleted_at'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $dates = [
        'deleted_at'
    ];

    /**
     * パスワードのハッシュ化
     *
     * @param string $password
     * @return void
     */
    public function setPasswordAttribute($password)
    {
        if ($password) {
            $this->attributes['password'] = Hash::make($password);
        } else {
            unset($this->attributes['password']);
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

        $user = User::firstOrNew(['id' => $id]);

        // 値が設定されている場合のみ、登録・更新として設定
        $user->fill($inputAll);

        // アカウントロック
        if(isset($inputAll['isLocked'])) {
            // チェックボックスチェック時
            $user->is_locked = Flag::ON->value;
        } else {
            // チェックボックス未チェック時
            $user->is_locked = Flag::OFF->value;
        }

        // 登録・更新項目の設定
        $user->save();

        return $user;
    }
}
