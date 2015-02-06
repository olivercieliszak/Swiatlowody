<?php
error_reporting(E_ALL);



//Zabezpieczenie - eliminacja niebezpiecznych i zbednych danych
function czysc($input){

	if(is_array($input)){
		$return = array();
		foreach($input as $key => $value){
			$return[czysc($key)] = czysc($value);
		}
	}
	else
		$return = trim(htmlspecialchars($input));
	
	return $return;
}
foreach($_REQUEST as $nazwa => $wartosc){
	$_REQUEST[$nazwa] = czysc($wartosc);
}
$_R = (object) $_REQUEST;

foreach($_GET as $nazwa => $wartosc){
	$_GET[$nazwa] = czysc($wartosc);
}
$_G = (object) $_GET;

$_P2 = (object) $_POST;

foreach($_POST as $nazwa => $wartosc){
	$_POST[$nazwa] = czysc($wartosc);
}
$_P = (object) $_POST;

unset($_REQUEST,$_GET,$_POST);

foreach($_COOKIE as $nazwa => $wartosc){
	$_COOKIE[$nazwa] = czysc($wartosc);
}
$_C = (object) $_COOKIE;
unset($_COOKIE);

if(isset($__GET -> modul))
	define('NAZWA_MODULU', $__REQUEST -> modul);
else
	define('NAZWA_MODULU', 'Błąd');

function sqlizer($in){

	return '"'.$in.'"';

}

function print_rr($input){
	echo '<pre>';
	print_r($input);
	echo '</pre><hr>';
}

	
class Swiatlowody {
	
	//zmienne globalne dla baz danych
	private $db;
	private $dbi;
	public $iloscZapytan = 0;
	public $timeAll;
	public $timeSQL;
	public $dbHOST;
	public $dbUSER;
	public $dbPASS;
	public $dbNAME;
	public $koloryPunktow = array(
	'niebieska' => '#6991FD',
	'czerwona' => '#FD7567',
	'zolta' => '#FDF569',
	'fioletowa' => '#8E67FD',
	'rozowa' => '#E661AC',
	'zielona' => '#00E64D',
	'czarna' => '#000000',
	'biala' => '#FFFFFF'
	);
	public $userID;
	public $userName;
	public $kluczDoAPI;
	
	
	//konstruktor
	function Swiatlowody(){
	
		$this -> timeAll = microtime(true);
		include('./inc/login.inc.php');
		mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
		$this -> dbHOST = $dbHOST;
		$this -> dbUSER = $dbUSER;
		$this -> dbPASS = $dbPASS;
		$this -> dbNAME = $dbNAME;
		$this -> kluczDoAPI = $klucz_do_api;
		try{
		
			$this -> db = new mysqli($this -> dbHOST, $this -> dbUSER,  $this -> dbPASS, $this -> dbNAME);
		
		}
		catch(mysqli_sql_exception $e) {
			echo $this -> komunikat(false, false, 'Błąd połączenia z bazą danych:<br><br>'. $e -> getMessage());
		}

	}
	function login($auth = false, $time = false, $userID = false, $userName = false){
		$err = false;
		$loginAPI = false;

		//jeżeli sesja potwierdzi wcześniejsze zalogowanie, usuwamy błędy
		if(isset($_SESSION['ok']) && $_SESSION['ok'] == 1){	
			$err = false;
			$this -> userName = $_SESSION['userName'];
			$this -> userID = $_SESSION['userID'];
			
		}
		else{
			//sprawdzenie czy czas miedzy serwerami sie zgadza
			if(time() - (int)$time > 5){
				$err = true; 
				$this -> log('Wystąpił błąd przy autoryzacji zdalnej - błąd testu czasu');

			}
			//sprawdzenie czy klucz sie zgadza
			else if($auth !== sha1($this -> kluczDoAPI.$time.$userID.$userName)) {
				$err = true;
				$this -> log('Wystąpił błąd przy autoryzacji zdalnej - błąd wymiany kluczy');
			}
			else
				$loginAPI = true;
		}


		if($err == false){
			$_SESSION['ok'] = 1;

			if($loginAPI){
				$_SESSION['userID'] = $userID;
				$_SESSION['userName'] = $userName;
				$this -> userID = $userID;
				$this -> userName = $userName;
				$this -> log('Zalogowano');	

				}
		}
		else{
			$this -> log('Błąd logowania');
			echo $this -> komunikat(false, false, 'Zdalne logowanie zakończyło się niepowodzeniem');
			exit();
		}
	
	}
	function db_q($q, $debug = false, $noRun = false){
		try{
			$tmpTimeSQL = microtime(true);

			if($noRun != true){
				$query = $this-> db -> query($q);
				$this -> iloscZapytan++;
			}
			if($debug == true)
				echo $q.'<br>';
			
			$this -> timeSQL += microtime(true) - $tmpTimeSQL;
			return $query;
		}
		catch(mysqli_sql_exception $e) {
			echo $this -> komunikat(false, false, 'Wystąpił błąd w zapytaniu:<br><b>'.$q.'</b><br><br>Opis błędu:<br><b>'. $this -> db -> error.'</b>');
			$this -> log('Wystąpił błąd w zapytaniu:<br><b>'.$q.'</b><br><br>Opis błędu:<br><b>'. $this -> db -> error.'</b>');
		}
	}
	function db_iq($q, $debug = false){
	
		$return = $this -> db_q($q, $debug);
		
		if($debug == true){
			print_rr($q);
			print_rr($return);
		}
		
		return $this -> db -> insert_id;	
	
	}
	function db_sq($q, $debug = false){
	
		$return = $this -> db_q($q, $debug) -> fetch_object();
		
		if($debug == true){
			print_rr($return);
		}
		
		return $return;
	}
	function pobierzIDKabla($kabelWloknoID){
	
		return @$this -> db_sq('SELECT kabelID from kabelWlokno WHERE kabelWloknoID = "'.$kabelWloknoID.'"') -> kabelID;
	
	}

