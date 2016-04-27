<?php
/**
 * @package Profiler for Joomla! 2.5
 * @version $Id: rotext.php 42 2013-04-26 16:03:35Z harold $
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

jimport('joomla.form.formfield');


class JFormFieldRotext extends JFormField
{
	protected $type = 'Rotext';

	protected function getInput()
	{
		
		$dispatcher	= JDispatcher::getInstance();
		
		
		$type = $this->element['typero'];
		$value_noempty = "";
		if(is_string($this->value))
			$value_noempty = $this->value ? htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8') : "&nbsp;";  
		switch ($type) {
			case "calendar":
				$format = $this->element['showformat'] ? (string) $this->element['showformat'] : 'Y-m-d';
				if($this->value && $this->value!="0000-00-00 00:00:00" && $this->value!="0000-00-00"){
					jimport('joomla.utilities.date');
    				$date = new JDate($this->value);
					$return = htmlspecialchars($date->format($format), ENT_COMPAT, 'UTF-8');
				}else{
					$return = "";
				}
				break;
			
			case "avatar":
				$config = JComponentHelper::getParams('com_profiler');
				$folder = $config->get('hpdestfolder');
				if($this->value) {
					$value		= $this->value ? JURI::root().'media/com_profiler/'.$folder.'/'.$this->fieldname.'/'.$this->value : '';
				} else {
					$value		= JURI::root().'media/com_profiler/img/user.png';
				}
				
				$imageinfo = getimagesize($value);
				if($imageinfo[0] > $imageinfo[1]) {
					$width		= $this->element['cols'] > 0 ? ' width:'.(int) $this->element['cols'].'px;' : '';
					$height		= ($this->element['rows'] > 0 && !$width) ? ' height:'.(int) $this->element['rows'].'px;' : '';
				} else {
					$height		= $this->element['rows'] > 0 ? ' height:'.(int) $this->element['rows'].'px;' : '';
					$width		= ($this->element['cols'] > 0 && !$height) ? ' width:'.(int) $this->element['cols'].'px;' : '';
				}
				
				$return =  	'<span class="avatar"><img src="'.$value.'" style="'.$width.$height.'" /></span>';
				break;

			case "file":
				$config = JComponentHelper::getParams('com_profiler');
				$folder = $config->get('hpdestfolder');
				if($this->value) {
					$value		= $this->value ? JURI::root().'media/com_profiler/'.$folder.'/'.$this->fieldname.'/'.$this->value : '';
					$return =  	'<a href="'.$value.'">'.JText::_("COM_PROFILER_DOWNLOAD").'</a>';
				} 				
				break;
				
			case "profile":
				$access = ProfilerHelperAccess::getInstance();
				
				$values = $access->getUserGroupIDs($this->form->getValue("id"));
				//JUserHelper::getUserGroups($this->form->getValue("userid"));
								
				$value = implode(", ", $access->getUserGroupTitles($values));
				
				$return =  $value;
				break;
				
			case "joomlauser":
				$value = "";
				if($this->value > 0) {
					return $this->value;
				} elseif ($this->value == -1 ) {
					$value = JText::_("COM_PROFILER_NOUSER");
				}
				$return =  $value;
				break;
				
			case "email":
				//$id = $this->form->getValue("id");
				//$link = JRoute::_("index.php?option=com_profiler&view=message&tmpl=component&toid=".$id);
				//return "<a class=\"modal\" href=\"$link\" rel=\"{handler: 'iframe', size: {x: 600, y: 500}}\" >".
				
				$return =  $value_noempty;
				break;
				
				
			case "editor":
				$return =  $this->value;
				break;
				
			case "sql":
				$query	= (string) $this->element['query'];
				$db = JFactory::getDBO();
				$db->setQuery($query);
				$options = $db->loadAssocList('value', (string) $this->element['name']);
				//$options = array_merge_recursive($options, $this->getOptions());
				$options = $options + $this->getOptions();

				if(is_array($this->value) || is_object($this->value)) {
					foreach($this->value as &$row) {
						$row = $options[$row];
					}
					$value = implode(", ", $this->value);
				} elseif(is_string($this->value) && $this->value) {
					$value = $options[$this->value];
				} else {
					$value = "&nbsp;";
				}
				
				
				$return =  $value;
				break;
				
			case "list":
			case "checkboxes":
			case "radio": 
				$options = (array) $this->getOptions();
				if(is_array($this->value) || is_object($this->value)) {
					foreach($this->value as &$row) {
						$row = $options[$row];
					}
					$value = implode(", ", $this->value);
				} elseif(isset($options[$this->value])) {
					$value = $options[$this->value];
				}
				if(isset($value) && $value) { 
					$return = $value;
				} else {
					$return = "";
				}
						
				break;
				
			case "checkbox":
				if($this->value) {
					$return = '<i style="color: #51A351;" class="icon-ok"></i>';
				} else {
					$return = '<i style="color: #BD362F;" class="icon-remove"></i>';
				}
				break;
				
			case "plugin":
			
				JPluginHelper::importPlugin('pffields', "field" . $this->element['plugin']);
				$result = $dispatcher->trigger('getPffieldsReadonly_' . $this->element['plugin'], array($this));
				$return = implode("", $result);
				break;
							
			default:
				$return =  $value_noempty;	
		}
		if(is_string($return)) {
			return "<span class=\"profiler-readonly\">".$return."</span>";
		}
		
		
	}
	
	protected function getOptions()
	{
		$options = array();
		foreach ($this->element->children() as $option) {
			if ($option->getName() != 'option') {
				continue;
			}
			
			$row = (string) $option['value'];
					
			if(trim((string) $option) == "JNO") {
				//$value = '<i style="color: #BD362F;" class="icon-remove"></i>';
				$value = JText::_(trim((string) $option));
			} elseif(trim((string) $option) == "JYES") {
				//$value = '<i style="color: #51A351;" class="icon-ok"></i>';
				$value = JText::_(trim((string) $option));
			} else {
				$value = JText::_(trim((string) $option));
			}
			$options[$row] = $value;
		}

		reset($options);

		return $options;
	}
	
}
