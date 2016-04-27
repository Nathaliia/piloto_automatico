<?php
/**
 * @package Profiler for Joomla! 2.5
 * @version $Id: profile.php 31 2013-01-09 22:33:43Z harold $
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
JFormHelper::loadFieldClass('list');

class JFormFieldProfile extends JFormFieldList
{

	protected $type = 'Profile';

	protected function getInput()
	{
		// Initialise variables.
		$html = array();
		$readonly = ((string) $this->element['readonly'] == 'true') ? ' readonly="readonly"' : '';
		$disabled	= ((string) $this->element['disabled'] == 'true') ? ' disabled="disabled"' : '';
		
		// Get the field value
		$values = array();
		if(is_array($this->value) || is_object($this->value)) {
			foreach ($this->value as $id => $value) {
				if(!is_array($value)) {
					$values[$id] = $value;
				}
			}
		} else {
			$values[] = $this->value;
		}

		$access = ProfilerHelperAccess::getInstance();
		$value = implode(", ", $access->getUserGroupTitles($values));		
		
		$types = $this->_getTypeList($values);

		$size	= ($v = $this->element['size']) ? ' size="'.$v.'"' : '';
		$class	= ($v = $this->element['class']) ? ' class="'.$v.'"' : 'class="text_area"';

		

		// Load the javascript
		//JHtml::_('behavior.framework');
		//JHtml::_('behavior.modal', 'a.modal');

		$document = JFactory::getDocument();
//		$document->addScriptDeclaration("
//			    $('#profilerModal').on('hidden', function () {
//				    submitform('setProfile')
 //   			})
		
		
//		");
/*		$document->addScriptDeclaration("
		window.addEvent('domready', function() {
			var div = new Element('div').setStyle('display', 'none').injectBefore(document.id('menu-types'));
			document.id('menu-types').injectInside(div);
		});
		
		function profileSelect(Id) {
			if(document.id('group_' + Id).checked) {
				document.id('group_' + Id).checked = false;
			} else {
				document.id('group_' + Id).checked = true;
			}
		}
		
		");*/

		$html[] = '<input type="text" readonly="readonly" disabled="disabled" value="'.$value.'"'.$size.$class.' />';
		if(!$readonly && !$disabled) {
			//$html[] = '<a class="modal" href="#" rel="{handler:\'clone\', target:\'menu-types\', size: {x: 400, y: 600}}">'.JText::_('COM_PROFILER_SELECT').'</a>';
			//$html[] = '<div id="menu-types">';
			//$html[] = $types;
			//$html[] = '<a class="choose_type" href="javascript: submitform()">'.JText::_("JSAVE").'</a>';
			//$html[] = '</div>';
			
			
			$html[] = '<div class="modal hide fade" id="profilerModal">';
				$html[] = '<div class="modal-header">';
					$html[] = '<button class="close" data-dismiss="modal">&times;</button>';
					$html[] = '<h3>'.JText::_('COM_PROFILER_PROFILE_CHOOSE').'</h3>';
				$html[] = '</div>';
				$html[] = '<div class="modal-body">';
					$html[] = $types;
				$html[] = '</div>';
				$html[] = '<div class="modal-footer">';
					$html[] = '<a class="btn btn-primary" href="javascript:submitform(\'user.setProfile\')"/>Close</a>';
				$html[] = '</div>';
				
			$html[] = '</div>';
			$html[] = '<a class="btn btn-primary" data-toggle="modal" href="#profilerModal">'.JText::_('COM_PROFILER_SELECT').'</a>';
		}

		return implode("\n", $html);
	}

	protected function _getTypeList($values)
	{
		
		static $count;

		$count++;
		
		// Initialise variables.
		$html		= array();
		
		// Get the field options.
		$access = ProfilerHelperAccess::getInstance();
		$groups = $access->getRegisterUserGroups();
		
		$checkSuperAdmin = false;
		
		$recordId	= (int) $this->form->getValue('id');

		$html[] = '<ul class="checklist usergroups">';

		for ($i=0, $n=count($groups); $i < $n; $i++) {
			$item = &$groups[$i];

			// If checkSuperAdmin is true, only add item if the user is superadmin or the group is not super admin
			if ((!$checkSuperAdmin) || $isSuperAdmin || (!JAccess::checkGroup($item['id'], 'core.admin'))) {
				// Setup  the variable attributes.
				$eid = 'group_' . $item['id'];
				// Don't call in_array unless something is selected
				$checked = '';
				if ($values) {
					if(is_array($values)) {
						$checked = in_array($item['id'], $values) ? ' checked="checked"' : '';
					} else {
						$checked = $item['id'] === $values ? ' checked="checked"' : '';
					}
				}

				// Build the HTML for the item.
				$html[] = '	<li>';
				$html[] = '		<input type="checkbox" name="' . $this->name . '" value="' . $item['id'] . '" id="' . $eid . '"';
				$html[] = '				' . $checked . ' onclick="profileSelect('.$item['id'].')" />';
				$html[] = '		<label for="' . $eid . '">';
				$html[] = '		' . str_repeat('<span class="gi">|&mdash;</span>', $item['level']) . $item['title'];
				$html[] = '		</label>';
				$html[] = '	</li>';
			}
		}
		$html[] = '</ul>';

		return implode("\n", $html);
	}


}
