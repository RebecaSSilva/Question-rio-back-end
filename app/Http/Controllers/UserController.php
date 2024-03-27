<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class UserController extends Controller
{
    // Endpoint para criar um novo usuário
    public function create(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'responses_this_month ' => 'nullable|responses_this_month',
            'password' => 'required|string|min:6',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'responses_this_month' => $data['responses_this_month'],
            'password' => bcrypt($data['password']),
        ]);

        return response()->json($user, 201);
    }

    // Endpoint para fazer login do usuário
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            return response()->json('Usuário autenticado com sucesso' , 200, [], JSON_UNESCAPED_UNICODE);
        }

        return response()->json(['message' => 'Não autorizado'], 401, [], JSON_UNESCAPED_UNICODE);
    }

    // Endpoint para mostrar os detalhes do usuário atual, incluindo o consumo de respostas
    public function show(Request $request)
    {
        // Obter o usuário autenticado
        $user = $request->user();
    
        // Verificar se o usuário está autenticado
        if (!$user) {
            return response()->json(['error' => 'Usuário não autenticado.'], 401);
        }
    
        return response()->json($user);
    }
}
