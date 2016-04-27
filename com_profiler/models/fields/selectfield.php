<?php
/**
 * @package Profiler Fields for Joomla! 3.0
 * @version $Id: valuelist.php 3 2013-01-09 22:50:10Z harold $
 * @author Harold Prins
 * @copyright (C) 2011-2013 Harold Prins
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html
 *  
 * Profiler is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *  
 * You should have received a copy of the GNU General Public License
 * along with Profiler.  If not, see <http://www.gnu.org/licenses/gpl-2.0.html>.
*/
defined('JPATH_BASE') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');

class JFormFieldSelectfield extends JFormField
{
	protected $type = 'Selectfield';

	protected function getInput()
	{
		// Initialise variables.
		$html = array();
		
		// Get the field value
		$valuetext = $this->value;
		
		$values = explode(",", $valuetext);
		
		$where = $this->element['where'];
		
		//SELECT name AS key, name AS value FROM #__profiler_fields
		$query = "SELECT name AS `key`, title AS `value` FROM #__profiler_fields WHERE extension = '" . $where . "';";
		$db = JFactory::getDBO();
		
		$db->setQuery($query);
		$items = $db->loadObjectlist();
		
		$i = 0;		
		
		if($valuetext) {
			foreach($values as $value) {
				$i++;
				$valuedetail = explode("|", $value);
				$valuelist[] = '<tr class="optionrow"><td>';
				$valuelist[] = '<input type="text" size="20" class="inputbox" value="'.$valuedetail[0].'" name="names'.$this->id.'[]">';
				$valuelist[] = '</td><td>';
				//$valuelist[] = '<input type="text" size="30" class="inputbox" value="'.$valuedetail[1].'" name="values'.$this->id.'[]">';
				$valuelist[] = '<select class="" name="values'.$this->id.'[]">';
				$valuelist[] = implode("", $this->getOptions($items, $valuedetail[1]));
				$valuelist[] = '</select>';
				$valuelist[] = '</td><td>';
				$valuelist[] = "<a href=\"#\" onClick=\"jQuery(this).closest('tr').remove();\"><i class=\"icon-remove\"></i></a>";
				$valuelist[] = '</td></tr>';
			}
		} else {
			$valuelist[] = '<tr class="optionrow"><td>';
			$valuelist[] = '<input type="text" size="30" class="inputbox" value="" name="names'.$this->id.'[]">';
			$valuelist[] = '</td><td>';
			//$valuelist[] = '<input type="text" size="30" class="inputbox" value="" name="values'.$this->id.'[]">';
			$valuelist[] = '<select class="" name="values'.$this->id.'[]">';
			$valuelist[] = implode("", $this->getOptions($items));
			$valuelist[] = '</select>';
			$valuelist[] = '</td><td>';
			$valuelist[] = "<a href=\"#\" onClick=\"jQuery(this).closest('tr').remove();\"><i class=\"icon-remove\"></i></a>";
			$valuelist[] = '</td></tr>';
		}
		
		$emptyvaluelist[] = '<tr class="optionrow"><td>';
		$emptyvaluelist[] = '<input type="text" size="30" class="inputbox" value="" name="names'.$this->id.'[]">';
		$emptyvaluelist[] = '</td><td>';
		//$emptyvaluelist[] = '<input type="text" size="30" class="inputbox" value="" name="values'.$this->id.'[]">';
		$emptyvaluelist[] = '<select class="" name="values'.$this->id.'[]">';
		$emptyvaluelist[] = implode("", $this->getOptions($items));
		$emptyvaluelist[] = '</select>';
		$emptyvaluelist[] = '</td><td>';
		$emptyvaluelist[] = "<a href=\"#\" onClick=\"jQuery(this).closest('tr').remove();\"><i class=\"icon-remove\"></i></a>";
		$emptyvaluelist[] = '</td></tr>';
		

		// Load the javascript
		JHtml::_('behavior.framework');
		JHtml::_('behavior.modal', 'a.modal', array('onClose'=>'\function(){modalclose();}'));
		JHtml::_('script', 'com_profiler/selectfield.js', true, true);
		
		// Build the script.
		$script = array();

		$script[] = '	function PostValueList'.$this->id.'(title) {';
		$script[] = '		var names = $$("input[name=names'.$this->id.'\[\]]").map(function(e) { return e.value; });';
		$script[] = '		var values = $$("select[name=values'.$this->id.'\[\]]").map(function(e) { return e.value; });';
		$script[] = '		var returnvalue = "";';
		$script[] = '		var count = 0;';
		$script[] = '		Array.each(names, function(name, index){';
		$script[] = '			count = count + 1;';
		$script[] = '			if(!!name || !!values[index]) {;';
		$script[] = '				if(count > 1) {';
		$script[] = '					returnvalue = returnvalue + ",";';
		$script[] = '				};';
		$script[] = '				returnvalue = returnvalue + name + "|" + values[index] ;';
		$script[] = '			};';    	
		$script[] = '		});';
		$script[] = '		document.getElementById("' . $this->id . '").value = returnvalue;';
		$script[] = '		jQuery("#ProfilerModal'.$this->id.'").modal("hide");';
		$script[] = '	};';
		
		$script[] = '   function createoption'.$this->id.'() {';
		$script[] = '       jQuery("#ProfilerTable'.$this->id.' tbody:last").append("'. addslashes(implode("", $emptyvaluelist)) . '");';
		$script[] = '       jQuery("#ProfilerTable'.$this->id.' select:last").chosen();';
//		$script[] = '		var modalbox = document.id("ProfilerModal'.$this->id.'");';
//		$script[] = '		var optioncontainer = modalbox.getElement("tbody");'; 
//		$script[] = '		var optionrow = modalbox.getElement(".optionrow");'; 
//		$script[] = '   	var clone = optionrow.clone().inject(optioncontainer);';
		$script[] = '	};';
		
		JFactory::getDocument()->addScriptDeclaration(implode("\n", $script));

		$html[] = '<div class="input-append">';
		$html[] = '  <textarea id="' . $this->id . '" name="' . $this->name . '" class="inputbox" rows="3" cols="30" readonly="readonly" />'.$valuetext.'</textarea>';
		//$html[] = '		<a class="btn modal" title="' . JText::_('COM_PFFIELDS_FIELD_EDITOPTIONS') . '"' . ' href="#" rel="{handler: \'adopt\', target:\'value-list\', size: {x: 600, y: 500}}">';
		//$html[] = '			' . JText::_('COM_PROFILER_FIELD_EDITOPTIONS') . '</a>';
		$html[] = '		<a class="btn fltlft" href="#ProfilerModal'.$this->id.'" role="button" data-toggle="modal">';
		$html[] = '			' . JText::_('COM_PROFILER_FIELD_EDITOPTIONS') . '</a>';
		$html[] = '</div>';
		
		
		//modal
		$html[] = '<div id="ProfilerModal'.$this->id.'" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">';
		$html[] = '<div class="modal-header">';
		$html[] = '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>';
		$html[] = '<h3>'. $this->title .'</h3>';
		$html[] = '</div>';
		$html[] = '<div class="modal-body">';
		
		$html[] = '<table id="ProfilerTable'.$this->id.'">';
		$html[] = '<thead>';
		$html[] = '<tr><th>'.JText::_("COM_PROFILER_SELECTFIELD_FIELDTITLE").'</th><th>'.JText::_("COM_PROFILER_SELECTFIELD_FIELDNAME").'</th><th></th></tr>';
		$html[] = '</thead>';
		$html[] = '<tbody>';
		$html[] = implode("\n", $valuelist);
		$html[] = '</tbody>';
		$html[] = '</table>';
		$html[] = '<div style="height:150px"></div>';
		$html[] = '</div>';
		$html[] = '<div class="modal-footer">';
		$html[] = '<a href="#" onClick="createoption'.$this->id.'()" class="btn btn-primary">'.JText::_("COM_PROFILER_FIELD_ADDOPTION").$this->title.'</a>';
		$html[] = '<a href="#" onClick="PostValueList'.$this->id.'(\'test\')" class="btn btn-primary">'.JText::_("COM_PROFILER_FIELD_SAVE").$this->title.'</a>';
		$html[] = '</div>';
		$html[] = '</div>';
		
		
		//modal
		//$html[] = '<div id="modalhide" style="display: none">';
		//$html[] = '<div id="value-list">';
		//$html[] = '<table>';
		//$html[] = '<thead>';
		//$html[] = '<tr><th>'.JText::_("COM_PROFILER_SELECTFIELD_FIELDTITLE").'</th><th>'.JText::_("COM_PROFILER_SELECTFIELD_FIELDNAME").'</th></tr>';
		//$html[] = '</thead>';
		//$html[] = '<tbody>';
		//$html[] = implode("\n", $valuelist);
		//$html[] = '</tbody>';
		//$html[] = '</table>';
		//$html[] = '<div class="button2-left"><div class="blank"><a href="#" onClick="createoption()" title="'.JText::_("COM_PROFILER_FIELD_ADDOPTION").'">'.JText::_("COM_PROFILER_FIELD_ADDOPTION").'</a></div></div>';
		//$html[] = '<div class="button2-left"><div class="blank"><a href="#" onClick="PostValueList(\'test\')" title="'.JText::_("JSAVE").'">'.JText::_("JSAVE").'</a></div></div>';
		//$html[] = '</div>';
		//$html[] = '</div>';
		
		return implode("\n", $html);
	}
	
	protected function getOptions($items, $value = null)
	{
		if (!empty($items))
		{
			foreach ($items as $item)
			{
				//$options[] = JHtml::_('select.option', $item->key, JText::_($item->value));
				$selected = $value == $item->key ? 'selected="selected"' : "";
				$options[] = '<option value="'.$item->key.'" '.$selected.'>'.JText::_($item->value).'</option>'; 
			}
		}
		return $options;
	}
}