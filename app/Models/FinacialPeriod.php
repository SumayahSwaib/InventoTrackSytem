<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinacialPeriod extends Model
{
    use HasFactory;
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $active_finacial_period = FinacialPeriod::where([
                'company_id' => $model->company_id,
                'status' => 'Active',
            ])->first();
            if ($active_finacial_period != null /* && $model->status =='Active' */) {
                throw new \Exception('There is an active finacial period, please close it first');
            }
        });

        static::updating(function ($model) {
            $active_finacial_period = FinacialPeriod::where([
                'company_id' => $model->company_id,
                'status' => 'Active',
            ])->first();
            if ($active_finacial_period != null && $active_finacial_period->id != $model->id) {
                throw new \Exception('There is an active finacial period, please close it first');
            }
        });
    }
}
