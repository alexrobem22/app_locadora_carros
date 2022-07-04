<?php

namespace App\Http\Controllers;

use App\Models\Locacao;
use App\Http\Requests\StoreLocacaoRequest;
use App\Http\Requests\UpdateLocacaoRequest;
use App\Repositories\LocacaoRepository;
use Illuminate\Http\Request;

class LocacaoController extends Controller
{
    public function __construct(Locacao $locacao)
    {
        $this->locacao = $locacao;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        $locacaoRepository = new LocacaoRepository($this->locacao);

        if($request->has('filtro')){

          $locacaoRepository->filtro($request->filtro);
        }
            //o has verifica se um determinado paramentro no request existe se ta defenido
      if($request->has('atributos')){

        $locacaoRepository->selectAtributos($request->atributos);
    }
    return response()->json($locacaoRepository->getResultado(), 200);

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
     * @param  \App\Http\Requests\StoreLocacaoRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate($this->locacao->rules());

        $locacao = $this->locacao->create([
            'cliente_id' => $request->cliente_id,
            'carro_id' => $request->carro_id,
            'data_inicio_periodo' => $request->data_inicio_periodo,
            'data_final_previsto_periodo' => $request->data_final_previsto_periodo,
            'data_final_realizado_periodo' => $request->data_final_realizado_periodo,
            'valor_diaria' => $request->valor_diaria,
            'km_inicial' => $request->km_inicial,
            'km_final' => $request->km_final,
        ]);

         return response()->json($locacao, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Locacao  $locacao
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $locacao = $this->locacao->find($id);
        if($locacao === null){
            // return ['erro' => 'Recurso pesquisado não existe']; // json
            return response()->json(['erro' => 'Recurso pesquisado não existe'], 404); // no response eu passo o erro 404
        }
        return response()->json($locacao, 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Locacao  $locacao
     * @return \Illuminate\Http\Response
     */
    public function edit(Locacao $locacao)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateLocacaoRequest  $request
     * @param  \App\Models\Locacao  $locacao
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        $locacao = $this->locacao->find($id);

        if($locacao === null){

            return response()->json(['erro' => 'Impossivel realizar a atualizacão. O recurso solicitado não existe'], 404);
        }

        if ($request->method() === 'PATCH'){

            $regrasDinamicas = array();
            //percorrendo todas as regras definidas no Model
            foreach($locacao->rules() as $input => $regra){


                //coletar apenas as regras aplicaveis aos parametros parciais da requisicao
                if(array_key_exists($input, $request->all())){
                    $regrasDinamicas[$input] = $regra;

                }
            }

            $request->validate($regrasDinamicas);

        }
        else{


            $request->validate($locacao->rules());

        }

        $locacao->fill($request->all());

        $locacao = $this->locacao->create([
            'cliente_id' => $locacao->cliente_id,
            'carro_id' => $locacao->carro_id,
            'data_inicio_periodo' => $locacao->data_inicio_periodo,
            'data_final_previsto_periodo' => $locacao->data_final_previsto_periodo,
            'data_final_realizado_periodo' => $locacao->data_final_realizado_periodo,
            'valor_diaria' => $locacao->valor_diaria,
            'km_inicial' => $locacao->km_inicial,
            'km_final' => $locacao->km_final,
        ]);

        return response()->json($locacao, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Locacao  $locacao
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $locacao = $this->locacao->find($id);

        if($locacao === null){

            return response()->json(['erro' => 'Impossivel realizar a exclusão. O recurso solicitado não existe'], 404);
        }

        $locacao->delete();

        return response()->json(['msg' => 'A Locacão foi removido com sucesso!'], 200);
    }
}
