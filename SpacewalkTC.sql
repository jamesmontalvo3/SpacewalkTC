
CREATE TABLE items_on_event (

	/* 
		For each event there may be many revs, and within each rev there will
		be many items. Each item_number should be used only once within each
		rev, though.
	*/
	event_id 
	rev
	item_number
	
	/* 
		If it's an IMS item populate these, else they should be null
		Note: for each event_id/rev combo there should be only one 
		ims_cage/ims_pn/ims_sn combo...but there can be multiple instances
		of null ims_cage/pn/sn...so I'm not sure I can make an index for it
	*/
	ims_cage
	ims_pn
	ims_sn
	
	/*
		Should we store barcode? I suppose so, so it doesn't have to fetch it
		from IMS each time. It is not grouped with the columns above because
		it is not a required field. Cage/pn/sn uniquely identify items in IMS.
	*/
	ims_barcode
	
	/* If not an IMS item */
	
	
	/*
		Removed the following columns because parents may not always be IMS
		items since we need to support "custom" items
		
		future_parent_cage
		future_parent_pn
		future_parent_sn
	*/

	
	display_text  /*override default?*/
	qty /*default 1*/
	
	new_location_text /* like: EMU 3005 */
	
	config_notes
	gather_notes
	
	/* JSON like: [["NASA","ISS",""],["NASA","A/L",""],["NASA","Tether Staging Area",""]] */
	initial_parents 

) ENGINE=InnoDB, DEFAULT CHARSET=utf8;

/* CREATE UNIQUE INDEX index_name ON items_on_event(event_id,rev,ims_cage,ims_pn,ims_sn); */
