<?php

if($_G -> kabelID > 0 && $_G -> kolorTubaID){
	
	$mufaID = array();
	$kabelWloknoIDUzywany = array();

	if(isset($_G -> przelacznicaID)){
		$zapytanie = $FP -> db_q('SELECT kabelWloknoID FROM przelacznicaPort WHERE kabelID = "'.$_G -> kabelID.'" AND przelacznicaID = "'.$_G -> przelacznicaID.'"');
		while($wynik = $zapytanie -> fetch_object()){
			$kabelWloknoIDUzywany[$wynik -> kabelWloknoID] = true;	
		}
	}
	$zapytanie = $FP -> db_q('SELECT kabelWloknoID, kolorWloknoID FROM kabelWlokno WHERE kabelID = "'.$_G -> kabelID.'" AND kolorTubaID = "'.$_G -> kolorTubaID.'" ORDER BY kolorWloknoID ASC');
	$return = array();
	while($wynik = $zapytanie -> fetch_object()){
		if(!array_key_exists($wynik -> kabelWloknoID,$kabelWloknoIDUzywany)){
			$kolor = $FP -> kolor('wlokno', $wynik -> kolorWloknoID);
			$return[$wynik -> kolorWloknoID]['kabelWloknoID'] = $wynik -> kabelWloknoID;
			$return[$wynik -> kolorWloknoID]['kolor'] = $kolor -> kolor;
			$return[$wynik -> kolorWloknoID]['kolorHTML'] = $kolor -> kolorHTML;
			$return[$wynik -> kolorWloknoID]['relacja'] = $FP -> pobierzRelacjeLogicznaWlokna($wynik -> kabelWloknoID);
			$polaczoneWlokno = $FP -> db_sq('SELECT COUNT(*) as ilosc, mufaID from mufa NATURAL RIGHT JOIN mufaSpaw WHERE kabelWloknoID1 = "'.$wynik -> kabelWloknoID.'" OR kabelWloknoID2 = "'.$wynik -> kabelWloknoID.'"');

			if($polaczoneWlokno -> ilosc > 0){
				$return[$wynik -> kolorWloknoID]['dalszaRelacja'] = 'Włókno zaspawane w mufie nr '.$polaczoneWlokno -> mufaID;
			}

		}
	}
	echo json_encode($return);
	}

