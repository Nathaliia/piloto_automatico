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

jimport('joomla.application.component.controller');

class VikrentcarController extends JController {
	function display() {
		$view=JRequest :: getVar('view', '');


		//$uri =& JFactory::getURI();
  		//$url = $uri->toString(array('scheme', 'user', 'pass', 'host', 'port'));



		if($view == 'carslist') {
			JRequest :: setVar('view', 'carslist');
		}elseif($view == 'cardetails') {
			JRequest :: setVar('view', 'cardetails');
		}elseif($view == 'loginregister') {
			JRequest :: setVar('view', 'loginregister');
		}elseif($view == 'locationsmap') {
			JRequest :: setVar('view', 'locationsmap');
		}elseif($view == 'locationslist') {
			JRequest :: setVar('view', 'locationslist');
		}else {

			JRequest :: setVar('format', 'raw');

			JFactory::$document = null;
    		JFactory::getDocument();

			JRequest :: setVar('view', 'vikrentcar');
		}
		parent :: display();
	}

	function search() {
		JRequest :: setVar('view', 'search');
		//
		parent :: display();
	}

	function showprc() {
		JRequest :: setVar('view', 'showprc');
		parent :: display();
	}

	function oconfirm() {
		//$data = JRequest :: getString('ItemsNames', '', 'request');
		$requirelogin = vikrentcar::requireLogin();
		if($requirelogin) {
			if(vikrentcar::userIsLogged()) {

				JRequest :: setVar('view', 'oconfirm');
			}else {
				JRequest :: setVar('view', 'loginregister');
			}
		}else {
			JRequest :: setVar('view', 'oconfirm');
		}
		parent :: display();
	}

	function ordencarrito() {

		$requirelogin = vikrentcar::requireLogin();
		if($requirelogin) {
			if(vikrentcar::userIsLogged()) {

				JRequest :: setVar('view', 'ordencarrito');

			}else {
				JRequest :: setVar('view', 'loginregister');

			}
		}else {
			JRequest :: setVar('view', 'ordencarrito');
		}
		parent :: display();
	}

	function enviarcorreo(){
		$user =& JFactory::getUser();
		$idUser = $user->get( 'id' );
		$subject='registro';
		$body='Registro exitoso';

		//vikrentcar::enviarEmailAcymailing($idUser,'',$subject,$body);

	}

	function servicios(){

		$doc =& JFactory::getDocument();



		$service= JRequest :: getString('service', '', 'request');
		echo 'prueba '.$service;
		switch ($service) {

		    case '6':
		        //$content='jQuery.noConflict(); jQuery(document).ready(function() {debugger; jQuery( "#servicios option:selected").val(\'4\'); jQuery( "#servicios option:selected").change();}';
		        //$content=' jQuery(document).ready(function() {alert("hola");});';

		        break;
		     case '2':

		        break;
		     case '3':

		        break;
		}

		//$doc->addScriptDeclaration( $content );

	}

	function sincronizar_users(){

		require_once JPATH_ADMINISTRATOR.'/components/com_profiler/controllers/user.php';
		$app		= JFactory::getApplication();
		//ProfilerControllerUser::synchronize();
		//
		$db = &JFactory::getDBO();
		$query = "SELECT u.id, u.name, u.email FROM  #__users  AS u WHERE NOT EXISTS (SELECT pu.userid FROM #__profiler_users AS pu WHERE pu.userid =u.id)";
		//$query = "SELECT u.id, u.name FROM #__users AS u WHERE NOT EXISTS (SELECT pu.id FROM #__profiler_users AS pu WHERE pu.id=u.id)";
		$db->setQuery($query);
		$users = $db->loadAssocList();
		foreach ($users as $user) {

			$query= "SELECT * FROM #__vikrentcar_profiles WHERE user_id='".$user['id']."'";
			$db->setQuery($query);
			$db->query();
			$profiles = $db->loadAssocList();

			$xx= $profiles[0]['lname'];


			$query = "INSERT INTO #__profiler_users (userid , name, firstname, lastname ,email) VALUES ('".$user['id']."','".$profiles[0]['name'].' '.$profiles[0]['lname']."', '".$profiles[0]['name']."', '".$profiles[0]['lname']."', '".$user['email']."');";
			$db->setQuery($query);
			$db->query();



			$q="UPDATE #__profiler_users SET pro_city='".$profiles[0]['city']."', pro_address='".$profiles[0]['address']."', pro_movil='".$profiles[0]['movil']."', pro_phone='".$profiles[0]['phone']."', pro_doc_type='".$profiles[0]['doc_type']."', pro_num_doc='".$profiles[0]['num_doc']."', pro_convenio='".$profiles[0]['convenio']."' WHERE userid='".$user['id']."'";
			$db->setQuery($q);
			$db->query();
			$app->enqueueMessage('Synchronize user '.$user['id'].' '.$user['username']);
		}

		echo 'Sincronizacion completada';
	}

	/*function enviarSms($idUser, $listid, $msgsms){

		$msgsms=vikrentcar::getInfoOrderSms('321');

		vikrentcar::enviarSms(621,LISTA_NOTIFICACION_RESERVAS_SMS,urlencode($msgsms));
		//vikrentcar::EstaInscritoLista(642,LISTA_NOTIFICACION_CANCELACIONES_SMS);
		//vikrentcar::EstaInscritoLista(642,LISTA_NOTIFICACION_CANCELACIONES_SMS);
		//vikrentcar::getInfoOrderSms	('1');
	}
	*/
	function cancelarservicio(){


		$app =& JFactory::getApplication();

		$task	 = JRequest :: getString('task', '', 'request');
		//$idorder	 = JRequest :: getString('chun4_vikrentcar_canceledorders___id_order', '', 'request');
		$idorder	 = JRequest :: getString('idorder', '', 'request');
		$user =& JFactory::getUser();
		$userId = $user->get( 'id' );
		$dbo = & JFactory :: getDBO();

		$idPiloto	 = JRequest :: getString('idCond', '', 'request');
		$tabla='orders';
		$idcarnew=NULL;
		$status='canceled';


		$lang = JFactory::getLanguage();

		$code_lang= $lang->getTag();



		try {

			$q = "SELECT  * FROM `#__vikrentcar_".$tabla."`  WHERE `id`='" . $idorder . "';";
			$dbo->setQuery($q);
			$dbo->Query($q);
			$orderinfo = $dbo->loadAssocList();
			//si la orden ya esta cancelada
			if($orderinfo[0]['status']=='canceled'){

			$app->enqueueMessage(JText::_('MSGCANCELSERVICEALREADY'));

			}else{

				if($orderinfo[0]['status']=='standby'){

					$changeSaldo=false;

				}else{

					$changeSaldo=true;

				}


			$q = "SELECT  * FROM `#__vikrentcar_cars`  WHERE `id`='" . $orderinfo[0]['idcar'] . "';";
			$dbo->setQuery($q);
			$dbo->Query($q);
			$carinfo = $dbo->loadAssocList();

			$q = "SELECT  `num_doc` FROM `#__vikrentcar_profiles`  WHERE `user_id`='" . $userId . "';";
			$dbo->setQuery($q);
			$dbo->Query($q);
			$profileinfo = $dbo->loadAssocList();
			//solo el conductor propietario del carro puede cancelar servicio
				if($carinfo[0]['idCond']==$profileinfo[0]['user_id']){

					$q = "UPDATE  `#__vikrentcar_".$tabla."` SET status='".$status."' WHERE `id`='" . $idorder . "';";

					$dbo->setQuery($q);
					$dbo->Query($q);
				}

				$q = "SELECT  * FROM `#__vikrentcar_".$tabla."`  WHERE `id`='" . $idorder . "';";
				$dbo->setQuery($q);
				$dbo->Query($q);
				$orderinfo = $dbo->loadAssocList();

				if($orderinfo[0]['status']=='canceled'){

					//$app->enqueueMessage(JText::_('MSGORDER').": ".$orderinfo[0]['id']." ".JText::_('MSGCANCELSERVICE')." ".JText::_('MSGBYSERVICE')." ".JText::_('MSGBYDRIVER')." ".$profileinfo[0]['num_doc']);

					$ftitle = vikrentcar :: getFrontTitle($orderinfo[0]['ts']);
					$tarInfo = vikrentcar :: getTarInfo($orderinfo[0]['idtar'],$orderinfo[0]['hourly']);
					$optstr= vikrentcar::getOptInfo($orderinfo[0]['optionals'],$orderinfo[0]['days'], $tarInfo[0]['cost'] );
					$pricestr = vikrentcar :: getPriceName($tarInfo[0]['idprice']) . ": " . vikrentcar :: sayCostPlusIva($tarInfo[0]['cost'], $tarInfo[0]['idprice']) . (!empty ($tarInfo[0]['attrdata']) ? " " . $currencyname . "\n" . vikrentcar :: getPriceAttr($tarInfo[0]['idprice']) . ": " . $tarInfo[0]['attrdata'] : "");
					$carinfo = vikrentcar :: getCarInfo($orderinfo[0]['idcar']);
					$hmess= vikrentcar::crearEmailCancelacion( '1','', $ftitle, $orderinfo[0]['ts'],$orderinfo[0]['custdata'], $carinfo['name'], $orderinfo[0]['ritiro'], $orderinfo[0]['consegna'], $pricestr, $optstr, $orderinfo[0]['totpaid'], $link,$orderinfo[0]['status'], $place = "", $returnplace = "", $maillocfee = "", $orderinfo[0]['id'], $strcouponeff = "");
					$subject= JText::sprintf('VRLIBNINECANCELSUBJECT',$orderinfo[0]['id']);
					$body= $hmess;


					vikrentcar::enviarEmailAcymailing($orderinfo[0]['ujid'],LISTA_NOTIFICACION_CANCELACIONES,$subject,$body);


					/*$q="SELECT `user_id` FROM `#__vikrentcar_profiles` WHERE `num_doc`='".$carinfo['idCond']."'";
					$dbo->setQuery($q);
					$dbo->Query($q);
					$juserPiloto = $dbo->loadAssocList();*/

					//se envia email al piloto confirmando la cancelacion de servicio
					vikrentcar::enviarEmailAcymailing($carinfo['idCond'],'',$subject,$body);
					//
					if($code_lang=='es-ES'){

						$idList	='20';


						$app->redirect(JURI::root().'index.php?option=com_fabrik&view=list&listid='.$idList.'&Itemid=544&dataOrder');
					    $app->enqueueMessage(JText::_('Orden cancelada con exito'));


					}else{


						$idList	='20';

						$app->redirect(JURI::root().'index.php?option=com_fabrik&view=list&listid='.$idList.'&Itemid=544&dataOrder');
					    $app->enqueueMessage(JText::_('Orden cancelada con exito'));


					}

					if($changeSaldo){
					$concepto='Cancelacion Sevicio';


					$lid= vikrentcar::saveCredito($idorder,$userId, $concepto);


					$newSaldo = vikrentcar::calcularNuevoSaldo($idorder, $userId, $lid);

					vikrentcar::saveSaldo($newSaldo, $userId);
					}


					$saldo= vikrentcar::getSaldoUser($userId);


					$app->enqueueMessage(JText::_('MSGORDER').": ".$orderinfo[0]['id']." ".JText::_('MSGCANCELSERVICE')." ".JText::_('MSGBYSERVICE')." ".'Cliente '." ".$profileinfo[0]['num_doc'].': '. $profileinfo[0]['name'].' '. $profileinfo[0]['lname'].'<br/>'.'Usted Tiene un saldo de: '.number_format($saldo, 0).'<br/>'.'Ahora puede editar la Orden');

					$app->redirect(JURI::root().'index.php?option=com_fabrik&view=list&listid=20&Itemid=544&dataOrder');
					//$app->enqueueMessage(JText::_('Orden cancelada con exito'));



					//guardar saldo si se pago el servicio
					//




					}
					else{

					$app->enqueueMessage(JText::_('MSGCANCELSERVICENOSUCCESS'));


				}


			}




			/*$q = "SELECT  * FROM `#__vikrentcar_".$tabla."`  WHERE `id`='" . $idorder . "';";

			$dbo->setQuery($q);
			$dbo->Query($q);
			$orderinfo = $dbo->loadAssocList();*/
			//confirma si el servicio se cancelo con exito



		} catch (Exception $e) {

			$app->enqueueMessage('Error: '.$e->getMessage());


		}




	}



	function cancelarservicio2(){

		//error_reporting(-1);

	 vikrentcar::cancelarservicio();



	}

	function obtenerQuery(){

		$id = JRequest :: getString('id', '', 'request');
		$tabla= JRequest :: getString('tabla', '', 'request');
		$dbo = & JFactory :: getDBO();
		$q = "SELECT * FROM `#__vikrentcar_".$tabla."`  WHERE `id`='" . $id . "';";
		$dbo->setQuery($q);
		$dbo->Query($q);
		$consulta = $dbo->loadAssocList();

		$datos= json_encode($consulta);

		echo $datos;

	}

	function borrarItemCarrito(){

		$idOrder = JRequest :: getString('id', '', 'request');
		//se actualiza estado a cancelado para la orden que ha sido borrada en el carrito
		$dbo = & JFactory :: getDBO();
		$q = "UPDATE `#__vikrentcar_orders` SET `status`= 'canceled' WHERE `id`='" . $idOrder . "';";
		$dbo->setQuery($q);
		$dbo->Query($q);




		echo $idOrder[0]['id'].' '. JText::_('VRMSGDELITEMCARRES');


	}

	function ajax()
	{

		//captura variables enviadas via ajax
		$session = JFactory::getSession();
		$itemsCart = JRequest :: getString('ItemsNames', '', 'request');
		$itemPrice = JRequest :: getString('ItemsPrice', '', 'request');
		$itemsQty = JRequest :: getString('ItemsQty', '', 'request');
		$idcars = JRequest :: getString('Idcars', '', 'request');
		$tars = JRequest :: getString('Tars', '', 'request');
		$hourly = JRequest :: getString('Hourly', '', 'request');
		$idordes = JRequest :: getString('Orderids', '', 'request');
		$optionals = JRequest :: getString('optionals', '', 'request');






		$session->set('itemsCart', $itemsCart);
		$session->set('itemsPrice',$itemPrice);
		$session->set('itemsQty', $itemsQty);
		$session->set('idcars', $idcars);
		$session->set('tars', $tars);
		$session->set('hourly', $hourly);
		$session->set('idorders', $idordes);




		echo $itemsCart;
		exit;
	}



