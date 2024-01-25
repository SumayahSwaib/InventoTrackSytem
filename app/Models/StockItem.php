<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use function PHPUnit\Framework\throwException;

class StockItem extends Model
{
    use HasFactory;
    // we are trying to add the category by creating the subcategory 
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model = self::prepare($model);
            $model->current_quantity = $model->original_quantity;

            return $model;
        });

        static::updating(function ($model) {
            $model = self::prepare($model);
            return $model;
        });

        static::created(function ($model) {
            $category = StockCategory::find($model->stock_category_id);
            $category->update_self();

            $subcategory = StockSubCategory::find($model->stock_sub_category_id);
            $subcategory->update_self();
        });

        static::updated(function ($model) {
            $category = StockCategory::find($model->stock_category_id);
            $category->update_self();
            $subcategory = StockSubCategory::find($model->stock_sub_category_id);
            $subcategory->update_self();
        });
        static::deleted(function ($model) {
            $category = StockCategory::find($model->stock_category_id);
            $category->update_self();
            $subcategory = StockSubCategory::find($model->stock_sub_category_id);
            $subcategory->update_self();
        });
    }


    static public function prepare($model)
    {
        $sub_category = StockSubCategory::find($model->stock_sub_category_id);
        if ($sub_category == null) {
            throw new \Exception("Invalid Stock Category");
        }
        $model->stock_category_id = $sub_category->stock_category_id;

        $user = User::find($model->created_by_id);
        if ($user == null) {
            throw new \Exception("Invalid user");
        }
        $financial_period = Utils::getActiveFinacialPeriod($user->company_id);
        if ($financial_period == null) {
            throw new \Exception("Invalid finacial period");
        }
        $model->finacial_period_id = $financial_period->id;
        $model->company_id = $user->company_id;

        if ($model->sku == null || strlen($model->sku) < 3) {
            $model->sku = Utils::generateSKU($model->company_id);
        }
        if ($model->update_sku == "Yes" && $model->generate_sku == "Manual") {
            $model->sku = Utils::generateSKU($model->company_id);
            $model->generate_sku = "No";
        }

        return $model;
    }

    // make the uploading of multiple images posible by converting the images to json data 
    public function getGalleryAttribute($value)
    {
        if ($value != null && strlen($value) > 3) {
            return json_decode($value);
        }
        return [];
    }

    public function setGalleryAttribute($value)
    {
        $this->attributes['gallery'] = json_encode($value);
    }


    // appens for name_text
    protected $appends = ['name_text'];

    public function getNameTextAttribute()
    {
        $name_text = $this->name;
        if ($this->stockSubCategory != null) {
            $name_text = $name_text . "-" . $this->stockSubCategory->name;
        }
        $name_text = $name_text . "(". number_format($this->current_quantity)."". $this->stockSubCategory->measurements_unit.")";
        return $name_text;
    }

    // stockasaubCategory relation
    public function stockCategory()
    {
        return $this->belongsTo(StockCategory::class);
    }

    public function stockSubCategory()
    {
        return $this->belongsTo(StockSubCategory::class);
    }
}
