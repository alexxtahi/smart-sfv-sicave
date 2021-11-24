<?php

namespace App\Http\Controllers;

use App\Configuration;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class ConfigurationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $infoConfig = Configuration::find(1);
        $menuPrincipal = "Configuration";
        $titleControlleur = "Configuration des paramètres";
        $btnModalAjout = "FALSE";
        return view('configuration.index', compact('infoConfig', 'btnModalAjout', 'menuPrincipal', 'titleControlleur'));
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        $data = $request->all();

        $request->validate([
            'nom_compagnie' => 'required',
            'commune_compagnie' => 'required',
            'nom_responsable' => 'required',
            'contact_responsable' => 'required',
            'logo' => 'mimes:jpeg,jpg,png,gif',
        ]);

        $configuration = new Configuration();
        $configuration->nom_compagnie = $data['nom_compagnie'];
        $configuration->commune_compagnie = $data['commune_compagnie'];
        $configuration->nom_responsable = $data['nom_responsable'];
        $configuration->contact_responsable = $data['contact_responsable'];
        $configuration->cellulaire = isset($data['cellulaire']) && !empty($data['cellulaire']) ? $data['cellulaire'] : null;
        $configuration->telephone_fixe = isset($data['telephone_fixe']) && !empty($data['telephone_fixe']) ? $data['telephone_fixe'] : null;
        $configuration->telephone_faxe = isset($data['telephone_faxe']) && !empty($data['telephone_faxe']) ? $data['telephone_faxe'] : null;
        $configuration->site_web_compagnie = isset($data['site_web_compagnie']) && !empty($data['site_web_compagnie']) ? $data['site_web_compagnie'] : null;
        $configuration->adresse_compagnie = isset($data['adresse_compagnie']) && !empty($data['adresse_compagnie']) ? $data['adresse_compagnie'] : null;
        $configuration->email_compagnie = isset($data['email_compagnie']) && !empty($data['email_compagnie']) ? $data['email_compagnie'] : null;
        $configuration->type_compagnie = isset($data['type_compagnie']) && !empty($data['type_compagnie']) ? $data['type_compagnie'] : null;
        $configuration->capital = isset($data['capital']) && !empty($data['capital']) ? $data['capital'] : null;
        $configuration->rccm = isset($data['rccm']) && !empty($data['rccm']) ? $data['rccm'] : null;
        $configuration->ncc = isset($data['ncc']) && !empty($data['ncc']) ? $data['ncc'] : null;
        $configuration->nc_tresor = isset($data['nc_tresor']) && !empty($data['nc_tresor']) ? $data['nc_tresor'] : null;
        $configuration->numero_compte_banque = isset($data['numero_compte_banque']) && !empty($data['numero_compte_banque']) ? $data['numero_compte_banque'] : null;
        $configuration->banque = isset($data['banque']) && !empty($data['banque']) ? $data['banque'] : null;
        //Insertion de l'image du logo
        if (isset($data['logo']) && !empty($data['logo'])) {
            $logo = request()->file('logo');
            $file_name = str_replace(' ', '_', $logo->getClientOriginalName());
            $search  = array('À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'à', 'á', 'â', 'ã', 'ä', 'å', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ð', 'ò', 'ó', 'ô', 'õ', 'ö', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ');
            $replace = array('A', 'A', 'A', 'A', 'A', 'A', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 'a', 'a', 'a', 'a', 'a', 'a', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y');
            $new_file_name = str_replace($search, $replace, $file_name);

            $path = public_path() . '/images/';
            $logo->move($path, $new_file_name);
            $configuration->logo = 'images/' . $new_file_name;
        }

        $configuration->created_by = Auth::user()->id;
        $configuration->save();
        return redirect()->route('configuration');
    }

    /**
     * Display the specified resource.
     *
     * @param  Configuration  $configuration
     * @return Response
     */
    public function show()
    {
        $configuration = Configuration::find(1);
        $menuPrincipal = "Configuration";
        $titleControlleur = "Modification des informations du paramètre";
        $btnModalAjout = "FALSE";
        return view('configuration.infos-update', compact('configuration', 'btnModalAjout', 'menuPrincipal', 'titleControlleur'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  Configuration  $configuration
     * @return Response
     */
    public function update(Request $request, Configuration $configuration)
    {
        $Configuration = Configuration::find(1);
        if ($Configuration) {

            $request->validate([
                'nom_compagnie' => 'required',
                'commune_compagnie' => 'required',
                'nom_responsable' => 'required',
                'contact_responsable' => 'required',
                'logo' => 'mimes:jpeg,jpg,png,gif',
            ]);
            $data = $request->all();

            $Configuration->nom_compagnie = $data['nom_compagnie'];
            $Configuration->commune_compagnie = $data['commune_compagnie'];
            $Configuration->nom_responsable = $data['nom_responsable'];
            $Configuration->contact_responsable = $data['contact_responsable'];
            $Configuration->cellulaire = isset($data['cellulaire']) && !empty($data['cellulaire']) ? $data['cellulaire'] : null;
            $Configuration->telephone_fixe = isset($data['telephone_fixe']) && !empty($data['telephone_fixe']) ? $data['telephone_fixe'] : null;
            $Configuration->telephone_faxe = isset($data['telephone_faxe']) && !empty($data['telephone_faxe']) ? $data['telephone_faxe'] : null;
            $Configuration->site_web_compagnie = isset($data['site_web_compagnie']) && !empty($data['site_web_compagnie']) ? $data['site_web_compagnie'] : null;
            $Configuration->adresse_compagnie = isset($data['adresse_compagnie']) && !empty($data['adresse_compagnie']) ? $data['adresse_compagnie'] : null;
            $Configuration->email_compagnie = isset($data['email_compagnie']) && !empty($data['email_compagnie']) ? $data['email_compagnie'] : null;
            $Configuration->type_compagnie = isset($data['type_compagnie']) && !empty($data['type_compagnie']) ? $data['type_compagnie'] : null;
            $Configuration->capital = isset($data['capital']) && !empty($data['capital']) ? $data['capital'] : null;
            $Configuration->rccm = isset($data['rccm']) && !empty($data['rccm']) ? $data['rccm'] : null;
            $Configuration->ncc = isset($data['ncc']) && !empty($data['ncc']) ? $data['ncc'] : null;
            $Configuration->nc_tresor = isset($data['nc_tresor']) && !empty($data['nc_tresor']) ? $data['nc_tresor'] : null;
            $Configuration->numero_compte_banque = isset($data['numero_compte_banque']) && !empty($data['numero_compte_banque']) ? $data['numero_compte_banque'] : null;
            $Configuration->banque = isset($data['banque']) && !empty($data['banque']) ? $data['banque'] : null;
            //Insertion de l'image du logo
            if (isset($data['logo']) && !empty($data['logo'])) {
                $logo = request()->file('logo');
                $file_name = str_replace(' ', '_', $logo->getClientOriginalName());
                $search  = array('À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'à', 'á', 'â', 'ã', 'ä', 'å', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ð', 'ò', 'ó', 'ô', 'õ', 'ö', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ');
                $replace = array('A', 'A', 'A', 'A', 'A', 'A', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 'a', 'a', 'a', 'a', 'a', 'a', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y');
                $new_file_name = str_replace($search, $replace, $file_name);

                $path = public_path() . '/images/';
                $logo->move($path, $new_file_name);
                $Configuration->logo = 'images/' . $new_file_name;
            }

            $Configuration->updated_by = Auth::user()->id;
            $Configuration->save();
            return redirect()->route('configuration');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Configuration  $configuration
     * @return Response
     */
    public function destroy(Configuration $configuration)
    {
        //
    }
}
