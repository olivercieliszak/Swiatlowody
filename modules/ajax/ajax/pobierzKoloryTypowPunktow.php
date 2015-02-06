<?php

	$zapytanie = $FP -> db_q('SELECT punktTypID, kolorPunkt FROM punktTyp ORDER BY punktTypID ASC');
	$return = array();
	while($wynik = $zapytanie -> fetch_object()){
		$return[$wynik -> punktTypID] = $wynik -> kolorPunkt;
	}
	echo json_encode($return);
	

