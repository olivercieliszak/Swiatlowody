<?php 

if($_G -> punktID > 0){
	$zapytanie = $FP -> db_q('SELECT kabelID FROM kabelPunkt WHERE punktID = "'.$_G -> punktID.'" ORDER BY kabelID ASC');
	$return = array();
	while($wynik = $zapytanie -> fetch_object()){
		$return[$wynik -> kabelID] = $FP -> pobierzRelacjeKabla($wynik -> kabelID);
	}
	echo json_encode($return);
}