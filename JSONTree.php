<?php
/**
 *	@author: James Montalvo
 *	@email: jamesmontalvo3@gmail.com
 *	@copyright: Copyright (C) 2010, James Montalvo
 *	@license: http://opensource.org/licenses/mit-license MIT License
 **/
 

class JSONTree extends FormEntity {

    public $fields;
    public $minrows;
    public $minrowsview;
    public $minrowsedit;
    public $baseclass;
    public $sql;
    public $itemized_list;
	public $checkbox_spacing = 1;

    public function __construct (
        $names,
        $table_name, // table to grab all items from
        $db_ini_file,    // pass a MysqlQuery object...perhaps not necessary...but then would have to pass user/pass/host/database as well.
        $lookupURL,  // url used for ajax lookup of items...perhaps file should use this class to generate? Some way to call this file?
		$editURL, // non-ajax page...standard database and item create/edit/view
        $initial_offset=8,
        $depth_offset=30,
        $styleArray=array(),
        $validation=array() ) {

        parent::__construct ($names, //$sqlname, $htmlname, $label, $longlabel, 
            $styleArray=array(), $validation);

        #
        #   create pre-fetched array of item info...
        #
        //$this->table_name = $table_name; //necessary to keep this around? probably not...

		$this->item_edit_URL = $editURL;
		
        $this->table_name = $table_name;

        $db = BasicPHP::connect_to_db($db_ini_file) or die("no database ini file");
        $this->sql = new MysqlQuery($db, $table_name);

        $temp_array = $this->sql->exe("SELECT * FROM {$table_name}");
        foreach ($temp_array as $row) {
            $this->all_item_info[$row[id]] = $row;
        }

        //var_dump($this->all_item_info);

        $this->value = array(); //required preset to empty value...will get val from DB later (if available)

        $this->lookupURL = $lookupURL;
        
        $this->rowname = isset($names[rowname]) ?
            $names[rowname] : $this->sqlname;

        $this->baseclass = "form-entity-standard-json-table";
        $this->format->addClass($this->baseclass);

        $this->initial_offset = $initial_offset;
        $this->depth_offset = $depth_offset;

        /*$this->fields = $fields;

        // not sure why this is required...
        foreach ($this->fields as $varname => $FormEntity)
            $FormEntity->format->addClass($varname);

        $this->minrows = ($table_layout[minrows]) ? 
            $table_layout[minrows] : 3;

        $this->minrowsview = ($table_layout[minrowsview]) ? 
            $table_layout[minrowsview] : $this->minrows;

        $this->minrowsedit = ($table_layout[minrowsedit]) ? 
            $table_layout[minrowsedit] : $this->minrows;

        // individual fields within JSON table are not submittable...
        // ...compiled into single HIDDEN field.
        foreach ($this->fields as $varname => $FormEntity)
            $FormEntity->format->removeClass("submittable-field");*/

    }

    public function getPHPClassName () {
        return get_class();
    }

    public function setValue ($val) {
        $val = json_decode($val, true);

        if ($val) {
            $this->value = $val;
            return $val;
        } else {
            $this->value = array();
            return array();
        }
        
    }
    
    public function getDisplayValue ($singleColumn=false) {
        //return $this->get_JSON_tree(false);
		return $this->get_JSON_tree_viewer_no_LI();
        //return "display currently disabled";
    }  

    public function getEditorHTML ($singleColumn=false) {
        return $this->get_JSON_tree(true);
    }

