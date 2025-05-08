<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CallController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\TicketController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);


Route::middleware(['auth:sanctum'])->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);
    // Agent Routes


    Route::prefix('calls')->name('calls.')->group(function () {
        Route::get('/', [CallController::class, 'index'])->name('index');
        Route::get('/create', [CallController::class, 'create'])->name('create');
        Route::post('/', [CallController::class, 'store'])->name('store');
        Route::get('/{call}', [CallController::class, 'show'])->name('show');
        Route::get('/{call}/edit', [CallController::class, 'edit'])->name('edit');
        Route::put('/{call}', [CallController::class, 'update'])->name('update');
        Route::delete('/{call}', [CallController::class, 'destroy'])->name('delete');
        Route::get('/{call}/tickets/create', [TicketController::class, 'createFromCall'])->name('create-ticket');
    });

    Route::prefix('tickets')->name('tickets.')->group(function () {
        Route::get('/create', [TicketController::class, 'create'])->name('create');
        Route::post('/', [TicketController::class, 'store'])->name('store');
        Route::get('/{ticket}/edit', [TicketController::class, 'edit'])->name('edit');
        Route::put('/{ticket}', [TicketController::class, 'update'])->name('update');
        Route::post('/{ticket}/status', [TicketController::class, 'updateStatus'])->name('update-status');
    });

    Route::prefix('comments')->name('comments.')->group(function () {
        Route::post('/tickets/{ticket}', [CommentController::class, 'store'])->name('store');
        Route::put('/{comment}', [CommentController::class, 'update'])->name('update');
        Route::delete('/{comment}', [CommentController::class, 'destroy'])->name('delete');
    });


    // Supervisor Routes
    Route::prefix('calls')->name('calls.')->group(function () {
        Route::get('/', [CallController::class, 'index'])->name('index');
        Route::get('/{call}', [CallController::class, 'show'])->name('show');
    });

    Route::prefix('tickets')->name('tickets.')->group(function () {
        Route::get('/', [TicketController::class, 'index'])->name('index');
        Route::get('/{ticket}', [TicketController::class, 'show'])->name('show');
        Route::put('/{ticket}', [TicketController::class, 'update'])->name('update');
        Route::delete('/{ticket}', [TicketController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('comments')->name('comments.')->group(function () {
        Route::post('/tickets/{ticket}', [CommentController::class, 'store'])->name('store');
        Route::put('/{comment}', [CommentController::class, 'update'])->name('update');
        Route::delete('/{comment}', [CommentController::class, 'destroy'])->name('delete');
    });
});
