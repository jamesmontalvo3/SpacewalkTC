-- during development: 
-- drop database stc;create database stc;use stc;source c:/xampp/htdocs/SpacewalkTC/SpacewalkTC.sql;
-- drop database stc;create database stc;use stc;source /www/SpacewalkTC/SpacewalkTC.sql;

CREATE TABLE event (

	-- event ID
	id							int unsigned AUTO_INCREMENT NOT NULL PRIMARY KEY,
	
	-- @TODO: Should this be GMT day number? Like GMT 104/10:40:00
	`datetime`						varbinary(14),
	
	-- name of the event...not sure if this is the EVA or the gather/config
	-- event yet
	name						varchar(255) NOT NULL,
	
	-- @pointer: erevision->id
	released_rev_id				int unsigned
				
) 
ENGINE=InnoDB, 
DEFAULT CHARSET=utf8;

-- indices?


CREATE TABLE erevision (
	
	id							int unsigned AUTO_INCREMENT NOT NULL PRIMARY KEY,
	
	-- All drafts are of a particular event
	-- @pointer: event->id
	event_id					int unsigned NOT NULL,

	-- event version
	version						smallint unsigned,
	
	-- JEDI message...not sure if this is just the number or the name as well
	jedi						varchar(255),
	
	-- All the stuff at the top of the tool gather/config, including the
	-- procedure steps
	overview					text,

	
	-- All drafts, except for the very first for a particular event, should
	-- have an origination draft. This means that the user who saved a draft 
	-- must have started from somewhere. 
	-- Also note that each time the user saves a draft, that new draft ID will
	-- be inserted in the `userlast` table.
	ori_rev						int unsigned,
	-- No longer required: ori_version					int unsigned,
	
	draft_ts					binary(14) NOT NULL,
	
	username					varchar(16),
	
	-- set to NULL when a released version? Or store pre-built JSON here so it can
	-- just be sent on to the client?
	items_json					text
	
) ENGINE=InnoDB, DEFAULT CHARSET=utf8;

CREATE INDEX key_event_id ON erevision (event_id);

ALTER TABLE event
	ADD CONSTRAINT fk_event_released_rev_id 
	FOREIGN KEY (released_rev_id) 
	REFERENCES erevision (id)
	ON DELETE CASCADE
	ON UPDATE CASCADE;

ALTER TABLE erevision
	ADD CONSTRAINT fk_erevision_event_id 
	FOREIGN KEY (event_id) 
	REFERENCES event (id)
	ON DELETE CASCADE
	ON UPDATE CASCADE;



CREATE TABLE itemdefault (

	-- Required for Laravel
	id							int unsigned NOT NULL PRIMARY KEY,

	-- Each IMS cage/pn combination can have a default display text
	ims_cage					varchar(5) NOT NULL,
	ims_pn						varchar(32) NOT NULL,
	
	-- text to display
	display_text				varchar(255) NOT NULL default '',
	allow_multiple_qty			boolean default 0

) ENGINE=InnoDB, DEFAULT CHARSET=utf8;

/*
	INDICES?
*/


-- used to indicate where a user last left off on a particular event. If they
-- last edited draft ID 2352 they will be given that as a starting point. If
-- they lasted edited version 3 they will be given that. Every time a user
-- saves a draft the new draft ID will be inserted here. 
CREATE TABLE userlast (

	-- A primary key is required for Laravel, but this table shouldn't ever
	-- have a user interface associated with it so I don't think it's required.

	-- user id
	u_id						int unsigned NOT NULL,

	-- event id
	e_id						int unsigned NOT NULL,
	
	-- rev id
	r_id						int unsigned
	
	-- version number
	-- NO longer necessary: version						int unsigned

) ENGINE=InnoDB, DEFAULT CHARSET=utf8;


CREATE TABLE `user` (

	id							int unsigned AUTO_INCREMENT NOT NULL PRIMARY KEY,
	
	username					varchar(16), -- for testing will use IP only to make simple
	
	UNIQUE KEY `unique_username` (`username`) 

) ENGINE=InnoDB, DEFAULT CHARSET=utf8;
