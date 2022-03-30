<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/invoice', [App\Http\Controllers\DOMPdfController::class, 'index'])->name('invoice');
Route::get('/invoice-pdf/{type}', [App\Http\Controllers\DOMPdfController::class, 'pdf'])->name('invoice.pdf');

//ajax-crud start:

Route::get('crud','CrudIndexController@index');
Route::post('upazila-list','CrudIndexController@upazila_lsit')->name('upazila.list');

Route::group(['prefix' => 'user', 'as' => 'user.'], function(){
    Route::post('store','CrudIndexController@store')->name('store');
    Route::post('list','CrudIndexController@userList')->name('list');
    Route::post('edit','CrudIndexController@userEdit')->name('edit');
    Route::post('show','CrudIndexController@userShow')->name('show');
    Route::post('delete','CrudIndexController@userDestroy')->name('delete');
    Route::post('change-status','CrudIndexController@userChnageStatus')->name('change.status');
});

//ajax-crud end

//excel file upload start:

Route::post('excel-file-upload','ExcelFileUploadController@index')->name('excel.file.upload');