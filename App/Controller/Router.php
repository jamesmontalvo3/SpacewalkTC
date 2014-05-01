<?php

namespace Spacewalk\Controller;


class Router {

	static public function route ($controller, $param) {

		switch ($controller) {
			case "Event":
				require "App/Controller/Event.php";
				return new \Spacewalk\Controller\Event();
				break;
			case "Revision":
				require "App/Controller/Event.php";
				return new \Spacewalk\Controller\Event();
				break;
			default:
				
		}

	}

}