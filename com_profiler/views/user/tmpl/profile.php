<?php
/**
 * @package Profiler for Joomla! 2.5
 * @version $Id: profile.php 41 2013-03-28 18:06:15Z harold $
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

// Include the component/HTML helpers and JavaScript code.
JPluginHelper::importPlugin('profiler');
$dispatcher =& JDispatcher::getInstance();

JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
//JHtml::_('formbehavior.chosen', 'select');

// Get the form categories and fields.
//if(isset($this->pro_groups['form'][0]))
	//$fieldsets_groups = $this->pro_groups['form'][0]->getFieldsets();
?>

<script type="text/javascript">
	Joomla.bevoresubmitbutton = function(task, plugin, ro) {
		form = document.getElementById('profiler-user');
		form.plugin.value = plugin;
		form.pluginro.value = ro;
		Joomla.submitform(task, form);
	}
</script>

<style type="text/css">
  .profiler-readonly {
    display: inline-block;
    margin-bottom: 0;
    vertical-align: middle;
    height: 18px;
    line-height: 18px;
    font-size: 13px;
    padding-top: 5px;
  }
</style>

<form action="<?php echo JRoute::_('index.php?option=com_profiler&view=user&id='.(int) $this->item->id); ?>" method="post" enctype="multipart/form-data" name="adminForm" id="profiler-user" class="form-validate">
		<div class="row-fluid">
			<div class="hero-unit">
				<div class="row">
					<div class="span3">
						<?php //image
							echo $this->loadTemplate('fields', 'image', false);
						?>	
					</div>
					<div class="span9">
						<?php if ($this->params->get('show_page_heading')) : ?>
							<h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
						<?php endif; ?>
						<?php //head
							echo $this->loadTemplate('fields', 'head');
						?>	
					</div>
				</div>
			
			</div>
		</div>
		<div class="row-fluid">
		    <div class="tabbable tabs-left">
				<ul class="nav nav-tabs">
					<?php $count = 0;
					 foreach ($this->fieldsets as $fieldset) :
						if ($fieldset->name == 'settings' || ($fieldset->position != "")) :
							continue;
						endif;
						$count++;
						if(($fieldset->type == 'plugin' && isset($this->plugincontent[$fieldset->alias])) || ($fieldset->type == 'module' && $this->loadmodule($fieldset->eid)) || ($fieldset->type == 'category')) { ?>
							<li <?php echo $count == 1 ? "class=\"active\"" : "";?>><a data-toggle="tab" href="#<?php echo $fieldset->alias;?>"><?php echo $fieldset->label;?></a></li>
						<?php } ?>
					<?php endforeach; ?>
				</ul>
				<div class="tab-content">
						<?php //body
							echo $this->loadTemplate('fields');
						?>
						
				</div>
			</div>
		</div>
		<div class="row-fluid">
			<div class="form-actions">
				<?php if (JRequest::getCmd('ro')){
					if($this->menu) { ?>
						<input type="button" class="btn btn-primary" onclick="location.href='<?php echo JRoute::_(ProfilerHelperRoute::getUserRoute($this->item->id, false)); ?>'" value="<?php echo JText::_("COM_PROFILER_EDIT") ?>" />
					<?php } ?>
					<input type="hidden" name="task" value="" />
				<?php } elseif (!JRequest::getCmd('ro')) { ?>
					<button type="submit" class="btn btn-primary validate"><?php echo $this->item->id ? JText::_('JSAVE') : JText::_('JREGISTER');?></button>
					<input type="hidden" name="option" value="com_profiler" />
					<input type="hidden" name="task" value="user.register" />
				<?php } ?>
				<input type="hidden" name="plugin" value="" />
				<input type="hidden" name="pluginro" value="" />
				<?php echo JHtml::_('form.token');?>
				<?php $this->loadPluginMenu();?>
			</div>
		</div>
		<div class="row-fluid">
			<div class="span4">
				<?php echo $this->loadTemplate('fields', 'footer1');?>
			</div>
			<div class="span4">
				<?php echo $this->loadTemplate('fields', 'footer2');?>
			</div>
			<div class="span4">
				<?php echo $this->loadTemplate('fields', 'footer3');?>
			</div>
			
		</div>

</form>

