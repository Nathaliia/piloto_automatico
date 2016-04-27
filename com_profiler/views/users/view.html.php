<?php
/**
 * @package Profiler for Joomla! 2.5
 * @version $Id: view.html.php 42 2013-04-26 16:03:35Z harold $
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

class ProfilerViewUsers extends JViewLegacy
{
	
	protected $rowtemplate = "";

	function display($tpl = null)
	{
		$app		= JFactory::getApplication();
		$params		= $app->getParams();
				
		$state		= $this->get('State');
		$items		= $this->get('Items');
		$fields		= $this->get('Fields');
		$pagination	= $this->get('Pagination');
		
		
		//stylesheet
		if($params->get("enable_css")) {
			JHtml::_('stylesheet', 'com_profiler/bootstrap-responsive.min.css', array(), true);
			JHtml::_('stylesheet', 'com_profiler/bootstrap.min.css', array(), true);
			JHtml::_('stylesheet', 'com_profiler/profiler.css', array(), true);
		}
		if($params->get("enable_jquery")) {
			JHtml::_('script', 'com_profiler/jquery.min.js', true, true);
		}
		if($params->get("enable_bootstrap")) {
			JHtml::_('script', 'com_profiler/bootstrap-tab.js', true, true);
			JHtml::_('script', 'com_profiler/bootstrap-collapse.js', true, true);
			JHtml::_('script', 'com_profiler/bootstrap-modal.js', true, true);
			JHtml::_('script', 'com_profiler/bootstrap-transition.js', true, true);
		}

		$this->pageclass_sfx = htmlspecialchars($params->get('pageclass_sfx'));
		
		$this->assignRef('state',		$state);
		$this->assignRef('params',		$params);
		$this->assignRef('items',		$items);
		$this->assignRef('fields',		$fields);
		$this->assignRef('pagination',	$pagination);
		
		//$active	= $app->getMenu()->getActive();
		if ($params->get("layout")) {
			$this->setLayout($params->get("layout"));
		}
		
		
		
		$this->_prepareFields();
		$this->_prepareFilters();
		$this->_prepareOrdering();
		$this->_prepareDocument();

		parent::display($tpl);
	}
	
	public function loadRowtemplate ($item)
	{
		$table = $this->params->get('table');
		
		
		if(!$this->rowtemplate) {
			$rowtemplate = $table['row'][0] ? "<td><p>" . nl2br($table['row'][0]) . "</p></td>" : "";
			$rowtemplate .= $table['row'][1] ? "<td><p>" . nl2br($table['row'][1]) . "</p></td>" : "";
			$rowtemplate .= $table['row'][2] ? "<td><p>" . nl2br($table['row'][2]) . "</p></td>" : "";
			$rowtemplate .= $table['row'][3] ? "<td><p>" . nl2br($table['row'][3]) . "</p></td>" : "";
			$rowtemplate .= $table['row'][4] ? "<td><p>" . nl2br($table['row'][4]) . "</p></td>" : "";
			$this->rowtemplate = str_replace(",", "<br/>", $rowtemplate);
		}
		
		return preg_replace('/\{([a-zA-Z_]+)\}/e', '$item->\\1', $this->rowtemplate);
		
	}
	
	public function loadHrowtemplate ()
	{
		$table = $this->params->get('table');
	
		$hrowtemplate = $table['hrow'][0] ? "<th>" . $table['hrow'][0] . "</th>" : "";
		$hrowtemplate .= $table['hrow'][1] ? "<th>" . $table['hrow'][1] . "</th>" : "";
		$hrowtemplate .= $table['hrow'][2] ? "<th>" . $table['hrow'][2] . "</th>" : "";
		$hrowtemplate .= $table['hrow'][3] ? "<th>" . $table['hrow'][3] . "</th>" : "";
		$hrowtemplate .= $table['hrow'][4] ? "<th>" . $table['hrow'][4] . "</th>" : "";
	
		return $hrowtemplate;
	
	}

	protected function _prepareDocument()
	{
		$app	= JFactory::getApplication();
		$menus	= $app->getMenu();
		$title	= null;

		// Because the application sets a default page title,
		// we need to get it from the menu item itself
		$menu = $menus->getActive();
		if($menu)
		{
			$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
		} else {
			$this->params->def('page_heading', JText::_('COM_PROFILER_DEFAULT_PAGE_TITLE'));
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
	
	protected function _prepareFields()
	{
		$config = JComponentHelper::getParams('com_profiler');
		if(!is_array($this->items)) {
			return;
		}
		foreach($this->items as &$row) {
		  foreach($row as $r => &$item) {
		  	if(($item || $r == "avatar") && isset($this->fields[$r])) {
				$field = $this->fields[$r];
				switch ($field['type']) {
					case "calendar":
						$format = $field['format'] ? (string) $field['format'] : $config->get('datetimedisplay');
						if($item){
							jimport('joomla.utilities.date');
    						$date = new JDate($item);
							$item = htmlspecialchars($date->Format($format), ENT_COMPAT, 'UTF-8');
						}
						break;
					case "list":
						if($field['multiple']) {
							$itemarray = json_decode($item, true);
							//afmaken
							$item = "multiple";	
						} else {
					
						$values = explode(",", $field['value']);
						foreach($values as $value) {
							$valuedetail = explode("=", $value);
							if($item == $valuedetail[0]) {
								$item = $valuedetail[1];
								break;
							}
						}
						}
						break;
					case "sql":
						$itemarray = json_decode($item, true);
						$query	= (string) $field['query'];
						$db = JFactory::getDBO();
						$db->setQuery($query);
						$options = $db->loadAssocList('value', (string) $field['name']);
						//$options = array_merge_recursive($options, $this->getOptions());
						$options = $options;// + $this->getOptions();

						if(is_array($itemarray) || is_object($itemarray)) {
							foreach($itemarray as &$row) {
								if(isset($option[$row]))
									$row = $options[$row];
							}
							$value = implode(", ", $itemarray);
						} elseif(is_string($itemarray)) {
							$value = $options[$itemarray];
						}
						$item = htmlspecialchars($value, ENT_COMPAT, 'UTF-8');
						break;
						
					case "avatar":
						$folder = $config->get('hpdestfolder');
						if($item) {
							$value		= $item ? JURI::root().'media/com_profiler/'.$folder.'/'.$field['type'].'/'.$item : '';
						} else {
							$value		= JURI::root().'media/com_profiler/img/user.png';
						}
				
						$imageinfo = getimagesize($value);
						if($imageinfo[0] > $imageinfo[1]) {
							$width		= $field['cols'] > 0 ? ' width:'.(int) $field['cols'].'px;' : '';
							$height		= ($field['rows'] > 0 && !$width) ? ' height:'.(int) $field['rows'].'px;' : '';
						} else {
							$height		= $field['rows'] > 0 ? ' height:'.(int) $field['rows'].'px;' : '';
							$width		= ($field['cols'] > 0 && !$height) ? ' width:'.(int) $field['cols'].'px;' : '';
						}
				
						$item = '<span class="avatar"><img src="'.$value.'" style="'.$width.$height.'" /></span>';
						break;
				}
			}
		  }
		}
	}
	
	protected function _prepareFilters()
	{
		$app = JFactory::getApplication();
		$params = $app->getParams();
		
		$access = ProfilerHelperAccess::getInstance();
		$usergroup_array = $access->getReadUserGroups();
		$editusergroups = implode(",", $access->getEditUserGroups());
		if(!$editusergroups) {
			$editusergroups = 0;
		}
		if(is_array($params->get('usergroup')) && count($params->get('usergroup')) > 0) {
			$usergroup = implode(",", array_intersect($usergroup_array, $params->get('usergroup')));
		} else {		
			$usergroup = implode(",", $access->getReadUserGroups());
		}
		if(!$usergroup) {
			$usergroup = 0;
		}
		
		$db = JFactory::getDbo();
		$db->setQuery(
			'SELECT a.id AS value, a.title AS text, COUNT(DISTINCT b.id) AS level' .
			' FROM #__usergroups AS a' .
			' LEFT JOIN `#__usergroups` AS b ON a.lft > b.lft AND a.rgt < b.rgt' .
			' WHERE a.id IN ('.$usergroup.')' .
			' GROUP BY a.id' .
			' ORDER BY a.lft ASC'
		);
		$options = $db->loadObjectList();

		// Check for a database error.
		if ($db->getErrorNum()) {
			JError::raiseNotice(500, $db->getErrorMsg());
			return null;
		}

		foreach ($options as &$option) {
			$option->text = str_repeat('- ',$option->level).$option->text;
		}
		//$access = ProfilerHelperAccess::getInstance();
		//$usergroup_array = $access->getReadUserGroups();
		$this->filters['profile'] = $options;
		
	}
	
	protected function _prepareOrdering()
	{
		
		$ordering = explode(",", $this->params->get('sort'));
		foreach($ordering as $row => $order) {
			$ordervars = explode("|", $order);
			$this->sort[$row]->text = $ordervars[0];
			$this->sort[$row]->value = $ordervars[1];
		}
		
	}
}