	function register() {
		$mainframe =& JFactory::getApplication();
		$dbo = & JFactory :: getDBO();
		//user data
		$pname = JRequest :: getString('name', '', 'request');
		$plname = JRequest :: getString('lname', '', 'request');
		$pemail = JRequest :: getString('email', '', 'request');
		$pusername = JRequest :: getString('username', '', 'request');
		$ppassword = JRequest :: getString('password', '', 'request');
		$pconfpassword = JRequest :: getString('confpassword', '', 'request');
		//
		//order data
		$ppriceid = JRequest :: getString('priceid', '', 'request');
		$pplace = JRequest :: getString('place', '', 'request');
		$preturnplace = JRequest :: getString('returnplace', '', 'request');
		$pcarid = JRequest :: getString('carid', '', 'request');
		$pdays = JRequest :: getString('days', '', 'request');
		$ppickup = JRequest :: getString('pickup', '', 'request');
		$prelease = JRequest :: getString('release', '', 'request');
		$pitemid = JRequest :: getString('Itemid', '', 'request');
		$copts = array();
		$q = "SELECT * FROM `#__vikrentcar_optionals`;";
		$dbo->setQuery($q);
		$dbo->Query($q);
		if ($dbo->getNumRows() > 0) {
			$optionals = $dbo->loadAssocList();
			foreach ($optionals as $opt) {
				$tmpvar = JRequest :: getString('optid' . $opt['id'], '', 'request');
				if (!empty ($tmpvar)) {
					$copts[$opt['id']] = $tmpvar;
				}
			}
		}
		$chosenopts = "";
		if(is_array($copts) && @count($copts) > 0) {
			foreach($copts as $idopt => $quanopt) {
				$chosenopts .= "&optid".$idopt."=".$quanopt;
			}
		}
		$qstring = "priceid=".$ppriceid."&place=".$pplace."&returnplace=".$preturnplace."&carid=".$pcarid."&days=".$pdays."&pickup=".$ppickup."&release=".$prelease.(!empty($chosenopts) ? $chosenopts : "").(!empty($pitemid) ? "&Itemid=".$pitemid : "");
		//
		if(!vikrentcar::userIsLogged()) {
			if (!empty($pname) && !empty($plname) && !empty($pusername) && validEmail($pemail) && $ppassword == $pconfpassword) {
				//save user
				$newuserid=vikrentcar::addJoomlaUser($pname." ".$plname, $pusername, $pemail, $ppassword);
				if ($newuserid!=false && strlen($newuserid)) {
					//registration success
					$credentials = array('username' => $pusername, 'password' => $ppassword );
					//autologin
					$mainframe->login($credentials);
					$currentUser = JFactory::getUser();
					$currentUser->setLastVisit(time());
					$currentUser->set('guest', 0);
					//
					if(!empty($pcarid)){
						//$mainframe->redirect(JRoute::_('index.php?option=com_vikrentcar&task=oconfirm&'.$qstring, false));
						$mainframe->redirect(JURI::root());
					}
					else{
						$mainframe->redirect(JURI::root());
						//$mainframe->redirect(JRoute::_('index.php', false));
					}

				}else {
					//error while saving new user
					JError :: raiseWarning('', JText::_('VRCREGERRSAVING'));
					$mainframe->redirect(JURI::root());
					//$mainframe->redirect(JRoute::_('index.php?option=com_vikrentcar&view=loginregister&'.$qstring, false));
				}
			}else {
				//invalid data
				JError :: raiseWarning('', JText::_('VRCREGERRINSDATA'));
				$mainframe->redirect(JURI::root());
				//$mainframe->redirect(JRoute::_('index.php?option=com_vikrentcar&view=loginregister&'.$qstring, false));
			}
		}else {
			//user is already logged in, proceed
			$mainframe->redirect(JURI::root());
			//$mainframe->redirect(JRoute::_('index.php?option=com_vikrentcar&task=oconfirm&'.$qstring, false));
		}
	}

function saveorderA() {

		$mainframe =& JFactory::getApplication();
		$dbo = & JFactory :: getDBO();

		echo 'esto es una prueba para revisar custdata';



	try{

		$q = "SELECT * FROM `#__vikrentcar_custfields` ORDER BY `#__vikrentcar_custfields`.`ordering` ASC;";
			$dbo->setQuery($q);
			$dbo->Query($q);
			$cfields = $dbo->getNumRows() > 0 ? $dbo->loadAssocList() : "";
			$suffdata = true;


			$currentUser = JFactory::getUser();
			$q = "SELECT * FROM `#__vikrentcar_profiles` WHERE user_id=".$currentUser->id.";";
			$dbo->setQuery($q);
			$dbo->Query($q);
			$cprofiles = $dbo->getNumRows() > 0 ? $dbo->loadAssocList() : $suffdata = false;;


			$useremail = "";
			if (@ is_array($cfields)) {
				foreach ($cfields as $cf) {
					if (intval($cf['required']) == 1 && $cf['type'] != 'separator') {
						if($autofillcust=='1'){

						}else{
							//esta es una excepcion para cuando el cliente es cliente corporativo
							if($cf['id']==3 && $cprofiles[0]['usertype']==1){
								//nada
							}else{
								$tmpcfval = JRequest :: getString('vrcf' . $cf['id'], '', 'request');

									if (strlen(str_replace(" ", "", trim($tmpcfval))) <= 0) {
										$suffdata = false;
										break;
									}
							}
						}
					}
				}
				//save user email and create custdata array
				$arrcustdata = array ();


				$emailwasfound = false;
				/*foreach ($cfields as $cf) {
					if (intval($cf['isemail']) == 1 && $emailwasfound == false) {
						if($autofillcust=='1'){
						$useremail= $cprofiles[0]['Email'];
						$emailwasfound = true;
						}else{
						$useremail = trim(JRequest :: getString('vrcf' . $cf['id'], '', 'request'));
						$emailwasfound = true;
						}
					}

					if($cf['type'] != 'separator') {
						//se crea arreglo de datos del cliente automaicamente
						if($autofillcust=='1'){
							if($cf['name'] == 'ORDER_NAME'){

								//$arrcustdata[JText :: _($cf['name'])]=JRequest :: getString('vrcf' . $cf['id'], '', 'request');

							}

							if($cf['name'] == 'ORDER_LNAME'){

								//$arrcustdata[JText :: _($cf['name'])]=JRequest :: getString('vrcf' . $cf['id'], '', 'request');

							}
							if($cf['name'] == 'ORDER_PHONE'){
								$arrcustdata[JText :: _($cf['name'])]=$cprofiles[0]['phone'];

							}
							if($cf['name'] == 'ORDER_ADDRESS'){
								$arrcustdata[JText :: _($cf['name'])]=$cprofiles[0]['address'];

							}
							if($cf['name'] == 'ORDER_CITY'){
								$arrcustdata[JText :: _($cf['name'])]=$cprofiles[0]['city'];

							}
						}else{
						$arrcustdata[JText :: _($cf['name'])] = JRequest :: getString('vrcf' . $cf['id'], '', 'request');
						}
					}
				}*/

				foreach ($cfields as $cf) {

					if (intval($cf['isemail']) == 1 && $emailwasfound == false) {

						$useremail = trim(JRequest :: getString('vrcf' . $cf['id'], '', 'request'));

						$emailwasfound = true;

					}

					if($cf['type'] != 'separator') {



						$arrcustdata[JText :: _($cf['name'])] = JRequest :: getString('vrcf' . $cf['id'], '', 'request');

					}

				}



			}




	}catch(Exception $e){

		    echo 'error :'. $e->getMessage();
	}


}

function saveorder() {

	//error_reporting(-1);



		$dbo = & JFactory :: getDBO();
		$pcar = JRequest :: getString('car', '', 'request');
		$pdays = JRequest :: getString('days', '', 'request');
		//vikrentcar 1.6
		$porigdays = JRequest :: getString('origdays', '', 'request');
		$pcouponcode = JRequest :: getString('couponcode', '', 'request');
		//
		$ppickup = JRequest :: getString('pickup', '', 'request');
		$prelease = JRequest :: getString('release', '', 'request');
		$pprtar = JRequest :: getString('prtar', '', 'request');
		$poptionals = JRequest :: getString('optionals', '', 'request');
		$ptotdue = JRequest :: getString('totdue', '', 'request');
		$pplace = JRequest :: getString('place', '', 'request');
		$preturnplace = JRequest :: getString('returnplace', '', 'request');
		$pgpayid = JRequest :: getString('gpayid', '', 'request');
		$ppriceid = JRequest :: getString('priceid', '', 'request');
		$phourly = JRequest :: getString('hourly', '', 'request');
		$pitemid = JRequest :: getInt('Itemid', '', 'request');

		//Nuevas Variables
		$pattr = JRequest :: getString('pattr', '', 'request');
		$city_ini = JRequest :: getString('city_ini', '', 'request');
		$add_ini = JRequest :: getString('add_ini', '', 'request');
		$city_fin = JRequest :: getString('city_fin', '', 'request');
		$add_fin = JRequest :: getString('add_fin', '', 'request');
		$add_vuelo = JRequest :: getString('add_vuelo', '', 'request');
		$numpasangers = JRequest :: getString('numpasangers', '', 'request');
		$format = JRequest :: getString('format', '', 'request');


		$isPaquete = JRequest :: getString('isPaquete', '', 'request');
		$valorPaqueteUsuario = JRequest :: getInt('valorPaqueteUsuario', '', 'request');
		$costoConCredito = JRequest :: getInt('CostCredit', '', 'request');


		$valorPaquete = JRequest :: getInt('valorPaquete', '', 'request');

		$autofillcust = JRequest :: getInt('autofillcust', '', 'request');

		$chekout = JRequest :: getString('chekout', '', 'request');

		$convenio = JRequest :: getString('convenio', '', 'request');

		$pitemid='520';






		$doc =& JFactory::getDocument();
		$app =& JFactory::getApplication();

		$saldoFavor=false;
		$salvardebidopaq=false;




		$validtoken = false;
		if (vikrentcar :: tokenForm()) {
			$pviktoken = JRequest :: getString('viktoken', '', 'request');
			session_start();
			if (!empty ($pviktoken) && $_SESSION['vikrtoken'] == $pviktoken) {
				unset ($_SESSION['vikrtoken']);
				$validtoken = true;
			}
		} else {
			$vaildtoken = true;
		}
		if (true) {
		//if ($validtoken) {
			$q = "SELECT * FROM `#__vikrentcar_custfields` ORDER BY `#__vikrentcar_custfields`.`ordering` ASC;";
			$dbo->setQuery($q);
			$dbo->Query($q);
			$cfields = $dbo->getNumRows() > 0 ? $dbo->loadAssocList() : "";
			$suffdata = true;


			$currentUser = JFactory::getUser();
			$q = "SELECT * FROM `#__vikrentcar_profiles` WHERE user_id=".$currentUser->id.";";
			$dbo->setQuery($q);
			$dbo->Query($q);
			$cprofiles = $dbo->getNumRows() > 0 ? $dbo->loadAssocList() : $suffdata = false;;
			
			$useremail = "";
			if (@ is_array($cfields)) {
				foreach ($cfields as $cf) {
					if (intval($cf['required']) == 1 && $cf['type'] != 'separator') {
						if($autofillcust=='1'){

						}else{
							//esta es una excepcion para cuando el cliente es cliente corporativo
							if($cf['id']==3 && $cprofiles[0]['usertype']==1){
								//nada
							}else{
								$tmpcfval = JRequest :: getString('vrcf' . $cf['id'], '', 'request');

									if (strlen(str_replace(" ", "", trim($tmpcfval))) <= 0) {
										$suffdata = false;
										break;
									}
							}
						}
					}
				}
				//save user email and create custdata array
				$arrcustdata = array ();


				$emailwasfound = false;
				/*foreach ($cfields as $cf) {
					if (intval($cf['isemail']) == 1 && $emailwasfound == false) {
						if($autofillcust=='1'){
						$useremail= $cprofiles[0]['Email'];
						$emailwasfound = true;
						}else{
						$useremail = trim(JRequest :: getString('vrcf' . $cf['id'], '', 'request'));
						$emailwasfound = true;
						}
					}

					if($cf['type'] != 'separator') {
						//se crea arreglo de datos del cliente automaicamente
						if($autofillcust=='1'){
							if($cf['name'] == 'ORDER_NAME'){

								//$arrcustdata[JText :: _($cf['name'])]=JRequest :: getString('vrcf' . $cf['id'], '', 'request');

							}

							if($cf['name'] == 'ORDER_LNAME'){

								//$arrcustdata[JText :: _($cf['name'])]=JRequest :: getString('vrcf' . $cf['id'], '', 'request');

							}
							if($cf['name'] == 'ORDER_PHONE'){
								$arrcustdata[JText :: _($cf['name'])]=$cprofiles[0]['phone'];

							}
							if($cf['name'] == 'ORDER_ADDRESS'){
								$arrcustdata[JText :: _($cf['name'])]=$cprofiles[0]['address'];

							}
							if($cf['name'] == 'ORDER_CITY'){
								$arrcustdata[JText :: _($cf['name'])]=$cprofiles[0]['city'];

							}
						}else{
						$arrcustdata[JText :: _($cf['name'])] = JRequest :: getString('vrcf' . $cf['id'], '', 'request');
						}
					}
				}*/

				foreach ($cfields as $cf) {

					if (intval($cf['isemail']) == 1 && $emailwasfound == false) {

						$useremail = trim(JRequest :: getString('vrcf' . $cf['id'], '', 'request'));

						$emailwasfound = true;

					}

					if($cf['type'] != 'separator') {



						$arrcustdata[JText :: _($cf['name'])] = JRequest :: getString('vrcf' . $cf['id'], '', 'request');

					}




				}



					$arrcustdata['Dirección Inicial']=$add_ini;

					$arrcustdata['Dirección Final']=$add_fin;

					$arrcustdata['Vuelo']=$add_vuelo;






				//detecta el dipo de servicio para guardar información adicional
				/*if(preg_match("/[aA-zZ]{1}[[:digit:]]{2}[-]{1}/", $add_ini)){


				$dato= split('-',$add_ini);
					//S33=Gama Alta-Traslados
					if($dato[0]=='S33'){
					//Guarda el numero del vuelo
					$arrcustdata['Vuelo']=$dato[1];
					$arrcustdata['Dirección Inicial']=$dato[2];
					}
				}else{
					//$arrcustdata['Lugar de Recogida']=$add_ini;
				}  */

			}

			if ($suffdata == true) {




				//vikrentcar 1.6 for dayValidTs()
				if(strlen($porigdays) > 0) {
					$calcdays = $pdays;
					$pdays = $porigdays;
				}else {
					$calcdays = $pdays;
				}
				//

				if (vikrentcar :: dayValidTs($pdays, $ppickup, $prelease)) {
					$currencyname = vikrentcar :: getCurrencyName();

					//Validacion de precio por atributo
					if (!empty ($pattr)){
						$q = "SELECT * FROM `#__vikrentcar_dispcost` WHERE `id`='" . $dbo->getEscaped($pprtar) . "' AND `idcar`='" . $dbo->getEscaped($pcar) . "' AND `attrdata`='" . $pattr . "';";
						$usedhourly = false;

					}else{
						$q = "SELECT * FROM `#__vikrentcar_dispcost` WHERE `id`='" . $dbo->getEscaped($pprtar) . "' AND `idcar`='" . $dbo->getEscaped($pcar) . "' AND `days`='" . $dbo->getEscaped($pdays) . "';";
						$usedhourly = false;

					}
					//}else{
						//vikrentcar 1.5
						if(intval($phourly) > 0) {
							if (empty ($pattr)){

							$q = "SELECT * FROM `#__vikrentcar_dispcosthours` WHERE `id`='" . $dbo->getEscaped($pprtar) . "' AND `idcar`='" . $dbo->getEscaped($pcar) . "' AND `hours`='" . $dbo->getEscaped($phourly) . "';";
							$usedhourly = true;
							}else{
							$q = "SELECT * FROM `#__vikrentcar_dispcosthours` WHERE `id`='" . $dbo->getEscaped($pprtar) . "' AND `idcar`='" . $dbo->getEscaped($pcar) . "' AND `attrdata`='" . $pattr . "';";
							$usedhourly = true;

							}
						}else {
							//vikrentcar 1.6 for extra hours charges
							if(strlen($porigdays) > 0) {
								if (empty ($pattr)){
								$q = "SELECT * FROM `#__vikrentcar_dispcost` WHERE `id`='" . $dbo->getEscaped($pprtar) . "' AND `idcar`='" . $dbo->getEscaped($pcar) . "' AND `days`='" . $dbo->getEscaped($calcdays) . "';";
								}else{
								$q = "SELECT * FROM `#__vikrentcar_dispcost` WHERE `id`='" . $dbo->getEscaped($pprtar) . "' AND `idcar`='" . $dbo->getEscaped($pcar) . "' AND  `attrdata`='" . $pattr . "';";

								}

							}else {
								if (empty ($pattr)){
								$q = "SELECT * FROM `#__vikrentcar_dispcost` WHERE `id`='" . $dbo->getEscaped($pprtar) . "' AND `idcar`='" . $dbo->getEscaped($pcar) . "' AND `days`='" . $dbo->getEscaped($pdays) . "';";
								}else{

								$q = "SELECT * FROM `#__vikrentcar_dispcost` WHERE `id`='" . $dbo->getEscaped($pprtar) . "' AND `idcar`='" . $dbo->getEscaped($pcar) . "' AND `attrdata`='" . $pattr . "';";

								}

							}
							//
							$usedhourly = false;
						}
					//}
					//




					$dbo->setQuery($q);
					$dbo->Query($q);





					if ($dbo->getNumRows() == 1) {
						$tar = $dbo->loadAssocList();


						//vikrentcar 1.5
						if($usedhourly || !empty($pattr)) {
							foreach($tar as $kt => $vt) {
								$tar[$kt]['days'] = 1;
							}
						}
						//
						//vikrentcar 1.6
						$secdiff = $prelease - $ppickup;
						$daysdiff = $secdiff / 86400;
						if (is_int($daysdiff)) {
							if ($daysdiff < 1) {
								$daysdiff = 1;
							}
						}else {
							if ($daysdiff < 1) {
								$daysdiff = 1;
								$ophours = $secdiff / 3600;
								$hoursdiff = intval(round($ophours));
								if($hoursdiff < 1) {
									$hoursdiff = 1;
								}
							}else {
								$sum = floor($daysdiff) * 86400;
								$newdiff = $secdiff - $sum;
								$maxhmore = vikrentcar :: getHoursMoreRb() * 3600;
								if ($maxhmore >= $newdiff) {
									$daysdiff = floor($daysdiff);
								}else {
									$daysdiff = ceil($daysdiff);
									$ehours = intval(round(($newdiff - $maxhmore) / 3600));
									$checkhourscharges = $ehours;
									if($checkhourscharges > 0) {
										$aehourschbasp = vikrentcar::applyExtraHoursChargesBasp();
									}
								}
							}
						}
						if($checkhourscharges > 0 && $aehourschbasp == true) {
							$ret = vikrentcar::applyExtraHoursChargesCar($tar, $pcar, $checkhourscharges, $daysdiff, false, true, true);
							$tar = $ret['return'];
							$calcdays = $ret['days'];
						}
						if($checkhourscharges > 0 && $aehourschbasp == false) {
							$tar = vikrentcar::extraHoursSetPreviousFareCar($tar, $pcar, $checkhourscharges, $daysdiff, true);
							$tar = vikrentcar::applySeasonsCar($tar, $ppickup, $prelease, $pplace);
							$ret = vikrentcar::applyExtraHoursChargesCar($tar, $pcar, $checkhourscharges, $daysdiff, true, true, true);
							$tar = $ret['return'];
							$calcdays = $ret['days'];
						}else {
							$tar = vikrentcar :: applySeasonsCar($tar, $ppickup, $prelease, $pplace);
						}
						//
						//modificacion
						//Se detecta si el usuario tiene paquetes horas
						/*
						$quees= vikrentcar:: getPriceName($tar[0]['idprice']);
						if(vikrentcar:: getPriceName($tar[0]['idprice'])=='Credito'){
							if(vikrentcar ::verPaqueteUser()!=0){
								$horasPaquete =vikrentcar ::verPaqueteUser();
								//$costoConCredito= vikrentcar ::sayCostWithCredito($tar[0]['idprice'], $tar[0]['idcar'], $tar[0]['hours']);




								if (array_key_exists("hours",$tar[0])){
									$costoConCredito= vikrentcar ::sayCostWithCredito($tar[0]['idprice'], $tar[0]['idcar'], $tar[0]['hours'], $ptotdue);
								}
								else{
									$costoConCredito= vikrentcar ::sayCostWithCreditoDay($tar[0]['idprice'], $tar[0]['idcar'], $tar[0]['days'], $ptotdue);
								}

								$isdue = vikrentcar :: sayCostPlusIva($costoConCredito, $tar[0]['idprice']);

							}else{
								$isdue = vikrentcar :: sayCostPlusIva($tar[0]['cost'], $tar[0]['idprice']);
							}
						}else{
							$isdue = vikrentcar :: sayCostPlusIva($costoConCredito, $tar[0]['idprice']);
						}

						*/

						$isdue = vikrentcar :: sayCostPlusIva($tar[0]['cost'], $tar[0]['idprice']);

						$optstr = "";


						if (!empty ($poptionals)) {
							$stepo = explode(";", $poptionals);
							foreach ($stepo as $oo) {
								if (!empty ($oo)) {
									$stept = explode(":", $oo);
									$q = "SELECT * FROM `#__vikrentcar_optionals` WHERE `id`='" . $dbo->getEscaped($stept[0]) . "';";
									$dbo->setQuery($q);
									$dbo->Query($q);
									if ($dbo->getNumRows() == 1) {
										$actopt = $dbo->loadAssocList();
										$realcost = (intval($actopt[0]['perday']) == 1 ? ($actopt[0]['cost'] * $pdays * $stept[1]) : ($actopt[0]['cost'] * $stept[1]));
										if (!empty($actopt[0]['maxprice']) && $actopt[0]['maxprice'] > 0 && $realcost > $actopt[0]['maxprice']) {
											$realcost = $actopt[0]['maxprice'];
											if(intval($actopt[0]['hmany']) == 1 && intval($stept[1]) > 1) {
												$realcost = $actopt[0]['maxprice'] * $stept[1];
											}
										}
										$tmpopr = vikrentcar :: sayOptionalsPlusIva($realcost, $actopt[0]['idiva']);
										$isdue += $tmpopr;
										$optstr .= ($stept[1] > 1 ? $stept[1] . " " : "") . $actopt[0]['name'] . ": " . $tmpopr . " " . $currencyname . "\n";
									}
								}
							}
						}
						$maillocfee = "";
						$validlocations = true;
						if (!empty ($pplace) && !empty ($preturnplace)) {
							$locfee = vikrentcar :: getLocFee($pplace, $preturnplace);
							if ($locfee) {
								$locfeecost = intval($locfee['daily']) == 1 ? ($locfee['cost'] * $pdays) : $locfee['cost'];
								$locfeewith = vikrentcar :: sayLocFeePlusIva($locfeecost, $locfee['idiva']);
								$isdue += $locfeewith;
								$maillocfee = $locfeewith;
							}
							//check valid locations
							$q = "SELECT `id`,`idplace`,`idretplace` FROM `#__vikrentcar_cars` WHERE `id`='" . $dbo->getEscaped($pcar) . "';";
							$dbo->setQuery($q);
							$dbo->Query($q);
							$infoplaces = $dbo->loadAssocList();
							if(!empty($infoplaces['idplace']) && !empty($infoplaces['idretplace'])) {
								$actplaces = explode(";", $infoplaces['idplace']);
								$actretplaces = explode(";", $infoplaces['idretplace']);
								if (!in_array($pplace, $actplaces) || !in_array($preturnplace, $actretplaces)) {
									$validlocations = false;
								}
							}
							//
						}
						//vikrentcar 1.6
						$origtotdue = $isdue;
						$usedcoupon = false;
						if(strlen($pcouponcode) > 0) {
							$coupon = vikrentcar::getCouponInfo($pcouponcode);
							if(is_array($coupon)) {
								$coupondateok = true;
								if(strlen($coupon['datevalid']) > 0) {
									$dateparts = explode("-", $coupon['datevalid']);
									$pickinfo = getdate($ppickup);
									$dropinfo = getdate($prelease);
									$checkpick = mktime(0, 0, 0, $pickinfo['mon'], $pickinfo['mday'], $pickinfo['year']);
									$checkdrop = mktime(0, 0, 0, $dropinfo['mon'], $dropinfo['mday'], $dropinfo['year']);
									if(!($checkpick >= $dateparts[0] && $checkpick <= $dateparts[1] && $checkdrop >= $dateparts[0] && $checkdrop <= $dateparts[1])) {
										$coupondateok = false;
									}
								}
								if($coupondateok == true) {
									$couponcarok = true;
									if($coupon['allvehicles'] == 0) {
										if(!(preg_match("/;".$pcar.";/i", $coupon['idcars']))) {
											$couponcarok = false;
										}
									}
									if($couponcarok == true) {
										$coupontotok = true;
										if(strlen($coupon['mintotord']) > 0) {
											if($isdue < $coupon['mintotord']) {
												$coupontotok = false;
											}
										}
										if($coupontotok == true) {
											$usedcoupon = true;
											if($coupon['percentot'] == 1) {
												//percent value
												$minuscoupon = 100 - $coupon['value'];
												$coupondiscount = $isdue * $coupon['value'] / 100;
												$isdue = $isdue * $minuscoupon / 100;
											}else {
												//total value
												$coupondiscount = $coupon['value'];
												$isdue = $isdue - $coupon['value'];
											}
											$strcouponeff = $coupon['id'].';'.$coupondiscount.';'.$coupon['code'];
										}
									}
								}
							}
						}
						//
						$strisdue = number_format($isdue, 2);
						$ptotdue = number_format($ptotdue, 2);
						
						if ($strisdue == $ptotdue) {
							$config =& JFactory::getConfig();
							$dateNow = new DateTime(null, new DateTimeZone($config->getValue('config.offset')));
							//$nowts  = $dateNow->getTimestamp() + $dateNow->getOffset();
							//$actnow  = $dateNow->getTimestamp()-18000;
							$nowts  = $dateNow->getTimestamp();



							//FB::log('nowts');

							if ($nowts < $ppickup && $nowts < $prelease && $ppickup < $prelease) {

								if($validlocations == true) {
									FB::log('validlocations');
									$q = "SELECT `units` FROM `#__vikrentcar_cars` WHERE `id`='" . $dbo->getEscaped($pcar) . "';";
									$dbo->setQuery($q);
									$dbo->Query($q);
									$units = $dbo->loadResult();

									if (vikrentcar :: carNotLocked($pcar, $units, $ppickup, $prelease)) {

										if (vikrentcar :: carBookable($pcar, $units, $ppickup, $prelease)) {
											//vikrentcar 1.6 restore $pdays to the actual days used
											//

											if(strlen($porigdays) > 0) {
												$pdays = $calcdays;
											}
											FB::log('carBookable');

											$sid = vikrentcar :: getSecretLink();
											$custdata = vikrentcar :: buildCustData($arrcustdata, "\n");
											if($format=='raw'){

												$viklink = JURI :: root() . "index.php?option=com_vikrentcar&task=vieworder&sid=" . $sid . "&ts=" . $nowts . (!empty ($pitemid) ? "&Itemid=" . $pitemid : "");

											}else{

												$viklink = JURI :: root() . "index.php?option=com_vikrentcar&task=vieworder&sid=" . $sid . "&ts=" . $nowts . (!empty ($pitemid) ? "&Itemid=" . $pitemid : "");

											}
											$admail = vikrentcar :: getAdminMail();
											$ftitle = vikrentcar :: getFrontTitle();
											$carinfo = vikrentcar :: getCarInfo($pcar);
											$pricestr = vikrentcar :: getPriceName($tar[0]['idprice']) . ": " . vikrentcar :: sayCostPlusIva($tar[0]['cost'], $tar[0]['idprice']) . (!empty ($tar[0]['attrdata']) ? " " . $currencyname . "\n" . vikrentcar :: getPriceAttr($tar[0]['idprice']) . ": " . $tar[0]['attrdata'] : "");
											$ritplace = (!empty ($pplace) ? vikrentcar :: getPlaceName($pplace) : "");
											$consegnaplace = (!empty ($preturnplace) ? vikrentcar :: getPlaceName($preturnplace) : "");
											$currentUser = JFactory::getUser();
											$idUser= $currentUser->get('id') ;



											if (vikrentcar :: areTherePayments()) {

												$payment = vikrentcar :: getPayment($pgpayid);
												$realback = vikrentcar :: getHoursCarAvail() * 3600;
												$realback += $prelease;

												//SE REVISA LOS SALDOS QUE TENGA EL USUARIOS
												$saldo= vikrentcar :: getSaldoUser($idUser);
												$ifsavesaldonormal=true;

												//se revisa otro tipo  de sado por ejemplo paquetes

												if($saldo==0){

													$saldo= vikrentcar :: getSaldoUser($idUser, false);

													if($saldo>0){

													 $ifsavesaldonormal=false;

													}

													$isPaquete= vikrentcar:: getPriceName($tar[0]['idprice']);
													
													//if($isPaquete!='Credito'){
													if (preg_match("/Paquete/i",$isPaquete)){

														//$canthours= vikrentcar::aplycableSaldo($idUser, $carinfo['idcat']);
														$idpaquete=vikrentcar::getSaldoPaquetes($idUser, $carinfo['idcat']);
														if($idpaquete!=0 || $idpaquete!=NULL ){

															//$saldo =vikrentcar::getMoneyEquivalenteHoras($phourly,  $carinfo['idcat'], $canthours);

															$saldo= vikrentcar :: getSaldoUser($idUser, false);
															$salvardebidopaq=true;
															//$saldo=$saldp-$saldo;
															$ifsavesaldonormal=false;


														}else{

															$saldo=0;
														}
													}


												}
												if((int)$saldo>=(int)$isdue){
													$saldoFavor=true;
													$payment['setconfirmed']=1;
												}else{

													if($saldo==0){

													}else{

														$isdue= $isdue-$saldo;
														vikrentcar::saveSaldo(0, $idUser, $ifsavesaldonormal);
														$payment['setconfirmed'] = 0;

													    $app->enqueueMessage(JText::_('VRMSGSALDOINSUFICIENTE').':'.number_format($isdue, 0));
													}
												}
												//detecta si cupon es convenio

												if(preg_match("/3/", $coupon['type'])) {




													$payment['setconfirmed'] = 1;





												}

												if (is_array($payment)) {


													//if (true) {
													//


													if (intval($payment['setconfirmed']) == 1) {

														$q = "INSERT INTO `#__vikrentcar_busy` (`idcar`,`ritiro`,`consegna`,`realback`) VALUES('" . $dbo->getEscaped($pcar) . "', '" . $dbo->getEscaped($ppickup) . "', '" . $dbo->getEscaped($prelease) . "','" . $realback . "');";
														$dbo->setQuery($q);
														$dbo->Query($q);
														$lid = $dbo->insertid();


														$q = "INSERT INTO `#__vikrentcar_orders` (`idbusy`,`custdata`,`ts`,`status`,`idcar`,`days`,`ritiro`,`consegna`,`idtar`,`optionals`,`custmail`,`sid`,`idplace`,`idreturnplace`,`totpaid`,`idpayment`,`ujid`,`hourly`,`coupon`, `passangers`) VALUES('" . $lid . "', '" . $dbo->getEscaped($custdata) . "','" . $nowts . "','confirmed','" . $dbo->getEscaped($pcar) . "','" . $dbo->getEscaped($pdays) . "','" . $dbo->getEscaped($ppickup) . "','" . $dbo->getEscaped($prelease) . "','" . $dbo->getEscaped($pprtar) . "','" . $dbo->getEscaped($poptionals) . "','" . $dbo->getEscaped($useremail) . "','" . $sid . "','" . $dbo->getEscaped($pplace) . "','" . $dbo->getEscaped($preturnplace) . "','" . $dbo->getEscaped($isdue) . "','" . $dbo->getEscaped($payment['id'] . '=' . $payment['name']) . "','".$currentUser->id."','".($usedhourly ? "1" : "0")."', '".($usedcoupon == true ? $dbo->getEscaped($strcouponeff) : "")."','".$dbo->getEscaped($numpasangers)."');";

														$dbo->setQuery($q);
														$dbo->Query($q);
														$neworderid = $dbo->insertid();
														if($usedcoupon == true && $coupon['type'] == 2) {

															$q="DELETE FROM `#__vikrentcar_coupons` WHERE `id`='".$coupon['id']."';";
															$dbo->setQuery($q);
															$dbo->Query($q);
														}
														//vikrentcar :: sendAdminMail($admail, JText :: _('VRORDNOL'), $ftitle, $nowts, $custdata, $carinfo['name'], $ppickup, $prelease, $pricestr, $optstr, $isdue, JText :: _('VRCOMPLETED'), $ritplace, $consegnaplace, $maillocfee, $payment['name'], $strcouponeff);
														//vikrentcar :: sendCustMail($useremail, strip_tags($ftitle) . " " . JText :: _('VRORDNOL'), $ftitle, $nowts, $custdata, $carinfo['name'], $ppickup, $prelease, $pricestr, $optstr, $isdue, $viklink, JText :: _('VRCOMPLETED'), $ritplace, $consegnaplace, $maillocfee, $neworderid, $strcouponeff);







														$body= vikrentcar :: makeemail($useremail, strip_tags($ftitle) . " " . JText :: _('VRORDNOL'), $ftitle, $nowts, $custdata, $carinfo['name'], $ppickup, $prelease, $pricestr, $optstr, $isdue, $viklink, JText :: _('VRCOMPLETED'), $ritplace, $consegnaplace, $maillocfee, $neworderid, $strcouponeff, '' ,'' , $tar[0]['idprice']);
														$subject='Notificacion Reserva: '.$neworderid;
														vikrentcar::enviarEmailAcymailing($idUser,LISTA_NOTIFICACION_RESERVAS,$subject,$body);


														$msgsms=vikrentcar::getInfoOrderSms($neworderid, 2);

														vikrentcar::enviarsms($idUser,LISTA_NOTIFICACION_RESERVAS_SMS, $msgsms, $neworderid);

														/*$q="SELECT `user_id` FROM `#__vikrentcar_profiles` WHERE `num_doc`='".$carinfo['idCond']."'";
														$dbo->setQuery($q);
														$dbo->Query($q);
														$juserPiloto = $dbo->loadAssocList();*/

														//se envia email al piloto notificando la reserva
														$body= vikrentcar :: makeemail($useremail, strip_tags($ftitle) . " " . JText :: _('VRORDNOL'), $ftitle, $nowts, $custdata, $carinfo['name'], $ppickup, $prelease, $pricestr, $optstr, $isdue, 'piloto', JText :: _('VRCOMPLETED'), $ritplace, $consegnaplace, $maillocfee, $neworderid, $strcouponeff, '' ,'' , $tar[0]['idprice']);

														vikrentcar::enviarEmailAcymailing($carinfo['idCond'],'',$subject,$body);
														//envio sms a conductor
														$msgsms=vikrentcar::getInfoOrderSms($neworderid, 4);
														vikrentcar::enviarsms($carinfo['idCond'],'', $msgsms, $neworderid);
														//
														//verifica saldo y aplica nuevo saldo

														if($saldoFavor){

															$concepto='Saldo a Favor: '.$neworderid;


														}else{

															$concepto='Compra servicio: '.$neworderid;


														}

														$userId = $idUser;
														$idorder=$neworderid;

														$lid= vikrentcar::saveDebito($idorder,$userId, $concepto, $salvardebidopaq, $idpaquete);

														$newSaldo = vikrentcar::calcularNuevoSaldo($idorder, $userId, $lid, $ifsavesaldonormal);

														if($newSaldo<0){

															$newSaldo=0;
														}


														vikrentcar::saveSaldo($newSaldo, $userId, $ifsavesaldonormal);




														//$app = & JFactory :: getApplication();
														if($format=='raw'){

															$app->redirect("index.php?option=com_vikrentcar&task=vieworder&format=raw&sid=" . $sid . "&ts=" . $nowts . (!empty ($pitemid) ? "&Itemid=" . $pitemid : ""));
														}else{
															$app->redirect("index.php?option=com_vikrentcar&task=vieworder&sid=" . $sid . "&ts=" . $nowts . (!empty ($pitemid) ? "&Itemid=" . $pitemid : ""));
														}
														//orderView($sid, $nowts, true);
													} else {

														//crea factura
														/*$chekout=(int)$chekout;
														//echo 'pasa por aqui'.$chekout;
														if($chekout==0){

															$q= "INSERT INTO `#__vikrentcar_factura` (`estado`) VALUES('standby')";
															$dbo->setQuery($q);
															$dbo->Query($q);

															$q = "SELECT MAX(id) FROM `#__vikrentcar_factura`;";
															$dbo->setQuery($q);
															$dbo->Query($q);
															$n = $dbo->loadAssocList();
															$id_factura=$n[0]['MAX(id)'];


														}
														else{

															$q = "SELECT MAX(id) FROM `#__vikrentcar_factura`;";
															$dbo->setQuery($q);
															$dbo->Query($q);
															$n = $dbo->loadAssocList();
															$id_factura=$n[0]['MAX(id)'];


														}
														*/



														$q = "INSERT INTO `#__vikrentcar_orders` (`custdata`,`ts`,`status`,`idcar`,`days`,`ritiro`,`consegna`,`idtar`,`optionals`,`custmail`,`sid`,`idplace`,`idreturnplace`, `totpaid`, `idpayment`,`ujid`,`hourly`,`coupon`,`passangers`) VALUES('" . $dbo->getEscaped($custdata) . "','" . $nowts . "','standby','" . $dbo->getEscaped($pcar) . "','" . $dbo->getEscaped($pdays) . "','" . $dbo->getEscaped($ppickup) . "','" . $dbo->getEscaped($prelease) . "','" . $dbo->getEscaped($pprtar) . "','" . $dbo->getEscaped($poptionals) . "','" . $dbo->getEscaped($useremail) . "','" . $sid . "','" . $dbo->getEscaped($pplace) . "','" . $dbo->getEscaped($preturnplace) . "','" . $dbo->getEscaped($isdue) . "','"  . $dbo->getEscaped($payment['id'] . '=' . $payment['name']) . "','".$currentUser->id."','".($usedhourly ? "1" : "0")."', '".($usedcoupon == true ? $dbo->getEscaped($strcouponeff) : "")."','".$dbo->getEscaped($numpasangers)."');";


														$dbo->setQuery($q);
														$dbo->Query($q);
														$neworderid = $dbo->insertid();

														if($usedcoupon == true && $coupon['type'] == 2) {
															$q="DELETE FROM `#__vikrentcar_coupons` WHERE `id`='".$coupon['id']."';";
															$dbo->setQuery($q);
															$dbo->Query($q);


														}


														$q = "INSERT INTO `#__vikrentcar_tmplock` (`idcar`,`ritiro`,`consegna`,`until`,`realback`) VALUES('" . $dbo->getEscaped($pcar) . "','" . $dbo->getEscaped($ppickup) . "','" . $dbo->getEscaped($prelease) . "','" . vikrentcar :: getMinutesLock(true) . "','" . $realback . "');";
														$dbo->setQuery($q);
														$dbo->Query($q);

														if($saldoFavor){

															$concepto='Saldo a Favor: '.$neworderid;


														}else{

															$concepto='Compra servicio: '.$neworderid;


														}


														$idorder =$neworderid;
														$userId= $currentUser->id;
														$lid= vikrentcar::saveDebito($idorder,$userId, $concepto);

														$newSaldo = vikrentcar::calcularNuevoSaldo($idorder, $userId, $lid);
														if($newSaldo<0){
															$newSaldo=0;
														}

														vikrentcar::saveSaldo($newSaldo, $userId);




														//vikrentcar :: sendAdminMail($admail, JText :: _('VRORDNOL'), $ftitle, $nowts, $custdata, $carinfo['name'], $ppickup, $prelease, $pricestr, $optstr, $isdue, JText :: _('VRINATTESA'), $ritplace, $consegnaplace, $maillocfee, $payment['name'], $strcouponeff);
														//vikrentcar :: sendCustMail($useremail, strip_tags($ftitle) . " " . JText :: _('VRORDNOL'), $ftitle, $nowts, $custdata, $carinfo['name'], $ppickup, $prelease, $pricestr, $optstr, $isdue, $viklink, JText :: _('VRINATTESA'), $ritplace, $consegnaplace, $maillocfee, $neworderid, $strcouponeff);
														if($pdays==1){

															$q = "SELECT idprice FROM `#__vikrentcar_dispcost` WHERE `id`='" . $dbo->getEscaped($pprtar)."'";
															$dbo->setQuery($q);
															$dbo->Query($q);
															$id_price = $dbo->loadAssocList();

														}else{

															$q = "SELECT idprice FROM `#__vikrentcar_dispcosthours` WHERE `id`='" . $dbo->getEscaped($pprtar)."'" ;
															$dbo->setQuery($q);
															$dbo->Query($q);
															$id_price = $dbo->loadAssocList();
														}
														//Modificacion
														if(vikrentcar:: getPriceName($id_price[0]['idprice'])==CREDITO){
														//if($isPaquete=="yes"){
															$q = "SELECT `user_id`, `credito` FROM `#__vikrentcar_profiles` WHERE `user_id`='".$idUser."'";
															$dbo->setQuery($q);
															$dbo->Query($q);
															$n = $dbo->loadAssocList();
															$creditoOld=$n[0]['credito'];
															$creditoActual=0;
															$creditoOld=0;
															//si existe id de usuario
															if(!$n[0]['user_id']){
																$creditoActual = $creditoOld + $valorPaquete;
																$q = "INSERT INTO `#__vikrentcar_profiles` (`user_id`,`credito`) VALUES('".$idUser."','".$creditoActual."');";
																$dbo->setQuery($q);
																$dbo->Query($q);

																/*$q = "SELECT `user_id`, `credito` FROM `#__vikrentcar_profiles` WHERE `user_id`='".$idUser."'";
																$dbo->setQuery($q);
																$dbo->Query($q);
																$n = $dbo->loadAssocList();*/



															}else {
																$q = "SELECT `user_id`, `credito` FROM `#__vikrentcar_profiles` WHERE `user_id`='".$idUser."'";
																$dbo->setQuery($q);
																$dbo->Query($q);
																$n = $dbo->loadAssocList();
																$creditoOld=$n[0]['credito'];

																$creditoActual = $creditoOld + $valorPaquete;
																$q = "UPDATE `#__vikrentcar_profiles` SET `credito`='".$creditoActual."' WHERE `user_id`='" . $idUser . "';";
																$dbo->setQuery($q);
																$dbo->Query($q);


																/*$q = "UPDATE `#__vikrentcar_creditos` SET `credito`='".$creditoActual."' WHERE `id_user`='" . $idUser . "';";
																$dbo->setQuery($q);
																$dbo->Query($q);*/

															}

																$q = "SELECT MAX(id) FROM `#__vikrentcar_orders`;";
																$dbo->setQuery($q);
																$dbo->Query($q);
																$n = $dbo->loadAssocList();
																$idorder=$n[0]['MAX(id)'];

																$q = "INSERT INTO `#__vikrentcar_creditos` (`id_user`,`credito`,`idorder` ) VALUES('".$idUser."','".$creditoActual."','".$idorder."');";
																$dbo->setQuery($q);
																$dbo->Query($q);

														}else{




															$q = "SELECT `user_id`, `credito` FROM `#__vikrentcar_profiles` WHERE `user_id`='".$idUser."'";
															$dbo->setQuery($q);
															$dbo->Query($q);
															$n = $dbo->loadAssocList();
															$creditoOld=$n[0]['credito'];
															if(!$n[0]['user_id']){

																$q = "INSERT INTO `#__vikrentcar_profiles` (`user_id`,`credito`) VALUES('".$idUser."','".$valorPaquete."');";
																$dbo->setQuery($q);
																$dbo->Query($q);


															}else {
																//actualiza a nuevo saldo
																$creditoActual = $creditoOld - $phourly;
																$horasusadas=$phourly;
																if($creditoActual<0){
																	$creditoActual=0;
																	$horasusadas= $creditoOld;
																}
																$q = "UPDATE `#__vikrentcar_profiles` SET `credito`='".$creditoActual."' WHERE `user_id`='" . $idUser . "';";
																$dbo->setQuery($q);
																$dbo->Query($q);


															}

															//

															$q = "SELECT MAX(id) FROM `#__vikrentcar_orders`;";
															$dbo->setQuery($q);
															$dbo->Query($q);
															$n = $dbo->loadAssocList();
															$idorder=$n[0]['MAX(id)'];

															//inserta en la tabla creditos
															//$horaspaq=($isPaquete=="yes")?$valorPaquete:$horasPaqueteUsadas;
															$q = "INSERT INTO `#__vikrentcar_creditos` (`id_user`,`idorder`,`credito`) VALUES('".$idUser."','".$idorder."','-".$horasusadas."');";
															$dbo->setQuery($q);
															$dbo->Query($q);
														}



													//Calculamos el saldo actual del usuario
													$saldo_actual = vikrentcar::calcularNuevoSaldo($idorder, $userId, $lid);

														$body= vikrentcar :: makeemail($useremail, strip_tags($ftitle) . " " . JText :: _('VRORDNOL'), $ftitle, $nowts, $custdata, $carinfo['name'], $ppickup, $prelease, $pricestr, $optstr, $isdue, $viklink, JText :: _('VRINATTESA'), $ritplace, $consegnaplace, $maillocfee, $neworderid, $strcouponeff, '' ,'' , $tar[0]['idprice']);
														$subject='Notificacion Reserva: '.$neworderid;
														vikrentcar::enviarEmailAcymailing($idUser,LISTA_NOTIFICACION_RESERVAS,$subject,$body);

														$msgsms=vikrentcar::getInfoOrderSms($neworderid, 2);


														//Verificamos que el saldo actual del usuario sea positivo para de esta manera poder enviarle el SMS de confirmacion
														if($saldo_actual >= 0){
															vikrentcar::enviarsms($idUser,LISTA_NOTIFICACION_RESERVAS_SMS, $msgsms, $neworderid);
														}
														

														/*$q="SELECT `user_id` FROM `#__vikrentcar_profiles` WHERE `num_doc`='".$carinfo['idCond']."'";
														$dbo->setQuery($q);
														$dbo->Query($q);
														$juserPiloto = $dbo->loadAssocList();*/

														//se envia email al piloto notificando la reserva
														$body= vikrentcar :: makeemail($useremail, strip_tags($ftitle) . " " . JText :: _('VRORDNOL'), $ftitle, $nowts, $custdata, $carinfo['name'], $ppickup, $prelease, $pricestr, $optstr, $isdue, 'piloto', JText :: _('VRINATTESA'), $ritplace, $consegnaplace, $maillocfee, $neworderid, $strcouponeff,'' ,'' , $tar[0]['idprice']);

														$envioemailPiloto= vikrentcar::enviarEmailAcymailing($carinfo['idCond'],'',$subject,$body);
														//envio sms a piloto
														//
														if(!$envioemailPiloto){


															//se envia email que hubo un error enviando correo al conductor
														$body= vikrentcar :: makeemail($useremail, strip_tags($ftitle) . " " . JText :: _('VRORDNOL'), $ftitle, $nowts, $custdata, $carinfo['name'], $ppickup, $prelease, $pricestr, $optstr, $isdue, 'error', JText :: _('VRINATTESA'), $ritplace, $consegnaplace, $maillocfee, $neworderid, $strcouponeff,  '' ,'' , $tar[0]['idprice']);
													    $subject ='Error Notificacion Reserva: '.$neworderid;
														$envioemailPiloto= vikrentcar::enviarEmailAcymailing('',LISTA_NOTIFICACION_RESERVAS,$subject,$body);



														}

														$msgsms=vikrentcar::getInfoOrderSms($neworderid, 4);


														//Verificamos que el saldo actual del usuario sea positivo para de esta manera poder enviarle el SMS de confirmacion al piloto
														if($saldo_actual >= 0){
															vikrentcar::enviarsms($carinfo['idCond'],'', $msgsms,  $neworderid);
														}										

														?>
		                                                <div>
		                                                <input type="hidden" name='ordenGuardada' value="<?php echo $neworderid; ?>"/>
														<p class="successmade"><?php echo JText::_('VRTHANKSONE'); ?></p>
														<br/>
														<!--<p>&bull; <?php //echo JText::_('VRTHANKSTWO'); ?> <a href="<?php //echo $viklink; ?>"><?php //echo JText::_('VRTHANKSTHREE'); ?></a></p>-->
														</div>
														<?php

														//$mainframe = & JFactory :: getApplication();
														if($format=='raw'){

															$app->redirect("index.php?option=com_vikrentcar&task=vieworder&format=raw&sid=" . $sid . "&ts=" . $nowts . (!empty ($pitemid) ? "&Itemid=" . $pitemid : ""));
														}else{
														$app->redirect("index.php?option=com_vikrentcar&task=vieworder&sid=" . $sid . "&ts=" . $nowts . (!empty ($pitemid) ? "&Itemid=" . $pitemid : ""));
														}//orderView($sid, $nowts, true);
													}
												} else {
													JError :: raiseWarning('', JText :: _('ERRSELECTPAYMENT'));
													//$app = & JFactory :: getApplication();
													if($format=='raw'){
													$app->redirect("index.php?option=com_vikrentcar&format=raw&priceid=" . $ppriceid . "&place=" . $pplace . "&returnplace=" . $peturnplace . "&carid=" . $pcar . "&days=" . $pdays . "&pickup=" . $ppickup . "&release=" . $prelease . "&task=oconfirm");
													}else{
													$app->redirect("index.php?option=com_vikrentcar&priceid=" . $ppriceid . "&place=" . $pplace . "&returnplace=" . $peturnplace . "&carid=" . $pcar . "&days=" . $pdays . "&pickup=" . $ppickup . "&release=" . $prelease . "&task=oconfirm");
													}

												}
											} else {
												$realback = vikrentcar :: getHoursCarAvail() * 3600;
												$realback += $prelease;
												$q = "INSERT INTO `#__vikrentcar_busy` (`idcar`,`ritiro`,`consegna`,`realback`) VALUES('" . $dbo->getEscaped($pcar) . "', '" . $dbo->getEscaped($ppickup) . "', '" . $dbo->getEscaped($prelease) . "','" . $realback . "');";
												$dbo->setQuery($q);
												$dbo->Query($q);
												$lid = $dbo->insertid();
												$q = "INSERT INTO `#__vikrentcar_orders` (`idbusy`,`custdata`,`ts`,`status`,`idcar`,`days`,`ritiro`,`consegna`,`idtar`,`optionals`,`custmail`,`sid`,`idplace`,`idreturnplace`,`totpaid`,`ujid`,`hourly`,`coupon`,`passangers`) VALUES('" . $lid . "', '" . $dbo->getEscaped($custdata) . "','" . $nowts . "','confirmed','" . $dbo->getEscaped($pcar) . "','" . $dbo->getEscaped($pdays) . "','" . $dbo->getEscaped($ppickup) . "','" . $dbo->getEscaped($prelease) . "','" . $dbo->getEscaped($pprtar) . "','" . $dbo->getEscaped($poptionals) . "','" . $dbo->getEscaped($useremail) . "','" . $sid . "','" . $dbo->getEscaped($pplace) . "','" . $dbo->getEscaped($preturnplace) . "','" . $dbo->getEscaped($isdue) . "','".$currentUser->id."','".($usedhourly ? "1" : "0")."', '".($usedcoupon == true ? $dbo->getEscaped($strcouponeff) : "")."','".$dbo->getEscaped($numpasangers)."');";
												$dbo->setQuery($q);
												$dbo->Query($q);
												$neworderid = $dbo->insertid();
												if($usedcoupon == true && $coupon['type'] == 2) {
													$q="DELETE FROM `#__vikrentcar_coupons` WHERE `id`='".$coupon['id']."';";
													$dbo->setQuery($q);
													$dbo->Query($q);
												}
												//Modificacion

												if($isPaquete=="yes"){
													$q = "SELECT `user_id`, `credito` FROM `#__vikrentcar_profiles` WHERE `user_id`='".$idUser."'";
													$dbo->setQuery($q);
													$dbo->Query($q);
													$n = $dbo->loadAssocList();
													$creditoOld=$n[0]['credito'];
													$creditoActual=0;
													$creditoOld=0;
													//si existe id de usuario
													if(!$n[0]['user_id']){
														$creditoActual = $creditoOld + $valorPaquete;
														$q = "INSERT INTO `#__vikrentcar_profiles` (`user_id`,`credito`) VALUES('".$idUser."','".$creditoActual."');";
														$dbo->setQuery($q);
														$dbo->Query($q);

														/*$q = "SELECT `user_id`, `credito` FROM `#__vikrentcar_profiles` WHERE `user_id`='".$idUser."'";
														$dbo->setQuery($q);
														$dbo->Query($q);
														$n = $dbo->loadAssocList();*/



													}else {
														$q = "SELECT `user_id`, `credito` FROM `#__vikrentcar_profiles` WHERE `user_id`='".$idUser."'";
														$dbo->setQuery($q);
														$dbo->Query($q);
														$n = $dbo->loadAssocList();
														$creditoOld=$n[0]['credito'];

														$creditoActual = $creditoOld + $valorPaquete;
														$q = "UPDATE `#__vikrentcar_profiles` SET `credito`='".$creditoActual."' WHERE `user_id`='" . $idUser . "';";
														$dbo->setQuery($q);
														$dbo->Query($q);


														/*$q = "UPDATE `#__vikrentcar_creditos` SET `credito`='".$creditoActual."' WHERE `id_user`='" . $idUser . "';";
														$dbo->setQuery($q);
														$dbo->Query($q);*/

													}

														$q = "SELECT MAX(id) FROM `#__vikrentcar_orders`;";
														$dbo->setQuery($q);
														$dbo->Query($q);
														$n = $dbo->loadAssocList();
														$idorder=$n[0]['MAX(id)'];

														$q = "INSERT INTO `#__vikrentcar_creditos` (`id_user`,`credito`,`idorder` ) VALUES('".$idUser."','".$creditoActual."','".$idorder."');";
														$dbo->setQuery($q);
														$dbo->Query($q);

												}else{




													$q = "SELECT `user_id`, `credito` FROM `#__vikrentcar_profiles` WHERE `user_id`='".$idUser."'";
													$dbo->setQuery($q);
													$dbo->Query($q);
													$n = $dbo->loadAssocList();
													$creditoOld=$n[0]['credito'];
													if(!$n[0]['user_id']){

														$q = "INSERT INTO `#__vikrentcar_profiles` (`user_id`,`credito`) VALUES('".$idUser."','".$valorPaquete."');";
														$dbo->setQuery($q);
														$dbo->Query($q);


													}else {
														//actualiza a nuevo saldo
														$creditoActual = $creditoOld - $phourly;
														$horasusadas=$phourly;
														if($creditoActual<0){
															$creditoActual=0;
															$horasusadas= $creditoOld;
														}
														$q = "UPDATE `#__vikrentcar_profiles` SET `credito`='".$creditoActual."' WHERE `user_id`='" . $idUser . "';";
														$dbo->setQuery($q);
														$dbo->Query($q);


													}

													//

													$q = "SELECT MAX(id) FROM `#__vikrentcar_orders`;";
													$dbo->setQuery($q);
													$dbo->Query($q);
													$n = $dbo->loadAssocList();
													$idorder=$n[0]['MAX(id)'];

													//inserta en la tabla creditos
													//$horaspaq=($isPaquete=="yes")?$valorPaquete:$horasPaqueteUsadas;
													$q = "INSERT INTO `#__vikrentcar_creditos` (`id_user`,`idorder`,`credito`) VALUES('".$idUser."','".$idorder."','-".$horasusadas."');";
													$dbo->setQuery($q);
													$dbo->Query($q);
												}

												 $body= vikrentcar :: makeemail($useremail, strip_tags($ftitle) . " " . JText :: _('VRORDNOL'), $ftitle, $nowts, $custdata, $carinfo['name'], $ppickup, $prelease, $pricestr, $optstr, $isdue, $viklink, JText :: _('VRINATTESA'), $ritplace, $consegnaplace, $maillocfee, $neworderid, $strcouponeff,  '' ,'' , $tar[0]['idprice']);
												 $subject='Notificacion Reserva: '.$neworderid;
												 vikrentcar::enviarEmailAcymailing($idUser,LISTA_NOTIFICACION_RESERVAS,$subject,$body);

												 //$msgsms=vikrentcar::getInfoOrderSms($neworderid);
											     //vikrentcar::enviarsms($idUser,LISTA_NOTIFICACION_RESERVAS_SMS, $msgsms);


												 /*$q="SELECT `user_id` FROM `#__vikrentcar_profiles` WHERE `num_doc`='".$carinfo['idCond']."'";
												 $dbo->setQuery($q);
												 $dbo->Query($q);
												 $juserPiloto = $dbo->loadAssocList();*/

												//se envia email al piloto
												  $body=undefined;
												  $body= vikrentcar :: makeemail($useremail, strip_tags($ftitle) . " " . JText :: _('VRORDNOL'), $ftitle, $nowts, $custdata, $carinfo['name'], $ppickup, $prelease, $pricestr, $optstr, $isdue, '', JText :: _('VRINATTESA'), $ritplace, $consegnaplace, $maillocfee, $neworderid, $strcouponeff,  '' ,'' , $tar[0]['idprice']);

												 vikrentcar::enviarEmailAcymailing($carinfo['idCond'],'',$subject,$body);


												 $msgsms=vikrentcar::getInfoOrderSms($neworderid, 2);

												 vikrentcar::enviarsms($idUser,LISTA_NOTIFICACION_RESERVAS_SMS, $msgsms,  $neworderid);

											     $msgsms=vikrentcar::getInfoOrderSms($neworderid, 4);
											     vikrentcar::enviarsms($carinfo['idCond'],'', $msgsms,  $neworderid);

												//echo ('Se ha enviado un email a su correo y listas: '.LISTA_NOTIFICACION_RESERVAS);

												//vikrentcar :: sendAdminMail($admail, JText :: _('VRORDNOL'), $ftitle, $nowts, $custdata, $carinfo['name'], $ppickup, $prelease, $pricestr, $optstr, $isdue, JText :: _('VRCOMPLETED'), $ritplace, $consegnaplace, $maillocfee, $strcouponeff);
												//vikrentcar :: sendCustMail($useremail, strip_tags($ftitle) . " " . JText :: _('VRORDNOL'), $ftitle, $nowts, $custdata, $carinfo['name'], $ppickup, $prelease, $pricestr, $optstr, $isdue, $viklink, JText :: _('VRCOMPLETED'), $ritplace, $consegnaplace, $maillocfee, $neworderid, $strcouponeff);


												 //datos para imprimir

												//echo vikrentcar :: getFullFrontTitle();


												?>
                                                <div>
                                                <input type="hidden" name='ordenGuardada' value="<?php echo $n[0]['MAX(id)']; ?>"/>
												<p class="successmade"><?php echo JText::_('VRTHANKSONE'); ?></p>
												<br/>
												<!--<p>&bull; <?php //echo JText::_('VRTHANKSTWO'); ?> <a href="<?php //echo $viklink; ?>"><?php //echo JText::_('VRTHANKSTHREE'); ?></a></p>-->
												</div>
												<?php

												FB::log('End');
											}
										} else {

											showSelect(JText :: _('VRCARBOOKEDBYOTHER'));
										}
									} else {

										showSelect(JText :: _('VRCARISLOCKED'));
									}
								}else {

									showSelect(JText :: _('VRINVALIDLOCATIONS'));
								}
							} else {

								showSelect(JText :: _('VRINVALIDDATES'));
							}
						} else {

							showSelect(JText :: _('VRINCONGRTOT'));
							//

							//$content= 'mostrarMensaje();';
							//$doc->addScriptDeclaration($content);
						}
					} else {

						showSelect(JText :: _('VRINCONGRDATAREC'));
					}
				} else {

					showSelect(JText :: _('VRINCONGRDATA'));
				}
			} else {

				showSelect(JText :: _('VRINSUFDATA'));
			}
		} else {

			showSelect(JText :: _('VRINVALIDTOKEN'));

		}


	}






