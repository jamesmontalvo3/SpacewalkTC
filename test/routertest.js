var app = "http://localhost/SpacewalkTC/index.php",
	paths = [
		{ action : "read",   url : "/12", type : "get" },
		{ action : "index",  url : "/",   type : "get" },
		{ action : "create", url : "/",   type : "post" },
		{ action : "update", url : "/12", type : "post" },
		{ action : "delete", url : "/12", type : "delete" }		
	],
	controllers = [
		"event"
	];

function ajaxtest (url, type, respPrefix) {

	$.ajax(
		url,
		{
			type : type,
			"success" : function(response){
				console.log(respPrefix + response);
			}
		}
	);

}

for(var c=0; c<controllers.length; c++) {

	for(var p=0; p<paths.length; p++) {

		ajaxtest(
			app + "/" + controllers[c] + paths[p].url,
			paths[p].type,
			controllers[c] + " " + paths[p].action + ": "
		);

	}

}




function modelUpsert (controller, data) {

	var url = app + "/" + controller + "/";

	if (data.id)
		url += data.id;

	$.ajax(
		url,
		{
			type : "put",
			data : data,
			success : function(response){
				console.log(response);
			}
		}
	);

}









function ajaxtest (url, type, respPrefix) {

	var pre = respPrefix || "";
	$.ajax(
		url,
		{
			type : type,
			"success" : function(response){
				console.log(pre + response);
			}
		}
	);

}
ajaxtest("api.php/event", "get");
ajaxtest("api.php/event/232", "get");
ajaxtest("api.php/event", "put");
ajaxtest("api.php/event/2352", "put");
ajaxtest("api.php/event/1/release/2345", "put");
ajaxtest("api.php/event/1/unrelease", "put");
ajaxtest("api.php/event/232", "delete");
ajaxtest("api.php/event/232/undelete", "delete");





function addEvent (name, released_rev_id, status) {

	$.ajax(
		"api.php/event",
		{
			type : "put",
			data : {
				name : name,
				released_rev_id : released_rev_id,
				status : status
			},
			"success" : function(response){
				console.log(response);
			}
		}
	);

}

function updateEvent (id, data) {

	$.ajax(
		"api.php/event/" + id,
		{
			type : "put",
			data : data,
			"success" : function(response){
				console.log(response);
			}
		}
	);

}

function releaseRevision (eventId, revId) {

	$.ajax(
		"api.php/event/" + eventId + '/release/' + revId,
		{
			type : "put",
			"success" : function(response){
				console.log(response);
			}
		}
	);

}

function unreleaseRevision (eventId, revId) {

	$.ajax(
		"api.php/event/" + eventId + '/unrelease/' + revId,
		{
			type : "put",
			"success" : function(response){
				console.log(response);
			}
		}
	);

}

String.prototype.timepad = function() {
    var str = this;
    if (str.length === 1)
        str = "0" + str;
    return str;
}

function getTimestamp (){
	var d = new Date();
	return d.getFullYear().toString()
		+ (d.getMonth()+1).toString().timepad()
		+ d.getDate().toString().timepad()
		+ d.getHours().toString().timepad()
		+ d.getMinutes().toString().timepad()
		+ d.getSeconds().toString().timepad();
}

addEvent("US EVA 28", null, null);

updateEvent(9, {
	// name ; ,
	// released_rev_id : ,
	// status : ,
	gmt_date : "2014/134",
	jedi : "40-235",
	overview : "This is a test of the overview section",
	ori_rev_id : null,
	revision_ts : getTimestamp(),
	user_id : 1
	// items_json : 
});