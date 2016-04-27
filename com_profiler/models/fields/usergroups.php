<?php
/**
 * @package Profiler for Joomla! 2.5
 * @version $Id: usergroups.php 31 2013-01-09 22:33:43Z harold $
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

class JFormFieldUsergroups1 extends JFormField
{

	protected $type = 'Usergroups';

	protected function getInput()
	{
		//echo "-" ;print_r($this->value); echo "-" ;die;
		
		$options = array();
		if(is_array($this->value) || is_object($this->value)) {
			foreach ($this->value as $id => $option) {
				if(!is_array($option)) {
					$options[$id] = $option;
				}
			}
		}
		return JHtml::_('access.usergroups', $this->name, $options);
	}
}