    /*public function get_JSON_table ($edit=false, $singleColumn=false) {

        // more recognizable name
        $row_list = $this->value;


        #
        #   CREATE HIDDEN INPUT TO MANAGE FormEntity SUBMISSION
        #   javascript will find table related to this <input> by id=thisID . "-table"
        #
        $output .= "<input type='hidden' {$this->getNameIdString()} class='submittable-field' value='' 
            fetype='{$this->fetype}' jsontablevars='";

        // listing table variables by varname:htmlId for javascript to use
        foreach ($this->fields as $varname => $FormEntity) 
            $output .= "$varname:{$FormEntity->htmlId};";

        $output .= "' />";


        #
        #   START TABLE AND CREATE HEADER ROW
        #
        $output .= "<table {$this->getNameIdString('-table')}>";

        if (  $singleColumn  ) {

            // uhhhh...what the hell? this does nothing, right? some formatting workaround? counting for adding more rows dynamically?
            $output .= "<tr class='{$this->baseclass}-headerRow'><td></td></tr>";

        } else {

            $output .= "<tr class='{$this->baseclass}-headerRow'>";

            foreach ($this->fields as $varname => $FormEntity) { 

                $col_label = $FormEntity->label;
            
                $output .= "<th class='{$this->baseclass}-$col_label'>$col_label</th>";
            }

            $output .= "<td></td></tr>";

        }


        // unique number for each row
        $count = 0;


        #
        #   CREATE ROWS WITH DATA FROM DATABASE
        #
        foreach ($row_list as $row) {

            $output .= $this->table_row($count, $edit, $row);
            $count++;
            
        }


        #
        #   CREATE ADDITIONAL BLANK ROWS AS REQUIRED
        #
        if ($edit || $showViewMinRows) {

            $current = $count;
            for ($i=$current; $i<$this->minrows; $i++) {
                $output .= $this->table_row($count, $edit);
                $count++;
            }

        }

        $output .= "</table>";
        
        $something = $this->table_row('_#_ROWINDEX_#_', true);
        //$something = str_replace("'", '"', $something);
        //$something = str_replace('"', "\\'", $something);

       // $something = "<tr><td></td><td></td></tr>";

        if ($edit) $output .= "<input type='button' id='{$this->htmlId}_addrow' value='add row' />" .
            "<script text='text/javascript'>
                document.getElementById('{$this->htmlId}_addrow').onclick = function () {

                    var testIndex;
                    var rowIndex = 0;

                    $('#{$this->htmlId}-table tr').each(function() {
                        testIndex = parseInt ($(this).attr('rowindex') );
                        if (testIndex > rowIndex) 
                            rowIndex = testIndex;
                    });

                    rowIndex += 1;

                    var row_html = $('#crazytable tr').parent().html();

                    row_html = row_html.replace(/_#_ROWINDEX_#_/g, rowIndex);
                    
                    $('#{$this->htmlId}-table tr:last').after(row_html);

                }
            </script>
            <table id='crazytable' style='display:none;'>$something</table>";

        return $output;

    }*/