	function vieworder() {
		$dbo = & JFactory :: getDBO();
		$sid = JRequest :: getString('sid', '', 'request');
		$ts = JRequest :: getString('ts', '', 'request');


		//consulta con redeban si ya se realizo el pago
		$q= "SELECT tr.id AS id_trans, o.id AS id_order, o.id_factura, tr.id_resultado_transac FROM  #__vikrentcar_orders AS o  INNER JOIN   #__vikrentcar_transaccion AS tr ON tr.id_factura=o.id_factura WHERE o.sid=".$sid." AND o.ts=".$ts." ORDER BY tr.id DESC";
			$dbo->setQuery($q);
		    $dbo->Query($q);

		    if ($dbo->getNumRows() > 0) {

		    	$idtrans = $dbo->loadAssocList();

		    	if($idtrans[0]['id_resultado_transac']==0){

		    		self::consultarEstadoPago($sid, $ts);

		    	}




		    }




		if (!empty ($sid) && !empty ($ts)) {

			$q = "SELECT * FROM `#__vikrentcar_orders` WHERE `sid`='" . $dbo->getEscaped($sid) . "' AND `ts`='" . $dbo->getEscaped($ts) . "';";
			$dbo->setQuery($q);
			$dbo->Query($q);
			if ($dbo->getNumRows() == 1) {
				$order = $dbo->loadAssocList();






				//if (false){
				if ($order[0]['status'] == "confirmed") {


					JRequest :: setVar('view', 'confirmedorder');
					parent :: display();
				} else {
					$q = "SELECT `units` FROM `#__vikrentcar_cars` WHERE `id`='" . $order[0]['idcar'] . "';";
					$dbo->setQuery($q);
					$dbo->Query($q);
					$cunits = $dbo->loadResult();
					$caravail = vikrentcar :: carBookable($order[0]['idcar'], $cunits, $order[0]['ritiro'], $order[0]['consegna']);
					$actualtime= time();
					$horaritiro= $order[0]['ritiro'];
					if (time() > $order[0]['ritiro']) {
						$caravail = false;
					}
					if ($caravail == true) {
						//SHOW PAYMENT FORM
						JRequest :: setVar('view', 'standbyorder');


						parent :: display();
					} else {
						$q = "DELETE FROM `#__vikrentcar_orders` WHERE `id`='" . $order[0]['id'] . "' LIMIT 1;";
						$dbo->setQuery($q);
						$dbo->Query($q);
						if(!empty($order[0]['idbusy'])) {
							$q="DELETE FROM `#__vikrentcar_busy` WHERE `id`='" . $order[0]['idbusy'] . "' LIMIT 1;";
							$dbo->setQuery($q);
							$dbo->Query($q);
						}
						if (time() > $order[0]['ritiro']) {
							showSelect("");
						} else {
							showSelect(JText :: _('VRERRREPSEARCH'));
						}
					}
				}
			} else {
				showSelect(JText :: _('VRORDERNOTFOUND'));
			}
		} else {
			showSelect(JText :: _('VRINSUFDATA'));
		}
	}

