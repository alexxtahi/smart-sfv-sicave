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
@if($infoConfig==null)
<div class="row"> 
    <div class="col-md-12">
        <form id="formAjout" action="{{route('configuration.store')}}" enctype="multipart/form-data"  method="post">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <span style="font-size: 16px;">
                        <i class="fa fa-cog fa-2x"></i>
                        Configuration des paramètres
                    </span>
                </div>
                <div class="modal-body ">
                    @csrf
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Nom de la compagnie *</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-bank"></i>
                                    </div>
                                    <input type="text" onkeyup="this.value = this.value.charAt(0).toUpperCase() + this.value.substr(1);" class="form-control" id="nom_compagnie" name="nom_compagnie" placeholder="Nom de la compagnie" required>
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
                                    <input type="text" onkeyup="this.value = this.value.charAt(0).toUpperCase() + this.value.substr(1);" class="form-control" id="commune_compagnie" name="commune_compagnie" placeholder="Nom de la ville de la compagnie" required>
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
                                    <input type="text" onkeyup="this.value = this.value.charAt(0).toUpperCase() + this.value.substr(1);" class="form-control" id="adresse_compagnie" name="adresse_compagnie" placeholder="Adresse du siège">
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
                                    <input type="text" onkeyup="this.value = this.value.charAt(0).toUpperCase() + this.value.substr(1);" class="form-control" id="nom_responsable" name="nom_responsable" placeholder="Nom et prénom(s) du reponsable" required>
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
                                    <input type="text" class="form-control bfh-phone" data-format="dd dd-dd-dd-dd" pattern="[0-9]{2} [0-9]{2}-[0-9]{2}-[0-9]{2}-[0-9]{2}"  name="contact_responsable" id="contact_responsable" placeholder="Contact du responsable" required>
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
                                    <input type="text" class="form-control bfh-phone" data-format="dd dd-dd-dd-dd" pattern="[0-9]{2} [0-9]{2}-[0-9]{2}-[0-9]{2}-[0-9]{2}"  name="cellulaire" id="cellulaire" placeholder="Contact mobile">
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
                                    <input type="text" class="form-control bfh-phone" data-format="dd dd-dd-dd-dd" pattern="[0-9]{2} [0-9]{2}-[0-9]{2}-[0-9]{2}-[0-9]{2}"  name="telephone_fixe" id="telephone_fixe" placeholder="Contact fixe">
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
                                    <input type="text" class="form-control bfh-phone" data-format="dd dd-dd-dd-dd" pattern="[0-9]{2} [0-9]{2}-[0-9]{2}-[0-9]{2}-[0-9]{2}"  name="telephone_faxe" id="telephone_faxe" placeholder="Contact faxe">
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
                                    <input type="text" class="form-control" id="site_web_compagnie" name="site_web_compagnie" placeholder="Site web de la compagnie">
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
                                    <input type="email" class="form-control" id="email_compagnie" name="email_compagnie" placeholder="E-mail de la compagnie">
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
                                    <input type="text" class="form-control" id="type_compagnie" name="type_compagnie" placeholder="Ex: SARL, SA...">
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
                                    <input type="text" class="form-control" pattern="[0-9]*"  name="capital" id="capital" placeholder="Montant du capital social">
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
                                    <input type="text" class="form-control" id="rccm" name="rccm" placeholder="RCCM de la compagnie">
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
                                    <input type="text" class="form-control" id="ncc" name="ncc" placeholder="NCC de la compagnie">
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
                                    <input type="text" class="form-control" id="nc_tresor" name="nc_tresor" placeholder="N° du compte au trésor">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Logo de la compagnie *</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-file"></i>
                                    </div>
                                    <input type="file" class="form-control" id="logo" name="logo" required>
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
                                    <input type="text" class="form-control" id="numero_compte_banque" name="numero_compte_banque" placeholder="N° du compte bancaire de la compagnie">
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
                                    <input type="text" class="form-control" id="banque" name="banque" placeholder="Nom de la banque du compte">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success    "><span class="overlay loader-overlay"> <i class="fa fa-refresh fa-spin"></i> </span>Valider</button>
                </div>
            </div>
        </form>
    </div>
