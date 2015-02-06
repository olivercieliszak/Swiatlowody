<?php 
//jeśli mamy kabelPunktID oznacza to, ze mamy zmieniona kolejnosc
if($_R -> relacjaID > 0){
$wynik = $FP -> db_sq('SELECT * FROM relacja WHERE relacjaID = "'.$_G -> relacjaID.'"');

?>
<table align="center">
<tr>
<td colspan="6"><h3>Przebieg punktów w relacji nr <?php echo $wynik -> relacjaID.':<br>'.$FP -> pobierzPunkt($wynik -> punktIDStart).' - '.$FP -> pobierzPunkt($wynik -> punktIDKoniec).'<br><br>'.$wynik -> opis ?></h3></td>
</tr>


<?php
$JSustawienia = array('pokazPunkty' => 1);
if(isset($_G -> pokazPunkty))
	$JSustawienia['pokazPunkty'] = $_G -> pokazPunkty;


$JSkable = array();
$i = 0;
$odleglosc = 0;
$zapytanie1 = $FP -> db_q('SELECT kabelWloknoID FROM relacjaWlokno WHERE relacjaID = "'.$_R -> relacjaID.'" ORDER BY kolejnosc');
while($wynik1 = $zapytanie1 -> fetch_object()){


	$kabelID = $FP -> pobierzIDKabla($wynik1 -> kabelWloknoID);
	$JSkable[$kabelID] = true;
	
	echo '<tr><td><br><b>Kabel nr '.$kabelID.'</b><br><br></td></tr>';

	//sprawdzamy czy zostal dodany przebieg fizyczny kabla. jezeli nie, wzorujemy sie tylko na punkcie poczatkowym i koncowym (moze ktos nie chce dodawac szczegolow?)
	$iloscPunktow = $FP -> db_sq('SELECT COUNT(*) as ilosc FROM kabelPunkt WHERE kabelID = "'.$kabelID.'"') -> ilosc;
	$ostatniPunktID;
	if($iloscPunktow > 1){
	
		$zapytanie2 = $FP -> db_q('SELECT * FROM kabelPunkt NATURAL JOIN punkt WHERE kabelID = "'.$kabelID.'" ORDER BY kolejnosc ASC');

		while($wynik2 = $zapytanie2 -> fetch_object()){
		
			
			echo '<tr>
			<td>'.$FP -> pobierzPunkt($wynik2 -> punktID);
			if($i >= 1){
				$odleglosc += $tmp = $FP -> odlegloscMiedzyPunktami($poprzedniPunkt -> gps, $wynik2 -> gps);
				echo '<br>'.$odleglosc.' m (+ '.$tmp.' m)';
			}
			else
				echo '<br>0 m (+0 m)';			
			
			if($FP -> pobierzTypPunktu($wynik2 -> punktID) == 2){
				if($FP -> db_sq('SELECT COUNT(*) as ilosc FROM przelacznicaPort WHERE kabelWloknoID = "'.$wynik1 -> kabelWloknoID.'"') -> ilosc > 0){
					$przelacznica = $FP -> db_sq('SELECT * FROM przelacznicaPort WHERE kabelWloknoID = "'.$wynik1 -> kabelWloknoID.'"');
					echo '<br><br>Port '.$przelacznica -> typ .' nr '.$przelacznica -> port.'<br>';
				}
			}


			
			echo '</td>
			</tr>';
			$poprzedniPunkt = $wynik2;			
			$i++;
			
			$ostatniPunktID = $wynik2 -> punktID;
		}
	}
	//jezeli nie ma podanych punktow posrednich
	else{
		$kabelDane = $FP -> db_sq('SELECT punktIDStart, punktIDKoniec FROM kabel WHERE kabelID = "'.$kabelID.'"');
		foreach($kabelDane as $nazwa => $punkt){
			
			echo '<tr>
			<td>'.$FP -> pobierzPunkt($punkt);
			if($i >= 1 && $nazwa == "punktIDKoniec"){
				$odleglosc += $tmp = $FP -> dlugoscKabla($kabelID);
				echo '<br>'.$odleglosc.' m (+ '.$tmp.' m)';
			}
			else if($i >= 1 && $nazwa == "punktIDStart"){
							echo '<br>'.$odleglosc.' m (+ 0 m)';	
			}
			else
				echo '<br>0 m (+ 0 m)';					
			
			if($FP -> pobierzTypPunktu($punkt) == 2){
				if($FP -> db_sq('SELECT COUNT(*) as ilosc FROM przelacznicaPort WHERE kabelWloknoID = "'.$wynik1 -> kabelWloknoID.'"') -> ilosc > 0){
					$przelacznica = $FP -> db_sq('SELECT * FROM przelacznicaPort WHERE kabelWloknoID = "'.$wynik1 -> kabelWloknoID.'"');
					echo '<br><br>Port '.$przelacznica -> typ .' nr '.$przelacznica -> port.'<br>';
				}
			}
			echo '</td>
			</tr>';		
			$ostatniPunktID = $punkt;
			$poprzedniPunkt = $FP -> db_sq('SELECT gps FROM punkt WHERE punktID = "'.$kabelDane -> punktIDKoniec.'"');
			$i++;

		}
	
	}
	$mufaID = $FP -> db_sq('SELECT mufaID, opis FROM mufa WHERE punktID = "'.$ostatniPunktID.'"');
	$mufaTekst = '';
	if(isset($mufaID -> mufaID)){
		$mufaTekst = '<a href="?modul=mufy&co=listaSpawow&mufaID='.$mufaID -> mufaID.'" style="color: #fff">Mufa nr '.$mufaID -> mufaID.'</a>';
	if($mufaID -> opis != "")
		$mufaTekst .= '<br>Opis: '.$mufaID -> opis;
	}
	else
		$mufaTekst = 'Koniec relacji';

	echo '<tr><td colspan="10" style="background-color: #000; color: #fff">'.$mufaTekst.'</td></tr>';
	
}

//przygotowujemy mape
foreach($JSkable as $kabelID => $nic){
	$kabelPunktID = $FP -> db_sq('SELECT punktIDStart, punktIDKoniec FROM kabel WHERE kabelID = "'.$kabelID.'"');

	$JSiloscPunktow = 0;
	$zapytanie2 = $FP -> db_q('SELECT * FROM kabelPunkt WHERE kabelID = "'.$kabelID.'" ORDER BY kolejnosc ASC');
	while($wynik2 = $zapytanie2 -> fetch_object()){
		$punkt = $FP -> db_sq('SELECT gps, opis, punktTyp.typ, punkt.punktTypID FROM punkt LEFT JOIN punktTyp ON punkt.punktTypID = punktTyp.punktTypID WHERE punktID = "'. $wynik2 -> punktID .'"');
		
		
		$punktID[$kabelID][$wynik2 -> kolejnosc] = $wynik2 -> punktID;
		$PHPdata[$kabelID][$wynik2 -> kolejnosc]['typ'] = $punkt -> typ;
		$PHPdata[$kabelID][$wynik2 -> kolejnosc]['opis'] = $punkt -> opis;
		$PHPdata[$kabelID][$wynik2 -> kolejnosc]['gps'] = $punkt -> gps;
		$PHPdata[$kabelID][$wynik2 -> kolejnosc]['punktID'] = $wynik2 -> punktID;
		$PHPdata[$kabelID][$wynik2 -> kolejnosc]['punktTypID'] = $punkt -> punktTypID;
		//jeżeli mufa to pobieramy mufaID
		if($punkt -> punktTypID == 1)
			@$PHPdata[$kabelID][$wynik2 -> kolejnosc]['mufaID'] = $FP -> db_sq('SELECT mufaID from mufa WHERE punktID = "'.$wynik2 -> punktID.'"') -> mufaID;
		
		//jeżeli przełącznica to pobieramy przelacznicaID
		else if($punkt -> punktTypID == 2)
			@$PHPdata[$kabelID][$wynik2 -> kolejnosc]['przelacznicaID'] = $FP -> db_sq('SELECT przelacznicaID from przelacznica WHERE punktID = "'.$wynik2 -> punktID.'"') -> przelacznicaID;
		
		$PHPdata[$kabelID][$wynik2 -> kolejnosc]['kabelPunktID'] = $wynik2 -> kabelPunktID;
		$JSiloscPunktow++;
	}
	//jezeli nie ma zadnych punktow posrednich, rysujemy relacje logiczna
	if($JSiloscPunktow == 0){
		$i = 1;	
		foreach ($kabelPunktID as $nazwa => $punktID){
			
			$punkt = $FP -> db_sq('SELECT gps, opis, punktTyp.typ, punkt.punktTypID FROM punkt LEFT JOIN punktTyp ON punkt.punktTypID = punktTyp.punktTypID WHERE punktID = "'. $punktID .'"');
			
			
			
			$PHPdata[$kabelID][$i]['typ'] = $punkt -> typ;
			$PHPdata[$kabelID][$i]['opis'] = $punkt -> opis;
			$PHPdata[$kabelID][$i]['gps'] = $punkt -> gps;
			$PHPdata[$kabelID][$i]['punktID'] = $punktID;
			$PHPdata[$kabelID][$i]['punktTypID'] = $punkt -> punktTypID;
			//jeżeli mufa to pobieramy mufaID
			if($punkt -> punktTypID == 1)
				@$PHPdata[$kabelID][$i]['mufaID'] = $FP -> db_sq('SELECT mufaID from mufa WHERE punktID = "'.$punktID.'"') -> mufaID;
			
			//jeżeli przełącznica to pobieramy przelacznicaID
			else if($punkt -> punktTypID == 2)
				@$PHPdata[$kabelID][$i]['przelacznicaID'] = $FP -> db_sq('SELECT przelacznicaID from przelacznica WHERE punktID = "'.$punktID.'"') -> przelacznicaID;
						
			$i++;
		}
		
	
	}
	
	$PHPdata[$kabelID]['opis'] = 'Kabel '.$FP -> pobierzRelacjeKabla($kabelID,1);
	$PHPdata[$kabelID]['iloscWlokien'] = $FP -> db_sq('SELECT COUNT(*) as ilosc FROM kabelWlokno WHERE kabelID = "'.$kabelID.'"') -> ilosc;

}
?>
</table>
<br><br>
<script src="http://maps.google.com/maps/api/js?sensor=false" type="text/javascript"></script>
<script src="inc/markerclusterer_compiled.js" type="text/javascript"></script>

<script>
//dane z PHP
<?php echo 'var JSustawienia = '.json_encode($JSustawienia).';'; ?>
<?php echo 'var PHPdata = '.json_encode($PHPdata).';'; ?>
</script>
<script type="text/javascript">
/* 

	Początek mapy

*/
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
	
	for(kabelID in PHPdata){
		for(x in PHPdata[kabelID]){
	
			gps = rozdzielGPS(PHPdata[kabelID][ x ].gps);


			var zIndexVal = undefined;
				
			//rysujemy punkty posrednie
				
			if(x > 1){
			
				gps1 = rozdzielGPS(PHPdata[kabelID][ x - 1 ].gps);
				var grubosc = 1;
				if(PHPdata[kabelID].iloscWlokien <= 8)
					grubosc = 1;
				else if (PHPdata[kabelID].iloscWlokien <= 12)
					grubosc = 1.5;
				else if (PHPdata[kabelID].iloscWlokien <= 24)
					grubosc = 2.5;
				else if (PHPdata[kabelID].iloscWlokien <= 72)
					grubosc = 3;					
				else
					grubosc = 4;
				kreska(gps1.lat, gps1.lon, gps.lat, gps.lon, PHPdata[kabelID].opis + '<br><br><b><a href="?modul=kable&co=listaWlokien&kabelID='+kabelID+'">Lista włókien (Ilość włókien: '+PHPdata[kabelID].iloscWlokien+') </a></b><br><hr>Punkty sąsiednie:<br><div style="white-space: nowrap">- ' + PHPdata[kabelID][x - 1].typ + ' <b>'+ PHPdata[kabelID][x - 1].opis + '</b> (' + PHPdata[kabelID][x - 1].punktID + ')</div><div style="white-space: nowrap">- ' + PHPdata[kabelID][x].typ + ' <b>'+ PHPdata[kabelID][x].opis + '</b> (' + PHPdata[kabelID][x].punktID + ')</div>','yellow',grubosc);
				
				markerBounds.extend(new google.maps.LatLng(gps1.lat, gps1.lon));
				markerBounds.extend(new google.maps.LatLng(gps.lat, gps.lon));

			}
			if(JSustawienia.pokazPunkty == 1){
			
				while(listaPozycji[gps.lat] == gps.lon){
					gps.lat = parseFloat(gps.lat) + odlegloscX;
					gps.lon = parseFloat(gps.lon) + odlegloscX;
				}
				if(punkty[PHPdata[kabelID][x].punktID] != 1 && PHPdata[kabelID][x].punktID != undefined){
					var zawartoscMarkera = '<div style="white-space: nowrap">'+ PHPdata[kabelID][x].opis + '<br>(' + PHPdata[kabelID][x].typ + ' - punkt nr ' + PHPdata[kabelID][x].punktID + ')</div><br>';
					
					//jezeli typ danych to mufa
					if(PHPdata[kabelID][x].punktTypID == 1){
						zawartoscMarkera += '<a href="?modul=mufy&co=listaSpawow&mufaID='+ PHPdata[kabelID][x].mufaID + '" class="zmien">Lista spawów</a><br>';
						zIndexVal = 'max';
					}
					
					//jezeli typ danych to przelacznica
					if(PHPdata[kabelID][x].punktTypID == 2){
						zawartoscMarkera + '<a href="?modul=przelacznice&co=listaPortow&przelacznicaID='+ PHPdata[kabelID][x].przelacznicaID + '" class="zmien">Lista portów</a><br>';
						zIndexVal = 'max';
					}
						
						
					zawartoscMarkera += '<a href="?modul=punkty&co=listaObiektow&punktID=' + PHPdata[kabelID][x].punktID + '" class="zmien">Co tu jest?</a><br>';
					if(PHPdata[kabelID][x].punktTypID != 1 && PHPdata[kabelID][x].punktTypID != 2){
						zawartoscMarkera += '<a href="?modul=mufy&co=dodajMufe&punktID=' + PHPdata[kabelID][x].punktID + '" class="zmien">Dodaj mufę</a><br>';
						zawartoscMarkera += '<a href="?modul=przelacznice&co=dodajPrzelacznice&punktID=' + PHPdata[kabelID][x].punktID + '" class="zmien">Dodaj przełącznicę</a><br>';
					}
					
					zawartoscMarkera += '<a href="?modul=kable&co=przetnijKabel&punktID=' + PHPdata[kabelID][x].punktID + '" class="zmien">Przetnij kabel</a><hr>';
					zawartoscMarkera += '<a href="?modul=punkty&co=zmienPunkt&punktID=' + PHPdata[kabelID][x].punktID + '" class="zmien">Zmień punkt</a><br>';
					zawartoscMarkera += '<a href="?modul=punkty&co=usunPunkt&punktID=' + PHPdata[kabelID][x].punktID + '" class="usun">Usuń punkt</a>';
					
					//dodajemy marker na mapie
					var tmpMarker = dodajMarker(gps.lat, gps.lon,zawartoscMarkera,kolorMarkera(PHPdata[kabelID][x].punktTypID), zIndexVal);
					markery.push(tmpMarker);

					markerBounds.extend(new google.maps.LatLng(gps.lat, gps.lon));

					}
			}
			punkty[PHPdata[kabelID][x].punktID] = 1;
			listaPozycji[gps.lat] = gps.lon;

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
		$('#mapa').height(window.innerHeight - $('#tech').height() - $('#tech2').height());
		$('#mapa').width($('table').width());
		google.maps.event.trigger(mapa, 'resize');

	
	});
</script>
   <div id="mapa" style="height:550px;"></div>
   <div style="float: right; margin-top: 10px; text-align: right" id="tech2">
<?php
if($JSustawienia['pokazPunkty'] == 1) 
	echo '<a href="?modul=relacje&co=listaPunktow&relacjaID='.$_R -> relacjaID.'&pokazPunkty=0#mapa" class="zmien">Ukryj punkty</a>';
else
	echo '<a href="?modul=relacje&co=listaPunktow&relacjaID='.$_R -> relacjaID.'&pokazPunkty=1#mapa" class="zmien">Pokaż punkty</a>';

?><br>
Długość wszystkich kabli: <?php echo $odleglosc; ?> m
</div>
<?php
}
?>