	function formPlanilla(){

		$document    = & JFactory :: getDocument();
		$dbo = & JFactory :: getDBO();

		$q = "SELECT `setting` FROM `#__vikrentcar_config` WHERE `param`='sitelogo';";
		$dbo->setQuery($q);
		$dbo->Query($q);
		$sitelogo = $dbo->loadResult();

		if (!empty ($sitelogo) && @ file_exists('./administrator/components/com_vikrentcar/resources/' . $sitelogo)) {
				$image = array (
				"FileName" => JURI :: root() . "administrator/components/com_vikrentcar/resources/" . $sitelogo, "Content-Type" => "automatic/name", "Disposition" => "inline");
				$attachlogo = true;
		}
		$tlogo = ($attachlogo ? "<img src=\"" . $image['FileName'] . "\" alt=\"imglogo\"/>\n" : "");


		$savePlanillaLink = JURI :: root() . "index.php?option=com_vikrentcar&task=savePlanilla";

		echo  '<div class="pg-form"><form id="formPlanilla" action="'.$savePlanillaLink.'">'
				.$tlogo.'<div class="f-item"> <label> Asocie el número de planilla correspondiente a la orden</label> </div>
				<div class="column">

					<div class="f-item">

			  			<label>Número de Planilla:</label> </br>
			  			<input type="text" name="inputPlanilla" id="inputPlanilla" >
			  		</div></br>
			  		<div class="f-item">
			  			<input  type="submit" value="Guardar">
			  		</div>


			 	</div>
			 </form>
			 </div>
			 ';
	}


	function savePlanilla(){


  		$inputPlanilla = JRequest :: getString('inputPlanilla', '', 'request');
  		$rowlist = JRequest :: getString('rowlist', '', 'request');


  		$dbo = & JFactory :: getDBO();
  		$q = "UPDATE `#__vikrentcar_orders` SET  `planilla`='" . $dbo->getEscaped($inputPlanilla). "'" . " WHERE `id`='" . $dbo->getEscaped($rowlist) . "';";
		$dbo->setQuery($q);
		$dbo->Query($q);

		if($dbo->getAffectedRows() !=0){
			echo $inputPlanilla;
		}else{

			echo 'No Actualizado';
		}




  		//echo $inputPlanilla.'    '.  $rowlist;


  		//$dbo = & JFactory :: getDBO();






		//$dbo = & JFactory :: getDBO();


	}

