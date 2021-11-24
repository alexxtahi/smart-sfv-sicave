<?php

namespace App\Http\Controllers\Crm;

use Exception;
use App\Models\Crm\Compte;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Crm\MouvementCompte;
use App\Http\Controllers\Controller;

class MouvementCompteController extends Controller
{
    //Fonction pour recuperer les infos de Helpers
    public function infosConfig()
    {
        $get_configuration_infos = \App\Helpers\ConfigurationHelper\Configuration::get_configuration_infos(1);
        return $get_configuration_infos;
    }

    public function index()
    {
        $comptes = DB::table('comptes')->orderBy('id', 'DESC')->get();

        $menuPrincipal = "CRM";
        $titleControlleur = "Mouvement des comptes";
        $btnModalAjout = "FALSE";
        return view('crm.mouvement-compte.index', compact('comptes', 'menuPrincipal', 'titleControlleur', 'btnModalAjout'));
    }

    public function listeMouvementCompte()
    {
        $mouvements = MouvementCompte::with('compte')
            ->select('mouvement_comptes.*', DB::raw('DATE_FORMAT(date_mouvement, "%d-%m-%Y") as date_mouvements'))
            ->orderBy('mouvement_comptes.id', 'DESC')
            ->get();
        $jsonData["rows"] = $mouvements->toArray();
        $jsonData["total"] = $mouvements->count();
        return response()->json($jsonData);
    }

    public function listeMouvementComptePdf()
    {
        // Chargement des données
        $data = $this->listeMouvementCompte();
        $data = json_decode($data->content(), true);
        $data['configs'] = $this->infosConfig();
        $data['Total'] = count($data['rows']);
        $data['totalEntree'] = 0;
        $data['totalSortie'] = 0;
        // Calcul du montant total
        foreach ($data['rows'] as $row) {
            $data['totalEntree'] += $row['entree'];
            $data['totalSortie'] += $row['sortie'];
        }
        // Affichage
        //return $data;
        return view('crm.etats.liste-mouvement-comptes', $data);
    }

    public function listeMouvementCompteByCompte($compte)
    {
        $mouvements = MouvementCompte::with('compte')
            ->where('compte_id', $compte)
            ->select('mouvement_comptes.*', DB::raw('DATE_FORMAT(date_mouvement, "%d-%m-%Y") as date_mouvements'))
            ->orderBy('mouvement_comptes.id', 'DESC')
            ->get();
        $jsonData["rows"] = $mouvements->toArray();
        $jsonData["total"] = $mouvements->count();
        return response()->json($jsonData);
    }

    public function listeMouvementCompteByDate($dates)
    {
        $date = Carbon::createFromFormat('d-m-Y', $dates);
        $mouvements = MouvementCompte::with('compte')
            ->whereDate('date_mouvement', '=', $date)
            ->select('mouvement_comptes.*', DB::raw('DATE_FORMAT(date_mouvement, "%d-%m-%Y") as date_mouvements'))
            ->orderBy('mouvement_comptes.id', 'DESC')
            ->get();
        $jsonData["rows"] = $mouvements->toArray();
        $jsonData["total"] = $mouvements->count();
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
        $data = $request->all();

        $mouvement = MouvementCompte::findOrFail($data['id']);
        $compte = Compte::find($mouvement->compte_id);

        if ($compte) {
            try {

                $compte->entree = $compte->entree - $mouvement->entree;
                $compte->sortie = $compte->sortie - $mouvement->sortie;
                $compte->save();

                $mouvement->initiale = $compte->entree - $compte->sortie;
                $mouvement->entree = isset($data['entree']) && !empty($data['entree']) ? $data['entree'] : null;
                $mouvement->sortie = isset($data['sortie']) && !empty($data['sortie']) ? $data['sortie'] : null;
                $mouvement->save();


                $compte->entree = $compte->entree + (isset($data['entree']) && !empty($data['entree']) ? $data['entree'] : 0);
                $compte->sortie = $compte->sortie + (isset($data['sortie']) && !empty($data['sortie']) ? $data['sortie'] : 0);
                $compte->save();

                $jsonData["data"] = json_decode($mouvement);
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
     * @param  \App\Models\Crm\MouvementCompte  $mouvementCompte
     * @return Response
     */
    public function destroy($id)
    {
        $mouvement = MouvementCompte::findOrFail($id);

        $jsonData = ["code" => 1, "msg" => " Opération effectuée avec succès."];
        if ($mouvement) {
            try {

                $compte = Compte::find($mouvement->compte_id);

                if ($compte) {
                    $compte->entree = $compte->entree - $mouvement->entree;
                    $compte->sortie = $compte->sortie - $mouvement->sortie;
                    $compte->save();
                }

                $mouvement->delete();

                $jsonData["data"] = json_decode($compte);
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
