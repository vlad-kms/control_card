<?php
/**
 * @package    controlcard
 *
 * @author     User <your@email.com>
 * @copyright  A copyright
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @link       http://your.url.com
 */

#use Joomla\CMS\MVC\Controller\BaseController;

defined('_JEXEC') or die;

/**
 * Controlcard controller.
 *
 * @package  controlcard
 * @since    1.0
 */
class ControlcardControllerReasons extends JControllerForm
{
  protected $default_view = "reasons";

  public function __construct($config = array())
  {
    parent::__construct($config);
  }

  protected function allowAdd($data = array())
  {
    return $this->allowEdit($data);
  }

  protected function allowDelete($data = array(), $key = 'id')
  {
    if ( count($data) == 0)
    {
      $item=null;
    }
    else
    {
      $item = ArrayHelper::toObject($data, '\JObject');
    }
    $model = $this->getModel();
    if ($model)
    {
      return $model->canDelete($item);
    }
    else
    {
      return \JFactory::getUser()->authorise('core.reasons.delete', $this->option);
    }
  }

  protected function allowEdit($data = array(), $key = 'id')
  {
    if ( count($data) == 0) {
      $item=null;
    } else {
      $item = ArrayHelper::toObject($data, '\JObject');
    }
    $model = $this->getModel();
    if ($model) {
      return $model->canEdit($item);
    }
    else {
      return \JFactory::getUser()->authorise('core.resaons.edit', $this->option);
    }
  }

}
