<?php 

if($_G -> kabelID > 0){
	$zapytanie = $FP -> db_q('SELECT DISTINCT kolorTubaID FROM kabelWlokno WHERE kabelID = "'.$_G -> kabelID.'" ORDER BY kolorTubaID ASC');
	$return = array();
	while($wynik = $zapytanie -> fetch_object()){
		$kolor = $FP -> kolor('tuba', $wynik -> kolorTubaID);
		$return[$wynik -> kolorTubaID]['kolor'] = $kolor -> kolor;
		$return[$wynik -> kolorTubaID]['kolorHTML'] = $kolor -> kolorHTML;
	}
	echo json_encode($return);
}