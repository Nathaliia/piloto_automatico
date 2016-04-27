<?php
/*
 * --------------------------------------------------------------------------------
   Weblogicx India  - K2 Store v 2.4
 * --------------------------------------------------------------------------------
 * @package		Joomla! 1.5x
 * @subpackage	K2 Store
 * @author    	Weblogicx India http://www.weblogicxindia.com
 * @copyright	Copyright (c) 2010 - 2015 Weblogicx India Ltd. All rights reserved.
 * @license		GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 * @link		http://weblogicxindia.com
 * --------------------------------------------------------------------------------
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

$action = JRoute::_('index.php?option=com_k2store&view=checkout&Itemid='.$this->params->get('itemid'));

if ($this->address) {
$row = $this->address;
}
?>

<script language="javascript" type="text/javascript">
<!--
function myValidate(f) {
   if (document.formvalidator.isValid(f)) {
     // f.check.value='<?php echo JUtility::getToken(); ?>'; //send token
      return true; 
   }
   else {
      var msg = 'Please fill all required fields.';
      alert(msg);
   }
   return false;
}
-->
</script>

		<div class="k2storeOrderSummary">
			<?php echo @$this->orderSummary; ?>
		</div>
		
		<div class="shipping_address_form" >
			<h3><?php echo JText::_('Billing Address'); ?></h3>
			<form action="<?php echo $action; ?>" method="post" class="adminform" class="form-validate" name="adminForm" enctype="multipart/form-data" onSubmit="return myValidate(this);" >			
			<table>
			    <tbody>
			    
			    <tr>
			        <th style="width: 100px; text-align: right;" class="key">
			             <?php echo JText::_( 'First name' ); ?> *
			        </th>
			        <td>
			            <input name="first_name" id="first_name" 
			            type="text" size="35" maxlength="250"
			            class="required"
			            value="<?php echo @$row->first_name; ?>" />
			        </td>
			    </tr>
			    <tr>
			        <th style="width: 100px; text-align: right;" class="key">
			             <?php echo JText::_( 'Last name' ); ?> *
			        </th>
			        <td>
			           <input type="text" name="last_name"
			           class="required"
			            id="last_name" size="45" maxlength="250"
			            value="<?php echo @$row->last_name; ?>" />
			        </td>
			    </tr>
			    <tr>
			        <th style="width: 100px; text-align: right;" class="key">
			              <?php echo JText::_( 'Address Line 1' ); ?> *
			        </th>
			        <td>
			            <input type="text" name="address_1"
			            class="required"
			            id="address_1" size="48" maxlength="250" 
			            value="<?php echo @$row->address_1; ?>" />
			        </td>
			    </tr>
			    <tr>
			        <th style="width: 100px; text-align: right;" class="key">
			              <?php echo JText::_( 'Address Line 2' ); ?>
			        </th>
			        <td>
			            <input type="text" name="address_2"
			            id="address_2" size="48" maxlength="250" 
			            value="<?php echo @$row->address_2; ?>" />
			        </td>
			    </tr>
				<tr>
					<th style="width: 100px; text-align: right;" class="key">
			            <?php echo JText::_( 'City' ); ?> *
					</th>
					<td>
						<input type="text" name="city" id="city"
						class="required"
						size="48" maxlength="250" 
						value="<?php echo @$row->city; ?>" />
					</td>
				</tr>
				
				<tr>
					<th style="width: 100px; text-align: right;" class="key">
			            <?php echo JText::_( 'Zip' ); ?> *
					</th>
					<td>
						<input type="text" name="zip" id="zip"
						class="required"
						size="48" maxlength="250" 
						value="<?php echo @$row->zip; ?>" />
					</td>
				</tr>
				
				<tr>
					<th style="width: 100px; text-align: right;" class="key">
			            <?php echo JText::_( 'State' ); ?>
					</th>
					<td>
						<input type="text" name="state" id="state"
						size="48" maxlength="250" 
						value="<?php echo @$row->state; ?>" />
					</td>
				</tr>
				
				<tr>
					<th style="width: 100px; text-align: right;" class="key">
			            <?php echo JText::_( 'Country' ); ?> *
					</th>
					<td>
						<input type="text" name="country" id="country"
						class="required"
						size="48" maxlength="250" 
						value="<?php echo @$row->country; ?>" />
					</td>
				</tr>
				
				<tr>
					<th style="width: 100px; text-align: right;" class="key">
			            <?php echo JText::_( 'Phone' ); ?> *
					</th>
					<td>
						<input type="text" name="phone_1" id="phone_1"
						class="required"
						size="25" maxlength="250" 
						value="<?php echo @$row->phone_1; ?>" />
					</td>
				</tr>
				
				<tr>
					<th style="width: 100px; text-align: right;" class="key">
			            <?php echo JText::_( 'Mobile' ); ?>
					</th>
					<td>
						<input type="text" name="phone_2" id="phone_2"
						size="25" maxlength="250"
						value="<?php echo @$row->phone_2; ?>" />
					</td>
				</tr>
				
				<tr>
					<th style="width: 100px; text-align: right;" class="key">
						<?php echo JText::_( 'Fax' ); ?>
					</th>
					<td>
						<input type="text" name="fax" id="fax" 
						size="25" maxlength="250" 
						value="<?php echo @$row->fax; ?>" />
					</td>
				</tr>
				
				</tbody>
			</table>
			<div style="float: right;">
				<input type="hidden" name="id" value="<?php echo @$row->id; ?>" />
				<input type="hidden" name="option" value="com_k2store" />
				<input type="hidden" name="controller" value="checkout" />
				<input type="hidden" name="task" value="save" />
				<input type="hidden" name="savetype" value="address" />				
				<input type="submit" value="Proceed to Payment" />
				
				<?php echo JHTML::_( 'form.token' ); ?>
			</div>
			
			</form>
		</div>
		
