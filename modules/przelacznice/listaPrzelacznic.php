<table align="center">
<tr>
<td colspan="6"><h3>Lista przełącznic</h3></td>
</tr>
<tr>
<td><b>ID</b></td>
<td><b>Punkt</b></td>
<td><b>Opis</b></td>
<td><b>Ilość podłączonych kabli</b></td>
<td><b>Ilość portów</b></td>
<td><b><a href="?modul=przelacznice&co=mapaPrzelacznic" class="zmien">Mapa przełącznic</a></b> | <b><a href="?modul=przelacznice&co=dodajPrzelacznice" class="dodaj">Dodaj przełącznice</a></b></td>
</tr>

<?php
$zapytanie = $FP -> db_q('SELECT * FROM przelacznica ORDER BY przelacznicaID ASC');
while($wynik = $zapytanie -> fetch_object()){
$kableWPrzelacznicy = $FP -> db_sq('SELECT COUNT(*) AS ilosc FROM kabel WHERE punktIDStart = "'.$wynik -> punktID.'" OR punktIDKoniec = "'.$wynik -> punktID.'"') -> ilosc;

//liczenie ilosci kabli w mufie na podstawie ilosci spawow
$iloscPortow = $FP -> db_sq('SELECT COUNT(*) as ilosc FROM przelacznicaPort WHERE przelacznicaID = "'.$wynik -> przelacznicaID .'"') -> ilosc;

echo '
<tr>
<td>'.$wynik -> przelacznicaID.'</td>
<td>'.$FP -> pobierzSkroconyPunkt($wynik -> punktID).'</td>
<td>'.$wynik -> opis.'</td>
<td>'.$kableWPrzelacznicy.' | <a href="?modul=przelacznice&co=dodajKabel&przelacznicaID='.$wynik -> przelacznicaID.'" class="dodaj">Dodaj</a></td>
<td>'.$iloscPortow.'</td>
<td><a href="?modul=przelacznice&co=listaPortow&przelacznicaID='.$wynik -> przelacznicaID.'" class="zmien">Porty</a> | <a href="?modul=przelacznice&co=zmienPrzelacznice&przelacznicaID='.$wynik -> przelacznicaID.'" class="zmien">Zmień</a> | <a href="?modul=przelacznice&co=usunPrzelacznice&przelacznicaID='.$wynik -> przelacznicaID.'" class="usun">Usuń</a></td>
</tr>';
}
?>
</table>
