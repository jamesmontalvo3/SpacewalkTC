<?php

ini_set('display_errors',1);
error_reporting(-1);

require 'vendor/autoload.php';
require 'App/config.php';

// require 'App/Database/rb.phar';
// R::setup($swalk_dbstring, $swalk_dbuser, $swalk_dbpass);
// R::freeze( true );



// models that need to be properly included in controller files or be handled
// by an autoloader...
// require_once "App/Model/Model_Event.php";
// require_once "App/Model/Model_Revision.php";


$app = new \Slim\Slim(array(
	"debug" => true,
	'log.enabled' => true
));

$app->get('/', function() {
	echo "<h1>Welcome to the SpacewalkTC API</h1><p>This API is still under construction</p>";
});

// @TODO: this should move to a helpers class or something...
function getController ($controller) {
	require_once "App/Controller/$controller.php";
	$ControllerClass  = "\\Spacewalk\\Controller\\$controller";
	$reflection = new \ReflectionClass($ControllerClass);
	return $reflection->newInstance(); 
}



/* * * * * * *
 *  ROUTERS  *
 * * * * * * */

/**
 *  EVENT
 **/
$app->group('/event', function () use ($app) {


	/**
	 * GET LIST OF EVENTS
	 * @url: /api.php/event/
	 * @method: GET
	 * @todo: IMPLEMENT
	 * @todo: need limits and ASC/DESC added
	 **/
	$app->get('/', function () {
		// echo getController($controller)->index();
		echo "GET LIST OF EVENTS";
	});

	/**
	 * GET DATA ABOUT AN EVENT
	 * @url: /api.php/event/:id
	 * @method: GET
	 * @todo: IMPLEMENT
	 *
	 * @querystring:
	 *     eventinfo = false        => optional, default true, returns rev info only
	 *     revision = multiple pipe separated
	 *         latest      => most recent rev, whether official or draft
	 *         published   => get the "official" rev
	 *         <int>       => get rev with id = <int>
	 *         history     => all revs
	 *             history_order   => ASC or DESC (default DESC, most recent)
	 *             history_limit   => <int> (default 10)
	 *             history_before_id => <rev ID> (default null) finds revs prior to <rev ID>
	 *             history_after_id  => <rev ID> (default null) finds revs after <rev ID>
	 *             history_before_ts => <yyyymmddhhmmss> (default null) finds rev after timestamp
	 *             history_after_ts  => <yyyymmddhhmmss> (default null) finds rev after timestamp
	 *         new         => all revs since published
	 */
	$app->get('/:id', function ($id) {
		// echo getController($controller)->index();
		echo "GET DATA ABOUT EVENT $id";
	});


	/**
	 * CREATE AN EVENT
	 * @url: /api.php/event/
	 * @method: PUT
	 * @todo: IMPLEMENT
	 * 
	 * user sends data for new event, returns event ID and no revision info (blank rev?)
	 **/
	$app->put('/', function () {
		// echo getController($controller)->index();
		echo "CREATE AN EVENT";
	});


	/**
	 * UPDATE EVENT
	 * @url: /api.php/event/:id
	 * @method: PUT
	 * 
	 * updates event info, creates new revision row if different from previous
	 * @todo: IMPLEMENT
	 **/
	$app->put('/:id', function ($id) {
		// echo getController($controller)->index();
		echo "UPDATE EVENT $id";
	});



	/**
	 * RELEASE AN EVENT
	 * @url: /api.php/event/:id/release/:revid
	 * @method: PUT
	 * 
	 * marks :revid as newly released revision
	 * @todo: IMPLEMENT
	 **/
	$app->put('/:id/release/:revid', function ($id,$revid) {
		// echo getController($controller)->index();
		echo "RELEASE revision $revid of event $id";
	});


	/**
	 * UN-RELEASE AN EVENT
	 * @url: /api.php/event/:id/unrelease
	 * @method: PUT
	 * 
	 * (privileged) removes "released" from most recent released rev
	 * @todo: IMPLEMENT
	 **/
	$app->put('/:id/unrelease', function ($id) {
		// echo getController($controller)->index();
		echo "UNRELEASE event $id";
	});


	/**
	 * DELETE AN EVENT
	 * @url: /api.php/event/:id
	 * @method: DELETE
	 * 
	 * (privileged) marks set `status` to 'deleted'
	 * @todo: IMPLEMENT
	 **/
	$app->delete('/:id', function ($id) {
		// echo getController($controller)->index();
		echo "DELETE event $id";
	});


	/**
	 * DELETE AN EVENT
	 * @url: /api.php/event/:id/undelete
	 * @method: DELETE
	 * 
	 * (privileged) marks set `status` to 'deleted'
	 * @todo: IMPLEMENT
	 * @todo: Is this a good way to undelete?
	 **/
	$app->delete('/:id/undelete', function ($id) {
		// echo getController($controller)->index();
		echo "UN-DELETE event $id";
	});

}); // end event group (api.php/event)


/**
 * ITEM
 **/
$app->group('/item', function () use ($app) {




	/*

	GET ALL ITEM DEFAULTS
		/api.php/item/defaults

	CREATE or UPDATE ITEM DEFAULTS (put)
		/api.php/item/:ims_cage/:ims_pn

	 */
});



/**
 * USER
 **/
$app->group('/user', function () use ($app) {


	/*
	LIST users (get)
	CREATE user (put)
		/api.php/user/

	READ user (get)
	UPDATE user (put)
		/api.php/user/:id
		/api.php/user/:username
	*/

});



/**
 * Maintenance actions
 **/
$app->group('/maint', function () use ($app) {

	// removes non-released revs of revisions > 1 year old or something...
	$app->delete('/clean-revision-table', function () {
		echo "CLEAN REVISION TABLE";
	});


	$app->post('/refresh-item-on-event-table/', function () {
		echo "REFRESH item_on_event table";
	});

	// same as above, but refreshes table for just this event...
	$app->post('/refresh-item-on-event-table/:id', function ($id) {
		echo "REFRESH item_on_event table, event $id";
	});

});


// // index  : Show all events @TODO will this work?
// // NOTE (BUG?): for some reason the trailing slash is needed on this in the
// // presence of the other get method with /:controller/:id
// $app->get('/:controller/', function ($controller) {
// 	echo getController($controller)->index();
// });

// // read   :
// $app->get('/:controller/:id', function ($controller, $id) {
// 	echo getController($controller)->read($id);
// });

// // create : 
// // NOTE (BUG?): for some reason the trailing slash is needed on this in the
// // presence of the other post method with /:controller/:id
// $app->put('/:controller/', function ($controller) use ($app) {
// 	echo getController($controller)->create( $app->request->put() );
// });

// // update :
// $app->put('/:controller/:id', function ($controller, $id) use ($app) {
// 	echo getController($controller)->update($id, $app->request->put() );
// });

// // delete : 
// $app->delete('/:controller/:id', function ($controller, $id) {
// 	echo getController($controller)->delete($id);
// });

$app->run();
