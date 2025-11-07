<?php

use App\Controllers\Admin\ContattiManagerController;
use App\Controllers\Admin\CorsiManagerController;
use App\Controllers\Admin\CurriculumManagmentController;
use App\Controllers\Admin\HomeManagerController;
use App\Core\Services\Route;
use App\Controllers\LawController;
use App\Controllers\HomeController;
use App\Controllers\ErrorsController;
use App\Controllers\ContattiController;
use App\Controllers\CertificatiController;
use App\Controllers\ProgettiController;
use App\Controllers\Auth\AuthController;
use App\Controllers\PortfolioController;
use App\Controllers\Auth\TokenController;
use App\Controllers\Admin\DashBoardController;
use App\Controllers\Admin\LawsMngController;
use App\Controllers\Admin\PortfolioManagerController as AdminPortfolio;
use App\Controllers\Admin\LogsController;
use App\Controllers\Admin\MaintenanceController;
use App\Controllers\Admin\ProfileMngController;
use App\Controllers\Admin\ProjectManagerController;
use App\Controllers\Admin\SkillMngController;

// Guest Pages
// Route::get('/cookie', LawController::class, 'cookie');
// Route::get('/laws', LawController::class, 'home');




// Auth
Route::post('/sign-up', AuthController::class, 'registration');
Route::post('/token/change-password', TokenController::class, 'validatePin');

 // Admin 
Route::get('/admin/dashboard', DashBoardController::class, 'index');
Route::get('/admin/logout', DashBoardController::class, 'logout');

// // Administration Portfolio
// Route::get('/admin/portfolio', AdminPortfolio::class, 'index');
// Route::post('/admin/portfolio', AdminPortfolio::class, 'store');
// Route::get('/admin/portfolio-edit/{id}',AdminPortfolio::class,'edit');
// Route::post('/admin/portfolio-update/{id}',AdminPortfolio::class,'update');
// Route::post('/admin/portfolio-delete/{id}',AdminPortfolio::class,'destroy');



// Administration Logs ad Messages
Route::get('/admin/logs',  LogsController::class,'index');




// Asministrarion  Projects
Route::post('/admin/progetti-edit/{id}', ProjectManagerController::class,'update');
Route::delete('/admin/project-delete/{id}', ProjectManagerController::class,'destroy');




// Administration Article


Route::post('/admin/home-delete/{id}',HomeManagerController::class,'destroy');

// Administration Profile
Route::post('/admin/profile',ProfileMngController::class, 'store');
Route::get('/admin/profile/{id}',ProfileMngController::class, 'edit');
Route::post('/admin/profile/{id}',ProfileMngController::class, 'update');
Route::post('/admin/profile-delete/{id}',ProfileMngController::class,'destroy');

// Administration Skills
Route::post('/admin/skill',SkillMngController::class, 'store');
Route::get('/admin/skill/{id}',SkillMngController::class, 'edit');
Route::post('/admin/skill/{id}',SkillMngController::class, 'update');
Route::post('/admin/skill-delete/{id}',SkillMngController::class,'destroy');

// Administration maintenance
Route::get('/admin/settings',MaintenanceController::class,'index');
Route::post('/admin/settings',MaintenanceController::class,'submit');

// Restituisce l'array delle rotte
return Route::all();