    public function get_JSON_tree ($edit=false) {

        $tree_data = $this->value;
		
        #
        #   CREATE HIDDEN INPUT TO MANAGE FormEntity SUBMISSION
        #   javascript will find table related to this <input> by id=thisID . "-table"
        #
        if ($edit)
            $output .= "<input type='hidden' {$this->getNameIdString()} class='submittable-field' value='' 
                fetype='{$this->fetype}' />";

    
        $output .=
            "<div class='wrapperDiv jquery-section-block'>";

            
        if ($edit) {
            $output .=
                //"<span id='{$this->htmlId}-addrootchild' class='linkspan'>add item</span>";
                "<div class='json-tree-menu ui-widget-header ui-corner-top'>".
                    "<table style='width:100%;'><tr>".
                        "<td id='json-tree-titlebar' class='firstcol'>".
							'<span class="json-tree-title">Tool Config Editor</span>(<a class="eoslink" target="_blank" href="http://topssnet.ndc.nasa.gov/topssnet2/predive-test/wiki/index.php?title=Tool_Matrix_Users\'_Guide">Help</a>)'.
                        "</td>".
                    "</tr></table>".
                "</div>";
		
			ob_start();
			?><script type='text/javascript'>

				var expAll = $('<button>Expand All</button>').button().click(function(){  //{icons: {primary: 'ui-icon-circle-triangle-s'}}
					eos.expandAllItems();
					return false;
				}).button();
				var colAll = $('<button>Collapse All</button>').button().click(function(){ // {icons: {primary: 'ui-icon-circle-triangle-e'}}
					eos.collapseAllItems();
					return false;
				}).button();
				var colTop = $('<button>Collapse Top Level</button>').button().click(function(){  // {icons: {primary: 'ui-icon-circle-triangle-e'}}
					eos.collapseTopLevelItems();
					return false;
				}).button();
				var toggleTypeCol = $('<button>hide types column</button>').click(function(){
					if ( $('.types-column').css('display') == 'none' ) {
						$('.types-column').css('display', 'inline');
						$(this).button('option', 'label', 'hide types column');
					}
					else {
						$('.types-column').css('display', 'none');
						$(this).button('option', 'label', 'show types column');
					}
					return false;
				}).button();
				var help = $('<button>Help</button>').button({icons: {primary: 'ui-icon-help'}}).click(function(){  // 
					window.open("http://topssnet.jsc.nasa.gov/topssnet2/predive-test/wiki/index.php?title=Tool_Matrix_Users%27_Guide");
					return false;
				}).button();
			
				
				
			
			
				$(document).ready(function(){
					
					var disp = "inline-block";
					
					var add = $('<button>Add Tool</button>').button({icons: {primary: 'ui-icon-plusthick'}}).click(function(){
						eos.addItem('.itemSelected');
						return false;
					}).css("display", disp);
					
					var edit = $('<button>Edit</button>').button({icons: {primary: 'ui-icon-pencil'}}).click(function(){
						eos.editItem('.itemSelected');
						return false;
					}).addClass('selectedItemButton').hide();
					
					var cut = $('<button>Cut</button>').button({icons: {primary: 'ui-icon-scissors'}}).click(function(){
						eos.cutItem('.itemSelected');
						return false;
					}).addClass('selectedItemButton').hide();
					
					var copy = $('<button>Copy</button>').button({icons: {primary: 'ui-icon-clipboard'}}).click(function(){
						eos.copyItem('.itemSelected');
						return false;
					}).addClass('selectedItemButton').hide();
					
					var paste = $('<button>Paste</button>').button({icons: {primary: 'ui-icon-copy'}}).click(function(){
						eos.pasteItem('.itemSelected');
						return false;
					}).attr('id','paste-menubar-button').hide(); // show on "copyItem"
					
					var itemUp = $('<button>Move Up</button>').button({icons: {primary: 'ui-icon-arrowthick-1-n'}}).click(function(){
						eos.moveSelectedItemUp(this);
						return false;
					}).addClass('selectedItemButton').hide();
					
					var itemDown = $('<button>Move Down</button>').button({icons: {primary: 'ui-icon-arrowthick-1-s'}}).click(function(){
						eos.moveSelectedItemDown(this);
						return false;
					}).addClass('selectedItemButton').hide();

					// thought about making some hot keys...not for now, though.
					// $(document).keypress(function(ev){
						// // up key: 38
						// // down key: 40
						// //ev.which
						
						// // if an item is selected
						// if (item = eos.getSelectedItemLI) {
							
						// }
					// });
					

					var separator = '<span style=\"margin: 0px 5px;\"></span>';

					
					
					$('body').append(
						$('<div></div>')
							.attr('id', 'tools-footer')
							.addClass('ui-corner-top ui-widget-header') //ui-smoothness-widget-header
							.append(add, edit, cut, copy, paste, separator, itemUp, itemDown, separator)
					);
					
				});
				
			</script><?php
			
			$output .= ob_get_clean();

        }
        
		if ($edit)
			$bufferDivStyle = " style='background-color:white;' "; //height:500px;
		else
			$bufferDivStyle = "";
		
        $output .= "<div class='bufferDiv' id='bufferDiv-{$this->htmlId}'$bufferDivStyle>".
            "<ul id='{$this->htmlId}-root-ul' 
                class='jsontree-root-ul' 
                initoffset='{$this->initial_offset}' 
                depthoffset='{$this->depth_offset}' 
                rootid='{$this->htmlId}'
                lookupurl='{$this->lookupURL}' style='padding-bottom: 100px;' >";

        // foreach ($tree_data as $item_data) {
           // $output .= $this->tree_item(0, $item_data, $edit);
        // }

		//echo "<pre>".print_r(json_encode($tree_data))."</pre>";
		
		$encoded_tree_data = str_replace("'", "\'", json_encode($tree_data));
		
		ob_start();
		?>
		<script type='text/javascript'>
			$(document).ready(function(){
				window.rawJsonTreeString = '<?php echo $encoded_tree_data; ?>'; //unfortunate use of global...
				eos.initiateJsonTree();
				//console.log('initiated...');
			});
		</script>
		<?php
		$output .= ob_get_clean();
		
        $output .=
                    "</ul>". //close root ul
                "</div>". // close bufferDiv
				//"<div style='text-align:right;'><a href='javascript:expandBufferDiv(\"bufferDiv-{$this->htmlId}\")'>expand</a></div>".
				"<script type='text/javascript'>
				
					function expandBufferDiv (id) {
						var h = $('#' + id).css('height');
						h = parseInt(h);
						
						h += 500;
						
						$('#' + id).css('height', h + 'px');
					}
					
				</script>".
            "</div>"; // close wrapper div?

        if ($edit) {
            $output .= "<ul id='{$this->htmlId}-itemclone' style='display:none'>" . $this->tree_item("javascript", false, $edit) . "</ul>";

            
			$output .=
                
                "<ul id='myMenu' class='contextMenu' style='width:150px;'>".
                    
					// add and edit
					"<li class='add'>".
                        "<a href='#add'>Add Item</a>".
                    "</li>".
                    "<li class='edit'>".
                        "<a href='#edit'>Edit Item</a>".
                    "</li>".
					
					// cut, copy, paste
                    "<li class='cut separator'>".
                        "<a href='#cut'>Cut / Delete</a>".
                    "</li>".
                    "<li class='copy'>".
                        "<a href='#copy'>Copy</a>".
                    "</li>".
                    "<li class='paste'>".
                        "<a href='#paste'>Paste</a>".
                    "</li>".
					
					// move up and down
                    "<li class='moveup separator'>".
                        "<a href='#moveup'>Move Item Up</a>".
                    "</li>".
                    "<li class='movedown'>".
                        "<a href='#movedown'>Move Item Down</a>".
                    "</li>".

					// comment and info
                    "<li class='comment separator'>".
                        "<a href='#comment'>Edit Comment</a>".
                    "</li>".
                    "<li class='info'>".
                        "<a href='#info'>Get Info</a>".
                    "</li>".
					
                    /*"<li class='delete separator'>".
                        "<a href='#delete'>Delete</a>".
                    "</li>".*/
                "</ul><script>$('#myMenu').appendTo('body');</script>";
			
			$output .= $this->get_item_browser_dialog();
			
			
			
			
            /*
                NEED SCRIPT TO ADD ITEM TO TREE.
            */
			ob_start();
			?>
			<script type='text/javascript'>
			
				$('#<?php echo $this->htmlId; ?>-root-ul').find('.itemLI').each(function(index,element){
					eos.addItemFunctions(element);
				});
					
				//$('#<?php echo $this->htmlId; ?>-root-ul').click(function(){
				$("body").click(function(){
					eos.deselectItem();
				});
					
				eos.jsonTreeContextMenu('.first-col-hover-wrapper'); //was on .itemName
				eos.jsonTreeContextMenu('#<?php echo $this->htmlId; ?>-root-ul');
				
				
				
			</script>
			<?php
	
			$output .= ob_get_clean();

	
                    /*
                    $('#{$this->htmlId}-upButton').click(function () {

                        var LI = $('.itemSelected').closest('.itemLI');
                    
                        //var LI = $(this).closest('.itemLI'); 
                        var prev = $(LI).prev();

                        if (prev.length != 0) {
                            $(prev).before(LI);
                        }
                        
                    });


                    $('#{$this->htmlId}-downButton').click(function () {

                        var LI = $('.itemSelected').closest('.itemLI');

                        //var LI = $(this).closest('.itemLI'); 
                        var next = $(LI).next();



                        if (next.length != 0)
                            $(next).after(LI);

                    });*/

                    
                    /*
                    $('#{$this->htmlId}-addButton').click(function () {

                        var parent = $('.itemSelected').closest('.itemLI');
                        
                        var rootId = \"{$this->htmlId}\";
                        var newLocation;
                        var offset;
                        var lookupURL = \"{$this->lookupURL}\";

                        var initOffset  = $('#{$this->htmlId}-root-ul').attr('initoffset');
                        var depthOffset = $('#{$this->htmlId}-root-ul').attr('depthoffset');

                        if (parent.length == 0) {
                            newLocation = 'root';
                            offset = initOffset;
                        }
                        else {
                            newLocation = $('.itemSelected').first();
                            offset = $(parent).children('.firstcol').first().css('padding-left');
                            offset = parseInt(offset);
                            offset = offset + depthOffset;
                            offset = offset + 'px';
                        }


                        eos.toggleExpandCollapse( 
                            $(newLocation).parents('.itemLI').first(),
                            'expand',
                            eos.getNewJSONTreeItem(rootId, newLocation, offset, lookupURL)
                        );

                    });


                    $('#{$this->htmlId}-deleteButton').click(function () {
                    
                        $('.itemSelected').closest('.itemLI').remove();

                    });


                    $('#{$this->htmlId}-copyButton').click(function () {
                    
                        eos.copiedItem = $('.itemSelected').closest('.itemLI');

                    });


                    $('#{$this->htmlId}-cutButton').click(function () {

                        eos.copiedItem = $('.itemSelected').closest('.itemLI');
                        $('.itemSelected').closest('.itemLI').remove();

                    });

                    $('#{$this->htmlId}-pasteButton').click(function () {

                        if ($('.itemSelected').length != 0)
                            var ul = $('.itemSelected').closest('.itemLI').find('.itemUL').first();
                        else
                            var ul = $('#{$this->htmlId}-root-ul');
                               
                        var newItem = $(eos.copiedItem).clone();

                        $(newItem).appendTo(ul);

                        //has to come after append, in order to grab new indent info
                        eos.adjustIndents(newItem);
                        
                        eos.addItemFunctions(newItem);

                        $(newItem).find('.itemLI').each(function(index, element) {
                            eos.addItemFunctions(element);
                        });
                        
                    });*/
				
				
        }

        
        return $output;

    }
	
