<?php

if(isset($_R -> opis)){
	$wykonajKopie = $FP -> backup_tables('./backup/', $_R -> opis);
	if(!empty($wykonajKopie)){

		echo $FP -> komunikat(true, 'Kopia zapasowa - '.$_R -> opis.' - została wykonana prawidłowo.<br>Wersja: '.$wykonajKopie);

	}
	include('kopieZapasowe.php');

}
?>