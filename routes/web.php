<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;

//Route::get("/", function () {
//    return view("welcome");
//});

Route::group(['prefix' => 'admin'], function () {
    Route::name('voyager.')->group(function () {
        Route::get('roles-index', [RoleController::class, 'index'])->name('roles.index-new');
        Route::get('roles/create-new', [RoleController::class, 'create'])->name('roles.create');
        Route::post('roles', [RoleController::class, 'store'])->name('roles.store');
        Route::get('roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit');
        Route::put('roles/{role}', [RoleController::class, 'update'])->name('roles.update');
        Route::delete('roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');
    });

    Route::name('voyager.')->group(function () {
        Route::get('users-index', [UserController::class, 'index'])->name('users.index-new');           // or users/index-new
        Route::get('users/create-new', [UserController::class, 'create'])->name('users.create-new');         // or users/create-new
        Route::post('users', [UserController::class, 'store'])->name('users.store');
        Route::get('users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy')->whereNumber('user');
    });

    Route::name('voyager.')->group(function () {
        Route::resource('studios', \App\Http\Controllers\Admin\StudioController::class);
        Route::resource('groups', \App\Http\Controllers\Admin\GroupController::class);
        Route::resource('sections', \App\Http\Controllers\Admin\SectionController::class);
        Route::resource('choices', \App\Http\Controllers\Admin\ChoiceController::class);

        Route::resource('steps', \App\Http\Controllers\Admin\StepController::class);
        Route::post('steps/{step}/add-sections', [\App\Http\Controllers\Admin\StepController::class, 'addSections'])->name('steps.add-sections');
        Route::delete('steps/{step}/remove-section/{pivot}', [\App\Http\Controllers\Admin\StepController::class, 'removeSection'])->name('steps.remove-section');
        Route::get('steps/{step}/toggle/{pivot}', [\App\Http\Controllers\Admin\StepController::class, 'togglePivot'])->name('steps.toggle-pivot');
        Route::get('steps/{step}/move-up/{pivot}', [\App\Http\Controllers\Admin\StepController::class, 'moveUp'])->name('steps.move-up');
        Route::get('steps/{step}/move-down/{pivot}', [\App\Http\Controllers\Admin\StepController::class, 'moveDown'])->name('steps.move-down');
    });

    Route::get('coin-packages', [\App\Http\Controllers\Admin\CoinPackageController::class, 'index'])->name('voyager.coin_packages.index');
    Route::get('coin-packages/create', [\App\Http\Controllers\Admin\CoinPackageController::class, 'create'])->name('voyager.coin_packages.create');
    Route::post('coin-packages', [\App\Http\Controllers\Admin\CoinPackageController::class, 'store'])->name('voyager.coin_packages.store');
    Route::get('coin-packages/{id}/edit', [\App\Http\Controllers\Admin\CoinPackageController::class, 'edit'])->name('voyager.coin_packages.edit');
    Route::put('coin-packages/{id}', [\App\Http\Controllers\Admin\CoinPackageController::class, 'update'])->name('voyager.coin_packages.update');
    Route::delete('coin-packages/{id}', [\App\Http\Controllers\Admin\CoinPackageController::class, 'destroy'])->name('voyager.coin_packages.destroy');
    Route::get('coin-packages/{id}', [\App\Http\Controllers\Admin\CoinPackageController::class, 'show'])->name('voyager.coin_packages.show');

    // -------------------------------------------------------------------------
    // Client Orders (read-only)
    // -------------------------------------------------------------------------
    Route::get('client-orders', [\App\Http\Controllers\Admin\ClientOrderController::class, 'index'])->name('voyager.client_orders.index');
    Route::get('client-orders/{id}', [\App\Http\Controllers\Admin\ClientOrderController::class, 'show'])->name('voyager.client_orders.show');

    // -------------------------------------------------------------------------
    // Wallet Transactions (read-only)
    // -------------------------------------------------------------------------
    Route::get('wallet-transactions', [\App\Http\Controllers\Admin\WalletTransactionController::class, 'index'])->name('voyager.wallet_transactions.index');
    Route::get('wallet-transactions/{id}', [\App\Http\Controllers\Admin\WalletTransactionController::class, 'show'])->name('voyager.wallet_transactions.show');


    Voyager::routes();
});

