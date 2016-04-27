<?php
/**
 * @package Profiler for Joomla! 3.0
 * @version $Id: edit.php 12 2013-03-28 18:07:09Z harold $
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
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
//JHtml::_('formbehavior.chosen', 'select');

//javascript form handling?>

<script type="text/javascript">
	Joomla.bevoresubmitbutton = function(task, plugin, ro) {
		form = document.getElementById('profiler-user');
		form.plugin.value = plugin;
		form.pluginro.value = ro;
		Joomla.submitform(task, form);
	}
</script>

<div class="container-fluid">
<form action="<?php echo JRoute::_('index.php?option=com_profiler&view=user&id='.(int) $this->item->id); ?>" method="post" enctype="multipart/form-data" name="adminForm" id="profiler-user" class="form-validate form-horizontal">

		<div class="row-fluid">
			<?php if ($this->params->get('show_page_heading', 1)) : ?>
				<h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
			<?php endif; ?>
		</div>
		<div class="row-fluid">
		<?php
			echo $this->loadTemplate('fields');
		?>
		</div>
		
<?php
//add fieldgroup frontend userparams 
if ($this->params->get('frontend_userparams')) : ?>	
		<h2><?php echo JText::_('COM_PROFILER_SETTINGS_FIELDSET_LABEL'); ?></h2>
		<?php foreach($this->form->getFieldset('settings') as $field): ?>
			<?php if ($field->hidden): ?>
				<?php echo $field->input; ?>
			<?php else: ?>
				<div class="control-group">
					<label class="control-label" for="<?php echo $field->name; ?>"><?php echo $field->title; ?></label>
					<div class="controls">
						<?php echo $field->input; ?>
					</div>
				</div>
			<?php endif; ?>
		<?php endforeach; ?>

<?php endif; 
// add hidden fields and buttons?>


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

<?php 
//plugin usermenu

?>
</form>
</div>
