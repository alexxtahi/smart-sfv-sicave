<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Exception;
use App\Models\Boutique\Vente;
use App\Models\Parametre\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class ClientController extends Controller
{
    //Ajouter un client
    public function store(Request $request)
    {
        $jsonData = ["code" => 1, "msg" => "Enregistrement effectué avec succès."];
        if ($request->isMethod('post') && $request->input('full_name_client')) {

            $data = $request->all();

            try {

                $client = new Client;

                //Création code du client
                $maxIdTable = DB::table('clients')->max('id');
                $idClient = $maxIdTable + 1;
                $caractere_speciaux = array("'", "-", " ");
                $code_client = '411' . substr(strtoupper(str_replace($caractere_speciaux, '', $data['full_name_client'])), 0, 3) . $idClient;

                $client->code_client = $code_client;
                $client->full_name_client = $data['full_name_client'];
                $client->contact_client = $data['contact_client'];
                $client->nation_id = $data['nation_id'];
                $client->regime_id = $data['regime_id'];
                $client->email_client = isset($data['email_client']) && !empty($data['email_client']) ? $data['email_client'] : null;
                $client->plafond_client = isset($data['plafond_client']) && !empty($data['plafond_client']) ? $data['plafond_client'] : 0;
                $client->compte_contribuable_client = isset($data['compte_contribuable_client']) && !empty($data['compte_contribuable_client']) ? $data['compte_contribuable_client'] : null;
                $client->boite_postale_client = isset($data['boite_postale_client']) && !empty($data['boite_postale_client']) ? $data['boite_postale_client'] : null;
                $client->adresse_client = isset($data['adresse_client']) && !empty($data['adresse_client']) ? $data['adresse_client'] : null;
                $client->fax_client = isset($data['fax_client']) && !empty($data['fax_client']) ? $data['fax_client'] : null;
                $client->created_by = Auth::user()->id;
                $client->save();
                $jsonData["data"] = json_decode($client);
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

    //Liste des clients
    public function listeClient()
    {
        $clients = Client::with('nation', 'regime')
            ->select('clients.*')
            ->Where('deleted_at', null)
            ->orderBy('clients.id', 'ASC')
            ->get();
        $jsonData["rows"] = $clients->toArray();
        $jsonData["total"] = $clients->count();
        return response()->json($jsonData);
    }

    public function listeAchatsClient($client)
    {

        $totalAchat = 0;
        $totalAcompte = 0;
        $totalRemise = 0;
        $ventes = Vente::with('client', 'depot')
            ->join('article_ventes', 'article_ventes.vente_id', '=', 'ventes.id')->Where([['article_ventes.deleted_at', null], ['article_ventes.retourne', 0]])
            ->select('ventes.*', DB::raw('sum(article_ventes.quantite*article_ventes.prix-article_ventes.remise_sur_ligne) as sommeTotale'), DB::raw('sum(article_ventes.remise_sur_ligne) as sommeRemise'), DB::raw('DATE_FORMAT(ventes.date_vente, "%d-%m-%Y") as date_ventes'))
            ->Where([['ventes.deleted_at', null], ['ventes.client_id', $client]])
            ->groupBy('article_ventes.vente_id')
            ->orderBy('ventes.id', 'DESC')
            ->get();
        foreach ($ventes as $vente) {
            if ($vente->proformat == 0) {
                $totalAchat = $totalAchat + $vente->sommeTotale;
                $totalAcompte = $totalAcompte + $vente->acompte_facture;
                $totalRemise = $totalRemise + $vente->sommeRemise;
            }
        }
        $jsonData["rows"] = $ventes->toArray();
        $jsonData["total"] = $ventes->count();
        $jsonData["totalAchat"] = $totalAchat;
        $jsonData["totalAcompte"] = $totalAcompte;
        $jsonData["totalRemise"] = $totalRemise;
        return response()->json($jsonData);
    }

    //modifier un client
    public function update(Request $request, $id)
    {
        $jsonData = ["code" => 1, "msg" => "Modification effectuée avec succès."];

        $client = Client::find($id);

        if ($client) {
            $data = $request->all();
            try {

                $client->full_name_client = $data['full_name_client'];
                $client->contact_client = $data['contact_client'];
                $client->nation_id = $data['nation_id'];
                $client->regime_id = $data['regime_id'];
                $client->plafond_client = isset($data['plafond_client']) && !empty($data['plafond_client']) ? $data['plafond_client'] : 0;
                $client->compte_contribuable_client = isset($data['compte_contribuable_client']) && !empty($data['compte_contribuable_client']) ? $data['compte_contribuable_client'] : null;
                $client->email_client = isset($data['email_client']) && !empty($data['email_client']) ? $data['email_client'] : null;
                $client->boite_postale_client = isset($data['boite_postale_client']) && !empty($data['boite_postale_client']) ? $data['boite_postale_client'] : null;
                $client->adresse_client = isset($data['adresse_client']) && !empty($data['adresse_client']) ? $data['adresse_client'] : null;
                $client->fax_client = isset($data['fax_client']) && !empty($data['fax_client']) ? $data['fax_client'] : null;
                $client->updated_by = Auth::user()->id;
                $client->save();

                $jsonData["data"] = json_decode($client);
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


    //supprimer un client
    public function destroy($id)
    {
        $jsonData = ["code" => 1, "msg" => " Opération effectuée avec succès."];

        $client = Client::find($id);
        if ($client) {
            try {

                $client->update(['deleted_by' => Auth::user()->id]);
                $client->delete();
                $jsonData["data"] = json_decode($client);
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
