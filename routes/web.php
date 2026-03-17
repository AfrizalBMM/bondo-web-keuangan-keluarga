<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/onboarding', function () {
    return Inertia::render('Onboarding');
})->middleware(['auth', 'verified'])->name('onboarding');

Route::get('/wallets', [App\Http\Controllers\WalletController::class, 'index'])->middleware(['auth', 'verified'])->name('wallets');
Route::post('/wallets', [App\Http\Controllers\WalletController::class, 'store'])->middleware(['auth', 'verified'])->name('wallets.store');

Route::get('/transactions', [App\Http\Controllers\TransactionController::class, 'index'])->middleware(['auth', 'verified'])->name('transactions');
Route::post('/transactions', [App\Http\Controllers\TransactionController::class, 'store'])->middleware(['auth', 'verified'])->name('transactions.store');
Route::post('/transactions/smart-add', [App\Http\Controllers\TransactionController::class, 'smartStore'])->middleware(['auth', 'verified'])->name('transactions.smart');

Route::get('/categories', [App\Http\Controllers\CategoryController::class, 'index'])->middleware(['auth', 'verified'])->name('categories');
Route::post('/categories', [App\Http\Controllers\CategoryController::class, 'store'])->middleware(['auth', 'verified'])->name('categories.store');

Route::get('/assets', [App\Http\Controllers\AssetController::class, 'index'])->middleware(['auth', 'verified'])->name('assets');
Route::post('/assets', [App\Http\Controllers\AssetController::class, 'store'])->middleware(['auth', 'verified'])->name('assets.store');

Route::get('/goals', [App\Http\Controllers\GoalController::class, 'index'])->middleware(['auth', 'verified'])->name('goals');
Route::post('/goals', [App\Http\Controllers\GoalController::class, 'store'])->middleware(['auth', 'verified'])->name('goals.store');
Route::post('/goals/{goal}/deposit', [App\Http\Controllers\GoalController::class, 'deposit'])->middleware(['auth', 'verified'])->name('goals.deposit');

Route::get('/debts', [App\Http\Controllers\DebtController::class, 'index'])->middleware(['auth', 'verified'])->name('debts');
Route::post('/debts', [App\Http\Controllers\DebtController::class, 'store'])->middleware(['auth', 'verified'])->name('debts.store');
Route::post('/debts/{debt}/payment', [App\Http\Controllers\DebtController::class, 'payment'])->middleware(['auth', 'verified'])->name('debts.payment');

Route::get('/budget', [App\Http\Controllers\BudgetController::class, 'index'])->middleware(['auth', 'verified'])->name('budget');
Route::post('/budget', [App\Http\Controllers\BudgetController::class, 'store'])->middleware(['auth', 'verified'])->name('budget.store');

Route::get('/reports', function () {
    return Inertia::render('Reports');
})->middleware(['auth', 'verified'])->name('reports');

Route::get('/notifications', function () {
    return Inertia::render('Notifications');
})->middleware(['auth', 'verified'])->name('notifications');

Route::post('/push-subscriptions', [App\Http\Controllers\PushNotificationController::class, 'store'])->middleware(['auth', 'verified'])->name('push.subscribe');
Route::post('/push-subscriptions/delete', [App\Http\Controllers\PushNotificationController::class, 'destroy'])->middleware(['auth', 'verified'])->name('push.unsubscribe');

Route::post('/family/create', function () {
    return redirect()->route('dashboard');
})->middleware(['auth', 'verified'])->name('family.create');

Route::post('/family/join', function () {
    return redirect()->route('dashboard');
})->middleware(['auth', 'verified'])->name('family.join');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

Route::get('/test-push', function (Illuminate\Http\Request $request) {
    if (!$request->user()) {
        return "Must log in first.";
    }
    
    $request->user()->notify(new \App\Notifications\TestWebPushNotification());
    
    return "Push notification queued/sent! Check browser.";
})->middleware(['auth', 'verified']);
