<?php

use App\Http\Controllers\ChatController;
use Illuminate\Support\Facades\Route;
/*
|--------------------------------------------------------------------------
| CHATBOT AI DASHBOARD PRINGSEWU
|--------------------------------------------------------------------------
*/

Route::get('/', [ChatController::class, 'index'])->name('chat.index');

Route::post('/chat', [ChatController::class, 'chat'])->name('chat.send');

Route::post('/conversation/new', [ChatController::class, 'newConversation'])
    ->name('chat.new');

Route::get('/conversation/{id}', [ChatController::class, 'openConversation'])
    ->name('chat.open');

Route::get('/conversation/{id}/download', [ChatController::class, 'downloadConversation'])
    ->name('chat.download');