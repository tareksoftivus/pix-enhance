<?php

use App\Modules\Support\Http\Controllers\User\SupportTicketsController;
use Illuminate\Support\Facades\Route;

Route::get('support-tickets', [SupportTicketsController::class, 'index'])->name('support-tickets.index');
Route::get('support-tickets/create', [SupportTicketsController::class, 'create'])->name('support-tickets.create');
Route::post('support-tickets', [SupportTicketsController::class, 'store'])->name('support-tickets.store');
Route::get('support-tickets/{ticket}/messages', [SupportTicketsController::class, 'messages'])->name('support-tickets.messages');
Route::get('support-tickets/{ticket}', [SupportTicketsController::class, 'show'])->name('support-tickets.show');
Route::post('support-tickets/{ticket}/reply', [SupportTicketsController::class, 'reply'])->name('support-tickets.reply');
