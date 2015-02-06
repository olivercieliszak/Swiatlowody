<table align="center">
<tr>
<td colspan="7"><h3>Lista kabli</h3></td>
</tr>
<tr>
<td><b>ID</b></td>
<td><b>Początek</b></td>
<td><b>Koniec</b></td>
<td><b>Długość</b></td>
<td><b>Opis</b></td>
<td><b>Ilość włókien</b></td>
<td nowrap><b><a href="?modul=kable&co=mapaKabli" class="zmien">Mapa kabli</a> | <a href="?modul=kable&co=dodajKabel" class="dodaj">Dodaj kabel</a></b></td>
</tr>

<?php
$zapytanie = $FP -> db_q('SELECT * FROM kabel ORDER BY kabelID ASC');
while($wynik = $zapytanie -> fetch_object()){
	$punktIDStart = $FP -> pobierzPunkt($wynik -> punktIDStart);
	$punktIDKoniec = $FP -> pobierzPunkt($wynik -> punktIDKoniec);

	echo '
	<tr>
	<td>'.$wynik -> kabelID.'</td>
	<td>'.$punktIDStart.'</td>
	<td>'.$punktIDKoniec.'</td>
	<td>'.$FP -> dlugoscKabla($wynik -> kabelID).' m</td>
	<td>'.$wynik -> opis.'</td>
	<td>'.$FP -> db_sq('SELECT COUNT(*) AS ilosc FROM relacjaWlokno NATURAL LEFT JOIN kabelWlokno WHERE kabelWlokno.kabelID = "'.$wynik -> kabelID.'"') -> ilosc .' / '. $FP -> db_sq('SELECT COUNT(*) as iloscZajetychWlokien FROM kabelWlokno WHERE kabelID = "'.$wynik -> kabelID.'"') -> iloscZajetychWlokien.'</td>
	<td><a href="?modul=kable&co=listaWlokien&kabelID='. $wynik -> kabelID .'" class="zmien">Włókna</a> | <a href="?modul=kable&co=listaPunktow&kabelID='. $wynik -> kabelID .'" class="zmien">Przebieg</a><br><a href="?modul=kable&co=zmienKabel&kabelID='.$wynik -> kabelID.'" class="zmien">Zmień</a> | <a href="?modul=kable&co=usunKabel&kabelID='.$wynik -> kabelID.'" class="usun">Usuń</a></td>
	</tr>';
}
?>
</table>