	function pobierzPunktIDzWlokna($kabelWloknoID){
	
		return $this -> db_sq('SELECT punktIDStart, punktIDKoniec from kabel WHERE kabelID = "'.$kabelID = $this -> pobierzIDKabla($kabelWloknoID).'"');
	
	}
	
	function pobierzTypPunktu($punktID){
	
		return $this -> db_sq('SELECT punktTypID FROM punkt WHERE punktID = "'.$punktID.'"') -> punktTypID;
	
	}
	function gps($punkt){
		$punkt = explode(',', $punkt);
		$return = array('lat' => $punkt[0], 'lon' => $punkt[1]);
		return (object)$return;
	}
	function odlegloscMiedzyPunktami($punkt1, $punkt2){
		$punkt1 = $this -> gps($punkt1);
		$punkt2 = $this -> gps($punkt2);		
		$theta = $punkt1 -> lon - $punkt2 -> lon;
		$dist = sin(deg2rad($punkt1 -> lat)) * sin(deg2rad($punkt2 -> lat)) +  cos(deg2rad($punkt1 -> lat)) * cos(deg2rad($punkt2 -> lat)) * cos(deg2rad($theta));
		$dist = acos($dist);
		$dist = rad2deg($dist);
		$odleglosc = $dist * 111189.57696;
		$odleglosc = round($odleglosc, 2);
		return $odleglosc;
	}
	
	function dlugoscKabla($kabelID){
	
		$dlugosc = 0;
		$iloscPunktow = $this -> db_sq('SELECT COUNT(*) as ilosc FROM kabelPunkt WHERE kabelID = "'.$kabelID.'"') -> ilosc;
		if($iloscPunktow > 1){
		
			$zapytanie2 = $this -> db_q('SELECT gps FROM kabelPunkt NATURAL LEFT JOIN punkt WHERE kabelID = "'.$kabelID.'" ORDER BY kolejnosc ASC');
			$i = 0;
			while($wynik2 = $zapytanie2 -> fetch_object()){
					
					if($i >= 1){
					
						$dlugosc += $this -> odlegloscMiedzyPunktami($poprzedniPunkt -> gps, $wynik2 -> gps);
						
					}
					$poprzedniPunkt = $wynik2;
					$i++;
					
			}
		}
		//jezeli nie ma podanych punktow posrednich
		else{
			$kabelDane = $this -> db_sq('SELECT punktIDStart, punktIDKoniec FROM kabel WHERE kabelID = "'.$kabelID.'"');
			$i = 0;

			foreach($kabelDane as $punkt){

				if($i >= 1){
					$dlugosc += $this -> odlegloscMiedzyPunktami($this -> db_sq('SELECT gps FROM punkt WHERE punktID = "'.$poprzedniPunkt.'"') -> gps , $this -> db_sq('SELECT gps FROM punkt WHERE punktID = "'.$punkt.'"') -> gps);
				}
				$poprzedniPunkt = $punkt;
				$i++;
			}
		
		}
	
		return $dlugosc;
		
	}
	
