<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\GoalController;
use App\Http\Controllers\DebtController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PushNotificationController;
use App\Http\Controllers\AIChatController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// 1. Landing & Auth Redirect
Route::get('/', function () {
    return redirect()->route('login');
});

// 2. Onboarding (Bisa diakses tanpa harus punya keluarga dulu)
Route::get('/onboarding', function () {
    return Inertia::render('Onboarding');
})->middleware(['auth', 'verified'])->name('onboarding');

// 3. Family Actions (Proses pembuatan/join keluarga)
Route::post('/family/create', [App\Http\Controllers\FamilyController::class, 'store'])
    ->middleware(['auth', 'verified'])
    ->name('family.create');

Route::post('/family/join', [App\Http\Controllers\FamilyController::class, 'join'])
    ->middleware(['auth', 'verified'])
    ->name('family.join');

// 4. PROTECTED ROUTES (Hanya bisa diakses jika sudah login & PUNYA KELUARGA)
Route::middleware(['auth', 'verified', 'has_family'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Wallets - Menggunakan penamaan manual agar tepat 'wallets' bukan 'wallets.index'
    Route::get('/wallets', [WalletController::class, 'index'])->name('wallets');
    Route::post('/wallets', [WalletController::class, 'store'])->name('wallets.store');
    Route::put('/wallets/{wallet}', [WalletController::class, 'update'])->name('wallets.update');
    Route::delete('/wallets/{wallet}', [WalletController::class, 'destroy'])->name('wallets.destroy');

    // Transactions
    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions');
    Route::post('/transactions', [TransactionController::class, 'store'])->name('transactions.store');
    Route::put('/transactions/{transaction}', [TransactionController::class, 'update'])->name('transactions.update');
    Route::delete('/transactions/{transaction}', [TransactionController::class, 'destroy'])->name('transactions.destroy');

    // Categories
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

    // Assets
    Route::get('/assets', [AssetController::class, 'index'])->name('assets');
    Route::post('/assets', [AssetController::class, 'store'])->name('assets.store');
    Route::put('/assets/{asset}', [AssetController::class, 'update'])->name('assets.update');
    Route::delete('/assets/{asset}', [AssetController::class, 'destroy'])->name('assets.destroy');

    // Goals & Deposit
    Route::get('/goals', [GoalController::class, 'index'])->name('goals');
    Route::post('/goals', [GoalController::class, 'store'])->name('goals.store');
    Route::put('/goals/{goal}', [GoalController::class, 'update'])->name('goals.update');
    Route::delete('/goals/{goal}', [GoalController::class, 'destroy'])->name('goals.destroy');
    Route::post('/goals/{goal}/deposit', [GoalController::class, 'deposit'])->name('goals.deposit');

    // Debts & Payment
    Route::get('/debts', [DebtController::class, 'index'])->name('debts');
    Route::post('/debts', [DebtController::class, 'store'])->name('debts.store');
    Route::put('/debts/{debt}', [DebtController::class, 'update'])->name('debts.update');
    Route::delete('/debts/{debt}', [DebtController::class, 'destroy'])->name('debts.destroy');
    Route::post('/debts/{debt}/payment', [DebtController::class, 'payment'])->name('debts.payment');

    // Budgets
    Route::get('/budget', [BudgetController::class, 'index'])->name('budget');
    Route::post('/budget', [BudgetController::class, 'store'])->name('budget.store');
    Route::delete('/budget/{budget}', [BudgetController::class, 'destroy'])->name('budget.destroy');

    // Reports & Notifications
    Route::get('/reports', [ReportController::class, 'index'])->name('reports');
    Route::get('/reports/export-pdf', [ReportController::class, 'exportPdf'])->name('reports.export-pdf');
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications');
    Route::post('/notifications/mark-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-read');

    // Push Notifications
    Route::post('/push-subscriptions', [PushNotificationController::class, 'store'])->name('push.subscribe');
    Route::post('/push-subscriptions/delete', [PushNotificationController::class, 'destroy'])->name('push.unsubscribe');

    // AI Advisor Chat
    Route::get('/ai-advisor', [AIChatController::class, 'index'])->name('ai.advisor');
    Route::post('/ai-advisor/chat', [AIChatController::class, 'chat'])->name('ai.chat');
});

// 5. User Profile (Standar Laravel Breeze)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::patch('/profile/family', [ProfileController::class, 'updateFamily'])->name('family.update');
    Route::delete('/profile/family', [ProfileController::class, 'destroyFamily'])->name('family.destroy');
    Route::post('/profile/family/leave', [ProfileController::class, 'leaveFamily'])->name('family.leave');
    Route::delete('/profile/family/members/{user}', [ProfileController::class, 'removeMember'])->name('family.members.destroy');
});

// 6. Test Route
Route::get('/test-push', function (Illuminate\Http\Request $request) {
    $request->user()->notify(new \App\Notifications\TestWebPushNotification());
    return "Push notification sent!";
})->middleware(['auth', 'verified']);




require __DIR__.'/auth.php';