<li class="{{request()->is('stock/*') && !request()->is('stock/depots') ? 'active treeview' : 'treeview'}}">
    <a href="#">
        <i class="fa fa-hourglass-half"></i> <span>Stock</span>
        <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
        </span>
    </a>
    <ul class="treeview-menu">
        <li class="{{Route::currentRouteName() === 'stock.articles.index' ? 'active' : ''}}">
            <a href="{{route('stock.articles.index')}}">
                &nbsp;&nbsp;&nbsp;<i class="fa fa-cubes"></i> Article
            </a>
        </li>
        <li class="{{Route::currentRouteName() === 'stock.approvisionnements.index' ? 'active' : ''}}">
            <a href="{{route('stock.approvisionnements.index')}}">
                &nbsp;&nbsp;&nbsp;<i class="fa fa-sort-amount-asc"></i> Entrée en stock
            </a>
        </li>
        <li class="{{Route::currentRouteName() === 'stock.transfert-stocks.index' ? 'active' : ''}}">
            <a href="{{route('stock.transfert-stocks.index')}}">
                &nbsp;&nbsp;&nbsp;<i class="fa fa-exchange"></i> Transfert de stock
            </a>
        </li>
        <li class="{{Route::currentRouteName() === 'stock.destockages.index' ? 'active' : ''}}">
            <a href="{{route('stock.destockages.index')}}">
                &nbsp;&nbsp;&nbsp;<i class="fa fa-download"></i> Sortie d'articles
            </a>
        </li>
        <li class="{{Route::currentRouteName() === 'stock.inventaires.index' ? 'active' : ''}}">
            <a href="{{route('stock.inventaires.index')}}">
                &nbsp;&nbsp;&nbsp;<i class="fa fa-calendar-plus-o"></i> Inventaire
            </a>
        </li>
        <li class="{{Route::currentRouteName() === 'stock.bon-commandes.index' ? 'active' : ''}}">
            <a href="{{route('stock.bon-commandes.index')}}">
                &nbsp;&nbsp;&nbsp;<i class="fa fa-file-text-o"></i> Bon de commande
            </a>
        </li>
        <!--
        <li class="{{Route::currentRouteName() === 'stock.reception-bon-commandes' ? 'active' : ''}}">
            <a href="{{route('stock.reception-bon-commandes')}}">
                &nbsp;&nbsp;&nbsp;<i class="fa fa-clipboard"></i> R&eacute;ception de bon
            </a>
        </li>
        -->
    </ul>
