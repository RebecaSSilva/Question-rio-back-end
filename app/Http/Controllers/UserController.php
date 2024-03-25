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
            'password' => 'required|string|min:6',
            // Adicione validações adicionais conforme necessário
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);

        return response()->json($user, 201);
    }

    // Endpoint para fazer login do usuário
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            return response()->json($user, 200);
        }

        return response()->json(['message' => 'Unauthorized'], 401);
    }

    // Endpoint para mostrar os detalhes do usuário atual, incluindo o consumo de respostas
    public function show($id)
    {
        $user = User::findOrFail($id);
        // Verifique se o usuário tem permissão para acessar seus próprios detalhes
        // Implemente a lógica de permissão conforme necessário

        // Implemente a lógica para calcular e retornar o consumo de respostas do usuário

        return response()->json($user);
    }
}
