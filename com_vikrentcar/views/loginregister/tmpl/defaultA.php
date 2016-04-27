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
error_reporting(0);

$priceid       = $this->priceid;
$place         = $this->place;
$returnplace   = $this->returnplace;
$carid         = $this->carid;
$days          = $this->days;
$pickup        = $this->pickup;
$release       = $this->release;
$copts         = $this->copts;
$format        = $this->format;

$pattr         =$this->pattr;
$city_ini      =$this->city_ini;
$add_ini       =$this->add_ini;
$city_fin      =$this->city_fin;
$add_fin       =$this->add_fin;

$nameItemsCart =$this->nameItemsCart;
$itemsPrice    =$this->itemsPrice;
$itemsQty      =$this->itemsQty;
$idcars        =$this->idcars;
$idtars        =$this->idtars;
$hourly        =$this->hourly;
$idOrders      =$this->idOrders;

$action        = 'index.php?option=com_user&amp;task=login';
$validate      = JUtility :: getToken();
$pitemid       = JRequest :: getString('Itemid', '', 'request');
$urlNatural= JURI :: root() . "index.php?option=com_fabrik&view=form&formid=12&Itemid=513&lang=es" ;
$urlClienteE= JURI :: root() . "index.php?option=com_fabrik&view=form&formid=16&Itemid=522&lang=es" ;
//if (!empty($nameItemsCart)) {
//
if (!empty($carid) && !empty($pickup) && !empty($release)) {
	$chosenopts = "";
	if(is_array($copts) && @count($copts) > 0) {
		foreach($copts as $idopt => $quanopt) {
			$chosenopts .= "&optid".$idopt."=".$quanopt;
		}
	}
	



		/*if(empty($format)){
		$goto = "index.php?option=com_vikrentcar&task=ordencarrito&itemsCart=".$nameItemsCart."&itemsPrice=".$itemsPrice."&itemsQty=".$itemsQty."&idcars=".$idcars."&tars=".$idtars."&hourly=".$hourly."&idOrders=".$idOrders);
		}else{
		$goto = "index.php?option=com_vikrentcar&task=ordencarrito&format=".$format."&itemsCart=".$nameItemsCart."&itemsPrice=".$itemsPrice."&itemsQty=".$itemsQty."&idcars=".$idcars."&tars=".$idtars."&hourly=".$hourly."&idOrders=".$idOrders);
		}*/

		if(empty($format)){
		$goto = "index.php?option=com_vikrentcar&task=oconfirm&priceid=".$priceid."&place=".$place."&returnplace=".$returnplace."&carid=".$carid."&days=".$days."&pickup=".$pickup."&release=".$release.(!empty($chosenopts) ? $chosenopts : "").(!empty($pitemid) ? "&Itemid=".$pitemid : "");
		}else{
		$goto = "index.php?option=com_vikrentcar&task=oconfirm&format=".$format."&priceid=".$priceid."&place=".$place."&returnplace=".$returnplace."&carid=".$carid."&days=".$days."&pickup=".$pickup."&release=".$release.(!empty($chosenopts) ? $chosenopts : "").(!empty($pitemid) ? "&Itemid=".$pitemid : "");	
		}


	
	
	$goto = JRoute::_($goto, false);


} else {
	// The Joomla! home page
	$goto = JURI::root();

	/*

		$menu = & JSite :: getMenu();
		$default = $menu->getDefault();
		
		$uri = JFactory :: getURI($default->link . '&Itemid=' . $default->id);
		$goto = $uri->toString(array (
			'path',
			'query',
			'fragment'
		));

	*/

	
}
$return_url = base64_encode($goto);

?>

