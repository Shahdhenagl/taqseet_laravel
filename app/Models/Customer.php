<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Customer extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'name',
        'phone_number',
        'whatsapp_number',
        'access_token',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
            if (empty($model->access_token)) {
                $model->access_token = Str::random(32);
            }
        });
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function installmentPlans()
    {
        return $this->hasMany(InstallmentPlan::class);
    }

    public function postponementRequests()
    {
        return $this->hasMany(PostponementRequest::class);
    }

    public function getPortalUrlAttribute()
    {
        return route('customer.portal', ['token' => $this->access_token]);
    }
}
