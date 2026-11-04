<?php

use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\FaqController;
use App\Http\Controllers\Api\V1\MenuController;
use App\Http\Controllers\Api\V1\PageController;
use App\Http\Controllers\Api\V1\PostController;
use App\Http\Controllers\Api\V1\QuestionController;
use App\Http\Controllers\Api\V1\QuestionTopicController;
use App\Http\Controllers\Api\V1\SettingController;
use App\Http\Controllers\Api\V1\SlideController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('v1')->name('api.v1.')->middleware('throttle:api')->group(function () {
    Route::get('posts', [PostController::class, 'index'])->name('posts.index');
    Route::get('posts/{post:slug}', [PostController::class, 'show'])->name('posts.show');

    Route::get('pages', [PageController::class, 'index'])->name('pages.index');
    Route::get('pages/{page:slug}', [PageController::class, 'show'])->name('pages.show');

    Route::get('categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('slides', [SlideController::class, 'index'])->name('slides.index');
    Route::get('menus/{location}', [MenuController::class, 'show'])->name('menus.show');
    Route::get('settings', [SettingController::class, 'index'])->name('settings.index');

    Route::get('faqs', [FaqController::class, 'index'])->name('faqs.index');
    Route::get('question-topics', [QuestionTopicController::class, 'index'])->name('question-topics.index');

    Route::get('questions', [QuestionController::class, 'index'])->name('questions.index');
    Route::get('questions/{question}', [QuestionController::class, 'show'])->name('questions.show');
    Route::post('questions', [QuestionController::class, 'store'])
        ->middleware('throttle:questions')
        ->name('questions.store');
});
