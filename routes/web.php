<?php
use Illuminate\Support\Facades\Route;

// # SI IMPORTA IL ProjectController
use App\Http\Controllers\Admin\ProjectController;

use App\Http\Controllers\Admin\PageController as AdminPageController;

use App\Http\Controllers\Guest\PageController as GuestPageController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// # ROTTE UTENTI NON LOGGATI
Route::get('/', [GuestPageController::class, 'index'])->name('guest.home');

// # ROTTE UTENTI LOGGATI
Route::middleware(['auth', 'verified'])
  ->prefix('admin')
  ->name('admin.')
  ->group(function () {

    Route::get('/', [AdminPageController::class, 'index'])->name('home');
    // # QUI IMPOSTIAMO LA ROTTA DEL RESOURCE CONTROLLER
    Route::resource('projects', ProjectController::class);
    // # QUI CI CREIAMO UNA NUOVA ROTTA PER ELIMINARE L'IMMAGINE NELL'EDIT
    Route::delete('/projects/{project}/delete-image', [ProjectController::class, 'deleteImage'])->name('projects.delete-image');
  });

require __DIR__ . '/auth.php';