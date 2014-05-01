/**
 *	@author: James Montalvo
 *	@email: jamesmontalvo3@gmail.com
 *	@copyright: Copyright (C) 2010, James Montalvo
 *	@license: http://opensource.org/licenses/mit-license MIT License
 **/


/**
 *	Note to future Predive Calendar and Tool Matrix developers:
 *
 *	I've attempted to document this the best way I know how. Please forgive any
 *  non-standard things I have done. 
 *
 *	This web application is built on PHP and MySQL, with heavy use of
 *  Javascript. In particular it uses jQuery and jQuery UI. Good understanding
 *  of both jQuery frameworks is required for comprehension of this code.
 *
 *  Below are a number of CONCEPTS I have determined as key to understanding
 *	the function of the code. They are used throughout this file, denoted like
 *  *My Concept*. So a comment may say something like: 
 *
 *		"When you click the button a *Dialog Box* will appear."
 * 
 *	Also, when inside the code I came across something that should be improved
 *	or reconsidered, I added an @action.
 * 
 *  I hope this makes it more clear. Thanks for your patience.
 *		~James Montalvo, 05 June 2012
 **/
 
/**
 *  @concept *Tool Request* A page within the EVA Calendar which is used by
 *		EVA instructors to request tools for NBL and other events. Tools come
 *		from the USA Tools Lab. In the past this was built into SARAH, and 
 *		prior to that it was an Excel spreadsheet. At that time it was refered
 *		to as the "Tool Matrix". At this writing, people still call it that,
 *		even though that seems like a misnomer to me.
 *  @concept *Tree Item* or *Tree Items* nodes within the tree structure of a 
 *  	*Tool Request*. 
 *	@concept *Tree Branch* a *Tree Item* which contains sub items and possibly
 *		sub-sub items, sub-sub-sub items, etc.
 *	@concept *Dialog Box* this concept refers to the jQuery.dialog() method 
 *		built into jQuery UI. (http://jqueryui.com/demos/dialog/)
 *	@concept *Chooser Item* An item created which contains the
 *		*Autocomplete Textbox* and button to open the *Item Browser*. This item
 *		allows creation of new items. Once an item is chosen it becomes a 
 *		*Tree Item* and is no longer the *Chooser Item*.
 *
 *		Also, when an existing item is edited, it becomes the *Chooser Item*. 
 *	@concept *Item Browser* When building a *Tool Request* the user has the
 *		capability to either search for tools via a textbox or a browser. The
 *		*Item Browser* is a *Dialog Box* which pops up, listing all of the 
 *		available tools.
 *  @concept *Autocomplete Textbox* When building a *Tool Request* the user has
 *		the capability to either search for tools via a textbox or a browser.
 *		The textbox is called the *Autocomplete Textbox*, which allows the user
 *		to type only a few characters to find the items they are looking for.
 * 		For example, they may type "tor" to find "Torque Wrench". 
 *  @concept *Selected Item* The item in a *Tool Request* highlighted, 
 *		indicating that it is the item the user is performing actions upon.
 *		Method names and comments in this file may not be consistant with this
 *		terminology as of this writing (05 June 2012). In some cases the
 *		selected item may refer to the item the user has chosen, either by
 *		searching for it or by browsing through the *Item Browser*. One case of
 *		this is the insertSelectedItem() method, which inserts the item the
 *		user has chosen.
 * 
 *		Also can be indicated by eos.activeItemLI, specifically when dealing
 *		with the *Item Browser*. Because the *Item Browser* usage could cause
 *		the user to click somewhere that would deselect the *Selected Item*,
 *		use of the *Item Browser* sets eos.activeItemLI, meaning the intended
 *		*Selected Item* will be remembered.
 *	@concept *Chosen Item* The item in a *Tool Request* that a user has chosen
 *		by search or browsing. See *Selected Item* above for ambiguity issues.
 *  @concept *Standard Children* a list of *Tree Items* or *Tree Branches*  
 * 		which is added along with a *Tree Item* by default.
 *	@concept *Scheduler Page* Another Javascript intensive page. Allows 
 *		schedulers to more easily move classes around (in theory). In practice
 *		no schedulers use this at this time.
 *
 **/

/** TO DO
 *  
 *	1) COMPLETED - Real time itemized list
 *	2) COMPLETED - Custom alerts
 * 	3) COMPLETED - Like-item highlight
 *	4) Fix add-item-to-tool-catalog
 *		a) Fix JSONTree saving
 *		b) Category not saving
 *		c) Move "comments" below "standard children"
 *		d) Improve layout of "training hw part numbers"
 *			1) if not too difficult, add IO images to these
 *			2) remove IO images from main area
 *			3) onmouseover checkboxes, show thumbnail???
 *	5) Link to EVA Library
 *	6) Fix tools tab in Event viewer
 *  7) Make tools page PDFable
 *	8) SCUBA wired comm
 *	9) "Other Water Event" black not red on calendar
 *	10) Test like whoa
 *	11) Fix email on "copy but make changes" but not email on "copy"
 *	12) Fix redirect to "../false" issue
 * 
 *	Code cleanup and such:
 *	1) getTextboxSelect(): very long. Clean up.
 *	2) jQuery and jQuery UI update? When is jQuery UI 1.9 coming out?
 *	3) Fix LayerX and LayerY issues in webkit
 *
 *
 **/
 

/**
 * PROPERTIES:
 *	selectedItem
 *	copiedItem
 *	lookupURL
 *	jsonTreeData
 *	initialTreeStructure
 *	initiateJsonTree
 *	activeItemLI
 *
 *	
 * METHODS:
 * 
 * 	data structure
 *		getPostObject
 *		getFieldValue
 *		getJSONTreeValue
 *
 *	TBD
 *		getNewJSONTreeItem
 *		initiateJsonTree
 *		buildInitialTreeStructure
 *
 *	
 *		getJsonTreeItemByID
 *		getItemNameByID
 *		getItemAutoQtyByID
 *		getItemAutoCommentByID
 *		getItemAutoChildrenByID
 *		getItemCheckboxDataByID
 *		getCheckboxAlertByIdAndType
 *		getCheckboxPartNumsByIdAndType
 *		getItemAutoCheckedBoxesByID
 *		getItemCheckboxAlertsByID
 *		getItemCheckboxOptionArrayByID
 *
 *	
 *		encodeJsonTreeCheckboxType
 *		displayItemSelector
 *		getTextboxSelect
 *		getItemBrowserDialogButton
 *		getCancelNewLIButton
 *		getCancelEditButton
 *		getSelectedItemLI
 *		selectItemFromBrowser
 *		updateListItem
 *		doCheckboxAlerts
 *		createNewItemUnderParent
 *		createChildLI
 *		modifyLI
 *		getUniqueNumber
 *		handleCheckboxes
 *		createChildItems
 *		removeItemSelectors
 *		cancelNewItem
 *		cancelItemEdit
 *		editItem
 *		handleComment
 *		itemComment
 *		deselectItem
 *		selectItem
 *		addItemFunctions
 *		getItemInfo
 *
 *	Expand / Collapse
 *		toggleExpandCollapse
 *		rebuildExpandCollapseImages
 *		expandAllItems
 *		collapseAllItems
 *		collapseTopLevelItems
 *
 *		adjustIndents
 *	
 *	Context Menu
 *		jsonTreeContextMenu
 *		addItem
 *		copyItem
 *		cutItem
 *		pasteItem
 *		contextMenuMoveItemUp
 *		contextMenuMoveItemDown
 *
 *	Move items
 *		moveSelectedItemUp
 *		moveSelectedItemDown
 *
 *	Itemized List
 *		updateItemizedList
 *		
 *		document.ready->tooltip
 *	
 *	
 **/

 
"use strict";

