<?php

	//przetwarzamy dane z poprzedniego formularza
	if(isset($_P -> data)){
		$trasa = json_decode($_P2 -> data);
		$kolejnosc = 1;
		foreach($trasa as $kabelWloknoID => $data){
			
			$zapytanie = $FP -> db_q('INSERT INTO relacjaWlokno SET relacjaID = "'.$_P -> relacjaID.'", kabelWloknoID = "'.$kabelWloknoID.'", kolejnosc = "'.$kolejnosc++.'"');
		}
		echo $FP -> komunikat($zapytanie, 'Nowe włókna zostały prawidłowo przypisane do relacji<br><br><a href="?modul=relacje&co=listaWlokien&relacjaID='.$_P -> relacjaID.'">Powrót do listy włókien w relacji nr '.$_P -> relacjaID.'</a>',
		'Wystąpił błąd podczas przypisywania włókien do relacji</i><br><br><a href="?modul=relacje&co=listaRelacji&relacjaID='.$_P -> relacjaID.'">Powrót do listy włókien w relacji nr '.$_P -> relacjaID.'</a>');
		if($zapytanie)
			$FP -> log('Włókna zostały przypisane do relacji nr '.$_P -> relacjaID);

	}




/*
1. Lecimy od początku relacji
2. Aby kolejne włókno dodać ro relacji musi być zespawane z poprzednim czyli:
	1. bierzemy kabel nr 2, wlokno 26
	2. chcemy polaczyc z kablem nr 1 - jedyne dostepne wlokno jakie moze byc to 87 (spaw w mufie 4)
	3. w dalszej kolejnosci wlokno 1.87 moze sie polaczyc tylko z włóknem nr 8.60
	4. tym sposobem mamy zestawiona relacje miedzy punktami relacji
3. jak to zrobic
	a. ustalamy punkt poczatkowy
	b. listujemy kable zwiazane z punktem poczatkowym
	c. listujemy wlokna w tych kablach
	d. po kolei sprawdzamy przebieg kazdego wlokna
	e. w momencie gdy przebieg osiagnie punkt docelowy, pojawia sie relacja do potwierdzenia
	
	--
zalozenia:
1. aby zestawić wlokna do relacji, wlokna musza byc pospawane
2. aby zestawic relacje, nalezy wybrac nieprzypisane wlokno z punktu poczatkowego

*/

else if(isset($_G -> relacjaID)){
	$wynik = $FP -> db_sq('SELECT * FROM relacja WHERE relacjaID = "'.$_G -> relacjaID.'"');
	
	//punkt poczatkowy
	$wynik -> punktIDStart;
	
	//listujemy kable zwiazane z punktem poczatkowym
	$zapytanie = $FP -> db_q('SELECT kabelID FROM kabel WHERE punktIDStart = "'.$wynik -> punktIDStart.'" OR punktIDKoniec = "'.$wynik -> punktIDStart.'" ORDER by kabelID ASC');
	while($wynik_kabelID = $zapytanie -> fetch_object()){
		
	//	print_rr($wynik_kabelID);

		
		//listujemy wlokna w tych kablach
		$zapytanie_wlokna = $FP -> db_q('SELECT kabelWlokno.kabelWloknoID FROM kabelWlokno NATURAL LEFT JOIN relacjaWlokno WHERE kabelID = "'.$wynik_kabelID -> kabelID.'" AND relacjaWlokno.kabelWloknoID IS NULL ORDER BY kabelWlokno.kabelWloknoID ASC');

		while($wynik_wloknaID = $zapytanie_wlokna -> fetch_object()){

			//mamy liste wszystkich wlokien we wszystkich kablach ktore wychodza od nas z serwerowni
			//po kolei sprawdzamy przebieg kazdego wlokna
			//print_rr($wynik_wloknaID);
			$koniec = $FP -> ustalKoniec(array('odleglosc' => 0, 'trasa' => array(), 'punktIDStart' => $wynik -> punktIDStart,  'kabelWloknoID' => $wynik_wloknaID -> kabelWloknoID));
			//$koniec = $FP -> ustalKoniec(array('odleglosc' => 0, 'trasa' => array(), 'punktIDStart' => $wynik -> punktIDStart,  'kabelWloknoID' => '22'));
			$koniec = (object)$koniec;
			//print_rr($koniec);
			//sprawdzamy czy osiagnelismy docelowe miejsce
			
			if(@$koniec -> punktIDKoniec == $wynik -> punktIDKoniec){
				$wynikiZOdleglosciami[$wynik_wloknaID -> kabelWloknoID] = $koniec;
				$trasa = $koniec -> trasa;
			}
			
		}
	}

	//print_rr($wynikiZOdleglosciami);


?>
<script>
$( document ).ready(function() {
    $(".multiple").css("height", parseInt($(".multiple option").length) * 17);

});
</script>
<table align="center">
<tr>
<td colspan="2"><h3>Dodawanie włókien do relacji</h3></td>
</tr>
<?php
if(isset($wynikiZOdleglosciami)){
	foreach($wynikiZOdleglosciami as $kabelWloknoID => $data){
?>
<form action="?modul=relacje&co=dodajWlokno" method="POST">
<input type="hidden" name="relacjaID" value="<?php echo $_R -> relacjaID ?>">
<input type="hidden" name="kabelWloknoID" value="<?php echo $kabelWloknoID ?>">
<input type="hidden" name="data" value='<?php echo json_encode($data -> trasa) ?>'>

<tr>
<td colspan="2"><br>Odległość: <?php echo $data -> odleglosc.' ';
if($data -> odleglosc == 1)
	echo 'włókno';
elseif($data -> odleglosc > 1 && $data -> odleglosc < 5)
	echo 'włókna';
else
	echo 'włókien';
?><br><br><input type="submit" value="Wybierz tę relację"><br><br></td>
</tr>
<?php
	foreach($data -> trasa as $wloknoID => $data2){
	
	$kabel = $FP -> db_sq('SELECT * FROM kabelWlokno WHERE kabelWloknoID = "'.$wloknoID.'"');
	$tuba = $FP -> kolor('tuba',$kabel->kolorTubaID);
	$wlokno = $FP -> kolor('wlokno',$kabel->kolorWloknoID);
?>

<tr>
<td>Kabel</td>
<td><b><?php echo $FP -> pobierzRelacjeKabla($kabel -> kabelID); ?></b></td>
</tr>
<tr>
<td>Tuba</td>
<td style="background-color: <?php echo $tuba -> kolorHTML ?>;border: 1px solid #656565; color: <?php echo $FP -> znajdzKolor($tuba -> kolorHTML) ?>"><?php echo $tuba -> kolor ?></td>
</tr>
<tr>
<td>Włókno</td>
<td style="background-color: <?php echo $wlokno -> kolorHTML ?>;border: 1px solid #656565; color: <?php echo $FP -> znajdzKolor($wlokno -> kolorHTML) ?>"><?php echo $wlokno -> kolor ?> (<?php echo $wloknoID ?>)</td>
</tr>
<?php
}
?>
</form>
<tr>
<td colspan="2"><br><hr style="border: 2px solid #000"><br></td>
</tr>
<?php
}
}
else { ?>
<tr>
<td colspan="2"><h3><b>Brak wolnych włókien w tej relacji</b></h3></td>
</tr>
<?php } ?>
</table>
<?php } ?>