	function notifypayment() {
		$psid = JRequest :: getString('sid', '', 'request');
		$pts = JRequest :: getString('ts', '', 'request');
		$dbo = & JFactory :: getDBO();
		if (vikrentcar :: getDateFormat() == "%d/%m/%Y") {
			$df = 'd/m/Y';
		} else {
			$df = 'Y/m/d';
		}
		if (strlen($psid) && strlen($pts)) {
			$admail = vikrentcar :: getAdminMail();
			$q = "SELECT * FROM `#__vikrentcar_orders` WHERE `ts`='" . $dbo->getEscaped($pts) . "' AND `sid`='" . $dbo->getEscaped($psid) . "';";
			$dbo->setQuery($q);
			$dbo->Query($q);
			if ($dbo->getNumRows() > 0) {
				$rows = $dbo->loadAssocList();
				$rows[0]['admin_email'] = $admail;
				$exppay = explode('=', $rows[0]['idpayment']);
				$payment = vikrentcar :: getPayment($exppay[0]);
				require_once(JPATH_ADMINISTRATOR . DS ."components". DS ."com_vikrentcar". DS . "payments" . DS . $payment['file']);
				$obj = new vikRentCarPayment($rows[0]);
				$array_result = $obj->validatePayment();
				if ($array_result['verified'] == 1) {
					//valid payment
					$ritplace = (!empty ($rows[0]['idplace']) ? vikrentcar :: getPlaceName($rows[0]['idplace']) : "");
					$consegnaplace = (!empty ($rows[0]['idreturnplace']) ? vikrentcar :: getPlaceName($rows[0]['idreturnplace']) : "");
					$realback = vikrentcar :: getHoursCarAvail() * 3600;
					$realback += $rows[0]['consegna'];
					//send mails
					$ftitle = vikrentcar :: getFrontTitle();
					$nowts = time();
					$carinfo = vikrentcar :: getCarInfo($rows[0]['idcar']);
					$viklink = JURI :: root() . "index.php?option=com_vikrentcar&task=vieworder&sid=" . $psid . "&ts=" . $pts;
					//vikrentcar 1.5
					if($rows[0]['hourly'] == 1) {
						$q = "SELECT * FROM `#__vikrentcar_dispcosthours_master` WHERE `id`='" . $rows[0]['idtar'] . "';";
					}else {
						$q = "SELECT * FROM `#__vikrentcar_dispcost_master` WHERE `id`='" . $rows[0]['idtar'] . "';";
					}
					//
					$dbo->setQuery($q);
					$dbo->Query($q);
					$tar = $dbo->loadAssocList();
					//vikrentcar 1.5
					if($rows[0]['hourly'] == 1) {
						foreach($tar as $kt => $vt) {
							$tar[$kt]['days'] = 1;
						}
					}
					//
					//vikrentcar 1.6
					$checkhourscharges = 0;
					$hoursdiff = 0;
					$ppickup = $rows[0]['ritiro'];
					$prelease = $rows[0]['consegna'];
					$secdiff = $prelease - $ppickup;
					$daysdiff = $secdiff / 86400;
					if (is_int($daysdiff)) {
						if ($daysdiff < 1) {
							$daysdiff = 1;
						}
					}else {
						if ($daysdiff < 1) {
							$daysdiff = 1;
							$checkhourly = true;
							$ophours = $secdiff / 3600;
							$hoursdiff = intval(round($ophours));
							if($hoursdiff < 1) {
								$hoursdiff = 1;
							}
						}else {
							$sum = floor($daysdiff) * 86400;
							$newdiff = $secdiff - $sum;
							$maxhmore = vikrentcar :: getHoursMoreRb() * 3600;
							if ($maxhmore >= $newdiff) {
								$daysdiff = floor($daysdiff);
							}else {
								$daysdiff = ceil($daysdiff);
								//vikrentcar 1.6
								$ehours = intval(round(($newdiff - $maxhmore) / 3600));
								$checkhourscharges = $ehours;
								if($checkhourscharges > 0) {
									$aehourschbasp = vikrentcar::applyExtraHoursChargesBasp();
								}
								//
							}
						}
					}
					if($checkhourscharges > 0 && $aehourschbasp == true) {
						$ret = vikrentcar::applyExtraHoursChargesCar($tar, $rows[0]['idcar'], $checkhourscharges, $daysdiff, false, true, true);
						$tar = $ret['return'];
						$calcdays = $ret['days'];
					}
					if($checkhourscharges > 0 && $aehourschbasp == false) {
						$tar = vikrentcar::extraHoursSetPreviousFareCar($tar, $rows[0]['idcar'], $checkhourscharges, $daysdiff, true);
						$tar = vikrentcar :: applySeasonsCar($tar, $rows[0]['ritiro'], $rows[0]['consegna'], $rows[0]['idplace']);
						$ret = vikrentcar::applyExtraHoursChargesCar($tar, $rows[0]['idcar'], $checkhourscharges, $daysdiff, true, true, true);
						$tar = $ret['return'];
						$calcdays = $ret['days'];
					}else {
						$tar = vikrentcar :: applySeasonsCar($tar, $rows[0]['ritiro'], $rows[0]['consegna'], $rows[0]['idplace']);
					}
					//
					$pricestr = vikrentcar :: getPriceName($tar[0]['idprice']) . ": " . vikrentcar :: sayCostPlusIva($tar[0]['cost'], $tar[0]['idprice']) . (!empty ($tar[0]['attrdata']) ? "\n" . vikrentcar :: getPriceAttr($tar[0]['idprice']) . ": " . $tar[0]['attrdata'] : "");
					$isdue = vikrentcar :: sayCostPlusIva($tar[0]['cost'], $tar[0]['idprice']);
					$currencyname = vikrentcar :: getCurrencyName();
					$optstr = "";
					if (!empty ($rows[0]['optionals'])) {
						$stepo = explode(";", $rows[0]['optionals']);
						foreach ($stepo as $oo) {
							if (!empty ($oo)) {
								$stept = explode(":", $oo);
								$q = "SELECT * FROM `#__vikrentcar_optionals` WHERE `id`='" . $dbo->getEscaped($stept[0]) . "';";
								$dbo->setQuery($q);
								$dbo->Query($q);
								if ($dbo->getNumRows() == 1) {
									$actopt = $dbo->loadAssocList();
									$realcost = (intval($actopt[0]['perday']) == 1 ? ($actopt[0]['cost'] * $rows[0]['days'] * $stept[1]) : ($actopt[0]['cost'] * $stept[1]));
									if (!empty ($actopt[0]['maxprice']) && $actopt[0]['maxprice'] > 0 && $realcost > $actopt[0]['maxprice']) {
										$realcost = $actopt[0]['maxprice'];
										if(intval($actopt[0]['hmany']) == 1 && intval($stept[1]) > 1) {
											$realcost = $actopt[0]['maxprice'] * $stept[1];
										}
									}
									$tmpopr = vikrentcar :: sayOptionalsPlusIva($realcost, $actopt[0]['idiva']);
									$isdue += $tmpopr;
									$optstr .= ($stept[1] > 1 ? $stept[1] . " " : "") . $actopt[0]['name'] . ": " . $tmpopr . " " . $currencyname . "\n";
								}
							}
						}
					}
					$maillocfee = "";
					if (!empty ($rows[0]['idplace']) && !empty ($rows[0]['idreturnplace'])) {
						$locfee = vikrentcar :: getLocFee($rows[0]['idplace'], $rows[0]['idreturnplace']);
						if ($locfee) {
							$locfeecost = intval($locfee['daily']) == 1 ? ($locfee['cost'] * $rows[0]['days']) : $locfee['cost'];
							$locfeewith = vikrentcar :: sayLocFeePlusIva($locfeecost, $locfee['idiva']);
							$isdue += $locfeewith;
							$maillocfee = $locfeewith;
						}
					}
					//vikrentcar 1.6 coupon
					$usedcoupon = false;
					$origisdue = $isdue;
					if(strlen($rows[0]['coupon']) > 0) {
						$usedcoupon = true;
						$expcoupon = explode(";", $rows[0]['coupon']);
						$isdue = $isdue - $expcoupon[1];
					}
					//
					if ($payment['charge'] > 0.00) {
						$shouldpay = $isdue;
						if($payment['ch_disc'] == 1) {
							//charge
							if($payment['val_pcent'] == 1) {
								//fixed value
								$shouldpay += $payment['charge'];
							}else {
								//percent value
								$percent_to_pay = $shouldpay * $payment['charge'] / 100;
								$shouldpay += $percent_to_pay;
							}
						}else {
							//discount
							if($payment['val_pcent'] == 1) {
								//fixed value
								$shouldpay -= $payment['charge'];
							}else {
								//percent value
								$percent_to_pay = $shouldpay * $payment['charge'] / 100;
								$shouldpay -= $percent_to_pay;
							}
						}
					}
					if (!vikrentcar :: payTotal()) {
						$percentdeposit = vikrentcar :: getAccPerCent();
						if ($percentdeposit > 0) {
							$shouldpay = $shouldpay * $percentdeposit / 100;
						}
					}
					//check if the total amount paid is the same as the total order
					if(array_key_exists('tot_paid', $array_result)) {
						$shouldpay = round($shouldpay, 2);
						$totreceived = round($array_result['tot_paid'], 2);
						if($shouldpay != $totreceived) {
							//the amount paid is different than the total order
							//fares might have changed or the deposit might be different
							//Sending just an email to the admin that will check
							@mail($admail, JText :: _('VRCTOTPAYMENTINVALID'), JText::sprintf('VRCTOTPAYMENTINVALIDTXT', $rows[0]['id'], $totreceived." (".$array_result['tot_paid'].")", $shouldpay));
						}
					}
					//
					$q = "INSERT INTO `#__vikrentcar_busy` (`idcar`,`ritiro`,`consegna`,`realback`) VALUES('" . $rows[0]['idcar'] . "','" . $rows[0]['ritiro'] . "','" . $rows[0]['consegna'] . "','" . $realback . "');";
					$dbo->setQuery($q);
					$dbo->Query($q);
					$busynow = $dbo->insertid();
					$q = "UPDATE `#__vikrentcar_orders` SET `status`='confirmed', `idbusy`='" . $busynow . "'" . ($array_result['tot_paid'] ? ", `totpaid`='" . $array_result['tot_paid'] . "'" : "") . " WHERE `id`='" . $rows[0]['id'] . "';";
					$dbo->setQuery($q);
					$dbo->Query($q);
					//envia email
					//vikrentcar :: sendAdminMail($admail, JText :: _('VRRENTALORD'), $ftitle, $nowts, $rows[0]['custdata'], $carinfo['name'], $rows[0]['ritiro'], $rows[0]['consegna'], $pricestr, $optstr, $isdue, JText :: _('VRCOMPLETED'), $ritplace, $consegnaplace, $maillocfee, $payment['name'], $rows[0]['coupon']);
					//vikrentcar :: sendCustMail($rows[0]['custmail'], strip_tags($ftitle) . " " . JText :: _('VRRENTALORD'), $ftitle, $nowts, $rows[0]['custdata'], $carinfo['name'], $rows[0]['ritiro'], $rows[0]['consegna'], $pricestr, $optstr, $isdue, $viklink, JText :: _('VRCOMPLETED'), $ritplace, $consegnaplace, $maillocfee, $rows[0]['id'], $rows[0]['coupon']);
				} else {
					@mail($admail, JText :: _('VRPAYMENTNOTVER'), JText :: _('VRSERVRESP') . ":\n\n" . $array_result['log']);
				}
			}
		}
		return true;
	}

function notifypayment2($psid,$pts, $montoTotal) {


		//$psid = JRequest :: getString('sid', '', 'request');
		//$pts = JRequest :: getString('ts', '', 'request');
		$dbo = & JFactory :: getDBO();



		if (vikrentcar :: getDateFormat() == "%d/%m/%Y") {
			$df = 'd/m/Y';

		} else {
			$df = 'Y/m/d';

		}

		$pitemid='520';



		if (strlen($psid) && strlen($pts)) {

			$admail = vikrentcar :: getAdminMail();
			$q = "SELECT * FROM `#__vikrentcar_orders` WHERE `ts`='" . $dbo->getEscaped($pts) . "' AND `sid`='" . $dbo->getEscaped($psid) . "';";
			$dbo->setQuery($q);
			$dbo->Query($q);
			if ($dbo->getNumRows() > 0) {
				$rows = $dbo->loadAssocList();
				$rows[0]['admin_email'] = $admail;
				$exppay = explode('=', $rows[0]['idpayment']);
				$payment = vikrentcar :: getPayment($exppay[0]);
				require_once(JPATH_ADMINISTRATOR . DS ."components". DS ."com_vikrentcar". DS . "payments" . DS . $payment['file']);
				$obj = new vikRentCarPayment($rows[0]);
				$array_result = $obj->validatePayment();


				//if (true) {
				if ($array_result['verified'] == 1) {
					//valid payment
					$ritplace = (!empty ($rows[0]['idplace']) ? vikrentcar :: getPlaceName($rows[0]['idplace']) : "");
					$consegnaplace = (!empty ($rows[0]['idreturnplace']) ? vikrentcar :: getPlaceName($rows[0]['idreturnplace']) : "");
					$realback = vikrentcar :: getHoursCarAvail() * 3600;
					$realback += $rows[0]['consegna'];
					//send mails
					$ftitle = vikrentcar :: getFrontTitle();
					$nowts = time();
					$carinfo = vikrentcar :: getCarInfo($rows[0]['idcar']);
					$viklink = JURI :: root() . "index.php?option=com_vikrentcar&task=vieworder&sid=" . $psid . "&ts=" . $pts."&Itemid=".$pitemid;
					//vikrentcar 1.5
					if($rows[0]['hourly'] == 1) {
						$q = "SELECT * FROM `#__vikrentcar_dispcosthours_master` WHERE `id`='" . $rows[0]['idtar'] . "';";
					}else {
						$q = "SELECT * FROM `#__vikrentcar_dispcost_master` WHERE `id`='" . $rows[0]['idtar'] . "';";
					}
					//
					$dbo->setQuery($q);
					$dbo->Query($q);
					$tar = $dbo->loadAssocList();

					$isPaquete= vikrentcar:: getPriceName($tar[0]['idprice']);
					//vikrentcar 1.5
					if($rows[0]['hourly'] == 1) {
						foreach($tar as $kt => $vt) {
							$tar[$kt]['days'] = 1;
						}
					}
					//
					//vikrentcar 1.6
					$checkhourscharges = 0;
					$hoursdiff = 0;
					$ppickup = $rows[0]['ritiro'];
					$prelease = $rows[0]['consegna'];
					$secdiff = $prelease - $ppickup;
					$daysdiff = $secdiff / 86400;
					if (is_int($daysdiff)) {
						if ($daysdiff < 1) {
							$daysdiff = 1;
						}
					}else {
						if ($daysdiff < 1) {
							$daysdiff = 1;
							$checkhourly = true;
							$ophours = $secdiff / 3600;
							$hoursdiff = intval(round($ophours));
							if($hoursdiff < 1) {
								$hoursdiff = 1;
							}
						}else {
							$sum = floor($daysdiff) * 86400;
							$newdiff = $secdiff - $sum;
							$maxhmore = vikrentcar :: getHoursMoreRb() * 3600;
							if ($maxhmore >= $newdiff) {
								$daysdiff = floor($daysdiff);
							}else {
								$daysdiff = ceil($daysdiff);
								//vikrentcar 1.6
								$ehours = intval(round(($newdiff - $maxhmore) / 3600));
								$checkhourscharges = $ehours;
								if($checkhourscharges > 0) {
									$aehourschbasp = vikrentcar::applyExtraHoursChargesBasp();
								}
								//
							}
						}
					}
					if($checkhourscharges > 0 && $aehourschbasp == true) {
						$ret = vikrentcar::applyExtraHoursChargesCar($tar, $rows[0]['idcar'], $checkhourscharges, $daysdiff, false, true, true);
						$tar = $ret['return'];
						$calcdays = $ret['days'];
					}
					if($checkhourscharges > 0 && $aehourschbasp == false) {
						$tar = vikrentcar::extraHoursSetPreviousFareCar($tar, $rows[0]['idcar'], $checkhourscharges, $daysdiff, true);
						$tar = vikrentcar :: applySeasonsCar($tar, $rows[0]['ritiro'], $rows[0]['consegna'], $rows[0]['idplace']);
						$ret = vikrentcar::applyExtraHoursChargesCar($tar, $rows[0]['idcar'], $checkhourscharges, $daysdiff, true, true, true);
						$tar = $ret['return'];
						$calcdays = $ret['days'];
					}else {
						$tar = vikrentcar :: applySeasonsCar($tar, $rows[0]['ritiro'], $rows[0]['consegna'], $rows[0]['idplace']);
					}
					//
					$pricestr = vikrentcar :: getPriceName($tar[0]['idprice']) . ": " . vikrentcar :: sayCostPlusIva($tar[0]['cost'], $tar[0]['idprice']) . (!empty ($tar[0]['attrdata']) ? "\n" . vikrentcar :: getPriceAttr($tar[0]['idprice']) . ": " . $tar[0]['attrdata'] : "");
					$isdue = vikrentcar :: sayCostPlusIva($tar[0]['cost'], $tar[0]['idprice']);
					$currencyname = vikrentcar :: getCurrencyName();
					$optstr = "";
					if (!empty ($rows[0]['optionals'])) {
						$stepo = explode(";", $rows[0]['optionals']);
						foreach ($stepo as $oo) {
							if (!empty ($oo)) {
								$stept = explode(":", $oo);
								$q = "SELECT * FROM `#__vikrentcar_optionals` WHERE `id`='" . $dbo->getEscaped($stept[0]) . "';";
								$dbo->setQuery($q);
								$dbo->Query($q);
								if ($dbo->getNumRows() == 1) {
									$actopt = $dbo->loadAssocList();
									$realcost = (intval($actopt[0]['perday']) == 1 ? ($actopt[0]['cost'] * $rows[0]['days'] * $stept[1]) : ($actopt[0]['cost'] * $stept[1]));
									if (!empty ($actopt[0]['maxprice']) && $actopt[0]['maxprice'] > 0 && $realcost > $actopt[0]['maxprice']) {
										$realcost = $actopt[0]['maxprice'];
										if(intval($actopt[0]['hmany']) == 1 && intval($stept[1]) > 1) {
											$realcost = $actopt[0]['maxprice'] * $stept[1];
										}
									}
									$tmpopr = vikrentcar :: sayOptionalsPlusIva($realcost, $actopt[0]['idiva']);
									$isdue += $tmpopr;
									$optstr .= ($stept[1] > 1 ? $stept[1] . " " : "") . $actopt[0]['name'] . ": " . $tmpopr . " " . $currencyname . "\n";
								}
							}
						}
					}
					$maillocfee = "";
					if (!empty ($rows[0]['idplace']) && !empty ($rows[0]['idreturnplace'])) {
						$locfee = vikrentcar :: getLocFee($rows[0]['idplace'], $rows[0]['idreturnplace']);
						if ($locfee) {
							$locfeecost = intval($locfee['daily']) == 1 ? ($locfee['cost'] * $rows[0]['days']) : $locfee['cost'];
							$locfeewith = vikrentcar :: sayLocFeePlusIva($locfeecost, $locfee['idiva']);
							$isdue += $locfeewith;
							$maillocfee = $locfeewith;
						}
					}
					//vikrentcar 1.6 coupon
					$usedcoupon = false;
					$origisdue = $isdue;
					if(strlen($rows[0]['coupon']) > 0) {
						$usedcoupon = true;
						$expcoupon = explode(";", $rows[0]['coupon']);
						$isdue = $isdue - $expcoupon[1];
					}
					//
					if ($payment['charge'] > 0.00) {
						$shouldpay = $isdue;
						if($payment['ch_disc'] == 1) {
							//charge
							if($payment['val_pcent'] == 1) {
								//fixed value
								$shouldpay += $payment['charge'];
							}else {
								//percent value
								$percent_to_pay = $shouldpay * $payment['charge'] / 100;
								$shouldpay += $percent_to_pay;
							}
						}else {
							//discount
							if($payment['val_pcent'] == 1) {
								//fixed value
								$shouldpay -= $payment['charge'];
							}else {
								//percent value
								$percent_to_pay = $shouldpay * $payment['charge'] / 100;
								$shouldpay -= $percent_to_pay;
							}
						}
					}
					if (!vikrentcar :: payTotal()) {
						$percentdeposit = vikrentcar :: getAccPerCent();
						if ($percentdeposit > 0) {
							$shouldpay = $shouldpay * $percentdeposit / 100;
						}
					}
					//check if the total amount paid is the same as the total order
					if(array_key_exists('tot_paid', $array_result)) {
						$shouldpay = round($shouldpay, 2);
						$totreceived = round($array_result['tot_paid'], 2);
						if($shouldpay != $totreceived) {
							//the amount paid is different than the total order
							//fares might have changed or the deposit might be different
							//Sending just an email to the admin that will check
							@mail($admail, JText :: _('VRCTOTPAYMENTINVALID'), JText::sprintf('VRCTOTPAYMENTINVALIDTXT', $rows[0]['id'], $totreceived." (".$array_result['tot_paid'].")", $shouldpay));
						}
					}
					//
					/*$flag=0;
					$ope= $isdue - (int)$array_result['tot_paid'];
					if($ope==$isdue){

						$flag=1;


					}*/

					$q = "INSERT INTO `#__vikrentcar_busy` (`idcar`,`ritiro`,`consegna`,`realback`) VALUES('" . $rows[0]['idcar'] . "','" . $rows[0]['ritiro'] . "','" . $rows[0]['consegna'] . "','" . $realback . "');";
					$dbo->setQuery($q);
					$dbo->Query($q);
					$busynow = $dbo->insertid();
					$q = "UPDATE `#__vikrentcar_orders` SET `status`='confirmed', `idbusy`='" . $busynow . "'" . ($array_result['tot_paid'] ? ", `totpaid`='" . $isdue . "'" : ", `totpaid`='" . $isdue . "'") . " WHERE `id`='" . $rows[0]['id'] . "';";
					$dbo->setQuery($q);
					$dbo->Query($q);

					$userId=$rows[0]['ujid'];



					$saldo= vikrentcar :: getSaldoUser($userId);

					if (preg_match("/Paquete/i",$isPaquete)){

					//if($isPaquete=='Credito'){


							$concepto='Pago Paquete: '.$rows[0]['id'];

							if($rows[0]['hourly']){

								$ptar=$tar[0]['hours'];


							}else{

								$ptar=$tar[0]['days'];


							}

							$valorequivalenteMoney =vikrentcar::getMoneyEquivalenteHoras($rows[0]['hourly'],$carinfo['idcat'],$tar[0]['attrdata']);


							$numerohoras = ereg_replace("[^0-9]", "", $tar[0]['attrdata']);



							vikrentcar::savePaqueteHoras($rows,$numerohoras, $valorequivalenteMoney);

							$lid= vikrentcar::saveCredito($rows[0]['id'],$userId, $concepto, false);

							$guardarsaldonormal=false;

							$newSaldo = vikrentcar::calcularNuevoSaldo($rows[0]['id'], $rows[0]['ujid'], $lid, $guardarsaldonormal);
							//$newSaldo = vikrentcar::calcularNuevoSaldo($saldo, $userId);
							vikrentcar::saveSaldo($newSaldo, $userId,$guardarsaldonormal);

					}else{

							$concepto='Pago servicio: '.$rows[0]['id'];
							$lid= vikrentcar::saveDebito($rows[0]['id'],$userId, $concepto);

							$newSaldo = vikrentcar::calcularNuevoSaldo($rows[0]['id'], $rows[0]['ujid'], $lid);
							//$newSaldo = vikrentcar::calcularNuevoSaldo($saldo, $userId);
							vikrentcar::saveSaldo($newSaldo, $userId);


					}








					$ftitle = vikrentcar :: getFrontTitle();

					//$pricestr = vikrentcar :: getPriceName($tar[0]['idprice']) . ": " . vikrentcar :: sayCostPlusIva($tar[0]['cost'], $tar[0]['idprice']) . (!empty ($tar[0]['attrdata']) ? " " . $currencyname . "\n" . vikrentcar :: getPriceAttr($tar[0]['idprice']) . ": " . $tar[0]['attrdata'] : "");
					//$ritplace = (!empty ($pplace) ? vikrentcar :: getPlaceName($pplace) : "");
					//$consegnaplace = (!empty ($preturnplace) ? vikrentcar :: getPlaceName($preturnplace) : "");
					$currentUser = JFactory::getUser();
					$idUser= $currentUser->get('id') ;


					//enviar email a cliente
					$body= vikrentcar :: makeemail($rows[0]['custmail'], strip_tags($ftitle) . " " . JText :: _('VRORDNOL'), $ftitle, $nowts, $rows[0]['custdata'], $carinfo['name'], $ppickup, $prelease, $pricestr, $optstr, $isdue, $viklink, JText :: _('VRCOMPLETED'), $ritplace, $consegnaplace, $maillocfee,  $rows[0]['id'], $strcouponeff, '', '' ,$tar[0]['idprice']);
					$subject='Confirmación de Reserva: '.$rows[0]['id'];
					vikrentcar::enviarEmailAcymailing($idUser,LISTA_NOTIFICACION_RESERVAS,$subject,$body);

					$msgsms=vikrentcar::getInfoOrderSms($rows[0]['id'] , 2);

					vikrentcar::enviarsms($idUser,LISTA_NOTIFICACION_RESERVAS_SMS, $msgsms, $rows[0]['id']);



					/*$q="SELECT `user_id`, email FROM `#__vikrentcar_profiles` WHERE `num_doc`='".$carinfo['idCond']."'";
					$dbo->setQuery($q);
					$dbo->Query($q);
					$juserPiloto = $dbo->loadAssocList();*/

					$body= vikrentcar :: makeemail($juserPiloto[0]['email'], strip_tags($ftitle) . " " . JText :: _('VRORDNOL'), $ftitle, $nowts, $rows[0]['custdata'], $carinfo['name'], $ppickup, $prelease, $pricestr, $optstr, $isdue, 'piloto', JText :: _('VRCOMPLETED'), $ritplace, $consegnaplace, $maillocfee,  $rows[0]['id'], $strcouponeff, '', '', $tar[0]['idprice']);
					$subject='Confirmación de Reserva: '.$rows[0]['id'];
					$envioemailPiloto= vikrentcar::enviarEmailAcymailing($carinfo['idCond'],'',$subject,$body);
					if(!$envioemailPiloto){


						//se envia email que hubo un error enviando correo al conductor
					$body= vikrentcar :: makeemail($useremail, strip_tags($ftitle) . " " . JText :: _('VRORDNOL'), $ftitle, $nowts, $custdata, $carinfo['name'], $ppickup, $prelease, $pricestr, $optstr, $isdue, 'error', JText :: _('VRINATTESA'), $ritplace, $consegnaplace, $maillocfee, $neworderid, $strcouponeff,  '' ,'' , $tar[0]['idprice']);
				    $subject ='Error Notificacion Reserva: '.$neworderid;
					$envioemailPiloto= vikrentcar::enviarEmailAcymailing('',LISTA_NOTIFICACION_RESERVAS,$subject,$body);



					}

					$msgsms=vikrentcar::getInfoOrderSms($rows[0]['id'], 2);

					vikrentcar::enviarsms($carinfo['idCond'],'', $msgsms, $rows[0]['id']);



					//envia email
					//vikrentcar :: sendAdminMail($admail, JText :: _('VRRENTALORD'), $ftitle, $nowts, $rows[0]['custdata'], $carinfo['name'], $rows[0]['ritiro'], $rows[0]['consegna'], $pricestr, $optstr, $isdue, JText :: _('VRCOMPLETED'), $ritplace, $consegnaplace, $maillocfee, $payment['name'], $rows[0]['coupon']);
					//vikrentcar :: sendCustMail($rows[0]['custmail'], strip_tags($ftitle) . " " . JText :: _('VRRENTALORD'), $ftitle, $nowts, $rows[0]['custdata'], $carinfo['name'], $rows[0]['ritiro'], $rows[0]['consegna'], $pricestr, $optstr, $isdue, $viklink, JText :: _('VRCOMPLETED'), $ritplace, $consegnaplace, $maillocfee, $rows[0]['id'], $rows[0]['coupon']);
				} else {
					@mail($admail, JText :: _('VRPAYMENTNOTVER'), JText :: _('VRSERVRESP') . ":\n\n" . $array_result['log']);
				}
			}
		}
		return true;
	}

