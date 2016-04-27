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
$url = JRoute::_( "index.php?option=com_k2store&view=mycart" ); 
$guest_url = JRoute::_( "index.php?option=com_k2store&view=checkout&guest=1&Itemid=".$this->params->get("itemid"));
?>
		<div class="k2storeLogin">
        <div class='componentheading'>
            <span><?php echo JText::_( "Returning Users" ); ?></span>
        </div>
    
        <!-- LOGIN FORM -->
        
        <?php if (JPluginHelper::isEnabled('authentication', 'openid')) :
                $lang->load( 'plg_authentication_openid', JPATH_ADMINISTRATOR );
                $langScript =   'var JLanguage = {};'.
                                ' JLanguage.WHAT_IS_OPENID = \''.JText::_( 'WHAT_IS_OPENID' ).'\';'.
                                ' JLanguage.LOGIN_WITH_OPENID = \''.JText::_( 'LOGIN_WITH_OPENID' ).'\';'.
                                ' JLanguage.NORMAL_LOGIN = \''.JText::_( 'NORMAL_LOGIN' ).'\';'.
                                ' var modlogin = 1;';
                $document = &JFactory::getDocument();
                $document->addScriptDeclaration( $langScript );
                JHTML::_('script', 'openid.js');
        endif; ?>
        
        <form action="<?php echo JRoute::_( 'index.php?option=com_k2store', true, $this->params->get('usesecure')); ?>" method="post" name="login" id="form-login" >
        
           			<div class="k2storeUserLogin">
                    <?php echo JText::_('USERNAME'); ?>
               		<br />
                    <input type="text" name="username" class="inputbox" size="18" alt="username" />
                	</div>
                	<div class="k2storeUserPassword">
                    <?php echo JText::_('PASSWORD'); ?>
                	<br />
                    <input type="password" name="password" class="inputbox" size="18" alt="password" />
                    </div>
                    
                     <input type="submit" name="submit" class="button" value="<?php echo JText::_('LOGIN') ?>" />

           		 <?php if (JPluginHelper::isEnabled('system', 'remember')) : ?>
           
                  
                    <div class="rememberMe">
                      <?php echo JText::_('REMEMBER ME'); ?>
                     <span style="float: left">
                        <input type="checkbox" name="remember" class="inputbox" value="yes"/>
                    </span>
                    </div>
                           
            <?php endif; ?>
               <ul class="loginLinks">
                        <li>
                            <?php // TODO Can we do this in a lightbox or something? Why does the user have to leave? ?>
                            <a href="<?php echo JRoute::_( 'index.php?option=com_users&view=reset' ); ?>">
                            <?php echo JText::_('FORGOT_YOUR_PASSWORD'); ?></a>
                        </li>
                        <li>
                            <?php // TODO Can we do this in a lightbox or something? Why does the user have to leave? ?>
                            <a href="<?php echo JRoute::_( 'index.php?option=com_users&view=remind' ); ?>">
                            <?php echo JText::_('FORGOT_YOUR_USERNAME'); ?></a>
                        </li>
                    </ul>
                    
        
            <input type="hidden" name="option" value="com_users" />
            <input type="hidden" name="task" value="user.login" />
            <input type="hidden" name="return" value="<?php echo base64_encode( $url ); ?>" />
            <?php echo JHTML::_( 'form.token' ); ?>
        </form>
    </div>
    
     <div class="k2storeNewusers">
   <!-- New Users -->
   
    
        <div class='componentheading'>
            <span><?php echo JText::_( "New Users" ); ?></span>
        </div>
        
        <!-- REGISTRATION -->

       
                <?php echo JTEXT::_('PLEASE REGISTER TO CONTINUE SHOPPING'); ?>
                           <input type="button" class="button" onclick="window.location='<?php echo JRoute::_( "index.php?option=com_users&view=registration&return=".base64_encode( $url ) ); ?>'" value="<?php echo JText::_( "REGISTER" ); ?>" />
           

        <div class="reset"></div>
        </div>
        
        <?php if ($this->params->get('allow_guest_checkout')) : ?>
        <!--Guest -->
         <div class="k2storeGuests">
            <div class='componentheading' style="margin-top:15px;">
                <span><?php echo JText::_( "Checkout as a Guest" ); ?></span>
            </div>
            <!-- REGISTRATION -->
        
                 <?php echo JTEXT::_('CHECKOUT AS A GUEST DESC'); ?>
                  <input type="button" class="button" onclick="window.location='<?php echo $guest_url;  ?>'" value="<?php echo JText::_( "Checkout as a Guest" ); ?>" />
        </div>
        <?php endif; ?>
