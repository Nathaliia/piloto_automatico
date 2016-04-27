<?php
/**
 * @package Profiler for Joomla! 2.5
 * @version $Id: user.php 41 2013-03-28 18:06:15Z harold $
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

require_once JPATH_COMPONENT.'/controller.php';

class ProfilerControllerUser extends ProfilerController
{


	public function register()
	{
		// Check for request forgeries.
		JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		
		$app		= JFactory::getApplication();
		$recordId	= JRequest::getInt('id');
		
		
		
		// Get the user data.
		$requestData = JRequest::getVar('jform', array(), 'post', 'array');
		
		if(!isset($requestData['id']))
			$requestData['id'] = $recordId;
		
		if(!isset($requestData['userid']) && $requestData['id'] > 0) {
			$access = ProfilerHelperAccess::getInstance();
			$requestData['userid'] = $access->getJoomlaUserId($requestData['id']);
		}
			

		// If registration is disabled - Redirect to login page.
		if(JComponentHelper::getParams('com_profiler')->get('allowUserRegistration') == 0 && (isset($requestData['id']) && $requestData['id'] == 0)) {
			$this->setRedirect(JRoute::_('index.php?option=com_users&view=login', false));
			return false;
		}

		// Initialise variables.
		//$app	= JFactory::getApplication();
		$model	= $this->getModel('user', 'ProfilerModel');

		
		// Validate the posted data.
		$form	= $model->getForm();
		if (!$form) {
			JError::raiseError(500, $model->getError());
			return false;
		}
		$data	= $model->validate($form, $requestData);

		// Check for validation errors.
		if ($data === false) {
			// Get the validation messages.
			$errors	= $model->getErrors();

			// Push up to three validation messages out to the user.
			for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++) {
				if (JError::isError($errors[$i])) {
					$app->enqueueMessage($errors[$i]->getMessage(), 'warning');
				} else {
					$app->enqueueMessage($errors[$i], 'warning');
				}
			}

			// Save the data in the session.
			$app->setUserState('com_profiler.edit.user.data', $requestData);

			// Redirect back to the registration screen.
			
			//$this->setRedirect(JRoute::_('index.php?option=com_profiler&view=user&layout=edit', false));
			$this->setRedirect(JRoute::_(ProfilerHelperRoute::getUserRoute($model->getState('user.id'), false), false));
			return false;
		}

		// Attempt to save the data.
		$return	= $model->save($data);

		// Check for errors.
		if ($return === false) {
			// Save the data in the session.
			$app->setUserState('com_profiler.edit.user.data', $data);

			// Redirect back to the edit screen.
			$this->setMessage(JText::sprintf('COM_PROFILER_REGISTRATION_SAVE_FAILED', $model->getError()), 'warning');
			$this->setRedirect(JRoute::_(ProfilerHelperRoute::getUserRoute($model->getState('user.id'), false), false));
			return false;
		}

		// Flush the data from the session.
		$app->setUserState('com_profiler.edit.user.data', null);

		// Redirect to the profile screen.
		if ($return === 'adminactivate'){
			$this->setMessage(JText::_('COM_PROFILER_REGISTRATION_COMPLETE_VERIFY'));
			$this->setRedirect(JRoute::_("index.php?Itemid=".JComponentHelper::getParams('com_profiler')->get('redirectregister')), false);
		} else if ($return === 'useractivate') {
			$this->setMessage(JText::_('COM_PROFILER_REGISTRATION_COMPLETE_ACTIVATE'));
			$this->setRedirect(JRoute::_("index.php?Itemid=".JComponentHelper::getParams('com_profiler')->get('redirectregister')), false);
		} else if ($return === 'usernew') {
			$this->setMessage(JText::_('COM_PROFILER_REGISTRATION_SAVE_SUCCESS'));
			$this->setRedirect(JRoute::_("index.php?Itemid=".JComponentHelper::getParams('com_profiler')->get('redirectregister')), false);
		} else {
			$this->setMessage(JText::_('COM_PROFILER_SAVE_SUCCESS'));
			$this->setRedirect(JRoute::_(ProfilerHelperRoute::getUserRoute($model->getState('user.id'), true), false));
		}


		return true;
	}

	public function setProfile() {
		// Initialise variables.
		$app		= JFactory::getApplication();
		
		// Get the posted values from the request.
		$data		= JRequest::getVar('jform', array(), 'post', 'array');
		$recordId	= JRequest::getInt('id');
		
		//Save the data in the session.
		$app->setUserState('com_profiler.edit.user.data', $data);
		$this->setRedirect(JRoute::_(ProfilerHelperRoute::getUserRoute($recordId, false), false));
		
	}
	
	public function activate()
	{
		$user		= JFactory::getUser();
		$params		= JComponentHelper::getParams('com_profiler');

		// If the user is logged in, return them back to the homepage.
		if ($user->get('id')) {
			$this->setRedirect('index.php');
			return true;
		}

		// If user registration or account activation is disabled, throw a 403.
		if ($params->get('useractivation') == 0 || $params->get('allowUserRegistration') == 0) {
			JError::raiseError(403, JText::_('JLIB_APPLICATION_ERROR_ACCESS_FORBIDDEN'));
			return false;
		}

		$model = $this->getModel('user', 'ProfilerModel');
		$token = JRequest::getVar('token', null, 'request', 'alnum');

		// Check that the token is in a valid format.
		if ($token === null || strlen($token) !== 32) {
			JError::raiseError(403, JText::_('JINVALID_TOKEN'));
			return false;
		}

		// Attempt to activate the user.
		$return = $model->siteactivate($token);

		// Check for errors.
		if ($return === false) {
			// Redirect back to the homepage.
			$this->setMessage(JText::sprintf('COM_PROFILER_REGISTRATION_SAVE_FAILED', $model->getError()), 'warning');
			$this->setRedirect('index.php');
			return false;
		}

		$useractivation = $params->get('useractivation');

		// Redirect to the login screen.
		if ($useractivation == 0)
		{
			$this->setMessage(JText::_('COM_PROFILER_REGISTRATION_SAVE_SUCCESS'));
			$this->setRedirect(JRoute::_('index.php?option=com_users&view=login', false));
		}
		elseif ($useractivation == 1)
		{
			$this->setMessage(JText::_('COM_PROFILER_REGISTRATION_ACTIVATE_SUCCESS'));
			$this->setRedirect(JRoute::_('index.php?option=com_users&view=login', false));
		}
		elseif ($return->getParam('activate'))
		{
			$this->setMessage(JText::_('COM_PROFILER_REGISTRATION_VERIFY_SUCCESS'));
			$this->setRedirect(JRoute::_('index.php?option=com_users&view=registration&layout=complete', false));
		}
		else
		{
			$this->setMessage(JText::_('COM_PROFILER_REGISTRATION_ADMINACTIVATE_SUCCESS'));
			$this->setRedirect(JRoute::_('index.php?option=com_users&view=registration&layout=complete', false));
		}
		return true;
	}
	

}
