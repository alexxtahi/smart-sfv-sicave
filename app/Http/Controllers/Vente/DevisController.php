<?php

namespace App\Http\Controllers\Vente;

use Exception;
use App\Models\Vente\Devis;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\Vente\ArticleDevis;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
include_once(app_path() . "/number-to-letters/nombre_en_lettre.php");

class DevisController extends Controller
{

    //Fonction pour recuperer les infos de Helpers
    public function infosConfig()
    {
        $get_configuration_infos = \App\Helpers\ConfigurationHelper\Configuration::get_configuration_infos(1);
        return $get_configuration_infos;
    }

    public function index(){

        $clients = DB::table('clients')->Where('deleted_at', NULL)->orderBy('full_name_client', 'asc')->get();
        $depots = DB::table('depots')->Where('deleted_at', NULL)->orderBy('libelle_depot', 'asc')->get();

        $menuPrincipal = "Devis";
        $titleControlleur = "Facture proforma et devis";
        $btnModalAjout = "TRUE";
        return view('vente.devis.index',compact('clients','depots','menuPrincipal', 'titleControlleur', 'btnModalAjout'));
    }

    public function listeDevis(string $numero_devis = null){
        if($numero_devis){
            $devis = Devis::with('depot','client')
                                ->join('article_devis','article_devis.devis_id','=','devis.id')
                                ->select('devis.*',DB::raw('sum(article_devis.quantite*article_devis.prix) as montantTTC'),DB::raw('DATE_FORMAT(devis.date_devis, "%d-%m-%Y") as date_deviss'))
                                ->Where('devis.numero_devis', 'like', '%' . $numero_devis . '%')
                                ->groupBy('article_devis.devis_id')
                                ->orderBy('devis.id','DESC')
                                ->get();
        }else{
            $devis = Devis::with('depot','client')
                                ->join('article_devis','article_devis.devis_id','=','devis.id')
                                ->select('devis.*',DB::raw('sum(article_devis.quantite*article_devis.prix) as montantTTC'),DB::raw('DATE_FORMAT(devis.date_devis, "%d-%m-%Y") as date_deviss'))
                                ->groupBy('article_devis.devis_id')
                                ->orderBy('devis.id','DESC')
                                ->get();
        }

        $jsonData["rows"] = $devis->toArray();
        $jsonData["total"] = $devis->count();
        return response()->json($jsonData);
    }
    public function listeDevisByClient($client){

        $devis = Devis::with('depot','client')
                                ->join('article_devis','article_devis.devis_id','=','devis.id')
                                ->select('devis.*',DB::raw('sum(article_devis.quantite*article_devis.prix) as montantTTC'),DB::raw('DATE_FORMAT(devis.date_devis, "%d-%m-%Y") as date_deviss'))
                                ->Where('devis.client_id', $client)
                                ->groupBy('article_devis.devis_id')
                                ->orderBy('devis.id','DESC')
                                ->get();

        $jsonData["rows"] = $devis->toArray();
        $jsonData["total"] = $devis->count();
        return response()->json($jsonData);
    }
    public function listeDevisByDate($date_vente){
        $date = Carbon::createFromFormat('d-m-Y', $date_vente);

        $devis = Devis::with('depot','client')
                        ->join('article_devis','article_devis.devis_id','=','devis.id')
                        ->select('devis.*',DB::raw('sum(article_devis.quantite*article_devis.prix) as montantTTC'),DB::raw('DATE_FORMAT(devis.date_devis, "%d-%m-%Y") as date_deviss'))
                        ->WhereDate('devis.date_devis',$date)
                        ->groupBy('article_devis.devis_id')
                        ->orderBy('devis.id','DESC')
                        ->get();

        $jsonData["rows"] = $devis->toArray();
        $jsonData["total"] = $devis->count();
        return response()->json($jsonData);
    }

