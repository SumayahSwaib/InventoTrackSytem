<?php

use App\Models\Gen;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

/* Route::get('/', function () {
    return view('welcome');
}); */

Route::get('generate-models', function () {
    $id = request('id');
    $gen = Gen::find($id);
    if($gen == null){
        return die('Gen not found');
    }
    $gen->gen_model();
    return die('generate-models');
});