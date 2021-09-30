<?php // phpcs:ignore
declare(strict_types = 1);

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\Component\Fields\Administrator\Plugin\FieldsListPlugin;

class PlgFieldsArticleSelect extends FieldsListPlugin
{
    /**
     * Indicates that the language should be loaded automatically.
     *
     * @var boolean
     */
    public $autoloadLanguage = true;

    /**
     * Transforms the field into a DOM XML element and appends it as a child on the given parent.
     *
     * @param  \stdClass             $field  The field.
     * @param  \DOMElement           $parent The field node parent.
     * @param  \Joomla\CMS\Form\Form $form   The form.
     *
     * @return  \DOMElement
     */
    public function onCustomFieldsPrepareDom($field, DOMElement $parent, Form $form)
    {
        $fieldNode = parent::onCustomFieldsPrepareDom($field, $parent, $form);

        if (!$fieldNode) {
            return $fieldNode;
        }

        // Retrieve the categories from the field options.
        $categories = array_map(function ($id): int {
            return (int) $id;
        }, (array) $field->fieldparams->get('categories'));

        // Construct the field.
        $fieldNode->setAttribute('type', 'sql');
        $fieldNode->setAttribute('layout', 'joomla.form.field.list-fancy-select');
        $fieldNode->setAttribute('value_field', 'text');
        $fieldNode->setAttribute('key_field', 'value');
        $fieldNode->setAttribute('multiple', 'true');

        $fieldNode->setAttribute(
            'query',
            'select title as value, CONCAT(title, " [", id, "]") as text from #__content' .
            (count($categories) ? ' where catid in (' . implode(',', $categories) . ')' : '')
        );

        return $fieldNode;
    }
}
