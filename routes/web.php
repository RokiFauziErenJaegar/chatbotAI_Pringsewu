<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatController;

Route::get('/', [ChatController::class, 'index'])->name('chat.index');

Route::post('/conversation/new', [ChatController::class, 'newConversation'])
    ->middleware(['throttle:chat'])
    ->name('chat.new');

Route::get('/conversation/{id}', [ChatController::class, 'openConversation'])
    ->name('chat.open');

Route::get('/conversation/{id}/download', [ChatController::class, 'downloadConversation'])
    ->name('chat.download');

Route::post('/chat', [ChatController::class, 'chat'])
    ->middleware(['throttle:chat'])
    ->name('chat.send');
