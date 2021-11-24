@extends('layouts.app')
@section('content')
<script src="{{asset('assets/js/jquery.validate.min.js')}}"></script>
    @if(Auth::user()->role == 'Caissier')
        <div class="row">
            @foreach($caisses as $caisse)
                <div class="col-md-3 col-sm-6 col-xs-12">
                    <div class="info-box">
                        <a style="text-decoration: none; color: #000000; cursor:pointer;" onClick="ouvrirCaisse({{$caisse->id}})">
                            <span class="info-box-icon {{$caisse->ouvert ? 'bg-green' : 'bg-red'}}">
                                <i class="fa fa-fax"></i>
                            </span>
                            <div class="info-box-content">
                                <span class="info-box-text {{$caisse->ouvert ? 'text-green' : 'text-red'}}">{{$caisse->ouvert ? 'Ouverte' : 'Fermée'}}</span>
                                <span class="info-box-number" id="nom_caisse-{{$caisse->id}}">{{$caisse->libelle_caisse}}</span>
                            </div>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="row">
            @foreach($depots as $depot)
                <div class="col-md-12">
                    <div class="box box-default {{ $depot->id == 1 ? '' : 'collapsed-box' }}">
                        <div class="box-header with-border">
                            <h3 class="box-title">{{$depot->libelle_depot}}</h3>
                            <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                            </button>
                            </div>
                        </div>
                        <div class="box-body" style="">
                            @php $caisses = App\Models\Parametre\Caisse::getCaissesByDepot($depot->id) @endphp
                        <div class="row">
                            @foreach($caisses as $caisse)
                                <div class="col-md-3 col-sm-6 col-xs-12">
                                    <div class="info-box">
                                        <a style="text-decoration: none; color: #000000; cursor:pointer;" onClick="ouvrirCaisseDepot({{$caisse->id}})">
                                            <span class="info-box-icon {{$caisse->ouvert ? 'bg-green' : 'bg-red'}}">
                                                <i class="fa fa-fax"></i>
                                            </span>
                                            <div class="info-box-content">
                                                <span class="info-box-text {{$caisse->ouvert ? 'text-green' : 'text-red'}}">{{$caisse->ouvert ? 'Ouverte' : 'Fermée'}}</span>
                                                <span class="info-box-number" id="nom_caisse_depot-{{$caisse->id}}">{{$caisse->libelle_caisse}}</span>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        </div>
                    </div>
                </div>
            @endforeach
       </div>
    @endif

<!-- Modal ouverture caisse-->
<div class="modal fade bs-modal-ouverture-caisse" role="dialog" data-backdrop="static">
    <form id="formOuvertureCaisse" class="form-horizontal" action="#" method="post">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-green-active">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <span class="circle">
                        Ouverture de caisse
                    </span>
                </div>
                @csrf
                <div class="modal-body">
                    <div class="box-body">
                        <p class="h3 text-center">Ouverture de <span id="libelle_caisse"></span></p>
                        <input name="caisse_id" id="caisse_id" class="hidden"/><br/>
                        <div class="form-group">
                            <label class="col-sm-4 control-label">Montant &agrave; l'ouverture *</label>
                            <div class="col-md-8">
                                <input type="text" pattern="[0-9]*" id="montant_ouverture" name="montant_ouverture" class="form-control"  placeholder="Montant à l'ouverture" required="required"/>
                            </div>
                        </div>
                        <!--<span class="pull-right label label-danger">Toutes vos op&eacute;rations financi&egrave;res seront enregistr&eacute;es dans cette caisse.</span>-->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-sm btn-success"><span class="overlay loader-overlay"> <i class="fa fa-refresh fa-spin"></i> </span> Valider</button>
                </div>
            </div>
        </div>
    </form>
</div>

<script type="text/javascript">

    $(function () {
        $("#formOuvertureCaisse").submit(function(e){
            e.preventDefault();
            var $valid = $(this).valid();
            if (!$valid) {
                    $validator.focusInvalid();
                    return false;
            }
            var $ajaxLoader = $("#formOuvertureCaisse .loader-overlay");
            var methode = 'POST';
            var url = "{{route('comptabilite.ouverture-caisse')}}";
            ouvertureCaisseAction(methode, url, $(this), $(this).serialize(), $ajaxLoader);
        });
    });

    function ouvrirCaisse(idCaisse){
        var caisse = document.getElementById("nom_caisse-"+idCaisse);
        var titre = caisse.textContent;
        $("#caisse_id").val(idCaisse);
        $("#libelle_caisse").html(titre);
        $("#montant_ouverture").val("");
        $(".bs-modal-ouverture-caisse").modal("show");
    }

    function ouvrirCaisseDepot(idCaisse){
        $.getJSON("../parametre/liste-caisses-by-id/" + idCaisse, function (reponse) {
            $.each(reponse.rows, function (index, caisse) {
                if(caisse.ouvert == 0){
                   alert('Cette caisse est fermée');
                   return;
                }else{
                    location.href ="../vente/vue-liste-ventes-caisse/"+caisse.id;
                }
            });
        })

    }

    function ouvertureCaisseAction(methode, url, $formObject, formData, $ajoutLoader) {
        jQuery.ajax({
            type: methode,
            url: url,
            cache: false,
            data: formData,
            success:function (reponse, textStatus, xhr){
                if (reponse.code === 1) {
                    //Si la caisse est ouverte on recharge la page
                    location.reload();
                }
                $.gritter.add({
                    // heading of the notification
                    title: "SMART-SFV",
                    // the text inside the notification
                    text: reponse.msg,
                    sticky: false,
                    image: basePath + "/assets/img/gritter/confirm.png",
                });
            },
            error: function (err) {
                var res = eval('('+err.responseText+')');
                var messageErreur = res.message;

                $.gritter.add({
                    // heading of the notification
                    title: "SMART-SFV",
                    // the text inside the notification
                    text: messageErreur,
                    sticky: false,
                    image: basePath + "/assets/img/gritter/confirm.png",
                });
                $formObject.removeAttr("disabled");
                $ajoutLoader.hide();
            },
            beforeSend: function () {
                $formObject.attr("disabled", true);
                $ajoutLoader.show();
            },
            complete: function () {
                $ajoutLoader.hide();
            },
        });
    };

</script>
@endsection
