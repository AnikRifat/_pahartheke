<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{

    protected static function boot()
    {
        parent::boot();
        self::created(function ($model) {
            $number = get_setting('contact_phone');
            $order_data = json_decode($model->shipping_address, true);
            $name = $order_data['name'];
            sendSMS($number, env('APP_NAME'), "One order has been placed by $name at ".env('APP_NAME'));
        });
    }
    
    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }

    public function refund_requests()
    {
        return $this->hasMany(RefundRequest::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pickup_point()
    {
        return $this->belongsTo(PickupPoint::class);
    }

    public function affiliate_log()
    {
        return $this->hasMany(AffiliateLog::class);
    }

    public function club_point()
    {
        return $this->hasMany(ClubPoint::class);
    }

    public function deliveryBoy()
    {
        return $this->hasOne(DeliveryMan::class,'id','delivery_man_id');
    }
}
