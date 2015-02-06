<?php	

//////////////
//
// modul wylogowania
//
//////////////
if(isset($_REQUEST['wyloguj'])){
	if($_REQUEST['wyloguj'] == 1){
		$_SESSION['ok'] = 0;
		$_SESSION['userID'] = 0;
		$_SESSION['userName'] = 0;
	//	exit(print_r($_SESSION));
		header('Location: ./');
	}
}



