<?php
$wynik = $FP -> db_sq('SELECT * FROM mufaSpaw WHERE mufaSpawID = "'.(int)$_R -> mufaSpawID.'"');

if(isset($_P -> mufaSpawID)){
	if(!empty($_P -> mufaSpawID)){
			$zapytanie = $FP -> db_q('UPDATE mufaSpaw SET opis = "'.$_R -> opis.'" WHERE mufaSpawID = "'.$_P -> mufaSpawID.'"');
			echo $FP -> komunikat($zapytanie, 'Spaw nr <i>'.$_P -> mufaSpawID.'</i> został prawidłowo zmieniony.<br><br><a href="?modul=mufy&co=listaSpawow&mufaID='.$wynik -> mufaID.'">Powrót do listy spawów</a>', 'Wystąpił błąd podczas zmiany spawu nr <i>'.$wynik -> mufaSpawID.'</i>');
			if($zapytanie)
				$FP -> log('Zmieniono spaw włókien nr '.$wynik -> kabelWloknoID1.'/'.$wynik -> kabelID1.' i '.$wynik -> kabelWloknoID2.'/'.$wynik -> kabelID2.' w mufie nr '.$wynik -> mufaID);

	}
}

else{

$wlokno1 = $FP -> db_sq('SELECT kolorTubaID, kolorWloknoID FROM kabelWlokno WHERE kabelWloknoID = "'.$wynik -> kabelWloknoID1.'"');
$wlokno2 = $FP -> db_sq('SELECT kolorTubaID, kolorWloknoID FROM kabelWlokno WHERE kabelWloknoID = "'.$wynik -> kabelWloknoID2.'"');

$tuba1 = $FP -> kolor('tuba',$wlokno1 -> kolorTubaID);
$wlokno1 = $FP -> kolor('wlokno',$wlokno1 -> kolorWloknoID);
$tuba2 = $FP -> kolor('tuba',$wlokno2 -> kolorTubaID);
$wlokno2 = $FP -> kolor('wlokno',$wlokno2 -> kolorWloknoID);

?>
<form action="?modul=mufy&co=ZmienSpaw" method="POST">
<input type="hidden" name="mufaSpawID" value="<?php echo $_R -> mufaSpawID ?>">
<table align="center">
<tr>
<td colspan="2"><h3>Zmiana spawu</h3>Zmiana włókien w spawie jest możliwa<br>tylko poprzez usunięcie i dodanie nowego spawu</td>
</tr>
<tr>
<td>ID</td>
<td><?php echo $wynik -> mufaSpawID ?><input type="hidden" name="mufaSpawID" value="<?php echo $wynik -> mufaSpawID ?>"></td>
</tr>
<td>Kabel A</td>
<td><b><?php echo $FP -> pobierzRelacjeKabla($wynik -> kabelID1); ?></b></td>
</tr>
<tr>
<td>Tuba A</td>
<td style="background-color: <?php echo $tuba1 -> kolorHTML ?>;border: 1px solid #656565; color: <?php echo $FP -> znajdzKolor($tuba1 -> kolorHTML) ?>"><?php echo $tuba1 -> kolor ?></td>
</tr>
<tr>
<td>Włókno A</td>
<td style="background-color: <?php echo $wlokno1 -> kolorHTML ?>;border: 1px solid #656565; color: <?php echo $FP -> znajdzKolor($wlokno1 -> kolorHTML) ?>"><?php echo $wlokno1 -> kolor ?></td>
</tr>
<tr>
<td colspan="2"><br></td>
</tr>
<td>Kabel B</td>
<td><b><?php echo $FP -> pobierzRelacjeKabla($wynik -> kabelID2); ?></b></td>
</tr>
<tr>
<td>Tuba B</td>
<td style="background-color: <?php echo $tuba2 -> kolorHTML ?>;border: 1px solid #656565; color: <?php echo $FP -> znajdzKolor($tuba2 -> kolorHTML) ?>"><?php echo $tuba2 -> kolor ?></td>
</tr>
<tr>
<td>Włókno B</td>
<td style="background-color: <?php echo $wlokno2 -> kolorHTML ?>;border: 1px solid #656565; color: <?php echo $FP -> znajdzKolor($wlokno2 -> kolorHTML) ?>"><?php echo $wlokno2 -> kolor ?></td>
</tr>
<tr>
<td colspan="2"><br></td>
</tr>
<tr>
<td><b>Opis</b></td>
<td><input type="text" name="opis" value="<?php echo $wynik -> opis ?>"></td>
</tr>
<tr>
<td colspan="2"><input type="submit" value="Zmień!"></td>
</tr>

</table>
</form>
<?php
}
?>