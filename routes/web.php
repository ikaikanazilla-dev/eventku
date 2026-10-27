<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CheckInController;
use App\Http\Controllers\OrganizerController;
use App\Http\Controllers\ParticipantController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\TicketQrController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PublicController::class, 'home'])->name('home');
Route::get('/events', [PublicController::class, 'events'])->name('events.index');
Route::get('/events/{event:slug}', [PublicController::class, 'show'])->name('events.show');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'loginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
    Route::get('/register', [AuthController::class, 'registerForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
});
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware(['auth', 'active'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::middleware('role:participant')->prefix('participant')->name('participant.')->group(function () {
        Route::get('/dashboard', [ParticipantController::class, 'dashboard'])->name('dashboard');
        Route::get('/tickets', [ParticipantController::class, 'tickets'])->name('tickets');
        Route::post('/events/{event}/register', [ParticipantController::class, 'register'])->name('event.register');
        Route::get('/tickets/{ticket}', [ParticipantController::class, 'ticket'])->name('ticket.show');
        Route::get('/tickets/{ticket}/pdf', [ParticipantController::class, 'pdf'])->name('ticket.pdf');
        Route::get('/tickets/{ticket}/qr', [TicketQrController::class, 'show'])->name('ticket.qr');
    });
    Route::middleware('role:organizer')->prefix('organizer')->name('organizer.')->group(function () {
        Route::get('/dashboard', [OrganizerController::class, 'dashboard'])->name('dashboard');
        Route::get('/events', [OrganizerController::class, 'events'])->name('events');
        Route::get('/events/create', [OrganizerController::class, 'create'])->name('event.create');
        Route::post('/events', [OrganizerController::class, 'store'])->name('event.store');
        Route::get('/events/{event}', [OrganizerController::class, 'show'])->name('event.show');
        Route::get('/events/{event}/edit', [OrganizerController::class, 'edit'])->name('event.edit');
        Route::put('/events/{event}', [OrganizerController::class, 'update'])->name('event.update');
        Route::delete('/events/{event}', [OrganizerController::class, 'destroy'])->name('event.destroy');
        Route::post('/events/{event}/submit', [OrganizerController::class, 'submit'])->name('event.submit');
        Route::get('/registrations', [OrganizerController::class, 'registrations'])->name('registrations');
        Route::get('/events/{event}/checkin', [OrganizerController::class, 'checkin'])->name('checkin');
        Route::post('/events/{event}/checkin', [CheckInController::class, 'store'])->name('checkin.store');
        Route::get('/reports', [OrganizerController::class, 'reports'])->name('reports');
    });
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/users', [AdminController::class, 'users'])->name('users');
        Route::patch('/users/{user}/toggle', [AdminController::class, 'toggleUser'])->name('user.toggle');
        Route::patch('/users/{user}/role', [AdminController::class, 'updateRole'])->name('user.role');
        Route::get('/events', [AdminController::class, 'events'])->name('events');
        Route::get('/events/{event}', [AdminController::class, 'showEvent'])->name('event.show');
        Route::get('/events/{event}/edit', [AdminController::class, 'editEvent'])->name('event.edit');
        Route::put('/events/{event}', [AdminController::class, 'updateEvent'])->name('event.update');
        Route::post('/events/{event}/approve', [AdminController::class, 'approve'])->name('event.approve');
        Route::post('/events/{event}/reject', [AdminController::class, 'reject'])->name('event.reject');
        Route::get('/categories', [AdminController::class, 'categories'])->name('categories');
        Route::post('/categories', [AdminController::class, 'storeCategory'])->name('category.store');
        Route::get('/logs', [AdminController::class, 'logs'])->name('logs');
    });
});
