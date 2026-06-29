<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FeedController;
use App\Http\Controllers\PreferenceController;
use App\Http\Resources\AuthorResource;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\SourceResource;
use App\Models\Author;
use App\Models\Category;
use App\Models\Source;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return [
        'data' => [
            'name' => config('app.name').' API',
            'version' => 'v1',
        ],
    ];
});

Route::middleware('throttle:auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('throttle:api')->group(function () {
    Route::get('/articles', [ArticleController::class, 'index']);
    Route::get('/articles/{article}', [ArticleController::class, 'show']);

    Route::get('/sources', fn () => SourceResource::collection(Source::orderBy('slug')->get()));
    Route::get('/categories', fn () => CategoryResource::collection(Category::orderBy('slug')->get()));
    Route::get('/authors', fn () => AuthorResource::collection(Author::orderBy('slug')->get()));

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user', [AuthController::class, 'profile']);

        Route::get('/preferences', [PreferenceController::class, 'show']);
        Route::put('/preferences', [PreferenceController::class, 'update']);

        Route::get('/feed', FeedController::class);
    });
});
