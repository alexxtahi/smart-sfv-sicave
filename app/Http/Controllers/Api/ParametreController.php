<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Parametre\Client;
use Illuminate\Support\Facades\DB;

class ParametreController extends Controller
{
    public function listeRegime()
    {
        $regimes = DB::table('regimes')
            ->select('regimes.*')
            ->Where('deleted_at', null)
            ->orderBy('libelle_regime', 'ASC')
            ->get();
        $jsonData["rows"] = $regimes->toArray();
        $jsonData["total"] = $regimes->count();
        return response()->json($jsonData);
    }

    public function listeNation()
    {
        $nations = DB::table('nations')
            ->select('nations.*')
            ->Where('deleted_at', null)
            ->orderBy('libelle_nation', 'ASC')
            ->get();
        $jsonData["rows"] = $nations->toArray();
        $jsonData["total"] = $nations->count();
        return response()->json($jsonData);
    }
}
