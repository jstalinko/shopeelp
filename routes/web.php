<?php

use App\Http\Controllers\CampaignController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

use App\Http\Controllers\LinkController;

use App\Http\Controllers\TemplateController;

Route::group(['prefix' => 'dashboard','middleware' => ['auth','verified']] , function(){
    Route::get('/' ,fn() => Inertia::render('Dashboard/Index'))->name('dashboard');
    
    // Links CRUD Routes
    Route::get('/links', [LinkController::class, 'index'])->name('dashboard.links');
    Route::post('/links', [LinkController::class, 'store'])->name('dashboard.links.store');
    Route::put('/links/{link}', [LinkController::class, 'update'])->name('dashboard.links.update');
    Route::delete('/links/{link}', [LinkController::class, 'destroy'])->name('dashboard.links.destroy');
    Route::patch('/links/{link}/toggle-active', [LinkController::class, 'toggleActive'])->name('dashboard.links.toggle-active');

    // Templates Configuration Routes
    Route::get('/templates', [TemplateController::class, 'index'])->name('dashboard.templates');
    Route::put('/templates/{template}/config', [TemplateController::class, 'updateConfig'])->name('dashboard.templates.update-config');

    Route::get('/stats' ,fn() => Inertia::render('Dashboard/Stats'))->name('dashboard.stats');
    Route::get('/config' ,fn() => Inertia::render('Dashboard/Config'))->name('dashboard.config');

});


require __DIR__.'/settings.php';
require __DIR__.'/auth.php';


Route::get('/{slug}' , CampaignController::class)->name('campaignEngine');