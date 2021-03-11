<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Test extends Model
{
    public function account()
    {
        return $this->belongsTo(Account::class , 'test_account_id');
    }
}