<script language="JavaScript" type="text/javascript">
function checkVrcReg() {
	var vrvar = document.vrcreg;
	if(!vrvar.name.value.match(/\S/)) {
		document.getElementById('vrcfname').style.color='#ff0000';
		return false;
	}else {
		document.getElementById('vrcfname').style.color='';
	}
	if(!vrvar.lname.value.match(/\S/)) {
		document.getElementById('vrcflname').style.color='#ff0000';
		return false;
	}else {
		document.getElementById('vrcflname').style.color='';
	}
	if(!vrvar.email.value.match(/\S/)) {
		document.getElementById('vrcfemail').style.color='#ff0000';
		return false;
	}else {
		document.getElementById('vrcfemail').style.color='';
	}
	if(!vrvar.username.value.match(/\S/)) {
		document.getElementById('vrcfusername').style.color='#ff0000';
		return false;
	}else {
		document.getElementById('vrcfusername').style.color='';
	}
	if(!vrvar.password.value.match(/\S/)) {
		document.getElementById('vrcfpassword').style.color='#ff0000';
		return false;
	}else {
		document.getElementById('vrcfpassword').style.color='';
	}
	if(!vrvar.confpassword.value.match(/\S/)) {
		document.getElementById('vrcfconfpassword').style.color='#ff0000';
		return false;
	}else {
		document.getElementById('vrcfconfpassword').style.color='';
	}
	return true;
}

function myFunction1(){

document.location.href="<?php echo $urlClienteE ?>";

}

function myFunction2(){

document.location.href="<?php echo $urlNatural ?>";
	
}

</script>

<div class="loginregistercont">
		
	<div class="registerblock">
	<form action="<?php echo JRoute::_('index.php?option=com_vikrentcar'); ?>" method="post" name="vrcreg" onsubmit="return checkVrcReg();">
	<h3><?php echo JText::_('VRREGSIGNUP'); ?></h3>
	<h4> Si usted esta registrado inicie la sesión, de lo contrario regítrese aquí</h4>
	<table valign="top" style="display:none">
		<tr><td align="right"><span id="vrcfname"><?php echo JText::_('VRREGNAME'); ?></span></td><td><input type="text" name="name" value="" size="20" class="vrcinput"/></td></tr>
		<tr><td align="right"><span id="vrcflname"><?php echo JText::_('VRREGLNAME'); ?></span></td><td><input type="text" name="lname" value="" size="20" class="vrcinput"/></td></tr>
		<tr><td align="right"><span id="vrcfemail"><?php echo JText::_('VRREGEMAIL'); ?></span></td><td><input type="text" name="email" value="" size="20" class="vrcinput"/></td></tr>
		<tr><td align="right"><span id="vrcfusername"><?php echo JText::_('VRREGUNAME'); ?></span></td><td><input type="text" name="username" value="" size="20" class="vrcinput"/></td></tr>
		<tr><td align="right"><span id="vrcfpassword"><?php echo JText::_('VRREGPWD'); ?></span></td><td><input type="password" name="password" value="" size="20" class="vrcinput"/></td></tr>
		<tr><td align="right"><span id="vrcfconfpassword"><?php echo JText::_('VRREGCONFIRMPWD'); ?></span></td><td><input type="password" name="confpassword" value="" size="20" class="vrcinput"/></td></tr>
		<tr><td align="right">&nbsp;</td><td><input type="submit" value="<?php echo JText::_('VRREGSIGNUPBTN'); ?>" class="booknow" name="submit" style="display:none" /></td></tr>
		

	</table>
	<table>
	<tr><td align="right">&nbsp;</td><td><input type="button" value="Cliente Corporativo" class="booknow" id="ClienteE" onclick="myFunction1()" /></td></tr>
	<tr><td align="right">&nbsp;</td><td><input type="button" value="Persona Natural" class="booknow" id="Persona" onclick="myFunction2()"/></td></tr>
	</table>
	<input type ="hidden" name="priceid" value="<?php echo $priceid; ?>" />
	<input type ="hidden" name="place" value="<?php echo $place; ?>" />
	<input type ="hidden" name="returnplace" value="<?php echo $returnplace; ?>" />
	<input type ="hidden" name="carid" value="<?php echo $carid; ?>" />
	<input type ="hidden" name="days" value="<?php echo $days; ?>" />
	<input type ="hidden" name="pickup" value="<?php echo $pickup; ?>" />
	<input type ="hidden" name="release" value="<?php echo $release; ?>" />
	
	<input type ="hidden" name="pattr" value="<?php echo $pattr; ?>"/>
	<input type ="hidden" name="city_ini" value="<?php echo $city_ini; ?>"/>
	<input type ="hidden" name="add_ini" value="<?php echo $add_ini; ?>"/>
	<input type ="hidden" name="city_fin" value="<?php echo $city_fin; ?>"/>
	<input type ="hidden" name="add_fin" value="<?php echo $add_fin; ?>"/>
	<?php
	if(is_array($copts) && @count($copts) > 0) {
		foreach($copts as $idopt => $quanopt) {
			?>
	<input type="hidden" name="optid<?php echo $idopt; ?>" value="<?php echo $quanopt; ?>" />
			<?php
		}
	}
	?>
	<input type ="hidden" name="Itemid" value="<?php echo $pitemid; ?>" />
	<input type ="hidden" name="option" value="com_vikrentcar" />
	<input type ="hidden" name="task" value="register" />
	</form>
	</div>
