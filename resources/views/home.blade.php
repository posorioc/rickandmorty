@extends('master')

@section('content')
<div class="header">
	<img src="https://www.notion.so/image/https%3A%2F%2Fs3-us-west-2.amazonaws.com%2Fsecure.notion-static.com%2F70fa2042-9d59-4125-848c-555237d58c4c%2F633249.jpg?table=block&id=84a1b794-dc09-429f-b317-8c2a24e7c217&width=2730&userId=&cache=v2"
		alt="Rick and Morty Challenge"
	/>
</div>
<div class="container main">
	<div class="row mt-5">
        <div class="col-md-12">          
            <h1>Rick and Morty Challenge</h1>
		</div>
	</div>
    <div class="row mt-5">
        <div class="col-md-12">          
            <h2>1. Char counter</h2>
			<ul>
			<li>Cuántas veces aparece la letra "l" (case insensitive) en los nombres de todos los <span>location</span>: <strong>{{$charCounterLocations}}</strong></li>
			<li>Cuántas veces aparece la letra "e" (case insensitive) en los nombres de todos los <span>episode</span>: <strong>{{$charCounterEpisode}}</strong></li>
			<li>Cuántas veces aparece la letra "c" (case insensitive) en los nombres de todos los <span>character</span>: <strong>{{$charCounterCharacter}}</strong></li>
			<li>Cuánto tardó el programa en total: <strong>{{$charCounterTime}} seg</strong></li>
			</ul>
        </div>        
    </div>
	<div class="row mt-5">
        <div class="col-md-12">          
            <h2>2. Episode locations</h2>
			<ul>
			<li>Para cada episode, indicar la cantidad y un listado con las location (origin) de todos los character que aparecieron en ese episode (sin repetir)</li>
			<li>cuánto tardó el programa en total: <strong>{{$episodeLocationTime}} seg</strong></li>
			</ul>
			@foreach($episodeLocation as $episode)
				<h3>Episodio: {{$episode->name}}, Cantidad locaciones: {{$episode->count_origin}}</h3>
				<h4>Locaciones Origen</h4>
				@foreach($episode->origin as $origin)
					<p>{{$origin}}</p>
				@endforeach
			@endforeach				
        </div>        
    </div>
</div>
@endsection