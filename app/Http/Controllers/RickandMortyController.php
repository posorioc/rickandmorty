<?php

namespace App\Http\Controllers;

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
		$locations = $this->charCounter();

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
		Output: array(count_char, count_locations)
			count_char: Cantidad de caracteres,
			count_episodes: Cantidad de locaciones
		
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
			count_episodes: Cantidad de locaciones
		
	*/
	private function charCounterCharacter($char)
	{
		$characters = $this->sendRequest($this->character);
		$count_characters = 0; //Se usa para validar si se estan contando todas los personajes
		$count_char = 0;  //Se usa para guardar cuantas veces se encuentra un caracter en los nombres de todos los personajes
		if($characters){
			$pages = $characters['info']['pages'];
			for($i = 1;$i<=$pages; $i++ ){
				$data = $this->sendRequest($this->episodes.'?page='.$i);
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

		//echo "respuesta: ".$response."<br>";
		$errorMsg = curl_error($ch);
		//echo "error: ".$errorMsg."<br>";
		curl_close($ch);
	
		$response = json_decode($response, true);
		
		if (!$response) {
			return false;
		}
		return $response;
	}
}
