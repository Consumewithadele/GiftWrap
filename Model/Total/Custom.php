<?php
namespace Khomenko\GiftWrap\Model\Total;

use Magento\Quote\Model\Quote\Address\Total\AbstractTotal;
use Magento\Quote\Model\Quote;
use Magento\Quote\Api\Data\ShippingAssignmentInterface;
use Magento\Quote\Model\Quote\Address\Total;
use Khomenko\GiftWrap\Helper\Data as CustomTotalHelper;

class Custom extends AbstractTotal
{

    /**
     * @var CustomTotalHelper
     */
    protected $helper;

    /**
     * Custom constructor.
     *
     * @param CustomTotalHelper $helper
     */
    public function __construct(
        CustomTotalHelper $helper
    ) {
        $this->helper = $helper;
        $this->setCode('custom');
    }

    /**
     * @param Quote $quote
     * @param ShippingAssignmentInterface $shippingAssignment
     * @param Total $total
     * @return $this
     */
    public function collect(
        Quote $quote,
        ShippingAssignmentInterface $shippingAssignment,
        Total $total
    ) {
        parent::collect($quote, $shippingAssignment, $total);
        $items = $shippingAssignment->getItems();
        if (!count($items)) {
            return $this;
        }

        if (!$quote->getData('gift_wrap_required')) {
            return $this;
        }

        $amount = $this->helper->getAmount();

        $total->setTotalAmount('custom', $amount);
        $total->setBaseTotalAmount('custom', $amount);
        $total->setCustomAmount($amount);
        $total->setBaseCustomAmount($amount);
        return $this;
    }

    /**
     * @param Total $total
     */
    protected function clearValues(Total $total)
    {
        $total->setTotalAmount('subtotal', 0);
        $total->setBaseTotalAmount('subtotal', 0);
        $total->setTotalAmount('tax', 0);
        $total->setBaseTotalAmount('tax', 0);
        $total->setTotalAmount('discount_tax_compensation', 0);
        $total->setBaseTotalAmount('discount_tax_compensation', 0);
        $total->setTotalAmount('shipping_discount_tax_compensation', 0);
        $total->setBaseTotalAmount('shipping_discount_tax_compensation', 0);
        $total->setSubtotalInclTax(0);
        $total->setBaseSubtotalInclTax(0);
    }

    /**
     * @param Quote $quote
     * @param Total $total
     * @return array
     */
    public function fetch(Quote $quote, Total $total)
    {
        $amount = $quote->getData('gift_wrap_required') ?
            $this->helper->getAmount() : 0;

        return [
            'code' => $this->getCode(),
            'title' => $this->helper->getTitle(),
            'value' => $amount
        ];
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getLabel()
    {
        return __($this->helper->getTitle());
    }
}
