<?php
/**
 * @package Profiler for Joomla! 2.5
 * @version $Id: view.html.php 48 2013-06-10 21:36:21Z harold $
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

jimport('joomla.application.component.view');
require_once JPATH_COMPONENT.'/helpers/view.php';

class ProfilerViewUser extends JViewLegacy
{
	protected $form;
	protected $item;
	protected $grouplist;
	protected $groups;
	protected $state;
	
	protected $pro_groups;
	protected $fieldsets;
	
	protected $view_position;
	protected $view_labels;
	
	public $plugincontent = array();
	
	public function display($tpl = null)
	{
		$app		= JFactory::getApplication();
		$this->params 	= $app->getParams();
				
		$this->form			= $this->get('Form');
		$this->item			= $this->get('Item');
		$this->grouplist	= $this->get('Groups');
		$this->groups		= $this->get('AssignedGroups');
		$this->state		= $this->get('State');
		//$this->params		= $this->state->get('params');
		$this->menu			= $this->get('Menu');
		$this->module		= array();
		
		
		$this->loadPlugin();
				
		//$params->merge($this->params);

		// Check for errors.
		if(!$this->form) {
			return false;
		} elseif (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}
		
		$this->fieldsets	= $this->form->getFieldsets();


		//stylesheet
		if($this->params->get("enable_css")) {
			JHtml::_('stylesheet', 'com_profiler/bootstrap-responsive.min.css', array(), true);
			JHtml::_('stylesheet', 'com_profiler/bootstrap.min.css', array(), true);
			JHtml::_('stylesheet', 'com_profiler/profiler.css', array(), true);
		}
		if($this->params->get("enable_jquery")) {
			JHtml::_('script', 'com_profiler/jquery.min.js', true, true);
		}
		if($this->params->get("enable_bootstrap")) {
			JHtml::_('script', 'com_profiler/bootstrap-tab.js', true, true);
			JHtml::_('script', 'com_profiler/bootstrap-collapse.js', true, true);
			JHtml::_('script', 'com_profiler/bootstrap-modal.js', true, true);
			JHtml::_('script', 'com_profiler/bootstrap-transition.js', true, true);
		}
		
		JHtml::_('behavior.keepalive');
		JHtml::_('behavior.tooltip');
		JHtml::_('behavior.formvalidation');
		
		$this->pageclass_sfx = htmlspecialchars($this->params->get('pageclass_sfx'));

		$this->form->setValue('password',		null);
		$this->form->setValue('password2',	null);
		
		// Override the layout only if this is not the active menu item
		// If it is the active menu item, then the view and item id will match
		$active	= $app->getMenu()->getActive();
		if ((!$active) || ((strpos($active->link, 'view=user') === false) || (strpos($active->link, '&id=' . (string) $this->item->id) === false))) {
			if ($layout = $this->params->get('userlist_layout')) {
				$this->setLayout($layout);
			}
		}
		elseif (isset($active->query['layout'])) {
			// We need to set the layout in case this is an alternative menu item (with an alternative layout)
			$this->setLayout($active->query['layout']);
		}
		

		$this->prepareDocument();
		
		// Flush the data from the session.
		JFactory::getApplication()->setUserState('com_profiler.edit.user.data', null);
		
		parent::display($tpl);
		
	}
	
	protected function prepareDocument()
	{
		$app		= JFactory::getApplication();
		$menus		= $app->getMenu();
		$title 		= null;

		// Because the application sets a default page title,
		// we need to get it from the menu item itself
		$menu = $menus->getActive();
		if ($menu) {
			$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
		} else {
			$this->params->def('page_heading', JText::_('COM_PROFILER_REGISTRATION'));
		}

		$title = $this->params->get('page_title', '');
		if (empty($title)) {
			$title = $app->getCfg('sitename');
		}
		elseif ($app->getCfg('sitename_pagetitles', 0)) {
			$title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
		}
		$this->document->setTitle($title);
	}
	
	public function loadTemplate($tpl = null, $position = null, $labels = true) {
		
		$this->view_position = $position;
		$this->view_labels = $labels;
		
		return parent::loadTemplate($tpl);		
		
	}
	
	public function loadPluginMenu() {
		JPluginHelper::importPlugin('profiler');
		$dispatcher =& JDispatcher::getInstance();
		$results = $dispatcher->trigger('usermenu', array($this->item));
		$readonly	= JRequest::getInt('ro');
		
		if(array($results)) {
			foreach ($results as $result) {
				if($result) {
					echo '<input type="button" class="btn '.$result['class'].'" onclick="Joomla.bevoresubmitbutton(\'plugin.execute\', \''. $result['link'] . '\',\''. $readonly . '\')" value="'.JText::_($result['label']) .'" />';
				}
			}
		}
	}
	
	public function loadPlugin($return = "all") {
		JPluginHelper::importPlugin('profiler');
		$dispatcher =& JDispatcher::getInstance();
		$results = $dispatcher->trigger('display', array($this->item->id));
		$return = array();
		if(is_array($results)) {
			foreach ($results as $result) {
				if(isset($result['label']) && isset($result['content'])) {
					$return[$result['id']]['label']= JText::_($result['label']);
					$return[$result['id']]['content']= $result['content'];
					$return[$result['id']]['id']= $result['id'];
				} elseif (is_array($result)) {
					foreach($result AS $resultsub) {
						if(isset($resultsub['label']) && isset($resultsub['content'])) {
							$return[$resultsub['id']]['label']= JText::_($resultsub['label']);
							$return[$resultsub['id']]['content']= $resultsub['content'];
							$return[$resultsub['id']]['id']= $resultsub['id'];						}
					}
				}
			}
			$this->plugincontent = $return;
		}
		return $this->plugincontent;
	}
	
	public function loadModule($moduleid) {
		if(isset($this->module[$moduleid])) {
			return $this->module[$moduleid];
		}
		
		$user = JFactory::getUser();
		$groups = implode(',', $user->getAuthorisedViewLevels());
		
		$db = JFactory::getDbo();
		$query	= $db->getQuery(true);
		$query->select('m.title, m.module');
		$query->from('#__modules AS m');
		$query->where('m.id = '. (int) $moduleid);
		$query->where('m.access IN (' . $groups . ')');
		$db->setQuery($query);
		$result = $db->loadAssoc();
		if(isset($result['module']) && isset($result['title'])) {
			return $this->module[$moduleid] = ProfilerHelperView::loadmod($result['module'], $result['title'], "html");
		} else {
			return $this->module[$moduleid] = "";
		}
	}
}
