<?php
/**
 * Copyright (c) Extensionsforjoomla.com - E4J - Alessio <tech@extensionsforjoomla.com>
 * 
 * You should have received a copy of the License
 * along with this program.  If not, see <http://www.extensionsforjoomla.com/>.
 * 
 * For any bug, error please contact <tech@extensionsforjoomla.com>
 * We will try to fix it.
 * 
 * Extensionsforjoomla.com - All Rights Reserved
 * 
 */

defined('_JEXEC') OR die('Restricted Area');

defined('_VIKRENTCAREXEC') OR die('Restricted Area');

?>

<style type="text/css">
.clearfix:after {
content: "."; display: block; clear: both; visibility: hidden; line-height: 0; height: 0;
}
</style>

<p>{logo}</p>

<div class="container" style="width: 80%; font-family: 'Arial Rounded MT Bold', 'Helvetica Rounded', Arial, sans-serif;">
<p class="Stile1" style="font-size: 18px; font-weight: bold;">{company_name}</p>
	<div class="statusorder" style="width: 100%; float: none; clear: both;">
    	<div class="boxstatusorder" style="border-radius: 4px; -moz-border-radius: 4px; -webkit-border-radius: 4px; float: left; height: 25px; line-height: 25px; background: #d9f1ff; margin: 0 5px 10px 0; padding: 10px; border: 1px solid #c4dbdd;"><p class="Stile1" style="font-size: 18px; font-weight: bold; margin: 0; padding: 0;"><?php echo JText::_('VRCORDERNUMBER'); ?>: {order_id}</p></div>
        <div class="boxstatusorder" style="border-radius: 4px; -moz-border-radius: 4px; -webkit-border-radius: 4px; float: left; height: 25px; line-height: 25px; background: #F53636; margin: 0 5px 10px 0; padding: 10px; border: 1px solid #c4dbdd;"><span class="Stile1" style="font-size: 18px; font-weight: bold;"><?php echo JText :: _('VRLIBSEVEN'); ?>: <span class="{order_status_class}">{order_status}</span></span></div>
        <div class="boxstatusorder" style="border-radius: 4px; -moz-border-radius: 4px; -webkit-border-radius: 4px; float: left; height: 25px; line-height: 25px; background: #d9f1ff; margin: 0 0 10px; padding: 10px; border: 1px solid #c4dbdd;">
<strong><?php echo JText::_('VRLIBEIGHT'); ?>:</strong>{order_date}</div>
    </div>
    <div class="persdetail" style="width: 95.8%; clear: both; float: none; border-radius: 4px; -moz-border-radius: 4px; -webkit-border-radius: 4px; line-height: 1.6em; padding: 10px; border: 1px solid #eee;">
    	<h3 class="Stile1" style="font-size: 18px; font-weight: bold; margin: 0 0 10px; padding: 0;">
<?php echo JText::_('VRLIBNINE'); ?>:</h3>
        Error con el envio del correo al Piloto: {name_Cond}, verifique el email del piloto o que este se encuentre suscrito al modulo de notificaciones
    </div>

   
   

</div>