@extends('layouts.app')
@section('content')
@if(Auth::user()->role == 'Concepteur')
<script src="{{asset('assets/js/jquery.validate.min.js')}}"></script>
<script src="{{asset('assets/js/bootstrap-table.min.js')}}"></script>
<script src="{{asset('assets/js/underscore-min.js')}}"></script>
<script src="{{asset('assets/plugins/bootstrap-table/locale/bootstrap-table-fr-FR.js')}}"></script>
<script src="{{asset('assets/js/fonction_crude.js')}}"></script>
<script src="{{asset('assets/js/jquery.datetimepicker.full.min.js')}}"></script>
<script src="{{asset('assets/plugins/Bootstrap-form-helpers/js/bootstrap-formhelpers-phone.js')}}"></script>
<script src="{{asset('assets/plugins/datepicker/bootstrap-datepicker.js')}}"></script>
<link href="{{asset('assets/css/bootstrap-table.min.css')}}" rel="stylesheet">
<link href="{{asset('assets/css/jquery.datetimepicker.min.css')}}" rel="stylesheet">
<div class="row"> 
    <div class="col-md-12">
        <form id="formAjout" action="{{route('configuration.update', $configuration->id)}}" enctype="multipart/form-data"  method="post">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <span style="font-size: 16px;">
                        <i class="fa fa-cog fa-2x"></i>
                        Configuration des paramètres <b>(Modification des informations)</b>
                    </span>
                </div>
                <div class="modal-body ">
                    @method('PUT')
                    @csrf
                     <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Nom de la compagnie *</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-bank"></i>
                                    </div>
                                    <input type="text" onkeyup="this.value = this.value.charAt(0).toUpperCase() + this.value.substr(1);" value="{{$configuration->nom_compagnie}}" class="form-control" id="nom_compagnie" name="nom_compagnie" placeholder="Nom de la compagnie" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Ville de la compagnie *</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-institution"></i>
                                    </div>
                                    <input type="text" onkeyup="this.value = this.value.charAt(0).toUpperCase() + this.value.substr(1);" class="form-control" value="{{$configuration->commune_compagnie}}" id="commune_compagnie" name="commune_compagnie" placeholder="Nom de la ville de la compagnie" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Adresse complet de la compagnie</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-map-marker"></i>
                                    </div>
                                    <input type="text" onkeyup="this.value = this.value.charAt(0).toUpperCase() + this.value.substr(1);" class="form-control" value="{{$configuration->adresse_compagnie}}" id="adresse_compagnie" name="adresse_compagnie" placeholder="Adresse du siège">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Responsable *</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-user"></i>
                                    </div>
                                    <input type="text" onkeyup="this.value = this.value.charAt(0).toUpperCase() + this.value.substr(1);" class="form-control" value="{{$configuration->nom_responsable}}" id="nom_responsable" name="nom_responsable" placeholder="Nom et prénom(s) du reponsable" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Contact du responsable *</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-phone"></i>
                                    </div>
                                    <input type="text" class="form-control bfh-phone" data-format="dd dd-dd-dd-dd" pattern="[0-9]{2} [0-9]{2}-[0-9]{2}-[0-9]{2}-[0-9]{2}" value="{{$configuration->contact_responsable}}" name="contact_responsable" id="contact_responsable" placeholder="Contact du responsable" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Contact mobile de la compagnie</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-phone"></i>
                                    </div>
                                    <input type="text" class="form-control bfh-phone" data-format="dd dd-dd-dd-dd" pattern="[0-9]{2} [0-9]{2}-[0-9]{2}-[0-9]{2}-[0-9]{2}" value="{{$configuration->cellulaire}}"  name="cellulaire" id="cellulaire" placeholder="Contact mobile">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Contact fixe de la compagnie</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-mobile-phone"></i>
                                    </div>
                                    <input type="text" class="form-control bfh-phone" data-format="dd dd-dd-dd-dd" pattern="[0-9]{2} [0-9]{2}-[0-9]{2}-[0-9]{2}-[0-9]{2}" value="{{$configuration->telephone_fixe}}"  name="telephone_fixe" id="telephone_fixe" placeholder="Contact fixe">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Contact faxe de la compagnie</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-fax"></i>
                                    </div>
                                    <input type="text" class="form-control bfh-phone" data-format="dd dd-dd-dd-dd" pattern="[0-9]{2} [0-9]{2}-[0-9]{2}-[0-9]{2}-[0-9]{2}" value="{{$configuration->telephone_faxe}}"  name="telephone_faxe" id="telephone_faxe" placeholder="Contact faxe">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Site web de la compagnie</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-link"></i>
                                    </div>
                                    <input type="text" class="form-control" value="{{$configuration->site_web_compagnie}}" id="site_web_compagnie" name="site_web_compagnie" placeholder="Site web de la compagnie">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>E-mail la compagnie</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-at"></i>
                                    </div>
                                    <input type="email" class="form-control" value="{{$configuration->email_compagnie}}" id="email_compagnie" name="email_compagnie" placeholder="E-mail de la compagnie">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Type de compagnie</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-list"></i>
                                    </div>
                                    <input type="text" class="form-control" value="{{$configuration->type_compagnie}}" id="type_compagnie" name="type_compagnie" placeholder="Ex: SARL, SA...">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Capital social</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-money"></i>
                                    </div>
                                    <input type="text" class="form-control" pattern="[0-9]*" value="{{$configuration->capital}}"  name="capital" id="capital" placeholder="Montant du capital social">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>RCCM</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-edit"></i>
                                    </div>
                                    <input type="text" class="form-control" value="{{$configuration->rccm}}" id="rccm" name="rccm" placeholder="RCCM de la compagnie">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>N° du compte contribuable</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-edit"></i>
                                    </div>
                                    <input type="text" class="form-control" value="{{$configuration->ncc}}" id="ncc" name="ncc" placeholder="NCC de la compagnie">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>N° du compte au tr&eacute;sor</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-edit"></i>
                                    </div>
                                    <input type="text" class="form-control" value="{{$configuration->nc_tresor}}" id="nc_tresor" name="nc_tresor" placeholder="N° du compte au trésor">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Logo de la compagnie </label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-file"></i>
                                    </div>
                                    <input type="file" class="form-control" id="logo" name="logo">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>N° du compte bancaire</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-edit"></i>
                                    </div>
                                    <input type="text" class="form-control" value="{{$configuration->numero_compte_banque}}" id="numero_compte_banque" name="numero_compte_banque" placeholder="N° du compte bancaire de la compagnie">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Nom de la banque</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-edit"></i>
                                    </div>
                                    <input type="text" class="form-control" value="{{$configuration->banque}}" id="banque" name="banque" placeholder="Nom de la banque du compte">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success btn-send"><span class="overlay loader-overlay"> <i class="fa fa-refresh fa-spin"></i> </span>Valider</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script type="text/javascript">

</script>
@else
@include('layouts.partials.look_page')
@endif
@endsection