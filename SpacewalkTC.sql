
CREATE TABLE items_on_event (

	-- For each event there may be many revs, and within each rev there will
	-- be many items. Each item_number should be used only once within each
	-- rev, though.
	event_id					int unsigned NOT NULL,
	version						int unsigned NOT NULL,
	item_number					int unsigned NOT NULL AUTO_INCREMENT,

	-- If it's an IMS item populate these, else they should be null
	-- Note: for each event_id/rev combo there should be only one 
	-- ims_cage/ims_pn/ims_sn combo...but there can be multiple instances
	-- of null ims_cage/pn/sn...so I'm not sure I can make an index for it
	ims_cage					varchar(5),
	ims_pn						varchar(32),
	ims_sn						varchar(32),
	
	-- Should we store barcode? I suppose so, so it doesn't have to fetch it
	-- from IMS each time. It is not grouped with the columns above because
	-- it is not a required field. Cage/pn/sn uniquely identify items in IMS.
	ims_barcode					varchar(9),
	
	-- Used to override the defaults in the `item_display_text` table and the
	-- Ops Nom from IMS. 
	
	-- Also, if the item is not in IMS (a "custom item") then this is how it
	-- will be identified.
	display_text				varchar(255) NOT NULL default '',
	
	-- Used to identify which item will be the parent of this item (not the
	-- current parent per IMS, but the intended parent per the tool config).

	-- Note: The following columns were removed because parents may not always
	-- be IMS items since we need to support "custom" items:
	--   future_parent_cage
	--   future_parent_pn
	--   future_parent_sn
	future_parent_item_number 	int unsigned NOT NULL,
	
	-- Shit.
	-- @TODO: How do we handle multiple IMS S/Ns?
	qty 						int unsigned NOT NULL default 1,
	
	-- Displayed in the "final location" column, under the location code
	-- as an optional note. For example it may say "EMU 3005" or "Mesh bag
	-- labelled "EVA 26 Tools"".
	new_location_text			varchar(255) NOT NULL default '',
	
	-- config_notes: short parenthetical notes after items in the tool config
	-- within the procedures
	
	-- gather_notes: actionable notes (i.e. starting with bold verb) for crew
	-- to perform while setting up the tools.
	config_notes				varchar(255) NOT NULL default '',
	gather_notes				text,
	
	-- The "ancestry" of the item initially, e.g. the parent item, its parent,
	-- and all parents until the root item. First item is immediate parent;
	-- last item is root item.
	
	-- Formatted as JSON array of JSON arrays. Each contained array is the IMS
	-- unique identifying triplet of ims_cage, ims_pn and ims_sn.
	
	-- Example:
	-- [["NASA","Tether Staging Area",""],["NASA","A/L",""],["NASA","ISS",""]]
	initial_parents				text

) ENGINE=InnoDB, DEFAULT CHARSET=utf8;

/* Can I create an index on this?
CREATE UNIQUE INDEX index_name ON items_on_event(event_id,rev,ims_cage,ims_pn,ims_sn);
*/



CREATE TABLE item_display_text (

	-- Each IMS cage/pn combination can have a default display text
	ims_cage					varchar(5) NOT NULL,
	ims_pn						varchar(32) NOT NULL,
	
	-- text to display
	display_text				varchar(255) NOT NULL default '' 

) ENGINE=InnoDB, DEFAULT CHARSET=utf8;

/*
	INDICES?
*/

CREATE TABLE events (

	-- event ID
	id							int unsigned NOT NULL AUTO_INCREMENT,
	
	-- @TODO: Should this be GMT day number? Like GMT 104/10:40:00
	e_date						varbinary(14),
	
	-- name of the event...not sure if this is the EVA or the gather/config
	-- event yet
	name						varchar(255) NOT NULL,
	
	-- JEDI message...not sure if this is just the number or the name as well
	jedi						varchar(255),
	
	-- All the stuff at the top of the tool gather/config, including the
	-- procedure steps
	overview					text
	
) ENGINE=InnoDB, DEFAULT CHARSET=utf8;

/*
	INDICES?
*/
