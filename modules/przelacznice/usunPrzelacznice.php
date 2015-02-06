<?php
$wynik = $FP -> db_sq('SELECT * FROM przelacznica WHERE przelacznicaID = "'.(int)$_R -> przelacznicaID.'"');

if(isset($_P -> przelacznicaID)){
	if(!empty($_P -> przelacznicaID)){
		if($FP -> db_sq('SELECT count(*) as ilosc FROM relacja WHERE  punktIDStart = "'.$wynik -> punktID.'" OR punktIDKoniec = "'.$wynik -> punktID.'"') -> ilosc > 0)
			echo $FP -> komunikat(false, false, 'Nie można usunąć punktu używanego w relacji.<br>Najpierw usuń punkt z relacji a dopiero potem sam punkt');
		else if($FP -> db_sq('SELECT count(*) as ilosc FROM kabelPunkt WHERE punktID = "'.$wynik -> punktID.'"') -> ilosc > 0)
			echo $FP -> komunikat(false, false, 'Nie można usunąć punktu używanego jako przebieg kabla.<br>Najpierw usuń punkt z przebiegu kabla a dopiero potem sam punkt');
		else if($FP -> db_sq('SELECT count(*) as ilosc FROM kabel WHERE punktIDStart = "'.$wynik -> punktID.'" OR punktIDKoniec = "'.$wynik -> punktID.'"') -> ilosc > 0)
			echo $FP -> komunikat(false, false, 'Nie można usunąć punktu używanego w kablu.<br>Najpierw usuń punkt z kabla a dopiero potem sam punkt');

		else if($FP -> db_sq('SELECT count(*) as ilosc FROM przelacznicaPort WHERE przelacznicaID = "'.$_R -> przelacznicaID.'"') -> ilosc > 0)
			echo $FP -> komunikat(false, false, 'Nie można usunąć przełącznicy w której są porty');
		//nie mozna usunac przelacznicy ktora ma podlaczone kable
		else if($FP -> db_sq('SELECT COUNT(*) AS ilosc FROM kabel WHERE punktIDStart = "'.$wynik -> punktID.'" OR punktIDKoniec = "'.$wynik -> punktID.'"') -> ilosc > 0)
			echo $FP -> komunikat(false, false, 'Nie można usunąć przełącznicy do której podłączone są kable');
		else{
			$zapytanie = $FP -> db_q('DELETE FROM przelacznica WHERE przelacznicaID = "'.$_P -> przelacznicaID.'"');
			$zapytanie2 = $FP -> db_q('DELETE FROM punkt WHERE punktID = "'.$wynik -> punktID.'"');

			//$zapytanie2 = $FP -> db_q('DELETE FROM przelacznicaPort WHERE przelacznicaID = "'.$_P -> przelacznicaID.'"');
			echo $FP -> komunikat($zapytanie, 'Przełącznica nr <i>'.$_P -> przelacznicaID.'</i> została prawidłowo usunięta.<br><br><a href="?modul=przelacznice&co=listaPrzelacznic">Powrót do listy przełącznic</a>', 'Wystąpił błąd podczas usuwania przełącznicy nr <i>'.$_P -> przelacznicaID.'</i>');
			if($zapytanie)
				$FP -> log('Usunięto przełącznicę nr '.$_P -> przelacznicaID);
		}
	}
}

else{
?>
<form action="?modul=przelacznice&co=usunPrzelacznice" method="POST">
<table align="center">
<tr>
<td colspan="2"><h3>Usuwanie przełącznicy</h3>
<div class="usun"><b>Czy na pewno chcesz usunąć tę przełącznicę?</b></div></td>
</tr>
<tr>
<td>ID</td>
<td><?php echo $wynik -> przelacznicaID ?><input type="hidden" name="przelacznicaID" value="<?php echo $wynik -> przelacznicaID ?>"></td>
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