<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $primaryKey = 'account_id';

    public function tests()
    {
        return $this->hasMany(Test::class , 'test_account_id');
    }
}
