<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Http\Requests\StoreClienteRequest;
use App\Http\Requests\UpdateClienteRequest;
use App\Repositories\ClienteRepository;
use Illuminate\Http\Request;


class ClienteController extends Controller
{

    public function __construct(Cliente $cliente)
    {
        $this->cliente = $cliente;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        $clienteRepository = new ClienteRepository($this->cliente);

        if($request->has('filtro')){

          $clienteRepository->filtro($request->filtro);

        }

            //o has verifica se um determinado paramentro no request existe se ta defenido
      if($request->has('atributos')){


        $clienteRepository->selectAtributos($request->atributos);

    }

    return response()->json($clienteRepository->getResultado(), 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreClienteRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $request->validate($this->cliente->rules());

        $cliente = $this->cliente->create([
            'nome' => $request->nome

        ]);

         return response()->json($cliente, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Cliente  $cliente
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        $cliente = $this->cliente->find($id);
        if($cliente === null){
            // return ['erro' => 'Recurso pesquisado não existe']; // json
            return response()->json(['erro' => 'Recurso pesquisado não existe'], 404); // no response eu passo o erro 404
        }
        return response()->json($cliente, 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Cliente  $cliente
     * @return \Illuminate\Http\Response
     */
    public function edit(Cliente $cliente)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateClienteRequest  $request
     * @param  \App\Models\Cliente  $cliente
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        $cliente = $this->cliente->find($id);

        if($cliente === null){

            return response()->json(['erro' => 'Impossivel realizar a atualizacão. O recurso solicitado não existe'], 404);
        }

        if ($request->method() === 'PATCH'){

            $regrasDinamicas = array();
            //percorrendo todas as regras definidas no Model
            foreach($cliente->rules() as $input => $regra){


                //coletar apenas as regras aplicaveis aos parametros parciais da requisicao
                if(array_key_exists($input, $request->all())){
                    $regrasDinamicas[$input] = $regra;

                }
            }

            $request->validate($regrasDinamicas);

        }
        else{


            $request->validate($cliente->rules());

        }

        $cliente->fill($request->all());

             $cliente->update([
                'nome' => $cliente->nome

        ]);

        return response()->json($cliente, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Cliente  $cliente
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $cliente = $this->cliente->find($id);

        if($cliente === null){

            return response()->json(['erro' => 'Impossivel realizar a exclusão. O recurso solicitado não existe'], 404);
        }

        $cliente->delete();

        return response()->json(['msg' => 'O carro foi removido com sucesso!'], 200);
    }
}