window.eos = {
	
	/**
	 *	Field value methods
	 *
	 *
	 *
	 *
	 *
	 *
	 *
	 **/
    getPostObject : function (noSubmitVal) {

		// argument "form" should be used to specify which form is being submitted, so multiple can be on the page.

        var fieldVals = {};
        var htmlName;
        var fieldValue;
		
        $(".submittable-field").each(function(index, element){
		
            htmlName = $(element).attr("name");
            fieldValue = eos.getFieldValue( $(element).attr("id") ); //CORRECT JQUERY?
            fieldVals[htmlName] = fieldValue;

        });
		
		if ( ! noSubmitVal ) {
		
			if (eos.submitType)
				fieldVals.submit = eos.submitType;
			else
				fieldVals.submit = 1;
				
		}

        return fieldVals;

    },

    getFieldValue : function (fieldId) {
	
        var field_type = $("#" + fieldId).attr("fetype");
        var types = {
            Hidden : "getStandardValue", //added to FormEntity
            FormEntity : "getStandardValue", //added to FormEntity
            Textarea : "getStandardValue", //added to FormEntity
			RichTextarea : "getRichTextValue",
            Textbox : "getStandardValue", //added to FormEntity
            Dropdown : "getStandardValue", //added to FormEntity
            DropdownOtherInput : "getDropdownOtherInputValue", //added to FormEntity
            Checkbox : "getCheckboxValue", //added to FormEntity
            Radio : "getRadioValue", //added to FormEntity
            UserRecorder : "getStandardValue",
            DateEntry : "getStandardValue",
            DateTimeEntry : "getStandardValue", //"getDateTimeEntryValue",
            JSONTable : "getJSONTableValue", //added to FormEntity
            JSONTree : "getJSONTreeValue"
        };

        var method = types[field_type]; 
        
        // calls specialized function for getting field value from element
        var value = this[method](fieldId);
		
        return value;
    
    },
   
    
    getJSONTreeValue : function (ul) { //need to pass hidden ID
		
        // handle when new ID is passed at start of 
        if (typeof(ul) == "string") {
            ul = document.getElementById(ul + "-root-ul");

            var rootUL = true;

        }

        var output = [];
        var child;

		var idInput;
		var qtyInput;
		var commentInput;
		
		// ID of comment item...used to make any item without an ID saveable...in custom form...
		// must be set to "Custom Name" or "Comment Name" id in database...hackity hack
		var commentItemId = 220;

		
        $(ul).children("li").each(function(){

            child = this; //to avoid confusing nested "this"

			qtyInput = parseInt( $(child).find('.qty-input').first().val() );
			if ( ! qtyInput  ) qtyInput = 0;
						
			commentInput = $(child).find('.comment-display').first().html();
			commentInput = commentInput.replace(/\\/gi,'\\\\').replace(/"/gi, '\\"');
			//commentInput.replace(/&/g, "&amp;").replace(/>/g, "&gt;").replace(/</g, "&lt;").replace(/"/g, "&quot;");
			
			idInput = $(child).attr("itemid");
			if (idInput < 1) {
				idInput = commentItemId;
				if ($.trim(commentInput) == "")
					commentInput = "use comments for name";
			}

			var itemObj = {
                id       : idInput,
                //itemname : $(child).find(".itemName").first().html(),
                //qty      : $(child).attr("itemqty"),
                qty      : qtyInput,
                comments : commentInput, //$(child).attr("itemcomment"),
				checkedBoxes : [],
                children : eos.getJSONTreeValue( child.getElementsByTagName("ul")[0] ) //for some reason find.first.each didn't work                
            };
			var trainingHwTypes = $(child).attr('trainingHwTypes');
			trainingHwTypes = trainingHwTypes.split(";");
			//alert(trainingHwTypes);
			//alert('test1');

			var typeClassName;
			
			//itemObj["training_hw"] = [];
			for(var i=0; i<trainingHwTypes.length; i++) {
				
				// class names for the checkboxes were "encoded" when they were created
				// basically this just means they had all special characters and spaces stripped
				// from them to make them safe as class names.
				typeClassName = '.training_hw_' + eos.encodeJsonTreeCheckboxType(trainingHwTypes[i]);
				
				if ($(child).find(typeClassName).first().is(':checked')) {
					//itemObj[ "training_hw_" + trainingHwTypes[i] ] = 1;
					//alert('checked: ' + trainingHwTypes[i]);
					// prior to 05 June 2012 was: itemObj["training_hw"][ trainingHwTypes[i] ] = 1;
				
					itemObj["checkedBoxes"].push(trainingHwTypes[i]);
				}
				else {
					//itemObj[ "training_hw_" + trainingHwTypes[i] ] = 0;
					//alert('un-checked: ' + trainingHwTypes[i]);
					// prior to 05 June 2012 was: itemObj["training_hw"][ trainingHwTypes[i] ] = 0;
				
					var asdfasdfasdf = 0; //dummy for now
				}
				
			}

            output.push(itemObj);

        });

		//alert(output);
		
        if (rootUL) {
            //var x = 1;
			output = JSON.stringify(output);
			//alert(output);
		}
		
		return output;

    },

    getNewJSONTreeItem : function (rootId, newLocation, offset, lookupURL) {

		// kill any pre-existing item input or edit
		eos.cancelItemEdit();
		$("#newitem").closest(".itemLI").remove();
	
        var rootUL = $("#" + rootId + "-root-ul");

        if (newLocation == "root") {
            newLocation = rootUL;

            var offset = newLocation.attr("initoffset");

        }
        else {
            newLocation = $(newLocation).parents(".itemLI").first().children(".itemUL").first();

            var depth = newLocation.parents(".itemUL").length + 1;

            var offset = parseInt(rootUL.attr("initoffset")) + (depth * parseInt(rootUL.attr("depthoffset")));

        }

        // clone hidden LI
        var LI = $('#' + rootId + "-itemclone").html();

		//## REQUIRED?
        LI = $(LI);    

        LI.find(".firstcol").css("padding-left", offset + "px");
        //LI.find(".itemUnderline").css("left", -offset + "px");
        LI.find(".dropBetween").css("left", offset + "px");
        
        //## REQUIRED??
        //LI = $(LI);
        
		// hides item name info, appends input elements
		eos.displayItemSelector(LI);
        
        $(newLocation).append(LI);

        $("#newitem").focus().select();

    },
	
	
	
/**	JSONTree: Initiation of JSONTree
 *
 *
 *
 *
 *
 *
 *
 **/

	selectedItem : null,
    copiedItem : null,
	lookupURL : "tool_repository.php", // should be set by JSONTree.php probably...but better than current method.
	jsonTreeData : null, // RENAME: toolRepository...to be filled with entire list from lookupURL
	initialTreeStructure : null, //filled with structure of an event's tools
	

	/** Creates "repository" of tool info; pulls in & builds saved item tree
	 *
	 **/
	initiateJsonTree : function () {

		$.getJSON(
			'tool_repository.php',
			{},
			function (data) {
				eos.jsonTreeData = data;
				
				eos.initialTreeStructure = JSON.parse(window.rawJsonTreeString);
				eos.buildInitialTreeStructure();
				
				/*$.getJSON(
					'form_json.php',
					{
						event_id : eos.getUrlVars().event_id,
						form_type : "tools"
					},
					function(eventTreeData){
						//have to JSON.parse() eventTreeData.tools, but not eventTreeData
						//because tools parameter sent as a string.
						eos.initialTreeStructure = JSON.parse(eventTreeData.tools); 
						eos.buildInitialTreeStructure();
					}
				);*/
				
			}
		);

		// browser <div> created in get_item_browser_dialog() in JSONTree.php
		$('#browse-tree-items').dialog({
			width : 900,
			height : $(window).height() - 40,
			modal : true,
			autoOpen : false,
			buttons : {
				Cancel : function() { $(this).dialog('close'); }
			}
		});
		
		// keeps dialog in fixed position. Needs to be added after creating dialog?
		$('#browse-tree-items').dialog({dialogClass: 'fixed-dialog'});
		$('.fixed-dialog.ui-dialog').css({position:'fixed', top:20});
	
	}, 


	/**
	 *
	 **/
	buildInitialTreeStructure : function () {
		
		var appendToUL = $(".jsontree-root-ul").first();

		for (var i=0; i < eos.initialTreeStructure.length; i++) {
			
			//alert('test2asdf');
			//alert(JSON.stringify(eos.initialTreeStructure[i]));
			eos.createNewItemUnderParent(eos.initialTreeStructure[i], appendToUL); 
		
		}
		
		eos.rebuildExpandCollapseImages();

		eos.updateItemizedList();
		
	},
	
	/** Looks through eos.jsonTreeData, finds item by ID
	 *
	 *
	 *	Add this to methods:
	 *		selectItemFromBrowser
	 **/
	getJsonTreeItemByID : function (id) {
		return eos.jsonTreeData[id];
	},
	
	getItemNameByID : function (id) {
		return eos.jsonTreeData[id].itemName;
	},
	
	getItemAutoQtyByID : function (id) {
		return eos.jsonTreeData[id].autoQty;
	},
	
	getItemAutoCommentByID : function (id) {
		return eos.jsonTreeData[id].autoComment;
	},

	getItemAutoChildrenByID : function (id) {
		return JSON.parse(eos.jsonTreeData[id].autoChildren);
	},

	getItemCheckboxDataByID : function (id) {
		return eos.jsonTreeData[id].checkboxes;
	},
	
	getCheckboxAlertByIdAndType : function(id, type) {
		return eos.getItemCheckboxDataByID(id)[type].dialogText;
	},
	
	/**
	 * returns in the form: 
	 * [
	 *   { 'pn' : PARTNUMBER, 'opsnom' : OPSNOM },
	 *   { 'pn' : ... },
     *   { ... }
     * ]
	 **/
	getCheckboxPartNumsByIdAndType : function(id, type) {
	
			
		// list of partnums and opsnoms by priority
		var partnums = eos.getItemCheckboxDataByID(id)[type].partnums;
		
		// console.log("getCheckboxPartNumsByIdAndType");
		// console.log(partnums);
		
		var output = [];
		// for (var i=0; i<partnums.length; i++) {
		var c = 0;
		for (var tool_lab_id in partnums) {
			output[c] = {
				pn : partnums[tool_lab_id][0],
				opsnom : partnums[tool_lab_id][1]
			};
			c++;
		}
	
		return output;
	
	},

	getItemAutoCheckedBoxesByID : function (id) {
		
		// if ( ! eos.jsonTreeData[id] )
			// alert(id);
		
		var out = [];
		var cbox = eos.getItemCheckboxDataByID(id);
		
		for (var typeName in cbox) {
			if(cbox[typeName].autoCheck)
				out.push(typeName);
		}
		
		return out;
	
	},
	
	getItemCheckboxAlertsByID : function (id) {
	
		// if ( ! eos.jsonTreeData[id] )
			// alert("alert: " + id);
		
		var cboxes = eos.getItemCheckboxDataByID(id);
		var out = {};
		
		for (var typeName in cboxes) {
			if(cboxes[typeName].dialogText) {
				out[typeName] = cboxes[typeName].dialogText;
			}
		}
	
		return out;
	},
	
	getItemCheckboxOptionArrayByID : function (id) {
		
		// if ( ! eos.jsonTreeData[id] )
			// alert("options: " + id);
		
		var out = [];
		var cbox = eos.getItemCheckboxDataByID(id);
		
		for (var typeName in cbox) {
			out.push(typeName)
		}
		
		return out;
	
	},
	
	// type checkboxes get classnames like class="training_hw_Hi-Fi"
	// which works fine when only HTML-class acceptable characters are used
	// but when types have names like "1G only" the space messes it up and
	// sees two classes of names "training_hw_1G" and "only". There are also
	// problems with characters like:
	// ~ ! @ $ % ^ & * ( ) + - = , . / ' ; : " ? > < [ ] \ { } | ` #
	// Spaces are handled as \s, which finds all unicode whitespace characters
	// (tabs, line breaks, space, etc)
	encodeJsonTreeCheckboxType : function (type) {	
		return type.replace(/[\(\)\[\]\{\}\"\'\`\~\!\@\$\%\^\&\*\+\-\=\,\.\/\;\:\?\>\<\\\|\#\s]/gi,"");
	},

	
	
	
	
	
	/**	JSONTree: Setting up the *Chooser Item*
	 *
	 *
	 *
	 *
	 *
	 *
	 *
	 *
	 *
	 **/
	
	/** Used by *Item Browser* to point back to INTENDED *Selected Item*
	 *
	 **/
	activeItemLI : null,

	/** Inserts elements into an LI allowing the user to choose an item
	 *
	 *	For a supplied LI, hides the LI's itemName. Inserts a textbox to select
	 *  a new item. Can be used for editing the LI, or when a new LI is created
	 **/
	displayItemSelector : function (LI,isEditItem) {
	
		// first hide itemName, then return its parent
		var hoverWrapper = $(LI).find('.itemName').first().hide().parent();
		
		hoverWrapper
            .append( eos.getTextboxSelect(LI) )
			.append( eos.getItemBrowserDialogButton(LI) )
			.append( "<span id='item-selector-spacer'>&nbsp;|&nbsp;</span>" );
		
		// cancel buttons have different functionality
		// "add item" cancel button removes item row
		// "edit item" cancel button removes input elements, keeps row
		if ( isEditItem ) {
            hoverWrapper.append( eos.getCancelEditButton() );
			var cancelFunction = function() { eos.cancelItemEdit(); }
		}
		else {
            hoverWrapper.append( eos.getCancelNewLIButton() );
			var cancelFunction = function() { eos.cancelNewItem(); }
		}
		
		$(document).keyup(function(event) { // escape key only has keyup event...not keypress
			if (event.which == 27) { // escape key pressed
				//event.preventDefault();
				cancelFunction();
			}
		});
	},
	
	/** Creates *Autocomplete Textbox* to determine *Chosen Item* from list
	 *
	 **/
    getTextboxSelect : function (LI) {
	
        return $("<input type='text' name='newitem' id='newitem' value='Enter New Name' class='newitem-input' />")
            .autocomplete({
                minLength: 2,
				source: function( request, response ) {
					// Much of the commenting here is from a Stack Overflow article
					// http://stackoverflow.com/questions/2382497/jquery-autocomplete-plug-in-search-configuration
					// 
					// --------------------------------------------------------
					//
					// $.ui.autocomplete.escapeRegex(request.term)
					//   Escapes search term so that any regex-meaningful 
					//   terms in the text typed by the user are treated as 
					//   plain text. For example, the dot (.) is meaningful to
					//   regex.
					//
					// --------------------------------------------------------
				
					var frontMatchMultiplier = 2;
					var anyMatchMultiplier = 1;
					var noMatchMultiplier = -2;
				
					// remove white space from beginning and end
					var term = $.trim(request.term);
					
					// split into array of terms
					var terms = term.split(" ");
					
					// remove blank terms
					// Array.splice(index_of_item_to_be_removed, 1); removes array item
					
					// demo of functionality, since this for-loop was questionable (tested in Chrome 19, Firefox 8, IE 7) - 22 May 2012 - J.Montalvo
					// var testarr = ["one", 2, "", 0, "", -1];
					// for(var i=0; i<testarr.length; i++){ 
						// if(testarr[i] === ""){ 
							// testarr.splice(i,1); 
						// } 
					// }
					// for(var i=0; i<testarr.length; i++){ 
						// document.write(testarr[i] + "<br />");
					// }					
					for(var i=0; i<terms.length; i++){ 
						if(terms[i] === ""){ terms.splice(i,1); } 
					}

					var item, 
						frontMatchList=[],
						anyMatchList=[],
						noMatchList=[],
						isFrontMatch,
						tempMatchString,
						tempMatchArray,
						resp,
						itemWasPushed;
					
					// function to insert item in proper place in list
					var scoreArray = [];
					var insertInScoreArray = function(newScore, newItemData) {
					
						var newItem = {score: newScore, item: newItemData};
					
						if(scoreArray.length == 0) // first item
							scoreArray.push(newItem);
						else {
							itemWasPushed = false;
							for(var j=0; j<scoreArray.length; j++) { // loop through all items currently in list
								if (scoreArray[j].score < newScore) { // if new score is higher than check item
									scoreArray.splice(j,0,newItem); // insert before current checked item
									break; // once found, no need to check items below
								}
							}
							if ( ! itemWasPushed )
								scoreArray.push(newItem);
						}
					};
					
					//alert('teasdg');
					
					// for(var i=0; i<eos.jsonTreeData.length; i++) {
					for(var i in eos.jsonTreeData) {
						
						item = eos.jsonTreeData[i];

						// THIS IS SUCH A SHITTY WAY OF DOING THIS...
						tempMatchString = item.full_name + " " + item.short_name + " " + item.category;
						tempMatchArray = tempMatchString.split(" ");

						var frontMatches = 0;
						var anyMatches = 0;
						var noMatches = 0
						
						for(var t=0; t<terms.length; t++) {
							
							// used to determine if names/categories of particular items match search term
							var anyMatcher = new RegExp( $.ui.autocomplete.escapeRegex(terms[t]), "i" );

							// matches against the beginning of the string only (term="me" will match "Megan" but not "James" 
							var frontMatcher = new RegExp( $.ui.autocomplete.escapeRegex("^" + terms[t]), "i" );

							// loop, increment front matches
							for(var j=0; j<tempMatchArray.length; j++) {
								if(frontMatcher.test(tempMatchArray[j]))
									frontMatches++;
							}
					
							// loop, increment all matches
							for(var j=0; j<tempMatchArray.length; j++) {
								if(anyMatcher.test(tempMatchArray[j]))
									anyMatches++;
							}

							// check the full item string for any matches of this term
							if ( ! anyMatcher.test(tempMatchString) )
								noMatches++;
							
						}
						
						// push item to score list
						insertInScoreArray(
							frontMatches * frontMatchMultiplier + anyMatches * anyMatchMultiplier + noMatches * noMatchMultiplier, 
							item
						);
						
					}
					
					// pull top 10 matches, create itemData-only array
					var resp = []; // response array
					window.scoreArray = scoreArray;
					if (scoreArray.length > 0)
						for(var r=0; r<10; r++) {
							if(scoreArray[r].score > 0)
								resp.push(scoreArray[r].item);
							else
								break; // no more valid scores
						}
					response(resp);
					
					
					
					// ---------------------------------------------------
					
					/*
					// used to determine if names/categories of particular items match search term
					var allMatcher = new RegExp( $.ui.autocomplete.escapeRegex(term), "i" );

					// matches against the beginning of the string only (term="me" will match "Megan" but not "James" 
					var frontMatcher = new RegExp( $.ui.autocomplete.escapeRegex("^" + term), "i" );
					
					
					for(var i=0; i<eos.jsonTreeData.length; i++) {
												
						item = eos.jsonTreeData[i];
						
						tempMatchString = item.full_name + " " + item.short_name + " " + item.category;
						tempMatchArray = tempMatchString.split(" ");
						
						// probably a better way to do this with regex, but I'm not good at them
						// ATTENTION REQUIRED: clean this up with regex
						isFrontMatch = false;
						frontMatches = 0;
						for(var j=0; j<tempMatchArray.length; j++) {
							if(frontMatcher.test(tempMatchArray[j])) {
								isFrontMatch = true;
								frontMatches++;
							}
						}
						
						
						if (isFrontMatch)
							frontMatchList.push(item);
						else if (allMatcher.test(tempMatchString))
							anyMatchList.push(item)
						else
							noMatchList.push(item);
						
					}
					
					resp = frontMatchList;
					maxListLength = 10;
					
					if (frontMatchList.length < maxListLength) {
						
						// max number to add is either difference between curent array length and max length
						// or the entirety of anyMatchList
						var maxAdd = Math.min(maxListLength - frontMatchList.length, anyMatchList.length);
					
						for(i=0; i<maxAdd; i++)
							resp.push(anyMatchList[i]);
					}
					response(resp);
					*/
				},
				select: function(event, ui) {				
					eos.updateListItem(LI, ui.item);
					eos.removeItemSelectors();
					eos.createChildItems(LI, ui.item.id);
					eos.rebuildExpandCollapseImages();
					eos.updateItemizedList();
	                return false;
                }
            })
			.keypress(function(event){ return event.keyCode != 13; }) // prevents ENTER key from causing form to submit
			.dblclick(function(){ return false; }); // reason for this? deactivate comment window?

    },
	
	/**	Creates "browse" button to pop up *Item Browser*
	 *	
	 **/
	getItemBrowserDialogButton : function (LI) {
	
		return $("<span class='armory-linkspan' id='browse-dialog-button'>browse</span>")

            //when clicking the button, apply data and remove input layer
            .click(function(){
				$('#browse-tree-items').dialog('open');
				eos.activeItemLI = LI;
			});

	},
	
	/** Creates button to abort creation of new item. Calls eos.cancelNewItem()
	 *
	 **/
    getCancelNewLIButton : function () {

        return $("<span class='armory-linkspan' id='cancelnew'>cancel</span>")

            //when clicking the button, apply data and remove input layer
            .click(function(){
                //doesn't appear can remove node while still in THIS. setTimeout so the
                //remove occurs after THIS is no longer in scope.
                setTimeout(function(){eos.cancelNewItem();},1);
            });
            
    },
	
	/** Creates button to abort editing (different from canceling new item)
	 *
	 **/
    getCancelEditButton : function () {

        return $("<span class='armory-linkspan' id='canceledit'>cancel</span>")

            // requires set timeout, because function cannot remove element
			// that initiated its call
			.click(function(){
				setTimeout(eos.cancelItemEdit(),1);
			});

    },
	
	
	



/**	JSONTree: Choosing items and applying item data
 *
 *
 *
 *
 *
 *
 *
 *
 **/
 

	/** Returns current *Selected Item* or false
	 *	
	 **/
	getSelectedItemLI : function () {
	
		var item = $(".itemSelected");
		if (item.length == 0)
			return false;
		else if (item.length == 1)
			return item;
		else {
			//console.log("ERROR: multiple items selected");
			return item.first();
		}
	
	},
	
	/** Chooses an item by ID. Used by *Item Browser*. Rename? Perhaps to "chooseItemFromBrowser"?
	 *
	 *	This method is probably more generic and could be used for other
	 *	cases requiring item insertion. Except for the part that closes the
	 *  item browser...
	 **/
	selectItemFromBrowser : function (id) {
	
		var item = eos.jsonTreeData[id];
			
		// applies data to <LI> containing *Autocomplete Textbox*, etc
		eos.updateListItem(eos.activeItemLI, item);
		
		// removes *Autocomplete Textbox*, etc
		eos.removeItemSelectors();
		
		$('#browse-tree-items').dialog('close');
		
		eos.updateItemizedList();
		
		return false;
	
	},
	
	/** Applies data from *Chosen Item* to a particular *Selected Item*.
	 * 		Handles checkboxes, comments, item functions, standard children...
	 *	@param LI: the jQuery <li> to wipe clean, then insert the new item data
	 *		into.
	 *  @param newItemData: object containing new data to overwrite <li> with
	 *
	 *	Steps/Sections:
	 *		1) Changes itemID attribute of <li>
	 *		2) Changes itemName
	 *		3) Determines checkbox required, including whether they are
	 *             prechecked and if they have dialogText
	 *		4) Applies/appends checkbox info
	 *		5) Handles comment (show/hide based on anything present)
	 *		6) Adds item functions (context menu, etc)
	 *		7) Adds standard children using AJAX call
	 *
	 *	Uses:
	 *		1) On SELECT in getTextboxSelect() (i.e. when *Chosen Item* is
	 *             chosen using *Autocomplete Textbox*)
	 *		2) In selectItemFromBrowser() (i.e. when *Chosen Item* is chosen
	 *             using *Item Browser*)
	 **/
	// was: insertSelectedItem()
	// item used to be ui. all references to item used to be ui.item
	updateListItem : function (LI, newItemData) {
		
		// adds/changes item id, name, qty, comment, checkboxes of <LI>. 
		eos.modifyLI(LI, newItemData);
		
		// Adds context menu, exp/collapse, row highlight, and dblclick comment
		eos.addItemFunctions(LI);
		
		// checkboxes can have alerts onclick. This annunciates them if the 
		// checkbox is pre-checked when the item is added
		eos.doCheckboxAlerts(newItemData);

	},
	
	/**
	 *	@CurrentWork
	 *  @TODO : item.genericDialogText is not currently set by anything. It must
	 *    be created at some point, to allow new items to immediately annunciate
	 *    when they are added to the page. This will allow common items thought 
	 *    to be owned by the tools lab to be added, with statements like "This
	 *    item is owned by Staging. You cannot order it here."
	 **/
	doCheckboxAlerts : function (item) {

		if ( item.genericDialogText ) {
			alert( item.genericDialogText );
		}
	
		for (var type in item.checkboxes) {
			if (item.checkboxes[type].autoCheck && $.trim(item.checkboxes[type].dialogText) ) {
				alert( item.checkboxes[type].dialogText );
			}
		}
		return true;
	},
	
	/** HOW is this different from insertSelectedItem? How do they overlap? 
	 *  Should these two be refactored?
	 *
	 *	Steps/Sections:
	 *		1) Determines root and parent elements
	 *		2) Determines nesting-depth and indentation/offset
	 *		3) Clones hidden LI and applies offsets
	 *		4) Applies item info (generic and specific)
	 *		5) Adds checkboxes
	 *		6) Handles comment (show/hide based on anything present)
	 *		7) Adds item functions (context menu, etc)
	 *		8) Appends item to parent 
	 *		9) Loops through children, calling appendChildItem() on each child
	 *
	 *	Uses:
	 *		1) Itself: for each child item it calls itself
	 *		2) buildInitialTreeStructure()
	 *		3) insertSelectedItem(): for child items
	 *
	 **/
	// was: appendChildItem()
	createNewItemUnderParent : function (newItemData, parentUL) {
				
		// verifies parentUL is jQuery...might not be required.
		parentUL = $(parentUL);
		
		// clones <LI></LI> from template <LI>
		// calculates indent/offset, applies indent/offset
		var LI = eos.createChildLI(parentUL);
	
		// Applies data from newItemData to a particular LI
		eos.updateListItem(LI, newItemData)
	
		// attach the <LI> to the DOM
        $(parentUL).append(LI);
		
		eos.createChildItems(LI, newItemData);
		
    },

	/** Clones the template LI and applies an offset based upon the parent item
	 *
	 **/
	createChildLI : function(parentUL) {
	
		var rootId = $(parentUL).closest('.jsontree-root-ul').attr('rootid'); // root id required to find clone-item
			// note: if parentUL is rootUL, jQuery.closest() will still find it.
			// $.closest() begins at the current element, in this case "parentUL"
		var rootUL = $("#" + rootId + "-root-ul");
	
		var depth;
		
		if (parentUL.hasClass("jsontree-root-ul"))
			depth = 0;
		else {
			// jQuery.parents() finds all ancestors (matching selector). In this
			// case, we're finding and counting all ancestors to determine how 
			// much to indent an item.	
			depth = parentUL.parents(".itemUL").length + 1; 
		}
		
		var offset = parseInt(rootUL.attr("initoffset")) + (depth * parseInt(rootUL.attr("depthoffset")));


	
	
        // clone hidden LI
        var LI = $('#' + rootId + "-itemclone").html();
        LI = $(LI); //is there a prettier way to do this?

        LI.find(".firstcol").css("padding-left", offset + "px"); // indents expand/collapse button, qty input, item name
        //LI.find(".itemUnderline").css("left", -offset + "px"); REMOVED WHY? No more underlines displayed?
        LI.find(".dropBetween").css("left", offset + "px"); // indents line used for drag & drop. Drag & drop not coded yet (if ever)
	
		LI.find(".qty-input").first().change(function(){
			if ( isNaN($(this).val()) || $(this).val() < 1 ){
				$(this).val(0);
			}
			else {
				$(this).val( Math.floor($(this).val()) );
			}
			eos.updateItemizedList();
		});
	
		return LI;
	
	},
	
	itemClassPrefix : "itemClass", //prefix to class for each item ID; like .itemClass12 and .itemClass63
	
	/** Modifies the data within a given LI. if itemObj is an object, uses its
	 *		data. If it's a number, pull generic info with that number = id
	 **/
	modifyLI : function(LI, itemObj) {
	
		/**
		 *	If itemObj is an object, the LI will be populated with data 
		 *		from itemObj
		 * 
		 *	If itemObj is a number it is the desired item ID, and the LI 
		 *		will be populated with generic item data for that ID
		 **/
		var tempId = "no tempId";
		if ( ! itemObj.id ) { //if no itemObj.id, item is not an object.
			tempId = itemObj;
			itemObj = eos.getJsonTreeItemByID(itemObj);
		
			/*
			itemObj = {
				id : id,
				qty : eos.getItemAutoQtyByID(id),
				comments : eos.getItemAutoCommentByID(id),
				children : eos.getItemAutoChildrenByID(id),
				checkedBoxes : eos.getItemAutoCheckedBoxesByID(id)
			};*/
		}
	
		if (! itemObj) {
			try {
				console.log("itemObj.id not functioning in modifyLI(). Passed ID = " + tempId);
				console.log(itemObj);
			}
			catch(e){}
		}
		
		var oldID = LI.attr("itemId");

		// add/modify HTML attributes of LI
		LI.attr({
			itemId : itemObj.id,
			trainingHwTypes : (eos.getItemCheckboxOptionArrayByID(itemObj.id)).join(";")
		});

		// Change/add item name. Show element in case it's hidden.
		LI.find(".itemName").first().show().html( eos.getItemNameByID(itemObj.id) );
	
		// Change/add item quantity
		LI.find(".qty-input").first().val(itemObj.qty);

		eos.itemClassPrefix = "itemClass";
		if (LI.hasClass(eos.itemClassPrefix + oldID)) {
			LI.removeClass(eos.itemClassPrefix + oldID);
		}
		LI.addClass(eos.itemClassPrefix + LI.attr("itemId"));
		
		// Change/add comments
		eos.handleComment(LI, itemObj.comments);

		// builds checkboxes and inserts into LI
		// if 3rd parameter left off, will check boxes based on generic auto-check
		eos.handleCheckboxes(LI, itemObj.id, itemObj.checkedBoxes);
				
	},
	
	uniqueNumber : 0,
	getUniqueNumber : function () {
		eos.uniqueNumber++;
		return eos.uniqueNumber;
	}, 
	
	/**
	 *
	 **/
	handleCheckboxes : function (LI, itemId, checkedBoxes) {
	
		if ( ! checkedBoxes )
			checkedBoxes = eos.getItemAutoCheckedBoxesByID(itemId);
		
		// Object with key = item type (hi-fi, lo-fi, etc) and value = alert
		// text most items don't have alert text, though, so this is often an 
		// empty object.
		//var cboxAlerts = eos.getItemCheckboxAlertsByID(itemId);
		
		var allCheckData = eos.getItemCheckboxDataByID(itemId);
			
		var newCheckData = {};
		var count = 0;
		for(var typeName in allCheckData) {
			
			var prime_pn, prime_name;
			var subs = []; // substitute partnumbers
			var partnums = allCheckData[typeName]["partnums"];
			
			newCheckData[typeName] = {
				check : eos.in_array(typeName, checkedBoxes),
				alert : eos.getCheckboxAlertByIdAndType(itemId, typeName),
				partnums : eos.getCheckboxPartNumsByIdAndType(itemId, typeName)
			};
			count++;
		
		}
				
		// var typeHasCheckedBox = {};
		// for(var c=0; c<checkedBoxes.length; c++) {
			// typeHasCheckedBox[ checkedBoxes[c] ] = true;
		// }
		
		// local function for adding checkboxes
		var cboxAdderFn = function(type, check, checkboxNumber) {
		
			var attrs = {
				traininghwtype : type,
				value : 1
			};
			
			if (check)
				attrs.checked = 'checked';
		
			return $("<input type='checkbox' id='checkbox" + checkboxNumber + "' />")
				.css({
					position : 'relative',
					top : '2px'
				})
				.attr(attrs)
				.addClass('qty-input-check')
				.addClass('training_hw_' + eos.encodeJsonTreeCheckboxType(type))
				.click(function(){
					if (newCheckData[type].alert)
						alert(newCheckData[type].alert);
					eos.updateItemizedList();
				});
		
		};

		// local function for adding checkbox label
		var cboxLabelAdderFn = function(type, itemData, checkboxNumber) {
		
			/*
			itemData.partnums : [
			  { 'pn' : PARTNUMBER, 'opsnom' : OPSNOM },
			  { 'pn' : ... },
			  { ... }
			]
			*/ 
			
			return $("<label class='first-col-text checkbox-label' for='checkbox" + checkboxNumber + "'>" + type + "</label>");

		};
		
		var cboxWrapperFn = function(type, itemData) {
		
			var pnList = '';
			
			// see tooltip declaration at the bottom of this file
			for (var i=0; i<itemData.partnums.length; i++){
				pnList += itemData.partnums[i].pn + ':' + itemData.partnums[i].opsnom + ';';
			}
			
			return $("<span class='checkbox-wrapper' partnums='" + pnList + "' primepn='" + itemData.partnums[0].pn + "' typename='" + type + "'></span>");
		
		};
		
		var cboxColumn = $(LI).find(".types-column").first();
		cboxColumn.empty();
		
		for(var typeName in newCheckData) {
						
			var uniqueCheckboxNumber = eos.getUniqueNumber();
			
			cboxColumn.append(
			
				cboxWrapperFn(typeName, newCheckData[typeName])
				
					// add checkbox
					.append(cboxAdderFn(typeName, newCheckData[typeName].check, uniqueCheckboxNumber))
				
					// add label
					.append(cboxLabelAdderFn(typeName, newCheckData[typeName], uniqueCheckboxNumber))
				
			);
			
		}
			
	},
	
	/** Creates
	 *
	 **/
	createChildItems : function (LI, newItemData) {
		
		// if doesn't have id, it's not an object: it is the ID itself
		// pull object data from json tree generic data
		if ( ! newItemData.id ) { 
			
			/*var generic = eos.getJsonTreeItemByID(newItemData);
		
			newItemData = {
				id : generic.id,
				checkboxes : generic.checkboxes,
				comments : generic.autoComment,
				children : generic.autoChildren
			};*/
		
			newItemData = eos.getJsonTreeItemByID(newItemData);
		
		}
			
		if (newItemData.children) {

			for (var i=0; i < newItemData.children.length; i++) {

				eos.createNewItemUnderParent(  // recall this method
					newItemData.children[i], // pass each child
					$(LI).find(".itemUL").first() // <UL> into which child <LI> go
				); 					
			}
		}

	},

	
	
	
	
	
	
	
	
/**	JSONTree: Removing the *Chooser Item* (or just its components)
 *
 *
 *
 *
 *
 *
 *
 *
 **/
	
	/**	Strips *Autocomplete Textbox*, buttons, etc from DOM, leaving *Tree Item* that contains them
	 *
	 **/
	removeItemSelectors : function () {
	
		//doesn't appear can remove node while still in THIS is in scope. 
		//setTimeout so remove() methods occur after THIS no longer in scope.
		setTimeout(
			function(){
				//var inDiv = document.getElementById("inputDiv");
				//inDiv.parentNode.removeChild( inDiv );
				$("#cancelnew").remove();
				$("#newitem").remove();
				$("#item-selector-spacer").remove();
				$("#browse-dialog-button").remove();
				$("#canceledit").remove();
			},1 // timeout 1 millisecond...ASAP
		);

	},
	
	/** Removes *Tree Item* containing *Autocomplete Textbox* and *Item Browser* button.
	 *
	 **/
	cancelNewItem : function () {
		//get the LI of the input element
		//remove it from its parent.
		$("#cancelnew").closest(".itemLI").remove();
		eos.rebuildExpandCollapseImages();
	},

	/** Strips *Autocomplete Textbox*, buttons, etc from DOM, reinstating *Tree Item* present prior to editing. Overlap with removeItemSelectors()
	 *
	 **/
	cancelItemEdit : function () {
		
		var hiddenItemInfo = $("#newitem").closest(".itemLI").find(".itemName").first();

		$("#newitem").remove();
		$("#canceledit").remove();
		$("#browse-dialog-button").remove();
		$("#item-selector-spacer").remove();
		
		hiddenItemInfo.show();
		
	},

	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
    editItem : function () {

        var LI = $(".itemSelected").closest(".itemLI");

		eos.displayItemSelector(LI, true)
			
		eos.deselectItem();
			
        $("#newitem").focus().select();

    },
	
	/** Inserts comment text, shows parens if comment != ""
	 *
	 *	Used in: itemComment, appendChildItem, insertSelectedItem
	 **/
	handleComment : function (LI, comment) {

		//if (typeof comment == "string" && $.trim(comment) ==)
			
	
		if (comment) {
			$(LI).find(".comment-wrapper").first().css("display", "inline");						
		}
		else
			$(LI).find(".comment-wrapper").first().css("display", "none");						
		
		$(LI).find(".comment-display").first().html(
			comment
		);

	},
	
	itemComment : function (elemInsideLI) {
	
        var LI = $(elemInsideLI).closest(".itemLI");
		var commentVal = $(LI).find(".comment-display").first().html();
		//commentVal = commentVal.replace("'", '\'');
		//alert(commentVal);

		var dialog_id = "addItemComment";
		
		$("#" + dialog_id).remove();
	
		var D = $('<div title="Add Comment to Item" id="' + dialog_id + '"></div>');

		var addChangeFunction = function(){
			//alert('test: ' + $("#" + dialog_id + "-input").val() );
			var comm = $("#" + dialog_id + "-input").val();
			comm = comm.replace(/&/g, "&amp;").replace(/>/g, "&gt;").replace(/</g, "&lt;").replace(/"/g, "&quot;");

			eos.handleComment(LI, comm);
			
			$( "#" + dialog_id ).dialog( "close" );
		}
		
		D.dialog({
			//autoOpen: false,
			width: 500,
			//height: 300,
			modal: true,
			buttons : {
				Cancel : function () {
					$( "#" + dialog_id ).dialog( "close" );
				},
				"Add Change" : addChangeFunction
			}
		}).html(
			"<div style='text-align:center;padding:30px 0px'><input type='text' id='" + dialog_id + "-input' style='width:90%;' /></div>" //value='" + commentVal + "' 
		);
		
		$('#' + dialog_id +'-input').val(
			commentVal.replace(/&amp;/g, "&").replace(/&gt;/g, ">").replace(/&lt;/g, "<").replace(/&quot;/g, '"')
		);
		
		$("#" + dialog_id + "-input").keypress(function(event){
			if (event.keyCode == 13)
				addChangeFunction();
			return event.keyCode != 13;
		}).focus().select();
		
		//D.dialog('open');
		return false;
		
	},

	
	/** Deselects item, removes buttons useful only for selected items
	 *
	 **/
	deselectItem : function () {
		$('.first-col-hover-wrapper').removeClass('itemSelected').removeClass('sameAsItemSelected');
		$('.selectedItemButton').hide();
	},
	
	/** Marks item as selected, shows buttons which affect selected items
	 *
	 **/
	selectItem : function (elemOrSubElem) {
		
		var id;
		var hoverWrapper;
		
		if ($(elemOrSubElem).hasClass("first-col-hover-wrapper"))
			hoverWrapper = $(elemOrSubElem);
		else if ($(elemOrSubElem).hasClass("itemLI"))
			hoverWrapper = $(elemOrSubElem).find(".first-col-hover-wrapper").first();
		else
			hoverWrapper = $(elemOrSubElem).closest(".itemLI").find(".first-col-hover-wrapper").first();
		
		// selects item
		hoverWrapper.addClass("itemSelected");
		
		// jQuery.show() will not work...displays as block.
		$('.selectedItemButton').css("display","inline");

		id = hoverWrapper.closest(".itemLI").attr("itemId");
		$("." + eos.itemClassPrefix + id).each(function(index,element){
			//alert(index);
			$(element).find(".first-col-hover-wrapper").first().addClass("sameAsItemSelected");
		});
		
		
	},
	
	/** Adds context menu, expand/collapse, row highlight, and dblclick comment
	 *
	 **/
    addItemFunctions : function (itemLI) {

        $(itemLI).find('.exp-col-button').first().click(function () {

            eos.toggleExpandCollapse(itemLI);

        });		
		
		var highlightRow = function(elemInsideItem){
			$(elemInsideItem).closest(".item").css("background-color", "#FFF9D1"); //  FFF6BF
		}
		var deHighlightRow = function(elemInsideItem){		
			$(elemInsideItem).closest(".item").css("background-color", "transparent");			
		}
		
        $(itemLI).find('.first-col-hover-wrapper').first().unbind('hover').hover(
			function() {
				$(this).addClass("itemHovered"); // makes item darker yellow for hover. Does not overshadow blue of clicked item
				highlightRow(this);
			},
			function() {
				$(this).removeClass("itemHovered"); // removes darker yellow for item hover
				deHighlightRow(this);
			}
		);
		
        $(itemLI).find('.types-column').first().unbind('hover').hover(
			function(){highlightRow(this)},
			function(){deHighlightRow(this)}
		);
		
		$(itemLI).find(".qty-input").first().change(function(){			
			if ( isNaN($(this).val()) || $(this).val() < 1 ){
				$(this).val(0);
			}
			else {
				$(this).val( Math.floor($(this).val()) );
			}
			eos.updateItemizedList();
		});

		
		
        $(itemLI).find('.first-col-hover-wrapper').first().unbind('click').click(function() {
			
			eos.deselectItem();
			eos.selectItem(this);
			$("#myMenu").css("display", "none");
						
			return false;
			
        }).unbind('dblclick').dblclick(function(){
			eos.itemComment($(itemLI).children().first());
        });

        eos.jsonTreeContextMenu( $(itemLI).find('.first-col-hover-wrapper').first() ); //was on .itemName

	},
	
	getItemInfo : function (element) {
	
		var itemLI = $(element).closest(".itemLI");
			//setTimeout(function(){ alert('test2'); }, 500);
		$("#itemInfo").remove();
	
		var D = $('<div title="Loading Item Info..." id="itemInfo"></div>');

		D.dialog({
			//autoOpen: false,
			width: "80%",
			height: $(window).height() - 150,
			modal: true,
			dialogClass: "fixed-dialog"
		});
		
		$('.fixed-dialog.ui-dialog').css({position:"fixed", top:50});

		$.get(
			"tools_catalog.php",
			{ id : $(itemLI).attr("itemid"), v : "form_only" },
			function (data) {
				$("#itemInfo").dialog({title: "Item Info"});
				$("#itemInfo").css("margin","0 0 15px 0").html(data);
				$(".eos-button").button();
			},
			"html"
		);
		
		//D.dialog('open');
		return false;
	},
	

	
	
	
	
	
	
	
	
	

	
	
	
	
/**	JSONTree: Expand/Collapse Methods. Or should this be more broad like "Tree Display Methods"
 *
 *
 *
 *
 *
 **/
 
	/**
	 *
	 **/
    toggleExpandCollapse : function (itemLI, direction, expFunc, colFunc) {

        //argument direction optional: causes only one direction possible...
    
        var ul = $(itemLI).find('.itemUL').first();
        var img = $(itemLI).find('.exp-col-button').first();
		var numChildren = ul.find('.itemLI').length;
		
		if (numChildren > 0) {
		
			if(  $(ul).is(':visible') ){

				if (direction != 'expand') {    
					$(ul).hide('blind',{},100, colFunc);
					$(img).attr('src', 'images/expand.png');
				}

			}
			else{

				if (direction != 'collapse') {
					$(ul).show('blind',{},100, expFunc);
					$(img).attr('src', 'images/collapse.png');
				}

			}

		}
		else {
			
			$(img).attr('src', 'images/no-exp-col.png');
			
		}
            
    },

	/**
	 *
	 **/	
	rebuildExpandCollapseImages : function () {
	
		var img;
	
		$(".itemLI").each(function(index, element){
			
			if ($(element).find(".itemUL").first().children().length == 0)
				img = "no-exp-col.png";
			else if ($(element).find(".itemUL").first().is(":visible"))
				img = "collapse.png";
			else
				img = "expand.png";
			
			$(element).find(".exp-col-button").first().attr('src', 'images/' + img);
			
		});
	
		/*
			foreach .itemLI {
			
				if closest $(.itemUL).length == 0
					make no-exp-col.png
				else if ($(ul).is(':visible'))
					make minus sign
				else
					make plus sign...right?
			
			}
		*/
		
	},
	
	/**
	 *
	 **/
	expandAllItems : function () {
		
		$(".itemLI").each(function(index,elem){
			eos.toggleExpandCollapse($(elem), "expand");
		});
		
	},

	/**
	 *
	 **/	
	collapseAllItems : function () {

		$(".itemLI").each(function(index,elem){
			eos.toggleExpandCollapse($(elem), "collapse");
		});
	
	},
	
	/**
	 *
	 **/
	collapseTopLevelItems : function () {
	
		$("#tools-root-ul").children("li").each(function(index,elem){
			eos.toggleExpandCollapse($(elem), "collapse");
		});
	
	},

	/** Calculates and applies indent of a given *Tree Item* and sub-items
	 *
	 **/
    adjustIndents : function (li) {

        var initOffset  = parseInt( 
            $(li).parents('.jsontree-root-ul').first().attr('initoffset') //closest()?
        );
        
        var depthOffset = parseInt(
            $(li).parents('.jsontree-root-ul').first().attr('depthoffset') //closest()?
        );

       // alert(initOffset + " and " + depthOffset);

        var parent = $(li).parent().closest(".itemLI");

        var offset;

        if(parent.length == 0) {
            //parent == root
            offset = initOffset;
        }
        else {

            offset = parseInt(
                $(parent).find(".firstcol").first().css("padding-left")
            );

            //alert(offset);

            offset += depthOffset;

        }
        
        
        $(li).find(".firstcol").css("padding-left", offset + "px");
        $(li).find(".dropBetween").css("left", offset + "px"); // for drag&drop, non-existant function. Wanted to leave capability.

		// adjust indents for all child elements
        $(li).find(".itemUL").first().children('.itemLI').each(function(index,element){
            eos.adjustIndents(element);
        });
    
    },
	
	
	

	
	
	
	
	
	
	
	
	
	
	
/** JSONTree: Context Menu methods
 *
 *
 *
 *
 *
 *
 *
 *
 **/
	
    jsonTreeContextMenu : function (selector) {
		
        $(document).ready( function() {
		
			$(selector).mousedown(function(event){
				if (event.which == 3) {
					$('.first-col-hover-wrapper').removeClass('itemSelected'); 
					if ( $(selector).closest(".itemLI").size() > 0 )
						$(this).addClass('itemSelected');
				}
			});
				
            $(selector).contextMenu(
                { menu: 'myMenu' },
                function(action, el, pos) {
				
                    switch(action) {
                        case "add":
                            eos.addItem(el);
							eos.rebuildExpandCollapseImages();
                            break;
                        case "edit":
                            eos.editItem(el);
                            break;
						case "comment":
							eos.itemComment(el);
							break;
						case "cut":
                            eos.cutItem(el);
							eos.rebuildExpandCollapseImages();
                            break;
                        case "copy":
                            eos.copyItem(el);
                            break;
                        case "paste":
                            eos.pasteItem(el);
							eos.rebuildExpandCollapseImages();
                            break;
						case "info":
							eos.getItemInfo(el);
							break;
						case "moveup":
							eos.contextMenuMoveItemUp(el);
							break;
						case "movedown":
							eos.contextMenuMoveItemDown(el);
							break;
                        case "delete": //same as cut...deprecated
                            eos.deleteItem(el);
							eos.rebuildExpandCollapseImages();
                            break;
                        //default:
                    }
                    
                }
            );
			
			


        });

    },
	
    addItem : function () {

		//eos.deselectItem();
	
        var rootId = $('.jsontree-root-ul').first().attr('rootid');
        var lookupURL = $('.jsontree-root-ul').first().attr('lookupurl');
        var newLocation;
        var offset;

        if ( $(".itemSelected").length == 0 ) {
            newLocation = 'root';
            offset = 1234; //$(elemInsideLI).attr('initoffset');
        }
        else {
            newLocation = $(".itemSelected").first(); //only allow single addition...
            offset = 322; //not needed...handled in getNewJSONTreeItem
        }

        eos.toggleExpandCollapse( 
            $(newLocation).parents('.itemLI').first(),
            'expand',
            eos.getNewJSONTreeItem(rootId, newLocation, offset, lookupURL)
        );

    },

    copyItem : function () {
    //$('#{$this->htmlId}-copyButton').click(function () {
    
        eos.copiedItem = $(".itemSelected").closest('.itemLI');
		$("#paste-menubar-button").css("display","inline");
		
    },

    cutItem : function () {
    //$('#{$this->htmlId}-cutButton').click(function () {
	
		eos.copyItem();
        
		var cutItem = $(".itemSelected");
		
		var newSelected = cutItem.closest(".itemLI").prev();
		if (newSelected.length == 0)
			newSelected = cutItem.closest(".itemLI").next();
		if (newSelected.length == 0)
			newSelected = cutItem.closest(".itemUL").closest(".itemLI"); 
		
		newSelected = newSelected.children().first(); // really innefficient
		
		newSelected = eos.selectItem(newSelected);
		
		$(cutItem).closest('.itemLI').remove();
		
		eos.updateItemizedList();
    },

    pasteItem : function () {

		var itemSelected = $(".itemSelected");
	
        if ( itemSelected.length == 0 )
            var ul = $(".jsontree-root-ul"); //not really an element inside the LI.
        else
            var ul = itemSelected.closest(".itemLI").find(".itemUL").first();     

        var newItem = $(eos.copiedItem).clone();

        $(newItem).appendTo(ul);

        //has to come after append, in order to grab new indent info
        eos.adjustIndents(newItem);
        
        eos.addItemFunctions(newItem);

		eos.deselectItem(); // remove all selects (for some reason both parent and new item are selected)
		eos.selectItem(itemSelected); // reselect only the parent
		
        $(newItem).find('.itemLI').each(function(index, element) {
            eos.addItemFunctions(element);
        });
		
		eos.updateItemizedList();
        
    },
	
	contextMenuMoveItemUp : function (elemInsideLI) {
		
		var LI = $(elemInsideLI).closest(".itemLI");
        var prev = $(LI).prev();

        if (prev.length != 0) {
            $(prev).before(LI);
        }
		
	},
	
	contextMenuMoveItemDown : function (elemInsideLI) {
		
        var LI = $(elemInsideLI).closest('.itemLI');
        var next = $(LI).next();

        if (next.length != 0)
            $(next).after(LI);		
		
	},
		
    moveSelectedItemUp : function () {

        var LI = $('.itemSelected').closest('.itemLI');
        var prev = $(LI).prev();

        if (prev.length != 0) {
            $(prev).before(LI);
        }

		return false; //don't bubble. Deselects Item
        
    },

    moveSelectedItemDown : function () {

        var LI = $('.itemSelected').closest('.itemLI');
        var next = $(LI).next();

        if (next.length != 0)
            $(next).after(LI);

		return false; //don't bubble. Deselects Item
			
    },




	
	
	
	
		
	
	// was "createItemizedList"...update in progress.
	updateItemizedList : function () {
	
		var iList = {};
	
		$(".itemLI").each(function(index,elem){
			
			var name = $(elem).find(".itemName").first().html();
			var baseQty = $(elem).find(".qty-input").first().val();
			baseQty = parseInt(baseQty);
			
			//var types = $(elem).attr("traininghwtypes").split(";");
			
			$(elem).find(".types-column").first().find("input:checked").each(function(i,e){
		
				var type = $(e).attr("traininghwtype");
				
				if ( ! iList[name] ) 
					iList[name] = {};
					
				if ( ! iList[name][type] )
					iList[name][type] = baseQty;
				else
					iList[name][type] += baseQty;

					
			});
			
			
		});

		var html = "<table class='armory-table-basic tablesorter'><thead><tr>" +
			"<th class='ui-widget-header'>Tool</th><th class='ui-widget-header'>Fidelity</th><th class='ui-widget-header'>Qty</th></tr></thead><tbody>";
		
		for (var itemName in iList) {

			for (var type in iList[itemName]) {		
				html += "<tr><td>" + itemName + "</td><td>" + type + "</td><td>" + iList[itemName][type] + "</td></tr>";
			}
		
		}
		
		html += "</tbody></table>";
		
		html = $(html).tablesorter({widgets : ["zebra"]});
		
		$("#itemized-list-wrapper").html(html);
		
	}
	
};

$(document).ready(function(){
	
	// tooltip declaration
	$(document).tooltip({
		items : "[partnums]",
		content : function () {
			var element = $( this );
			
			if ( element.is( "[partnums]" ) ) {
				var text = element.attr( "partnums" );
				var partnums = text.split(";");
				var primePN = partnums[0].split(':');
				var output =  '<strong>' + primePN[1] + '</strong><br />' + primePN[0];
				var tempPN;
				
				// if more than one part number
				if (partnums[1]) {
					output += '<br /><br /><strong>Substitute P/Ns:</strong><br /><ul>';
				
					for (var i=1; i<partnums.length; i++) {
						tempPN = partnums[i].split(':');
						if ( tempPN[0] ) 
							output += '<li>' + tempPN[0] + ': ' + tempPN[1] + '</li>';
					}
					
					output += '</ul>';
				}
				else {
					output += '<br /><br /><strong>No substitute P/Ns</strong>';
				}
				
				output += "";
				return output;
			}
		
		}
	});

});