-- This table tracks the specific tools used on an event with a finer
-- granularity. From this table it'll be easier to determine which tools are
-- most commonly used, which are commonly children of other tools, and perhaps
-- even do some limited-life tracking.




-- @TODO: update comments. this table's purpose has changed dramatically.
CREATE TABLE eventitem (

	-- Some primary key is required for Laravel
	id							int unsigned NOT NULL PRIMARY KEY,

	-- For each event there may be many revs, and within each rev there will
	-- be many items. Each item_number should be used only once within each
	-- rev, though. Thus, the event_id/item_number combination is
	-- unique.
	event_id					int unsigned NOT NULL,
	-- no longer maintain revs: event_version int unsigned NOT NULL,
	item_number					int unsigned NOT NULL,

	-- If this is an IMS item: populate these; Else: they should be null.
	-- Note: for each event_id/version combo there should be only one 
	-- ims_cage/ims_pn/ims_sn combo...but there can be multiple instances
	-- of null ims_cage/pn/sn...so I'm not sure I can make an index for it
	--
	-- Should ims_sn have a "not yet set" value? perhaps NULL is not-yet-set
	-- and an empty string is a blank S/N? That could be confusing...so 
	-- maybe not-yet-set should be indicated by some special string like 
	-- "SERIALNUMBER-NOTYETSET". Or perhaps another column is required to 
	-- indicate serial number is TBD.
	ims_cage					varchar(5),
	ims_pn						varchar(32),
	ims_sn						varchar(32),
	
	-- Marks S/N as still to be determined. Allows user to create tool config
	-- but not yet enter the S/N values if they don't know yet. This column is
	-- needed to differentiate between S/N = "" (blank, e.g. not in IMS),
	-- S/N = NULL (this item does not require a S/N), and S/N is TBD. 
	ims_sn_tbd					boolean default 1,
	
	-- Should we store barcode? I suppose so, so it doesn't have to fetch it
	-- from IMS each time. It is not grouped with the columns above because
	-- it is not a required field. Cage/pn/sn uniquely identify items in IMS.
	ims_barcode					varchar(9),
	
	-- Determines whether or not to show the serial number in the tool gather
	-- document. Since some items have nonsense serial numbers (e.g. common
	-- items like wipes, wire ties and bolts) we need to be able to hide them.
	show_sn						boolean default 1,
	
	-- Used to override the defaults in the `item_defaults` table and the
	-- Ops Nom from IMS. 
	--
	-- Also, if the item is not in IMS (a "custom item") then this is how it
	-- will be identified.
	display_text				varchar(255) NOT NULL default '',
	
	-- Used to identify which item will be the parent of this item (not the
	-- current parent per IMS, but the intended parent per the tool config).
	-- 
	-- Note: The following columns were removed because parents may not always
	-- be IMS items since we need to support "custom" items:
	--   future_parent_cage
	--   future_parent_pn
	--   future_parent_sn
	future_parent_item_number 	int unsigned NOT NULL,
	
	-- Shit. This is hard. Not sure if this is the best way but here we go:
	-- Each item will have its own line in the database, but the 
	-- merge_with_item_number will allow there to be line items in the tool
	-- config that show qty>1 and have multiple serial numbers. One item will
	-- be sort of the "master" item, and all others will have the 
	-- merge_with_item_number set to that item. Additionally, the table
	-- item_defaults has a field "allow_multiple_qty" which will allow
	-- some items to set qty>1 without listing multiple S/Ns. For items with
	-- this set to FALSE (zero) setting the qty>1 will automatically create
	-- additional S/N fields.
	-- @TODO: How do we handle multiple IMS S/Ns?
	-- @TODO: probably should go with TINYINT or SMALLINT here...
	qty 						smallint unsigned NOT NULL default 1,
	merge_with_item_number		smallint unsigned,
	
	-- Displayed in the "final location" column, under the location code
	-- as an optional note. For example it may say "EMU 3005" or "Mesh bag
	-- labelled "EVA 26 Tools"".
	new_location_text			varchar(255) NOT NULL default '',
	
	-- config_notes: short parenthetical notes after items in the tool config
	-- within the procedures
	-- 
	-- gather_notes: actionable notes (i.e. starting with bold verb) for crew
	-- to perform while setting up the tools.
	config_notes				varchar(255) NOT NULL default '',
	gather_notes				text,
	
	-- The "ancestry" of the item initially, e.g. the parent item, its parent,
	-- and all parents until the root item. First item is immediate parent;
	-- last item is root item.
	--
	-- Formatted as JSON array of JSON arrays. Each contained array is the IMS
	-- unique identifying triplet of ims_cage, ims_pn and ims_sn.
	--
	-- Example:
	-- [["NASA","Tether Staging Area",""],["NASA","A/L",""],["NASA","ISS",""]]
	initial_parents				text
	
	-- Not sure if this is a good idea since item_number may change as the user
	-- PRIMARY KEY (event_id, event_version, item_number),
	
		
) ENGINE=InnoDB, DEFAULT CHARSET=utf8;

-- Also not sure this is a great idea for speed purposes. Leaving for now.
CREATE INDEX event_id ON eventitem (event_id);

ALTER TABLE eventitem
	ADD CONSTRAINT fk_eventitem_event_id 
	FOREIGN KEY (event_id) 
	REFERENCES event (id)
	ON DELETE CASCADE
	ON UPDATE CASCADE;



/* Can I create an index on this?
CREATE UNIQUE INDEX index_name ON eventitem(event_id,rev,ims_cage,ims_pn,ims_sn);
*/

-- Not sure how to make this work...
-- ALTER TABLE eventitem
	-- ADD CONSTRAINT fk_future_parent
	-- FOREIGN KEY (event_id, event_version, future_parent_item_number)
	-- REFERENCES eventitem (event_id, event_version, item_number)
	-- ON DELETE NO ACTION
	-- ON UPDATE NO ACTION;
