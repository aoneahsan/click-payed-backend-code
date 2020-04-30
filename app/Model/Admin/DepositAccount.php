<?php

namespace App\Model\Admin;

use Illuminate\Database\Eloquent\Model;

class DepositAccount extends Model
{
    protected $fillable = [
        'user_id', 'payment_method', 'account_number', 'status', 'order_no', 'additional_note', 'extra_field'
    ];
}
