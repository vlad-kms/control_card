<?php
/**
 * @package     ${NAMESPACE}
 * @subpackage
 *
 * @copyright   A copyright
 * @license     A "Slug" license name e.g. GPL2
 */

JFormHelper::loadFieldClass('predefinedlist');

class JFormFieldPerformedType extends JFormFieldPredefinedList
{
	protected $type = 'PerformedType';

	protected $predefinedOptions = array(
		1 => COM_CONTROLCARD_CARDS_PERFORMED_TYPE_VALUE_1,
		2 => COM_CONTROLCARD_CARDS_PERFORMED_TYPE_VALUE_2,
		3 => COM_CONTROLCARD_CARDS_PERFORMED_TYPE_VALUE_3,
		6 => COM_CONTROLCARD_CARDS_PERFORMED_TYPE_VALUE_6,
		7 => COM_CONTROLCARD_CARDS_PERFORMED_TYPE_VALUE_7,
		4 => COM_CONTROLCARD_CARDS_PERFORMED_TYPE_VALUE_4,
		5 => COM_CONTROLCARD_CARDS_PERFORMED_TYPE_VALUE_5,
		8 => COM_CONTROLCARD_CARDS_PERFORMED_TYPE_VALUE_8
	);

}