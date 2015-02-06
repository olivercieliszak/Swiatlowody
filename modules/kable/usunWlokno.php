<?php
$wynik = $FP -> db_sq('SELECT * FROM kabelWlokno WHERE kabelWloknoID = "'.(int)$_R -> kabelWloknoID.'"');
if(isset($_P -> kabelWloknoID)){
	if($FP -> db_sq('SELECT count(*) as ilosc FROM mufaSpaw WHERE kabelWloknoID1 = "'.(int)$_R -> kabelWloknoID.'" OR kabelWloknoID2 = "'.(int)$_R -> kabelWloknoID.'"') -> ilosc > 0)
		echo $FP -> komunikat(false, false, 'Nie można usunąć włókna, które zostało zaspawane.<br>Najpierw usuń spaw a dopiero potem samo włókno');
	else if($FP -> db_sq('SELECT count(*) as ilosc FROM relacjaWlokno WHERE kabelWloknoID = "'.(int)$_R -> kabelWloknoID.'"') -> ilosc > 0)
		echo $FP -> komunikat(false, false, 'Nie można usunąć włókna, które jest częścią relacji<br>Najpierw usuń włókno z relacji a dopiero potem samo włókno');
	
	else if(!empty($_P -> kabelWloknoID)){
	$usunWlokno = $FP -> db_q('DELETE FROM kabelWlokno WHERE kabelWloknoID = "'.$_P -> kabelWloknoID.'"');
	
	echo $FP -> komunikat($usunWlokno,
	'Włókno nr <i>'.$_P -> kabelWloknoID.'</i> w kablu w relacji<br><i>'. $FP -> pobierzRelacjeKabla($wynik -> kabelID) .'</i><br>zostało prawidłowo usunięte.<br><br><a href="?modul=kable&co=listaWlokien&kabelID='.$wynik -> kabelID.'">Powrót do listy włókien w kablu nr '.$wynik -> kabelID.'</a>',
	'Włókno nr <i>'.$_P -> kabelWloknoID.'</i> w kablu w relacji <i>'. $FP -> pobierzRelacjeKabla($wynik -> kabelID) .'</i> nie zostało prawidłowo usunięte.<br><br><a href="?modul=kable&co=listaWlokien&kabelID='.$wynik -> kabelID.'">Powrót do listy włókien w kablu nr '.$wynik -> kabelID.'</a>');
	if($usunWlokno)
		$FP -> log('Usunięto włókno z kabla nr '.$wynik -> kabelID);
	}
}

else{
?>
<form action="?modul=kable&co=usunWlokno" method="POST">
<table align="center">
<tr>
<td colspan="2"><h3>Usuwanie włókna</h3><div class="usun"><b>Czy na pewno chcesz usunąć to włókno?</b></div></td>
</tr>
<?php
$tuba = $FP -> kolor('tuba',$wynik -> kolorTubaID);
$wlokno = $FP -> kolor('wlokno',$wynik -> kolorWloknoID);
?>
<tr>
<td>ID</td>
<td><?php echo $wynik -> kabelWloknoID ?><input type="hidden" name="kabelWloknoID" value="<?php echo $wynik -> kabelWloknoID ?>"></td>
</tr>
<td>Kabel</td>
<td><b><?php echo $FP -> pobierzRelacjeKabla($wynik -> kabelID); ?></b></td>
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
<td colspan="2"><input type="submit" value="Usuń" class="usun"></td>
</tr>

</table>
</form>
<?php
}
?>