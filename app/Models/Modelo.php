<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Modelo extends Model
{
    use HasFactory;

    protected $fillable = ['marca_id','nome', 'imagem','numero_portas', 'lugares', 'air_bag', 'abs'];

    public function rules(){
        return [
            'marca_id' => 'exists:marcas,id',
            'nome' => 'required|unique:marcas,nome,'.$this->id.'|min:3',//esse parametro $this->id e para parte de update se o id existe ele passar
            'imagem' => 'required|file|mimes:png,jpeg,jpg',
            'numero_portas' => 'required|integer|digits_between:1,5',//digits_between:1,5 esse paramentro ele so pode digitar um numero entre 1 e 5
            'lugares' => 'required|integer|digits_between:1,20',
            'air_bag' => 'required|boolean',
            'abs' => 'required|boolean' //valor boolean true ou false , true pode ser numero 1 ou string 1 nesse caso ele aceita string, para false a mesma coisa so que o valor de false = 0
        ];
    }
    /**
     * aqui tamos falando dos 3 paramentros do unique
     *
     * 1) tabela
     * 2) nome da coluna que sera pesquisada na tabela3
     * 3) id do registro que sera desconsiderado na pesquisa
     */

     public function marca(){
         //um modelo PERTENCE A UMA MARCA
         return $this->belongsTo(Marca::class);
     }

}
