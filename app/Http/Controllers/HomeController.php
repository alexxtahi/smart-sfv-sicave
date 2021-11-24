<?php

namespace App\Http\Controllers;

use App\Models\Boutique\BonCommande;
use App\Models\Boutique\DepotArticle;
use App\Models\Boutique\Vente;
use App\Models\Canal\Agence;
use App\Models\Boutique\Depense;
use App\Models\Parametre\Article;
use App\Models\Parametre\Client;
use App\Models\Parametre\Depot;
use App\Models\Parametre\Fournisseur;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use function view;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return Renderable
     */
    public function index()
    {
        $menuPrincipal = "Accueil";
        $titleControlleur = "Tableau de bord";
        $btnModalAjout = "FALSE";
        return view('home',compact('menuPrincipal', 'titleControlleur', 'btnModalAjout'));
    }

    public function caissier()
    {
        $menuPrincipal = "Accueil";
        $titleControlleur = "Caissier";
        $btnModalAjout = "FALSE";
        return view('caissier',compact('menuPrincipal', 'titleControlleur', 'btnModalAjout'));
    }
}
