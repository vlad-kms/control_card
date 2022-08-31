<?php
/**
 * @package    controlcard
 *
 * @author     User <your@email.com>
 * @copyright  A copyright
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @link       http://your.url.com
 */

use Joomla\CMS\MVC\Model\ListModel;

defined('_JEXEC') or die;

/**
 * Controlcard
 *
 * @package  controlcard
 * @since    1.0
 */
class ControlcardModelControlcard extends ListModel
{
    public function getData()
    {
        $em = get_class_methods('JController');
        $r=new ReflectionClass($this);
        $rName = $r->getName();
        $rMethods = $r->getMethods(ReflectionMethod::IS_PUBLIC);
    }
}
