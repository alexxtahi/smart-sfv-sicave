<li class="{{request()->is('parametre/*') && Route::currentRouteName()!=='parametre.depots.index' ? 'active treeview' : 'treeview'}}">
    <a href="#">
        <i class="fa fa-cogs"></i> <span>Param&egrave;tre</span>
        <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
        </span>
    </a>
    <ul class="treeview-menu">
            <li class="{{Route::currentRouteName() === 'parametre.categories.index' ? 'active' : '' }}">
                <a href="{{route('parametre.categories.index')}}">
                    &nbsp;&nbsp;&nbsp;<i class="fa fa-contao"></i> Cat&eacute;gorie
                </a>
            </li>
            <!--
            <li class="{{Route::currentRouteName() === 'parametre.sous-categories' ? 'active' : ''}}">
                <a href="{{route('parametre.sous-categories')}}">
                    &nbsp;&nbsp;&nbsp;<i class="fa fa-chrome"></i> Sous cat&eacute;gorie
                </a>
            </li>
            -->
            <li class="{{Route::currentRouteName() === 'parametre.unites.index' ? 'active' : ''}}">
                <a href="{{route('parametre.unites.index')}}">
                    &nbsp;&nbsp;&nbsp;<i class="fa fa-tachometer"></i> Unit&eacute;
                </a>
            </li>
            <!--
            <li class="{{Route::currentRouteName() === 'parametre.tailles.index' ? 'active' : '' }}">
                <a href="{{route('parametre.tailles.index')}}">
                    &nbsp;&nbsp;&nbsp;<i class="fa fa-arrows-v"></i> Taille
                </a>
            </li>
            <li class="{{Route::currentRouteName() === 'parametre.rayons.index' ? 'active' : ''}}">
                <a href="{{route('parametre.rayons.index')}}">
                    &nbsp;&nbsp;&nbsp;<i class="fa fa-arrows-h"></i> Rayon
                </a>
            </li>
            <li class="{{Route::currentRouteName() === 'parametre.rangees.index' ? 'active' : ''}}">
                <a href="{{route('parametre.rangees.index')}}">
                    &nbsp;&nbsp;&nbsp;<i class="fa fa-bars"></i> Rang&eacute;e
                </a>
            </li>
            <li class="{{Route::currentRouteName() === 'parametre.casiers.index'? 'active' : ''}}">
                <a href="{{route('parametre.casiers.index')}}">
                    &nbsp;&nbsp;&nbsp;<i class="fa fa-arrows"></i> Casier
                </a>
            </li>
            -->
            <li class="{{Route::currentRouteName() === 'parametre.param-tva.index' ? 'active' : ''}}">
                <a href="{{route('parametre.param-tva.index')}}">
                    &nbsp;&nbsp;&nbsp;<i class="fa fa-text-height"></i>Param TVA
                </a>
            </li>
            <li class="{{Route::currentRouteName() === 'parametre.caisses.index' ? 'active' : '' }}">
                <a href="{{route('parametre.caisses.index')}}">
                    &nbsp;&nbsp;&nbsp;<i class="fa fa-hdd-o"></i> Caisse
                </a>
            </li>
            <!--
            <li class="{{Route::currentRouteName() === 'parametre.banques.index' ? 'active' : ''}}">
                <a href="{{route('parametre.banques.index')}}">
                    &nbsp;&nbsp;&nbsp;<i class="fa fa-bank"></i> Banque
                </a>
            </li>
            <li class="{{Route::currentRouteName() === 'parametre.nations.index' ? 'active' : ''}}">
                <a href="{{route('parametre.nations.index')}}">
                    &nbsp;&nbsp;&nbsp;<i class="fa fa-flag"></i> Pays
                </a>
            </li>
            <li class="{{Route::currentRouteName() === 'parametre.moyen-reglements.index' ? 'active' : '' }}">
                <a href="{{route('parametre.moyen-reglements.index')}}">
                    &nbsp;&nbsp;&nbsp;<i class="fa fa-cog"></i> Moyen de payement
                </a>
            </li>
            <li class="{{Route::currentRouteName() === 'parametre.cartes-fidelites.index' ? 'active' : ''}}">
                <a href="{{route('parametre.cartes-fidelites.index')}}">
                    &nbsp;&nbsp;&nbsp;<i class="fa fa-cc-discover"></i> Carte de fid&eacute;lit&eacute;
                </a>
            </li>-->
            <li class="{{Route::currentRouteName() === 'parametre.categorie-depenses.index' ? 'active' : ''}}">
                <a href="{{route('parametre.categorie-depenses.index')}}">
                    &nbsp;&nbsp;&nbsp;<i class="fa fa-list"></i> Dépenses
                </a>
            </li>
    </ul>
