<li class="{{request()->is('crm/*') ? 'active treeview' : 'treeview'}}">
    <a href="#">
        <i class="fa fa-user-times"></i> <span>CRM</span>
        <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
        </span>
    </a>
    <ul class="treeview-menu">
        <!--
        <li class="{{Route::currentRouteName() === 'crm.regimes.index' ? 'active' : ''}}">
            <a href="{{route('crm.regimes.index')}}">
                &nbsp;&nbsp;&nbsp;<i class="fa fa-circle-o"></i> R&eacute;gime
            </a>
        </li>
        -->
        <li class="{{Route::currentRouteName() === 'crm.clients.index' || request()->is('crm/fiche-client/*') ? 'active' : ''}}">
            <a href="{{route('crm.clients.index')}}">
                &nbsp;&nbsp;&nbsp;<i class="fa fa-user"></i> Client
            </a>
        </li>
        <li class="{{Route::currentRouteName() === 'crm.fournisseurs.index' || request()->is('crm/fiche-fournisseur/*') ? 'active' : '' }}">
            <a href="{{route('crm.fournisseurs.index')}}">
                &nbsp;&nbsp;&nbsp;<i class="fa fa-truck"></i> Fournisseur
            </a>
        </li>
        <!--
        <li class="{{Route::currentRouteName() === 'crm.compte-client' ? 'active' : ''}}">
            <a href="{{route('crm.compte-client')}}">
                &nbsp;&nbsp;&nbsp;<i class="fa fa-adjust"></i> Compte client
            </a>
        </li
         <li class="{{Route::currentRouteName() === 'crm.comptes-cartes-fidelites' ? 'active' : ''}}">
            <a href="{{route('crm.comptes-cartes-fidelites')}}">
                &nbsp;&nbsp;&nbsp;<i class="fa fa-credit-card"></i> Compte carte de fid&eacute;lit&eacute;
            </a>
        </li>
        li class="{{Route::currentRouteName() === 'crm.comptes.index' ? 'active' : ''}}">
            <a href="{{route('crm.comptes.index')}}">
                &nbsp;&nbsp;&nbsp;<i class="fa fa-balance-scale"></i>Tous les comptes
            </a>
        </li
        <li class="{{Route::currentRouteName() === 'crm.mouvements.index' ? 'active' : ''}}">
            <a href="{{route('crm.mouvements.index')}}">
                &nbsp;&nbsp;&nbsp;<i class="fa fa-arrows-h"></i>Mouvement des comptes
            </a>
        </li>
        -->
    </ul>