</div>
@else
<div class="row">
    <div class="col-md-12">
        <div class="box box-widget widget-user">
            <div class="widget-user-header bg-primary">
                <h2 class="widget-user-username"><b><?= strtoupper($infoConfig->nom_compagnie);?></b>, {{$infoConfig->adresse_compagnie}}</h2>
                    <a href="{{route('configuration.infos-update')}}" class="btn btn-default pull-right">Modifier les infos</a>
                </div>
            <div class="widget-user-image">
                <img class="attachment-img" src="{{asset($infoConfig->logo)}}" alt="Logo">
            </div>
            <div class="box-footer">
                <div class="row">
                    <div class="col-sm-3 border-right">
                        <div class="description-block">
                            <h5 class="description-header">Responsable</h5>
                            <span class="description-text">{{$infoConfig->nom_responsable}}</span>
                        </div>
                        <!-- /.description-block -->
                    </div>
                    <div class="col-sm-3 border-right">
                        <div class="description-block">
                            <h5 class="description-header">Contact du responsable</h5>
                            <span class="description-text">{{$infoConfig->contact_responsable}}</span>
                        </div>
                        <!-- /.description-block -->
                    </div>
                    <div class="col-sm-3">
                        <div class="description-block">
                            <h5 class="description-header">Commune de la compagnie</h5>
                            <span class="description-text">{{$infoConfig->commune_compagnie}}</span>
                        </div>
                        <!-- /.description-block -->
                    </div>
                    <div class="col-sm-3">
                        <div class="description-block">
                            <h5 class="description-header">Contact de la compagnie</h5>
                            <span class="description-text">{{$infoConfig->cellulaire}}</span>
                        </div>
                        <!-- /.description-block -->
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-3">
                        <div class="description-block">
                            <h5 class="description-header">Site web</h5>
                            <span class="description-text" style="text-transform : lowercase;"><?= strtolower($infoConfig->site_web_compagnie); ?></span>
                        </div>
                        <!-- /.description-block -->
                    </div>
                    <div class="col-sm-3">
                        <div class="description-block">
                            <h5 class="description-header">E-mail</h5>
                            <span class="description-text" style="text-transform : lowercase;"><?= strtolower($infoConfig->email_compagnie); ?></span>
                        </div>
                        <!-- /.description-block -->
                    </div>
                    <div class="col-sm-3">
                        <div class="description-block">
                            <h5 class="description-header">T&eacute;l&eacute;phone fixe</h5>
                            <span class="description-text">{{$infoConfig->telephone_fixe}}</span>
                        </div>
                        <!-- /.description-block -->
                    </div>
                    <div class="col-sm-3">
                        <div class="description-block">
                            <h5 class="description-header">T&eacute;l&eacute;phone faxe</h5>
                            <span class="description-text">{{$infoConfig->telephone_faxe}}</span>
                        </div>
                        <!-- /.description-block -->
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-3">
                        <div class="description-block">
                            <h5 class="description-header">Type de compagnie</h5>
                            <span class="description-text">{{$infoConfig->type_compagnie}}</span>
                        </div>
                        <!-- /.description-block -->
                    </div>
                    <div class="col-sm-3">
                        <div class="description-block">
                            <h5 class="description-header">Capital social</h5>
                            <span class="description-text"><?= number_format($infoConfig->capital, 0, ',', ' '); ?></span>
                        </div>
                        <!-- /.description-block -->
                    </div>
                    <div class="col-sm-3">
                        <div class="description-block">
                            <h5 class="description-header">RCCM</h5>
                            <span class="description-text">{{$infoConfig->rccm}}</span>
                        </div>
                        <!-- /.description-block -->
                    </div>
                    <div class="col-sm-3">
                        <div class="description-block">
                            <h5 class="description-header">NCC</h5>
                            <span class="description-text">{{$infoConfig->ncc}}</span>
                        </div>
                        <!-- /.description-block -->
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-3">
                        <div class="description-block">
                            <h5 class="description-header">N° du compte bancaire</h5>
                            <span class="description-text">{{$infoConfig->numero_compte_banque}}</span>
                        </div>
                        <!-- /.description-block -->
                    </div>
                    <div class="col-sm-3">
                        <div class="description-block">
                            <h5 class="description-header">Banque du compte</h5>
                            <span class="description-text">{{$infoConfig->banque}}</span>
                        </div>
                        <!-- /.description-block -->
                    </div>
                    <div class="col-sm-3">
                        <div class="description-block">
                            <h5 class="description-header">N° du compte du tr&eacute;sor</h5>
                            <span class="description-text">{{$infoConfig->nc_tresor}}</span>
                        </div>
                        <!-- /.description-block -->
                    </div>
                </div>
            </div>
        </div>
        <!-- /.widget-user -->
    </div>
</div>
@endif
<script type="text/javascript">

</script>
@else
@include('layouts.partials.look_page')
@endif
@endsection