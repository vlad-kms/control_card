<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_users
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

JFormHelper::loadFieldClass('list');

/**
 * Access Levels field.
 *
 * @since  3.6.0
 */
class JFormFieldUnrelatedUser extends JFormFieldList
{
	/**
	* The form field type.
    *
    * @var     string
    * @since   3.6.0
    */
	protected $type = 'UnrelatedUser';

    protected function getOptions()
    {
        // Merge any additional options in the XML definition.
        return array_merge(parent::getOptions(), UsersHelperDebug::getLevelsOptions());
    }
}
