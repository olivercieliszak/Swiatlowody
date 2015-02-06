<?php

$punktID = array();
$JSdaneORelacji = array();

$zapytanie = $FP -> db_q('SELECT * FROM kabel ORDER BY kabelID ASC');
while($wynik = $zapytanie -> fetch_object()){
	$kabelPunktID = $FP -> db_sq('SELECT punktIDStart, punktIDKoniec FROM kabel WHERE kabelID = "'.$wynik -> kabelID.'"');


	$zapytanie2 = $FP -> db_q('SELECT * FROM kabelPunkt WHERE kabelID = "'.$wynik -> kabelID.'" ORDER BY kolejnosc ASC');
	while($wynik2 = $zapytanie2 -> fetch_object()){
		$punkt = $FP -> db_sq('SELECT gps, opis, punktTyp.typ, punkt.punktTypID FROM punkt LEFT JOIN punktTyp ON punkt.punktTypID = punktTyp.punktTypID WHERE punktID = "'. $wynik2 -> punktID .'"');
		
		
		$punktID[$wynik -> kabelID][$wynik2 -> kolejnosc] = $wynik2 -> punktID;
		$JSdaneORelacji[$wynik -> kabelID][$wynik2 -> kolejnosc]['typ'] = $punkt -> typ;
		$JSdaneORelacji[$wynik -> kabelID][$wynik2 -> kolejnosc]['opis'] = $punkt -> opis;
		$JSdaneORelacji[$wynik -> kabelID][$wynik2 -> kolejnosc]['gps'] = $punkt -> gps;
		$JSdaneORelacji[$wynik -> kabelID][$wynik2 -> kolejnosc]['punktID'] = $wynik2 -> punktID;
		$JSdaneORelacji[$wynik -> kabelID][$wynik2 -> kolejnosc]['punktTypID'] = $punkt -> punktTypID;
		//jeżeli mufa to pobieramy mufaID
		if($punkt -> punktTypID == 1)
			@$JSdaneORelacji[$wynik -> kabelID][$wynik2 -> kolejnosc]['mufaID'] = $FP -> db_sq('SELECT mufaID from mufa WHERE punktID = "'.$wynik2 -> punktID.'"') -> mufaID;
		
		//jeżeli przełącznica to pobieramy przelacznicaID
		else if($punkt -> punktTypID == 2)
			@$JSdaneORelacji[$wynik -> kabelID][$wynik2 -> kolejnosc]['przelacznicaID'] = $FP -> db_sq('SELECT przelacznicaID from przelacznica WHERE punktID = "'.$wynik2 -> punktID.'"') -> przelacznicaID;
		
		$JSdaneORelacji[$wynik -> kabelID][$wynik2 -> kolejnosc]['kabelPunktID'] = $wynik2 -> kabelPunktID;
		$JSdaneORelacji[$wynik -> kabelID]['opis'] = 'Kabel '.$FP -> pobierzRelacjeKabla($wynik -> kabelID,1);
		$JSdaneORelacji[$wynik -> kabelID]['iloscWlokien'] = $FP -> db_sq('SELECT COUNT(*) as ilosc FROM kabelWlokno WHERE kabelID = "'.$wynik -> kabelID.'"') -> ilosc;
		
	}

}
?>

<?php echo 'var PHPdaneORelacji = '.json_encode($JSdaneORelacji).';'; ?>
