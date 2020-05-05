<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class WithDrawalRequest extends Model
{
    protected $fillable = [
        'user_id', 'payment_method', 'trx_id', 'amount', 'approved_by', 'approved_at', 'status'
    ];

    public function user_details()
    {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }

}
