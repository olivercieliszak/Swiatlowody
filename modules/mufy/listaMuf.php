<table align="center">
<tr>
<td colspan="6"><h3>Lista muf</h3></td>
</tr>
<tr>
<td><b>ID</b></td>
<td><b>Punkt</b></td>
<td><b>Opis</b></td>
<td><b>Ilość podłączonych kabli</b></td>
<td><b>Ilość spawów</b></td>
<td><b><a href="?modul=mufy&co=mapaMuf" class="zmien">Mapa muf</a></b> | <b><a href="?modul=mufy&co=dodajMufe" class="dodaj">Dodaj mufę</a></b></td>
</tr>

<?php
$zapytanie = $FP -> db_q('SELECT mufaID, opis, punktID FROM mufa ORDER BY mufaID ASC');
while($wynik = $zapytanie -> fetch_object()){
//liczenie ilosci kabli w mufie na podstawie ilosci spawow
$kableWMufie = array();
//$kableWMufie = $FP -> db_q('SELECT DISTINCT kabelWlokno.kabelID as kabelID FROM mufaSpaw LEFT JOIN kabelWlokno ON mufaSpaw.kabelWloknoID1 = kabelWlokno.kabelWloknoID') -> fetch_array();
$kableWMufie =  $FP -> db_sq('SELECT COUNT(*) AS ilosc FROM kabel WHERE punktIDStart = "'.$wynik -> punktID.'" OR punktIDKoniec = "'.$wynik -> punktID.'"') -> ilosc;
@$spawyWMufie = $FP -> db_sq('SELECT COUNT(kabelWloknoID1) as ilosc FROM mufaSpaw WHERE mufaID = "'.$wynik -> mufaID .'"') -> ilosc;

echo '
<tr>
<td>'.$wynik -> mufaID.'</td>
<td>'.$FP -> pobierzSkroconyPunkt($wynik -> punktID).'</td>
<td>'.$wynik -> opis.'</td>
<td>'.$kableWMufie.' | <a href="?modul=mufy&co=dodajKabel&mufaID='.$wynik -> mufaID.'" class="dodaj">Dodaj</a></td>
<td>'.(int)$spawyWMufie.'</td>
<td><a href="?modul=mufy&co=listaSpawow&mufaID='.$wynik -> mufaID.'" class="zmien">Spawy</a> | <a href="?modul=mufy&co=zmienMufe&mufaID='.$wynik -> mufaID.'" class="zmien">Zmień</a> | <a href="?modul=mufy&co=usunMufe&mufaID='.$wynik -> mufaID.'" class="usun">Usuń</a></td>
</tr>';
}
?>
</table>
