<?php
/**
 * @package Profiler for Joomla! 2.5
 * @version $Id$
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
defined('_JEXEC') or die;

class JFormFieldJoomlauser extends JFormField
{
	protected $type = 'Joomlauser';

	protected function getInput()
	{
		// Initialize some field attributes.
		$size = $this->element['size'] ? ' size="' . (int) $this->element['size'] . '"' : '';
		$maxLength = $this->element['maxlength'] ? ' maxlength="' . (int) $this->element['maxlength'] . '"' : '';
		$class = $this->element['class'] ? ' class="' . (string) $this->element['class'] . '"' : '';
		$readonly = ((string) $this->element['readonly'] == 'true') ? ' readonly="readonly"' : '';
		$disabled = ((string) $this->element['disabled'] == 'true') ? ' disabled="disabled"' : '';

		// Initialize JavaScript field attributes.
		$onchange = $this->element['onchange'] ? ' onchange="' . (string) $this->element['onchange'] . '"' : '';

		
		if($this->value > 0) {
			$html[] = '<input type="text" name="' . $this->name . '" id="' . $this->id . '"' . ' value="'
				. htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8') . '"' . $class . $size . $disabled . $readonly . $onchange . $maxLength . '/>';
		} elseif ($this->value == -1 ) {
			$value = JText::_("COM_PROFILER_NOUSER");
			$html[] = $value;
			$html[] = '<input type="hidden" name="' . $this->name . '"  id="' . $this->id . '" value="'	. htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8') . '" />';
				
		} else {
			$class = $this->element['class'] ? ' class="radio btn-group ' . (string) $this->element['class'] . '"' : ' class="radio btn-group"';
			$html[] = '<fieldset id="' . $this->id . '"' . $class . '>';
			$options = $this->getOptions();
			foreach ($options as $i => $option)
			{
				// @todo dit is onjuist de value check is overbodig
				if($this->value > 0 && $option->value == 1) {
					$checked = ' checked="checked"';
				} elseif($this->value < 1 && $option->value == 0) {
					$checked = ' checked="checked"';
				} else {
					$checked = '';
				}
			
				$html[] = '<input type="radio" id="' . $this->id . $i . '" name="' . $this->name . '"' . ' value="'
						. htmlspecialchars($option->value, ENT_COMPAT, 'UTF-8') . '"' . $checked . '/>';
		
				$html[] = '<label for="' . $this->id . $i . '">'
						. JText::alt($option->text, preg_replace('/[^a-zA-Z0-9_\-]/', '_', $this->fieldname)) . '</label>';
			}
		
			// End the radio field output.
			$html[] = '</fieldset>';
		}
		
		return implode($html);
		
		
	}
	
	protected function getOptions()
	{

		$options[] = JHtml::_('select.option', "-1", JTEXT::_('JNO'), 'value', 'text');
		$options[] = JHtml::_('select.option', "0", JTEXT::_('JYES'), 'value', 'text');
		return $options;
	}
}
