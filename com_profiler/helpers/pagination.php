<?php
/**
 * @package Profiler for Joomla! 2.5
 * @version $Id: default.php 13 2013-04-26 15:59:36Z harold $
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

//change pagination in ajax...

jimport('joomla.html.pagination');

class ProfilerHelperPagination extends JPagination
{
	
	public function getLimitBox($ajax = false)
	{
		if(!$ajax)
			return parent::getLimitBox();
		
		$limits = array();
	
		// Make the option list.
		for ($i = 5; $i <= 30; $i += 5)
		{
			$limits[] = JHtml::_('select.option', "$i");
		}
		$limits[] = JHtml::_('select.option', '50', JText::_('J50'));
		$limits[] = JHtml::_('select.option', '100', JText::_('J100'));
		$limits[] = JHtml::_('select.option', '0', JText::_('JALL'));
	
		$selected = $this->_viewall ? 0 : $this->limit;
	
		$html = JHtml::_(
				'select.genericlist',
				$limits,
				$this->prefix . 'limit',
				'class="inputbox input-mini" size="1" onchange="ajaxsearch();"',
				'value',
				'text',
				$selected
		);
		return $html;
	}
	
	public function getPagesLinks($ajax = false)
	{
		if(!$ajax)
			return parent::getPagesLinks();	
		
		$app = JFactory::getApplication();
		
		// Build the page navigation list.
		$data = $this->_buildDataObject();
		
		$list = array();
		$list['prefix'] = $this->prefix;
		
		$itemOverride = false;
		$listOverride = false;
		
		$chromePath = JPATH_THEMES . '/' . $app->getTemplate() . '/html/pagination.php';
		if (file_exists($chromePath))
		{
			include_once $chromePath;
			if (function_exists('pagination_itemajax_active') && function_exists('pagination_item_inactive'))
			{
				$itemOverride = true;
			}
			if (function_exists('pagination_list_render'))
			{
				$listOverride = true;
			}
		}
		
		// Build the select list
		if ($data->all->base !== null)
		{
			$list['all']['active'] = true;
			$list['all']['data'] = ($itemOverride) ? pagination_itemajax_active($data->all) : $this->_itemajax_active($data->all);
		}
		else
		{
			$list['all']['active'] = false;
			$list['all']['data'] = ($itemOverride) ? pagination_item_inactive($data->all) : $this->_item_inactive($data->all);
		}
		
		if ($data->start->base !== null)
		{
			$list['start']['active'] = true;
			$list['start']['data'] = ($itemOverride) ? pagination_itemajax_active($data->start) : $this->_itemajax_active($data->start);
		}
		else
		{
			$list['start']['active'] = false;
			$list['start']['data'] = ($itemOverride) ? pagination_item_inactive($data->start) : $this->_item_inactive($data->start);
		}
		if ($data->previous->base !== null)
		{
			$list['previous']['active'] = true;
			$list['previous']['data'] = ($itemOverride) ? pagination_itemajax_active($data->previous) : $this->_itemajax_active($data->previous);
		}
		else
		{
			$list['previous']['active'] = false;
			$list['previous']['data'] = ($itemOverride) ? pagination_item_inactive($data->previous) : $this->_item_inactive($data->previous);
		}
		
		// Make sure it exists
		$list['pages'] = array();
		foreach ($data->pages as $i => $page)
		{
			if ($page->base !== null)
			{
				$list['pages'][$i]['active'] = true;
				$list['pages'][$i]['data'] = ($itemOverride) ? pagination_itemajax_active($page) : $this->_itemajax_active($page);
			}
			else
			{
				$list['pages'][$i]['active'] = false;
				$list['pages'][$i]['data'] = ($itemOverride) ? pagination_item_inactive($page) : $this->_item_inactive($page);
			}
		}
		
		if ($data->next->base !== null)
		{
			$list['next']['active'] = true;
			$list['next']['data'] = ($itemOverride) ? pagination_itemajax_active($data->next) : $this->_itemajax_active($data->next);
		}
		else
		{
			$list['next']['active'] = false;
			$list['next']['data'] = ($itemOverride) ? pagination_item_inactive($data->next) : $this->_item_inactive($data->next);
		}
		
		if ($data->end->base !== null)
		{
			$list['end']['active'] = true;
			$list['end']['data'] = ($itemOverride) ? pagination_itemajax_active($data->end) : $this->_itemajax_active($data->end);
		}
		else
		{
			$list['end']['active'] = false;
			$list['end']['data'] = ($itemOverride) ? pagination_item_inactive($data->end) : $this->_item_inactive($data->end);
		}
		
		if ($this->total > $this->limit)
		{
			return ($listOverride) ? pagination_list_render($list) : $this->_list_render($list);
		}
		else
		{
			return '';
		}
	}
	
	protected function _itemajax_active(JPaginationObject $item)
	{
		if ($item->base > 0)
		{
			return "<a title=\"" . $item->text . "\" onclick=\"jQuery('#limitstart').val(" . $item->base
					. "); ajaxsearch();return false;\" href=\"#\" class=\"pagenav\">" . $item->text . "</a>";
		}
		else
		{
			return "<a title=\"" . $item->text . "\" onclick=\"jQuery('#limitstart').val(0);" 
					. " ajaxsearch();return false;\" href=\"#\" class=\"pagenav\">" . $item->text . "</a>";
		}
	}
}