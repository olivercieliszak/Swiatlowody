<?php

if($_G -> kabelID > 0 && $_G -> kolorTubaID && $_G -> mufaID > 0){

	$kabelWloknoIDUzywany = array();
	$zapytanie = $FP -> db_q('SELECT kabelWloknoID1, kabelWloknoID2 FROM mufaSpaw WHERE (kabelID1 = "'.$_G -> kabelID.'" OR kabelID2 = "'.$_G -> kabelID.'") AND mufaID = "'.$_G -> mufaID.'"');
	$mufaID = array();
	while($wynik = $zapytanie -> fetch_object()){
		$kabelWloknoIDUzywany[$wynik -> kabelWloknoID1] = true;	
		$kabelWloknoIDUzywany[$wynik -> kabelWloknoID2] = true;	
	}
	$zapytanie = $FP -> db_q('SELECT kabelWloknoID, kolorWloknoID FROM kabelWlokno WHERE kabelID = "'.$_G -> kabelID.'" AND kolorTubaID = "'.$_G -> kolorTubaID.'" ORDER BY kolorWloknoID ASC');
	$return = array();
	while($wynik = $zapytanie -> fetch_object()){
		if(!array_key_exists($wynik -> kabelWloknoID,$kabelWloknoIDUzywany)){
			$kolor = $FP -> kolor('wlokno', $wynik -> kolorWloknoID);
			$return[$wynik -> kolorWloknoID]['kabelWloknoID'] = $wynik -> kabelWloknoID;
			$return[$wynik -> kolorWloknoID]['kolor'] = $kolor -> kolor;
			$return[$wynik -> kolorWloknoID]['kolorHTML'] = $kolor -> kolorHTML;
		}
	}
	echo json_encode($return);
	}