Route::get('/', [\App\Http\Controllers\CreatorController::class, 'index'])->name('creator.index');
Route::middleware('auth:client')->group(function () {
    Route::post('/creator/generate', [\App\Http\Controllers\CreatorController::class, 'generate'])
//    ->middleware(['client.auth','client.hasCoins'])
        ->name('creator.generate');
});
//Route::post('/creator/generate', [\App\Http\Controllers\CreatorController::class, 'generate'])->name('creator.generate');


Route::prefix('client')->name('client.')->group(function () {
    Route::middleware('guest:client')->group(function () {
        Route::get('login', [\App\Http\Controllers\ClientAuthController::class, 'showLogin'])->name('login');
        Route::post('login', [\App\Http\Controllers\ClientAuthController::class, 'login']);

        Route::get('register', [\App\Http\Controllers\ClientAuthController::class, 'showRegister'])->name('register');
        Route::post('register', [\App\Http\Controllers\ClientAuthController::class, 'register']);

        // Password reset (optional views below)
        Route::get('forgot-password', [\App\Http\Controllers\ClientAuthController::class, 'showForgot'])->name('password.request');
        Route::post('forgot-password', [\App\Http\Controllers\ClientAuthController::class, 'sendResetLink'])->name('password.email');
        Route::get('reset-password/{token}', [\App\Http\Controllers\ClientAuthController::class, 'showReset'])->name('password.reset');
        Route::post('reset-password', [\App\Http\Controllers\ClientAuthController::class, 'reset'])->name('password.update');

        // Social login
        Route::get('oauth/{provider}', [\App\Http\Controllers\ClientSocialController::class, 'redirect'])->name('oauth.redirect');
        Route::get('oauth/{provider}/callback', [\App\Http\Controllers\ClientSocialController::class, 'callback'])->name('oauth.callback');
    });

//    Route::middleware('auth:client')->group(function () {
////        Route::post('logout', [\App\Http\Controllers\ClientAuthController::class, 'logout'])->name('logout');
////        Route::get('dashboard', fn() => view('client.dashboard'))->name('dashboard');
////        Route::get('profile', fn() => view('client.profile'))->name('profile');
//    });
});


Route::prefix('client')->name('client.')->group(function () {
    Route::middleware('auth:client')->group(function () {
        Route::get('dashboard', [\App\Http\Controllers\ClientAccountController::class, 'dashboard'])->name('dashboard');
        Route::get('profile', [\App\Http\Controllers\ClientAccountController::class, 'profile'])->name('profile');
        Route::post('profile', [\App\Http\Controllers\ClientAccountController::class, 'updateProfile'])->name('profile.update');
        Route::post('profile/password', [\App\Http\Controllers\ClientAccountController::class, 'updatePassword'])->name('profile.password');
        Route::post('logout', [\App\Http\Controllers\ClientAccountController::class, 'logout'])->name('logout');
    });
});

Route::prefix('client')->name('client.')->group(function () {

    // OTP verify pages for logged-in clients
    Route::middleware('auth:client')->group(function () {
        Route::get('verify', [\App\Http\Controllers\ClientOtpController::class, 'showVerify'])->name('verify.show');
        Route::post('verify/send', [\App\Http\Controllers\ClientOtpController::class, 'send'])->name('verify.send');
        Route::post('verify', [\App\Http\Controllers\ClientOtpController::class, 'verify'])->name('verify.perform');

        // Packages & wallet (require verified)
        Route::middleware('client.verified')->group(function () {
            Route::get('packages', [\App\Http\Controllers\ClientPackageController::class, 'index'])->name('packages.index');
            Route::post('packages/{package}/buy', [\App\Http\Controllers\ClientPackageController::class, 'buy'])->name('packages.buy');

            Route::get('wallet', [\App\Http\Controllers\ClientWalletController::class, 'index'])->name('wallet.index');
        });
    });

    // Your existing auth routes (login/register/logout/dashboard/profile) remain as added earlier
});