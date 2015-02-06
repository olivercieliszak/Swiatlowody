<table align="center">
<tr>
<td colspan="9"><h3>Lista relacji</h3></td>
</tr>
<tr>
<td><b>ID</b></td>
<td><b>Opis</b></td>
<td><b>Początek</b></td>
<td><b>Początek<br>opis</b></td>
<td><b>Koniec</b></td>
<td><b>Koniec<br>opis</b></td>
<td><b>Suma<br>włókien</b></td>
<td><b>Długość</b></td>
<td><b><a href="?modul=relacje&co=dodajRelacje" class="dodaj">Dodaj relację</a></b></td>
</tr>

<?php
$zapytanie = $FP -> db_q('SELECT * FROM relacja ORDER BY relacjaID ASC');
while($wynik = $zapytanie -> fetch_object()){
	$dlugosc = 0;
	$zapytanie2 = $FP -> db_q('SELECT kabelID FROM relacjaWlokno NATURAL LEFT JOIN kabelWlokno WHERE relacjaID = "'.$wynik -> relacjaID.'"');
	while($wynik2 = $zapytanie2 -> fetch_object()){
		
		$dlugosc += $FP -> dlugoscKabla($wynik2 -> kabelID);
	
	}
	echo '
	<tr>
	<td>'.$wynik -> relacjaID.'</td>
	<td>'.$wynik -> opis.'</td>
	<td>'.$FP -> pobierzPunkt($wynik -> punktIDStart).'</td>
	<td>'.$wynik -> opisStart.'</td>
	<td>'.$FP -> pobierzPunkt($wynik -> punktIDKoniec).'</td>
	<td>'.$wynik -> opisKoniec.'</td>
	<td>'.$FP -> db_sq('SELECT COUNT(*) AS ilosc FROM relacjaWlokno WHERE relacjaID = "'.$wynik -> relacjaID.'"') -> ilosc.'</td>
	<td>'.$dlugosc.' m</td>
	<td><a href="?modul=relacje&co=listaWlokien&relacjaID='. $wynik -> relacjaID .'" class="zmien">Włókna</a><br><a href="?modul=relacje&co=listaPunktow&relacjaID='. $wynik -> relacjaID .'" class="zmien">Przebieg</a><br><a href="?modul=relacje&co=zmienRelacje&relacjaID='.$wynik -> relacjaID.'" class="zmien">Zmień</a> | <a href="?modul=relacje&co=usunRelacje&relacjaID='.$wynik -> relacjaID.'" class="usun">Usuń</a></td>
	</tr>';
}
?>
</table>
