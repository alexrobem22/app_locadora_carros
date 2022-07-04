<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use App\Models\Marca;
use App\Repositories\MarcaRepository;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Exists;

class MarcaController extends Controller
{

    public function __construct(Marca $marca)
    {
        $this->marca = $marca;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $marcaRepository = new MarcaRepository($this->marca);

        if($request->has('atributos_modelos')){
            $atributos_modelos = 'modelos:id,'.$request->atributos_modelos;
            //with('marca:id,nome,imagem') ai e um exemplo que o with aceita paramentros / temos que sempre passar o id pq ele que relaciona as tabelas
            $marcaRepository->selectAtributosRegistrosRelacionados($atributos_modelos);
        }
        else{
            // $marcas = $this->marca->with('modelos');
            $marcaRepository->selectAtributosRegistrosRelacionados('modelos');
        }

        if($request->has('filtro')){
           
          $marcaRepository->filtro($request->filtro);

        }

            //o has verifica se um determinado paramentro no request existe se ta defenido
      if($request->has('atributos')){


        $marcaRepository->selectAtributos($request->atributos);

    }


//-----------------------------------------------------------------------------//



    //     $marcas = array();

    //     if($request->has('atributos_modelos')){
    //         $atributos_modelos = $request->atributos_modelos;
    //         //with('marca:id,nome,imagem') ai e um exemplo que o with aceita paramentros / temos que sempre passar o id pq ele que relaciona as tabelas
    //         $marcas = $this->marca->with('modelos:id,'.$atributos_modelos);
    //     }
    //     else{
    //         $marcas = $this->marca->with('modelos');
    //     }

    //     if($request->has('filtro')){
    //         $filtros = explode(';', $request->filtro);//aqui eu dividos os item pelo ponto e virgula
    //         // dd($filtros);
    //         foreach ($filtros as $key => $condicao){
    //             $c = explode(':', $condicao);
    //             $marcas = $marcas->where($c[0], $c[1], $c[2]);
    //         }

    //     }
    //   //o has verifica se um determinado paramentro no request existe se ta defenido
    //   if($request->has('atributos')){

    //     $atributos = $request->atributos;

    //     //$modelo = $this->modelo->select('id', 'nome', 'imagem')->get();//dessa forma trago os dados especificos
    //     $marcas = $marcas->selectRaw($atributos)->get(); // o selectRaw aceita o paramentro do request e fica de modo dinamico

    //     //'id', 'nome', 'imagem'; // no primeiro select nos passa assim
    //     // id,nome,imagem // recebemos as variavel do request assim temo que tranformar para o select aceita

    // }else{
    //     $marcas = $marcas->get();
    // }
    //
    // return response()->json($this->modelo->with('marca')->get(), 200);
    return response()->json($marcaRepository->getResultado(), 200);
    //all() -> criando um obj de consulta + get() = collection
    //get() -> modificar a consulta -> collection
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        // dd($request->all());


        $request->validate($this->marca->rules(), $this->marca->feedback());
        //stateless

        // dd($request->nome);
        // dd($request->get(nome));
        // dd($request->input(nome));

        $imagem = $request->file('imagem');
        $imagem_urn = $imagem->store('imagens', 'public');// o metodo store tem 2 paramentro o 1 e o path = caminho que vai ser armazenado. o 2 paramentro e chamado de disco onde vamos armazena e nos configura isso em config no arquivo filesystems.



        $marca = $this->marca->create([
            'nome' => $request->nome,
            'imagem' => $imagem_urn
        ]);

        // $marca = Marca::create([
        //     'nome' => $request->nome,
        //     'imagem' => $request->imagem
        // ]);

        // dd($marca);

         return response()->json($marca, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Marca  $marca
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        $marca = $this->marca->with('modelos')->find($id);
        if($marca === null){
            // return ['erro' => 'Recurso pesquisado não existe']; // json
            return response()->json(['erro' => 'Recurso pesquisado não existe'], 404); // no response eu passo o erro 404
        }
        return response()->json($marca, 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Marca  $marca
     * @return \Illuminate\Http\Response
     */
    public function edit(Marca $marca)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Marca  $marca
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        $marca = $this->marca->find($id);

        if($marca === null){

            return response()->json(['erro' => 'Impossivel realizar a atualizacão. O recurso solicitado não existe'], 404);
        }

        if ($request->method() === 'PATCH'){

            $regrasDinamicas = array();
            //percorrendo todas as regras definidas no Model
            foreach($marca->rules() as $input => $regra){


                //coletar apenas as regras aplicaveis aos parametros parciais da requisicao
                if(array_key_exists($input, $request->all())){
                    $regrasDinamicas[$input] = $regra;

                }
            }

            $request->validate($regrasDinamicas, $marca->feedback());

        }
        else{


            $request->validate($marca->rules(), $marca->feedback());

        }
        //remover o arquivo caso um novo arquivo tenha sido enviado no request
        if($request->file('imagem')){
            Storage::disk('public')->delete($marca->imagem);// aqui estou removendo a imagem do meu disco
        }
        $imagem = $request->file('imagem');
        $imagem_urn = $imagem->store('imagens', 'public');// o metodo store tem 2 paramentro o 1 e o path = caminho que vai ser armazenado, no caso o nome da pasta. o 2 paramentro e chamado de disco onde vamos armazena e nos configura isso em config no arquivo filesystems.

        //prenchendo o objeto $marca com os dados do request
        $marca->fill($request->all()); //o metodo fill espera um array
        // dd([$marca->getAttributes(), $marca->nome]);

        //poderiamos usar o metodo save
        // $marca->imgame = $imagem_urn;
        // $marca->save();


             $marca->update([
            'nome' => $marca->nome,
            'imagem' => $marca->imagem = $imagem_urn
        ]);

       // $marca->update($request->all());// pego todo o request

        // $marca->update([
        //     'nome' => $request->nome,
        //     'imagem' => $request->imagem
        // ]);

        return response()->json($marca, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Marca  $marca
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $marca = $this->marca->find($id);

        if($marca === null){

            return response()->json(['erro' => 'Impossivel realizar a exclusão. O recurso solicitado não existe'], 404);
        }

         //remover o arquivo caso um novo arquivo tenha sido enviado no request
            Storage::disk('public')->delete($marca->imagem);// aqui estou removendo a imagem do meu disco


        $marca->delete();

        return response()->json(['msg' => 'A marca foi removida com sucesso!'], 200);

    }
}
