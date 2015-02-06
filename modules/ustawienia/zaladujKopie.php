
<?php
if(isset($_G -> wersja)){
	//takie male zabezpieczenie
	if(strstr($_G -> wersja, '-') && strstr($_G -> wersja, '_')){
		$dataGodzina = explode('_', $_G -> wersja);
	if(count($dataGodzina) > 1){
		$godzina = str_replace('-', ':', $dataGodzina[2]);
	}
	//linia zagluszona ze wzgledow bezpieczenstwa
	@$plik = file('./backup/'.$_G -> wersja.'.sql');

?>
<center>
<h3 id="infoProgess"><?php echo 'Trwa przywracanie kopii zapasowej z dnia '.$dataGodzina[1].' z godziny '.$godzina; ?></h3><br>
<div id="progress" style="width:500px;border:1px solid #ccc; text-align: left"></div><br>
<div id="information"></div>
</center>
<?php
		set_time_limit(600);
		$ilosc = count($plik);
		//dla bezpieczenstwa wykonujemy kopie zapasowa przed przywroceniem innej kopii
		$wykonajKopie = $FP -> backup_tables('./backup/', 'Przywracanie kopii zapasowej');

		foreach ($plik as $line_num => $line) {
				if($line_num >= 1){
				$procenty = intval($line_num/$ilosc * 100)."%";
				// Javascript for updating the progress bar and information
				echo '<script language="javascript">
				document.getElementById("progress").innerHTML="<div style=\"width:'.$procenty.';background-color:#ddd;\">&nbsp;</div>";
				document.getElementById("information").innerHTML="<b>'.$line_num.' / '.$ilosc.' rekordów</b>";
				</script>';
				echo str_repeat(' ',1024*64);
	
				$zapytanie = $FP -> db_q($line);

				flush();
				}
				else
					$opis = $line;

		}
		echo '<script> $("#progress").hide(); </script>';
		echo '<script> $("#information").hide(); </script>';
		echo '<script> $("#infoProgess").hide(); </script>';
		echo $FP -> komunikat($zapytanie, 'Kopia zapasowa "'.$opis.'" z dnia '.$dataGodzina[1].' z godziny '.$godzina.'<br>została załadowana prawidłowo', 'Wystąpił błąd podczas ładowania kopii zapasowej');
		if($zapytanie)
			$FP -> log('Załadowano kopię zapasową "'.$opis.'" z dnia '.$dataGodzina[1].' z godziny '.$godzina);
	}
}
?>
<table>
<tr>
<td>
<h3>Przywróć kopię zapasową</h3>
<div class="usun"><b>UWAGA! Wszystkie dane zostaną nadpisane!</b></div><br><br>Wybierz wersję:<br><br>
</td>
</tr>


<?php 

function iloscRekordowWPliku($plik){

	$iloscLinii = 0;
	$handle = fopen($plik, "r");
	while(!feof($handle)){
	  $linia = fgets($handle);
	  $iloscLinii++;
	}

	fclose($handle);

	return $iloscLinii - 1;
}
$folder = './backup/';
$pliki = scandir($folder);
rsort($pliki);
foreach($pliki as $nazwaPliku){
	
	$dataGodzina = explode('_', $nazwaPliku);
	if(count($dataGodzina) > 1){
		$godzina = str_replace('-', ':', $dataGodzina[2]);
		$f = fopen($folder.$nazwaPliku, 'r');
		$opis = trim(fgets($f));
		fclose($f);
		if($opis == '')
			$opis = '(brak opisu)';
		echo '<tr><td><a href="?modul=ustawienia&co=zaladujKopie&wersja='.substr($nazwaPliku, 0, -4).'" onClick="if(confirm(\'Czy napewno chcesz przywrócić kopię zapasową\n'.$opis.' z dnia '.$dataGodzina[1].' z godziny '.substr($godzina, 0, -4).'?\nSpowoduje to nadpisanie wszystkich danych!\') == 0) return false">'.$dataGodzina[1].' '.substr($godzina, 0, -4).' ('.iloscRekordowWPliku($folder.$nazwaPliku).' rekordów) - '. $opis .'</a></td></tr>';
	}
}
?>
</table>