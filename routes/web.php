<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;

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
