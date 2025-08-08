<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Elegivel;
use Illuminate\Support\Facades\Http;

class EleicaoController extends Controller
{
    public function index()
    {
        return "Olá, bem vindo à eleição do IFRN!";
    }

    public function verResultado(int $idEleicao)
    {
        $response = Http::get('http://13.221.77.151:8000/votos?id_eleicao='.$idEleicao);

        if ($response->failed()) {
            return response()->json(['mensagem' => 'Erro ao buscar votos'], 500);
        }

        $votos = $response->json();

        if (empty($votos)) {
            return response()->json(['mensagem' => 'Nenhum voto encontrado'], 404);
        }

        // Conta os votos por candidato
        $contagem = [];
        foreach ($votos as $voto) {
            $id = $voto['id_candidato'];
            $contagem[$id] = ($contagem[$id] ?? 0) + 1;
        }

        arsort($contagem);
        $idVencedor = array_key_first($contagem);
        $totalVotos = $contagem[$idVencedor];

        return response()->json([
            'mensagem' => 'Vencedor da eleição',
            'id_candidato' => $idVencedor,
            'total_votos' => $totalVotos
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
