<?php

if(isset($_R -> kabelID)){
$JSautosugestia = array();
$JSautosugestia['id'] = false;
$JSautosugestia['gps'] = false;
$JSautosugestia['typ'] = false;


if(isset($_P -> kabelID)){
	if(!empty($_P -> kabelID)){
		if($FP -> db_sq('SELECT COUNT(*) as ilosc FROM kabelPunkt WHERE kabelID = "'.$_P -> kabelID.'" AND punktID = "'.$_P -> punktID.'"') -> ilosc > 0)
			echo $FP -> komunikat(false, false, 'Dany punkt już występuje w przebiegu kabla');
		
		else{
			
			//jezeli kolejnosc bedzie inna niz planowana, trzeba przesunac wszystkie pozostale
			$zapytanie = $FP -> db_q('SELECT kabelPunktID, kolejnosc FROM kabelPunkt WHERE kabelID = "'.$_P -> kabelID.'" AND kolejnosc >= '.$_P -> kolejnosc.' ORDER BY kolejnosc ASC'); 
			while($wynik = $zapytanie -> fetch_object()){
				
				
				
				$FP -> db_q('UPDATE kabelPunkt SET kolejnosc = "'.++$wynik -> kolejnosc.'" WHERE kabelPunktID = "'.$wynik -> kabelPunktID.'"');
				
			}
			
			
			$zapytanieInsert = $FP -> db_q('INSERT INTO kabelPunkt SET kabelID = "'.$_P -> kabelID.'", punktID = "'.$_P -> punktID .'", kolejnosc = "'.$_P -> kolejnosc.'"');
			
			echo $FP -> komunikat($zapytanieInsert, 'Punkt <i>'. $FP -> pobierzPunkt($_P -> punktID) .'</i><br>został prawidłowo dodany do przebiegu kabla<br><i>'.$FP -> pobierzRelacjeKabla($_R -> kabelID).'</i><br><br><a href="?modul=kable&co=listaPunktow&kabelID='.$_R -> kabelID.'">Pokaż przebieg kabla nr '.$_R -> kabelID.'</a>',
			'Wystąpił błąd podczas dodawania uzupełniania przebiegu kabla <i>'.$FP -> pobierzRelacjeKabla($_R -> kabelID).'</i>');
			if($zapytanieInsert)
				$FP -> log('Punkt '.$FP -> pobierzPunkt($_P -> punktID).' został dodany do przebiegu kabla nr '.$_R -> kabelID);
		}
	}


}

$ilosc = $FP -> db_sq('SELECT COUNT(*) as ilosc FROM kabelPunkt WHERE kabelID = "'.$_R -> kabelID.'"') -> ilosc;
if($ilosc == 0){
	$JSautosugestia['typ'] = 'start';
	$JSautosugestia['id'] = $FP -> db_sq('SELECT punktIDStart FROM kabel WHERE kabelID = "'.$_R -> kabelID.'"') -> punktIDStart;
	$JSautosugestia['gps'] = $FP -> db_sq('SELECT gps FROM punkt WHERE punktID = "'.$JSautosugestia['id'].'"') -> gps;
}

?>
<form action="?modul=kable&co=dodajPunkt" method="POST">
<input type="hidden" name="kabelID" value="<?php echo $_R -> kabelID ?>">
<table align="center">
<tr>
<td colspan="2"><h3>Uzupełnianie przebiegu kabla <?php echo $FP -> pobierzRelacjeKabla($_R -> kabelID) ?></h3><br>
<?php
if($JSautosugestia['typ'] == 'start'){
	echo 'To pierwszy punkt przebiegu.<br>Automatycznie został ustawiony punkt początkowy kabla.';
}
?>
</td>
</tr>
<tr>
<td>Punkt</td>
<td>
<?php 
		$PHPdata = array();
		$punkty = array();
		$zapytanie = $FP -> db_q('SELECT punktID FROM kabelPunkt WHERE kabelID = "'.$_R -> kabelID.'" ORDER BY punktID ASC');
		while($wynik = $zapytanie -> fetch_object()){
			$punkty[$wynik -> punktID] = true;
		}
		
		$zapytanie2 = $FP -> db_q('SELECT punktID, punktTypID, kolorPunkt, typ, opis, gps FROM punkt NATURAL LEFT JOIN punktTyp ORDER BY punktID ASC');
		
		echo '<select name="punktID" id="punktID" onChange="zmienKolor();">';
		while($wynik = $zapytanie2 -> fetch_object()){
			if(!isset($punkty[$wynik -> punktID])){
				if($wynik -> opis == '')
					$wynik -> opis = '(bez opisu)';
				
				if($wynik -> punktID == $JSautosugestia['id']){
					echo  '<option value="'.$wynik -> punktID.'" style="background-color: '.$FP -> koloryPunktowTlo($wynik -> kolorPunkt).'; color: '.$FP -> koloryPunktowTekst($wynik -> kolorPunkt).';" selected>'.$wynik -> punktID.' - '.$wynik -> typ.': '.$wynik -> opis.'</option>';
					//$JSautosugestia = $JSautosugestia;
				}
				else
					echo  '<option value="'.$wynik -> punktID.'" style="background-color: '.$FP -> koloryPunktowTlo($wynik -> kolorPunkt).'; color: '.$FP -> koloryPunktowTekst($wynik -> kolorPunkt).';">'.$wynik -> punktID.' - '.$wynik -> typ.': '.$wynik -> opis.'</option>';
			
				$PHPdata[$wynik -> gps] = $wynik;
			}
				
		

		}
		echo '</select>';

?></td>
</tr>
<tr>
<td>Kolejność</td>
<td><?php 
	
		$zapytanie = $FP -> db_q('SELECT kolejnosc FROM kabelPunkt WHERE kabelID = "'.$_R -> kabelID.'" ORDER BY kolejnosc ASC');
		$kolejnosc = 0;
		echo '<select name="kolejnosc">';
		while($wynik = $zapytanie -> fetch_object()){

			echo  '<option>'.$wynik -> kolejnosc.'</option>';
			$kolejnosc = $wynik -> kolejnosc;
		}
		echo '<option selected>'.++$kolejnosc.'</option>';
		echo '</select>';


 ?></td>
</tr>

<tr>
<td colspan="2"><input type="submit" value="Dodaj!"></td>
</tr>

</table>
</form>

<br>
<script src="http://maps.google.com/maps/api/js?sensor=false" type="text/javascript"></script>
<script src="inc/markerclusterer_compiled.js" type="text/javascript"></script>

<script>
//dane z PHP
<?php 
echo 'var PHPdata = '.json_encode($PHPdata).';';
echo 'var PHPautosugestia = '.json_encode($JSautosugestia).';';
 ?>
 //alert(print_r(PHPdata));
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

	for(punktID in PHPdata){

			var zIndexVal = undefined;
			
			gps = rozdzielGPS(PHPdata[punktID].gps);
			
			//przesuniecie wzgledem siebie punktow o takich samych koordynatach
			while(listaPozycji[gps.lat] == gps.lon){
				gps.lat = parseFloat(gps.lat) + odlegloscX;
				gps.lon = parseFloat(gps.lon) + odlegloscX;
			}
			var opis = '<b>'+PHPdata[punktID].opis+'</b>';
			if(opis == '')
				opis = '(brak opisu)';
			var zawartoscMarkera = '<div style="white-space: nowrap">'+ opis + '<br>(' + PHPdata[punktID].typ + ' - punkt nr ' + PHPdata[punktID].punktID + ')</div><br>';
			
			//jezeli typ danych to mufa
			if(PHPdata[punktID].punktTypID == 1){
				zawartoscMarkera += '<a href="?modul=mufy&co=listaSpawow&mufaID='+ PHPdata[punktID].mufaID + '" class="zmien">Lista spawów</a><br>';
				zIndexVal = 'max';
			}
			
			//jezeli typ danych to przelacznica
			if(PHPdata[punktID].punktTypID == 2){
				zawartoscMarkera + '<a href="?modul=przelacznice&co=listaPortow&przelacznicaID='+ PHPdata[punktID].przelacznicaID + '" class="zmien">Lista portów</a><br>';
				zIndexVal = 'max';
			}
				
				
			zawartoscMarkera += '<a href="?modul=punkty&co=listaObiektow&punktID=' + PHPdata[punktID].punktID+ '" class="zmien">Co tu jest?</a><br>';
			if(PHPdata[punktID].punktTypID != 1 && PHPdata[punktID].punktTypID != 2){
				zawartoscMarkera += '<a href="?modul=mufy&co=dodajMufe&punktID=' + PHPdata[punktID].punktID+ '" class="zmien">Dodaj mufę</a><br>';
				zawartoscMarkera += '<a href="?modul=przelacznice&co=dodajPrzelacznice&punktID=' + PHPdata[punktID].punktID+ '" class="zmien">Dodaj przełącznicę</a><br>';
			}
			
			zawartoscMarkera += '<a href="?modul=kable&co=przetnijKabel&punktID=' + PHPdata[punktID].punktID+ '" class="zmien">Przetnij kabel</a><hr>';
			zawartoscMarkera += '<a href="?modul=punkty&co=zmienPunkt&punktID=' + PHPdata[punktID].punktID+ '" class="zmien">Zmień punkt</a><br>';
			zawartoscMarkera += '<a href="?modul=punkty&co=usunPunkt&punktID=' + PHPdata[punktID].punktID+ '" class="usun">Usuń punkt</a>';
			
			var tmpMarker = dodajMarker(gps.lat, gps.lon,zawartoscMarkera,kolorMarkera(PHPdata[punktID].punktTypID), zIndexVal);

			markery.push(tmpMarker);
			markerBounds.extend(new google.maps.LatLng(gps.lat, gps.lon));		
			listaPozycji[gps.lat] = gps.lon;
			
			google.maps.event.addListener(tmpMarker,"click",function(zdarzenie){
	
				$('#punktID').val(PHPdata[skroc(zdarzenie.latLng)].punktID);
				zmienKolor();
				}
			);
		
		}	
	

	uruchomPo();
	if(markerBounds)
		mapa.fitBounds(markerBounds);
	markerClusterer = new MarkerClusterer(mapa, markery, markerClustererOptions);
	
	if(PHPautosugestia.gps != false){
		var gps = rozdzielGPS(PHPautosugestia.gps);
		mapa.setCenter(new google.maps.LatLng(gps.lat, gps.lon));
		
	}
	$('#mapa').width($('table').width());
	$('#mapa').height(window.innerHeight-$('table').height()-$('#tech').height());
	google.maps.event.trigger(mapa, 'resize');

	});
	
	function zmienKolor(){
	
	$('#punktID').css('background-color', $('#punktID').children(":selected").css('background-color'));
	$('#punktID').css('color', znajdzKolor($('#punktID').children(":selected").css('background-color')));
	
	}
	zmienKolor();	
</script>
   <div id="mapa" style="height:450px; margin: auto"></div>

<hr>
<?php
}
$wylaczMape = 1;
include('listaPunktow.php');
?>