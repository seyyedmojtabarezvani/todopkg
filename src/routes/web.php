<?php

use Illuminate\Support\Facades\Route;
use Mojtaba\Todopkg\Controllers\MainController;

Route::get('/todo', [MainController::class, 'index'])->name('index');
Route::post('/add-label', [MainController::class, 'addLabel'])->name('addLabel');
Route::post('/add-task', [MainController::class, 'addTask'])->name('addTask');
Route::post('/add-task-labels', [MainController::class, 'addLabelToTask'])->name('addLabelToTask');
Route::post('/edit-task', [MainController::class, 'editTask'])->name('editTask');
Route::post('/edit-task-status', [MainController::class, 'editTaskStatus'])->name('editTaskStatus');
Route::get('/get-all-labels', [MainController::class, 'getAllLabels'])->name('getAllLabels');
Route::get('/get-task-by-label', [MainController::class, 'getTaskByLabel'])->name('getTaskByLabel');
Route::get('/get-task-by-id', [MainController::class, 'getTaskById'])->name('getTaskById');