	function ustalKoniec($return){
		
		

		//1. mamy id wlokna
		//2. ustalamy czy jest gdzies zaspawane w mufie
		//2a. jezeli tak to wracamy do poczatku
		//2b. jezeli nie - zwracamy koniec
		
		//pozniej:
		//ilosc skokow po drodze
		//co po drodze
		//mozemy wejsc jako id2 albo id1
		$nastepnyID = 0;
		$return['odleglosc']++;
		$return['trasa'][$return['kabelWloknoID']]['odleglosc'] = $return['odleglosc'];
	
	//pierwszy przebieg petli jest tutaj
		if(empty($return['punkty'])){
			$kabelWloknoID = $this -> pobierzPunktIDzWlokna($return['kabelWloknoID']);
			$return['punkty'][$return['punktIDStart']] = $return['odleglosc'];
			$return['punkty'][$kabelWloknoID -> punktIDKoniec] = $return['odleglosc'];
			$return['punkty']['ostatni'] = $kabelWloknoID -> punktIDKoniec;

			//jezeli zadany przez nas poczatek nie jest poczatkiem wlokna, musimy odwrocic strony wlokna zeby znalezc punkt niepoczatkowy relacji
			if($return['punktIDStart'] != $kabelWloknoID -> punktIDStart){
				$return['punkty'][$kabelWloknoID -> punktIDStart] = $return['odleglosc'];
				$return['punkty'][$kabelWloknoID -> punktIDKoniec] = $return['odleglosc'];
				$return['punkty']['ostatni'] = $kabelWloknoID -> punktIDStart;
			}

			$return['trasa'][$return['kabelWloknoID']]['punkty'] = $kabelWloknoID;	
		}
		
		//gdy odleglosc = 1 i mamy 2 rekordy z bazy to znaczy ze wpychamy sie w istniejaca relacje a tego nie chcemy...
		$zapytanieSQL = 'SELECT mufaID, kabelID1, kabelWloknoID1, kabelID2, kabelWloknoID2 FROM mufaSpaw WHERE kabelWloknoID1 = "'.$return['kabelWloknoID'].'" OR kabelWloknoID2 = "'.$return['kabelWloknoID'].'"';
		if($this -> db_q($zapytanieSQL) -> num_rows > 1 && $return['odleglosc'] < 2){
			$return['punktIDKoniec'] = 0;
			$return['srodek'] = 1;
			return $return;
		}
		else{
			//sprawdzamy czy wlokno nie idzie do kolejnej mufy
			//w tym celu odpytujemy mufy czy nie ma naszego wlokna w jakiejkolwiek mufie (na pozycji 1 lub 2)
				$zapytanie = $this -> db_q($zapytanieSQL);
				while($wynik = $zapytanie -> fetch_object()){
					
					//zapisujemy wynik zapytania do historii zeby moc przesledzic trase
					$return['trasa'][$return['kabelWloknoID']]['wynik'] = $wynik;

					$kabelWloknoID1 = $this -> pobierzPunktIDzWlokna($wynik -> kabelWloknoID1);
					$kabelWloknoID2 = $this -> pobierzPunktIDzWlokna($wynik -> kabelWloknoID2);

					//sprawdzamy czy punkty znalezionego KOLEJNEGO wlokna zgadzaja sie z poprzednimi uwzgledniajac pozycje STARTOWĄ				
					//jezeli znalezione wloknoID na pozycji 2 jest rozne od naszego zrodlowego wlokna
					
					if(
					$wynik -> kabelWloknoID1 != $return['kabelWloknoID']
					&& @($kabelWloknoID1 -> punktIDStart == $return['punkty']['ostatni'] || $kabelWloknoID1 -> punktIDKoniec == $return['punkty']['ostatni'])
					)
						$nastepnyID = $wynik -> kabelWloknoID1;
					else if(
					$wynik -> kabelWloknoID2 != $return['kabelWloknoID']
					&& ($kabelWloknoID2 -> punktIDStart == $return['punkty']['ostatni'] || $kabelWloknoID2 -> punktIDKoniec == $return['punkty']['ostatni'])
					)
						$nastepnyID = $wynik -> kabelWloknoID2;

				
					//zabezpieczenie przed zbyt dluga trasa
					if($return['odleglosc'] > 10){
						$return['error'] = 'Zbyt długa trasa!';
						return $return;
					}
					//gdy znalezione zostalo kolejne wlokno polaczone z naszym wloknem
					if($nastepnyID > 0){
					
						$kabelWloknoID = $this -> pobierzPunktIDzWlokna($nastepnyID);
						$return['trasa'][$nastepnyID]['punkty'] = $kabelWloknoID;

						//zapisujemy odleglosc w ktorej od poczatku jest nasze wlokno
						$return['trasa'][$nastepnyID]['odleglosc'] = $return['odleglosc'];
						//ustamay nastepne wloknoID do sprawdzenia trasy...
						$return['kabelWloknoID'] = $nastepnyID;
						//zeby ocenic jaki punkt ma byc naszym startowym, powinnismy wiedziec jaki byl ostatnio dodany. na tej podstawie mozemy
						//zalozyc ze ostatnio dodany punkt bedzie punktem startowym kolejnego wlokna
						if($return['punkty']['ostatni'] == $kabelWloknoID -> punktIDStart)
							$return['punkty'][$kabelWloknoID -> punktIDStart] = $return['odleglosc'];
						else
							$return['punkty'][$kabelWloknoID -> punktIDKoniec] = $return['odleglosc'];
						
						if($kabelWloknoID -> punktIDStart == $return['punkty']['ostatni'])
							$return['punkty']['ostatni'] = $kabelWloknoID -> punktIDKoniec;
							
						else if($kabelWloknoID -> punktIDKoniec == $return['punkty']['ostatni'])
							$return['punkty']['ostatni'] = $kabelWloknoID -> punktIDStart;
						
						//...i odpalamy rekurencje zeby je szukac dalej
					
						return $this -> ustalKoniec($return);
					}
				}

			//jezeli nie ma juz zadnej mufy dalej, sprawdzmy w jakim punkcie konczy sie to wlokno
			if($nastepnyID == 0){
			
				$return['punktIDKoniec'] = $return['punkty']['ostatni'];
				
				return $return;
			}	
		}

		
	}
	
