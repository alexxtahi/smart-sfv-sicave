<?php

namespace App\Http\Controllers\Comptabilite;

use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Comptabilite\TimbreDeclare;
use App\Models\Comptabilite\TimbreTiketDeclare;

class TimbreDeclareController extends Controller
{
    public function listeTimbreDeclarare()
    {
        $totalHT = 0;
        $totalTTC = 0;
        $totalTimbre = 0;

        $declarations = TimbreDeclare::orderBy('timbre_declares.id', 'DESC')
                                        ->join('timbre_tiket_declares', 'timbre_tiket_declares.declaration', '=', 'timbre_declares.id')
                                        ->join('ventes', 'ventes.id', '=', 'timbre_tiket_declares.ticket')
                                        ->join('article_ventes', 'article_ventes.vente_id', '=', 'ventes.id')->Where('article_ventes.retourne',0)
                                        ->join('articles', 'articles.id', '=', 'article_ventes.article_id')
                                        ->join('param_tvas', 'param_tvas.id', '=', 'articles.param_tva_id')
                                        ->select('timbre_declares.*', DB::raw('SUM((article_ventes.prix/(1+param_tvas.tva))*article_ventes.quantite) AS  montantHT'), DB::raw('SUM(article_ventes.prix*article_ventes.quantite) AS montantTTC'), DB::raw('DATE_FORMAT(timbre_declares.date_declaration, "%d-%m-%Y") as date_declarations'))
                                        ->groupBy('timbre_declares.id')
                                        ->get();

        foreach ($declarations as $declaration) {
            $totalHT = $totalHT + $declaration->montantHT;
            $totalTTC = $totalTTC + $declaration->montantTTC;
            $declaration->montantTTC > 5000 ? $totalTimbre = $totalTimbre + 100 : $totalTimbre = $totalTimbre + 0 ;
        }

        $jsonData["rows"] = $declarations->toArray();
        $jsonData["total"] = $declarations->count();
        $jsonData["totalTTC"] = $totalTTC;
        $jsonData["totalHT"] = $totalHT;
        $jsonData["totalTimbre"] = $totalTimbre;
        return response()->json($jsonData);
    }

    public function listeTimbreDeclarareByDepot($depot)
    {
        $totalHT = 0;
        $totalTTC = 0;
        $totalTimbre = 0;

        $declarations = TimbreDeclare::orderBy('timbre_declares.id', 'DESC')
                                        ->join('timbre_tiket_declares', 'timbre_tiket_declares.declaration', '=', 'timbre_declares.id')
                                        ->join('ventes', 'ventes.id', '=', 'timbre_tiket_declares.ticket')
                                        ->join('article_ventes', 'article_ventes.vente_id', '=', 'ventes.id')->Where('article_ventes.retourne',0)
                                        ->join('articles', 'articles.id', '=', 'article_ventes.article_id')
                                        ->join('param_tvas', 'param_tvas.id', '=', 'articles.param_tva_id')
                                        ->where('ventes.depot_id',$depot)
                                        ->select('timbre_declares.*', DB::raw('SUM((article_ventes.prix/(1+param_tvas.tva))*article_ventes.quantite) AS  montantHT'), DB::raw('SUM(article_ventes.prix*article_ventes.quantite) AS montantTTC'), DB::raw('DATE_FORMAT(timbre_declares.date_declaration, "%d-%m-%Y") as date_declarations'))
                                        ->groupBy('timbre_declares.id')
                                        ->get();

        foreach ($declarations as $declaration) {
            $totalHT = $totalHT + $declaration->montantHT;
            $totalTTC = $totalTTC + $declaration->montantTTC;
            $declaration->montantTTC > 5000 ? $totalTimbre = $totalTimbre + 100 : $totalTimbre = $totalTimbre + 0 ;
        }

        $jsonData["rows"] = $declarations->toArray();
        $jsonData["total"] = $declarations->count();
        $jsonData["totalTTC"] = $totalTTC;
        $jsonData["totalHT"] = $totalHT;
        $jsonData["totalTimbre"] = $totalTimbre;
        return response()->json($jsonData);
    }

