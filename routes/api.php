<?php

use App\Http\Controllers\ApiController;
use App\Models\StockItem;
use App\Models\StockSubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use function PHPUnit\Framework\returnSelf;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
// register api
Route::post('auth/register', [ApiController::class, 'register']);
Route::post('auth/login', [ApiController::class, 'login']);
Route::post('api/{model}', [ApiController::class, 'my_update']);
Route::get('api/{model}', [ApiController::class, 'my_List']);
Route::post('file-uploading', [ApiController::class, 'file_uploading']);




Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
// api for stock category
Route::get('/stock-sub-categories', function (Request $request) {
    $q = $request->get('q');

    $company_id = $request->get('company_id');
    if ($company_id == null) {
        return response()->json([
            'data' => [],
        ], 400);
    }

    $sub_categories = StockSubCategory::Where('company_id', $company_id)
        ->where('name', 'like', "%$q%")
        ->orderBy('name', 'asc')
        ->limit(20)
        ->get();
    $data = [];
    foreach ($sub_categories as $sub_category) {
        $data[] = [
            'id' => $sub_category->id,
            'text' => $sub_category->name_text . "(" . $sub_category->measurements_unit . ")",
        ];
    }
    // dd($data);
    return response()->json(['data' => $data,]);
});
// api for stock Item
Route::get('/stock-items', function (Request $request) {
    $q = $request->get('q');

    $company_id = $request->get('company_id');
    if ($company_id == null) {
        return response()->json([
            'data' => [],
        ], 400);
    }

    $stock_items = StockItem::Where('company_id', $company_id)
        ->where('name', 'like', "%$q%")
        ->orderBy('name', 'asc')
        ->limit(20)
        ->get();
    $data = [];
    foreach ($stock_items as $stock_item_id) {
        $data[] = [
            'id' => $stock_item_id->id,
            'text' => $stock_item_id->name_text,
        ];
    }
    // dd($data);
    return response()->json(['data' => $data,]);
});
