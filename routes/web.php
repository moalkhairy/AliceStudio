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


    Voyager::routes();
});

Route::get('/', [\App\Http\Controllers\CreatorController::class, 'index'])->name('creator.index');
Route::post('/creator/generate', [\App\Http\Controllers\CreatorController::class, 'generate'])->name('creator.generate');


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

    Route::middleware('auth:client')->group(function () {
        Route::post('logout', [\App\Http\Controllers\ClientAuthController::class, 'logout'])->name('logout');
        Route::get('dashboard', fn() => view('client.dashboard'))->name('dashboard');
        Route::get('profile', fn() => view('client.profile'))->name('profile');
    });
});


Route::prefix('client')->name('client.')->group(function () {
    // guest routes (login/register etc.) remain as you already added…

    Route::middleware('auth:client')->group(function () {
        Route::get('dashboard', [\App\Http\Controllers\ClientAccountController::class, 'dashboard'])->name('dashboard');
        Route::get('profile', [\App\Http\Controllers\ClientAccountController::class, 'profile'])->name('profile');
        Route::post('profile', [\App\Http\Controllers\ClientAccountController::class, 'updateProfile'])->name('profile.update');
        Route::post('profile/password', [\App\Http\Controllers\ClientAccountController::class, 'updatePassword'])->name('profile.password');
        Route::post('logout', [\App\Http\Controllers\ClientAccountController::class, 'logout'])->name('logout');
    });
});