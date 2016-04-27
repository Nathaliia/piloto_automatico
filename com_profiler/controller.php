<?php
/**
 * @package Profiler for Joomla! 2.5
 * @version $Id: controller.php 48 2013-06-10 21:36:21Z harold $
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

jimport('joomla.application.component.controller');

class ProfilerController extends JControllerLegacy
{

	public function display($cachable = false, $urlparams = false) {
		// Get the document object.
		$document	= JFactory::getDocument();
		$config 	= JComponentHelper::getParams('com_profiler');

		// Set the default view name and format from the Request.
		$vName	 = JRequest::getCmd('view', 'login');
		$vFormat = $document->getType();
		$lName	 = JRequest::getCmd('layout', 'default');
		
		if($lName == "myprofile") {
			$app		= JFactory::getApplication();
			$params		= $app->getParams();
			JRequest::setVar('layout', $params->get('userlist_layout'));
			if(!isset($_GET['ro'])) {
				JRequest::setVar('ro', $params->get('readonly'), 'GET');
			}

			$myprofile = true;
		}
		
		if ($view = $this->getView($vName, $vFormat, '', array('base_path' => $this->basePath, 'layout' => $lName))) {
			switch ($vName) {
				case 'user':
					$this->model_prefix = 'ProfilerModelSite';
					$model = $this->getModel('user', 'ProfilerModelSite');
					$userId = (!empty($userId)) ? $userId : (int)$model->getState('user.id');
					
					if(isset($myprofile)) {
						$meId = JFactory::getUser()->id;
						$userId = ProfilerHelperAccess::getProfilerUserId($meId);
						JRequest::setVar('id', $userId);
					}					
					
					//is nieuw? dan register
					if (!$userId) {
						if($config->get('allowUserRegistration') == 0) {
							$this->setRedirect(JRoute::_('index.php?option=com_users&view=login', false));
							return;
						}
					} else {
					
					}
					
					//direct link?
					if($lName == "default") {
						JRequest::setVar('layout', 'profile');
					}
					break;
				
				case 'users':
					break;
					
				case 'group':
					$this->model_prefix = 'ProfilerModelSite';
					$model = $this->getModel('group', 'ProfilerModelSite');
					$groupId = (!empty($userId)) ? $userId : (int)$model->getState('group.id');
					
					//is nieuw? dan register
					if (!$groupId) {
						//if($config->get('allowUserRegistration') == 0) {
						//	$this->setRedirect(JRoute::_('index.php?option=com_users&view=login', false));
						//	return;
						//}
					} else {
							
					}
					break;
							
				
				case 'groups':
					break;
					
			}
			parent::display();
		}
		
		
	}

}
