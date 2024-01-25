<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockRecord extends Model
{
    use HasFactory;
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {

            $stock_item = StockItem::find($model->stock_item_id);
            if ($stock_item == null) {
                throw new \Exception("Invalid stock sub Category");
            }
            $financial_period = Utils::getActiveFinacialPeriod($stock_item->company_id);
            if ($financial_period == null) {
                throw new \Exception("Invalid finacial period");
            }
            $model->finacial_period_id = $financial_period->id;



            $model->company_id = $stock_item->company_id;
            $model->stock_category_id = $stock_item->stock_category_id;
            $model->stock_sub_category_id = $stock_item->stock_sub_category_id;
            $model->sku = $stock_item->sku;
            $model->name = $stock_item->name;
            $model->measurement_unit = $stock_item->stockSubCategory->measurements_unit;
            if ($model->decription == null) {
                $model->decription = $stock_item->type;
            }

            $quantity = abs($model->quantity);
            if ($quantity < 1) {
                throw new \Exception("Invalid Quantity");
            }

            $model->selling_price = $stock_item->selling_price;
            $model->total_sales = $model->selling_price * $quantity;
            $model->quantity = $quantity;

            // calculating profits

            if ($model->type == "Sale" || $model->type == "Internel Use") {
                // $model->profit = abs($model->profit);
                $model->total_sales = abs($model->total_sales);
                $model->profits = $model->total_sales  - ($stock_item->buying_price * $quantity);
            } else {
                $model->total_sales = 0;
                $model->profits = 0;
            }


            // we are making sure someone cant make sales when quantity is geater than the available stock

            $current_quamtity = $stock_item->current_quantity;
            if ($current_quamtity < $current_quamtity) {
                throw new \Exception("Insufficient Stock");
            }

            // logic what will reduce the current quantity when a stock record is made ie sales
            $new_quantity = $current_quamtity - $quantity;
            $stock_item->current_quantity = $new_quantity;
            $stock_item->save();


            return $model;
        });

        static::created(function ($model) {
            $stock_item = StockItem::find($model->stock_item_id);
            if ($stock_item == null) {
                throw new \Exception("Invalid stock sub Category");
            }

            $stock_item->stockSubCategory->update_self();
            $stock_item->stockSubCategory->StockCategory->update_self();
        });
    }
}
