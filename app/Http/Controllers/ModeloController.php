<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Storage;
use App\Models\Modelo;
use App\Repositories\ModeloRepository;
use Illuminate\Http\Request;

class ModeloController extends Controller
{
    public function __construct(Modelo $modelo)
    {
        $this->modelo = $modelo;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $modeloRepository = new ModeloRepository($this->modelo);

        if($request->has('atributos_marca')){
            $atributos_marca = 'marca:id,'.$request->atributos_marca;
            //with('marca:id,nome,imagem') ai e um exemplo que o with aceita paramentros / temos que sempre passar o id pq ele que relaciona as tabelas
            $modeloRepository->selectAtributosRegistrosRelacionados($atributos_marca);
        }
        else{
            // $marcas = $this->marca->with('modelos');
            $modeloRepository->selectAtributosRegistrosRelacionados('marca');
        }

        if($request->has('filtro')){
            // dd($request->filtro);
          $modeloRepository->filtro($request->filtro);

        }

            //o has verifica se um determinado paramentro no request existe se ta defenido
      if($request->has('atributos')){


        $modeloRepository->selectAtributos($request->atributos);
      }

      return response()->json($modeloRepository->getResultado(), 200);
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

        $request->validate($this->modelo->rules());
        //stateless

        $imagem = $request->file('imagem');
        $imagem_urn = $imagem->store('imagens/modelos', 'public');// o metodo store tem 2 paramentro o 1 e o path = caminho que vai ser armazenado. o 2 paramentro e chamado de disco onde vamos armazena e nos configura isso em config no arquivo filesystems.


        $modelo = $this->modelo->create([
            'marca_id' => $request->marca_id,
            'nome' => $request->nome,
            'imagem' => $imagem_urn,
            'numero_portas' => $request->numero_portas,
            'lugares' => $request->lugares,
            'air_bag' => $request->air_bag,
            'abs' => $request->abs
        ]);

        // $marca = Marca::create([
        //     'nome' => $request->nome,
        //     'imagem' => $request->imagem
        // ]);

        // dd($marca);

         return response()->json($modelo, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Modelo  $modelo
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        $modelo = $this->modelo->with('marca')->find($id);//no with to pegando o relacionamento
        if($modelo === null){
            // return ['erro' => 'Recurso pesquisado não existe']; // json
            return response()->json(['erro' => 'Recurso pesquisado não existe'], 404); // no response eu passo o erro 404
        }
        return response()->json($modelo, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Modelo  $modelo
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        $modelo = $this->modelo->find($id);

        if($modelo === null){

            return response()->json(['erro' => 'Impossivel realizar a atualizacão. O recurso solicitado não existe'], 404);
        }

        if ($request->method() === 'PATCH'){

            $regrasDinamicas = array();
            //percorrendo todas as regras definidas no Model
            foreach($modelo->rules() as $input => $regra){


                //coletar apenas as regras aplicaveis aos parametros parciais da requisicao
                if(array_key_exists($input, $request->all())){
                    $regrasDinamicas[$input] = $regra;

                }
            }

            $request->validate($regrasDinamicas);

        }
        else{


            $request->validate($modelo->rules());

        }
        //remover o arquivo caso um novo arquivo tenha sido enviado no request
        if($request->file('imagem')){
            Storage::disk('public')->delete($modelo->imagem);// aqui estou removendo a imagem do meu disco
        }
        $imagem = $request->file('imagem');
        $imagem_urn = $imagem->store('imagens/modelos', 'public');// o metodo store tem 2 paramentro o 1 e o path = caminho que vai ser armazenado, no caso o nome da pasta. o 2 paramentro e chamado de disco onde vamos armazena e nos configura isso em config no arquivo filesystems.

            $modelo->fill($request->all()); //o metodo fill espera um array, pego os array e sobre escrevo o que ta na variavel.


             $modelo->update([
                'marca_id' => $modelo->marca_id,
                'nome' => $modelo->nome,
                'imagem' => $modelo->imagem = $imagem_urn,
                'numero_portas' => $modelo->numero_portas,
                'lugares' => $modelo->lugares,
                'air_bag' => $modelo->air_bag,
                'abs' => $modelo->abs
        ]);

       // $marca->update($request->all());// pego todo o request

        // $marca->update([
        //     'nome' => $request->nome,
        //     'imagem' => $request->imagem
        // ]);

        return response()->json($modelo, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Modelo  $modelo
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $modelo = $this->modelo->find($id);

        if($modelo === null){

            return response()->json(['erro' => 'Impossivel realizar a exclusão. O recurso solicitado não existe'], 404);
        }

         //remover o arquivo caso um novo arquivo tenha sido enviado no request
            Storage::disk('public')->delete($modelo->imagem);// aqui estou removendo a imagem do meu disco


        $modelo->delete();

        return response()->json(['msg' => 'O modelo foi removida com sucesso!'], 200);
    }
}