    public function get_JSON_tree_viewer () {

        $tree_data = $this->value;

    
        $output .=
            "<div class='wrapperDiv'>";
        		
        $output .= "<div class='bufferDiv-view-only' id='bufferDiv'>".
            "<ul id='{$this->htmlId}-root-ul' 
                class='jsontree-root-ul' 
                initoffset='{$this->initial_offset}' 
                depthoffset='{$this->depth_offset}' 
                rootid='{$this->htmlId}'
                lookupurl='{$this->lookupURL}'>";

        foreach ($tree_data as $item_data) {
            $output .= $this->tree_item_viewer($item_data);
        }

        $output .=
                    "</ul>".
                "</div>".
            "</div>";

        
        return $output;

    }
		
    public function get_JSON_tree_viewer_no_LI () {

        $tree_data = $this->value;

    
        $output .=
            "<div class='wrapperDiv'>";
        		
        $output .= "<div class='bufferDiv-view-only' id='bufferDiv'>";
		/* "<ul id='{$this->htmlId}-root-ul' 
                class='jsontree-root-ul' 
                initoffset='{$this->initial_offset}' 
                depthoffset='{$this->depth_offset}' 
                rootid='{$this->htmlId}'
                lookupurl='{$this->lookupURL}'>"*/
		
		
        foreach ($tree_data as $item_data) {
            $output .= $this->tree_item_viewer_no_LI($item_data, 0);
        }

        $output .=
                    //"</ul>".
                "</div>".
            "</div>";

        
        return $output;

    }
	
	
	
