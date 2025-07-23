<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Elegivel;

class EleicaoController extends Controller
{
    public function index()
    {
        return "Olá, bem vindo à eleição do IFRN!";
    }

    public function verResultado($idEleicao)
    {
        $vencedor = $this->calcularResultado($idEleicao);
        
        if (!$vencedor) {
            return response()->json(['mensagem' => 'Eleição não encontrada ou sem candidatos'], 404);
        }

        return response()->json([
            'mensagem' => 'Resultado da eleição #'.$idEleicao,
            'vencedor' => $vencedor
        ]);
    }
    
    private function calcularResultadoSegundoTurno($candidato1, $candidato2)
    {
        if ($candidato1->total_votos != $candidato2->total_votos) {
            return $candidato1->total_votos > $candidato2->total_votos ? $candidato1 : $candidato2;
        }

        return $candidato1->idade > $candidato2->idade ? $candidato1 : $candidato2;
    }

    private function calcularResultado($idEleicao)
    {
        $candidatos = Elegivel::where('eleicao_id', $idEleicao)->get();

        if ($candidatos->count() < 1) {
            return null;
        }

        if ($candidatos->count() == 2) {
            return $this->calcularResultadoSegundoTurno($candidatos[0], $candidatos[1]);
        }

        return $candidatos->sortByDesc('total_votos')->first();
    }
}
