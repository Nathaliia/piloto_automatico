<?php
/**
 * @package Profiler for Joomla! 3.0
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
defined('_JEXEC') or die;

?>

	<div class="row-fluid">
		<?php if (empty($this->items)) : ?>
		<p> <?php echo JText::_('COM_PROFILER_NO_USERS'); ?></p>
		<?php else : ?>
		<table class="table table-striped">
			<thead>
			<tr>
				<?php echo $this->loadHrowtemplate(); ?>
			</tr>
			</thead>
			<tbody>
				<?php
				foreach ($this->items as $i => $item) : 
					$link = "onclick=\"location.href='". JRoute::_(ProfilerHelperRoute::getUserRoute($item->id, $this->params->get('readonly'))) ."'\""; ?>
					<tr class="pointer" <?php echo $link ?>>
						<?php echo $this->loadRowtemplate($item); ?>
					</tr>
				<?php endforeach; ?>			
			</tbody>
		</table>
		<?php endif ?>
	</div>
	
	<div class="row-fluid">
		<div class="pagination pagination-centered">
			<p>
				<?php echo $this->pagination->getPagesCounter(); ?>
			</p>
		<?php echo $this->pagination->getPagesLinks(true); ?>
		</div>
	</div>