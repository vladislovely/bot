<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class User extends Model
{
    protected $table = 'users';

    protected $dateFormat = 'Y-m-d H:i:s';

    protected $fillable = [
        'first_name',
        'username',
        'email',
    ];

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class, 'user_id', 'id');
    }

    public function history(): HasMany
    {
        return $this->hasMany(History::class, 'user_id', 'id');
    }
}
