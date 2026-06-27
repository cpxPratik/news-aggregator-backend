<?php

use App\Http\Controllers\ArticleController;
use App\Models\Author;
use App\Models\Category;
use App\Models\Source;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/test', function () {
    return response()->json(['message' => 'This is test api']);
});

Route::get('/articles', [ArticleController::class, 'index']);
Route::get('/articles/{article}', [ArticleController::class, 'show']);

Route::get('/sources', fn () => Source::orderBy('slug')->get());
Route::get('/categories', fn () => Category::orderBy('slug')->get());
Route::get('/authors', fn () => Author::orderBy('slug')->get());
