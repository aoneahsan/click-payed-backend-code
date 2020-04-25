<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class UserTransactionHistory extends Model
{
    protected $fillable = [
        'user_id', 'topup_request_id', 'withdrawal_request_id', 'transaction_type', 'trx_id', 'amount', 'remaining_balance', 'approved_by'
    ];
}
