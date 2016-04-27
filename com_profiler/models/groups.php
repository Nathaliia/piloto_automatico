<?php
/**
 * @package Profiler for Joomla! 2.5
 * @version $Id: groups.php 48 2013-06-10 21:36:21Z harold $
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

class ProfilerModelGroups extends JModelList
{
	protected $_item = null;
	protected $_fields = null;
	
	public function __construct($config = array())
	{
		$app = JFactory::getApplication();
		$params = $app->getParams();
		
		if (empty($config['filter_fields'])) {
			$sort = $params->get('groupsort');
			if($sort){
				$rows = explode(",", $sort);
				foreach($rows AS $row) {
					$rowvars = explode("|", $row);
					$pre = "p";
					
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
		

		return $items;
	}
	
	public function getFields()
	{
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);
		
		$query->select('a.*');
		$query->from('`#__profiler_fields` AS a');
		$query->where('a.table = "#__groups"');
		
		$db->setQuery($query);
		$this->_fields = $db->loadAssocList("name");
		
		return $this->_fields;
	}
	
	public function getUnsetpagination()
	{
		$this->setState('list.start', 0);
		$this->state->set('list.limit', 0);

		
	}

	protected function getListQuery()
	{
		$app = JFactory::getApplication();
		$params = $app->getParams();
		$user	= JFactory::getUser();


		$where = $params->get('groupwhere') ? " AND (".$params->get('groupwhere') . ")" : "";
		if (preg_match_all("/\{([a-zA-Z_]+)\}/e", $where, $wherefields)) {
			foreach($wherefields[1] as $wherefield) {
					$pre = "p";
					$where = str_replace("{".$wherefield."}", $pre.".".$wherefield, $where);
			}	
		}
		
		// Create a new query object.
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);
		
		$query->select("p.*");
		$query->from('`#__profiler_groups` AS p');
		
		if($where)
			$query->where($where);
	
		
		if(!$params->get('groupshowuser')) {
			$query->where('(p.groupblock = 0 OR isnull(p.groupblock))');
		}
		
		// Easy Filter
		$easysearch = $this->getState('filter.easysearch');
		if(!empty($easysearch)) {
			$easysearch = $db->Quote('%'.$db->escape($easysearch, true).'%');
			$easyfilterpar = $params->get('groupeasyfilter');
			$easywhere = array();
			if(is_array($easyfilterpar)) {
				foreach($easyfilterpar AS $easyfilter) {
					$pre = "p";
						
					$easywhere[] = $pre.'.'.$easyfilter.' LIKE '.$easysearch;
				}
				$query->where('('. implode(" || ", $easywhere) .')');
			}
				
		}
		
		// Filter 
		$filterpar = $params->get('groupfilter');
		if($filterpar){
			$filters = explode(",", $params->get('groupfilter'));
			foreach($filters AS $filter) {
				$filtervars = explode("|", $filter);
				$search = $this->getState('filter.search'.$filtervars[1]);
				if (!empty($search)) {
					$search = $db->Quote('%'.$db->escape($search, true).'%');

					$pre = "p";
				
					$query->where('('.$pre.'.'.$filtervars[1].' LIKE '.$search.')');
				}
			}
		}
		
		// Ordering
		$ordering = $this->getState('list.ordering', 'groupid');
		
		$pre = "p";
				
		$query->order($db->escape($pre.".".$ordering).' '.$db->escape($this->getState('list.direction', 'ASC')));
		return $query;
	}
	
	protected function populateState($ordering = null, $direction = null) {
		$app = JFactory::getApplication();
		$params = $app->getParams();
	
		$ordering = explode(" ", $params->get('groupordering', 'groupid ASC'));
		parent::populateState($ordering[0], $ordering[1]);
		
		$search = $this->getUserStateFromRequest($this->context.'.filter.easysearch', 'filter_easysearch');
		$this->setState('filter.easysearch', $search);
		
		
		$filterpar = $params->get('groupfilter');
		if($filterpar){
			$filters = explode(",", $params->get('groupfilter'));
			foreach($filters AS $filter) {
				$filtervars = explode("|", $filter);
				$search = $this->getUserStateFromRequest($this->context.'.filter.search'.$filtervars[1], 'filter_search'.$filtervars[1]);
				$this->setState('filter.search'.$filtervars[1], $search);
			}
		}
	}
	
	
}
