<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Utils



{
    // function for file uploading
    public static function file_uploading($file)
    {

        if ($file == null) {
            return "";
        }
        $file_extension = $file->getClientOriginalExtension();
        $file_name = time() . "_" . rand(1000, 100000) . "." . $file_extension;
        $public_path = public_path() . "/storage/images";
        $file->move($public_path, $file_name);
        $url = 'images/' . $file_name;
        return $url;
    }
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
    static public function get_table_name()
    {
        $tables = DB::select('SHOW TABLES');
        $db_name = env('DB_DATABASE');
        $key = "Tables_in_" . $db_name;
        $table_names = [];
        foreach ($tables as $key => $table) {
            $db_name = "Tables_in_".env('DB_DATABASE');
            $table_names[$table->$db_name] = $table->$db_name;
        }
        return $table_names;
    }
}
