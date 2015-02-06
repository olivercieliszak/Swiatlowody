<?php
/*
- wyszukiwanie bez spawow:
1. Wybieramy punkt początkowy - przechodzimy po kolei przez wszystkie kable ktore zaczynaja lub koncza sie w tym 

miejscu
2. sprawdzamy czy w punkcie koncowym sa inne poczatki / konce kabli
3. jezeli sa inne punkty, zapisujemy w pamieci i idziemy do pkt 1. jezeli nie ma, zwracamy trase

- wyszukiwanie bez spawow ale z przecinaniem
1. wybieramy punkt poczatkowy
2. sprawdzamy czy w punktach w relacji kabla znajduja sie inne poczatki / konce kabli
3. jezeli sa - zapisujemy w pamieci i idziemy do punktu pierwszego, jezeli nie ma, zwracamy trase
*/

if(isset($_G -> punktIDStart)){
	
	
	$data['punktIDStart'] = $_G -> punktIDStart;
	$x = $FP -> szukajKoncaKabli($data);
	print_rr($x);

	
}
?>