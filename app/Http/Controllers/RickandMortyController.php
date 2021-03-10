<?php

namespace App\Http\Controllers;
use Illuminate\Support\Collection;


class RickandMortyController extends Controller
{
    private $character;
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
		$this->test();
		//$locations = $this->charCounter();
		
		dd($locations);
	}
	
	public function charCounter(){
		$start_time = microtime(true);
		$locations = $this->charCounterLocations('l');
		echo 'Cuántas veces aparece la letra "l" en los nombres de todos los location: '.$locations['count_char'].'<br>';
		echo 'Cantidad de locaciones '.$locations['count_locations'].'<br>';
		
		$episodes = $this->charCounterEpisode('e');
		echo 'Cuántas veces aparece la letra "e" en los nombres de todos los episode: '.$episodes['count_char'].'<br>';
		echo 'Cantidad de episodios '.$episodes['count_episodes'].'<br>';
		
		$characters = $this->charCounterCharacter('c');
		echo 'Cuántas veces aparece la letra "c" en los nombres de todos los character: '.$characters['count_char'].'<br>';
		echo 'Cantidad de personajes '.$characters['count_characters'].'<br>';
		
		$end_time = microtime(true);
		$time = number_format(($end_time - $start_time), 2);

		echo 'This page loaded in ', $time, ' seconds';
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
		$characters = $this->sendRequest($this->character);	
		$count_characters = 0; //Se usa para validar si se estan contando todas los personajes
		$count_char = 0;  //Se usa para guardar cuantas veces se encuentra un caracter en los nombres de todos los personajes
		if($characters){
			$pages = $characters['info']['pages'];
			for($i = 1;$i<=$pages; $i++ ){
				$data = $this->sendRequest($this->character.'?page='.$i);
				$count_characters = $count_characters + count($data['results']);
				if($data['results']){
					foreach($data['results'] as $result){						
						$count_char = $count_char + substr_count($result['name'],$char);
					}
				}
			}	
		}
		return array('count_char' => $count_char, 'count_characters' => $count_characters);		
	}
	
	public function test(){
		$this->getCharacter();
		$start_time = microtime(true);
		$location = collect(); //Se crea una coleccion para trabajar con los datos
		$location->episode = collect();
		$episodes = $this->sendRequest($this->episodes); //Consulto los episodios
		if($episodes){
			$pages = $episodes['info']['pages']; //Consulto la cantidad de paginas
			for($i = 1;$i<=$pages; $i++ ){
				$data = $this->sendRequest($this->episodes.'?page='.$i); //Consulto los episodios por pagina
				if($data['results']){
					foreach($data['results'] as $result){ //Recorro los episodios por pagina
						$aux_episodio = collect();
						$aux_episodio->id = $result['id'];
						$aux_episodio->name = $result['name'];
						$aux_episodio->origin = collect();
						$aux_location = collect();
						foreach($result['characters'] as $aux){
							$character = $this->sendRequest($aux);
							//$aux_origin = collect();
							//$aux_origin->name = $character['origin']['name'];
							$aux_location->push($character['origin']['name']);	
						}
						$aux_episodio->origin = $aux_location->unique();
						$aux_episodio->count_origin = $aux_episodio->origin->count();
						$location->episode->push($aux_episodio);
					}
				}
			}	
		}
		$end_time = microtime(true);
		$time = number_format(($end_time - $start_time), 2);

		echo 'This page loaded in ', $time, ' seconds';
		dd($location);
	}
	
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
					$characters->push($aux_character);
				}
			}
			for($i = 2;$i<=$pages; $i++ ){
				$data = $this->sendRequest($this->character.'?page='.$i);
				if($data['results']){
					foreach($data['results'] as $result){ //Recorro los resultados por pagina y los guardo en una coleccion
						$aux_character = collect();
						$aux_character->id = $result['id'];
						$aux_character->name = $result['name'];
						$aux_character->origin = $result['origin']['name'];
						$characters->push($aux_character);
					}
				}
			}	
		}
		dd($characters);		
	}
	
	public function test2(){
		$episodes = $this->sendRequest($this->episodes);
	}
	
	/*
		Descripcion: Esta funcion realiza la llamada a la api mediante GET.
		Input: $api_url
		Output: $response
	*/
	private function sendRequest($api_url) 
	{
		
		/*$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $api_url); 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
		curl_setopt($ch, CURLOPT_HEADER, 0); 
		$response = curl_exec($ch); 

		//echo "respuesta: ".$response."<br>";
		$errorMsg = curl_error($ch);
		//echo "error: ".$errorMsg."<br>";
		curl_close($ch);
		*/
		$response = file_get_contents($api_url);
		$response = json_decode($response, true);
		
		if (!$response) {
			return false;
		}
		return $response;
	}
}
