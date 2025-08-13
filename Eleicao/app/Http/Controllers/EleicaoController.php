<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Http;


class EleicaoController extends Controller
{
    public function verResultado(int $idEleicao)
    {
        $votos = $this->buscarVotos($idEleicao);

        if (empty($votos)) {
            return response()->json(['mensagem' => 'Nenhum voto encontrado'], 404);
        }

        $contagem = $this->contarVotos($votos);

        // Montar array de concorrentes com nome e idade
        $concorrentes = [];
        foreach ($contagem as $idCandidato => $totalVotos) {
            $concorrentes[] = (object)[
                'id_candidato' => $idCandidato,
                'total_votos' => $totalVotos,
                'nome' => $this->gerarCandidato($idCandidato),
                'idade' => $this->gerarIdade($idCandidato),
            ];
        }

        // Se tiver exatamente 2 concorrentes, usa regra de segundo turno
        if (count($concorrentes) == 2) {
            $vencedor = $this->calcularResultadoSegundoTurno($concorrentes[0], $concorrentes[1]);
        } else {
            // Ordena por total de votos (desc) e em empate pelo candidato mais jovem (asc)
            usort($concorrentes, function ($a, $b) {
                if ($a->total_votos != $b->total_votos) {
                    return $b->total_votos <=> $a->total_votos;
                }
                return $a->idade <=> $b->idade;
            });
            $vencedor = $concorrentes[0];
        }

        return response()->json([
            'mensagem' => 'Resultado da eleição',
            'vencedor' => $vencedor,
            'concorrentes' => $concorrentes,
        ]);
    }

    private function buscarVotos(int $idEleicao): array
    {
        $response = Http::get('http://13.221.77.151:8000/votos?id_eleicao=' . $idEleicao);

        if ($response->failed()) {
            abort(500, 'Erro ao buscar votos');
        }

        return $response->json();
    }
    
    private function contarVotos(array $votos): array
    {
        $contagem = [];
        foreach ($votos as $voto) {
            $id = $voto['id_candidato'];
            $contagem[$id] = ($contagem[$id] ?? 0) + 1;
        }
        arsort($contagem);
        return $contagem;
    }

    private function calcularResultadoSegundoTurno($candidato1, $candidato2)
    {
        if ($candidato1->total_votos != $candidato2->total_votos) {
            return $candidato1->total_votos > $candidato2->total_votos ? $candidato1 : $candidato2;
        }

        // Critério desempate: candidato mais jovem vence
        return $candidato1->idade < $candidato2->idade ? $candidato1 : $candidato2;
    }

    private function gerarCandidato()
    {
        $nomes = [
            'Pedro Neto',
            'Lucas Silva',
            'Gabriel Santos',
            'Mateus Oliveira',
            'Rafael Almeida',
            'Felipe Costa',
            'João Pedro',
            'Guilherme Rocha',
            'Vinícius Pereira',
            'Bruno Ferreira',
            'Thiago Lima',
            'Eduardo Fernandes',
            'André Carvalho',
            'Daniel Sousa',
            'Caio Ribeiro',
            'Vitor Martins',
            'Leandro Alves',
            'Diego Gomes',
            'Ricardo Moreira',
            'Marcos Dias',
            'Alexandre Nunes',
            'Fernando Melo',
            'Carlos Henrique',
            'Rodrigo Pinto',
            'Marcelo Cardoso',
            'Samuel Barros',
            'Murilo Correia',
            'Fábio Teixeira',
            'Douglas Araújo',
            'Jean Carlos',
            'João Vitor',
            'Paulo Henrique',
            'Renan Santana',
            'Rômulo Campos',
            'Matheus Castro',
            'Cauã Lima',
            'Igor Machado',
            'Caetano Lopes',
            'Luiz Gustavo',
            'Anderson Silva',
            'Thierry Santos',
            'Nathan Oliveira',
            'Emanuel Pereira',
            'Arthur Costa',
            'Diego Souza',
            'Leonardo Ferreira',
            'Caio Ribeiro',
            'Enzo Alves',
            'Henrique Almeida',
            'Bruno Martins',
            'Rafael Gomes',
            'Samuel Silva',
            'Igor Santos',
            'João Luiz',
            'Daniel Carvalho',
            'Vitor Rocha',
            'Lucas Mendes',
            'Mateus Nascimento',
            'Gabriel Ribeiro',
            'Felipe Souza',
            'Eduardo Lima',
            'Caio Fernandes',
            'Vinícius Dias',
            'André Santos',
            'Marcos Almeida',
            'Thiago Pereira',
            'Diego Costa',
            'Carlos Eduardo',
            'Alexandre Silva',
            'Rodrigo Souza',
            'Marcelo Pereira',
            'Samuel Almeida',
            'Murilo Santos',
            'Fábio Lima',
            'Douglas Fernandes',
            'Jean Silva',
            'João Carlos',
            'Paulo Sousa',
            'Renan Silva',
            'Rômulo Pereira',
        ];
        $indice = array_rand($nomes);
        return $nomes[$indice];
        
    }

    private function gerarIdade(int $id)
    {
        return random_int(20, 60);
    }
}