    /* REQUIRES NO SPECIAL: javascript inserts value into hidden input
        and directly inserts in database (for now, needs scrubbing for safety later)
	public function getValFromForm ($type="POST") {
    }*/
    public function tree_item ($depth=0, $item=false, $edit=false) {//$id=-1, $qty=1, $comment='') {

        if ($depth === "javascript")
            $offset = "_#offset#_";
        else
            $offset = $this->initial_offset + ($this->depth_offset * $depth);

        // specific info about the item: id (ie what item it is), qty, specific comments to this item usage, children
        if ( ! $item )
            $item = array(
                "id"        => -1,
                "qty"       => 1,
                "comment"   => '',
                "children"  => array()
            );

        if ($offset == "_#offset#_")
            $child_offset = "_#child_off#_";
        else
            $child_offset = $offset + $this->depth_offset;

        // generic info about the item (name, P/N, generic comments, etc)
        $generic_info = $this->all_item_info[$item[id]];
        //var_dump($generic_info);

		
		$hw_type_html = "";
		if ($generic_info[training_hw]) {
		
			$training_hw = json_decode($generic_info[training_hw], true);
			$hw_types = array();
			foreach ($training_hw as $row) {
				if ( ! in_array($row['type'], $hw_types) )
					$hw_types[] = $row['type'];
			
				
			}
			
			$training_hw_types = implode(';',$hw_types);	
			
			//var_dump($item["training_hw"]);
			
			foreach ($hw_types as $type) {

				if ($item["training_hw"][$type])
					$checked = "checked='checked'";
				else
					$checked = "";
				
				$hw_type_html .=
					"<input type='checkbox' style='position:relative;top:2px;' traininghwtype='$type' 
						$checked class='qty-input-check training_hw_$type' value='1' onclick='eos.checkboxDialogAlert(\"" . "this is a test" . "\");' /><label class='first-col-text'>$type</label> ";

			}	
					
		
		}

		if (true) //(count($training_hw) > 0)
			$qty_textbox = "<input type='text' class='qty-input' value='{$item[qty]}' maxlength='2' /> ";
		else
			$qty_textbox = "";
		

        $output .=
            "<li class='itemLI' itemId='{$item[id]}' trainingHwTypes='$training_hw_types'>". // removed: itemQty='{$item[qty]}' itemComment='{$item[comment]}' genericComment='{$generic_info[comment]}'
                "<div class='itemDiv item'>". //style='margin-left:{$offset}px;'

                    "<div class='dropBetween' style='left:{$offset}px;'></div>"; //should probably be called dropAbove 


        if ($edit) {
		
			if ($item[comments])
				$comm_wrapper_display = "inline";
			else
				$comm_wrapper_display = "none";
				
			if (is_array($item['children']) && count($item['children']) > 0)
				$image_src = 'images/collapse.png';
			else
				$image_src = 'images/no-exp-col.png';
		
		
            $output .=
                    "<table class='itemTable'><tr>".

                        "<td class='firstcol' style='padding-left:{$offset}px;'>".
  /*                          
                            //"<div class='dragLayer'></div>". R&R'd with <img> handle
                            //"<img src='images/handle.png' class='dragLayer' />".
                            "<img src='images/collapse.png' class='itemIcon' />".
                            "<span class='itemName'>{$generic_info[name]}</span>".
*/

                            "<div class='firstcol-content' style='right:-{$offset}px'>".
								"<img src='$image_src' class='exp-col-button' />".
								$qty_textbox .
								"<span class='first-col-hover-wrapper'>".
									"<span class='itemName first-col-text'>{$generic_info[short_name]}</span>".
									"<span class='comment-wrapper' style='display:$comm_wrapper_display'>".
										"&nbsp;(<span class='comment-display first-col-text'>{$item[comments]}</span>)".
									"</span>".
								"</span>" .
								/*"<span class='type-qtys'>".
									"<input type='checkbox' /> <span class='first-col-text'>Hi-Fi</span> ".
									"<input type='checkbox' /> <span class='first-col-text'>Light-weight</span> ".
									"<input type='checkbox' /> <span class='first-col-text'>NBL</span> ".
								"</span>".*/
							"</div>".
                            //"<div class='itemUnderline' style='left:-{$offset}px'></div>".
                        "</td>".

                    //    "<td class='qty-column'>".
                    //        "<input type='text' class='qty-input' value='{$item[qty]}' maxlength='2' />" .
                    //    "</td>".

						"<td class='types-column'>".
							//"<input type='text' class='qty-input' value='1' /> <span class='first-col-text'>Hi-Fi</span> ".
							//"<input type='text' class='qty-input' value='1' /> <span class='first-col-text'>Light-weight</span> ".
							//"<input type='text' class='qty-input' value='1' /> <span class='first-col-text'>NBL</span> ".
							$hw_type_html .
						"</td>".

                    //    "<td class='comment-column'>".
                    //        "<input type='text' class='comment-input' value='{$item[comments]}' />".
                    //    "</td>".
                        /*"<td style='width:90px;'>".



                            "<img src='images/up.png' class='upButton' />".
                            "<img src='images/down.png' class='downButton' />".
                            "<img src='images/plus.png' class='itemMenuButton' 
                                onclick='eos.getNewJSONTreeItem(\"{$this->htmlId}\", this, \"$child_offset\", \"{$this->lookupURL}\")' />".
                            "<img src='images/close.png' class='closeButton itemMenuButton' />".

                        "</td>".*/
                    "</tr></table>";
        }
        else {

            if ($item['qty'] != 1 && $item['qty'] !== "")
                $qty_text = '(' . $item['qty'] . ') ';
            else
                $qty_text = '';

            if ($item['comments'] !== "")
                $comment_text = ' (' . $item['comments'] . ')';
            else
                $comment_text = '';

        
            $output .= "</span><span style='padding-left:{$offset}px;'>"; //<span style='width;$offset;'>
                    //"<img src='images/collapse.png' class='exp-col-button' />".

            $checkboxes = "";
            for ($i=0; $i < intval($item['qty']); $i++)
                $checkboxes .= "<img src='images/checkbox.png'  class='exp-col-button' />";


            $output .=
                    $checkboxes . 
                    "<span class='item-qty-display'>$qty_text</span>". 
                    "<span class='itemName'>{$generic_info[short_name]}</span>".
                    "<span class='item-qty-display'><i>$comment_text</i></span>". 
                "</span>";

        }



        $output .=

                    "<div class='itemUnderline'></div>". //style='left:-{$offset}px'
                    
                "</div>".
                "<ul class='itemUL'>";



        foreach ($item[children] as $child)
            $output .= $this->tree_item($depth+1, $child, $edit);


        $output .= "</ul>";

            //script removed, put into get-list

        $output .=
            "</li>"; //don't closeout LI until after script...otherwise jacks with move up/down of LI.

        return $output;

    }

	    
    public function tree_item_viewer ($item=false) {//$id=-1, $qty=1, $comment='') {

        // specific info about the item: id (ie what item it is), qty, specific comments to this item usage, children
        if ( ! $item )
            $item = array(
                "id"        => -1,
                "qty"       => 1,
                "comment"   => '',
                "children"  => array()
            );

        // generic info about the item (name, P/N, generic comments, etc)
        $generic_info = $this->all_item_info[$item[id]];

        $output .=
            "<li class='itemLI' itemId='{$item[id]}' itemQty='{$item[qty]}' itemComment='{$item[comment]}' genericComment='{$generic_info[comment]}'>";


		if ($item['qty'] != 1 && $item['qty'] !== "")
			$qty_text = '(' . $item['qty'] . ') ';
		else
			$qty_text = '';

		if ($item['comments'] !== "")
			$comment_text = ' (' . $item['comments'] . ')';
		else
			$comment_text = '';

	
		$checkboxes = "";
		for ($i=0; $i < intval($item['qty']); $i++)
			$checkboxes .= "<img src='images/checkbox.png'  class='exp-col-button' />";


		$output .=
				$checkboxes . 
				//"<span class='item-qty-display'>$qty_text</span>". 
				//"<span class='itemName'>{$generic_info[name]}</span>".
				//"<span class='item-qty-display'><i>$comment_text</i></span>";
				$qty_text . $generic_info[name] . "<i>$comment_text</i>";

				
		$output .= "<ul class='itemUL'>";

        foreach ($item[children] as $child)
            $output .= $this->tree_item_viewer($child);

        $output .= "</ul></li>";

        return $output;

    }
	
