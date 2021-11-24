<li class="{{request()->is('comptabilite/*') ? 'active treeview' : 'treeview'}}">
    <a href="#">
        <i class="fa fa-book"></i> <span>Comptabilit&eacute;</span>
        <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
        </span>
    </a>
    <ul class="treeview-menu">
        <li class="{{ Route::currentRouteName() === 'comptabilite.soldes-clients'
                        ? 'active' : ''
            }}">
            <a href="{{route('comptabilite.soldes-clients')}}">
                &nbsp;&nbsp;&nbsp;<i class="fa fa-circle"></i> Solde client
            </a>
        </li>
        <li class="{{ Route::currentRouteName() === 'comptabilite.soldes-fournisseurs'
                        ? 'active' : ''
            }}">
            <a href="{{route('comptabilite.soldes-fournisseurs')}}">
                &nbsp;&nbsp;&nbsp;<i class="fa fa-circle"></i> Solde fournisseurs
            </a>
        </li>
        <li class="{{Route::currentRouteName() === 'comptabilite.marge-sur-ventes'
                        ? 'active' : ''
            }}">
            <a href="{{route('comptabilite.marge-sur-ventes')}}">
                &nbsp;&nbsp;&nbsp;<i class="fa fa-circle"></i> Marge sur vente en caisse
            </a>
        </li>
        <li class="{{Route::currentRouteName() === 'comptabilite.points-caisses-clotures'
                        ? 'active' : ''
            }}">
            <a href="{{route('comptabilite.points-caisses-clotures')}}">
                &nbsp;&nbsp;&nbsp;<i class="fa fa-circle"></i> Points de caisse clotur&eacute;s
            </a>
        </li>
        <li class="{{Route::currentRouteName() === 'comptabilite.declaration-fiscale'
                        ? 'active' : ''
            }}">
            <a href="{{route('comptabilite.declaration-fiscale')}}">
                &nbsp;&nbsp;&nbsp;<i class="fa fa-circle"></i> D&eacute;claration TVA
            </a>
        </li>
        <li class="{{Route::currentRouteName() === 'comptabilite.timbre-fiscal'
                        ? 'active' : ''
            }}">
            <a href="{{route('comptabilite.timbre-fiscal')}}">
                &nbsp;&nbsp;&nbsp;<i class="fa fa-circle"></i> D&eacute;claration Timbre
            </a>
        </li>
        <li class="{{Route::currentRouteName() === 'comptabilite.ticket-declare-tva'
                        ? 'active' : ''
            }}">
            <a href="{{route('comptabilite.ticket-declare-tva')}}">
                &nbsp;&nbsp;&nbsp;<i class="fa fa-circle"></i> Tickets d&eacute;clar&eacute;s pour TVA
            </a>
        </li>
        <li class="{{Route::currentRouteName() === 'comptabilite.ticket-declare-timbre'
                        ? 'active' : ''
            }}">
            <a href="{{route('comptabilite.ticket-declare-timbre')}}">
                &nbsp;&nbsp;&nbsp;<i class="fa fa-circle"></i> Tickets d&eacute;clar&eacute;s pour timbre
            </a>
        </li>
        <li class="{{Route::currentRouteName() === 'comptabilite.depenses.index'
                        ? 'active' : ''
            }}">
            <a href="{{route('comptabilite.depenses.index')}}">
                &nbsp;&nbsp;&nbsp;<i class="fa fa-circle"></i> D&eacute;penses
            </a>
        </li>
    </ul>
