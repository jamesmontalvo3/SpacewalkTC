/*

Note: whatever event a user last had is not a good indicator, since they
could have two windows open pulling from different revision histories. 
Instead we should trust the ori_rev that the user submits to be true, and
thus the `userlast` table is just a convenience for where the user left
off last time...and it's not necessarily even a good indicator since they
could possibly have more than one revision history branch they're working
on...

*/

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