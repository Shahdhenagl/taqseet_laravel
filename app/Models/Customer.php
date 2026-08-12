<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class Customer extends Model
{
    use HasFactory, Notifiable;

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
            if (empty($model->attributes['id'])) {
                $model->attributes['id'] = (string) Str::uuid();
            }
            if (empty($model->attributes['access_token'])) {
                $model->attributes['access_token'] = Str::random(32);
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
        $token = $this->access_token ?: ('token_' . substr(md5($this->id), 0, 16));
        return route('customer.portal', ['token' => $token]);
    }
}