	function kolor($co, $id, $debug = false){
		$co = ucfirst($co);
		
		return $this -> db_sq('SELECT kolor, kolorHTML FROM kolor'.$co.' WHERE kolor'.$co.'ID = "'.$id.'"', $debug);
	
	}
	function pobierzPunkt($punktID, $debug = false){
		
		$q = $this -> db_sq('SELECT typ, opis FROM punkt NATURAL LEFT JOIN punktTyp WHERE punktID = "'.czysc($punktID).'"', $debug);
		$return = $q -> opis.' <br>('.$q -> typ.' - punkt nr ' . $punktID.')';
		return $return;
	
	}
	function pobierzSkroconyPunkt($punktID, $debug = false){
		
		$q = $this -> db_sq('SELECT typ, opis FROM punkt NATURAL LEFT JOIN punktTyp WHERE punktID = "'.czysc($punktID).'"', $debug);
		$return = $q -> opis.' <br>(Punkt nr ' . $punktID.')';
		return $return;
	
	}
	function pobierzRelacjeLogicznaWlokna($kabelWloknoID){
	
		$return = $this -> db_sq(
		'SELECT * FROM relacja
		RIGHT JOIN relacjaWlokno ON relacja.relacjaID = relacjaWlokno.relacjaID
		WHERE kabelWloknoID = "'.$kabelWloknoID.'"'
		);
		if(!empty($return))
			return 'Relacja nr '.$return -> relacjaID.'<br><b>Od:</b> '.$this -> pobierzPunkt($return -> punktIDStart).'<br><b>Do:</b> '.$this -> pobierzPunkt($return -> punktIDKoniec).'<br><br>'.$return -> opis.'';
		else
			return false;
	}
	
