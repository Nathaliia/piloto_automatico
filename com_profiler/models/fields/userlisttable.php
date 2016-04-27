<?php
/**
 * @package Profiler Fields for Joomla! 2.5
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

class JFormFieldUserlisttable extends JFormField
{
	protected $type = 'Userlisttable';

	protected function getInput()
	{
		// Initialise variables.
		$html = array();
		JHtml::_('script', 'com_profiler/jquery.min.js', true, true);
		JHtml::_('script', 'com_profiler/bootstrap-modal.js', true, true);
		JHtml::_('stylesheet', 'com_profiler/backend.min.css', array(), true);
		
		$where = $this->element['where'];
		

		$query = "SELECT name AS `key`, title AS `value` FROM #__profiler_fields WHERE extension = '" . $where . "';";
		$db = JFactory::getDBO();
		$db->setQuery($query);
		$items = $db->loadObjectlist();
		
		
		//$('#mySelect').change(function(){
		//	var value = $(this).val();
		//});
		$script = array();
		
		$script[] = 'function selectfield'.$this->fieldname.'(col) {';
		//$script[] = '   alert("this is an alert " + jQuery("#mySelect").val());';
		$script[] = '	jQuery("#ProfilerListtable'.$this->fieldname.'Box" + col).val(jQuery("#ProfilerListtable'.$this->fieldname.'Box" + col).val() + "{"+jQuery("#ProfilerListtable'.$this->fieldname.'Select" + col).val()+"}");';
		$script[] = '}';
		JFactory::getDocument()->addScriptDeclaration(implode("\n", $script));
		

		$html[] = '<table style="float: left">';
		$html[] = '<tr><td>';
		$html[] = '  <input type="text" name="' . $this->name . '[hrow][]" value="'. htmlspecialchars((isset($this->value['hrow']) ? $this->value['hrow'][0] : ""), ENT_COMPAT, 'UTF-8') . '"/>';
		$html[] = '</td></tr><tr><td>';
		$html[] = '  <textarea id="ProfilerListtable'.$this->fieldname.'Box0" name="' . $this->name . '[row][]" class="inputbox" rows="3" cols="30"  />'.(isset($this->value['row']) ? $this->value['row'][0] : "").'</textarea>';
		$html[] = '</td></tr><tr><td style="border-bottom: 1px solid #000;">';
		$html[] = '<select id="ProfilerListtable'.$this->fieldname.'Select0" onChange="selectfield'.$this->fieldname.'(\'0\')">';
		$html[] = implode("", $this->getOptions($items));
		$html[] = '</select>';
		$html[] = '</td></tr><tr><td>';
		$html[] = '  <input type="text" name="' . $this->name . '[hrow][]" value="'. htmlspecialchars((isset($this->value['hrow']) ? $this->value['hrow'][1] : ""), ENT_COMPAT, 'UTF-8') . '"/>';		
		$html[] = '</td></tr><tr><td>';
		$html[] = '  <textarea id="ProfilerListtable'.$this->fieldname.'Box1" name="' . $this->name . '[row][]" class="inputbox" rows="3" cols="30"  />'.(isset($this->value['row']) ? $this->value['row'][1] : "").'</textarea>';
		$html[] = '</td></tr><tr><td style="border-bottom: 1px solid #000;">';
		$html[] = '<select id="ProfilerListtable'.$this->fieldname.'Select1" onChange="selectfield'.$this->fieldname.'(\'1\')">';
		$html[] = implode("", $this->getOptions($items));
		$html[] = '</select>';
		$html[] = '</td></tr><tr><td>';
		$html[] = '  <input type="text" name="' . $this->name . '[hrow][]" value="'. htmlspecialchars((isset($this->value['hrow']) ? $this->value['hrow'][2] : ""), ENT_COMPAT, 'UTF-8') . '"/>';		
		$html[] = '</td></tr><tr><td>';
		$html[] = '  <textarea id="ProfilerListtable'.$this->fieldname.'Box2" name="' . $this->name . '[row][]" class="inputbox" rows="3" cols="30"  />'.(isset($this->value['row']) ? $this->value['row'][2] : "").'</textarea>';
		$html[] = '</td></tr><tr><td style="border-bottom: 1px solid #000;">';
		$html[] = '<select id="ProfilerListtable'.$this->fieldname.'Select2" onChange="selectfield'.$this->fieldname.'(\'2\')">';
		$html[] = implode("", $this->getOptions($items));
		$html[] = '</select>';
		$html[] = '</td></tr><tr><td>';
		$html[] = '  <input type="text" name="' . $this->name . '[hrow][]" value="'. htmlspecialchars((isset($this->value['hrow']) ? $this->value['hrow'][3] : ""), ENT_COMPAT, 'UTF-8') . '"/>';		
		$html[] = '</td></tr><tr><td>';
		$html[] = '  <textarea id="ProfilerListtable'.$this->fieldname.'Box3" name="' . $this->name . '[row][]" class="inputbox" rows="3" cols="30"  />'.(isset($this->value['row']) ? $this->value['row'][3] : "").'</textarea>';
		$html[] = '</td></tr><tr><td style="border-bottom: 1px solid #000;">';
		$html[] = '<select id="ProfilerListtable'.$this->fieldname.'Select3" onChange="selectfield'.$this->fieldname.'(\'3\')">';
		$html[] = implode("", $this->getOptions($items));
		$html[] = '</select>';
		$html[] = '</td></tr><tr><td>';
		$html[] = '  <input type="text" name="' . $this->name . '[hrow][]" value="'. htmlspecialchars((isset($this->value['hrow']) ? $this->value['hrow'][4] : ""), ENT_COMPAT, 'UTF-8') . '"/>';
		$html[] = '</td></tr><tr><td>';
		$html[] = '  <textarea id="ProfilerListtable'.$this->fieldname.'Box4" name="' . $this->name . '[row][]" class="inputbox" rows="3" cols="30"  />'.(isset($this->value['row']) ? $this->value['row'][4] : "").'</textarea>';
		$html[] = '</td></tr><tr><td>';
		$html[] = '<select id="ProfilerListtable'.$this->fieldname.'Select4" onChange="selectfield'.$this->fieldname.'(\'4\')">';
		$html[] = implode("", $this->getOptions($items));
		$html[] = '</select>';
		$html[] = '</td></tr>';
		$html[] = '</table>';
		
		
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