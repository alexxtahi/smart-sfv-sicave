<?php

namespace App\Http\Controllers\Parametre;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Parametre\CarteFidelite;

class CarteFideliteController extends Controller
{
    public function index()
    {
        $menuPrincipal = "Paramètre";
        $titleControlleur = "Carte de fidélité";
        $btnModalAjout = "FALSE";
        return view('parametre.carte-fidelite.index', compact('btnModalAjout', 'menuPrincipal', 'titleControlleur'));
    }

    public function listeCarteFidelite()
    {
        $carte_fidelites = DB::table('carte_fidelites')
            ->select('carte_fidelites.*', DB::raw('(carte_fidelites.reduction*100) as reductions'))
            ->Where('deleted_at', null)
            ->orderBy('libelle_carte_fidelite', 'ASC')
            ->get();
        $jsonData["rows"] = $carte_fidelites->toArray();
        $jsonData["total"] = $carte_fidelites->count();
        return response()->json($jsonData);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $jsonData = ["code" => 1, "msg" => "Enregistrement effectué avec succès."];
        if ($request->isMethod('post') && $request->input('libelle_carte_fidelite')) {
            $data = $request->all();

            $carteFidelite =  $data['id'] ? CarteFidelite::findOrFail($data['id']) : new CarteFidelite;

            try {

                $CarteFidelite = CarteFidelite::where([['libelle_carte_fidelite', $data['libelle_carte_fidelite']], ['id', '!=', $data['id']]])->first();
                if ($CarteFidelite != null) {
                    return response()->json(["code" => 0, "msg" => "Cet enregistrement existe déjà dans la base", "data" => null]);
                }

                $carteFidelite->libelle_carte_fidelite = $data['libelle_carte_fidelite'];
                $carteFidelite->reduction = ($data['reduction'] / 100);
                $carteFidelite->created_by = Auth::user()->id;
                $carteFidelite->save();
                $jsonData["data"] = json_decode($carteFidelite);
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
     * @param  \App\Parametre\CarteFidelite  $carteFidelite
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $jsonData = ["code" => 1, "msg" => " Opération effectuée avec succès."];
        $carteFidelite = CarteFidelite::find($id);

        if ($carteFidelite) {
            try {

                $carteFidelite->update(['deleted_by' => Auth::user()->id]);
                $carteFidelite->delete();
                $jsonData["data"] = json_decode($carteFidelite);
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
