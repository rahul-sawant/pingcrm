<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\ContactsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ImagesController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\UsersController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\OwnerOnly;
use App\Http\Controllers\{EndpointsController, OrganizationsController, StreamsController};

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Auth

Route::get('login', [AuthenticatedSessionController::class, 'create'])
    ->name('login')
    ->middleware('guest');

Route::post('login', [AuthenticatedSessionController::class, 'store'])
    ->name('login.store')
    ->middleware('guest');

Route::delete('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

// Dashboard

Route::get('/', [DashboardController::class, 'index'])
    ->name('dashboard')
    ->middleware('auth');

// Users

Route::group(['middleware' => ['auth', OwnerOnly::class]], function () {
    //Users
    Route::get('users', [UsersController::class, 'index'])->name('users');
    Route::get('users/create', [UsersController::class, 'create'])->name('users.create');
    Route::post('users', [UsersController::class, 'store'])->name('users.store');

    // Organization
    Route::get('organizations', [OrganizationsController::class, 'index'])->name('organizations');
    Route::get('organizations/create', [OrganizationsController::class, 'create'])->name('organizations.create');
    Route::post('organizations', [OrganizationsController::class, 'store'])->name('organizations.store');
    Route::get('organizations/{organization}/edit', [OrganizationsController::class, 'edit'])->name('organizations.edit');
    Route::put('organizations/{organization}', [OrganizationsController::class, 'update'])->name('organizations.update');
    Route::delete('organizations/{organization}', [OrganizationsController::class, 'destroy'])->name('organizations.destroy');
    Route::put('organizations/{organization}/restore', [OrganizationsController::class, 'restore'])->name('organizations.restore');

    // Endpoints
    Route::get('endpoints', [EndpointsController::class, 'index'])->name('endpoints');
    Route::get('endpoints/create', [EndpointsController::class, 'create'])->name('endpoints.create');
    Route::post('endpoints', [EndpointsController::class, 'store'])->name('endpoints.store');
    Route::get('endpoints/{endpoint}/edit', [EndpointsController::class, 'edit'])->name('endpoints.edit');
    Route::put('endpoints/{endpoint}', [EndpointsController::class, 'update'])->name('endpoints.update');
    Route::delete('endpoints/{endpoint}', [EndpointsController::class, 'destroy'])->name('endpoints.destroy');
    Route::put('endpoints/{endpoint}/restore', [EndpointsController::class, 'restore'])->name('endpoints.restore');

    // Streams
    Route::get('streams', [StreamsController::class, 'index'])->name('streams');
    Route::get('streams/create', [StreamsController::class, 'create'])->name('streams.create');
    Route::post('streams', [StreamsController::class, 'store'])->name('streams.store');
    Route::get('streams/{stream}/edit', [StreamsController::class, 'edit'])->name('streams.edit');
    Route::put('streams/{stream}', [StreamsController::class, 'update'])->name('streams.update');
    Route::delete('streams/{stream}', [StreamsController::class, 'destroy'])->name('streams.destroy');
    Route::put('streams/{stream}/restore', [StreamsController::class, 'restore'])->name('streams.restore');
});

Route::get('users/{user}/edit', [UsersController::class, 'edit'])
    ->name('users.edit')
    ->middleware('auth');

Route::put('users/{user}', [UsersController::class, 'update'])
    ->name('users.update')
    ->middleware('auth');

Route::delete('users/{user}', [UsersController::class, 'destroy'])
    ->name('users.destroy')
    ->middleware('auth');

Route::put('users/{user}/restore', [UsersController::class, 'restore'])
    ->name('users.restore')
    ->middleware('auth');

// Contacts

Route::get('contacts', [ContactsController::class, 'index'])
    ->name('contacts')
    ->middleware('auth');

Route::get('contacts/create', [ContactsController::class, 'create'])
    ->name('contacts.create')
    ->middleware('auth');

Route::post('contacts', [ContactsController::class, 'store'])
    ->name('contacts.store')
    ->middleware('auth');

Route::get('contacts/{contact}/edit', [ContactsController::class, 'edit'])
    ->name('contacts.edit')
    ->middleware('auth');

Route::put('contacts/{contact}', [ContactsController::class, 'update'])
    ->name('contacts.update')
    ->middleware('auth');

Route::delete('contacts/{contact}', [ContactsController::class, 'destroy'])
    ->name('contacts.destroy')
    ->middleware('auth');

Route::put('contacts/{contact}/restore', [ContactsController::class, 'restore'])
    ->name('contacts.restore')
    ->middleware('auth');

// Reports

Route::get('reports', [ReportsController::class, 'index'])
    ->name('reports')
    ->middleware('auth');

// Images

Route::get('/img/{path}', [ImagesController::class, 'show'])
    ->where('path', '.*')
    ->name('image');