	public function tree_item_viewer_no_LI ($item=false, $depth=0) {//$id=-1, $qty=1, $comment='') {

        // specific info about the item: id (ie what item it is), qty, specific comments to this item usage, children
        if ( ! $item )
            $item = array(
                "id"        => -1,
                "qty"       => 1,
                "comment"   => '',
                "children"  => array()
            );

        // generic info about the item (name, P/N, generic comments, etc)
        $generic_info = $this->all_item_info[$item[id]];

		$offset = $this->initial_offset + $depth * $this->depth_offset;
		
        $output .=
            "<p style='margin-left:{$offset}px;' class='itemP' itemId='{$item[id]}' itemQty='{$item[qty]}'>";  // itemComment='{$item[comment]}' genericComment='{$generic_info[comment]}'


		if ($item['qty'] != 1 && $item['qty'] !== "" && $item['qty'] != 0)
			$qty_text = '(' . $item['qty'] . ') ';
		else
			$qty_text = '';
	
		$checkboxes = "";
		for ($i=0; $i < intval($item['qty']); $i++)
			$checkboxes .= "<img src='images/checkbox.png' class='passive-checkbox' />";

		for ($i=0; $i < $this->checkbox_spacing ; $i++) {
			$checkboxes .= "&nbsp;";
		}
		
		if ($generic_info[short_name])
			$item_name = $generic_info['short_name'];
		else
			$item_name = $generic_info['full_name'];
		
		
		
		if ($generic_info['full_name'] == 'COMMENT_NAME') {
			$item_name = stripslashes($item['comments']);
			$comment_text = '';
		}
		else if ($item['comments'] !== "")
			$comment_text = ' (' . stripslashes($item['comments']) . ')';
		else
			$comment_text = '';
			
		if ($this->show_items_as_links)
			$item_name = "<a href='{$this->item_edit_URL}?v=row&id={$item['id']}'>$item_name</a>";
		
		$output .=
				$checkboxes . 
				//"<span class='item-qty-display'>$qty_text</span>". 
				//"<span class='itemName'>{$generic_info[name]}</span>".
				//"<span class='item-qty-display'><i>$comment_text</i></span>";
				$qty_text . $item_name . "<i>$comment_text</i></p>";

				
		//$output .= "<ul class='itemUL'>";

        foreach ($item[children] as $child)
            $output .= $this->tree_item_viewer_no_LI($child, $depth+1);

        //$output .= "</ul></li>";

        return $output;

    }
	
