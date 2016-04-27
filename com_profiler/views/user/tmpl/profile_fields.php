<?php
/**
 * @package Profiler for Joomla! 2.5
 * @version $Id$
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
$count = 0;
foreach ($this->fieldsets as $fieldsetid => $fieldset) :
	if ($fieldset->name == 'settings' || ($fieldset->position != $this->view_position)) :
		continue;
	endif;	
	$count++; ?>
	<div id="<?php echo $fieldset->alias;?>" class="<?php echo $this->view_labels ? "form-horizontal" : ""; ?> tab-pane  <?php echo $count == 1 ? "active" : "";?>">
		<?php 
		if($fieldset->type == 'plugin') {
			if(isset($this->plugincontent[$fieldset->alias])) {
				echo $this->plugincontent[$fieldset->alias]['content'];
			}
		} elseif($fieldset->type == 'module' && $this->loadmodule($fieldset->eid)) {
			echo $this->loadmodule($fieldset->eid);
		} elseif($fieldset->type == 'category') {
			foreach($this->form->getFieldset($fieldset->name) as $field) {
				if($field->hidden == true) { ?>
					<?php echo $field->input; ?>
				<?php } else { ?>
				<div class="control-group">
				<?php if($this->view_labels) : ?>
					<label class="control-label" for="<?php echo $field->name; ?>"><?php echo $field->label; ?></label>
				<?php endif; ?>
				<div class="controls">
					<?php echo $field->input; ?>		
				</div>
				</div>
				<?php } ?>
			<?php } ?>
		<?php } ?>
	</div>
	<?php unset($this->fieldsets[$fieldsetid]); 
endforeach; ?>
