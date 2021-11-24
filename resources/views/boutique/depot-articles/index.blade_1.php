@extends('layouts.app')
@section('content')
@foreach($depots as $depot)
<div class="col-md-4">
    <div class="box box-primary box-solid">
        <div class="box-header with-border">
            <h3 class="box-title">{{$depot->libelle_depot.' '.$depot->adresse_depot}}</h3>
        </div>
        <div class="box-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>Article</th>
                        <th>Lot</th>
                        <th align="center">Qt&eacute; / Btle</th>
                    </tr>
                </thead>
                <?php $articles = App\Models\Boutique\DepotArticle::getArticlesDepot($depot->id_depot);?>
                <tbody>
                    @foreach($articles as $article)
                        <tr>
                            <td>{{$article->description_article}}</td>
                            <td>{{$article->libelle_unite}}</td>
                            <td>{{$article->quantite_disponible}}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endforeach
@endsection
