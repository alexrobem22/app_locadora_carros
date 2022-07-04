<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Carro extends Model
{
    use HasFactory;
    protected $fillable = ['modelo_id','placa', 'disponivel','km'];

    public function rules(){
        return [
            'modelo_id' => 'exists:modelos,id',
            'placa' => 'required',
            'disponivel' => 'required',
            'km' => 'required'


        ];
    }
    public function modelo(){
        return $this->belongsTo(Modelo::class);
    }
    /**
     * aqui tamos falando dos 3 paramentros do unique
     *
     * 1) tabela
     * 2) nome da coluna que sera pesquisada na tabela3
     * 3) id do registro que sera desconsiderado na pesquisa
     */
}
