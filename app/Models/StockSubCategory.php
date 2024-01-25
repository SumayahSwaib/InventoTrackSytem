<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockSubCategory extends Model
{
    use HasFactory;

    



    public  function update_self()

    {

        $active_finacial_period = Utils::getActiveFinacialPeriod($this->company_id);
        if ($active_finacial_period == null) {
            return;
        }

        $total_buying_price = 0;
        $total_selling_price = 0;
        $current_quantity = 0;

        $stock_items = StockItem::where('stock_sub_category_id', $this->id)
            ->where('finacial_period_id', $active_finacial_period->id)
            ->get();
        foreach ($stock_items as $key => $value) {
            $total_buying_price += ($value->buying_price * $value->original_quantity);
            $total_selling_price += ($value->selling_price * $value->original_quantity);
            $total_selling_price += ($value->selling_price * $value->original_quantity);
            $current_quantity += $value->current_quantity;
        }

        $total_expected_profit = $total_selling_price - $total_buying_price;

        $this->buying_price = $total_buying_price;
        $this->selling_price = $total_selling_price;
        $this->expected_price = $total_expected_profit;
        $this->current_quantity = $current_quantity;
        // check if in stock
        if ($current_quantity > $this->reorder_level) {
            $this->in_stock = "Yes";
        } else {
            $this->in_stock = "No";
        }

        // earned profits
        $total_sales = StockRecord::where('stock_sub_category_id', $this->id)
            ->where('finacial_period_id', $active_finacial_period->id)
            ->sum('profits');
        $this->earned_price = $total_sales;
        $this->save();
    }
    public function StockCategory()
    {
        return $this->belongsTo(StockCategory::class);
    }
    // appens for name_text
    protected $appends = ['name_text'];

    public function getNameTextAttribute()
    {
        $name_text = $this->name;
        if ($this->StockCategory != null) {
            $name_text = $name_text . "-" . $this->stockCategory->name . ")";
        }
        return $name_text;
    }
}
