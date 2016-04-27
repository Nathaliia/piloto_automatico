<?php
/**
 * Fabrik List Template: Default Custom CSS
 *
 * @package     Joomla
 * @subpackage  Fabrik
 * @copyright   Copyright (C) 2005 Fabrik. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 */

/**
* If you need to make small adjustments or additions to the CSS for a Fabrik
* list template, you can create a custom_css.php file, which will be loaded after
* the main template_css.php for the template.
*
* This file will be invoked as a PHP file, so the list ID
* can be used in order to narrow the scope of any style changes.  You do
* this by prepending #listform_$c to any selectors you use.  This will become
* (say) #listform_12, owhich will be the HTML ID of your list on the page.
*
* See examples below, which you should remove if you copy this file.
*
* Don't edit anything outside of the BEGIN and END comments.
*
* For more on custom CSS, see the Wiki at:
*
* http://fabrikar.com/wiki/index.php/3.x_Form_Templates#Custom_CSS
*
* NOTE - for backward compatibility with Fabrik 2.1, and in case you
* just prefer a simpler CSS file, without the added PHP parsing that
* allows you to be be more specific in your selectors, we will also include
* a custom.css we find in the same location as this file.
*
*/

header('Content-type: text/css');
$c = $_REQUEST['c'];
$buttonCount = (int) $_REQUEST['buttoncount'];
$buttonTotal = $buttonCount === 0 ? '100%' : 30 * $buttonCount ."px";
echo "






/********************************************/
/ ****** start: action buttons **************/
/********************************************/

#listform_$c .fabrik_buttons {
	height:25px;
}

#listform_$c .fabrik_buttons{
	/* remove this if you want the top menu bar to be on the right hand side*/
	float:left !important;
}

#listform_$c ul.fabrik_action {
	list-style:none;
	background:none;
	list-style:none;
	min-height:25px;
	border-radius: 6px;
	float:right;
	margin:0;
	padding:0;
	border:1px solid #999;
	filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#eeeeee', endColorstr='#cccccc'); /* for IE */

	background: -webkit-gradient(linear, left top, left bottom, from(#eee),
		to(#ccc) ); /* for webkit browsers */
	background: -moz-linear-gradient(top, #eee, #ccc);
	background: -o-linear-gradient(top, #eeeeee 0%, #cccccc 100%);
  background: -ms-linear-gradient(top, #eeeeee 0%, #cccccc 100%);

}

#listform_$c .fabrik_action .fabrik_filter{
	margin-top:2px;
	padding:2px;
}

#listform_$c ul.fabrik_action li button{
	background-image:none;
	border:0;
	background:transparent;
}

#listform_$c .fabrikFilterContainer .fabrik_action{
	margin:0;
}

#listform_$c .fabrik_row ul.fabrik_action{
	width:$buttonTotal
}

/* $$$ hugh - separated pagination from fabrik_action, 'cos float right makes pagination disappear in Chrome! */
#listform_$c ul.pagination {
	list-style:none;
	background:none;
	list-style:none;
	min-height:25px;
	border-radius: 6px;
	/* float:right; */
	margin:5px;
	padding:0;
	border:1px solid #999;
	filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#eeeeee', endColorstr='#cccccc'); /* for IE */

	background: -webkit-gradient(linear, left top, left bottom, from(#eee),
		to(#ccc) ); /* for webkit browsers */
	background: -moz-linear-gradient(top, #eee, #ccc);
	background: -o-linear-gradient(top, #eeeeee 0%, #cccccc 100%);
  background: -ms-linear-gradient(top, #eeeeee 0%, #cccccc 100%);

}

#listform_$c ul.fabrik_action span{
	display:none;
}

#listform_$c .fabrik_action li,
.advancedSeach_$c .fabrik_action li{
	float:left;
	border-left:1px solid #999;
	min-height:17px;
	min-width:25px;
	text-align:center;
	margin:0;
	padding:0;
}


#listform_$c .fabrik_action li:first-child,
.advancedSeach_$c .fabrik_action li:first-child{
	-moz-border-radius: 6px 0 0 6px;
	-webkit-border-radius: 6px 0 0 6px;
	border-radius: 6px 0 0 6px;
	border:0;
}

#listform_$c .fabrik_action li a{
	display:block;
	padding:4px 6px 2px 6px;
}


/********************************************/
/ ****** end: action buttons ****************/
/********************************************/




";?>