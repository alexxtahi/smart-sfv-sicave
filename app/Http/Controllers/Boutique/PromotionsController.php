<?php

namespace App\Http\Controllers\Boutique;

use App\Http\Controllers\Controller;
use App\Models\Boutique\DepotArticle;
use App\Models\Boutique\Promotions;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PromotionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $depots = DB::table('depots')->Where('deleted_at', null)->orderBy('libelle_depot', 'asc')->get();
        $menuPrincipal = "Boutique";
        $titleControlleur = "Promotion";
        $btnModalAjout = "TRUE";
        return view('boutique.promotion.index', compact('depots', 'menuPrincipal', 'titleControlleur', 'btnModalAjout'));
    }

    public function listePromotion()
    {
        $promotions = Promotions::with('article', 'unite', 'depot')
            ->select('promotions.*', DB::raw('DATE_FORMAT(promotions.date_debut, "%d-%m-%Y") as date_debuts'), DB::raw('DATE_FORMAT(promotions.date_fin, "%d-%m-%Y") as date_fins'))
            ->Where('promotions.deleted_at', null)
            ->orderBy('promotions.id', 'DESC')
            ->get();
        $jsonData["rows"] = $promotions->toArray();
        $jsonData["total"] = $promotions->count();
        return response()->json($jsonData);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        $jsonData = ["code" => 1, "msg" => "Enregistrement effectué avec succès."];
        if ($request->isMethod('post') && $request->input('prix_promotion')) {
            $data = $request->all();
            try {
                //La date du début de la promotion ne doit pas être supérieure à la date de fin vérifier
                if (Carbon::createFromFormat('d-m-Y', $data['date_debut']) > Carbon::createFromFormat('d-m-Y', $data['date_fin'])) {
                    throw new Exception("La date du début de la promotion est supérieure à la date de fin vérifier svp!");
                }
                //Vérifions si l'article est disponible en stock et que la quantité est suiffisante
                $depo_article = DepotArticle::where([['depot_id', $data['depot_id']], ['article_id', $data['article_id']], ['unite_id', $data['unite_id']]])->first();

                if ($depo_article != null && $depo_article->quantite_disponible > 0) {
                } else {
                    return response()->json(["code" => 0, "msg" => "Ce lot d'article n'est pas dans le stock ou la quantité disponible dans ce dépôt est insuiffisante", "data" => null]);
                }

                $promotion = new Promotions;
                $promotion->date_debut = Carbon::createFromFormat('d-m-Y', $data['date_debut']);
                $promotion->date_fin = Carbon::createFromFormat('d-m-Y', $data['date_fin']);
                $promotion->article_id = $data['article_id'];
                $promotion->depot_id = $data['depot_id'];
                $promotion->unite_id = $data['unite_id'];
                $promotion->prix_promotion = $data['prix_promotion'];
                $promotion->en_promotion = isset($data['en_promotion']) && !empty($data['en_promotion']) ? TRUE : FALSE;
                $promotion->created_by = Auth::user()->id;
                $promotion->save();
                $jsonData["data"] = json_decode($promotion);
                return response()->json($jsonData);
            } catch (Exception $exc) {
                $jsonData["code"] = -1;
                $jsonData["data"] = null;
                $jsonData["msg"] = $exc->getMessage();
                return response()->json($jsonData);
            }
        }
        return response()->json(["code" => 0, "msg" => "Saisie invalide", "data" => null]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  \App\Promotions  $promotions
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $jsonData = ["code" => 1, "msg" => "Modification effectuée avec succès."];
        $promotion = Promotions::find($id);
        if ($promotion) {
            $data = $request->all();
            try {

                //La date du début de la promotion ne doit pas être supérieure à la date de fin vérifier
                if (Carbon::createFromFormat('d-m-Y', $data['date_debut']) > Carbon::createFromFormat('d-m-Y', $data['date_fin'])) {
                    throw new Exception("La date du début de la promotion est supérieure à la date de fin vérifier svp!");
                }
                //Vérifions si l'article est disponible en stock et que la quantité est suiffisante
                $depo_article = DepotArticle::where([['depot_id', $data['depot_id']], ['article_id', $data['article_id']], ['unite_id', $data['unite_id']]])->first();

                if ($depo_article != null && $depo_article->quantite_disponible > 0) {
                } else {
                    return response()->json(["code" => 0, "msg" => "Ce lot d'article n'est pas dans le stock ou la quantité disponible dans ce dépôt est insuiffisante", "data" => null]);
                }

                $promotion->date_debut = Carbon::createFromFormat('d-m-Y', $data['date_debut']);
                $promotion->date_fin = Carbon::createFromFormat('d-m-Y', $data['date_fin']);
                $promotion->article_id = $data['article_id'];
                $promotion->depot_id = $data['depot_id'];
                $promotion->unite_id = $data['unite_id'];
                $promotion->prix_promotion = $data['prix_promotion'];
                $promotion->en_promotion = isset($data['en_promotion']) && !empty($data['en_promotion']) ? TRUE : FALSE;
                $promotion->updated_by = Auth::user()->id;
                $promotion->save();

                $jsonData["data"] = json_decode($promotion);
                return response()->json($jsonData);
            } catch (Exception $exc) {
                $jsonData["code"] = -1;
                $jsonData["data"] = null;
                $jsonData["msg"] = $exc->getMessage();
                return response()->json($jsonData);
            }
        }
        return response()->json(["code" => 0, "msg" => "Saisie invalide", "data" => null]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Promotions  $promotions
     * @return Response
     */
    public function destroy($id)
    {
        $jsonData = ["code" => 1, "msg" => " Opération effectuée avec succès."];
        $promotion = Promotions::find($id);
        if ($promotion) {
            try {
                $promotion->update(['deleted_by' => Auth::user()->id]);
                $promotion->delete();
                $jsonData["data"] = json_decode($promotion);
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
