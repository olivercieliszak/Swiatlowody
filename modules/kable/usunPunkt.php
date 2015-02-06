<?php
$wynik = $FP -> db_sq('SELECT * FROM kabelPunkt WHERE kabelPunktID = "'.(int)$_R -> kabelPunktID.'"');

if(isset($_R -> kabelPunktID) && $_R -> kabelPunktID > 0){
	if(!empty($_P -> kabelPunktID)){
	$zapytanieDel = $FP -> db_q('DELETE FROM kabelPunkt WHERE kabelPunktID = "'.$_P -> kabelPunktID.'"');

	$zapytanie = $FP -> db_q('SELECT kabelPunktID FROM kabelPunkt WHERE kabelID = "'.$wynik -> kabelID.'" ORDER BY kolejnosc ASC'); 
	$kolejnosc = 1;
	while($wynik = $zapytanie -> fetch_object()){
		
		$FP -> db_q('UPDATE kabelPunkt SET kolejnosc = "'.$kolejnosc++.'" WHERE kabelPunktID = "'.$wynik -> kabelPunktID.'"');
		
	}
	echo $FP -> komunikat($zapytanieDel, 'Punkt przebiegu kabla <i>'.$FP -> pobierzRelacjeKabla($wynik -> kabelID).'</i> został usunięty
	<br><br><a href="?modul=kable&co=listaPunktow&kabelID='.$wynik -> kabelID.'">Powrót do listy przebiegu kablu</a>',
	'Wystąpił błąd podczas usuwania punktu przebiegu kabla <i>'.$FP -> pobierzRelacjeKabla($wynik -> kabelID).'</i>');
	if($zapytanieDel)
		$FP -> log('Usunięto punkt z przebiegu kabla nr '.$wynik -> kabelID);
	}

else{
$punkt = $FP -> db_sq('SELECT gps, opis, punktTyp.typ FROM punkt LEFT JOIN punktTyp ON punkt.punktTypID = punktTyp.punktTypID WHERE punktID = "'. $wynik -> punktID .'"');

?>
<form action="?modul=kable&co=usunPunkt" method="POST">
<input type="hidden" name="kabelPunktID" value="<?php echo $wynik -> kabelPunktID ?>">
<table align="center">
<tr>
<td colspan="2"><h3>Usuwanie punktu z przebiegu kabla <?php echo $FP -> pobierzRelacjeKabla($wynik -> kabelID) ?></h3>
<div class="usun"><b>Czy na pewno chcesz usunąć ten punkt z przebiegu kabla?</b></div></td>
</tr>
<tr>
<td>Punkt</td>
<td><?php echo $punkt -> typ .' <b>'.$punkt -> opis .'</b> (<i>'.$wynik -> punktID.'</i>)' ?></td>
</tr>
<tr>
<td colspan="2"><input type="submit" value="Usuń" class="usun"></td>
</tr>
</table>
</form>
<?php
}
}
?>