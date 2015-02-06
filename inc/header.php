<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="utf-8" />
	<style type="text/css">
		*{
			text-rendering: optimizeLegibility;
			-webkit-font-smoothing: antialiased;
			font-style: normal;
			font-family: 'Helvetica Neue', 'TeX Gyre Heros', Arial, sans-serif;
			line-height: 15px;
			letter-spacing: 0.4px;
			text-rendering: optimizeLegibility;
		}
		HTML{
			height: 100%;
			}
		BODY{
			font-size: 14px;
			margin: auto;
			height: 100%;
			width: 100%;
			font-weight: normal;
			
		}
		TABLE{
			border-collapse:collapse;
			width: 100%;
			max-width: 100%;
			
		}
		TD, TH{
			
			text-align: center;
			min-width: 100px;
			padding: 2px;
			border-bottom: 1px solid #D7D7D7;
			height: 25px;
			
		}
		INPUT, SELECT{
			text-align: center;
			background-color: inherit;
		}
		
		INPUT{
			border: 1px solid #656565;
		}
		INPUT[type=submit]{
			border: 1px solid #333;
			font-weight: bold;
		}
		INPUT .color{
		background-color: blue;
		}
		SELECT{
			border: 0,5px solid #656565;
			font-weight: bold;
		}
		
		H3{
			color: #000;
			line-height: 25px;
		}

		.bezramki{
			border: 0px;
		}
		#lewa{
			position: fixed;
			background-color: #E4E6E8;
			width: 230px;
			height: 100%;
			color: #333333;
			border-right: 1px solid #C2C4C5;
			top: 0px;
		}


		#lewa .menu{
			padding-top: 10px;
			padding-left: 22px; 
			height: 25px;
			vertical-align: middle;
			font-weight: normal;
			
		}
		#lewa A .menu:hover{
			background: #ddd;
			color: #777;
		}


		#lewa A{
			color: #333;

		}
		#lewa B, #lewa H1{
			font-weight: bold;
			
		}
		#lewa H1{
			color: #7a7b94;

		}
		#prawa{
			margin-left: 230px;
			background-color: #FFF;
			
			height: 100%;
			float: left;
			color: #000;
		}
		#autor{
			position: fixed;
			bottom: 0px;
			left: 128.5px;
			color: #777;
			font-size: 9.5px;
		}

		A{
			color: #000;
			text-decoration: none;
		}
		A:hover{
			color: #999;
		}
		.komunikat{
			font-size: 13px;
			font-weight: bold;
			text-align: center;
			min-width: 100px;
			padding: 20px;
			color: #fff;
			max-width: 50%;
			margin-top: 5px;
			margin-bottom: 15px;
			margin-left: 25%;
		}
		.komunikatOK{
			border: 1px solid green;
			background-color: #33CC66;
		}
		.komunikatBLAD{
			border: 1px solid red;
			background-color: #FC5350;
		}
		.komunikat A{
			color: #fff;
		}
		.dodaj{
			color: #0083ff;
			font-weight: bold;	
		
		}
		.zmin{
			display: inline;
			color: #555;
			font-weight: bold;
		}
		.usun{
			display: inline;
			color: #FC5350;
		}
		#tech{
			padding-left: 2px;
			font-size: 8px;
		}
		.doLewej TD{
			text-align: left;
			padding-left: 20px;
		}
		.doSrodka{
			text-align: center;
		}
		.instrukcja{
			max-width: 90%;
			border: 1px solid #999;
			margin: 15px;
			display: block;
			margin-left: auto;
			margin-right: auto;
			-webkit-box-shadow: 0px 3px 16px 0px rgba(50, 50, 50, 1);
			-moz-box-shadow:    0px 3px 16px 0px rgba(50, 50, 50, 1);
			box-shadow:         0px 3px 16px 0px rgba(50, 50, 50, 1);
		}
		#chowacz{	
			position: fixed;
			z-index: 10;
			text-shadow: -1px 0 white, 0 1px white, 1px 0 white;
			margin-left:217px;
			background-color: #E4E6E8;
			font-size: 25px;
			color: #999;
			top: 50%;
			padding: 0px;
			width: 12.5px;
			height: 22px;
			transform: translateY(-50%);
			-webkit-transform: translateY(-50%);
		}
		#chowacz:hover{
			color: blue;
		}
	</style>
	<title>Światłowody</title>
	<script src="inc/jquery-2.1.1.min.js"></script>
	<script type="text/javascript" src="inc/jquery.metadata.js"></script> 
	<script type="text/javascript" src="inc/jquery.tablesorter.min.js"></script> 
	<script src="inc/functions.js"></script>
	<script src="inc/functions.GoogleMaps.js"></script>
	<link href="inc/select2.css" rel="stylesheet"/>
    <script src="inc/select2.min.js"></script>
	<link href='inc/czcionka.css' rel='stylesheet' type='text/css'>
	<link rel="shortcut icon" href="//www.gstatic.com/mapspro/images/favicon-001.ico">
	<script>

	jQuery(document).ready(function($) {
			
			$('#prawa').width($(window).width() - $('#lewa').width());
			pokoloruj_wiersze();
			$( window ).resize(function() {
				$('#prawa').width($(window).width() - $('#lewa').width());

			});
			$('#'+getUrlParameter('modul')).css('background-color', '#D6D8DB');
	});

	
	</script>
	<script src="inc/rgbcolor.js"></script><!-- http://www.phpied.com/rgb-color-parser-in-javascript/ -->

</head>
<body>