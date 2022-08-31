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

$mc = new ControlcardModelControlcard();
$d =$mc->getData();
?>
<div id="j-sidebar-container" class="span2">
	<?php echo $this->sidebar; ?>
</div>
<div id="j-main-container" class="span10">
    <?php echo JPATH_COMPONENT_ADMINISTRATOR ?><div></div>
    <?php echo JPATH_COMPONENT_SITE ?><div></div>
    <?php echo JPath::clean($client->path . '/components/' . $extension . '/views/' . $view . '/tmpl') ?>
    <div></div>
    <?php echo $client->path ?>

</div>
