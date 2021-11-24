<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Exception;
use App\Models\Parametre\Fournisseur;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class FournisseurController extends Controller
{
    //Ajouter un fournisseur
    public function store(Request $request)
    {
        $jsonData = ["code" => 1, "msg" => "Enregistrement effectué avec succès."];
        if ($request->isMethod('post') && $request->input('full_name_fournisseur')) {

            $data = $request->all();

            try {
                $Fournisseur = Fournisseur::where('full_name_fournisseur', $data['full_name_fournisseur'])->first();
                if ($Fournisseur != null) {
                    return response()->json(["code" => 0, "msg" => "Cet enregistrement existe déjà dans la base", "data" => null]);
                }
                $fournisseur = new Fournisseur;
                //Création code du fournisseur
                $maxIdTable = DB::table('fournisseurs')->max('id');
                $idFournisseur = $maxIdTable + 1;
                $caractere_speciaux = array("'", "-", " ");
                $code_fournisseur = '401' . substr(strtoupper(str_replace($caractere_speciaux, '', $data['full_name_fournisseur'])), 0, 3) . $idFournisseur;

                $fournisseur->code_fournisseur = $code_fournisseur;
                $fournisseur->full_name_fournisseur = $data['full_name_fournisseur'];
                $fournisseur->contact_fournisseur = $data['contact_fournisseur'];
                $fournisseur->nation_id = $data['nation_id'];
                $fournisseur->email_fournisseur = isset($data['email_fournisseur']) && !empty($data['email_fournisseur']) ? $data['email_fournisseur'] : null;
                $fournisseur->banque_id = isset($data['banque_id']) && !empty($data['banque_id']) ? $data['banque_id'] : null;
                $fournisseur->compte_banque_fournisseur = isset($data['compte_banque_fournisseur']) && !empty($data['compte_banque_fournisseur']) ? $data['compte_banque_fournisseur'] : null;
                $fournisseur->compte_contribuable_fournisseur = isset($data['compte_contribuable_fournisseur']) && !empty($data['compte_contribuable_fournisseur']) ? $data['compte_contribuable_fournisseur'] : null;
                $fournisseur->boite_postale_fournisseur = isset($data['boite_postale_fournisseur']) && !empty($data['boite_postale_fournisseur']) ? $data['boite_postale_fournisseur'] : null;
                $fournisseur->adresse_fournisseur = isset($data['adresse_fournisseur']) && !empty($data['adresse_fournisseur']) ? $data['adresse_fournisseur'] : null;
                $fournisseur->fax_fournisseur = isset($data['fax_fournisseur']) && !empty($data['fax_fournisseur']) ? $data['fax_fournisseur'] : null;
                $fournisseur->created_by = Auth::user()->id;
                $fournisseur->save();
                $jsonData["data"] = json_decode($fournisseur);
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

    //Liste des fournisuers

    public function listeFournisseur()
    {
        $fournisseurs = Fournisseur::with('nation', 'banque')
            ->select('fournisseurs.*')
            ->Where('deleted_at', null)
            ->orderBy('full_name_fournisseur', 'ASC')
            ->get();
        $jsonData["rows"] = $fournisseurs->toArray();
        $jsonData["total"] = $fournisseurs->count();
        return response()->json($jsonData);
    }

    //modifier un fournisseur
    public function update(Request $request, Fournisseur $fournisseur)
    {
        $jsonData = ["code" => 1, "msg" => "Modification effectuée avec succès."];

        if ($fournisseur) {
            $data = $request->all();
            try {

                $fournisseur->full_name_fournisseur = $data['full_name_fournisseur'];
                $fournisseur->contact_fournisseur = $data['contact_fournisseur'];
                $fournisseur->nation_id = $data['nation_id'];
                $fournisseur->banque_id = isset($data['banque_id']) && !empty($data['banque_id']) ? $data['banque_id'] : null;
                $fournisseur->compte_banque_fournisseur = isset($data['compte_banque_fournisseur']) && !empty($data['compte_banque_fournisseur']) ? $data['compte_banque_fournisseur'] : null;
                $fournisseur->compte_contribuable_fournisseur = isset($data['compte_contribuable_fournisseur']) && !empty($data['compte_contribuable_fournisseur']) ? $data['compte_contribuable_fournisseur'] : null;
                $fournisseur->email_fournisseur = isset($data['email_fournisseur']) && !empty($data['email_fournisseur']) ? $data['email_fournisseur'] : null;
                $fournisseur->boite_postale_fournisseur = isset($data['boite_postale_fournisseur']) && !empty($data['boite_postale_fournisseur']) ? $data['boite_postale_fournisseur'] : null;
                $fournisseur->adresse_fournisseur = isset($data['adresse_fournisseur']) && !empty($data['adresse_fournisseur']) ? $data['adresse_fournisseur'] : null;
                $fournisseur->fax_fournisseur = isset($data['fax_fournisseur']) && !empty($data['fax_fournisseur']) ? $data['fax_fournisseur'] : null;
                $fournisseur->updated_by = Auth::user()->id;
                $fournisseur->save();

                $jsonData["data"] = json_decode($fournisseur);
                return response()->json($jsonData);
            } catch (Exception $exc) {
                $jsonData["code"] = -1;
                $jsonData["data"] = null;
                $jsonData["msg"] = $exc->getMessage();
                return response()->json($jsonData);
            }
        }
        return response()->json(["code" => 0, "msg" => "Echec de modification", "data" => null]);
    }


    //Supprimer un fournisseur

    public function destroy(Fournisseur $fournisseur)
    {
        $jsonData = ["code" => 1, "msg" => " Opération effectuée avec succès."];
        if ($fournisseur) {
            try {

                $fournisseur->update(['deleted_by' => Auth::user()->id]);
                $fournisseur->delete();
                $jsonData["data"] = json_decode($fournisseur);
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
