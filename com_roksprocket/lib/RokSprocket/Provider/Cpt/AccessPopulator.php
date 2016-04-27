<?php
/**
 * @version   $Id$
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2013 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

class RokSprocket_Provider_Cpt_AccessPopulator implements RokCommon_Filter_IPicklistPopulator
{
    /**
     *
     * @return array;
     */
    public function getPicklistOptions()
    {
        $editable_roles = get_editable_roles();

        foreach ( $editable_roles as $role => $details ) {
            $name = translate_user_role($details['name'] );
            $options[esc_attr($role)] = $name;
        }
        return $options;
    }
}
