<?php

namespace App\Http\Controllers;

use App\Models\Carro;
use App\Http\Requests\StoreCarroRequest;
use App\Http\Requests\UpdateCarroRequest;
use App\Repositories\CarroRepository;
use Illuminate\Http\Request;

class CarroController extends Controller
{
    public function __construct(Carro $carro)
    {
        $this->carro = $carro;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        $carroRepository = new CarroRepository($this->carro);

        if($request->has('atributos_modelo')){
            $atributos_modelo = 'modelo:id,'.$request->atributos_modelo;
            //with('marca:id,nome,imagem') ai e um exemplo que o with aceita paramentros / temos que sempre passar o id pq ele que relaciona as tabelas
            $carroRepository->selectAtributosRegistrosRelacionados($atributos_modelo);
        }
        else{
            // $marcas = $this->marca->with('modelos');
            $carroRepository->selectAtributosRegistrosRelacionados('modelo');
        }

        if($request->has('filtro')){

          $carroRepository->filtro($request->filtro);

        }

            //o has verifica se um determinado paramentro no request existe se ta defenido
      if($request->has('atributos')){


        $carroRepository->selectAtributos($request->atributos);

    }

    return response()->json($carroRepository->getResultado(), 200);

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
     * @param  \App\Http\Requests\StoreCarroRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate($this->carro->rules());

        $carro = $this->carro->create([
            'modelo_id' => $request->modelo_id,
            'placa' => $request->placa,
            'disponivel' => $request->disponivel,
            'km' => $request->km
        ]);

         return response()->json($carro, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Carro  $carro
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        $carro = $this->carro->with('modelo')->find($id);
        if($carro === null){
            // return ['erro' => 'Recurso pesquisado não existe']; // json
            return response()->json(['erro' => 'Recurso pesquisado não existe'], 404); // no response eu passo o erro 404
        }
        return response()->json($carro, 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Carro  $carro
     * @return \Illuminate\Http\Response
     */
    public function edit(Carro $carro)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateCarroRequest  $request
     * @param  \App\Models\Carro  $carro
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$id)
    {
        //
        $carro = $this->carro->find($id);

        if($carro === null){

            return response()->json(['erro' => 'Impossivel realizar a atualizacão. O recurso solicitado não existe'], 404);
        }

        if ($request->method() === 'PATCH'){

            $regrasDinamicas = array();
            //percorrendo todas as regras definidas no Model
            foreach($carro->rules() as $input => $regra){


                //coletar apenas as regras aplicaveis aos parametros parciais da requisicao
                if(array_key_exists($input, $request->all())){
                    $regrasDinamicas[$input] = $regra;

                }
            }

            $request->validate($regrasDinamicas);

        }
        else{


            $request->validate($carro->rules());

        }

        $carro->fill($request->all());

             $carro->update([
                'modelo_id' => $carro->modelo_id,
                'placa' => $carro->placa,
                'disponivel' => $carro->disponivel,
                'km' => $carro->km
        ]);

        return response()->json($carro, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Carro  $carro
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $carro = $this->carro->find($id);

        if($carro === null){

            return response()->json(['erro' => 'Impossivel realizar a exclusão. O recurso solicitado não existe'], 404);
        }

        $carro->delete();

        return response()->json(['msg' => 'O carro foi removido com sucesso!'], 200);
    }
}
