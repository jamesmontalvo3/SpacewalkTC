Notes and Reminders
===================

JS Tree Usage?
--------------
* Use tree-library with drag/drop?
* 1st Option: https://github.com/dragosu/jquery-aciTree
  * Note, depends on https://github.com/dragosu/jquery-aciPlugin
* 2nd Option: https://github.com/mar10/fancytree
* Context menu: use jQuery UI instead?

eosFieldValue.js notes
----------------------
* method in_array that replicates the PHP function. Use the underscore.js method _.indexOf in the future
* method getUrlVars: there has to be a library version of this

Misc Notes
----------
* Rest of page...what other form info?
  * what will the workflow be like?
* eosTree or aciTree with eosTree merged in
* new backend, stores individual data items
* Output:
  * Generate word document with 3-column tool config
  * Generate excel document (or PDF'd big table o'tools)
    * Later perhaps generate IPV XML procedure?
* IMS integration
* Limited Life
* Diffing tree strucure = Rooted Tree Isomorphism, a specialized version of Graph Isomorphism
* Tie into OOT? Go for EVA
  * when this is live, ISOs and IMS can adopt principle of putting something in "notes" for alerting SpacewalkTC that there is an issue with a particular item. Notes something like "This is some useless info !alert(This is some warning info)
* ? Save drafts with UPDATE events SET drafts=CONCAT(drafts,NEW_TEXT) where ...;
  

Big Picture Plan
----------------

1. Database layout (create database generator SQL file)
2. Create backend for saving values to database
	1. Receives values from form: e_id, e_version, e_date, e_name, jedi, overview, items
	2. All except "items" basically directly pushed to database
	3. "items" broken into multiple inserts into items_on_event
	
