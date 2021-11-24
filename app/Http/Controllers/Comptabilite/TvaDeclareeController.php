<?php

namespace App\Http\Controllers\Comptabilite;

use App\Http\Controllers\Controller;
use App\Models\Comptabilite\TicketInTva;
use App\Models\Comptabilite\TvaDeclaree;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class TvaDeclareeController extends Controller
{


    public function listeDeclarationTva()
    {
        $totalHT = 0;
        $totalTTC = 0;

        $declarations = TvaDeclaree::orderBy('tva_declarees.id', 'DESC')
                                    ->join('ticket_in_tvas', 'ticket_in_tvas.declaration', '=', 'tva_declarees.id')
                                    ->join('article_ventes', 'article_ventes.id', '=', 'ticket_in_tvas.ticket')
                                    ->join('articles', 'articles.id', '=', 'article_ventes.article_id')
                                    ->join('param_tvas', 'param_tvas.id', '=', 'articles.param_tva_id')
                                    ->select('tva_declarees.*', DB::raw('SUM((article_ventes.prix/(1+param_tvas.tva))*article_ventes.quantite) AS  montantHT'), DB::raw('SUM(article_ventes.prix*article_ventes.quantite) AS montantTTC'), DB::raw('DATE_FORMAT(tva_declarees.date_declaration, "%d-%m-%Y") as date_declarations'))
                                    ->groupBy('tva_declarees.id')
                                    ->get();

        foreach ($declarations as $declaration) {
            $totalHT = $totalHT + $declaration->montantHT;
            $totalTTC = $totalTTC + $declaration->montantTTC;
        }

        $jsonData["rows"] = $declarations->toArray();
        $jsonData["total"] = $declarations->count();
        $jsonData["totalTTC"] = $totalTTC;
        $jsonData["totalHT"] = $totalHT;
        return response()->json($jsonData);
    }

    public function listeDeclarationTvaByDepot($depot)
    {
        $totalHT = 0;
        $totalTTC = 0;
        $declarations = TvaDeclaree::orderBy('tva_declarees.id', 'DESC')
                                    ->join('ticket_in_tvas', 'ticket_in_tvas.declaration', '=', 'tva_declarees.id')
                                    ->join('article_ventes', 'article_ventes.id', '=', 'ticket_in_tvas.ticket')
                                    ->join('ventes', 'ventes.id', '=', 'article_ventes.vente_id')
                                    ->join('articles', 'articles.id', '=', 'article_ventes.article_id')
                                    ->join('param_tvas', 'param_tvas.id', '=', 'articles.param_tva_id')
                                    ->where('ventes.depot_id', $depot)
                                    ->select('tva_declarees.*', DB::raw('SUM((article_ventes.prix/(1+param_tvas.tva))*article_ventes.quantite) AS  montantHT'), DB::raw('SUM(article_ventes.prix*article_ventes.quantite) AS montantTTC'), DB::raw('DATE_FORMAT(tva_declarees.date_declaration, "%d-%m-%Y") as date_declarations'))
                                    ->groupBy('tva_declarees.id')
                                    ->get();
        foreach ($declarations as $declaration) {
            $totalHT = $totalHT + $declaration->montantHT;
            $totalTTC = $totalTTC + $declaration->montantTTC;
        }

        $jsonData["rows"] = $declarations->toArray();
        $jsonData["total"] = $declarations->count();
        $jsonData["totalTTC"] = $totalTTC;
        $jsonData["totalHT"] = $totalHT;
        return response()->json($jsonData);
    }

    public function listeDeclarationTvaByPeriode($debut, $fin)
    {
        $totalHT = 0;
        $totalTTC = 0;
        $date1 = Carbon::createFromFormat('d-m-Y', $debut);
        $date2 = Carbon::createFromFormat('d-m-Y', $fin);

        $declarations = TvaDeclaree::orderBy('tva_declarees.id', 'DESC')
                                    ->join('ticket_in_tvas', 'ticket_in_tvas.declaration', '=', 'tva_declarees.id')
                                    ->join('article_ventes', 'article_ventes.id', '=', 'ticket_in_tvas.ticket')
                                    ->join('articles', 'articles.id', '=', 'article_ventes.article_id')
                                    ->join('param_tvas', 'param_tvas.id', '=', 'articles.param_tva_id')
                                    ->select('tva_declarees.*', DB::raw('SUM((article_ventes.prix/(1+param_tvas.tva))*article_ventes.quantite) AS  montantHT'), DB::raw('SUM(article_ventes.prix*article_ventes.quantite) AS montantTTC'), DB::raw('DATE_FORMAT(tva_declarees.date_declaration, "%d-%m-%Y") as date_declarations'))
                                    ->whereDate('tva_declarees.date_declaration', '>=', $date1)
                                    ->whereDate('tva_declarees.date_declaration', '<=', $date2)
                                    ->groupBy('tva_declarees.id')
                                    ->get();

        foreach ($declarations as $declaration) {
            $totalHT = $totalHT + $declaration->montantHT;
            $totalTTC = $totalTTC + $declaration->montantTTC;
        }

        $jsonData["rows"] = $declarations->toArray();
        $jsonData["total"] = $declarations->count();
        $jsonData["totalTTC"] = $totalTTC;
        $jsonData["totalHT"] = $totalHT;
        return response()->json($jsonData);
    }

    public function listeDeclarationByDepotPeriode($debut, $fin, $depot)
    {
        $totalHT = 0;
        $totalTTC = 0;
        $date1 = Carbon::createFromFormat('d-m-Y', $debut);
        $date2 = Carbon::createFromFormat('d-m-Y', $fin);
        $declarations = TvaDeclaree::orderBy('tva_declarees.id', 'DESC')
                                        ->join('ticket_in_tvas', 'ticket_in_tvas.declaration', '=', 'tva_declarees.id')
                                        ->join('article_ventes', 'article_ventes.id', '=', 'ticket_in_tvas.ticket')
                                        ->join('ventes', 'ventes.id', '=', 'article_ventes.vente_id')
                                        ->join('articles', 'articles.id', '=', 'article_ventes.article_id')
                                        ->join('param_tvas', 'param_tvas.id', '=', 'articles.param_tva_id')
                                        ->whereDate('tva_declarees.date_declaration', '>=', $date1)
                                        ->whereDate('tva_declarees.date_declaration', '<=', $date2)
                                        ->where('ventes.depot_id', $depot)
                                        ->select('tva_declarees.*', DB::raw('SUM((article_ventes.prix/(1+param_tvas.tva))*article_ventes.quantite) AS  montantHT'), DB::raw('SUM(article_ventes.prix*article_ventes.quantite) AS montantTTC'), DB::raw('DATE_FORMAT(tva_declarees.date_declaration, "%d-%m-%Y") as date_declarations'))
                                        ->groupBy('tva_declarees.id')
                                        ->get();
        foreach ($declarations as $declaration) {
            $totalHT = $totalHT + $declaration->montantHT;
            $totalTTC = $totalTTC + $declaration->montantTTC;
        }

        $jsonData["rows"] = $declarations->toArray();
        $jsonData["total"] = $declarations->count();
        $jsonData["totalTTC"] = $totalTTC;
        $jsonData["totalHT"] = $totalHT;
        return response()->json($jsonData);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\TvaDeclaree  $tvaDeclaree
     * @return Response
     */
    public function destroy($id)
    {
        $tvaDeclaree = TvaDeclaree::find($id);
        $jsonData = ["code" => 1, "msg" => " Opération effectuée avec succès."];
        if ($tvaDeclaree) {
            try {

                $ticketInTvas = TicketInTva::where('ticket_in_tvas.declaration', $tvaDeclaree->id)->get();

                foreach ($ticketInTvas as $ticketInTva) {
                    $ticketInTva->delete();
                }

                $tvaDeclaree->delete();
                $jsonData["data"] = json_decode($tvaDeclaree);
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
