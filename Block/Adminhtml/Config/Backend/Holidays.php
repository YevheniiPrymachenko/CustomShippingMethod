<?php

declare(strict_types=1);

namespace Primak\CustomShipping\Block\Adminhtml\Config\Backend;

use Exception;
use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\Data\Form\Element\Factory;

/**
 * Class Holidays
 */
class Holidays extends AbstractFieldArray
{
    /**
     * @var string
     */
    protected $_template = 'Primak_CustomShipping::system/config/form/field/holidays.phtml';

    /**
     * @var Factory
     */
    protected $elementFactory;

    /**
     * @param Context $context
     * @param Factory $elementFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        Factory $elementFactory,
        array $data = []
    ) {
        $this->elementFactory = $elementFactory;

        parent::__construct($context, $data);
    }

    /**
     * @return void
     */
    public function _construct()
    {
        $this->addColumn('holiday_name', ['label' => __('Holiday name')]);
        $this->addColumn('holiday', ['label' => __('Date')]);

        $this->_addAfter = false;

        parent::_construct();
    }

    /**
     * Render array cell for prototypeJS template
     *
     * @param string $columnName
     *
     * @return string|array
     * @throws Exception
     */
    public function renderCellTemplate($columnName): string|array
    {
        if (!empty($this->_columns[$columnName])) {
            switch ($columnName) {
                case 'holiday':
                    $element = $this->elementFactory->create('date');
                    $element->setForm($this->getForm())
                        ->setName($this->_getCellInputElementName($columnName))
                        ->setHtmlId($this->_getCellInputElementId('<%- _id %>', $columnName))
                        ->setFormat('MM/dd');

                    return str_replace("\n", '', $element->getElementHtml());
                default:
                    break;
            }
        }

        return parent::renderCellTemplate($columnName);
    }

    public function getAddButtonLabel()
    {
        return $this->_addButtonLabel;
    }
}
