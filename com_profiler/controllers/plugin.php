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

jimport('joomla.application.component.controllerform');


class ProfilerControllerPlugin extends JControllerForm {
	
	public function execute() {
		JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		//$app		= JFactory::getApplication();
		
		//$data		= JRequest::getVar('jform', array(), 'post', 'array');
		$recordId	= JRequest::getInt('id');
		$readonly	= JRequest::getInt('pluginro');
		
		JPluginHelper::importPlugin('profiler');
		$dispatcher =& JDispatcher::getInstance();
		$plugin = JRequest::getCmd('plugin');
		$return =  $dispatcher->trigger('action'.$plugin, array(&$this));

		if(is_array($return[0])) {
			if($return[0]['redirect'] == true) {
				$this->setRedirect(JRoute::_(ProfilerHelperRoute::getUserRoute($recordId, $readonly), false), $return[0]['message'], $return[0]['type']);
			} else {
				echo $return[0]['message'];
			}
		} else {
			$this->setRedirect(JRoute::_(ProfilerHelperRoute::getUserRoute($recordId, $readonly), false));
		}
		
	}
	
}
