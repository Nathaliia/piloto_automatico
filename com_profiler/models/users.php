<?php
/**
 * @package Profiler for Joomla! 2.5
 * @version $Id: users.php 48 2013-06-10 21:36:21Z harold $
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

jimport('joomla.application.component.modellist');

class ProfilerModelUsers extends JModelList
{
	protected $_item = null;
	protected $_fields = null;
	
	public function __construct($config = array())
	{
		$app = JFactory::getApplication();
		$params = $app->getParams();
		
		if (empty($config['filter_fields'])) {
			$sort = $params->get('sort');
			if($sort){
				$rows = explode(",", $sort);
				foreach($rows AS $row) {
					$rowvars = explode("|", $row);
					$pre = "p";
					if(in_array($rowvars[1], array("name", "username", "email", "id", "registerDate", "lastvisitDate", "block", "sendEmail", "groups")))
						$pre = "a";
					
					$config['filter_fields'][] = $rowvars[1];
					$config['filter_fields'][] = $pre.".".$rowvars[1];
				}
			}
		}

		parent::__construct($config);
	}
	
	public function &getItems()
	{
		// Invoke the parent getItems method to get the main list
		$items = &parent::getItems();
		
		// Convert the params field into an object, saving original in _params
		if($items) {
		 for ($i = 0, $n = count($items); $i < $n; $i++) {
			$item = &$items[$i];
			if (!isset($this->_params)) {
				$params = new JRegistry();
				$params->loadString($item->params);
				$item->params = $params;
			}
		  }
		}

		return $items;
	}
	
	public function getFields()
	{
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);
		
		$query->select('a.*');
		$query->from('`#__profiler_fields` AS a');
		$query->where('a.table = "#__users" OR a.table = "#__profiler"');
		
		$db->setQuery($query);
		$this->_fields = $db->loadAssocList("name");
		
		return $this->_fields;
	}
	
	public function getUnsetpagination()
	{
		$this->setState('list.start', 0);
		$this->state->set('list.limit', 0);

		
	}

	public function getPagination()
	{
		// Get a storage key.
		$store = $this->getStoreId('getPagination');
	
		// Try to load the data from internal storage.
		if (isset($this->cache[$store]))
		{
			return $this->cache[$store];
		}
	
		// Create the pagination object.
		$limit = (int) $this->getState('list.limit') - (int) $this->getState('list.links');
		$page = new ProfilerHelperPagination($this->getTotal(), $this->getStart(), $limit);
	
		// Add the object to the internal cache.
		$this->cache[$store] = $page;
	
		return $this->cache[$store];
	}
	
	protected function getListQuery()
	{
		$app = JFactory::getApplication();
		$params = $app->getParams();
		$user	= JFactory::getUser();

		//$usergroup = implode(",", $params->get('usergroup'));
		$access = ProfilerHelperAccess::getInstance();
		$usergroup_array = $access->getReadUserGroups();
		$editusergroups = implode(",", $access->getEditUserGroups());
		if(!$editusergroups) {
			$editusergroups = 0;
		}
		$filtergroup = $this->getState('filter.searchgroupid');
		if(in_array($filtergroup, $usergroup_array)) {
			$usergroup = (int) $filtergroup;
		} else {
			if(is_array($params->get('usergroup')) && count($params->get('usergroup')) > 0) {
				$usergroup = implode(",", array_intersect($usergroup_array, $params->get('usergroup')));
			} else {		
				$usergroup = implode(",", $access->getReadUserGroups());
			}
		}
		if(!$usergroup) {
			$usergroup = 0;
		}
		
		$where = $params->get('where') ? " AND (".$params->get('where') . ")" : "";
		if (preg_match_all("/\{([a-zA-Z_]+)\}/e", $where, $wherefields)) {
			foreach($wherefields[1] as $wherefield) {
					$pre = "p";
					if(in_array($wherefield, array("username", "lastvisitDate", "block", "sendEmail")))
						$pre = "a";
					$where = str_replace("{".$wherefield."}", $pre.".".$wherefield, $where);
			}	
		}
		
		// Create a new query object.
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);
		
		$query->select("DISTINCT p.*, a.username, a.lastvisitDate, a.block, a.sendEmail, a.params, IF(usergroup2.group_id, 1, 0) AS editaccess, GROUP_CONCAT(DISTINCT usergroupnames.title SEPARATOR ', ') AS groups, GROUP_CONCAT(DISTINCT pusergroupnames.title SEPARATOR ', ') AS pgroups");
		$query->from('`#__profiler_users` AS p');
		$query->join('LEFT', '#__users AS a ON a.id = p.userid');
		$query->join('LEFT', '#__user_usergroup_map AS usergroup ON usergroup.user_id = a.id');
		$query->join('LEFT', '#__user_usergroup_map AS usergroup2 ON usergroup2.user_id = a.id AND usergroup2.group_id IN ('.$editusergroups.')');
		$query->join('LEFT', '#__user_usergroup_map AS usergroup3 ON usergroup3.user_id = a.id');
		$query->join('LEFT', '#__usergroups AS usergroupnames ON usergroupnames.id = usergroup3.group_id');
		
		$query->join('LEFT', '#__profiler_usergroup_map AS pusergroup ON pusergroup.user_id = p.id');
		$query->join('LEFT', '#__profiler_usergroup_map AS pusergroup2 ON pusergroup2.user_id = p.id AND pusergroup2.group_id IN ('.$editusergroups.')');
		$query->join('LEFT', '#__profiler_usergroup_map AS pusergroup3 ON pusergroup3.user_id = p.id');
		$query->join('LEFT', '#__usergroups AS pusergroupnames ON pusergroupnames.id = pusergroup3.group_id');
		$query->group('p.id');
		$query->where('(usergroup.group_id IN ('.$usergroup.') OR pusergroup.group_id IN ('.$usergroup.'))'  . $where);
	
		
		if(!$params->get('showblock')) {
			$query->where('(a.block = 0 OR isnull(a.block))');
		}
		
		// Easy Filter
		$easysearch = $this->getState('filter.easysearch');
		if(!empty($easysearch)) {
			$easysearch = $db->Quote('%'.$db->escape($easysearch, true).'%');
			$easyfilterpar = $params->get('easyfilter');
			$easywhere = array();
			if(is_array($easyfilterpar)) {
				foreach($easyfilterpar AS $easyfilter) {
					$pre = "p";
					if(in_array($easyfilter, array("username", "lastvisitDate", "block", "sendEmail")))
						$pre = "a";
		
					$easywhere[] = $pre.'.'.$easyfilter.' LIKE '.$easysearch;
				}
				$query->where('('. implode(" || ", $easywhere) .')');
			}
		
		}
		
		// Filter 
		$filterpar = $params->get('filter');
		if($filterpar){
			$filters = explode(",", $params->get('filter'));
			foreach($filters AS $filter) {
				$filtervars = explode("|", $filter);
				$search = $this->getState('filter.search'.$filtervars[1]);
				if (!empty($search)) {
					$search = $db->Quote('%'.$db->escape($search, true).'%');

					$pre = "p";
					if(in_array($filtervars[1], array("username", "lastvisitDate", "block", "sendEmail")))
						$pre = "a";
				
					$query->where('('.$pre.'.'.$filtervars[1].' LIKE '.$search.')');
				}
			}
		}
		
		// Ordering
		$ordering = $this->getState('list.ordering', 'id');
		
		$pre = "p";
		if(in_array($ordering, array("username", "lastvisitDate", "block", "sendEmail")))
			$pre = "a";
				
		$query->order($db->escape($pre.".".$ordering).' '.$db->escape($this->getState('list.direction', 'ASC')));
		return $query;
	}
	
	protected function populateState($ordering = null, $direction = null) {
		$app = JFactory::getApplication();
		$params = $app->getParams();
	
		$ordering = explode(" ", $params->get('ordering', 'id ASC'));
		parent::populateState($ordering[0], $ordering[1]);
		
		$search = $this->getUserStateFromRequest($this->context.'.filter.searchgroupid', 'filter_searchgroupid');
		$this->setState('filter.searchgroupid', $search);
		
		$search = $this->getUserStateFromRequest($this->context.'.filter.easysearch', 'filter_easysearch');
		$this->setState('filter.easysearch', $search);
		
		
		$filterpar = $params->get('filter');
		if($filterpar){
			$filters = explode(",", $params->get('filter'));
			foreach($filters AS $filter) {
				$filtervars = explode("|", $filter);
				$search = $this->getUserStateFromRequest($this->context.'.filter.search'.$filtervars[1], 'filter_search'.$filtervars[1]);
				$this->setState('filter.search'.$filtervars[1], $search);
			}
		}
	}
	
	
}
