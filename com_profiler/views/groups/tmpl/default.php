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


if(is_array($this->params->get('groupeasyfilter')))
	$easysearch = true;
if($this->params->get('groupfilter'))
	$advancedsearch = true;

?>
<div class="container-fluid">
<form class="form-horizontal" action="<?php echo JFilterOutput::ampReplace(JFactory::getURI()->toString()); ?>" method="post" name="filter_search" id="filter_search">

	<div class="row-fluid">
		<?php if ($this->params->get('show_page_heading', 1)) : ?>
			<h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
		<?php endif; ?>
	</div>
	
	<?php if((isset($easysearch) && $easysearch) || (isset($advancedsearch) && $advancedsearch)) : ?>
	<div class="row-fluid">
	 <div class="span12">
	  	<div class="accordion" id="accordion">
	  		<?php if(isset($easysearch) && $easysearch) : ?>
	  		<div class="accordion-group">
				<div class="accordion-heading">
					<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapseOne">
						<?php echo JText::_('COM_PROFILER_USERS_HEADER_EASYSEARCH'); ?>
					</a>
				</div>
				<div id="collapseOne" class="accordion-body collapse in">
					<div class="accordion-inner">
	   					<div class="control-group">
							<label class="control-label" for="filter_easysearch"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></label>
							<div class="controls">
								<div class="input-append">
									<input type="text" name="filter_easysearch" id="filter_easysearch" value="<?php echo $this->escape($this->state->get('filter.easysearch'))?>" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>" />
									<button type="submit" class="btn btn-primary"><i class="icon-search icon-white"></i> <?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
								</div>
							</div>
	  					</div>
					</div>
				</div>
			</div>
			<?php endif; ?>
			<?php if(isset($advancedsearch) && $advancedsearch) : ?>
	  		<div class="accordion-group">
				<div class="accordion-heading">
					<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo">
						<?php echo JText::_('COM_PROFILER_USERS_HEADER_ADVANCEDSEARCH'); ?>
					</a>
				</div>
				<div id="collapseTwo" class="accordion-body collapse">
					<div class="accordion-inner">
						<div class="form-horizontal">
						<?php 
						$filterpar = $this->params->get('groupfilter');
						if($filterpar){
							$filters = explode(",", $this->params->get('groupfilter'));
							foreach($filters AS $filter) {
								$filtervars = explode("|", $filter); ?>
								<div class="control-group">
								<label class="control-label" for="filter_search<?php echo $filtervars[1]?>"><?php echo $filtervars[0]?></label>
								<div class="controls">
									<input type="text" name="filter_search<?php echo $filtervars[1]?>" id="filter_search<?php echo $filtervars[1]?>" value="<?php echo $this->escape($this->state->get('filter.search'.$filtervars[1]))?>" title="<?php echo $filtervars[0]?>" />
								</div>
								</div><?php 
							}
						?>
						<button type="submit" class="btn btn-primary"><i class="icon-search icon-white"></i> <?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
					    <?php 
						}?>
						</div>					
					</div>
				</div>
			</div>
			<?php endif; ?>
		</div>
	  </div>
	</div>
	<?php endif; ?>
	
	<div class="row-fluid">
	  <div class="span12 well">
		<div class="form-inline pull-right">
		
			<?php echo JText::_('JGLOBAL_DISPLAY_NUM'); ?>&#160;
			<?php echo $this->pagination->getLimitBox(); ?>
			<select name="filter_order" class="input-small" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('COM_PROFILER_USERS_ORDER_USERGROUP');?></option>
				<?php echo JHtml::_('select.options', $this->sort, 'value', 'text', $this->state->get('list.ordering'));?>
			</select>
			
		</div>
		<input type="hidden" name="task" id="task" value="" />
		<input type="hidden" name="format" id="format" value="html" />
	 </div>
	</div>

	<div class="row-fluid">
		<?php if (empty($this->items)) : ?>
		<p> <?php echo JText::_('COM_PROFILER_NO_GROUPS'); ?></p>
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
					$link = "onclick=\"location.href='". JRoute::_(ProfilerHelperRoute::getGroupRoute($item->groupid, $this->params->get('groupreadonly'))) ."'\""; ?>
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
		<?php echo $this->pagination->getPagesLinks(); ?>
		</div>
	</div>
	
	
</form>
</div>

 
