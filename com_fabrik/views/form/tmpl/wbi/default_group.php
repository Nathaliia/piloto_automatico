<?php
/**
 * Admin Group Form Template
 *
 * @package     Joomla
 * @subpackage  Fabrik
 * @copyright   Copyright (C) 2005 Fabrik. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @since       3.0
 */

 ?>


<?php 
	$ubicacion = array('left','right');
	$a = 'left';
	
	
?>

<?php
$document = & JFactory :: getDocument();



$document->addStyleSheet(JURI::root().'components/com_vikrentcar/resources/style.css');


$document->addScript(JURI::root().'components/com_vikrentcar/resources/jquery-1.8.2.min.js');



$document->addScript(JURI::root().'components/com_vikrentcar/resources/jquery.uniform3.min.js');

?>

<script language="JavaScript" type="text/javascript">
jQuery.noConflict();
	jQuery(document).ready(function() {

   //jQuery("#chun4_vikrentcar_profiles___doc_type").uniform();
   jQuery('input[type=radio],select, input[type=checkbox]', '.fabrikForm').uniform();
   jQuery("#chun4_vikrentcar_profiles___doc_type").css('width','100%');

   
	});

</script>

<?php

 foreach ( $this->elements as $element) {
	
	
	?>

 <div class="<?php echo $element->containerClass;?>" <?php echo @$element->column;?> > 
	<div class="<?php echo $a;?>">
	<?php 	if ($a == 'left'){
				$a='left';
			}else{
				$a='left';
			} ?>
        
	 	<?php echo $element->label;?>
		<?php echo $element->errorTag; ?>
		<div class="fabrikElement">
			<?php echo $element->element;?>
		</div>
		<div class="fabrikErrorMessage">
			<?php echo $element->error;?>
		</div>
		
    </div> 
 </div>

	<?php }?>
