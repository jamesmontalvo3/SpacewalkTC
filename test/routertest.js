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


