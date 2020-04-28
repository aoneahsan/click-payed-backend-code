<?php

namespace App\Model\Admin;

use Illuminate\Database\Eloquent\Model;

class DepositAccount extends Model
{
    protected $fillable = [
        'user_id', 'payment_method', 'account_number', 'status', 'approved_by', 'approved_at', 'status', 'additional_note'
    ];
}
