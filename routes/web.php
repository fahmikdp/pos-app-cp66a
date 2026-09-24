<?php

use App\Livewire\Auth\Login;
use App\Livewire\Dashboard;
use App\Livewire\Manage\Categories;
use App\Livewire\Manage\MenuItems;
use App\Livewire\Pos\Cashier;
use App\Livewire\Pos\History;
use App\Livewire\Reports\Sales;
use App\Livewire\Reports\Transactions;
use App\Livewire\Settings\Logs;
use App\Livewire\Settings\Users;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('dashboard'));

Route::middleware('guest')->group(function () {
    Route::get('/login', Login::class)->name('login');
});

Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect()->route('login');
})->name('logout')->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');

    Route::get('/manage/categories', Categories::class)->name('manage.categories');
    Route::get('/manage/menu-items', MenuItems::class)->name('manage.menu-items');
    Route::get('/reports/transactions', Transactions::class)->name('reports.transactions');
    Route::get('/reports/sales', Sales::class)->name('reports.sales');

    Route::get('/pos/cashier', Cashier::class)->name('pos.cashier');
    Route::get('/pos/history', History::class)->name('pos.history');

    Route::get('/settings/users', Users::class)->name('settings.users');
    Route::get('/settings/logs', Logs::class)->name('settings.logs');
});