	function pobierzRelacjeKabla($kabelID,$br = false){
		//pobiera punkty poczatkowe i koncowe kabla na podstawie jego ID i zwraca je w przyjaznej tresci
		$zapytanie = $this -> db_sq('SELECT punktIDStart, punktIDKoniec FROM kabel WHERE kabelID  = "'.$kabelID.'"');

		$punktIDStart = $this -> pobierzPunkt($zapytanie -> punktIDStart);
		$punktIDKoniec = $this -> pobierzPunkt($zapytanie -> punktIDKoniec);
		if($br == true)
			$return = 'Kabel nr: <b>'.$kabelID.'</b><br>Od: <b>'. $punktIDStart .'</b><br>Do: <b>'. $punktIDKoniec.'</b>';
		else
			$return = $kabelID.': '. $punktIDStart .' - '. $punktIDKoniec;
		
		return $return;
	
	}
	function koloryPunktowTlo($kolor){
	
		if($kolor == "")
			$kolor = 'czerwona';
		
		return $this -> koloryPunktow[$kolor];
	
	}
	function koloryPunktowTekst($kolor){
	
		if($kolor == "")
			$kolor = 'czerwona';
		
		return $this -> znajdzKolor($this -> koloryPunktow[$kolor]);
	
	}
	function pobierzKoloryTypowPunktowDoSelecta($defaultValue = false){
		
		$return = '<select name="kolorPunkt" id="kolorPunkt" onChange="zmienKolor()">';

		foreach ($this -> koloryPunktow as $nazwaKoloru => $kolorHTML){
		
		if($defaultValue != false && $defaultValue == $nazwaKoloru)
			$return .= '<option value="'.$nazwaKoloru.'"  style="background-color: '.$kolorHTML.'; color: '.$this -> koloryPunktowTekst($nazwaKoloru).'" selected="selected">'.$nazwaKoloru.'</option>';
		else
			$return .= '<option value="'.$nazwaKoloru.'" style="background-color: '.$kolorHTML.'; color: '.$this -> koloryPunktowTekst($nazwaKoloru).'">'.$nazwaKoloru.'</option>';		
		}
		
		$return .= '</select>';
		$nazwaForm = 'kolorPunkt';
		$return .= "
		<script>
		function zmienKolor(){
	
	$('#".$nazwaForm."').css('background-color', $('#".$nazwaForm."').children(':selected').css('background-color'));
	$('#".$nazwaForm."').css('color', znajdzKolor($('#".$nazwaForm."').children(':selected').css('background-color')));
	
	}
	zmienKolor();
	</script>";

		return $return;
	
	}
	function pobierzKableDoSelecta($nazwaForm, $defaultValue = false, $multiple = false, $where = '1'){
	
		if($multiple)
			$return = '<select name="'.$nazwaForm.'" id="'.$nazwaForm.'" multiple>';
		else
			$return = '<select name="'.$nazwaForm.'" id="'.$nazwaForm.'">';
		
		$zapytanie = $this -> db_q('SELECT kabelID FROM kabel WHERE '.$where.' ORDER BY kabelID ASC');
		while($wynik = $zapytanie -> fetch_object()){
			if($defaultValue != false && $defaultValue == $wynik -> kabelID)
				$return .= '<option value="'.$wynik -> kabelID.'" selected="selected">'.$this -> pobierzRelacjeKabla($wynik -> kabelID).'</option>';
			else
				$return .= '<option value="'.$wynik -> kabelID.'">'.$this -> pobierzRelacjeKabla($wynik -> kabelID).'</option>';

		}
		$return .= '</select>';
		
		return $return;
	
	
	}
	function pobierzPunktyDoSelecta($defaultValue = false,$where = 1){
		$return = '<select name="punktID" id="punktID">';
		$zapytanie = $this -> db_q('SELECT punktID, typ, kolorPunkt, opis, gps FROM punkt NATURAL LEFT JOIN punktTyp WHERE punkt.punktTypID > 2 AND "'.$where.'" ORDER BY punktID ASC');
		while($wynik = $zapytanie -> fetch_object()){
				if($wynik -> opis == '')
					$wynik -> opis = '(bez opisu)';
				
				if($defaultValue != false && $defaultValue == $wynik -> punktID)
					$return .=  '<option value="'.$wynik -> punktID.'" style="background-color: '.$this -> koloryPunktowTlo($wynik -> kolorPunkt).'; color: '.$this -> koloryPunktowTekst($wynik -> kolorPunkt).';" selected>'.$wynik -> punktID.' - '.$wynik -> typ.': '.$wynik -> opis.'</option>';
				else
					$return .= '<option value="'.$wynik -> punktID.'" style="background-color: '.$this -> koloryPunktowTlo($wynik -> kolorPunkt).'; color: '.$this -> koloryPunktowTekst($wynik -> kolorPunkt).';">'.$wynik -> punktID.' - '.$wynik -> typ.': '.$wynik -> opis.'</option>';
		}
		$return .= '</select>';	
	
		return $return;
	}
	function pobierzTypyPunktowDoSelecta($nazwaForm = 'punktTypID', $defaultValue = false, $multiple = false, $where = 1){
	
		if($multiple)
			$return = '<select name="'.$nazwaForm.'" id="'.$nazwaForm.'" multiple onChange="zmienKolor()">';
		else
			$return = '<select name="'.$nazwaForm.'" id="'.$nazwaForm.'" onChange="zmienKolor()">';
		
		$zapytanie = $this -> db_q('SELECT typ, punktTypID, kolorPunkt FROM punktTyp WHERE punktTypID > 2 AND "'.$where.'" ORDER BY punktTypID ASC');
		while($wynik = $zapytanie -> fetch_object()){
			if($defaultValue != false && $defaultValue == $wynik -> punktTypID)
				$return .= '<option value="'.$wynik -> punktTypID.'" style="background-color: '.$this -> koloryPunktowTlo($wynik -> kolorPunkt).'; color: '.$this -> koloryPunktowTekst($wynik -> kolorPunkt).';" selected="selected">'.$wynik -> typ.'</option>';
			else
				$return .= '<option value="'.$wynik -> punktTypID.'"style="background-color: '.$this -> koloryPunktowTlo($wynik -> kolorPunkt).'; color: '.$this -> koloryPunktowTekst($wynik -> kolorPunkt).';">'.$wynik -> typ.'</option>';

		}
		$return .= '</select>';
		$return .= "
		<script>
		function zmienKolor(){
	
	$('#".$nazwaForm."').css('background-color', $('#".$nazwaForm."').children(':selected').css('background-color'));
	$('#".$nazwaForm."').css('color', znajdzKolor($('#".$nazwaForm."').children(':selected').css('background-color')));
	
	}
	zmienKolor();
	</script>";
		return $return;
	
	
	}
	function pobierzDoKolorowegoSelecta($co, $skad, $kolumnaWskazujaca, $kolumnaKolorujaca, $nazwaForm, $defaultValue = false, $multiple = false){
	//$kolumnaKolorujaca - ktora kolumna ma byc uzywana do nadawania koloru
		if($multiple)
			$return = '<select name="'.$nazwaForm.'" id="'.$nazwaForm.'" multiple class="multiple">';
		else
			$return = '<select name="'.$nazwaForm.'" id="'.$nazwaForm.'">';
		
		$zapytanie = $this -> db_q('SELECT '.$co.', '.$kolumnaWskazujaca.','.$kolumnaKolorujaca.' from '.$skad.' ORDER BY '.$kolumnaWskazujaca.' ASC');
		while($wynik = $zapytanie -> fetch_object()){
		if($defaultValue != false && $defaultValue == $wynik -> {$kolumnaWskazujaca})
			$return .= '<option value="'.$wynik -> {$kolumnaWskazujaca}.'" selected="selected" style="background-color: '.$wynik -> {$kolumnaKolorujaca}.'; color: '.$this -> znajdzKolor($wynik -> {$kolumnaKolorujaca}).'">'.$wynik -> {$co}.'</option>';
		else
			$return .= '<option value="'.$wynik -> {$kolumnaWskazujaca}.'" style="background-color: '.$wynik -> {$kolumnaKolorujaca}.'; color: '.$this -> znajdzKolor($wynik -> {$kolumnaKolorujaca}).'">'.$wynik -> {$co}.'</option>';
		
		}
		$return .= '</select>';
		
		return $return;
	
	}
	
	
	//metoda ladnie pyta o to co chcemy i nam to zwraca w formie gotowego selecta
	function pobierzDoSelecta($co, $skad, $kolumnaWskazujaca, $nazwaForm, $defaultValue = false, $multiple = false, $showId = false){
		//$co - co ma byc wewnatrz option
		//$skad - tabela
		//$kolumnaWskazujaca - kolumna ktora ma byc uzywana jako klucz do option
		//$nazwaForm - nazwa selecta
		//$defaultValue - domyslna wartosc kolumny wskazujacej
		//$multiple - czy ma byc multiselect
		//$showId - czy ma pokazywac w option Id z kolumny wskazujacej
		if($multiple)
			$return = '<select name="'.$nazwaForm.'" id="'.$nazwaForm.'" multiple>';
		else
			$return = '<select name="'.$nazwaForm.'" id="'.$nazwaForm.'">';
		
		$zapytanie = $this -> db_q('SELECT '.$co.', '.$kolumnaWskazujaca.' from '.$skad.' ORDER BY '.$kolumnaWskazujaca.' ASC');
		while($wynik = $zapytanie -> fetch_object()){
		$selected = '';
		$showIdValue = '';
		if($defaultValue != false && $defaultValue == $wynik -> {$kolumnaWskazujaca})
			$selected = ' selected';
		if($showId == true)
			$showIdValue = $wynik -> {$kolumnaWskazujaca} .': ';

			$return .= '<option value="'.$wynik -> {$kolumnaWskazujaca}.'"'.$selected.'>'.$showIdValue.$wynik -> {$co}.'</option>';
		
		}
		$return .= '</select>';
		
		return $return;
		
	}
	function pobierz($co = '',$limit = '',$sql = ''){

		$wynik = array();
		
		if(empty($co) && empty($sql))
			return false;
		
		
		
		return $wynik;
	}
	
