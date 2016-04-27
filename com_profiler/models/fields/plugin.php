<?php
/**
 * @package Profiler Fields for Joomla! 3.0
 * @version $Id: avatar.php 3 2013-01-09 22:50:10Z harold $
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

jimport('joomla.form.formfield');

class JFormFieldPlugin extends JFormField
{
	public $type = 'Plugin';

	protected function getInput()
	{
		
		$pluginname = $this->element['plugin'];
		
		$dispatcher	= JDispatcher::getInstance();
		JPluginHelper::importPlugin('pffields', "field" . $pluginname);
		$result = $dispatcher->trigger('getPffieldsInput_' . $pluginname, array($this));
		
		return implode("", $result);
		
	}
}