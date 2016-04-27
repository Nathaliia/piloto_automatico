<?php
/**
 * @package Profiler for Joomla! 2.5
 * @version $Id: default_excel.php 42 2013-04-26 16:03:35Z harold $
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

require_once JPATH_ADMINISTRATOR . '/components/com_profiler/helpers/excel.php';

// create a simple 2-dimensional array
$titlerow = array_keys(get_object_vars($this->items[0]));
$data = $this->items;
//$exportfields = explode(",", $this->params->get('download'));


// generate file (constructor parameters are optional)
$xls = new ProfilerHelperExcel('UTF-8', true);
$xls->addWorksheet("testpagina");
$xls->addWorksheet("testpagina2");
$xls->addRow($this->params->get('download'), "testpagina");
$xls->setAllowFields($this->params->get('download'));
$xls->addArray($data, "testpagina");
$xls->addArray($data, "testpagina2");
$xls->generateXML('my-test');
