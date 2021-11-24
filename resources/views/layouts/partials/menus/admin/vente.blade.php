<li class="{{ request()->is('vente/*')? 'active treeview' : 'treeview'}}">
    <a href="#">
        <i class="fa fa-shopping-cart"></i> <span>Vente</span>
        <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
        </span>
    </a>
    <ul class="treeview-menu">
        <li class="{{Route::currentRouteName() === 'vente.point-caisse' || Route::currentRouteName() === 'vente.vue-liste-ventes-caisse' ? 'active' : '' }}">
            <a href="{{route('vente.point-caisse')}}">
                &nbsp;&nbsp;&nbsp;<i class="fa fa-hdd-o"></i> Point de caisse
            </a>
        </li>
        <li class="{{Route::currentRouteName() === 'vente.ventes.index' ? 'active' : '' }}">
            <a href="{{route('vente.ventes.index')}}">
                &nbsp;&nbsp;&nbsp;<i class="fa fa-file-excel-o"></i> Facture
            </a>
        </li>
        <li class="{{Route::currentRouteName() === 'vente.devis.index' ? 'active' : '' }}">
            <a href="{{route('vente.devis.index')}}">
                &nbsp;&nbsp;&nbsp;<i class="fa fa-sticky-note-o"></i> Devis et facture proforma
            </a>
        </li>
        <li class="{{Route::currentRouteName() === 'vente.reglements_client' ? 'active' : '' }}">
            <a href="{{route('vente.reglements_client')}}">
                &nbsp;&nbsp;&nbsp;<i class="fa fa-money"></i> R&egrave;glement client
            </a>
        </li>
        <li class="{{Route::currentRouteName() === 'vente.reglements_fournisseur' ? 'active' : '' }}">
            <a href="{{route('vente.reglements_fournisseur')}}">
                &nbsp;&nbsp;&nbsp;<i class="fa fa-google-wallet"></i> R&egrave;glement fournisseur
            </a>
        </li>
    </ul>
