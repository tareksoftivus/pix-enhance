<?php

use App\Modules\Support\Http\Controllers\Admin\SupportTicketsController;
use Illuminate\Support\Facades\Route;

Route::get('support-tickets', [SupportTicketsController::class, 'index'])->name('support-tickets.index');
Route::get('support-tickets/{ticket}/messages', [SupportTicketsController::class, 'messages'])->name('support-tickets.messages');
Route::get('support-tickets/{ticket}', [SupportTicketsController::class, 'show'])->name('support-tickets.show');
Route::post('support-tickets/{ticket}/reply', [SupportTicketsController::class, 'reply'])->name('support-tickets.reply');
Route::patch('support-tickets/{ticket}/status', [SupportTicketsController::class, 'updateStatus'])->name('support-tickets.status');
Route::delete('support-tickets/{ticket}', [SupportTicketsController::class, 'destroy'])->name('support-tickets.destroy');
