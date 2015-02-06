<table align="center">
<tr>
<td colspan="5"><h3>Historia zdarzeń</h3></td>
</tr>
<tr>
<td><b>Czas</b></td>
<td><b>ID użytkownika</b></td>
<td><b>Nazwa użytkownika</b></td>
<td><b>Opis</b></td>
<td><b>IP</b></td>
</tr>
<tr>
<td colspan="5">
<?php
//stronnicowanie
$iloscWierszy = $FP -> db_sq('SELECT COUNT(*) AS ilosc FROM log') -> ilosc;
$iloscNaStrone = 20;
$iloscWyswietlanychStron = 6;
$iloscStron = ceil($iloscWierszy / $iloscNaStrone);

if(!isset($_R -> strona) || $_R -> strona > $iloscStron || $_R -> strona <= 0)
	$strona = 1;
else
	$strona = (int)$_R -> strona;
	
$zapytanie = $FP -> db_q('SELECT * FROM log ORDER BY logID DESC LIMIT '. (($strona - 1) * $iloscNaStrone) .', '.$iloscNaStrone);

if($iloscWyswietlanychStron > $iloscStron)
	$iloscWyswietlanychStron = $iloscStron;
	

$stronaPoczatek = $strona - floor($iloscWyswietlanychStron / 2);
$stronaKoniec = $stronaPoczatek + $iloscWyswietlanychStron;

if($stronaPoczatek <= 0)
	$stronaPoczatek = 1;
	
$zakonczenie = $iloscStron - $stronaKoniec;
if($zakonczenie < 0)
	$stronaPoczatek = $stronaPoczatek + $zakonczenie + 1;

$stronaKoniec = $stronaPoczatek + $iloscWyswietlanychStron;

$stronaPoprzednia = $strona - 1;
$stronaNastepna = $strona + 1;

if($strona > 1)
	echo '<a href="?modul=ustawienia&co=logi&strona='.$stronaPoprzednia.'">Poprzednia | </a> ';
if($stronaPoczatek > 1)
	echo '<a href="?modul=ustawienia&co=logi&strona=1">1</a>';
if($stronaPoczatek > 2)
	echo ' ... ';
for(; $stronaPoczatek < $stronaKoniec; $stronaPoczatek++){
	$link = '<a href="?modul=ustawienia&co=logi&strona='.$stronaPoczatek.'"> '.$stronaPoczatek.'</a>';
	if($stronaPoczatek == $strona)
		echo '<b>'.$link.'</b>';
	else
		echo $link;
}
if($stronaPoczatek < $iloscStron)
	echo ' ... ';
if($stronaPoczatek - 1 < $iloscStron)
	echo '<a href="?modul=ustawienia&co=logi&strona='.$iloscStron.'"> '.$iloscStron.'</a>';
if($strona < $iloscStron)
	echo ' <a href="?modul=ustawienia&co=logi&strona='.$stronaNastepna.'">| Następna</a>';

?>
</td>
</tr>

<?php
while($wynik = $zapytanie -> fetch_object()){
echo '
<tr>
<td>'.$wynik -> czas.'</td>
<td>'.$wynik -> userID.'</td>
<td>'.$wynik -> userName.'</td>
<td>'.$wynik -> zdarzenie.'</td>
<td>'.$wynik -> IP.'</td>
</tr>';
}
?>

</table>