	function getSaldoUser($userid){


	//$dato =vikrentcar::evalDisponibilidad();
	$dbo= JFactory::getDBO();
	$q = "SELECT `saldo` FROM `#__vikrentcar_profiles` WHERE `user_id`='" . $dbo->getEscaped($userid) . "';";
	$dbo->setQuery($q);
	$dbo->Query($q);
	$saldo = $dbo->loadResult();


	return $saldo;


}

function saveCredito($idorder, $userid, $concepto){

	$config =& JFactory::getConfig();
	$dbo= JFactory::getDBO();
	$dateNow = new DateTime(null, new DateTimeZone($config->getValue('config.offset')));

	$mysqlDateTime = $dateNow->format(DateTime::ISO8601);





	$q="SELECT * FROM `#__vikrentcar_orders` WHERE `id`='".$idorder."';";
	$dbo->setQuery($q);
	$dbo->Query($q);
	if ($dbo->getNumRows() == 1) {

		$rows = $dbo->loadAssocList();

	}


	$q="INSERT INTO `#__vikrentcar_payments_users` (`fecha`,`concepto`,`credito`,`id_user`) VALUES('".$mysqlDateTime."','".$concepto."','".$rows[0]['totpaid']."','".$userid."');";
	$dbo->setQuery($q);
	$dbo->Query($q);
	$lid = $dbo->insertid();

	return $lid;



}

function saveDebito($idorder, $userid, $concepto){

	$config =& JFactory::getConfig();
	$dbo= JFactory::getDBO();
	$dateNow = new DateTime(null, new DateTimeZone($config->getValue('config.offset')));

	$mysqlDateTime = $dateNow->format(DateTime::ISO8601);




	$q="SELECT * FROM `#__vikrentcar_orders` WHERE `id`='".$idorder."';";
	$dbo->setQuery($q);
	$dbo->Query($q);
	if ($dbo->getNumRows() == 1) {

		$rows = $dbo->loadAssocList();

	}


	$q="INSERT INTO `#__vikrentcar_payments_users` (`fecha`,`concepto`,`debito`,`id_user`) VALUES('".$mysqlDateTime."','".$concepto."','".$rows[0]['totpaid']."','".$userid."');";
	$dbo->setQuery($q);
	$dbo->Query($q);
	$lid = $dbo->insertid();

	return $lid;



}


function calcularNuevoSaldo($idorder, $userid, $lid){

	$dbo= JFactory::getDBO();
	//obtiene saldo actul
	$saldo= vikrentcar::getSaldoUser($userid);

	$q="SELECT * FROM `#__vikrentcar_payments_users` WHERE `id`='".$lid."';";
	$dbo->setQuery($q);
	$dbo->Query($q);
	if ($dbo->getNumRows() == 1) {

		$dpay = $dbo->loadAssocList();

	}

	$newSaldo= $saldo+$dpay[0]['credito']-$dpay[0]['debito'];

	return $newSaldo;

}

function saveSaldo($newSaldo, $idUser){

	$dbo= JFactory::getDBO();
	$q="UPDATE  `#__vikrentcar_profiles` SET  ` saldo`  ='".$newSaldo."' WHERE `user_id`='". $idUser."';";
	$dbo->setQuery($q);
	$dbo->Query($q);


}

function pruebascode2(){





		$db = &JFactory::getDBO();
		$query = "SELECT  cars.idCond FROM  #__vikrentcar_cars  as cars";
		$db->setQuery($query);
		$db->Query($query);
		$idconds = $db->loadAssocList();



		foreach ($idconds as $key => $value) {

			$query = "SELECT  p.user_id FROM  #__vikrentcar_profiles  as p WHERE p.num_doc=".$value['idCond'];
			$db->setQuery($query);
			$db->Query($query);

			$idUser = $db->loadResult();

			$q="UPDATE  `#__vikrentcar_cars` SET  `idCond`  ='".$idUser."' WHERE `idCond`='". $value['idCond']."';";
			$db->setQuery($q);
			$db->Query($q);





		}


		//vikrentcar::savePaqueteHoras(1, 1, $test);


}


	function ajaxlocopentime() {
		$pidloc = JRequest :: getInt('idloc', '', 'request');
		$ppickdrop = JRequest :: getString('pickdrop', '', 'request');
		$dbo = & JFactory :: getDBO();
		$ret = array();
		$q="SELECT `opentime` FROM `#__vikrentcar_places` WHERE `id`='".$pidloc."';";
		$dbo->setQuery($q);
		$dbo->Query($q);
		$opentime = $dbo->loadResult();
		if(strlen($opentime) > 0) {
			//load location time
			$parts = explode("-", $opentime);
			$opent=vikrentcar::getHoursMinutes($parts[0]);
			$closet=vikrentcar::getHoursMinutes($parts[1]);
			if ($opent != $closet) {
				$i = $opent[0];
				$imin = $opent[1];
				$j = $closet[0];
			}else {
				$i = 0;
				$imin = 0;
				$j = 23;
			}
		}else {
			//load global time
			$timeopst = vikrentcar :: getTimeOpenStore();
			if (is_array($timeopst) && $timeopst[0] != $timeopst[1]) {
				$opent = vikrentcar :: getHoursMinutes($timeopst[0]);
				$closet = vikrentcar :: getHoursMinutes($timeopst[1]);
				$i = $opent[0];
				$imin = $opent[1];
				$j = $closet[0];
			}else {
				$i = 0;
				$imin = 0;
				$j = 23;
			}
		}
		$hours = "";
		while ($i <= $j) {
			if ($i < 10) {
				$i = "0" . $i;
			} else {
				$i = $i;
			}
			$hours .= "<option value=\"" . $i . "\">" . $i . "</option>\n";
			$i++;
		}
		$minutes = "";
		for ($i = 0; $i < 60; $i += 15) {
			if ($i < 10) {
				$i = "0" . $i;
			} else {
				$i = $i;
			}
			$minutes .= "<option value=\"" . $i . "\"".((int)$i == $imin ? " selected=\"selected\"" : "").">" . $i . "</option>\n";
		}
		$suffix = $ppickdrop == 'pickup' ? 'pickup' : 'release';

		$ret['hours'] = '<select name="'.$suffix.'h">'.$hours.'</select>';
		$ret['minutes'] = '<select name="'.$suffix.'m">'.$minutes.'</select>';

		echo json_encode($ret);
		exit;
	}

	function ajaxCategories(){
		$idplace = JRequest :: getInt('idplace', '0', 'request');
		$dbo = & JFactory :: getDBO();
		//Categorias o Tipos de Servicios
		//
	    $vrcats="";

	    $lang = JFactory::getLanguage();

		$code_lang= $lang->getTag();

    	$q="SELECT DISTINCT c.* FROM `#__vikrentcar_categories` c
			inner join `#__vikrentcar_cars` s
			ON CONCAT(c.id, ';') LIKE s.idcat
			WHERE s.idplace like CONCAT('%','$idplace',';%') AND s.avail=1
			ORDER BY c.`order` ASC;";




		$dbo->setQuery($q);
		$dbo->Query($q);
		if ($dbo->getNumRows() > 0) {
			$categories = $dbo->loadAssocList();
			foreach($categories as $cat){


				$vrcats.="<option desc=\"".$cat['descr']."\"  value=\"".$cat['id']."\">".JText::_('VRNAMECAT'.$cat['id'])."</option>\n";
			}
		}

	    echo $vrcats;
	    exit;
	}








