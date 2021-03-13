<?php

namespace App\Http\Controllers;
use Illuminate\Support\Collection;


class RickandMortyController extends Controller
{
    private $character;
	private $characters;
	private $location;
	private $episodes;
	
	
    public function __construct()
    {
        $this->character = 'https://rickandmortyapi.com/api/character';
		$this->location = 'https://rickandmortyapi.com/api/location';
		$this->episodes = 'https://rickandmortyapi.com/api/episode';
    }

    public function index()
	{
		$this->characters = $this->getCharacter(); //Descargo los personajes desde la API.
		$charCounter = $this->charCounter();
		$episodeLocation = $this->episodeLocations();
	
		return view('home')
			->with('charCounterLocations',$charCounter['location'])
			->with('charCounterEpisode',$charCounter['episodes'])
			->with('charCounterCharacter',$charCounter['characters'])
			->with('charCounterTime',$charCounter['time'])
			->with('episodeLocation',$episodeLocation['episodes'])
			->with('episodeLocationTime',$episodeLocation['time']);
	}
	
	/*
	Descripcion: Esta funcion es la función principal para contar los caracteres y llama las otras funciones.
	Output: array(location, episodes, characters, time)
		location: Cantidad de caracteres 'x' en las locaciones,
		episodes: Cantidad de caracteres 'x' en los episiodios,
		characters: Cantidad de caracteres 'x' en los personajes,
		time: Tiempo de ejecución.
		
	*/
	public function charCounter(){
		$start_time = microtime(true); //Tiempo de inicio
		$locations = $this->charCounterLocations('l');
		//echo 'Cuántas veces aparece la letra "l" en los nombres de todos los location: '.$locations['count_char'].'<br>';
		//echo 'Cantidad de locaciones '.$locations['count_locations'].'<br>';
		
		$episodes = $this->charCounterEpisode('e');
		//echo 'Cuántas veces aparece la letra "e" en los nombres de todos los episode: '.$episodes['count_char'].'<br>';
		//echo 'Cantidad de episodios '.$episodes['count_episodes'].'<br>';
		
		$characters = $this->charCounterCharacter('c');
		//echo 'Cuántas veces aparece la letra "c" en los nombres de todos los character: '.$characters['count_char'].'<br>';
		//echo 'Cantidad de personajes '.$characters['count_characters'].'<br>';
		
		$end_time = microtime(true); //Tiempo de termino
		$time = number_format(($end_time - $start_time), 2); //Tiempo de total

		return array(
			'location' => $locations['count_char'], 
			'episodes' => $episodes['count_char'], 
			'characters' => $characters['count_char'],
			'time' => $time
		);
	}
	
	/*
	Descripcion: Esta funcion cuenta cuantas veces se encuentra un caracter en los nombres de todos los location.
	Input: $char
	Output: array(count_char, count_locations)
		count_char: Cantidad de caracteres,
		count_locations: Cantidad de locaciones
		
	*/
	private function charCounterLocations($char)
	{
		$locations = $this->sendRequest($this->location);
		$count_locations = 0; //Se usa para validar si se estan contando todas las locaciones
		$count_char = 0;  //Se usa para guardar cuantas veces se encuentra un caracter en los nombres de todos los location
		if($locations){
			$pages = $locations['info']['pages'];
			for($i = 1;$i<=$pages; $i++ ){
				$data = $this->sendRequest($this->location.'?page='.$i);
				$count_locations = $count_locations + count($data['results']);
				if($data['results']){
					foreach($data['results'] as $result){						
						$count_char = $count_char + substr_count($result['name'],$char);
					}
				}
			}	
		}
		return array('count_char' => $count_char, 'count_locations' => $count_locations);		
	}
	
	/*
	Descripcion: Esta funcion cuenta cuantas veces se encuentra un caracter en los nombres de todos los episodios.
	Input: $char
	Output: array(count_char, count_episodes)
		count_char: Cantidad de caracteres,
		count_episodes: Cantidad de episodios
	*/
	private function charCounterEpisode($char)
	{
		$episodes = $this->sendRequest($this->episodes);
		$count_episodes = 0; //Se usa para validar si se estan contando todas los episodios
		$count_char = 0;  //Se usa para guardar cuantas veces se encuentra un caracter en los nombres de todos los episodios
		if($episodes){
			$pages = $episodes['info']['pages'];
			for($i = 1;$i<=$pages; $i++ ){
				$data = $this->sendRequest($this->episodes.'?page='.$i);
				$count_episodes = $count_episodes + count($data['results']);
				if($data['results']){
					foreach($data['results'] as $result){						
						$count_char = $count_char + substr_count($result['name'],$char);
					}
				}
			}	
		}
		return array('count_char' => $count_char, 'count_episodes' => $count_episodes);		
	}
	
