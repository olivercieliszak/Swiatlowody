<?php
$wynik = $FP -> db_sq('SELECT * FROM mufa WHERE mufaID = "'.(int)$_R -> mufaID.'"');

if(isset($_P -> mufaID)){
	if(!empty($_P -> mufaID)){
		if($FP -> db_sq('SELECT count(*) as ilosc FROM relacja WHERE  punktIDStart = "'.$wynik -> punktID.'" OR punktIDKoniec = "'.$wynik -> punktID.'"') -> ilosc > 0)
			echo $FP -> komunikat(false, false, 'Nie można usunąć punktu używanego w relacji.<br>Najpierw usuń punkt z relacji a dopiero potem sam punkt');
		else if($FP -> db_sq('SELECT count(*) as ilosc FROM kabelPunkt WHERE punktID = "'.$wynik -> punktID.'"') -> ilosc > 0)
			echo $FP -> komunikat(false, false, 'Nie można usunąć punktu używanego jako przebieg kabla.<br>Najpierw usuń punkt z przebiegu kabla a dopiero potem sam punkt');
		else if($FP -> db_sq('SELECT count(*) as ilosc FROM kabel WHERE punktIDStart = "'.$wynik -> punktID.'" OR punktIDKoniec = "'.$wynik -> punktID.'"') -> ilosc > 0)
			echo $FP -> komunikat(false, false, 'Nie można usunąć punktu używanego w kablu.<br>Najpierw usuń punkt z kabla a dopiero potem sam punkt');	
		else if($FP -> db_sq('SELECT COUNT(*) AS ilosc FROM kabel WHERE punktIDStart = "'.$wynik -> punktID.'" OR punktIDKoniec = "'.$wynik -> punktID.'"') -> ilosc > 0)
			echo $FP -> komunikat(false, false, 'Nie można usunąć mufy w której są podłączone kable');
		else{
			$zapytanie = $FP -> db_q('DELETE FROM mufa WHERE mufaID = "'.$_P -> mufaID.'"');
			$zapytanie2 = $FP -> db_q('DELETE FROM punkt WHERE punktID = "'.$wynik -> punktID.'"');
			echo $FP -> komunikat($zapytanie, 'Mufa nr <i>'.$_P -> mufaID.'</i> została prawidłowo usunięta.<br><br><a href="?modul=mufy&co=listaMuf">Powrót do listy muf</a>', 'Wystąpił błąd podczas usuwania mufy nr <i>'.$_P -> mufaID.'</i>');
			if($zapytanie)
				$FP -> log('Usunięto mufę nr '.$_P -> mufaID.' - '.$wynik -> opis .' z punktu nr '.$wynik -> punktID);
		}
	}
}

else{
?>
<form action="?modul=mufy&co=usunMufe" method="POST">
<table align="center">
<tr>
<td colspan="2"><h3>Usuwanie mufy</h3>
<div class="usun"><b>Czy na pewno chcesz usunąć tą mufę?</b></div></td>
</tr>
<tr>
<td>ID</td>
<td><?php echo $wynik -> mufaID ?><input type="hidden" name="mufaID" value="<?php echo $wynik -> mufaID ?>"></td>
</tr>
<td>Punkt</td>
<td><?php echo $FP -> pobierzPunkt($wynik -> punktID) ?></td>
</tr>
<tr>
<td>Opis</td>
<td><?php echo $wynik -> opis ?></td>
</tr>
<tr>
<td colspan="2"><input type="submit" value="Usuń" class="usun"></td>
</tr>

</table>
</form>
<?php
}
?>