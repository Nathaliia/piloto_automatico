<?php
/**
 * @package Profiler for Joomla! 2.5
 * @version $Id: profiler.php 48 2013-06-10 21:36:21Z harold $
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
// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.controller');
require_once JPATH_COMPONENT.'/helpers/route.php';
require_once JPATH_ADMINISTRATOR . '/components/com_profiler/helpers/access.php';
require_once JPATH_ADMINISTRATOR . '/components/com_profiler/helpers/profiler.php';
require_once JPATH_COMPONENT . '/helpers/pagination.php';

$controller	= JControllerLegacy::getInstance('Profiler');
$controller->execute(JRequest::getCmd('task'));
$controller->redirect();
