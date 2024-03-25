<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\FormController;
use App\Http\Controllers\AnswerController;
use App\Http\Controllers\QuestionController;
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


// Endpoint para registrar um novo usuário
Route::post('/register', [UserController::class, 'create']);

// Endpoint para fazer login do usuário
Route::post('/login', [UserController::class, 'login']);

// Endpoint para mostrar os detalhes do usuário atual
Route::get('/user/{id}', [UserController::class, 'show']);


// Rotas protegidas pelo middleware 'auth:sanctum'q is
// Route::middleware('auth:sanctum')->group(function () {
    // Rotas relacionadas aos formulários
    Route::post('/form', [FormController::class, 'create'])->middleware('auth');
    Route::get('/forms/{id}', [FormController::class, 'show']); // Mostrar detalhes de um formulário específico
    Route::get('/forms', [FormController::class, 'list']); // Listar todos os formulários do usuário atual
    
    Route::get('/answers/{formId}', [AnswerController::class, 'listByForm']); // Listar todas as respostas de um formulário específico

    // Rotas relacionadas às questões
    Route::post('/questions', [QuestionController::class, 'create']); // Criar uma nova questão
// });
    // Rotas relacionadas às respostas
    Route::post('/answers', [AnswerController::class, 'create']); // Criar uma nova resposta