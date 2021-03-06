<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use App\Facades\SystemConfig;
use App\Models\Evento;
use App\Models\HinoEvento;
use Illuminate\Http\Request;
use DB;

class EventosController extends Controller
{
    public function listarEventos(Request $request){
        $eventos = Evento::with('hinos')->whereHas('grupos', function ($query) use ($request) {
            $query->where('id', $request->id_grupo);
        })->get();

        return response()->json([
            'eventos' => $eventos
        ]);
    }

    public function cadastrarEvento(Request $request){
        $evento = new Evento();
        $evento->nome = $request->nome;
        $evento->data = $request->data;
        $evento->id_igreja = $request->id_igreja;
        $evento->save();

        foreach ($request->hinos as $hino){
            $hinoEvento = new HinoEvento();
            $hinoEvento->id_evento = $evento->id;
            $hinoEvento->id_hino = $hino->id;
            $hinoEvento->sequencia = $hino->sequencia;
            $hinoEvento->save();

        }

        return response()->json(['status' => 200]);
    }

    public function excluirEvento(Request $request){
        HinoEvento::where('id_evento', $request->id)->delete();

        Evento::find($request->id)->delete();

        return response()->json(['status' => 200]);
    }
}
