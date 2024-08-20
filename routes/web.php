<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UtilizatorController;
use App\Http\Controllers\CondicaController;
use App\Http\Controllers\PrezentaController;
use App\Http\Controllers\RaportController;
use App\Http\Controllers\OptiuniController;
use App\Http\Controllers\DashboardController;

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
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'IntrariIesiri']) 
->middleware(['auth', 'verified'])
->name('dashboard');
Route::get('/dashboard/user-count', [DashboardController::class, 'userCount']);
Route::get('/dashboard/checkin-count', [DashboardController::class, 'checkInCount']);
Route::get('/dashboard/checkout-count', [DashboardController::class, 'checkOutCount']);
Route::get('/dashboard/absenti-count', [DashboardController::class, 'AbsentiCount']);
Route::get('/dashboard/data', [DashboardController::class, 'date']);

Route::get('/utilizatori', [UtilizatorController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('utilizatori');
Route::post('/utilizatori/newuser', [UtilizatorController::class, 'newuser'])->name('utilizatori.newuser');
Route::get('/users/{id}/edit', [UtilizatorController::class, 'edit'])->name('utilizatori.edit');
Route::put('/users/{id}', [UtilizatorController::class, 'update'])->name('utilizatori.update');
Route::delete('/utilizatori/{id}',[UtilizatorController::class, 'delete'])->name('utilizatori.delete');

Route::get('/prezenta', [PrezentaController::class, 'index'])
->middleware(['auth', 'verified'])
->name('prezenta');
Route::get('/prezenta/{date}', [PrezentaController::class, 'data'])->middleware(['auth', 'verified'])->name('prezenta.data');
Route::get('/prezentalunara', [PrezentaController::class, 'lunar'])->middleware(['auth', 'verified'])->name('prezenta.lunara');
Route::get('/prezentalunara/{date}', [PrezentaController::class, 'lunardata'])->middleware(['auth', 'verified'])->name('prezenta.lunardata');


Route::get('/raport', [RaportController::class, 'index'])
->middleware(['auth', 'verified'])
->name('raport');
Route::get('/raport/{date}', [RaportController::class, 'data'])->name('raport.data');
Route::get('/raportlunar', [RaportController::class, 'lunar'])->name('raportlunar');
Route::get('/raportlunar/{date}', [RaportController::class, 'lunardata'])->name('raportlunar.data');

Route::get('/optiuni', [OptiuniController::class, 'index'])
->middleware(['auth', 'verified'])
->name('optiuni');
Route::post('/export', [OptiuniController::class, 'exportData'])->name('optiuni.exportData');
Route::get('/condica', [CondicaController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('condica');
Route::get('/condica/{date}', [CondicaController::class,'condicaIndex'])->name('condica.data');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
