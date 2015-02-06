<script>
$(document).ready(function() 
    { 
        $("#sortuj").tablesorter({sortList: [[0,0]], headers: { 3: {sorter: false} }}).bind("sortEnd",function() { 
		pokoloruj_wiersze()
		});
    } 
); 
   
</script>
<table align="center" id="sortuj">
<thead>
<tr>
<td colspan="5"><h3>Lista punktów</h3></td>
</tr>
<tr style="cursor: pointer">
<th><b>ID</b></th>
<th><b>Opis i adres</b></th>
<th><b>Typ</b></th>
<th><b><a href="?modul=punkty&co=mapaPunktow" class="zmien">Mapa punktów</a></b> | <b><a href="?modul=punkty&co=dodajPunkt" class="dodaj">Dodaj punkt</a></b></th>
</tr>
</thead>
<tbody>

<?php
$zapytanie = $FP -> db_q('SELECT punktID, gps, opis, kolorPunkt, punktTyp.typ FROM punkt LEFT JOIN punktTyp ON punkt.punktTypID = punktTyp.punktTypID ORDER BY punktID ASC');
while($wynik = $zapytanie -> fetch_object()){
echo '
<tr>
<td>'.$wynik -> punktID.'</td>
<td>'.$wynik -> opis.'</td>
<td style="background-color: '.$FP -> koloryPunktowTlo($wynik -> kolorPunkt).'; color: '.$FP -> koloryPunktowTekst($wynik -> kolorPunkt).'">'.$wynik -> typ.'</td>
<td><a href="?modul=punkty&co=listaObiektow&punktID='.$wynik -> punktID.'" class="zmien">Obiekty</a> | <a href="?modul=punkty&co=zmienPunkt&punktID='.$wynik -> punktID.'" class="zmien">Zmień</a> | <a href="?modul=punkty&co=usunPunkt&punktID='.$wynik -> punktID.'" class="usun">Usuń</a></td>
</tr>';
}
?>
</tbody>
</table>
