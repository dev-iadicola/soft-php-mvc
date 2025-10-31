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
Route::get('/forgot', AuthController::class, 'forgotPassword');
Route::get('/sign-up', AuthController::class, 'signUp');
Route::get('/validate-pin/{token}', TokenController::class, 'pagePin');
Route::post('/login', AuthController::class, 'login');
Route::post('/forgot', TokenController::class, 'forgotPasswordToken');
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
Route::get('/admin/logs', LogsController::class,'index');
Route::get('/admin/contatti',ContattiManagerController::class,'index');
Route::get('/admin/contatti/{id}',ContattiManagerController::class,'get');
Route::post('/admin/contatti-delete/{id}',ContattiManagerController::class,'destroy');



// Asministrarion  Projects
Route::get('/admin/progetti',ProjectManagerController::class,'index');
Route::post('/admin/progetti',ProjectManagerController::class,'store');
Route::get('/admin/progetti-edit/{id}', ProjectManagerController::class,'edit');
Route::post('/admin/progetti-edit/{id}', ProjectManagerController::class,'update');
Route::delete('/admin/project-delete/{id}', ProjectManagerController::class,'destroy');

// Administration Courses
Route::get('/admin/corsi',CorsiManagerController::class,'index');
Route::post('/admin/corsi',CorsiManagerController::class,'store');
Route::get('/admin/corso-edit/{id}', CorsiManagerController::class,'edit');
Route::post('/admin/corso-update/{id}', CorsiManagerController::class,'update');
Route::delete('/admin/corso-delete/{id}', CorsiManagerController::class,'destroy');

// Administration curriculum
Route::get('/admin/cv', CurriculumManagmentController::class,'index');
Route::post('/admin/cv', CurriculumManagmentController::class,'store');
Route::delete('/admin/cv-delete/{id}', CurriculumManagmentController::class,'destroy');
Route::post('/download/{id}',CurriculumManagmentController::class,'download');

// Administration laws
Route::get('/admin/laws', LawsMngController::class,'index');
Route::post('/admin/laws', LawsMngController::class,'store');
Route::get('/admin/law-edit/{id}',LawsMngController::class,'edit');
Route::post('/admin/law-edit/{id}',LawsMngController::class,'update');
Route::delete('/admin/law-delete/{id}',LawsMngController::class,'destroy');

// Administration Article
Route::get('/admin/home',HomeManagerController::class, 'index');
Route::post('/admin/home',HomeManagerController::class, 'store');
Route::get('/admin/home/{id}',HomeManagerController::class, 'edit');
Route::post('/admin/home/{id}',HomeManagerController::class, 'update');
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