	function ajaxlocopentimemin() {
		$pidloc = JRequest :: getInt('idloc', '', 'request');
		$ppickdrop = JRequest :: getString('pickdrop', '', 'request');
		$dbo = & JFactory :: getDBO();
		$ret = array();
		$q="SELECT `opentime` FROM `#__vikrentcar_places` WHERE `id`='".$pidloc."';";
		$dbo->setQuery($q);
		$dbo->Query($q);
		$opentime = $dbo->loadResult();
		if(strlen($opentime) > 0) {
			//load location time
			$parts = explode("-", $opentime);
			$opent=vikrentcar::getHoursMinutes($parts[0]);
			$closet=vikrentcar::getHoursMinutes($parts[1]);
			if ($opent != $closet) {
				$i = $opent[0];
				$imin = $opent[1];
				$j = $closet[0];
			}else {
				$i = 0;
				$imin = 0;
				$j = 23;
			}
		}else {
			//load global time
			$timeopst = vikrentcar :: getTimeOpenStore();
			if (is_array($timeopst) && $timeopst[0] != $timeopst[1]) {
				$opent = vikrentcar :: getHoursMinutes($timeopst[0]);
				$closet = vikrentcar :: getHoursMinutes($timeopst[1]);
				$i = $opent[0];
				$imin = $opent[1];
				$j = $closet[0];
			}else {
				$i = 0;
				$imin = 0;
				$j = 23;
			}
		}
		$hours = "";
		while ($i <= $j) {
			if ($i < 10) {
				$i = "0" . $i;
			} else {
				$i = $i;
			}
			$hours .= "<option value=\"" . $i . "\">" . $i . "</option>\n";
			$i++;
		}
		$minutes = "";
		for ($i = 0; $i < 60; $i += 15) {
			if ($i < 10) {
				$i = "0" . $i;
			} else {
				$i = $i;
			}
			$minutes .= "<option value=\"" . $i . "\"".((int)$i == $imin ? " selected=\"selected\"" : "").">" . $i . "</option>\n";
		}
		$suffix = $ppickdrop == 'pickup' ? 'pickup' : 'release';

		$ret['hours'] = $hours;
		$ret['minutes'] = $minutes;




		echo json_encode($ret);
		exit;
	}


function pagosOnline(){

	$descripcion = JRequest :: getString('descripcion', '', 'request');
	$prueba = JRequest :: getString('prueba', '', 'request');
	$usuarioId = JRequest :: getString('usuarioId', '', 'request');
	$refVenta = JRequest :: getString('refVenta', '', 'request');
	$item_number = JRequest :: getString('item_number', '', 'request');
	$valor = JRequest :: getString('valor', '', 'request');
	$iva = JRequest :: getString('iva', '', 'request');
	$baseDevolucionIva = JRequest :: getString('baseDevolucionIva', '', 'request');
	$moneda = JRequest :: getString('moneda', '', 'request');
	$url_confirmacion = JRequest :: getString('url_confirmacion', '', 'request');
	$url_respuesta = JRequest :: getString('url_respuesta', '', 'request');
	$emailComprador = JRequest :: getString('emailComprador', '', 'request');
	$firma = JRequest :: getString('firma', '', 'request');


		//echo $item_number;

		$ch = curl_init();
		//$mainframe->redirect('https://gatewaylap.pagosonline.net/ppp-web-gateway?descripcion='.$descripcion.'&prueba='.$prueba.'&usuarioId='.$usuarioId.'&refVenta='.$refVenta.'&item_number='.$item_number.'&valor='.$valor.'&iva='.$iva.'&baseDevolucionIva='.$baseDevolucionIva.'&moneda='.$moneda.'&url_confirmacion='.$url_confirmacion.'&url_respuesta='.$url_respuesta.'&emailComprador='.$emailComprador.'&firma='.$firma);
		//curl_setopt($ch, CURLOPT_URL, 'https://gatewaylap.pagosonline.net/ppp-web-gateway?descripcion='.$descripcion.'&prueba='.$prueba.'&usuarioId='.$usuarioId.'&refVenta='.$refVenta.'&item_number='.$item_number.'&valor='.$valor.'&iva='.$iva.'&baseDevolucionIva='.$baseDevolucionIva.'&moneda='.$moneda.'&url_confirmacion='.$url_confirmacion.'&url_respuesta='.$url_respuesta.'&emailComprador='.$emailComprador.'&firma='.$firma);
		curl_setopt($ch, CURLOPT_URL, JURI::root().'pruebasPagosOnline.php?descripcion='.$descripcion.'&prueba='.$prueba.'&usuarioId='.$usuarioId.'&refVenta='.$refVenta.'&item_number='.$item_number.'&valor='.$valor.'&iva='.$iva.'&baseDevolucionIva='.$baseDevolucionIva.'&moneda='.$moneda.'&url_confirmacion='.$url_confirmacion.'&url_respuesta='.$url_respuesta.'&emailComprador='.$emailComprador.'&firma='.$firma);

		$resultado = curl_exec ($ch);
		curl_close($ch);



}





function pruebascode(){




	$db= JFactory::getDBO();
	$order=75;
	$query = 'SELECT  o.custdata as info_Cliente, c.name as Nombre_Servicio, c.info as infocar, c.placa as Pl, o.ritiro as Fecha_Recogida, o.consegna  as Fecha_Entrega, pcond.name as nameCond, pcond.lname as lnameCond, pcond.movil as movilCond , o.idCond as CondOrder FROM #__vikrentcar_orders as o LEFT JOIN #__vikrentcar_profiles as p on o.ujid = p.user_id LEFT JOIN #__vikrentcar_cars as c on c.id = o.idcar  LEFT JOIN #__vikrentcar_profiles as pcond on pcond.num_doc = c.idCond WHERE o.id = '.intval($order). ';';
	//$query = 'SELECT o.id as Num_Orden, o.status as Estado, o.custdata as addInfo, o.ritiro as Fecha_Recogida, o.consegna  as Fecha_Entrega  FROM #__vikrentcar_orders as o LEFT JOIN #__vikrentcar_profiles as p on o.ujid = p.user_id LEFT JOIN #__vikrentcar_cars as c on c.id = o.idcar WHERE o.id = '.intval($order). ';';
	$db->setQuery($query);
	$db->query();
	$dataorder = $db->loadAssocList();

	//valida si el servicio tiene conductor asignado
	//esto para servicios como el de mensajeria que la orden no tiene
	if(empty($dataorder[0]['Pl'])){

		$idConductor= $dataorder[0]['CondOrder'];

		$query = 'SELECT  pcond.name as nameCond, pcond.lname as lnameCond , c.placa as placa, c.info as infocar, pcond.movil as movil FROM #__vikrentcar_cars as c  LEFT JOIN #__vikrentcar_profiles as pcond on pcond.num_doc =  '.intval($idConductor). ' WHERE c.idCond = '.intval($idConductor). ';';
		$db->setQuery($query);
		$db->query();
		$dataorder2 = $db->loadAssocList();
		$dataorder[0]['Pl']=$dataorder2[0]['placa'];
		$dataorder[0]['movilCond']=$dataorder2[0]['movil'];
		$dataorder[0]['infocar']=$dataorder2[0]['infocar'];
		$dataorder[0]['nameCond']=$dataorder2[0]['nameCond'];
		$dataorder[0]['lnameCond']=$dataorder2[0]['lnameCond'];
	}
	if (vikrentcar :: getDateFormat() == "%d/%m/%Y") {
		$df = 'd/m/Y';
	} else {
		$df = 'Y/m/d';
	}

	//$fechaprogramada= date('l jS \of F Y',$dataorder[0]['ritiro']);
	//echo $fechaprogramada.'</br>';

	$arrayAddInfo= split("\n", $dataorder[0]['info_Cliente']);

	foreach ($arrayAddInfo as $key => $value) {

		$arrayInfoCliente= split(":", $value);
		//obtiene nombre
		if($arrayInfoCliente[0]=='Nombre'){


			$names= split(" ", trim($arrayInfoCliente[1]));

			$mensaje=  $names[0];


		}

		if($arrayInfoCliente[0]=='Apellido'){
			//concatena apellido
			//
			$lnames= split(" ", trim($arrayInfoCliente[1]));
			$mensaje.= ' '.$lnames[0];
			$arregloSMS['pass']=$mensaje;

		}
		//obtiene Lugar de Recogida
		if($arrayInfoCliente[0]=='Lugar de Recogida'){

			$mensaje=  $arrayInfoCliente[1];
			$arregloSMS['Dest']=substr($mensaje, 0, 20);
		}

		if($arrayInfoCliente[0]=='Vuelo'){
			$mensaje=  $arrayInfoCliente[1];
			$arregloSMS['Vuelo']=substr($mensaje, 0, 6);

		}


	}


	$arrayAddInfo2= split("\n", $dataorder[0]['infocar']);

	foreach ($arrayAddInfo2 as $key => $value) {




		if(preg_match("/Color/i",$value)){
			$arrayInfoCliente2= split(":", $value);
			//echo $key.' -'.' si tiene color'.$arrayInfoCliente2[1];
			$convertida=strip_tags($arrayInfoCliente2[1]);
			$arregloSMS['Color']=substr($convertida, 0, 7);
		}



	}


	$arregloSMS['Hr']= date( 'H:i', $dataorder[0]['Fecha_Recogida']);
	$names= split(" ", trim($dataorder[0]['nameCond']));

	if(count($names)==2){
		$arregloSMS['Cond']=$names[0].' '.$names[1];

	}else{

		$arregloSMS['Cond']=$names[0].' '.$names[1];
	}

	$lnames= split(" ", trim($dataorder[0]['lnameCond']));
	if(count($lnames)==2){
		$arregloSMS['Cond']=$names[0].' '.$lnames[0];

	}else{

		$arregloSMS['Cond']=$names[0].' '.$lnames[0];
	}
	//$arregloSMS['Cond']=$dataorder[0]['conductor'];
	$arregloSMS['Cel']=$dataorder[0]['movilCond'];

	$namesServ= split(" ", trim($dataorder[0]['Nombre_Servicio']));
	print_r($namesServ);
	//$nameservicio=$namesServ[0];

	if(count($namesServ)>2){
		//detecta el valor numerico del modelo del vehiculo para mostarlo
		foreach ($namesServ as $key => $value) {

			if(ctype_digit($value)){
			$nameservicio.=' '.$value;
			}
			if(!preg_match('/Servicio/i', $value)){

			$nameservicio.=$value.' ';

			}


		}

		$nameservicio=trim($nameservicio);
	}else{

		$arregloSMS['Veh']=substr($dataorder[0]['Nombre_Servicio'], 0, 15);
	}
	$arregloSMS['Veh']=substr($nameservicio, 0, 15);
	//$arregloSMS['Veh']=substr($dataorder[0]['Nombre_Servicio'], 0, 20);
	$arregloSMS['Pl']=substr($dataorder[0]['Pl'], 0, 7);


	$lang            = JFactory::getLanguage();
	$currentLanguaje = $lang->getName();

	if(preg_match('[Spanish]', $currentLanguaje)){
		$lenguaje='1';
	}else{
		$lenguaje='0';
	}
	$fecha=date($dataorder[0]['Fecha_Recogida']);
	//echo $dataorder[0]['Fecha_Recogida'];
	$fechastr= self::convertDateToString($fecha,$lenguaje);
	echo $fechastr;
	echo 'pass:'.trim($arregloSMS['pass']);
	echo 'Hr:'.trim($arregloSMS['Hr']);
	echo 'Cond:'.trim($arregloSMS['Cond']);
	echo 'Cel:'.trim($arregloSMS['Cel']);
	echo 'Veh:'.trim($arregloSMS['Veh']);
	echo 'Color:'.trim($arregloSMS['Color']);
	echo 'Pl:'.trim($arregloSMS['Pl']);
	echo 'Dest:'.trim($arregloSMS['Dest']);
	echo 'Vuelo:'.trim($arregloSMS['Vuelo']);




}

function convertDateToString($fecha, $lang){


	if($lang=='0'){
	$mthNames = array("", "Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec");
	$dayNames =  array("","Mon","Tue","Wed","Thu","Fri","Sat", "Sun");
	}else{
	$mthNames =  array("","Ene","Feb","Mar","Abr","May","Jun","Jul","Ago","Sep","Oct","Nov","Dic");
	$dayNames =  array("","Lun","Mar","Mier","Jue","Vie","Sab", "Dom");
	}


	$curr_day=date('N',$fecha);
	$curr_date=date('d/m/Y',$fecha);
	//$curr_date=date('j');
	//$curr_month=(int)date('m');
	//$curr_year='2014';
	if($lang=='0'){
	 return ($dayNames[$curr_day] . " ".$curr_date);
	}else{
	 return ($dayNames[$curr_day] . " ".$curr_date);
	}




}


function removerOrden () {

	$d = JRequest :: getInt('idorder', '', 'request');




		$dbo = & JFactory :: getDBO();
		$moveoldor=(vikrentcar::saveOldOrders() ? true : false);

			$q="SELECT * FROM `#__vikrentcar_orders` WHERE `id`='".$dbo->getEscaped($d)."';";
			$dbo->setQuery($q);
			$dbo->Query($q);
			if ($dbo->getNumRows() == 1) {
				$rows = $dbo->loadAssocList();
				if (!empty($rows[0]['idbusy'])) {
					$q="DELETE FROM `#__vikrentcar_busy` WHERE `id`='".$rows[0]['idbusy']."';";
					$dbo->setQuery($q);
					$dbo->Query($q);
				}
				if ($moveoldor) {
					$q="INSERT INTO `#__vikrentcar_oldorders` (`tsdel`,`custdata`,`ts`,`status`,`idcar`,`days`,`ritiro`,`consegna`,`idtar`,`optionals`,`custmail`,`sid`,`idplace`,`idreturnplace`,`totpaid`,`hourly`,`coupon`) VALUES('".time()."','".$dbo->getEscaped($rows[0]['custdata'])."','".$rows[0]['ts']."','".$rows[0]['status']."','".$rows[0]['idcar']."','".$rows[0]['days']."','".$rows[0]['ritiro']."','".$rows[0]['consegna']."','".$rows[0]['idtar']."','".$rows[0]['optionals']."','".$rows[0]['custmail']."','".$rows[0]['sid']."','".$rows[0]['idplace']."','".$rows[0]['idreturnplace']."','".$rows[0]['totpaid']."','".$rows[0]['hourly']."','".$dbo->getEscaped($rows[0]['coupon'])."');";
					$dbo->setQuery($q);
					$dbo->Query($q);

					//se retorna valor de credito si la orden es removida y si tiene credito de horas
					$q="SELECT `credito` ,`id_user`  FROM `#__vikrentcar_creditos` WHERE `idorder`='".$dbo->getEscaped($d)."';";
					$dbo->setQuery($q);
					$dbo->Query($q);
					$credito = $dbo->loadAssocList();


					//captura el saldo del credito


					$q="SELECT `credito` FROM `#__vikrentcar_profiles` WHERE `user_id`='".$credito[0]['id_user']."';";
					$dbo->setQuery($q);
					$dbo->Query($q);
					$saldoAntCredito = $dbo->loadAssocList();
					//calcula el nuevo salto
					$idprice=  vikrentcar::getIdPrice($rows[0]['idtar']);
					$flagPaq = vikrentcar:: getPriceName($idprice,$dbo->getEscaped($d));
					if($flagPaq=='Credito'){
						$nuevoCredito=$saldoAntCredito[0]['credito']-$credito[0]['credito'];
					}else{

						$nuevoCredito=$saldoAntCredito[0]['credito']-$credito[0]['credito'];
					}
					//gaurda el nuevo saldo en la tabla de creditos del usuario
					$q="UPDATE  `#__vikrentcar_profiles` SET  `credito`  ='".$nuevoCredito."' WHERE `user_id`='". $credito[0]['id_user']."';";
					$dbo->setQuery($q);
					$dbo->Query($q);

					$q = "INSERT INTO `#__vikrentcar_creditos` (`id_user`,`idorder`,`credito`) VALUES('".$credito[0]['id_user']."','".$dbo->getEscaped($d)."','".(-1)*$credito[0]['credito']."');";
					$dbo->setQuery($q);
					$dbo->Query($q);



				}
				$q="DELETE FROM `#__vikrentcar_orders` WHERE `id`='".$rows[0]['id']."';";
				$dbo->setQuery($q);
				$dbo->Query($q);
				if( $dbo->getErrorMsg()==''){

				echo '<div class="successmade"> Orden: '.$rows[0]['id'].' '.JText::_('MSGEBORRADO').'</div>';

				$ftitle = vikrentcar :: getFrontTitle($rows[0]['ts']);
				$tarInfo = vikrentcar :: getTarInfo($rows[0]['idtar'],$rows[0]['hourly']);
				$optstr= vikrentcar::getOptInfo($rows[0]['optionals'],$rows[0]['days'], $tarInfo[0]['cost'] );
				$pricestr = vikrentcar :: getPriceName($tarInfo[0]['idprice']) . ": " . vikrentcar :: sayCostPlusIva($tarInfo[0]['cost'], $tarInfo[0]['idprice']) . (!empty ($tarInfo[0]['attrdata']) ? " " . $currencyname . "\n" . vikrentcar :: getPriceAttr($tarInfo[0]['idprice']) . ": " . $tarInfo[0]['attrdata'] : "");
				$carinfo = vikrentcar :: getCarInfo($rows[0]['idcar']);
				$hmess= vikrentcar::crearEmailCancelacion( '1','', $ftitle, $rows[0]['ts'],$rows[0]['custdata'], $carinfo['name'], $rows[0]['ritiro'], $rows[0]['consegna'], $pricestr, $optstr, $rows[0]['totpaid'], $link,'cancelado', $place = "", $returnplace = "", $maillocfee = "", $orderid = $rows[0]['id'], $strcouponeff = "");
				$subject='Notificacion Cancelación de Servicios: '.$rows[0]['id'];
				$body= $hmess;
				//vikrentcar::enviarEmailAcymailing($rows[0]['ujid'],LISTA_NOTIFICACION_CANCELACIONES,$subject,$body);
				//$msgsms=vikrentcar::getInfoOrderSms($rows[0]['id']);
				//vikrentcar::enviarsms($idUser,LISTA_NOTIFICACION_CANCELACIONES_SMS, $msgsms);

				/*$q="SELECT `user_id` FROM `#__vikrentcar_profiles` WHERE `num_doc`='".$carinfo['idCond']."'";
				$dbo->setQuery($q);
				$dbo->Query($q);
				$juserPiloto = $dbo->loadAssocList();*/

				//se envia email al piloto confirmando la cancelacion de servicio
				//vikrentcar::enviarEmailAcymailing($juserPiloto[0]['user_id'],'',$subject,$body);


				}else{
				echo '<div class="err"> Error Orden: '.$rows[0]['id'].'</div>';
				}

			}


		//$app =& JFactory::getApplication();
		//$app->enqueueMessage(JText::_('VRMESSDELBUSY'));


}



function pagosonlineRedeban(){


		$imagenajax= JURI::root()."images/ico/progressbar.gif";
		$montoTotal = JRequest :: getString('montoTotal', '', 'request');
		$baseImpuesto = JRequest :: getString('baseImpuesto', '', 'request');
		$montoimpuesto = JRequest :: getString('montoimpuesto', '', 'request');
		$idOrder = JRequest :: getString('idorder', '', 'request');
		$idTransaccionTerminal = JRequest :: getString('idTransaccionTerminal', '', 'request');
		$numeroFactura = JRequest :: getString('numeroFactura', '', 'request');
		$idTransaccionActual = JRequest :: getString('idTransaccionActual', '', 'request');
		$newPay = JRequest :: getString('newPay', '', 'request');
		$montoadicional= $montoTotal- $newPay;
		$dbo = & JFactory :: getDBO();

		$urlpagos= JURI::root().'index.php?option=com_vikrentcar&task=consultarEstadoPagoRedeban';

		//$app->redirect("index.php?option=com_vikrentcar&task=vieworder&format=raw&sid=" . $sid . "&ts=" . $nowts . (!empty ($pitemid) ? "&Itemid=" . $pitemid : ""));


		try{

			$objClienteWS = new SoapClient("https://www.pagosrbm.com/GlobalPayServicios/GlobalPayServicioDePago/GlobalPayServicioDePago.wsdl",  array("trace" => 1));

			$config =& JFactory::getConfig();
	        $dateNow = new DateTime(null, new DateTimeZone($config->getValue('config.offset')));

	        $mysqlDateTime = $dateNow->format(DateTime::ISO8601);



	        $q= "INSERT INTO `#__vikrentcar_transaccion` (`fecha`, `id_factura`) VALUES('".$mysqlDateTime."','".$numeroFactura."')";
	        $dbo->setQuery($q);
	        $dbo->Query($q);
	        $idTransaccionTerminal = $dbo->insertid();



	        $requestParams = array(
	        "credenciales" => array(
	        "idUsuario" => IDUSUARIO,
	        "clave" =>CLAVE
	        ),
	        "cabeceraSolicitud" => array(
	         "infoPuntoInteraccion" => array(
	         "tipoTerminal" =>TIPOTERMINAL,
	         "idTerminal" => IDTERMINAL1,
	         "idAdquiriente" => IDADQUIRIENTE,
	         "idTransaccionTerminal" => $idTransaccionTerminal,
	        ),
	        ),
	        "infoCompra" => array(
	         "numeroFactura" => $numeroFactura,
	         "montoTotal" => $montoTotal,
	         "infoImpuestos" =>array(
	         "tipoImpuesto" => "IVA",
	         "monto" => $montoimpuesto,
	         "baseImpuesto" => $baseImpuesto,
	         ),
	        /*"montoDetallado" =>array(
	         "tipoMontoDetallado" => "MontoAdicional",
	         "monto" => $montoadicional,
	         ),*/
	         "infoComercio" =>array(
	         "informacionComercio" => "pilotoautomatico",
	         "informacionAdicional" => "pilotoautomaticopruebas",
	         ),
	         ),
	         );


			 $resultado = $objClienteWS->IniciarTransaccionDeCompra($requestParams);



	         $idTransaccionActual=$resultado->infoTransaccionResp->idTransaccionActual;

	         $q="UPDATE  #__vikrentcar_transaccion  SET id_trandaccionActual='".$idTransaccionActual. "'  WHERE id='".$idTransaccionTerminal."';";

             $dbo->setQuery($q);
             $dbo->query();

		     //$id_transaccion = $dbo->insertid();
	         //error en el envio de datos

	         //error tecnico
	         //
	         $codRespuesta=$resultado->infoRespuesta->codRespuesta;
	         $descRespuesta=$resultado->infoRespuesta->descRespuesta;
	         $estadoTransaccion=$resultado->infoRespuesta->estado;

	        /* if((int)$codRespuesta!=0){

	         	$app =& JFactory::getApplication();
	        	$app->enqueueMessage('Error: '.$codRespuesta.','. $descRespuesta.', Estado Transacción:'.$estadoTransaccion);



	         }*/



	         $funcion=$estadoTransaccion;

	         ?>

	        <div class='pagos'>
		    <input type="hidden" name="pagos" value="1" />
		    <input type="hidden" name="ptipoTerminal" value="<?php echo TIPOTERMINAL;  ?>" />
		    <input type="hidden" name="pidTerminal" value="<?php echo IDTERMINAL1;  ?>" />
		    <input type="hidden" name="pidAdquiriente" value="<?php echo IDADQUIRIENTE;  ?>" />
		    <input type="hidden" name="pidTransaccionTerminal" value="<?php echo $idTransaccionTerminal;  ?>" />
		    <input type="hidden" name="pidTransaccionActual" value="<?php echo $idTransaccionActual;  ?>" />
		    <input type="hidden" name="pnumeroFactura" value="<?php echo $numeroFactura;  ?>" />
		    <input type="hidden" name="pmontoTotal" value="<?php echo $montoTotal;  ?>" />
		    <input type="hidden" name="pfuncion" value="<?php echo $funcion;  ?>" />
		    <input type="hidden" name="pdescRespuesta" value="<?php echo $descRespuesta;  ?>" />
		    <input type="hidden" name="pcodRespuesta" value="<?php echo $codRespuesta;  ?>" />



			</div>

			<script type="text/javascript">




			jQuery(document).ready(function() {

				debugger;

			        var imagenajax= "<?php echo $imagenajax;?>";
			        var tipoTerminal="<?php echo TIPOTERMINAL;  ?>";
			        var idTerminal="<?php echo IDTERMINAL1;  ?>";
			        var idAdquiriente="<?php echo IDADQUIRIENTE;  ?>";
			        idTransaccionTerminal="<?php echo $idTransaccionTerminal;  ?>";
			        idTransaccionActual="<?php echo $idTransaccionActual;  ?>";
			        numeroFactura="<?php echo $numeroFactura;  ?>";
			        montoTotal="<?php echo $montoTotal;  ?>";


			        var funcion="<?php echo $funcion;  ?>";

			        var descRespuesta="<?php echo $descRespuesta;  ?>";
			        var codRespuesta="<?php echo $codRespuesta;  ?>";



			        console.log('enlace para redeban: '+'https://www.pagosrbm.com/GlobalPayWeb/gp/realizarPago.xhtml?idTerminal='+idTerminal+'&idTransaccion=<?php echo $idTransaccionActual; ?>');

			        //www.pagosrbm.com:8443/GlobalPayWeb/gp/realizarPago.xhtml?idTerminal=ESB10071&idTransaccion=211403916475188

			         //funciones lightbox redebank

			        var eventMethod = window.addEventListener ? "addEventListener" : "attachEvent";
			        var eventer = window[eventMethod];
			        var messageEvent = eventMethod == "attachEvent" ? "onmessage" : "message";

			        SqueezeBox.addEvent('onClose', function() {
			        	debugger;
			            jQuery('input[name="idTransaccionActual"]').val(idTransaccionActual);
			        });



			            eventer(messageEvent,function(e) {
			                //alert(e.origin);
			                //alert(e.data);
			                    if(e.data=='Finalizacion'){

			                        debugger;

			                      //parent.jQuery.fn.colorbox.close();
			                      parent.SqueezeBox.close();
			                      //jQuery('.htmlContent').html(' ');

			                       var pUrl= "<?php echo $urlpagos;?>";
			                       //jQuery('.htmlContent').append('<p><img src='+imagenajax+' /></p>');
			                       jQuery('#sp-component').append('<p><img src='+imagenajax+' /></p>');

			                       jQuery.ajax({

			                                type: "POST",
			                                url:pUrl,
			                                data:{funcion:'1', idTransaccionTerminal:idTransaccionTerminal,idTransaccionActual:idTransaccionActual, numeroFactura:numeroFactura , montoTotal:montoTotal,tipoTerminal:tipoTerminal,idTerminal:idTerminal,idAdquiriente:idAdquiriente },
			                                success: function (data) {




			                                jQuery('#sp-component').html(' <div class="vrcvordudata">'+data+'</div>');






			                                }
			                        });


			                    }

			                     if(e.data=='Cancelacion'){

			                         parent.SqueezeBox.close();

			                         jQuery('input[name="idTransaccionActual"]').val(idTransaccionActual);

			                    }

			                     if(e.data=='Error'){

			                        parent.SqueezeBox.close();
			                        jQuery('input[name="idTransaccionActual"]').val(idTransaccionActual);

			                    }

			            },false);


			        if(funcion=='error'){

			            alert('Error con Servidor de Pagos');

			        }else{

			            /*SqueezeBox.initialize({
			                size: {x: 750, y: 400}
			            });*/
			            //SqueezeBox.setContent('iframe','//www.pagosrbm.com:8443/GlobalPayWeb/gp/realizarPago.xhtml?idTerminal=ESB10071&idTransaccion=<?php echo $idTransaccionActual; ?>');
			            //SqueezeBox.resize({x: 750, y: 400})
			            //
			            //
			        if(parseInt(codRespuesta)==0){
			            debugger;

			            SqueezeBox.initialize({
			                size: {x: 400, y: 80}
			            });


			             //newElem.setStyle('border', 'solid 2px black');
			             //newElem.setStyle('width', '100px');
			             //
			             if(codRespuesta=='9002' || codRespuesta=='9003' || codRespuesta=='9004' || codRespuesta=='9006' ){

			              	var newElem = new Element( 'div' );
			                newElem.appendText("Código de Error:"+codRespuesta);
			                newElem.appendChild(document.createElement("br"));
			                newElem.appendText("Descripción Respuesta: "+descRespuesta);
			                newElem.appendChild(document.createElement("br"));
			                newElem.appendText("Estado de Transacción: "+funcion);
			                newElem.appendChild(document.createElement("br"));

			                SqueezeBox.setContent('adopt', newElem);
			                SqueezeBox.resize({x: 400, y: 80});

			             }




			        }else{
			            SqueezeBox.open("https://www.pagosrbm.com/GlobalPayWeb/gp/realizarPago.xhtml?idTerminal="+idTerminal+"&idTransaccion="+idTransaccionActual, {
			            handler: 'iframe',
			            size: { x: 700, y: 600 }
			            });

			        }


			        }

			         //jQuery(".ligthbox_redeban").colorbox({iframe:true, innerWidth:700, innerHeight:600, onClosed:function(){



			         //jQuery(".fancybox").fancybox();

			        //jQuery("#linkrede").click();














			    });


			</script>

	         <?php


		}catch(SoapFault $e){
		 //var_dump($e);

		    $funcion='error';

		    var_dump($e->getMessage());

		     echo $e->getMessage();




		     //print_r($document);

		}
	}

function consultarEstadoPagoRedeban(){


		$funcion = JRequest :: getString('funcion', '', 'request');
		$idTransaccionActual = JRequest :: getString('idTransaccionActual', '', 'request');

		if(!isset($funcion) || $funcion==null ||  empty($funcion)){
		$funcion='0';
		}

		$montoTotal = JRequest :: getString('montoTotal', '', 'request');
		$baseImpuesto = JRequest :: getString('baseImpuesto', '', 'request');
		$montoimpuesto = JRequest :: getString('montoimpuesto', '', 'request');
		$idOrder = JRequest :: getString('idorder', '', 'request');
		$idTransaccionTerminal = JRequest :: getString('idTransaccionTerminal', '', 'request');
		$numeroFactura = JRequest :: getString('numeroFactura', '', 'request');



		/*echo 'montoTotal: '.$montoTotal;
		echo 'baseImpuesto '.$baseImpuesto;
		echo 'montoimpuesto'.$montoimpuesto;
		echo 'idOrder '.$idOrder;
		echo 'idTransaccionTerminal '.$idTransaccionTerminal;
		echo 'numeroFactura'.$numeroFactura;*/



		$urlpagos= JURI::root().'ConsultarEstadoDePago.php';
		$mainframe =& JFactory::getApplication('site');
		//$mainframe->initialise();

		$document = & JFactory :: getDocument();


		$config =& JFactory::getConfig();
		$dateNow = new DateTime(null, new DateTimeZone($config->getValue('config.offset')));

		$mysqlDateTime = $dateNow->format(DateTime::ISO8601);
		//$nowts  = $dateNow->getTimestamp() + $dateNow->getOffset();
		//$urlservicebase='http://sms1.signacom.com.co:9090/SignacomWebServer/SendMessage?';
		//











		 //$objClienteWS = new SoapClient( "https://190.27.225.227:8443/GlobalPayServicios/GlobalPayServicioDePago/?wsdl", array("trace" => 1));
		 $objClienteWS = new SoapClient("https://www.pagosrbm.com/GlobalPayServicios/GlobalPayServicioDePago/GlobalPayServicioDePago.wsdl",  array("trace" => 1));







		        $requestParams = array(
		        "credenciales" => array(
		        "idUsuario" => IDUSUARIO,
		        "clave" =>CLAVE
		        ),
		        "cabeceraSolicitud" => array(
		         "infoPuntoInteraccion" => array(
		         "tipoTerminal" =>TIPOTERMINAL,
		         "idTerminal" => IDTERMINAL1,
		         "idAdquiriente" => IDADQUIRIENTE,
		         "idTransaccionTerminal" => $idTransaccionTerminal,
		        ),
		        ),
		        "idTransaccion"=>$idTransaccionActual

		        );



		        $resultado = $objClienteWS->ConsultarEstadoDePago($requestParams);
		       // echo "REQUEST:\n" . htmlentities($objClienteWS->__getLastRequest()) . "\n";
		       // echo "\n";




		        $idAprobacion= $resultado->infoPago->numeroAprobacion;
		        $estadotransaccion =$resultado->infoRespuesta->estado;






		         $costoTransaccion= $resultado->infoPago->costoTransaccion;
		         $fechaTransaccion= $resultado->infoPago->fechaTransaccion;


		echo 'idAprobacion: '.$idAprobacion. '</br>';
		echo 'estadotransaccion '.$estadotransaccion. '</br>';
		echo 'costoTransaccion'.$costoTransaccion. '</br>';
		echo 'fechaTransaccion '.$fechaTransaccion. '</br>';



		         $dbo = & JFactory :: getDBO();




		         $q="UPDATE  #__vikrentcar_factura  SET estado='".$estadotransaccion. "'  WHERE id='".$numeroFactura."';";

		         $dbo->setQuery($q);
		         $dbo->query();

		   if($estadotransaccion=='Aprobada'){

		         if($idAprobacion!=0){

		             $q="UPDATE  #__vikrentcar_transaccion  SET id_resultado_transac='".$idAprobacion. "'  WHERE id_factura='".$numeroFactura."';";

		             $dbo->setQuery($q);
		             $dbo->query();


		        }



		         $q = "SELECT * FROM `#__vikrentcar_orders` WHERE `id_factura`='" . $dbo->getEscaped($numeroFactura) ."';";
		         $dbo->setQuery($q);
		         $dbo->Query($q);
		            if ($dbo->getNumRows() > 0) {

		                $rows = $dbo->loadAssocList();

		                foreach ($rows as $key => $value) {
		                    $sid=$value['sid'];
		                    $ts=$value['ts'];

		                    echo 'sid '.$sid.'</br>';
		                    echo 'ts '.$ts;

		                   self::notifypayment2($value['sid'],$value['ts'], $montoTotal);



		                   JRequest :: setVar('sid', $sid );
		                   JRequest :: setVar('ts', $ts);
		                   JRequest :: setVar('format', 'raw');


		                   //self::vieworder();
		                   $app =& JFactory::getApplication();

		                   $app->redirect("index.php?option=com_vikrentcar&task=vieworder&format=raw&sid=" . $sid . "&ts=" . $ts );






		                }

		            }


		    }






	}

