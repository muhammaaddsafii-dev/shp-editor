<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PointController;
use App\Http\Controllers\PolylineController;
use App\Http\Controllers\PolygonController;

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

Route::get('/', function () {
    return view('dashboard.index');
});

//Point
//create point
Route::post('/store-point', [PointController::class, 'store'])->name('store-point');
//delete point
Route::delete('/delete-point/{id}', [PointController::class, 'destroy'])->name('delete-point');
//edit point
Route::get('/edit-point/{id}', [PointController::class, 'edit'])->name('edit-point');
//update point
Route::patch('/update-point/{id}', [PointController::class, 'update'])->name('update-point');
//modifikasi partial

//polyline

//create polyline
Route::post('/store-polyline', [PolylineController::class, 'store'])->name('store-polyline');
//delete polyline
Route::delete('/delete-polyline/{id}', [PolylineController::class, 'destroy'])->name('delete-polyline');
//edit polyline
Route::get('/edit-polyline/{id}', [PolylineController::class, 'edit'])->name('edit-polyline');
//update polyline
Route::patch('/update-polyline/{id}', [PolylineController::class, 'update'])->name('update-polyline');

//polygon
//delete polygon
Route::delete('/delete-polygon/{id}', [PolygonController::class, 'destroy'])->name('delete-polygon');
//edit polygon
Route::get('/edit-polygon/{id}', [PolygonController::class, 'edit'])->name('edit-polygon');
//create polygon
Route::post('/store-polygon', [PolygonController::class, 'store'])->name('store-polygon');
//update polygon
Route::patch('/update-polygon/{id}', [PolygonController::class, 'update'])->name('update-polygon');