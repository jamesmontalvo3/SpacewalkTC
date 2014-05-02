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



/*

App        : Send page

other
	release event

GET LIST OF EVENTS
	/api.php/event/ ?
		no additional methods for now

GET DATA ABOUT AN EVENT
	/api.php/event/:id ?
		eventinfo = false        => optional, default true, returns rev info only
		revision = multiple pipe separated
			latest      => most recent rev, whether official or draft
			published   => get the "official" rev
			<int>       => get rev with id = <int>
			history     => all revs
				history_order   => ASC or DESC (default DESC, most recent)
				history_limit   => <int> (default 10)
				history_before_id => <rev ID> (default null) finds revs prior to <rev ID>
				history_after_id  => <rev ID> (default null) finds revs after <rev ID>
				history_before_ts => <yyyymmddhhmmss> (default null) finds rev after timestamp
				history_after_ts  => <yyyymmddhhmmss> (default null) finds rev after timestamp
			new         => all revs since published

CREATE AN EVENT <-- user sends data for new event, returns event ID and no revision info (blank rev?)
	/api.php/event/

UPDATE EVENT (put)
	/api.php/event/:id   <-- updates event info, creates new revision row if different from previous

RELEASE and UNRELEASE AN EVENT (put)
	/api.php/event/:id/release/:revid  <-- marks :revid as newly released revision
	/api.php/event/:id/unrelease/      <-- (privileged) removes "released" from most recent released rev

DELETE EVENT
	/api.php/event/:id   <-- set `status` to 'deleted'
	/api.php/event/:id/undelete   <-- maybe do this to undelete something?

GET ALL ITEM DEFAULTS
	/api.php/item/defaults

CREATE or UPDATE ITEM DEFAULTS (put)
	/api.php/item/:ims_cage/:ims_pn


LIST users (get)
CREATE user (put)
	/api.php/user/

READ user (get)
UPDATE user (put)
	/api.php/user/:id
	/api.php/user/:username

OTHER
	/api.php/maint/clean-revision-table 
		* removes non-released revs of revisions > 1 year old or something...
	/api.php/maint/refresh-item-on-event-table/:id
		* optional ID to refresh just that event ID (or list of IDs?)

 */

$app = new \Slim\Slim(array(
	"debug" => true,
	'log.enabled' => true
));

$app->get('/', function() {
	echo "Hello, World";
});

// @TODO: this should move to a helpers class or something...
function getController ($controller) {
	require_once "App/Controller/$controller.php";
	$ControllerClass  = "\\Spacewalk\\Controller\\$controller";
	$reflection = new \ReflectionClass($ControllerClass);
	return $reflection->newInstance(); 
}


// index  : Show all events @TODO will this work?
// NOTE (BUG?): for some reason the trailing slash is needed on this in the
// presence of the other get method with /:controller/:id
$app->get('/:controller/', function ($controller) {
	echo getController($controller)->index();
});

// read   :
$app->get('/:controller/:id', function ($controller, $id) {
	echo getController($controller)->read($id);
});

// create : 
// NOTE (BUG?): for some reason the trailing slash is needed on this in the
// presence of the other post method with /:controller/:id
$app->put('/:controller/', function ($controller) use ($app) {
	echo getController($controller)->create( $app->request->put() );
});

// update :
$app->put('/:controller/:id', function ($controller, $id) use ($app) {
	echo getController($controller)->update($id, $app->request->put() );
});

// delete : 
$app->delete('/:controller/:id', function ($controller, $id) {
	echo getController($controller)->delete($id);
});

$app->run();
