<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Utils
{
    // method for getting users

    public static function get_user(Request $r)
    {
         $logged_in_user_id = $r->header("logged_in_user_id");
         $u = User::find($logged_in_user_id);
         return $u;


    }
    //methods for a success response
    public static function success($data, $message)
    {
        header('Content-Type: application/json');
        http_response_code(200);
        echo json_encode([
            'code' => 1,
            'message' => $message,
            'data' => $data,
        ]);
        die();
    }

    //methods for a success response
    public static function error($message)
    {
        header('Content-Type: application/json');
        http_response_code(200);
        echo json_encode([
            'code' => 0,
            'message' => $message,
        ]);
        die();
    }


    static function getActiveFinacialPeriod($company_id)
    {
        return FinacialPeriod::where('company_id', $company_id)
            ->where('status', 'active')->first();
    }

    static public function generateSKU($sub_category_id)
    {
        $year = date("y");
        $sub_category = StockSubCategory::find($sub_category_id);
        $serial = StockItem::where('stock_sub_category_id', $sub_category_id)->count() + 1;
        $sku = $year . "-" . $sub_category->id . "-" . "00" . $serial;
        return $sku;
    }
}
