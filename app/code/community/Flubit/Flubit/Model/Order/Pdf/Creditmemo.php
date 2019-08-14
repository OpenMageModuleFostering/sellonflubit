<?php
/**
 * Sales Order Creditmemo PDF model
 *
 * @category   Mage
 * @package    Mage_Sales
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Flubit_Flubit_Model_Order_Pdf_Creditmemo extends Mage_Sales_Model_Order_Pdf_Creditmemo
{
    /**
     * Draw table header for product items
     *
     * @param  Zend_Pdf_Page $page
     * @return void
     */
    protected function _drawHeader(Zend_Pdf_Page $page, Mage_Sales_Model_Order $order = NULL)
    {
        $magentoVersion = Mage::getVersion();
        if (version_compare($magentoVersion, '1.7', '>=')) {
            // if version is greater than 1.7

            $this->_setFontRegular($page, 10);
            $page->setFillColor(new Zend_Pdf_Color_RGB(0.93, 0.92, 0.92));
            $page->setLineColor(new Zend_Pdf_Color_GrayScale(0.5));
            $page->setLineWidth(0.5);
            $page->drawRectangle(25, $this->y, 570, $this->y - 30);
            $this->y -= 10;
            $page->setFillColor(new Zend_Pdf_Color_RGB(0, 0, 0));


            if ($order && !$order->getData('flubit_order_id')) {
                //columns headers
                $lines[0][] = array(
                    'text' => Mage::helper('sales')->__('Products'),
                    'feed' => 35,
                );

                $lines[0][] = array(
                    'text' => Mage::helper('core/string')->str_split(Mage::helper('sales')->__('SKU'), 12, true, true),
                    'feed' => 255,
                    'align' => 'right'
                );

                $lines[0][] = array(
                    'text' => Mage::helper('core/string')->str_split(Mage::helper('sales')->__('Total (ex)'), 12, true, true),
                    'feed' => 330,
                    'align' => 'right',
                        //'width' => 50,
                );

                $lines[0][] = array(
                    'text' => Mage::helper('core/string')->str_split(Mage::helper('sales')->__('Discount'), 12, true, true),
                    'feed' => 380,
                    'align' => 'right',
                        //'width' => 50,
                );

                $lines[0][] = array(
                    'text' => Mage::helper('core/string')->str_split(Mage::helper('sales')->__('Qty'), 12, true, true),
                    'feed' => 445,
                    'align' => 'right',
                        //'width' => 30,
                );

                $lines[0][] = array(
                    'text' => Mage::helper('core/string')->str_split(Mage::helper('sales')->__('Tax'), 12, true, true),
                    'feed' => 495,
                    'align' => 'right',
                        //'width' => 45,
                );

                $lines[0][] = array(
                    'text' => Mage::helper('core/string')->str_split(Mage::helper('sales')->__('Total (inc)'), 12, true, true),
                    'feed' => 565,
                    'align' => 'right'
                );
            } else {
                //columns headers for flubbit products
                //flubit
                $lines[0][] = array(
                    'text' => Mage::helper('sales')->__('Products'),
                    'feed' => 35,
                );
                //flubit
                $lines[0][] = array(
                    'text' => Mage::helper('core/string')->str_split(Mage::helper('sales')->__('SKU'), 12, true, true),
                    'feed' => 180,
                    'align' => 'right'
                );
                //flubit base price
                $lines[0][] = array(
                    'text' => Mage::helper('core/string')->str_split(Mage::helper('sales')->__('FBP'), 12, true, true),
                    'feed' => 250,
                    'align' => 'right',
                        //'width' => 50,
                );
                // flubit price sold at
                $lines[0][] = array(
                    'text' => Mage::helper('core/string')->str_split(Mage::helper('sales')->__('Price Sold At(VAT inc.)'), 30, true, true),
                    'feed' => 360,
                    'align' => 'right',
                        //'width' => 50,
                );
                // flubit qty 

                $lines[0][] = array(
                    'text' => Mage::helper('core/string')->str_split(Mage::helper('sales')->__('Qty'), 12, true, true),
                    'feed' => 380,
                    'align' => 'right',
                        //'width' => 30,
                );
                // flubit tax rate
                $lines[0][] = array(
                    'text' => Mage::helper('core/string')->str_split(Mage::helper('sales')->__('Tax Rate'), 12, true, true),
                    'feed' => 435,
                    'align' => 'right',
                        //'width' => 30,
                );
                // actual price sold at
                $lines[0][] = array(
                    'text' => Mage::helper('core/string')->str_split(Mage::helper('sales')->__('Actual Price Sold At(VAT inc.)'), 30, true, true),
                    'feed' => 565,
                    'align' => 'right'
                );
            }
            $lineBlock = array(
                'lines' => $lines,
                'height' => 10
            );

            $this->drawLineBlocks($page, array($lineBlock), array('table_header' => true));
            $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
            $this->y -= 20;
        } else {
                $font = $page->getFont();
                $size = $page->getFontSize();
                
            if ($order && $order->getData('flubit_order_id')) {


                $page->drawText(Mage::helper('sales')->__('Product'), $x = 35, $this->y, 'UTF-8');
                $x += 140;

                $page->drawText(Mage::helper('sales')->__('SKU'), $x, $this->y, 'UTF-8');
                $x += 90;

                $text = Mage::helper('sales')->__('FBP');
                $page->drawText($text, $this->getAlignRight($text, $x, 10, $font, $size), $this->y, 'UTF-8');
                $x += 40;

                $text = Mage::helper('sales')->__('Price Sold At(VAT inc.)');
                $page->drawText($text, $this->getAlignRight($text, $x, 50, $font, $size), $this->y, 'UTF-8');
                $x += 50;

                $text = Mage::helper('sales')->__('Qty');
                $page->drawText($text, $this->getAlignCenter($text, $x, 30, $font, $size), $this->y, 'UTF-8');
                $x += 45;

                $text = Mage::helper('sales')->__('Tax Rate');
                $page->drawText($text, $this->getAlignRight($text, $x, 45, $font, $size, 10), $this->y, 'UTF-8');
                $x += 45;

                $text = Mage::helper('sales')->__('Actual Price Sold At(VAT inc.)');
                $page->drawText($text, $this->getAlignRight($text, $x, 570 - $x, $font, $size), $this->y, 'UTF-8');
            } else {

                $page->drawText(Mage::helper('sales')->__('Products'), $x = 35, $this->y, 'UTF-8');
                $x += 220;

                $page->drawText(Mage::helper('sales')->__('SKU'), $x, $this->y, 'UTF-8');
                $x += 100;

                $text = Mage::helper('sales')->__('Total (ex)');
                $page->drawText($text, $this->getAlignRight($text, $x, 50, $font, $size), $this->y, 'UTF-8');
                $x += 50;

                $text = Mage::helper('sales')->__('Discount');
                $page->drawText($text, $this->getAlignRight($text, $x, 50, $font, $size), $this->y, 'UTF-8');
                $x += 50;

                $text = Mage::helper('sales')->__('Qty');
                $page->drawText($text, $this->getAlignCenter($text, $x, 30, $font, $size), $this->y, 'UTF-8');
                $x += 30;

                $text = Mage::helper('sales')->__('Tax');
                $page->drawText($text, $this->getAlignRight($text, $x, 45, $font, $size, 10), $this->y, 'UTF-8');
                $x += 45;

                $text = Mage::helper('sales')->__('Total (inc)');
                $page->drawText($text, $this->getAlignRight($text, $x, 570 - $x, $font, $size), $this->y, 'UTF-8');
            }
        }
    }

    /**
     * Return PDF document
     *
     * @param  array $creditmemos
     * @return Zend_Pdf
     */
    public function getPdf($creditmemos = array())
    {
        $magentoVersion = Mage::getVersion();
        if (version_compare($magentoVersion, '1.7', '>=')) {
        $this->_beforeGetPdf();
        $this->_initRenderer('creditmemo');

        $pdf = new Zend_Pdf();
        $this->_setPdf($pdf);
        $style = new Zend_Pdf_Style();
        $this->_setFontBold($style, 10);

        foreach ($creditmemos as $creditmemo) {
            if ($creditmemo->getStoreId()) {
                Mage::app()->getLocale()->emulate($creditmemo->getStoreId());
                Mage::app()->setCurrentStore($creditmemo->getStoreId());
            }
            $page  = $this->newPage();
            $order = $creditmemo->getOrder();
            /* Add image */
            $this->insertLogo($page, $creditmemo->getStore());
            /* Add address */
            $this->insertAddress($page, $creditmemo->getStore());
            /* Add head */
            $this->insertOrder(
                $page,
                $order,
                Mage::getStoreConfigFlag(self::XML_PATH_SALES_PDF_CREDITMEMO_PUT_ORDER_ID, $order->getStoreId())
            );
            /* Add document text and number */
            $this->insertDocumentNumber(
                $page,
                Mage::helper('sales')->__('Credit Memo # ') . $creditmemo->getIncrementId()
            );
            /* Add table head */
            $this->_drawHeader($page, $order);
            /* Add body */
            foreach ($creditmemo->getAllItems() as $item){
                if ($item->getOrderItem()->getParentItem()) {
                    continue;
                }
                /* Draw item */
                $this->_drawItem($item, $page, $order);
                $page = end($pdf->pages);
            }
            /* Add totals */
            $this->insertTotals($page, $creditmemo);
        }
        $this->_afterGetPdf();
        if ($creditmemo->getStoreId()) {
            Mage::app()->getLocale()->revert();
        }
        return $pdf;
        } else {
            //if version is smaller than 1.7
            $this->_beforeGetPdf();
            $this->_initRenderer('creditmemo');

            $pdf = new Zend_Pdf();
            $this->_setPdf($pdf);
            $style = new Zend_Pdf_Style();
            $this->_setFontBold($style, 10);

            foreach ($creditmemos as $creditmemo) {
                if ($creditmemo->getStoreId()) {
                    Mage::app()->getLocale()->emulate($creditmemo->getStoreId());
                    Mage::app()->setCurrentStore($creditmemo->getStoreId());
                }
                $page = $pdf->newPage(Zend_Pdf_Page::SIZE_A4);
                $pdf->pages[] = $page;

                $order = $creditmemo->getOrder();

                /* Add image */
                $this->insertLogo($page, $creditmemo->getStore());

                /* Add address */
                $this->insertAddress($page, $creditmemo->getStore());

                /* Add head */
                $this->insertOrder($page, $order, Mage::getStoreConfigFlag(self::XML_PATH_SALES_PDF_CREDITMEMO_PUT_ORDER_ID, $order->getStoreId()));

                $page->setFillColor(new Zend_Pdf_Color_GrayScale(1));
                $this->_setFontRegular($page);
                $page->drawText(Mage::helper('sales')->__('Credit Memo # ') . $creditmemo->getIncrementId(), 35, 780, 'UTF-8');

                /* Add table head */
                $page->setFillColor(new Zend_Pdf_Color_RGB(0.93, 0.92, 0.92));
                $page->setLineColor(new Zend_Pdf_Color_GrayScale(0.5));
                $page->setLineWidth(0.5);
                $page->drawRectangle(25, $this->y, 570, $this->y - 15);
                $this->y -=10;
                $page->setFillColor(new Zend_Pdf_Color_RGB(0.4, 0.4, 0.4));
                $this->_drawHeader($page, $order);
                $this->y -=15;

                $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));

                /* Add body */
                foreach ($creditmemo->getAllItems() as $item) {
                    if ($item->getOrderItem()->getParentItem()) {
                        continue;
                    }

                    if ($this->y < 20) {
                        $page = $this->newPage(array('table_header' => true));
                    }

                    /* Draw item */
                    $page = $this->_drawItem($item, $page, $order);
                }

                /* Add totals */
                $page = $this->insertTotals($page, $creditmemo);
            }

            $this->_afterGetPdf();

            if ($creditmemo->getStoreId()) {
                Mage::app()->getLocale()->revert();
            }
            return $pdf;
        }
    }

    /**
     * Create new page and assign to PDF object
     *
     * @param  array $settings
     * @return Zend_Pdf_Page
     */
    public function newPage(array $settings = array())
    {
        $page = parent::newPage($settings);
        if (!empty($settings['table_header'])) {
            $this->_drawHeader($page);
        }
        return $page;
    }
    /**
     * Insert totals to pdf page
     *
     * @param  Zend_Pdf_Page $page
     * @param  Mage_Sales_Model_Abstract $source
     * @return Zend_Pdf_Page
     */
    
    protected function insertTotals($page, $source) {
        $order = $source->getOrder();

        //custom
        $flubitOrder = $order->getFlubitOrderId();
        $grandtotal_flag = false;


        $totals = $this->_getTotalsList($source);
        $lineBlock = array(
            'lines' => array(),
            'height' => 15
        );
        foreach ($totals as $total) {

            //custom
            if ($flubitOrder && !in_array($total->getData('source_field'), array('shipping_amount', 'grand_total'))) {
                continue;
            }

            $total->setOrder($order)
                    ->setSource($source);

            if ($total->canDisplay()) {
                $total->setFontSize(10);
                foreach ($total->getTotalsForDisplay() as $totalData) {
                    //custom : start
                    if ($flubitOrder && (strpos($totalData['label'], 'Tax:') !== false)) {
                        continue;
                    }

                    if ($flubitOrder && (strpos($totalData['label'], 'Grand Total') !== false)) {
                        if ($grandtotal_flag) {
                            continue;
                        } else {
                            $grandtotal_flag = true;
                        }
                        $totalData['label'] = 'Grand Total';
                    }
                    //custom : end

                    $lineBlock['lines'][] = array(
                        array(
                            'text' => $totalData['label'],
                            'feed' => 475,
                            'align' => 'right',
                            'font_size' => $totalData['font_size'],
                            'font' => 'bold'
                        ),
                        array(
                            'text' => $totalData['amount'],
                            'feed' => 565,
                            'align' => 'right',
                            'font_size' => $totalData['font_size'],
                            'font' => 'bold'
                        ),
                    );
                }
            }
        }

        $this->y -= 20;
        $page = $this->drawLineBlocks($page, array($lineBlock));
        return $page;
    }
}