    public function listeTimbreDeclarareByPeriode($debut, $fin)
    {
        $totalHT = 0;
        $totalTTC = 0;
        $totalTimbre = 0;

        $date1 = Carbon::createFromFormat('d-m-Y', $debut);
        $date2 = Carbon::createFromFormat('d-m-Y', $fin);


        $declarations = TimbreDeclare::orderBy('timbre_declares.id', 'DESC')
                                        ->join('timbre_tiket_declares', 'timbre_tiket_declares.declaration', '=', 'timbre_declares.id')
                                        ->join('ventes', 'ventes.id', '=', 'timbre_tiket_declares.ticket')
                                        ->join('article_ventes', 'article_ventes.vente_id', '=', 'ventes.id')->Where('article_ventes.retourne',0)
                                        ->join('articles', 'articles.id', '=', 'article_ventes.article_id')
                                        ->join('param_tvas', 'param_tvas.id', '=', 'articles.param_tva_id')
                                        ->whereDate('timbre_declares.date_declaration', '>=', $date1)
                                        ->whereDate('timbre_declares.date_declaration', '<=', $date2)
                                        ->select('timbre_declares.*', DB::raw('SUM((article_ventes.prix/(1+param_tvas.tva))*article_ventes.quantite) AS  montantHT'), DB::raw('SUM(article_ventes.prix*article_ventes.quantite) AS montantTTC'), DB::raw('DATE_FORMAT(timbre_declares.date_declaration, "%d-%m-%Y") as date_declarations'))
                                        ->groupBy('timbre_declares.id')
                                        ->get();

        foreach ($declarations as $declaration) {
            $totalHT = $totalHT + $declaration->montantHT;
            $totalTTC = $totalTTC + $declaration->montantTTC;
            $declaration->montantTTC > 5000 ? $totalTimbre = $totalTimbre + 100 : $totalTimbre = $totalTimbre + 0 ;
        }

        $jsonData["rows"] = $declarations->toArray();
        $jsonData["total"] = $declarations->count();
        $jsonData["totalTTC"] = $totalTTC;
        $jsonData["totalHT"] = $totalHT;
        $jsonData["totalTimbre"] = $totalTimbre;
        return response()->json($jsonData);
    }

    public function listeTimbreDeclarareByDepotPeriode($depot, $debut, $fin)
    {
        $totalHT = 0;
        $totalTTC = 0;
        $totalTimbre = 0;

        $date1 = Carbon::createFromFormat('d-m-Y', $debut);
        $date2 = Carbon::createFromFormat('d-m-Y', $fin);


        $declarations = TimbreDeclare::orderBy('timbre_declares.id', 'DESC')
                                        ->join('timbre_tiket_declares', 'timbre_tiket_declares.declaration', '=', 'timbre_declares.id')
                                        ->join('ventes', 'ventes.id', '=', 'timbre_tiket_declares.ticket')
                                        ->join('article_ventes', 'article_ventes.vente_id', '=', 'ventes.id')->Where('article_ventes.retourne',0)
                                        ->join('articles', 'articles.id', '=', 'article_ventes.article_id')
                                        ->join('param_tvas', 'param_tvas.id', '=', 'articles.param_tva_id')
                                        ->where('ventes.depot_id',$depot)
                                        ->whereDate('timbre_declares.date_declaration', '>=', $date1)
                                        ->whereDate('timbre_declares.date_declaration', '<=', $date2)
                                        ->select('timbre_declares.*', DB::raw('SUM((article_ventes.prix/(1+param_tvas.tva))*article_ventes.quantite) AS  montantHT'), DB::raw('SUM(article_ventes.prix*article_ventes.quantite) AS montantTTC'), DB::raw('DATE_FORMAT(timbre_declares.date_declaration, "%d-%m-%Y") as date_declarations'))
                                        ->groupBy('timbre_declares.id')
                                        ->get();

        foreach ($declarations as $declaration) {
            $totalHT = $totalHT + $declaration->montantHT;
            $totalTTC = $totalTTC + $declaration->montantTTC;
            $declaration->montantTTC > 5000 ? $totalTimbre = $totalTimbre + 100 : $totalTimbre = $totalTimbre + 0 ;
        }

        $jsonData["rows"] = $declarations->toArray();
        $jsonData["total"] = $declarations->count();
        $jsonData["totalTTC"] = $totalTTC;
        $jsonData["totalHT"] = $totalHT;
        $jsonData["totalTimbre"] = $totalTimbre;
        return response()->json($jsonData);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\TimbreDeclare  $timbreDeclare
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $timbreDeclare = TimbreDeclare::find($id);
        $jsonData = ["code" => 1, "msg" => " Opération effectuée avec succès."];
        if ($timbreDeclare) {
            try {

                $timbreTiketDeclares = TimbreTiketDeclare::where('declaration', $timbreDeclare->id)->get();

                foreach ($timbreTiketDeclares as $timbreTiketDeclare) {
                    $timbreTiketDeclare->delete();
                }

                $timbreDeclare->delete();
                $jsonData["data"] = json_decode($timbreDeclare);
                return response()->json($jsonData);
            } catch (Exception $exc) {
                $jsonData["code"] = -1;
                $jsonData["data"] = null;
                $jsonData["msg"] = $exc->getMessage();
                return response()->json($jsonData);
            }
        }
        return response()->json(["code" => 0, "msg" => "Echec de suppression", "data" => null]);
    }
}
