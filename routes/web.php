<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Settings\GitHubConnectionController;
use App\Http\Controllers\RepositoryController;
use App\Http\Controllers\PullRequestController;
use App\Http\Controllers\WorkflowRunController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\WebhookEventController;
use App\Http\Controllers\Webhooks\GitHubWebhookController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth'])->get('/dashboard', DashboardController::class)->name('dashboard');
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/github', [GitHubConnectionController::class, 'edit'])->name('github.edit');
        Route::put('/github', [GitHubConnectionController::class, 'update'])->name('github.update');
        Route::post('/github/test', [GitHubConnectionController::class, 'test'])->name('github.test');
    });
    Route::get('/repositories', [RepositoryController::class, 'index'])->name('repositories.index');
    Route::post('/repositories/sync', [RepositoryController::class, 'sync'])->name('repositories.sync');
    Route::get('/pull-requests', [PullRequestController::class, 'index'])->name('pull-requests.index');
    Route::post('/pull-requests/sync', [PullRequestController::class, 'sync'])->name('pull-requests.sync');
    Route::get('/workflow-runs', [WorkflowRunController::class, 'index'])->name('workflow-runs.index');
    Route::post('/workflow-runs/sync', [WorkflowRunController::class, 'sync'])->name('workflow-runs.sync');
    Route::middleware(['auth'])->get('/webhook-events', [WebhookEventController::class, 'index'])
        ->name('webhook-events.index');
    Route::post('/webhooks/github', GitHubWebhookController::class)
        ->name('webhooks.github');
});

require __DIR__.'/auth.php';
