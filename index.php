<!DOCTYPE html>
<html>
	<head>
		<meta charset=”utf-8”> 
		<title>SpacewalkTC</title>
		<link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap.css">
	    <style>
	      body {
	        padding-top: 60px; /* 60px to make the container go all the way to the bottom of the topbar */
	      }
	    </style>
	  <!--  <link href="../assets/css/bootstrap-responsive.css" rel="stylesheet"> -->

	</head>
	<body>

		<div class="navbar navbar-inverse navbar-fixed-top">
			<div class="navbar-inner">
			<div class="container">
			<button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a class="brand" href="#">SpacewalkTC</a>
			<div class="nav-collapse collapse">
				<ul class="nav">
					<li class="active"><a href="#">Events</a></li>
					<li><a href="#about">About</a></li>
					<li><a href="#contact">Contact</a></li>
				</ul>
			</div><!--/.nav-collapse -->
			</div>
			</div>
		</div>

		<div class="container" id="container">


		</div> <!-- /container -->

		<!-- Templates -->
		<script type="text/template" id="this-is-a-test">
			<a href="#" class="event-name"><%= event.name %></a>
		</script>


		<script src="http://code.jquery.com/jquery-1.10.1.min.js"></script>
		<script src="bootstrap/js/bootstrap.js"></script>
		<script src="App/Libs/json2.js"></script>
		<script src="App/Libs/underscore-min.js"></script>
		<script src="App/Libs/backbone-min.js"></script>
		<script src="App/main.js"></script>
	</body>
</head><?php

// ini_set('display_errors',1);
// error_reporting(-1);

// require 'vendor/autoload.php'; // @TODO: do we want to load slim for this?
// require 'App/config.php';

?>