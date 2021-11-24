<?php

namespace App\Http\Controllers\Auth;

use App\Notifications\RegistredUserNotification;
use App\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $depots = DB::table('depots')->Where('deleted_at', null)->orderBy('libelle_depot', 'asc')->get();
        $menuPrincipal = "Auth";
        $titleControlleur = "Gestion des utilisateurs";
        $btnModalAjout = "TRUE";
        return view('auth.user.index', compact('depots', 'btnModalAjout', 'menuPrincipal', 'titleControlleur'));
    }

    public function userAgenceCanal()
    {
        $agences = DB::table('agences')->Where('deleted_at', null)->orderBy('libelle_agence', 'asc')->get();
        $menuPrincipal = "Auth";
        $titleControlleur = "Gestion des utilisateurs des agences";
        $btnModalAjout = "TRUE";
        return view('auth.user.user-agence', compact('agences', 'btnModalAjout', 'menuPrincipal', 'titleControlleur'));
    }

    public function listeUser()
    {
        $users = User::with('depot')
            ->select(DB::raw('DATE_FORMAT(users.last_login_at, "%d-%m-%Y à %H:%i:%s") as last_login'), 'users.*')
            ->orderBy('users.full_name', 'ASC')
            ->where([['users.deleted_at', null], ['users.id', '!=', 1]])
            ->get();
        $jsonData["rows"] = $users->toArray();
        $jsonData["total"] = $users->count();
        return response()->json($jsonData);
    }

    public function listeUserAgence()
    {
        $users = User::with('agence')
            ->select(DB::raw('DATE_FORMAT(users.last_login_at, "%d-%m-%Y à %H:%i:%s") as last_login'), 'users.*')
            ->orderBy('users.full_name', 'ASC')
            ->where([['users.deleted_at', null], ['users.id', '!=', 1], ['users.role', 'agence']])
            ->get();
        $jsonData["rows"] = $users->toArray();
        $jsonData["total"] = $users->count();
        return response()->json($jsonData);
    }

    public function profil()
    {
        $user = User::with('depot')
            ->select('users.*', DB::raw('DATE_FORMAT(users.last_login_at, "%d-%m-%Y à %H:%i:%s") as last_login'), DB::raw('DATE_FORMAT(users.created_at, "%d-%m-%Y à %H:%i:%s") as created'))
            ->where('users.id', Auth::user()->id)
            ->first();

        $menuPrincipal = "Auth";
        $titleControlleur = "Profil utilisateur";
        $btnModalAjout = "FALSE";
        return view('auth.user.profil', compact('user', 'btnModalAjout', 'menuPrincipal', 'titleControlleur'));
    }

    public function infosProfiTolUpdate()
    {
        $user = User::with('depot')
            ->select('users.*')
            ->where('users.id', Auth::user()->id)
            ->first();
        $menuPrincipal = "Auth";
        $titleControlleur = "Informations du profil à modifier";
        $btnModalAjout = "FALSE";
        return view('auth.user.infos-profil-to-update', compact('user', 'btnModalAjout', 'menuPrincipal', 'titleControlleur'));
    }

    public function updatePasswordPage()
    {
        $user = DB::table('users')
            ->select('users.*')
            ->where('users.id', Auth::user()->id)
            ->first();
        $menuPrincipal = "Auth";
        $titleControlleur = "Modification du mot de passe";
        $btnModalAjout = "FALSE";
        return view('auth.user.update-password', compact('user', 'btnModalAjout', 'menuPrincipal', 'titleControlleur'));
    }

    public function updateProfil(Request $request, $id)
    {
        $jsonData = ["code" => 1, "msg" => "Modification effectué avec succès."];
        $user = User::find($id);

        if ($user) {
            $data = $request->all();

            try {

                $UserEmail = User::where([['login', $data['login']], ['id', '!=', $user->id]])->first();
                if ($UserEmail != null) {
                    return response()->json(["code" => 0, "msg" => "Ce login ou adresse mail existe déjà", "data" => null]);
                }
                $user->full_name = $data['full_name'];
                $user->contact = $data['contact'];
                $user->email = isset($data['email']) && !empty($data['email']) ? $data['email'] : null;
                $user->login = isset($data['login']) && !empty($data['login']) ? $data['login'] : null;
                if (isset($data['email']) && !empty($data['email'])) {
                    $user->login =  $data['email'];
                }
                $user->updated_by = Auth::user()->id;
                $user->save();

                $jsonData["data"] = json_decode($user);
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

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        $jsonData = ["code" => 1, "msg" => "Enregistrement effectué avec succès."];
        if ($request->isMethod('post') && $request->input('full_name')) {

            $data = $request->all();

            $user = User::where('login', $data['login'])->first();
            if ($user) {
                return response()->json(["code" => 0, "msg" => "Ce compte existe déjà. Vérifier l'adresse mail ou le login", "data" => null]);
            } else {
                try {
                    if (empty($data['login']) && empty($data['email'])) {
                        return response()->json(["code" => 0, "msg" => "Veillez definir un email ou un login svp", "data" => null]);
                    }
                    $users = new User;
                    $users->full_name = $data['full_name'];
                    $users->role = $data['role'];
                    $users->depot_id = isset($data['depot_id']) && !empty($data['depot_id']) ? $data['depot_id'] : null;
                    $users->contact = $data['contact'];
                    $users->email = isset($data['email']) && !empty($data['email']) ? $data['email'] : null;

                    if (empty($data['password']) && empty($data['login'])) {
                        $users->login = $data['email'];
                        $users->password = bcrypt(Str::random(10));
                        $users->confirmation_token = str_replace('/', '', bcrypt(Str::random(16)));
                        $users->created_by = Auth::user()->id;
                        $users->save();
                        $users->notify(new RegistredUserNotification());
                    } else {
                        $userLogin = User::where('login', $data['login'])->first();
                        if ($userLogin) {
                            return response()->json(["code" => 0, "msg" => "Ce login existe déjà", "data" => null]);
                        }
                        $users->login = $data['login'];
                        $users->password = bcrypt($data['password']);
                        $users->confirmation_token = null;
                        $users->created_by = Auth::user()->id;
                        $users->save();
                    }

                    $jsonData["data"] = json_decode($users);

                    return response()->json($jsonData);
                } catch (Exception $exc) {
                    $jsonData["code"] = -1;
                    $jsonData["data"] = null;
                    $jsonData["msg"] = $exc->getMessage();
                    return response()->json($jsonData);
                }
            }
        }
        return response()->json(["code" => 0, "msg" => "Saisie invalide", "data" => null]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $user = User::find($id);
        $menuPrincipal = "Auth";
        $titleControlleur = "Modifier mes informations";
        return view('auth.user.profil_update', compact('user', 'menuPrincipal', 'titleControlleur'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $jsonData = ["code" => 1, "msg" => "Modification effectuée avec succès."];

        $user = User::find($id);

        if ($user) {
            $data = $request->all();

            try {

                if (($user->login == $data['login']) or ($user->email == $data['email'])) {
                    $user->full_name = $data['full_name'];
                    $user->login = isset($data['login']) && !empty($data['login']) ? $data['login'] : null;
                    if (isset($data['email']) && !empty($data['email'])) {
                        $user->login = $data['email'];
                    }
                    if (isset($data['password']) && !empty($data['password'])) {
                        $user->password = bcrypt($data['password']);
                    }
                    $user->role = $data['role'];
                    $user->depot_id = isset($data['depot_id']) && !empty($data['depot_id']) ? $data['depot_id'] : null;
                    $user->contact = $data['contact'];
                    $user->updated_by = Auth::user()->id;
                    $user->save();
                } else {
                    $userMail = User::where('login', $data['login'])->first();
                    if ($userMail) {
                        return response()->json(["code" => 0, "msg" => "Ce compte existe déjà. Vérifier l'adresse mail ou le login", "data" => null]);
                    }

                    $user->full_name = $data['full_name'];
                    $user->role = $data['role'];
                    $user->contact = $data['contact'];
                    $user->email = isset($data['email']) && !empty($data['email']) ? $data['email'] : null;
                    $user->login = isset($data['login']) && !empty($data['login']) ? $data['login'] : null;
                    $user->depot_id = isset($data['depot_id']) && !empty($data['depot_id']) ? $data['depot_id'] : null;
                    if (empty($data['password']) && empty($data['login'])) {
                        $user->login = $data['email'];
                        $user->password = bcrypt(Str::random(10));
                        $user->confirmation_token = str_replace('/', '', bcrypt(Str::random(16)));
                        $user->created_by = Auth::user()->id;
                        $user->save();
                        $user->notify(new RegistredUserNotification());
                    } else {
                        $userLogin = User::where('login', $data['login'])->first();
                        if ($userLogin) {
                            return response()->json(["code" => 0, "msg" => "Ce login existe déjà", "data" => null]);
                        }
                        $user->login = $data['login'];
                        $user->password = bcrypt($data['password']);
                        $user->confirmation_token = null;
                        $user->created_by = Auth::user()->id;
                        $user->save();
                    }
                }

                $jsonData["data"] = json_decode($user);
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

    /**
     * Activer ou désactiver un utilisateur.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        $jsonData = ["code" => 1, "msg" => " Opération effectuée avec succès."];

        try {
            $user = User::find($id);
            if ($user->statut_compte == 1) {
                $user->statut_compte = FALSE;
            } else {
                $user->statut_compte = TRUE;
            }
            $user->save();
            $jsonData["data"] = json_decode($user);
            return response()->json($jsonData);
        } catch (Exception $exc) {
            $jsonData["code"] = -1;
            $jsonData["data"] = null;
            $jsonData["msg"] = $exc->getMessage();
            return response()->json($jsonData);
        }
    }

    //Vérification du droit d'accés sur la vente en caisse
    public function verificationAccess(Request $request)
    {
        $jsonData = ["code" => 1, "msg" => "Succèss."];
        if ($request->isMethod('post') && $request->input('email')) {

            $data = $request->all();
            $user = User::where([['login', $data['email']], ['statut_compte', 1]])->first();
            if (!$user) {
                return response()->json(["code" => 0, "msg" => "Ce compte n'existe pas ou a été fermé", "data" => null]);
            }
            if (!Hash::check($data['password'], $user->password)) {
                return response()->json(["code" => 0, "msg" => "Vous avez fourni des mauvais identifiants", "data" => null]);
            }

            try {
                if (($user->depot_id == null && $user->role != "Administrateur") or ($user->depot_id != null && $user->role != "Gerant") or ($user->depot_id != null && $user->role == "Gerant" && $user->depot_id != $data['depot_gerant'])) {
                    return response()->json(["code" => 0, "msg" => "Vous n'avez pas ce droit", "data" => null]);
                }
                $jsonData["data"] = json_decode($user);
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

    //Ajout d'utilisateur agence
    // public function addUserAgence(Request $request)
    // {
    //     $jsonData = ["code" => 1, "msg" => "Enregistrement effectué avec succès."];
    //     if ($request->isMethod('post') && $request->input('full_name') && $request->input('agence_id')) {

    //         $data = $request->all();

    //         //            $userLog = User::where('login', $data['login'])->first();
    //         $userEmail = User::where([['email', $data['email']], ['email', '!=', null]])->first();
    //         //            if($userLog){
    //         //                return response()->json(["code" => 0, "msg" => "Ce compte existe déjà. Vérifier le login", "data" => null]);
    //         //            }
    //         if ($userEmail) {
    //             return response()->json(["code" => 0, "msg" => "Ce compte existe déjà. Vérifier l'adresse mail", "data" => null]);
    //         }

    //         try {
    //             //                    if(empty($data['login']) && empty($data['email'])){
    //             //                        return response()->json(["code" => 0, "msg" => "Veillez definir un email ou un login svp", "data" => null]);
    //             //                    }

    //             $agence = Agence::find($data['agence_id']);
    //             $users = new User;
    //             $users->full_name = $data['full_name'];
    //             $users->role = 'Agence';
    //             $users->agence_id = $data['agence_id'];
    //             $users->contact = $data['contact'];
    //             $users->localite_id = $agence->localite_id;
    //             $users->email = $data['email'];
    //             $users->login = $data['email'];
    //             $users->password = bcrypt(Str::random(10));
    //             $users->confirmation_token = str_replace('/', '', bcrypt(Str::random(16)));

    //             //                    $users->email = isset($data['email']) && !empty($data['email']) ? $data['email'] : null;

    //             //                    if(isset($data['password']) && !empty($data['password'])){
    //             //                        $users->password = bcrypt($data['password']);
    //             //                        $users->confirmation_token = null;
    //             //                    }else{
    //             //                        $users->password = bcrypt(Str::random(10));
    //             //                        $users->confirmation_token = str_replace('/', '', bcrypt(Str::random(16)));
    //             //                    }
    //             //                    $users->login = isset($data['login']) && !empty($data['login']) ? $data['login'] : null;
    //             //                    if(isset($data['email']) && !empty($data['email'])) {
    //             //                        $users->login = $data['email'];
    //             //                    }
    //             $users->created_by = Auth::user()->id;
    //             $users->save();
    //             $users->notify(new RegistredUserNotification());

    //             $jsonData["data"] = json_decode($users);

    //             return response()->json($jsonData);
    //         } catch (Exception $exc) {
    //             $jsonData["code"] = -1;
    //             $jsonData["data"] = null;
    //             $jsonData["msg"] = $exc->getMessage();
    //             return response()->json($jsonData);
    //         }
    //     }
    //     return response()->json(["code" => 0, "msg" => "Saisie invalide", "data" => null]);
    // }

    //Modification d'utilisateur agence
    // public function updateUserAgence(Request $request, $id)
    // {
    //     $users = User::find($id);
    //     $jsonData = ["code" => 1, "msg" => "Enregistrement effectué avec succès."];
    //     if ($users) {

    //         $data = $request->all();

    //         //            $userLog = User::where('login', $data['login'])->first();
    //         //            $userEmail = User::where([['email', $data['email']],['email','!=',null]])->first();
    //         //            if($userLog){
    //         //                return response()->json(["code" => 0, "msg" => "Ce compte existe déjà. Vérifier le login", "data" => null]);
    //         //            }
    //         //            if($userEmail){
    //         //                return response()->json(["code" => 0, "msg" => "Ce compte existe déjà. Vérifier l'adresse mail", "data" => null]);
    //         //            }
    //         //
    //         try {
    //             //                    if(empty($data['login']) && empty($data['email'])){
    //             //                        return response()->json(["code" => 0, "msg" => "Veillez definir un email ou un login svp", "data" => null]);
    //             //                    }

    //             $agence = Agence::find($data['agence_id']);

    //             if ($users->email != $data['email']) {
    //                 $users->full_name = $data['full_name'];
    //                 $users->role = 'agence';
    //                 $users->agence_id = $data['agence_id'];
    //                 $users->contact = $data['contact'];
    //                 $users->localite_id = $agence->localite_id;
    //                 $users->email = $data['email'];
    //                 $users->login = $data['email'];
    //                 $users->password = bcrypt(Str::random(10));
    //                 $users->confirmation_token = str_replace('/', '', bcrypt(Str::random(16)));
    //                 $users->notify(new RegistredUserNotification());
    //             } else {
    //                 $users->full_name = $data['full_name'];
    //                 $users->agence_id = $data['agence_id'];
    //                 $users->contact = $data['contact'];
    //                 $users->localite_id = $agence->localite_id;
    //             }
    //             //                    $users->email = isset($data['email']) && !empty($data['email']) ? $data['email'] : null;
    //             //                    if(isset($data['password']) && !empty($data['password'])){
    //             //                        $users->password = bcrypt($data['password']);
    //             //                        $users->confirmation_token = null;
    //             //                    }else{
    //             //                        $users->password = bcrypt(Str::random(10));
    //             //                        $users->confirmation_token = str_replace('/', '', bcrypt(Str::random(16)));
    //             //                    }
    //             //                    $users->login = isset($data['login']) && !empty($data['login']) ? $data['login'] : null;
    //             //                    if(isset($data['email']) && !empty($data['email'])) {
    //             //                        $users->login = $data['email'];
    //             //                    }

    //             $users->updated_by = Auth::user()->id;
    //             $users->save();

    //             $jsonData["data"] = json_decode($users);

    //             return response()->json($jsonData);
    //         } catch (Exception $exc) {
    //             $jsonData["code"] = -1;
    //             $jsonData["data"] = null;
    //             $jsonData["msg"] = $exc->getMessage();
    //             return response()->json($jsonData);
    //         }
    //     }
    //     return response()->json(["code" => 0, "msg" => "Saisie invalide", "data" => null]);
    // }

    //Réinitialisation du mot de passe par l'administrateur
    public function resetPasswordManualy($id)
    {
        $jsonData = ["code" => 1, "msg" => " Opération effectuée avec succès"];

        $user = User::find($id);
        $password = "";
        if ($user && $user->statut_compte != 0) {
            try {
                //Geration du passsword à 8 chiffre
                $ranges = array(range('a', 'z'), range('A', 'Z'), range(1, 9));
                $password = '';
                for ($i = 0; $i < 8; $i++) {
                    $rkey = array_rand($ranges);
                    $vkey = array_rand($ranges[$rkey]);
                    $password .= $ranges[$rkey][$vkey];
                }
                $user->password = bcrypt($password);
                $user->updated_by = $user->id;
                $user->save();
                $to_name = $user->full_name;
                $to_email = $user->email;
                $data = array("name" => $user->full_name, "body" => "Vous avez démandé à rénitialiser votre mot de passe. Votre nouveau mot de passse est : " . $password . " Votre login reste le même : " . $user->email);

                Mail::send('auth/user/mail', $data, function ($message) use ($to_name, $to_email) {
                    $message->to($to_email, $to_name)
                        ->subject('Rénitialisation de votre mot de passe SMART-SFV');
                    $message->from('tranxpert@smartyacademy.com', 'SMART-SFV');
                });
                $jsonData["data"] = json_decode($user);
                return response()->json($jsonData);
            } catch (Exception $exc) {
                $jsonData["code"] = -1;
                $jsonData["data"] = null;
                $jsonData["msg"] = $exc->getMessage();
                return response()->json($jsonData);
            }
        }
        return response()->json(["code" => 0, "msg" => "Ce compte n'existe pas ou a été fermé !", "data" => null]);
    }

    //Modification du mot de passe par l'utilisateur
    public function updatePasswordProfil(Request $request)
    {
        $data = $request->all();
        $user = User::find($data['idUser']);
        if ($user) {
            $credentials = request(['login', 'password']);
            if (!Auth::attempt($credentials)) {
                return redirect()->back()->with('error', 'Votre ancien mot de passe est incorrect.');
            }

            $request->validate([
                'new_password' => 'required|min:8|regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\x])(?=.*[!$@%]).*$/|',
            ]);
            $user->password = bcrypt($data['new_password']);
            $user->updated_by = $user->id;
            $user->save();
            return redirect()->route('auth.profil-informations');
        }
    }
}