	function saveseveralorders(){

		$orders = JRequest :: getString('ordenes', '', 'request');
		//echo 'exito '.$orders;

		$ad=$orders;
		$long =strlen($ad);

		$orders = substr($ad,1,$long-2);

		$dbo = & JFactory :: getDBO();
		$app =& JFactory::getApplication();


		/*$q= "SELECT id_factura FROM `chun4_vikrentcar_orders` WHERE id IN(".$orders.")";
		$dbo->setQuery($q);
		$dbo->Query($q);*/
		try{

		$q= "INSERT INTO `#__vikrentcar_factura` (`estado`) VALUES('standby')";
		$dbo->setQuery($q);
		$dbo->Query($q);
		$id_factura = $dbo->insertid();

		/*$q = "SELECT MAX(id) FROM `#__vikrentcar_factura`;";
		$dbo->setQuery($q);
		$dbo->Query($q);
		$n = $dbo->loadAssocList();
		$id_factura=$n[0]['MAX(id)'];*/



		$q = "UPDATE `#__vikrentcar_orders` SET `id_factura`='".$id_factura."' WHERE `id` IN(" . $orders . ");";
		$dbo->setQuery($q);
		$dbo->Query($q);


		$config =& JFactory::getConfig();
		$dateNow = new DateTime(null, new DateTimeZone($config->getValue('config.offset')));

		$mysqlDateTime = $dateNow->format(DateTime::ISO8601);

		/*$q= "INSERT INTO `#__vikrentcar_transaccion` (`fecha`, `id_factura`) VALUES('".$mysqlDateTime."','".$id_factura."')";
		$dbo->setQuery($q);
		$dbo->Query($q);
		$id_transaccion = $dbo->insertid();*/

		$salida=array("id_factura"=>$id_factura,"id_transaccion"=>$id_transaccion);

		$salida= json_encode($salida);
		echo $salida;

		} catch (Exception $e) {

			$app->enqueueMessage('Error: '.$e->getMessage());


		}

	}


	function testcurl(){

		 $sid=1761901800;
		 $ts=1412982489;



		$ruta = JURI::root()."index.php?option=com_vikrentcar&task=vieworder&format=raw&sid=" . $sid . "&ts=" . $ts;


       JRequest :: setVar('sid', $sid );
       JRequest :: setVar('ts', $ts);
       JRequest :: setVar('format', 'raw');

		 $app =& JFactory::getApplication();

		 $app->redirect("index.php?option=com_vikrentcar&task=vieworder&format=raw&sid=" . $sid . "&ts=" . $ts );




}

//Consulta si la orden tuvo una transaccion exitosa

function consultarEstadoPago($sid, $ts){


       //error_reporting(-1);



		$dbo = & JFactory :: getDBO();
		$q = "SELECT t.id FROM `#__vikrentcar_orders` as o INNER JOIN `#__vikrentcar_transaccion` as t ON o.id_factura=t.id_factura  WHERE `sid`='" . $dbo->getEscaped($sid) . "' AND `ts`='" . $dbo->getEscaped($ts) . "' ORDER BY t.id DESC ;";

		$dbo->setQuery($q);
		$dbo->Query($q);
		if ($dbo->getNumRows() > 0) {
			$order = $dbo->loadAssocList();
			$id_transaccion=$order[0]['id'];
			$dbo = & JFactory :: getDBO();


		$q = "SELECT * FROM `#__vikrentcar_transaccion` WHERE id=".$id_transaccion.";";
		$dbo->setQuery($q);
		$dbo->Query($q);
		$cfields = $dbo->getNumRows() > 0 ? $dbo->loadAssocList() : "";


		$idTransaccionTerminal= $cfields[0]['id'];

		$idTransaccionActual= $cfields[0]['id_trandaccionActual'];

		$objClienteWS = new SoapClient("https://www.pagosrbm.com/GlobalPayServicios/GlobalPayServicioDePago/GlobalPayServicioDePago.wsdl",  array("trace" => 1));



        $requestParams = array(
        "credenciales" => array(
        "idUsuario" => IDUSUARIO,
        "clave" =>CLAVE
        ),
        "cabeceraSolicitud" => array(
         "infoPuntoInteraccion" => array(
         "tipoTerminal" =>TIPOTERMINAL,
         "idTerminal" => IDTERMINAL1,
         "idAdquiriente" => IDADQUIRIENTE,
         "idTransaccionTerminal" => $idTransaccionTerminal,
        ),
        ),
        "idTransaccion"=>$idTransaccionActual

        );



        $resultado = $objClienteWS->ConsultarEstadoDePago($requestParams);
       //echo "REQUEST:\n" . htmlentities($objClienteWS->__getLastRequest()) . "\n";
         //echo "\n";
         //


         $idAprobacion= $resultado->infoPago->numeroAprobacion;
         $estadotransaccion =$resultado->infoRespuesta->estado;






         $costoTransaccion= $resultado->infoPago->costoTransaccion;
         $fechaTransaccion= $resultado->infoPago->fechaTransaccion;


         $sqldate = date('Y-m-d H:i:s', strtotime($fechaTransaccion));

         if($estadotransaccion=='Aprobada'){

         	$q = "UPDATE `#__vikrentcar_transaccion` SET `id_resultado_transac`='".$idAprobacion."' , fecha='".$sqldate."' WHERE `id_trandaccionActual`=". $idTransaccionActual .";";
			$dbo->setQuery($q);
			$dbo->Query($q);

         		$state= self::notifypayment2($sid,$ts, $montoTotal);

         }else{



         }


		}




	}

	function testconsultarEstadoPago(){


       error_reporting(-1);

       $sid= '552215754';
       $ts= '1425935963';

       echo 'esto es una prueba'. $ts;

		$dbo = & JFactory :: getDBO();
		$q = "SELECT t.id FROM `#__vikrentcar_orders` as o INNER JOIN `#__vikrentcar_transaccion` as t ON o.id_factura=t.id_factura  WHERE `sid`='" . $dbo->getEscaped($sid) . "' AND `ts`='" . $dbo->getEscaped($ts) . "';";

		$dbo->setQuery($q);
		$dbo->Query($q);
		if ($dbo->getNumRows() == 1) {
			$order = $dbo->loadAssocList();
			$id_transaccion=$order[0]['id'];
			$dbo = & JFactory :: getDBO();


		$q = "SELECT * FROM `#__vikrentcar_transaccion` WHERE id=".$id_transaccion.";";
		$dbo->setQuery($q);
		$dbo->Query($q);
		$cfields = $dbo->getNumRows() > 0 ? $dbo->loadAssocList() : "";


		$idTransaccionTerminal= $cfields[0]['id'];

		$idTransaccionActual= $cfields[0]['id_trandaccionActual'];

		$objClienteWS = new SoapClient("https://www.pagosrbm.com/GlobalPayServicios/GlobalPayServicioDePago/GlobalPayServicioDePago.wsdl",  array("trace" => 1));



        $requestParams = array(
        "credenciales" => array(
        "idUsuario" => IDUSUARIO,
        "clave" =>CLAVE
        ),
        "cabeceraSolicitud" => array(
         "infoPuntoInteraccion" => array(
         "tipoTerminal" =>TIPOTERMINAL,
         "idTerminal" => IDTERMINAL1,
         "idAdquiriente" => IDADQUIRIENTE,
         "idTransaccionTerminal" => $idTransaccionTerminal,
        ),
        ),
        "idTransaccion"=>$idTransaccionActual

        );



        $resultado = $objClienteWS->ConsultarEstadoDePago($requestParams);
        echo "REQUEST:\n" . htmlentities($objClienteWS->__getLastRequest()) . "\n";
         echo "\n";

         $idAprobacion= $resultado->infoPago->numeroAprobacion;
         $estadotransaccion =$resultado->infoRespuesta->estado;






         $costoTransaccion= $resultado->infoPago->costoTransaccion;
         $fechaTransaccion= $resultado->infoPago->fechaTransaccion;

         if($estadotransaccion=='Aprobada'){

         		$state= self::notifypayment2($sid,$ts, $montoTotal);

         }else{



         }


		}






	}

	function viewseveralorders(){

		$orders = JRequest :: getString('ordenes', '', 'request');
		//echo 'exito '.$orders;

		$ad=$orders;
		$long =strlen($ad);

		$orders = substr($ad,1,$long-2);

		$dbo = & JFactory :: getDBO();
		$app =& JFactory::getApplication();


		/*$q= "SELECT id_factura FROM `chun4_vikrentcar_orders` WHERE id IN(".$orders.")";
		$dbo->setQuery($q);
		$dbo->Query($q);*/
		try{

		$q= "INSERT INTO `#__vikrentcar_factura` (`estado`) VALUES('standby')";
		$dbo->setQuery($q);
		$dbo->Query($q);
		$id_factura = $dbo->insertid();

		/*$q = "SELECT MAX(id) FROM `#__vikrentcar_factura`;";
		$dbo->setQuery($q);
		$dbo->Query($q);
		$n = $dbo->loadAssocList();
		$id_factura=$n[0]['MAX(id)'];*/



		$q = "UPDATE `#__vikrentcar_orders` SET `id_factura`='".$id_factura."' WHERE `id` IN(" . $orders . ");";
		$dbo->setQuery($q);
		$dbo->Query($q);


		$config =& JFactory::getConfig();
		$dateNow = new DateTime(null, new DateTimeZone($config->getValue('config.offset')));

		$mysqlDateTime = $dateNow->format(DateTime::ISO8601);

		$q= "INSERT INTO `#__vikrentcar_transaccion` (`fecha`, `id_factura`) VALUES('".$mysqlDateTime."','".$id_factura."')";
		$dbo->setQuery($q);
		$dbo->Query($q);
		$id_transaccion = $dbo->insertid();

		$salida=array("id_factura"=>$id_factura,"id_transaccion"=>$id_transaccion);

		$salida= json_encode($salida);
		echo $salida;

		} catch (Exception $e) {

			$app->enqueueMessage('Error: '.$e->getMessage());


		}

	}
}


?>