	function komunikat($test = false, $tekstOK = false, $tekstBLAD = false){
		
		if($test === true)
			$return = '<div class="komunikat komunikatOK">'.$tekstOK.'</div>';
		else
			$return = '<div class="komunikat komunikatBLAD">'.$tekstBLAD.'</div>';
	
		return $return;
		
	}
	function log($zdarzenie, $userID = false, $userName = false){
		$userID = $this -> userID;
		$userName = $this -> userName;
		$zdarzenie = htmlspecialchars($zdarzenie);
		$this -> db_q('INSERT INTO log SET czas = NOW(), userID = "'.$userID.'", userName = "'.$userName.'", zdarzenie = "'.$zdarzenie.'", IP = "'.$_SERVER['REMOTE_ADDR'].'"');
	
	}

	
	//
	//
	//
	// stare funkcje:
	//
	//
	//
	
	function skroc_adres_old($temp){ 
		$temp = explode('|', $temp);
		return preg_replace('/[0-9]{2}-[0-9]{3} [^,]+, ?(ul.|al.|pl.)? /i', '', trim($temp[0])); 
	}

	function skroc_adres($temp){ 
		$temp = explode('|', $temp);
		$temp = preg_replace('/[0-9]{2}-[0-9]{3} ([^,]+, ?)(ul.|al.|pl.)? /i', '\1', trim($temp[0])); 
		return $temp; 
	}

