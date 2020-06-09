<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $connection = 'mysql2';
    protected $table = 'coupons';
}
