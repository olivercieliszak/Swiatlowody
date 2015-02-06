<?php
$wynik = $FP -> db_sq('SELECT * FROM przelacznicaPort WHERE przelacznicaPortID = "'.(int)$_R -> przelacznicaPortID.'"');

if(isset($_P -> przelacznicaPortID)){
	if(!empty($_P -> przelacznicaPortID)){
			$zapytanie = $FP -> db_q('UPDATE przelacznicaPort SET port = "'.$_R -> port.'", typ = "'.$_R -> typ.'" WHERE przelacznicaPortID = "'.$_P -> przelacznicaPortID.'"');
			echo $FP -> komunikat($zapytanie, 'Port nr <i>'.$_P -> port.'</i> został prawidłowo zmieniony.<br><br><a href="?modul=przelacznice&co=listaPortow&przelacznicaID='.$wynik -> przelacznicaID.'">Powrót do listy portów</a>', 'Wystąpił błąd podczas zmiany portu nr <i>'.$_P -> port.'</i>');
			if($zapytanie)
				$FP -> log('Port '.$wynik -> typ.' nr '.$wynik -> port.' został zmieniony na '.$_P -> typ.' nr '.$_P -> port.' w przełącznicy nr '.$wynik -> przelacznicaID);
	}
}

else{

$wlokno = $FP -> db_sq('SELECT kolorTubaID, kolorWloknoID FROM kabelWlokno WHERE kabelWloknoID = "'.$wynik -> kabelWloknoID.'"');

$tuba = $FP -> kolor('tuba',$wlokno -> kolorTubaID);
$wlokno = $FP -> kolor('wlokno',$wlokno -> kolorWloknoID);

?>
<form action="?modul=przelacznice&co=zmienPort" method="POST">
<input type="hidden" name="przelacznicaPortID" value="<?php echo $_R -> przelacznicaPortID ?>">
<table align="center">
<tr>
<td colspan="2"><h3>Zmiana portu</h3>Zmiana włókien w porcie jest możliwa<br>tylko poprzez usunięcie i dodanie nowego portu</td>
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
<td><input type="text" name="port" value="<?php echo $wynik -> port ?>"></td>
</tr><tr>
<td><b>Typ portu</b></td>
<td><input type="text" name="typ" value="<?php echo $wynik -> typ ?>"></td>
</tr>
<tr>
<td colspan="2"><input type="submit" value="Zmień!"></td>
</tr>

</table>
</form>
<?php
}
?>