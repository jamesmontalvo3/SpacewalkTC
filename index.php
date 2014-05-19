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
	      #input-event-date-year {
	      	width: 50px;
	      }
	      #input-event-date-day {
	      	width: 40px;
	      }
	      textarea {
	      	width: 100%;
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

		<!-- View-EventListViewItem -->
		<script type="text/template" id="View-EventListViewItem">
			<a href="#" class="event-name"><%= event.name %></a> - <%= event.revision.date %>
		</script>

		<!--  View-EventView  -->
		<script type="text/template" id="View-EventView">
			<h2><%= event.name %></h2>
			<ul style="list-style-type:none;">
				<li><label>Date:</label> <%= event.revision.gmt_date %></li>
				<li><%= event.revision.version %></li>
				<li><%= event.revision.jedi %></li>
				<li><%= event.revision.revision_ts %></li>
				<li><%= event.revision.user_id %></li>
			</ul>
			<p><%= event.revision.overview %></p>
			<p><%= event.revision.items_json %></p>
		</script>

		<!--  View-EventEditView  -->
		<script type="text/template" id="View-EventEditView">
			<h2><%= event.name %></h2>
			<ul style="list-style-type:none;">
				<li>
					<span>Date:</span> <input type="text" id="input-event-date-year" class="gmt-date" value="<%= event.revision.year %>" />
					<!-- slash between year and day--> /
					<input type="text" id="input-event-date-day" class="gmt-date" value="<%= event.revision.day %>" />
				</li>
				<li><span>Message #:</span> <input type="text" id="input-event-jedi" class="simple-input" value="<%= event.revision.jedi %>" /></li>
			</ul>
			<h3>Overview</h3>
			<p><textarea id="input-event-overview" class="simple-input"><%= event.revision.overview %></textarea></p>
			<h3>Tool Configuration</h3>
			<p><textarea id="input-event-items-json" class="simple-input"><%= event.revision.items_json %></textarea></p>
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