<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockCategory extends Model
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
        $stock_items = StockItem::where('stock_category_id', $this->id)
            ->where('finacial_period_id', $active_finacial_period->id)
            ->get();
        foreach ($stock_items as $key => $value) {
            $total_buying_price += ($value->buying_price * $value->original_quantity);
            $total_selling_price += ($value->selling_price * $value->original_quantity);
        }

        $total_expected_profit = $total_selling_price - $total_buying_price;

         // earned profits
         $this->earned_price = StockRecord::where('stock_category_id', $this->id)
         ->where('finacial_period_id', $active_finacial_period->id)
         ->sum('profits');

        $this->buying_price = $total_buying_price;
        $this->selling_price = $total_selling_price;
        $this->expected_price = $total_expected_profit;
        $this->save();
    }
}
