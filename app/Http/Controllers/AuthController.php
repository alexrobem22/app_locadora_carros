<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Http\Middleware\RefreshToken;

class AuthController extends Controller
{
    //
    public function login(Request $request){

        $credenciais = $request->all(['email', 'password']);//mesmo sendo o metodo all() pode especificar os campos que queremos dentro de um array[] $request->all(['email', 'password'])

        //dd($credenciais);
        //autenticação (email e senha)
        $token = auth('api')->attempt($credenciais);
        // dd($token);

        //retorna um json web token
        if($token){//usuario autenticado com sucesso
            return response()->json(['token' => $token]);
        }
        else{//erro de usuario ou senha
            return response()->json(['erro' => 'Usuario ou senha invalido'], 403);

            // 401 = Unauthor -> nao autorizado
            // 403 = forbidden-> proibido (login invalido)
        }
        return 'login';
    }

    public function logout(){
        auth('api')->logout();
        return response()->json(['msg' => ' O logout foi realizado com sucesso']);
    }

    public function refresh(){

        $token = auth('api')->refresh();//cliente encaminhe um jwt valido

        return response()->json(['token' => $token]);
    }

    public function me(){

        return response()->json(auth()->user());

    }

    public function cadastar(Request $request){

        $senha = bcrypt($request->password); //encriptar senha
        // $senha = Hash::make($request->passowrd);//
        // dd($senha);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $senha

        ]);

        return response()->Json($user, 201);
    }
}
