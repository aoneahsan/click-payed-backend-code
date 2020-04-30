<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class TopupWalletRequest extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'payment_method', 'trx_id', 'amount', 'approved_by', 'approved_at', 'status', 'additional_note'
    ];

    // public function user_details()
    // {
    //     return $this->hasOne('App\User', 'id', 'user_id');
    // }

    public function user_details()
    {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }
}