    public function itemized_list () {

        // creates list on $this->itemized_list
        $this->create_itemized_list_array();
        
        foreach ($this->itemized_list as $id => $item_info)
            $ids[] = $id;

		if(count($ids) > 0)
			$id_string = implode("' OR id='", $ids);
		else
			$id_string = '';
			
        $query = "SELECT * FROM {$this->table_name} WHERE id='$id_string' ORDER BY short_name";

        $items_info = $this->sql->exe($query);

        $output .= 
			"<table class='itemized-list form-entity-standard-json-table eos-table-basic armory-table-centered-cells'>".
				"<tr>".
					"<th class='form-entity-standard-json-table-headerRow'>Item</th>".
					//"<th>Part Num</th>".
					// BROKEN ANYWAY "<th>Base Qty</th>".
					"<th class='form-entity-standard-json-table-headerRow'>Quantities</th>".
				"</tr>";
	
		$odd = true;
        foreach ($items_info as $item) {

            //var_dump($item);
            //echo "<br /><br />"

			
            if ($item['training_hw'] != "[]" && $item['training_hw'] != "") {
				
				if ($odd)
					$row_class = "eos-table-oddrow";
				else
					$row_class = "eos-table-evenrow";
				$odd = !$odd;
			

                $id = $item['id'];

				$types = array();

				foreach ($this->itemized_list[$id]['hardware_type_qtys'] as $type => $qty)
					if ($qty > 0)
						$types[] = "<td class='itemized-list-type-name'>$type:</td><td class='itemized-list-type-qty'>$qty</td>";

				
                $output .= 
					"<tr class='$row_class'>".
						"<td>{$item['short_name']}</td>".
						//"<td>{$item['partnumber']}</td>".
						// BROKEN...doesn't give good indication of qty anyway, though: "<td style='text-align:center'>" . $this->itemized_list[$id]['base_qty'] . "</td>" . 
						"<td><table class='itemized-list-type-qtys-table'><tr>" . implode("", $types) . "</tr></table></td>".
					"</tr>";
            }
            
        }

        $output .= "</table>";

        return $output;
        
    }

	
	/**
	 *	creates and returns $this->itemized_list in form:
	 *		$this->itemized_list = array(
	 *			id0		=>	array( 
	 *				base_qty => ###,
	 *				hardware_type_qtys => array( type0 => ###, type1 => ### )
	 *			),
	 *			id1		=>	array( ... ),
	 *			id2		=>  array( ... ), ...
	 */
    public function create_itemized_list_array () {

        $tree_data = $this->value;

        $this->itemized_list = array();

        foreach ($tree_data as $item) {

            $this->push_data_to_itemized_list($item);

        }

        return $this->itemized_list;

    }