	/*
	Descripcion: Esta funcion cuenta cuantas veces se encuentra un caracter en los nombres de todos los personajes.
	Input: $char
	Output: array(count_char, count_locations)
		count_char: Cantidad de caracteres,
		count_characters: Cantidad de personajes
		
	*/
	private function charCounterCharacter($char)
	{
		$count_characters = 0; //Se usa para validar si se estan contando todas los personajes
		$count_char = 0;  //Se usa para guardar cuantas veces se encuentra un caracter en los nombres de todos los personajes
		if($this->characters){
			$count_characters = count($this->characters);
			foreach($this->characters as $character){
				$count_char = $count_char + substr_count($character->name,$char);
			}
		}
		return array('count_char' => $count_char, 'count_characters' => $count_characters);		
	}
	
	/*
	Descripcion: Esta funcion cuenta las locaciones de origen distintas de los personajes por capitulos.
	Output: array(episodes, time)
		episodes: Coleccion con los datos de los episodios y las locaciones de los personajes.
		time: Cantidad de personajes		
	*/
	public function episodeLocations(){
		$characters = $this->characters; //Consulto los personajes
		$start_time = microtime(true); //Tiempo de inicio
		//$location = collect(); //Se crea una coleccion para trabajar con los datos
		$episodes = collect();
		$data = $this->sendRequest($this->episodes); //Consulto los episodios
		if($data){
			if($data['results']){
				foreach($data['results'] as $result){ //Recorro los episodios por pagina
					$aux_episodio = collect();
					$aux_episodio->id = $result['id'];
					$aux_episodio->name = $result['name'];
					$aux_episodio->origin = collect();
					$aux_location = collect();
					foreach($result['characters'] as $aux){
						$aux_character = $characters->where('url', $aux);
						foreach($aux_character as $character){
							$aux_location->push($character->origin);	
						}
					}
					$aux_episodio->origin = $aux_location->unique();
					$aux_episodio->count_origin = $aux_episodio->origin->count();
					$episodes->push($aux_episodio);
				}
			}
			$pages = $data['info']['pages']; //Consulto la cantidad de paginas
			for($i = 2;$i<=$pages; $i++ ){
				$page = $this->sendRequest($this->episodes.'?page='.$i); //Consulto los episodios por pagina
				if($page['results']){
					foreach($page['results'] as $result){ //Recorro los episodios por pagina
						$aux_episodio = collect();
						$aux_episodio->id = $result['id'];
						$aux_episodio->name = $result['name'];
						$aux_episodio->origin = collect();
						$aux_location = collect();
						foreach($result['characters'] as $aux){
							$aux_character = $characters->where('url', $aux);
							foreach($aux_character as $character){
								$aux_location->push($character->origin);	
							}
						}
						$aux_episodio->origin = $aux_location->unique();
						$aux_episodio->count_origin = $aux_episodio->origin->count();
						$episodes->push($aux_episodio);
					}
				}
			}	
		}
		$end_time = microtime(true); //Tiempo de termino
		$time = number_format(($end_time - $start_time), 2); //Tiempo de total

		return array('episodes' => $episodes, 'time' => $time);
	}
	
	/*
		Descripcion: Esta funcion realiza obtiene los datos de los personajes
		Output: $characters. Coleccion con los datos: id, name, origin, url.
	*/	
	private function getCharacter()
	{
		$characters = collect(); //Se crea una coleccion para trabajar con los datos
	
		$data = $this->sendRequest($this->character);	
		if($data){
			$pages = $data['info']['pages'];
			if($data['results']){
				foreach($data['results'] as $result){ //Recorro los resultados por pagina y los guardo en una coleccion
					$aux_character = collect();
					$aux_character->id = $result['id'];
					$aux_character->name = $result['name'];
					$aux_character->origin = $result['origin']['name'];
					$aux_character->url = $result['url'];
					$characters->push($aux_character); //Guardo los datos en la coleccion
				}
			}
			for($i = 2;$i<=$pages; $i++ ){
				$page = $this->sendRequest($this->character.'?page='.$i); //Recorro el resto de las paginas.
				if($page['results']){
					foreach($page['results'] as $result){ //Recorro los resultados por pagina y los guardo en una coleccion
						$aux_character = collect();
						$aux_character->id = $result['id'];
						$aux_character->name = $result['name'];
						$aux_character->origin = $result['origin']['name'];
						$aux_character->url = $result['url'];
						$characters->push($aux_character); //Guardo los datos en la coleccion
					}
				}
			}	
		}
		return $characters;		
	}

	
	/*
	Descripcion: Esta funcion realiza la llamada a la api mediante GET.
	Input: $api_url
	Output: $response
	*/
	private function sendRequest($api_url) 
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $api_url); 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
		curl_setopt($ch, CURLOPT_HEADER, 0); 
		$response = curl_exec($ch); 
		$errorMsg = curl_error($ch);
		curl_close($ch);
		
		//$response = file_get_contents($api_url);
		$response = json_decode($response, true);
		
		if (!$response) {
			return false;
		}
		return $response;
	}
}
