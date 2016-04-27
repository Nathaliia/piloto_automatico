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

//produce an instant captcha
$session = JFactory::getSession();
$session->set('n1', rand(1,20));
$session->set('n2', rand(1,20));
$n_total = $session->get('n1') + $session->get('n2');
$session->set('expect', $n_total);

//load if there a guest address already in the session
if($session->get('guestaddress'))
$row = $session->get('guestaddress');

?>

<script language="javascript" type="text/javascript">
<!--

function myValidate(f) {
   if (document.formvalidator.isValid(f)) {
     // f.check.value='<?php echo JUtility::getToken(); ?>'; //send token
      return true; 
   }
   else {
      var msg = 'Some values are not acceptable.  Please retry.';
 
      //Example on how to test specific fields
      if($('email_address').hasClass('invalid')){msg += '\n\n\t* Invalid E-Mail Address';}
      
      if($('captcha').value != <?php echo $n_total; ?>){msg += '\n\n\t* Captcha wrong';}
 
      alert(msg);
   }
   return false;
}


function submitbutton(pressbutton) {
	var form = document.adminForm;
	if (pressbutton != 'cancel') {
		form.submit;
	}
	
}
-->
</script>

<div class='componentheading'>
    <span><?php echo JText::_( "Checkout as guest" ); ?></span>
</div>
		<div class="k2storeOrderSummary">
			<?php echo @$this->orderSummary; ?>
		</div>
		
		<div class="shipping_address_form" >
			<form action="<?php echo $action; ?>" method="post" class="adminform form-validate" name="adminForm" enctype="multipart/form-data" onSubmit="return myValidate(this);" >			
			<table>
			    <tbody>
			    
			    <tr>
			        <th style="width: 100px; text-align: right;" class="key">
			             <label for="first_name"> <?php echo JText::_( 'First name' ); ?> *</label>
			        </th>
			        <td>
			            <input name="first_name" id="first_name"
			            class="required"
			            type="text" size="35" maxlength="250"
			            value="<?php echo @$row['first_name']; ?>" />
			        </td>
			    </tr>
			    <tr>
			        <th style="width: 100px; text-align: right;" class="key">
			             <label for="last_name"> <?php echo JText::_( 'Last name' ); ?> *</label>
			        </th>
			        <td>
			           <input type="text" name="last_name"
			           class="required"
			            id="last_name" size="45" maxlength="250"
			            value="<?php echo @$row['last_name']; ?>" />
			        </td>
			    </tr>
			    
			     <tr>
			        <th style="width: 100px; text-align: right;" class="key">
			          <label for="email_address">   <?php echo JText::_( 'Email' ); ?> *</label>
			        </th>
			        <td>
			           <input type="text" name="email_address"
			           class="required validate-email"
			            id="email_address" size="45" maxlength="250"
			            value="<?php echo @$row['email_address']; ?>" />
			        </td>
			    </tr>
			    
			    <tr>
			        <th style="width: 100px; text-align: right;" class="key">
			          <label for="address_1">   <?php echo JText::_( 'Address Line 1' ); ?> *</label>
			        </th>
			        <td>
			            <input type="text" name="address_1"
			            class="required"
			            id="address_1" size="48" maxlength="250" 
			            value="<?php echo @$row['address_1']; ?>" />
			        </td>
			    </tr>
			    <tr>
			        <th style="width: 100px; text-align: right;" class="key">
			          <label for="address_2">  <?php echo JText::_( 'Address Line 2' ); ?> </label>
			        </th>
			        <td>
			            <input type="text" name="address_2"
			            id="address_2" size="48" maxlength="250" 
			            value="<?php echo @$row['address_2']; ?>" />
			        </td>
			    </tr>
				<tr>
					<th style="width: 100px; text-align: right;" class="key">
			           <label for="city"> <?php echo JText::_( 'City' ); ?> *</label>
					</th>
					<td>
						<input type="text" name="city" id="city"
						class="required"
						size="48" maxlength="250" 
						value="<?php echo @$row['city']; ?>" />
					</td>
				</tr>
				
				<tr>
					<th style="width: 100px; text-align: right;" class="key">
			         <label for="zip">   <?php echo JText::_( 'Zip' ); ?> *</label>
					</th>
					<td>
						<input type="text" name="zip" id="zip"
						class="required"
						size="48" maxlength="250" 
						value="<?php echo @$row['zip']; ?>" />
					</td>
				</tr>
				
				<tr>
					<th style="width: 100px; text-align: right;" class="key">
			       <label for="state"> <?php echo JText::_( 'State' ); ?></label>
					</th>
					<td>
						<input type="text" name="state" id="state"
						size="48" maxlength="250" 
						value="<?php echo @$row['state']; ?>" />
					</td>
				</tr>
				
				<tr>
					<th style="width: 100px; text-align: right;" class="key">
					<label for="country">     <?php echo JText::_( 'Country' ); ?> *</label>
					</th>
					<td>
						<input type="text" name="country" id="country"
						class="required"
						size="48" maxlength="250" 
						value="<?php echo @$row['country']; ?>" />
					</td>
				</tr>
				
				<tr>
					<th style="width: 100px; text-align: right;" class="key">
			         <label for="phone_1">  <?php echo JText::_( 'Phone' ); ?> *</label>
					</th>
					<td>
						<input type="text" name="phone_1" id="phone_1"
						class="required"
						size="25" maxlength="250" 
						value="<?php echo @$row['phone_1']; ?>" />
					</td>
				</tr>
				
				<tr>
					<th style="width: 100px; text-align: right;" class="key">
			         <label for="phone_2">   <?php echo JText::_( 'Mobile' ); ?></label>
					</th>
					<td>
						<input type="text" name="phone_2" id="phone_2"
						size="25" maxlength="250"
						value="<?php echo @$row['phone_2']; ?>" />
					</td>
				</tr>
				
				<tr>
					<th style="width: 100px; text-align: right;" class="key">
						<label for="fax"> <?php echo JText::_( 'Fax' ); ?></label>
					</th>
					<td>
						<input type="text" name="fax" id="fax" 
						size="25" maxlength="250" 
						value="<?php echo @$row['fax']; ?>" />
					</td>
				</tr>
				
				 <tr>
					<th style="width: 100px; text-align: right;" class="key"><label for="captcha"><?php echo JText::_('Captcha'); ?> </label>&nbsp;&nbsp;<?php echo $session->get('n1'); ?> + <?php echo $session->get('n2'); ?> =</th>
					<td><input type="text" class="validate-numeric required" size="10" name="captcha" id="captcha" /></td>
				</tr>
				
				</tbody>
			</table>
			<div style="float: right;">
				<input type="hidden" name="id" value="<?php echo @$row->id; ?>" />
				<input type="hidden" name="option" value="com_k2store" />
				<input type="hidden" name="controller" value="checkout" />
				<input type="hidden" name="task" value="guestsave" />
				<input type="hidden" name="guest" value="1" />
				<input type="hidden" name="savetype" value="guest_address" />				
				<input type="submit" value="Proceed to Payment" />
				
				<?php echo JHTML::_( 'form.token' ); ?>
			</div>
			
			</form>
		</div>
		
