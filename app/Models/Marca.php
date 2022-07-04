<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Marca extends Model
{
    use HasFactory;

    protected $fillable = ['nome', 'imagem'];

    public function rules(){
        return [
            'nome' => 'required|unique:marcas,nome,'.$this->id.'|min:3',
            'imagem' => 'required|file|mimes:png'
        ];
    }
    /**
     * aqui tamos falando dos 3 paramentros do unique
     *
     * 1) tabela
     * 2) nome da coluna que sera pesquisada na tabela3
     * 3) id do registro que sera desconsiderado na pesquisa
     */

    public function feedback(){
        return [
            'required' => 'O campo :attribute e obrigatorio',
            'nome.unique' => 'O nome da marca ja existe',
            'nome.min' => 'o nome deve ter no minimo 3 caracter',
            'imagem.mimes' => 'o formato do arquivo deve ser png'

        ];
    }

    //declarei o relacionamento de marca para modelo e de modelo para marca. o outro codigo ta em modelo
    public function modelos(){
        //uma marca possui muitos modelos
        return $this->hasMany(Modelo::class);
    }
}
