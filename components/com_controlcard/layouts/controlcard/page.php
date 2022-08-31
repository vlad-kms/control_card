<?php
/**
 * @package    controlcard
 *
 * @author     User <your@email.com>
 * @copyright  A copyright
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @link       http://your.url.com
 */

defined('_JEXEC') or die;

/*
if (!key_exists('field', $displayData))
{
    return;
}
*/
extract($displayData);

echo '</br>';
echo '================'.$title.'--------------------------' . '</br>';
echo '================'.$ddd.'--------------------------' . '</br>';
#echo $text;