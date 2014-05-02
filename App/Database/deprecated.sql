-- used to indicate where a user last left off on a particular event. If they
-- last edited draft ID 2352 they will be given that as a starting point. If
-- they lasted edited version 3 they will be given that. Every time a user
-- saves a draft the new draft ID will be inserted here. 
CREATE TABLE userlast (

	-- useless, but required by RedBeansPHP...
	id							int unsigned NOT NULL PRIMARY KEY,

	-- user id
	user_id						int unsigned NOT NULL,

	-- event id
	event_id					int unsigned NOT NULL,
	
	-- rev id
	revision_id					int unsigned NOT NULL
	
	-- version number
	-- NO longer necessary: version						int unsigned

) ENGINE=InnoDB, DEFAULT CHARSET=utf8;

-- @TODO: u_id/e_id combination is UNIQUE! Unique Key.