    public function store(Request $request){
        $jsonData = ["code" => 1, "msg" => "Enregistrement effectué avec succès."];

        if ($request->isMethod('post') && $request->input('monPanier')) {
            $data = $request->all();
            try {
                    if(empty($data['monPanier'])){
                        return response()->json(["code" => 0, "msg" => "Votre panier est vide", "data" => NULL]);
                    }

                    //Verification si le client et le dépôt existe
                    if((!isset($data['client_id']) && empty($data['client_id'])) or (!isset($data['depot_id']) && empty($data['depot_id']))){
                        return response()->json(["code" => 0, "msg" => "Les champs dépôt et client sont obligatoire", "data" => NULL]);
                    }

                    //formation numéro du ticket
                    $maxIdDevis = DB::table('devis')->max('id');
                    $annee = date("Y");
                    $numero_id = sprintf("%06d", ($maxIdDevis + 1));

                    $devis = new Devis;
                    $devis->numero_devis = $data["proformat_devis"] == 'devis' ? 'Devis-'.$annee.$numero_id : 'FP-'.$annee.$numero_id;
                    $devis->depot_id = $data["depot_id"];
                    $devis->client_id = $data["client_id"];
                    $devis->proformat_devis = $data["proformat_devis"];
                    $devis->date_devis = now();
                    $devis->save();

                    //Ajout des articles dans la vente
                    if($devis && !empty($data["monPanier"])){
                        //enregistrement des articles de la ligne de vente
                        $panierContent = is_array($data["monPanier"]) ? $data["monPanier"] : array($data["monPanier"]);

                        foreach($panierContent as $index => $article) {

                            $articleDevis = ArticleDevis::where([['devis_id',$devis->id],['depot_id',$data["depot_id"]],['article_id',$data["monPanier"][$index]["articles"]]])->first();
                            if(!$articleDevis){
                                $articleDevis = new ArticleDevis();
                                $articleDevis->article_id = $data["monPanier"][$index]["articles"];
                                $articleDevis->devis_id = $devis->id;
                                $articleDevis->depot_id = $data["depot_id"];
                                $articleDevis->choix_prix = $data["monPanier"][$index]["choix_prix"];
                                $articleDevis->quantite = $data["monPanier"][$index]["quantites"];
                                $articleDevis->prix = $data["monPanier"][$index]["prix"];
                                $articleDevis->save();
                            }
                        }
                    }

                    $jsonData["data"] = json_decode($devis);
                    return response()->json($jsonData);
            } catch (Exception $exc) {
                $jsonData["code"] = -1;
                $jsonData["data"] = NULL;
                $jsonData["msg"] = $exc->getMessage();
                return response()->json($jsonData);
            }
        }
        return response()->json(["code" => 0, "msg" => "Saisie invalide", "data" => NULL]);
    }

    public function updateDevis(Request $request){
        $jsonData = ["code" => 1, "msg" => "Enregistrement effectué avec succès."];
        $data = $request->all();
        $devis = Devis::find($data['idDevis']);

        if($devis) {

            try {

                    $devis->client_id = $data["client_id"];
                    $devis->proformat_devis = $data["proformat_devis"];
                    $devis->save();
                    $jsonData["data"] = json_decode($devis);
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

    public function destroy($id)
    {
        $jsonData = ["code" => 1, "msg" => " Opération effectuée avec succès."];
        $devis = Devis::find($id);

        if($devis){
                try {
                    $articleDevis = ArticleDevis::where('devis_id',$devis->id)->get();

                    foreach($articleDevis as $articleDevi){
                        $articleDevi->delete();
                    }

                    $devis->delete();
                    $jsonData["data"] = json_decode($devis);
                    return response()->json($jsonData);
                } catch (Exception $exc) {
                   $jsonData["code"] = -1;
                   $jsonData["data"] = NULL;
                   $jsonData["msg"] = $exc->getMessage();
                   return response()->json($jsonData);
                }
        }
        return response()->json(["code" => 0, "msg" => "Echec de suppression", "data" => NULL]);
    }

    //Devis PDF
    public function devisPdf($id){
        //Informations du ticket
        $infosDevis = Devis::with('depot','client')
                                ->join('article_ventes','article_ventes.vente_id','=','devis.id')->Where('article_ventes.deleted_at', NULL)
                                ->select('devis.*',DB::raw('sum(article_ventes.quantite*article_ventes.prix) as montantTTC'),DB::raw('DATE_FORMAT(devis.date_devis, "%d-%m-%Y") as date_deviss'))
                                ->groupBy('article_ventes.vente_id')
                                ->where('devis.id',$id)
                                ->first();

        //liste des articles du devis
        $articlesDevis = ArticleDevis::with('article')
                                    ->join('articles', 'articles.id', '=', 'article_devis.article_id')
                                    ->join('param_tvas','param_tvas.id','=','articles.param_tva_id')
                                    ->select('article_devis.*','param_tvas.tva')
                                    ->Where('article_devis.devis_id', $id)
                                    ->get();
        // Tableau de données
        $data = [
            //'header' => $header,
            'infosDevis' => $infosDevis,
            'articlesDevis' => $articlesDevis,
            //'userEdit' => $userEdit,
            'title' => $infosDevis->numero_devis . '-pdf',
        ];
        $data['configs'] = $this->infosConfig();
        $data['Total'] = count($data['articlesDevis']);
        $index = 1;
        $infosDevis['montantHT'] = 0;
        $infosDevis['montantTTC'] = 0;
        $infosDevis['montantTVA'] = 0;
        $infosDevis['qteTotal'] = 0;
        // Calcul du montant total
        foreach ($data['articlesDevis'] as $article) {
            $infosDevis['montantHT'] += $article['prix'] / (1 + $article['tva']);
            $infosDevis['montantTTC'] += $article['prix'] * $article['quantite'];
            if ($article['tva'] != 0)
                $infosDevis['montantTVA'] += ($article['prix'] / (1 + $article['tva'])) * $article['quantite'];
            $infosDevis['qteTotal'] += $article['quantite'];
            $article['index'] = $index++;
        }
        //$infosDevis['montantTVA'] *= $infosDevis['qteTotal'];
        //$infosDevis['montantHT'] = $infosDevis['montantTTC'] / (1 + $articlesDevis[0]['tva']);
        //Si c'est une facture proforma
        // Affichage
        //return $data;
        return view('crm.etats.devis', $data);
    }

}
