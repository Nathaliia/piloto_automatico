<?php
/**
 * @package Profiler for Joomla! 2.5
 * @version $Id: route.php 48 2013-06-10 21:36:21Z harold $
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


jimport('joomla.application.component.helper');

abstract class ProfilerHelperRoute
{
	protected static $lookup;
	
	public static function getUserRoute($id, $readonly){
		
		$link = 'index.php?option=com_profiler&view=user&ro='.$readonly.'&id='. $id;
	
		if ($item = self::_findItem()) {
			$link .= '&Itemid='.$item;
		} elseif(JRequest::getCmd('task') == "register") {
			$link .= '&layout=edit';
		}

		return $link;
	
	}

	public static function getGroupRoute($id, $readonly){
	
		$link = 'index.php?option=com_profiler&view=group&ro='.$readonly.'&groupid='. $id;
	
		if ($item = self::_findItem()) {
			$link .= '&Itemid='.$item;
		}
	
		return $link;
	
	}
	
	public static function getUsersajaxRoute(){
		$link = 'index.php?option=com_profiler&format=raw&view=users&layout=ajax&ajax=userlist';
	
		if ($item = self::_findItem()) {
			$link .= '&Itemid='.$item;
		}
	
		return $link;
	
	}
	
	public static function getUsersajaxdefaultRoute(){
		$link = 'index.php?option=com_profiler&format=raw&view=users&layout=ajax&ajax=default';
	
		if ($item = self::_findItem()) {
			$link .= '&Itemid='.$item;
		}
	
		return $link;
	
	}
	
	protected static function _findItem($needles = null)
	{
		$app		= JFactory::getApplication();
		$menus		= $app->getMenu('site');

		// Prepare the reverse lookup array.
		if (self::$lookup === null)
		{
			self::$lookup = array();

			$component	= JComponentHelper::getComponent('com_profiler');
			$items		= $menus->getItems('component_id', $component->id);
			if ($items) {
			  foreach ($items as $item)
			  {
				if (isset($item->query) && isset($item->query['view']))
				{
					$view = $item->query['view'];
					if (!isset(self::$lookup[$view])) {
						self::$lookup[$view] = array();
					}
					if (isset($item->query['id'])) {
						self::$lookup[$view][$item->query['id']] = $item->id;
					}
				}
			  }
			}
		}

		if ($needles)
		{
			foreach ($needles as $view => $ids)
			{
				if (isset(self::$lookup[$view]))
				{
					foreach($ids as $id)
					{
						if (isset(self::$lookup[$view][(int)$id])) {
							return self::$lookup[$view][(int)$id];
						}
					}
				}
			}
		}
		else
		{
			$active = $menus->getActive();
			if ($active && $active->component == 'com_profiler') {
				return $active->id;
			}
		}

		return null;
	}
}
