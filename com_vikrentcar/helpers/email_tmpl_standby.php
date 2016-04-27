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
        <div class="boxstatusorder" style="width: 95.8%; clear: both; float: none; border-radius: 4px; -moz-border-radius: 4px; -webkit-border-radius: 4px; line-height: 1.6em; background: #d9f1ff; padding: 10px; border: 1px solid #eee;  margin: 0 0 10px; border: 1px solid #c4dbdd;"><p class="Stile1" style="font-size: 18px; font-weight: bold; margin: 0; padding: 0;"><?php echo JText::_('VRCORDERNUMBER'); ?>: {order_id}</p></div>
        <div class="boxstatusorder" style="width: 95.8%; clear: both; float: none; border-radius: 4px; -moz-border-radius: 4px; -webkit-border-radius: 4px; line-height: 1.6em; background: #d9f1ff; padding: 10px; border: 1px solid #eee;  margin: 0 0 10px; border: 1px solid #c4dbdd;"><p class="Stile1" style="font-size: 18px; font-weight: bold; margin: 0; padding: 0;"><?php echo JText :: _('VRLIBSEVEN'); ?>: <span class="{order_status_class}">{order_status}</span></p></div>
        <div class="boxstatusorder" style="width: 95.8%; clear: both; float: none; border-radius: 4px; -moz-border-radius: 4px; -webkit-border-radius: 4px; line-height: 1.6em; background: #d9f1ff; padding: 10px; border: 1px solid #eee;  margin: 0 0 10px; border: 1px solid #c4dbdd;">

        <p class="Stile1" style="font-size: 18px; font-weight: bold; margin: 0; padding: 0;"><strong><?php echo JText::_('VRLIBEIGHT'); ?>:</strong>{order_date}</p></div>
    </div>
    <div class="persdetail" style="width: 95.8%; clear: both; float: none; border-radius: 4px; -moz-border-radius: 4px; -webkit-border-radius: 4px; line-height: 1.6em; padding: 10px; border: 1px solid #eee;">
        <h3 class="Stile1" style="font-size: 18px; font-weight: bold; margin: 0 0 10px; padding: 0;">
<?php echo JText::_('VRLIBNINE'); ?>:</h3>
  <div style="font-size:14px; " >
        {customer_info}
  </div>      
    </div>

   
     <div class="pilotodetail" style="width: 95.8%; clear: both; float: none; border-radius: 4px; -moz-border-radius: 4px; -webkit-border-radius: 4px; line-height: 1.6em; padding: 10px; border: 1px solid #eee;">
        {title_datapilot}
        <div style="font-size:14px;" >
         <p ><?php echo JText::_('VRLIBNINEPILOTNAME'); ?>: {name_Cond}</p>

        <p> <?php echo JText::_('VRLIBNINECELUCOND'); ?>: {CelularCond}</p>

        <p> <?php echo JText::_('VRLIBNINEDOCUMENTO'); ?>: {CedulaCond}</p>
        </div >
         

   
    </div>
    <div class="hiremainbox" style="-moz-border: 1px solid #eee; -webkit-border: 1px solid #eee; border-radius: 4px; width: 95.8%; background: #fbfbfb; margin: 10px 0 0; padding: 10px; border: 1px solid #eee;">
        <div class="hirecar clearfix" style="display: block\9; float: none; clear: both;">
            <p><span class="Stile1" style="font-size: 18px; font-weight: bold;"><?php echo JText::_('VRLIBTEN'); ?>: {item_name}</span></p>
              <div style="font-size:14px;" >
             {placa_Cond}
            
            <p >{carInfo}</p>
            </div >
           
            <div class="hiredate" style="/*float: left; */ border-radius: 4px; -moz-border-radius: 4px; -webkit-border-radius: 4px; background: #f6f6f6; margin: 10px 0 0 0; padding: 10px; border: 1px solid #c9e9fc;">
                <p style="margin: 0px 0 5px; padding: 0;"><span class="Stile12" style="display: block; font-size: 14px; font-weight: bold;  float:left;"><?php echo JText::_('VRLIBELEVEN'); ?>:</span>
                <span class="Stile9" style="display: block; font-size: 14px;">{pickup_date}</span></p>
                 <p style="margin: 0px 0 5px; padding: 0;"><span class="Stile12" style="display: block; font-size: 14px; font-weight: bold;  float:left;"><?php echo JText::_('VRLIBTTIMEINI'); ?>:</span>
                <span class="Stile9" style="display: block; font-size: 14px;">{tpickuptime}</span></p>
                <p style="margin: 0px 0 5px; padding: 0;"><span class="Stile12" style="display: block; font-size: 14px; font-weight: bold; float:left;"><?php echo JText::_('VRRITIROCAR'); ?>: </span>
                <span class="Stile9" style="display: block; font-size: 14px;">{pickup_location}</span></p>
            </div>
            <div class="hiredate" style="/*float: left;*/ border-radius: 4px; -moz-border-radius: 4px; -webkit-border-radius: 4px; background: #f6f6f6; margin: 10px 0 0 0; padding: 10px; border: 1px solid #c9e9fc;">
              
               
               {fecha_entrega}

            
                <p style="margin: 0px 0 5px; padding: 0;"><span class="Stile12" style="display: block; font-size: 14px; font-weight: bold;  float:left;"><?php echo JText::_('VRRETURNCARORD'); ?>:</span>
                <span class="Stile9" style="display: block; font-size: 14px;">{dropoff_location}</span></p>
            </div>
        </div>
        <div class="hireorderdetail">
            <p><span class="Stile1" style="font-size: 18px; font-weight: bold;"><?php echo JText::_('VRCORDERDETAILS'); ?>:</span></p>
            {order_details}
            <div class="hireordata hiretotal" style="color: #144D5C; border-top-color: #ddd; border-top-style: solid; border-top-width: 1px; margin: 60px 0 0; padding: 10px 0 0;">
<span class="Stile10" style="font-size: 18px; font-weight: bold;"><?php echo JText :: _('VRLIBSIX'); ?></span><div align="right" style="font-size: 14px; float: right;"><strong>{order_total}</strong></div>
</div>
        </div>
        <br><p><br><span class="smalltext" style="font-size: 12px; word-wrap: break-word;">
        <strong><?php echo JText :: _('VRLIBTENTHREE'); ?>:</strong><br>
        {order_link}
        </span><br></p>
        <span class="smalltext" style="font-size: 12px;">{footer_emailtext}</span>
    </div>
</div>