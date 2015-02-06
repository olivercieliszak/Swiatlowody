<?php
$wynik = $FP -> db_sq('SELECT * FROM przelacznica WHERE przelacznicaID = "'.(int)$_R -> przelacznicaID.'"');

if(isset($_P -> przelacznicaID)){
	if(!empty($_P -> przelacznicaID)){
	$zapytanie = $FP -> db_q('UPDATE przelacznica SET opis = "'.$_P -> opis.'" WHERE przelacznicaID = "'.$_P -> przelacznicaID.'"');
	echo $FP -> komunikat($zapytanie, 'Przełącznica nr '.$_P -> przelacznicaID.' w punkcie <i>'.$FP -> pobierzPunkt($wynik -> punktID).'</i> została zmieniona prawidłowo<br><br><a href="?modul=przelacznice&co=listaPrzelacznic">Powrót do listy przełącznic</a>',
	'Wystąpił błąd podczas zmiany przełącznicy<br><br><a href="?modul=przelacznice&co=listaPrzelacznic">Powrót do listy przełącznic</a>');
	if($zapytanie)
		$FP -> log('Zmieniono przełącznicę nr '.$_P -> przelacznicaID);
	}
}
else{


?>
<form action="?modul=przelacznice&co=zmienPrzelacznice" method="POST">
<table align="center">
<tr>
<td colspan="2"><h3>Zmiana przełącznicy</h3></td>
</tr>
<tr>
<td>ID</td>
<td><?php echo $wynik -> przelacznicaID ?><input type="hidden" name="przelacznicaID" value="<?php echo $wynik -> przelacznicaID ?>"></td>
</tr>
<tr>
<td>Punkt</td>
<td><?php echo $FP -> pobierzPunkt($wynik -> punktID); ?></td>
</tr>
<tr>
<td>Opis</td>
<td><input type="text" name="opis" size="30" value="<?php echo $wynik -> opis ?>"></td>
</tr>
<tr>
<td colspan="2"><input type="submit" value="Zmień!"></td>
</tr>

</table>
</form>
<?php
}
?>