    public function push_data_to_itemized_list ($item) {

		$this->itemized_list[ $item['id'] ]['base_qty'] = intval( $this->itemized_list[ $item['id'] ]['base_qty'] ) + intval($item['qty']);

		if ( ! $this->itemized_list[ $item['id'] ]['hardware_type_qtys'] )
			$this->itemized_list[ $item['id'] ]['hardware_type_qtys'] = array();
		
		foreach($item['checkedBoxes'] as $type) {
			
			$this->itemized_list[ $item['id'] ]['hardware_type_qtys'][$type] = 
				intval($this->itemized_list[ $item['id'] ]['hardware_type_qtys'][$type]) + intval($item['qty']); // was something * intval($item['qty'])
			
		}
		
        foreach ($item['children'] as $child_item)
            $this->push_data_to_itemized_list($child_item);

    }

	public function get_item_browser_dialog () {
			
		require_once "model/ToolClassificationModel.php";
		require_once "view/ToolCatalogView.php";

		$tool_catalog_controller = new EosController(
			new ToolClassificationModel(),
			new ToolCatalogView("Tool Catalog")
		);

		$tool_table = $tool_catalog_controller->view->get_tool_catalog_table(true); 

		
		$output = 
			"<div id='browse-tree-items' title='Browse'>
				<div style='margin: 5px 0;'><strong>Known Bug:</strong> Hold shift when sorting table. Otherwise will only sort in reverse alphabetical order.</div>
				$tool_table
			</div>";
		
		return $output;
	
	}
	   
}