<?php
jimport('joomla.version');
$version = new JVersion();
$jv=$version->getShortVersion();
if(version_compare($jv, '1.6.0') < 0) {
	//Joomla 1.5
?>
	<div class="loginblock">
	<form action="<?php echo $action; ?>" method="post">
	<h3><?php echo JText::_('VRREGSIGNIN'); ?></h3>
	<table valign="top">
		<tr><td align="right"><?php echo JText::_('VRREGUNAME'); ?></td><td><input type="text" name="username" value="" size="20" class="vrcinput"/></td></tr>
		<tr><td >&nbsp;</td><td>&nbsp;</td></tr>
		
		<tr><td align="right"><?php echo JText::_('VRREGPWD'); ?></td><td><input type="password" name="passwd" value="" size="20" class="vrcinput"/></td></tr>
		<tr align="center"><div id='processbar'></div></tr>
		<tr><td align="right">&nbsp;</td><td><input type="submit" value="<?php echo JText::_('VRREGSIGNINBTN'); ?>" class="booknow" name="Login" /></td></tr>
		
	</table>
	<input type ="hidden" name="remember" id="remember" value="yes" />
	<input type ="hidden" value="login" name="op2" />
	<input type ="hidden" name="return" value="<?php echo $return_url; ?>" />
	<input type ="hidden" name="<?php echo $validate; ?>" value="1" />
	</form>
	</div>
<?php
}else {
	//joomla 2.5
?>
	<div class="loginblock">
	<form action="index.php?option=com_users" method="post">
	<h3><?php echo JText::_('VRREGSIGNIN'); ?></h3>
	<table valign="top">
		<tr><td align ="right"><?php echo JText::_('VRREGUNAME'); ?></td><td><input type="text" name="username" value="" size="20" class="vrcinput"/></td></tr>
		<tr><td >&nbsp;</td><td>&nbsp;</td></tr>
		

		<tr><td align ="right"><?php echo JText::_('VRREGPWD'); ?></td><td><input type="password" name="password" value="" size="20" class="vrcinput"/></td></tr>
		<tr align="center"><div id='processbar'></div></tr>
		<tr><td align ="right">&nbsp;</td><td><input type="submit" value="<?php echo JText::_('VRREGSIGNINBTN'); ?>" class="booknow" name="Login" /></td></tr>
		

	</table>
	<input type ="hidden" name="remember" id="remember" value="yes" />
	<input type ="hidden" name="return" value="<?php echo $return_url; ?>" />
	<input type ="hidden" name="<?php echo $validate; ?>" value="1" />
	<input type ="hidden" name="option" value="com_users" />
	<input type ="hidden" name="task" value="user.login" />
	</form>
	</div>
<?php
}
?>
		
</div>
