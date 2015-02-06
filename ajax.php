<?php
session_start();
require_once('inc/logout.php');
require_once('inc/functions.php');
$FP=new Swiatlowody;

if(isset($_G -> auth) && isset($_G -> userID) && isset($_G -> userName))
	$FP -> login($_G -> auth, time(), $_G -> userID, $_G -> userName);
else
	$FP -> login();

if(isset($_G -> modul) && isset($_G -> co))
	require('./modules/'.$_G -> modul.'/ajax/'.$_G -> co.'.php');