	function skroc_gps($gps){
		$gps = explode(',', $gps);
		$gps[0] = round($gps[0],6);
		$gps[1] = round($gps[1],6);
		return $gps[0].','.$gps[1];
	}
	 function zamiana($string){
		$a = array( 'Ę', 'Ó', 'Ą', 'Ś', 'Ł', 'Ż', 'Ź', 'Ć', 'Ń', 'ę', 'ó', 'ą',
		'ś', 'ł', 'ż', 'ź', 'ć', 'ń' );
		$b = array( 'E', 'O', 'A', 'S', 'L', 'Z', 'Z', 'C', 'N', 'e', 'o', 'a',
		's', 'l', 'z', 'z', 'c', 'n' );
		 
		$string = str_replace( $a, $b, $string );
		$string = preg_replace( '#[^a-z0-9]#is', ' ', $string );
		$string = trim( $string );
		$string = preg_replace( '#\s{2,}#', ' ', $string );
		$string = str_replace( ' ', '-', $string );
		$string = strtolower($string);
		return $string;
	}

  function calc_brightness($color) {
    $rgb = $this -> hex2RGB($color);
    return sqrt(
       $rgb["red"] * $rgb["red"] * .299 +
       $rgb["green"] * $rgb["green"] * .587 +
       $rgb["blue"] * $rgb["blue"] * .114);          
  }
 
  function znajdzKolor($color) {
	if($color == '')
		$color = '#fff';
      $brightness = $this -> calc_brightness($color);
      $return = ($brightness < 160) ? "#FFFFFF" : "#000000";
	  return $return;

  }
 
  //http://www.php.net/manual/en/function.hexdec.php#99478
  function hex2RGB($hexStr, $returnAsString = false, $seperator = ',') {
      $hexStr = preg_replace("/[^0-9A-Fa-f]/", '', $hexStr); // Gets a proper hex string
      $rgbArray = array();
      if (strlen($hexStr) == 6) { //If a proper hex code, convert using bitwise operation. No overhead... faster
          $colorVal = hexdec($hexStr);
          $rgbArray['red'] = 0xFF & ($colorVal >> 0x10);
          $rgbArray['green'] = 0xFF & ($colorVal >> 0x8);
          $rgbArray['blue'] = 0xFF & $colorVal;
      } elseif (strlen($hexStr) == 3) { //if shorthand notation, need some string manipulations
          $rgbArray['red'] = hexdec(str_repeat(substr($hexStr, 0, 1), 2));
          $rgbArray['green'] = hexdec(str_repeat(substr($hexStr, 1, 1), 2));
          $rgbArray['blue'] = hexdec(str_repeat(substr($hexStr, 2, 1), 2));
      } else {
          return false; //Invalid hex color code
      }
      return $returnAsString ? implode($seperator, $rgbArray) : $rgbArray; // returns the rgb string or the associative array
  }  
		
