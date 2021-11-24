<li class="{{ request()->is('etat/*')? 'active treeview' : 'treeview'}}">
    <a href="#">
        <i class="glyphicon glyphicon-th-large"></i> <span>Etats</span>
        <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
        </span>
    </a>
    <ul class="treeview-menu">
        <li class="{{Route::currentRouteName() === 'etat.etat-approvisionnements' ? 'active' : ''}}">
            <a href="{{route('etat.etat-approvisionnements')}}">
                &nbsp;&nbsp;&nbsp;<i class="fa fa-circle"></i> Approvisionnement
            </a>
        </li>
        <li class="{{Route::currentRouteName() === 'etat.etat-transfert-stock' ? 'active' : ''}}">
            <a href="{{route('etat.etat-transfert-stock')}}">
                &nbsp;&nbsp;&nbsp;<i class="fa fa-circle"></i> Transfert de stock
            </a>
        </li>
        <li class="{{Route::currentRouteName() === 'etat.etat-destockages' ? 'active' : ''}}">
            <a href="{{route('etat.etat-destockages')}}">
                &nbsp;&nbsp;&nbsp;<i class="fa fa-circle"></i> D&eacute;stockage
            </a>
        </li>
        <li class="{{Route::currentRouteName() === 'etat.etat-inventaires' ? 'active' : ''}}">
            <a href="{{route('etat.etat-inventaires')}}">
                &nbsp;&nbsp;&nbsp;<i class="fa fa-circle"></i> Inventaire
            </a>
        </li>
        <!--li class="{{Route::currentRouteName() === 'etat.etat-bon-commande' ? 'active' : ''}}">
            <a href="{{route('etat.etat-bon-commande')}}">
                &nbsp;&nbsp;&nbsp;<i class="fa fa-circle"></i> Bon de commande
            </a>
        </li-->
        <li class="{{Route::currentRouteName() === 'etat.etat-articles' ? 'active' : ''}}">
            <a href="{{route('etat.etat-articles')}}">
                &nbsp;&nbsp;&nbsp;<i class="fa fa-circle"></i> Article
            </a>
        </li>
        <li class="{{Route::currentRouteName() === 'etat.etat-fournisseurs' ? 'active' : ''}}">
            <a href="{{route('etat.etat-fournisseurs')}}">
                &nbsp;&nbsp;&nbsp;<i class="fa fa-circle"></i> Fournisseur
            </a>
        </li>
        <li class="{{Route::currentRouteName() === 'etat.etat-clients' ? 'active' : ''}}">
            <a href="{{route('etat.etat-clients')}}">
                &nbsp;&nbsp;&nbsp;<i class="fa fa-circle"></i> Client
            </a>
        </li>
        <li class="{{Route::currentRouteName() === 'etat.etat-depots' ? 'active' : ''}}">
            <a href="{{route('etat.etat-depots')}}">
                &nbsp;&nbsp;&nbsp;<i class="fa fa-circle"></i> D&eacute;p&ocirc;t
            </a>
        </li>
    </ul>
