<?php
$wynik = $FP -> db_sq('SELECT * FROM przelacznicaPort WHERE przelacznicaPortID = "'.(int)$_R -> przelacznicaPortID.'"');
 if(isset($_P -> przelacznicaPortID)){
	$relacja = $FP -> pobierzRelacjeLogicznaWlokna($wynik -> kabelWloknoID);
	if(!empty( $relacja ))	
		echo $FP -> komunikat(false, false, 'Nie możesz usunąć portu który jest używany w relacji.<br>Usuń najpierw włókna z relacji a następnie usuń port z przełącznicy');

	else if(!empty($_P -> przelacznicaPortID)){
			$zapytanie = $FP -> db_q('DELETE FROM przelacznicaPort WHERE przelacznicaPortID = "'.$_P -> przelacznicaPortID.'"');
			echo $FP -> komunikat($zapytanie, 'Port nr <i>'.$wynik -> port.'</i> został prawidłowo usunięty.<br><br><a href="?modul=przelacznice&co=listaPortow&przelacznicaID='.$wynik -> przelacznicaID.'">Powrót do listy portów</a>', 'Wystąpił błąd podczas usuwania portu nr <i>'.$wynik -> port.'</i>');
			if($zapytanie)
				$FP -> log('Port '.$wynik -> typ.' nr <i>'.$wynik -> port.'</i> został usunięty z przełącznicy nr '.$wynik -> przelacznicaID);
	}
}

else{
$wlokno = $FP -> db_sq('SELECT kolorTubaID, kolorWloknoID FROM kabelWlokno WHERE kabelWloknoID = "'.$wynik -> kabelWloknoID.'"');

$tuba = $FP -> kolor('tuba',$wlokno -> kolorTubaID);
$wlokno = $FP -> kolor('wlokno',$wlokno -> kolorWloknoID);

?>
<form action="?modul=przelacznice&co=usunPort" method="POST">
<table align="center">
<tr>
<td colspan="2"><h3>Usuwanie portu</h3>
<div class="usun"><b>Czy na pewno chcesz usunąć ten port?</b></div></td>
</tr>
<tr>
<td>ID</td>
<td><?php echo $wynik -> przelacznicaPortID ?><input type="hidden" name="przelacznicaPortID" value="<?php echo $wynik -> przelacznicaPortID ?>"></td>
</tr>
<td>Kabel</td>
<td><?php echo $FP -> pobierzRelacjeKabla($wynik -> kabelID,1); ?></td>
</tr>
<tr>
<td>Tuba</td>
<td style="background-color: <?php echo $tuba -> kolorHTML ?>;border: 1px solid #656565; color: <?php echo $FP -> znajdzKolor($tuba -> kolorHTML) ?>"><?php echo $tuba -> kolor ?></td>
</tr>
<tr>
<td>Włókno</td>
<td style="background-color: <?php echo $wlokno -> kolorHTML ?>;border: 1px solid #656565; color: <?php echo $FP -> znajdzKolor($wlokno -> kolorHTML) ?>"><?php echo $wlokno -> kolor ?></td>
</tr>
<tr>
<td><b>Nr portu</b></td>
<td><?php echo $wynik -> port ?></td>
</tr><tr>
<td><b>Typ portu</b></td>
<td><?php echo $wynik -> typ ?></td>
</tr>
<tr>
<td colspan="2"><input type="submit" value="Usuń" class="usun"></td>
</tr>

</table>
</form>
<?php
}
?>