	/* backup the db OR just a table */
	//funkcja http://davidwalsh.name/backup-mysql-database-php
	//przerobiona na MySQLi przez Olivera Cieliszaka i dostosowana do systemu Swiatlowody
	function backup_tables($directory = './', $opis = '(brak opisu)', $tables = '*')
	{
		
		//metoda korzysta z metody laczenia do bazy z klasy "Swiatlowody"
		$name = $this -> dbNAME;

		//get all of the tables
		if($tables == '*')
		{
			$tables = array();

			$result = $this -> db_q('SHOW TABLES');
			while($row = $result -> fetch_row())
			{
				$tables[] = $row[0];
			}
		}
		else
		{
			$tables = is_array($tables) ? $tables : explode(',',$tables);
		}
		$return = $opis."\n";
		//cycle through
		foreach($tables as $table)
		{
			if($table != 'log'){
				$result = $this -> db_q('SELECT * FROM '.$table);
				$num_fields = $result -> field_count;
				
				$return.= 'DROP TABLE '.$table.';'."\n";
				$row2 = $this -> db_q('SHOW CREATE TABLE '.$table) -> fetch_row();
				$row2[1] = str_replace("\n", ' ', $row2[1]);
				$return.= $row2[1].";\n";
				
				
				for ($i = 0; $i < $num_fields; $i++) 
				{
					while($row = $result -> fetch_row())
					{
						$return.= 'INSERT INTO '.$table.' VALUES(';
						for($j=0; $j<$num_fields; $j++) 
						{
							$row[$j] = addslashes($row[$j]);
							$row[$j] = str_replace("\n","\\n",$row[$j]);
							if (isset($row[$j])) { $return.= '"'.$row[$j].'"' ; } else { $return.= '""'; }
							if ($j<($num_fields-1)) { $return.= ','; }
						}
						$return.= ");\n";
					}
				}
			}
		}
		
		//save file
		$handle = fopen($directory.$name.'_'.date('Y-m-d_H-i-s').'.sql','w+');
		fwrite($handle,$return);
		fclose($handle);
		$this -> log('Utworzono kopię zapasową "'.$opis.'"');
		return date('Y-m-d H:i:s');
	}
	function szukajKoncaKabli($data){
	

	$punktID = $data['punktIDStart'];
	echo $punktID;
	if(!isset($data['counter']))
		$data['counter'] = 0;
	if(!isset($data['archiwum']))
		$data['archiwum'][$punktID] = 1;

	$zapytanie = $this -> db_q('SELECT kabelID, punktIDStart, punktIDKoniec FROM kabel WHERE punktIDStart = "'.$data['punktIDStart'].'" OR punktIDKoniec = "'.$data['punktIDStart'].'"',1);
	
	while($wynik = $zapytanie -> fetch_object()){
			
			//mamy liste wszystkich kabli
			//po kolei sprawdzamy przebieg kazdego kabla
			//print_rr($wynik);
			//jezeli poczatek sie zgadza to koniec tez bedzie sie zgadzac
			if($punktID == $wynik -> punktIDStart){
				$dataTmp['kable'][$wynik -> kabelID]['punktIDStart'] = $wynik -> punktIDStart;
				$dataTmp['kable'][$wynik -> kabelID]['punktIDKoniec'] = $wynik -> punktIDKoniec;
			}
			else if($punktID == $wynik -> punktIDKoniec){
				$dataTmp['kable'][$wynik -> kabelID]['punktIDStart'] = $wynik -> punktIDKoniec;
				$dataTmp['kable'][$wynik -> kabelID]['punktIDKoniec'] = $wynik -> punktIDStart;
			}
			
			$data['punktIDStart'] = $dataTmp['kable'][$wynik -> kabelID]['punktIDKoniec'];
			
			if($data['nieRuszac']['kabelID'] == 0)
				$data['nieRuszac']['kabelID'] = $wynik -> kabelID;
			
			if($data['counter']++ > 10)
				return $data;
			print_rr($data);
			if(!array_key_exists($data['punktIDStart'], $data['archiwum'])){
				
				$this -> szukajKoncaKabli($data);
			
			}
				
		}
		return $data;
		
		//function 
	
	}
	
	
	
	
}