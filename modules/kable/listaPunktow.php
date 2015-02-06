<?php 
//jeśli mamy kabelPunktID oznacza to, ze mamy zmieniona kolejnosc
if(isset($_R -> kabelPunktID)){
	if($_R -> nowaKolejnosc < 1){
		echo $FP -> komunikat(false, false, 'Nie można przesunąć bardziej w górę');
	}
	elseif($_R -> nowaKolejnosc > $FP -> db_sq('SELECT kolejnosc FROM kabelPunkt WHERE kabelID = "'.$_R -> kabelID.'" ORDER BY kolejnosc DESC LIMIT 1') -> kolejnosc){
		echo $FP -> komunikat(false, false, 'Nie można przesunąć bardziej w dół');	
	}
	else{
		$istniejacyPunktwDanejKolejnosci = $FP -> db_sq('SELECT kabelPunktID, kolejnosc FROM kabelPunkt WHERE kabelID = "'.$_R -> kabelID.'" AND kolejnosc = "'.$_R -> nowaKolejnosc.'" ORDER BY kolejnosc ASC');
		if($_R -> ruch == 'wGore'){
			$FP -> db_q('UPDATE kabelPunkt SET kolejnosc = "'.($istniejacyPunktwDanejKolejnosci -> kolejnosc - 1).'" WHERE kabelPunktID = "'.$istniejacyPunktwDanejKolejnosci -> kabelPunktID.'"');
		}
		
		elseif($_R -> ruch == 'wDol'){
			$FP -> db_q('UPDATE kabelPunkt SET kolejnosc = "'.($istniejacyPunktwDanejKolejnosci -> kolejnosc + 1).'" WHERE kabelPunktID = "'.$istniejacyPunktwDanejKolejnosci -> kabelPunktID.'"');
		}

		$FP -> db_q('UPDATE kabelPunkt SET kolejnosc = "'.$_R -> nowaKolejnosc.'" WHERE kabelPunktID = "'.$_R -> kabelPunktID.'"');
		$FP -> log('Przesunięto punkt w kablu nr '.$_R -> kabelID);
	}
}
if($_R -> kabelID > 0){

	if(isset($_R -> przelicz)){
		$zapytanie = $FP -> db_q('SELECT * FROM kabelPunkt WHERE kabelID = "'.$_R -> kabelID.'" ORDER BY kolejnosc ASC');
		$i = 1;
		while($wynik = $zapytanie -> fetch_object()){
			$FP -> db_q('UPDATE kabelPunkt SET kolejnosc = "'.$i.'" WHERE kabelPunktID = "'.$wynik -> kabelPunktID.'"');
			$i++;
		}
		echo $FP -> komunikat(true, 'Przeliczono kolejność punktów kabla nr '.$_R -> kabelID);
		$FP -> log('Przeliczono kolejność punktów kabla nr '.$_R -> kabelID);
	
	}
?>

<table align="center">
<tr>
<td colspan="6"><h3>Przebieg kabla nr <?php echo $FP -> pobierzRelacjeKabla($_R -> kabelID) ?></h3></td>
</tr>
<tr>
<td><b>Kolejność</b></td>
<td><b>Punkt</b></td>
<td><a href="?modul=kable&co=dodajPunkt&kabelID=<?php echo $_R -> kabelID ?>" class="zmien"><b>Dodaj punkt</b></a></td>
</tr>

<?php
$zapytanie = $FP -> db_q('SELECT * FROM kabelPunkt WHERE kabelID = "'.$_R-> kabelID.'" ORDER BY kolejnosc ASC');
$punktID = array();
$kabelPunktID = $FP -> db_sq('SELECT punktIDStart, punktIDKoniec FROM kabel WHERE kabelID = "'.$_R -> kabelID.'"');
$PHPdata = array();
$ilosc = 0;

while($wynik = $zapytanie -> fetch_object()){
	$punkt = $FP -> db_sq('SELECT gps, opis, punktTyp.typ, punkt.punktTypID as punktTypID FROM punkt LEFT JOIN punktTyp ON punkt.punktTypID = punktTyp.punktTypID WHERE punktID = "'. $wynik -> punktID .'"');
	
	echo '
	<tr>
	<td><a href="?modul=kable&co=listaPunktow&kabelID='.$_R -> kabelID.'&kabelPunktID='.$wynik -> kabelPunktID.'&nowaKolejnosc='.($wynik -> kolejnosc + 1) .'&ruch=wGore"><img src="./img/down.png"></a> '.$wynik -> kolejnosc.' <a href="?modul=kable&co=listaPunktow&kabelID='.$_R -> kabelID.'&kabelPunktID='.$wynik -> kabelPunktID.'&nowaKolejnosc='.($wynik -> kolejnosc - 1) .'&ruch=wDol"><img src="./img/up.png"></a></td>
	<td><b>'.$punkt -> opis .'</b> ('.$punkt -> typ.' - punkt nr <i>'.$wynik -> punktID.'</i>) </td>
	<td><a href="?modul=kable&co=przetnijKabel&punktID='.$wynik -> punktID.'&kabelID='.$_R -> kabelID.'">Przetnij kabel</a> | <a href="?modul=kable&co=usunPunkt&kabelPunktID='.$wynik -> kabelPunktID.'" class="usun">Usuń</a></td>
	</tr>';
	
	$punktID[$wynik -> kolejnosc] = $wynik -> punktID;
	$PHPdata[$wynik -> kolejnosc]['typ'] = $punkt -> typ;
	$PHPdata[$wynik -> kolejnosc]['opis'] = $punkt -> opis;
	$PHPdata[$wynik -> kolejnosc]['gps'] = $punkt -> gps;
	$PHPdata[$wynik -> kolejnosc]['punktID'] = $wynik -> punktID;
	$PHPdata[$wynik -> kolejnosc]['kabelPunktID'] = $wynik -> kabelPunktID;
	$PHPdata[$wynik -> kolejnosc]['punktTypID'] = $punkt -> punktTypID;
	$PHPdata[$wynik -> kolejnosc]['kabelID'] = $_R -> kabelID;
	$ilosc++;
}
?>
</table>
<br><br><center>
<?php
if(!empty($punktID)){
	if($kabelPunktID -> punktIDStart != $punktID[1])
		echo '<b><font color="red">UWAGA! Pierwszy punkt nie pokrywa się z początkiem kabla!</font></b><br><br>';
	if($kabelPunktID -> punktIDKoniec != end($punktID))
		echo '<b><font color="red">UWAGA! Ostatni punkt nie pokrywa się z końcem kabla!</font></b><br><br>';
}

?>
<br><br>
<script src="http://maps.google.com/maps/api/js?sensor=false" type="text/javascript"></script>
<script src="inc/markerclusterer_compiled.js" type="text/javascript"></script>

<script>
//dane z PHP
<?php 

if($ilosc > 1 && !isset($wylaczMape)){
echo 'var PHPdata = '.json_encode($PHPdata).';'; ?>
</script>
<script type="text/javascript">

jQuery(document).ready(function($) {
	var opcjeMapy = {
		mapTypeId: google.maps.MapTypeId.HYBRID,
		//predefiniowane wartosci ktore sie zmienia jak sie pojawia jakies punkty
		zoom: poczatkowyZoom,
		center: new google.maps.LatLng(poczatkowyLat, poczatkowyLon)
	};
	mapa = new google.maps.Map(document.getElementById("mapa"), opcjeMapy);
	dymek = new google.maps.InfoWindow();
	markerBounds = new google.maps.LatLngBounds();
	var listaPozycji = [];
	var markery = [];
	var punkty = [];
	
	
	for(x in PHPdata){

		gps = rozdzielGPS(PHPdata[ x ].gps);


		var zIndexVal = undefined;
			
		//rysujemy punkty posrednie
		while(listaPozycji[gps.lat] == gps.lon){
			gps.lat = parseFloat(gps.lat) + odlegloscX;
			gps.lon = parseFloat(gps.lon) + odlegloscX;
		}
		listaPozycji[gps.lat] = gps.lon;
			
		if(x > 1){
			gps1 = rozdzielGPS(PHPdata[ x - 1 ].gps);
			var grubosc = 1;
			if(PHPdata.iloscWlokien <= 8)
				grubosc = 1;
			else if (PHPdata.iloscWlokien <= 12)
				grubosc = 1.5;
			else if (PHPdata.iloscWlokien <= 24)
				grubosc = 2.5;
			else if (PHPdata.iloscWlokien <= 72)
				grubosc = 3;					
			else
				grubosc = 4;
			kreska(gps1.lat, gps1.lon, gps.lat, gps.lon, PHPdata.opis + '<br><br><b><a href="?modul=kable&co=listaWlokien&kabelID='+PHPdata[x].kabelID+'">Lista włókien (Ilość włókien: '+PHPdata.iloscWlokien+') </a></b><br><hr>Punkty sąsiednie:<br><div style="white-space: nowrap">- ' + PHPdata[x - 1].typ + ' <b>'+ PHPdata[x - 1].opis + '</b> (' + PHPdata[x - 1].punktID + ')</div><div style="white-space: nowrap">- ' + PHPdata[x].typ + ' <b>'+ PHPdata[x].opis + '</b> (' + PHPdata[x].punktID + ')</div>','yellow',grubosc);
			markerBounds.extend(new google.maps.LatLng(gps1.lat, gps1.lon));
			markerBounds.extend(new google.maps.LatLng(gps.lat, gps.lon));

		}
		
	
			if(punkty[PHPdata[x].punktID] != 1 && PHPdata[x].punktID != undefined){
				var zawartoscMarkera = '<div style="white-space: nowrap">'+ PHPdata[x].opis + '<br>(' + PHPdata[x].typ + ' - punkt nr ' + PHPdata[x].punktID + ')</div><br>';
				
				//jezeli typ danych to mufa
				if(PHPdata[x].punktTypID == 1){
					zawartoscMarkera += '<a href="?modul=mufy&co=listaSpawow&mufaID='+ PHPdata[x].mufaID + '" class="zmien">Lista spawów</a><br>';
					zIndexVal = 'max';
				}
				
				//jezeli typ danych to przelacznica
				if(PHPdata[x].punktTypID == 2){
					zawartoscMarkera + '<a href="?modul=przelacznice&co=listaPortow&przelacznicaID='+ PHPdata[x].przelacznicaID + '" class="zmien">Lista portów</a><br>';
					zIndexVal = 'max';
				}
			
					
				zawartoscMarkera += '<a href="?modul=punkty&co=listaObiektow&punktID=' + PHPdata[x].punktID + '" class="zmien">Co tu jest?</a><br>';
				if(PHPdata[x].punktTypID != 1 && PHPdata[x].punktTypID != 2){
					zawartoscMarkera += '<a href="?modul=mufy&co=dodajMufe&punktID=' + PHPdata[x].punktID + '" class="zmien">Dodaj mufę</a><br>';
					zawartoscMarkera += '<a href="?modul=przelacznice&co=dodajPrzelacznice&punktID=' + PHPdata[x].punktID + '" class="zmien">Dodaj przełącznicę</a><br>';
				}
				
				zawartoscMarkera += '<a href="?modul=kable&co=przetnijKabel&punktID=' + PHPdata[x].punktID + '" class="zmien">Przetnij kabel</a><hr>';
				zawartoscMarkera += '<a href="?modul=punkty&co=zmienPunkt&punktID=' + PHPdata[x].punktID + '" class="zmien">Zmień punkt</a><br>';
				zawartoscMarkera += '<a href="?modul=punkty&co=usunPunkt&punktID=' + PHPdata[x].punktID + '" class="usun">Usuń punkt</a>';
				
				//dodajemy marker na mapie
				var tmpMarker = dodajMarker(gps.lat, gps.lon,zawartoscMarkera,kolorMarkera(PHPdata[x].punktTypID), zIndexVal);
				markery.push(tmpMarker);

				markerBounds.extend(new google.maps.LatLng(gps.lat, gps.lon));
				

			}
		}

		//uwaga! to musi byc uruchomione przed boundsami
		//uruchamiamy listenera do zooma i centera by trzymac to w Cookiesach
		uruchomPo();
		//przyblizamy do umieszczonych punktow
		if(markerBounds)
			mapa.fitBounds(markerBounds);
		//agregujemy markery
		markerClusterer = new MarkerClusterer(mapa, markery, markerClustererOptions);
		//ustawiamy wysokosc mapy
		$('#mapa').width('100%');
		$('#mapa').height(window.innerHeight);
		google.maps.event.trigger(mapa, 'resize');
	});
</script>

   <div id="mapa" style="height:550px; margin: auto"></div>

</center>
<?php
}
}
?>	