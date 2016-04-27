<?php
/**
 * @version   $Id: ItemPrivacyPopulator.php 54338 2012-07-13 17:49:24Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2013 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

class RokSprocket_Provider_EasyBlog_ItemPrivacyPopulator implements RokCommon_Filter_IPicklistPopulator
{
    /**
     *
     * @return array;
     */
    public function getPicklistOptions()
    {
        $options = array();
        $options['private'] = "Private";
        $options['public'] = "Public";

        return $options;
    }
}
