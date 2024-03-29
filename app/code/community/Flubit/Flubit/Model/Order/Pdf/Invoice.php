<?php

class Flubit_Flubit_Model_Order_Pdf_Invoice extends Mage_Sales_Model_Order_Pdf_Invoice {

    /**
     * Draw header for item table
     *
     * @param Zend_Pdf_Page $page
     * @return void
     */
    protected function _drawHeader(Zend_Pdf_Page $page, Mage_Sales_Model_Order $order) {

        /* Add table head */
        $this->_setFontRegular($page, 10);
        $page->setFillColor(new Zend_Pdf_Color_RGB(0.93, 0.92, 0.92));
        $page->setLineColor(new Zend_Pdf_Color_GrayScale(0.5));
        $page->setLineWidth(0.5);
        $page->drawRectangle(25, $this->y, 570, $this->y - 15);
        $this->y -= 10;
        $page->setFillColor(new Zend_Pdf_Color_RGB(0, 0, 0));

        if (!$order->getData('flubit_order_id')) {
            //columns headers
            $lines[0][] = array(
                'text' => Mage::helper('sales')->__('Products'),
                'feed' => 35
            );

            $lines[0][] = array(
                'text' => Mage::helper('sales')->__('SKU'),
                'feed' => 290,
                'align' => 'right'
            );

            $lines[0][] = array(
                'text' => Mage::helper('sales')->__('Qty'),
                'feed' => 435,
                'align' => 'right'
            );

            $lines[0][] = array(
                'text' => Mage::helper('sales')->__('Price'),
                'feed' => 360,
                'align' => 'right'
            );

            $lines[0][] = array(
                'text' => Mage::helper('sales')->__('Tax'),
                'feed' => 495,
                'align' => 'right'
            );

            $lines[0][] = array(
                'text' => Mage::helper('sales')->__('Subtotal'),
                'feed' => 565,
                'align' => 'right'
            );

            $lineBlock = array(
                'lines' => $lines,
                'height' => 5
            );
        } else {
            //columns headers
            $lines[0][] = array(
                'text' => Mage::helper('sales')->__('Products'),
                'feed' => 35
            );

            $lines[0][] = array(
                'text' => Mage::helper('sales')->__('SKU'),
                'feed' => 175,
                'align' => 'right'
            );

            $lines[0][] = array(
                'text' => Mage::helper('sales')->__('FBP'),
                'feed' => 250,
                'align' => 'right'
            );

            $lines[0][] = array(
                'text' => Mage::helper('sales')->__('Qty'),
                'feed' => 275,
                'align' => 'right'
            );



            $lines[0][] = array(
                'text' => Mage::helper('sales')->__('Price Sold At(VAT inc.)'),
                'feed' => 375,
                'align' => 'right'
            );

            $lines[0][] = array(
                'text' => Mage::helper('sales')->__('Tax Rate'),
                'feed' => 425,
                'align' => 'right'
            );

            $lines[0][] = array(
                'text' => Mage::helper('sales')->__('Actual Price Sold At(VAT inc.)'),
                'feed' => 550,
                'align' => 'right'
            );


            $lineBlock = array(
                'lines' => $lines,
                'height' => 5
            );
        }
        $this->drawLineBlocks($page, array($lineBlock), array('table_header' => true));
        $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
        $this->y -= 20;
    }

    /**
     * Return PDF document
     *
     * @param  array $invoices
     * @return Zend_Pdf
     */
    public function getPdf($invoices = array()) {
        $magentoVersion = Mage::getVersion();
        if (version_compare($magentoVersion, '1.7', '>=')) {
            //version is 1.7 or greater
            $this->_beforeGetPdf();
            $this->_initRenderer('invoice');

            $pdf = new Zend_Pdf();
            $this->_setPdf($pdf);
            $style = new Zend_Pdf_Style();
            $this->_setFontBold($style, 10);

            foreach ($invoices as $invoice) {
                if ($invoice->getStoreId()) {
                    Mage::app()->getLocale()->emulate($invoice->getStoreId());
                    Mage::app()->setCurrentStore($invoice->getStoreId());
                }
                $page = $this->newPage();
                $order = $invoice->getOrder();
                /* Add image */
                $this->insertLogo($page, $invoice->getStore());
                /* Add address */
                $this->insertAddress($page, $invoice->getStore());
                /* Add head */
                $this->insertOrder(
                        $page, $order, Mage::getStoreConfigFlag(self::XML_PATH_SALES_PDF_INVOICE_PUT_ORDER_ID, $order->getStoreId())
                );
                /* Add document text and number */
                $this->insertDocumentNumber(
                        $page, Mage::helper('sales')->__('Invoice # ') . $invoice->getIncrementId()
                );
                /* Add table */
                $this->_drawHeader($page, $order);
                /* Add body */
                foreach ($invoice->getAllItems() as $item) {
                    if ($item->getOrderItem()->getParentItem()) {
                        continue;
                    }
                    /* Draw item */
                    $this->_drawItem($item, $page, $order);
                    $page = end($pdf->pages);
                }
                /* Add totals */
                $this->insertTotals($page, $invoice);
                if ($invoice->getStoreId()) {
                    Mage::app()->getLocale()->revert();
                }
            }
            $this->_afterGetPdf();
        } else {
            // magento version is below 1.7
            $this->_beforeGetPdf();
            $this->_initRenderer('invoice');

            $pdf = new Zend_Pdf();
            $this->_setPdf($pdf);
            $style = new Zend_Pdf_Style();
            $this->_setFontBold($style, 10);

            foreach ($invoices as $invoice) {
                if ($invoice->getStoreId()) {
                    Mage::app()->getLocale()->emulate($invoice->getStoreId());
                    Mage::app()->setCurrentStore($invoice->getStoreId());
                }
                $page = $pdf->newPage(Zend_Pdf_Page::SIZE_A4);
                $pdf->pages[] = $page;

                $order = $invoice->getOrder();

                /* Add image */
                $this->insertLogo($page, $invoice->getStore());

                /* Add address */
                $this->insertAddress($page, $invoice->getStore());

                /* Add head */
                $this->insertOrder($page, $order, Mage::getStoreConfigFlag(self::XML_PATH_SALES_PDF_INVOICE_PUT_ORDER_ID, $order->getStoreId()));


                $page->setFillColor(new Zend_Pdf_Color_GrayScale(1));
                $this->_setFontRegular($page);
                $page->drawText(Mage::helper('sales')->__('Invoice # ') . $invoice->getIncrementId(), 35, 780, 'UTF-8');

                /* Add table */
                $page->setFillColor(new Zend_Pdf_Color_RGB(0.93, 0.92, 0.92));
                $page->setLineColor(new Zend_Pdf_Color_GrayScale(0.5));
                $page->setLineWidth(0.5);

                $page->drawRectangle(25, $this->y, 570, $this->y - 15);
                $this->y -=10;
                if ($order->getData('flubit_order_id')) {
                    /* Add table head */
                    $page->setFillColor(new Zend_Pdf_Color_RGB(0.4, 0.4, 0.4));
                    $page->drawText(Mage::helper('sales')->__('Products'), 35, $this->y, 'UTF-8');
                    $page->drawText(Mage::helper('sales')->__('SKU'), 200, $this->y, 'UTF-8');
                    $page->drawText(Mage::helper('sales')->__('FBP'), 260, $this->y, 'UTF-8');
                    $page->drawText(Mage::helper('sales')->__('Price Sold At(VAT inc.)'), 300, $this->y, 'UTF-8');
                    $page->drawText(Mage::helper('sales')->__('Qty'), 385, $this->y, 'UTF-8');
                    $page->drawText(Mage::helper('sales')->__('Tax Rate'), 420, $this->y, 'UTF-8');
                    $page->drawText(Mage::helper('sales')->__('Actual Price Sold At(VAT inc.)'), 470, $this->y, 'UTF-8');
                } else {
                    /* Add table head */
                    $page->setFillColor(new Zend_Pdf_Color_RGB(0.4, 0.4, 0.4));
                    $page->drawText(Mage::helper('sales')->__('Products'), 35, $this->y, 'UTF-8');
                    $page->drawText(Mage::helper('sales')->__('SKU'), 255, $this->y, 'UTF-8');
                    $page->drawText(Mage::helper('sales')->__('Price'), 380, $this->y, 'UTF-8');
                    $page->drawText(Mage::helper('sales')->__('Qty'), 430, $this->y, 'UTF-8');
                    $page->drawText(Mage::helper('sales')->__('Tax'), 480, $this->y, 'UTF-8');
                    $page->drawText(Mage::helper('sales')->__('Subtotal'), 535, $this->y, 'UTF-8');
                }

                $this->y -=15;

                $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));

                /* Add body */
                foreach ($invoice->getAllItems() as $item) {
                    if ($item->getOrderItem()->getParentItem()) {
                        continue;
                    }

                    if ($this->y < 15) {
                        $page = $this->newPage(array('table_header' => true));
                    }

                    /* Draw item */
                    $page = $this->_drawItem($item, $page, $order);
                }

                /* Add totals */
                $page = $this->insertTotals($page, $invoice);

                if ($invoice->getStoreId()) {
                    Mage::app()->getLocale()->revert();
                }
            }

            $this->_afterGetPdf();
        }

        return $pdf;
    }

    /**
     * Create new page and assign to PDF object
     *
     * @param array $settings
     * @return Zend_Pdf_Page
     */
    public function newPage(array $settings = array()) {
        $magentoVersion = Mage::getVersion();
        if (version_compare($magentoVersion, '1.7', '>=')) {
            //version is 1.7 or greater
            /* Add new table head */
            $page = $this->_getPdf()->newPage(Zend_Pdf_Page::SIZE_A4);
            $this->_getPdf()->pages[] = $page;
            $this->y = 800;

            if (!empty($settings['table_header'])) {
                $this->_setFontRegular($page);
                $page->setFillColor(new Zend_Pdf_Color_RGB(0.93, 0.92, 0.92));
                $page->setLineColor(new Zend_Pdf_Color_GrayScale(0.5));
                $page->setLineWidth(0.5);
                $page->drawRectangle(25, $this->y, 570, $this->y - 15);
                $this->y -=10;

                $page->setFillColor(new Zend_Pdf_Color_RGB(0.4, 0.4, 0.4));
                $page->drawText(Mage::helper('sales')->__('Product'), 35, $this->y, 'UTF-8');
                $page->drawText(Mage::helper('sales')->__('SKU'), 255, $this->y, 'UTF-8');
                $page->drawText(Mage::helper('sales')->__('Price'), 380, $this->y, 'UTF-8');
                $page->drawText(Mage::helper('sales')->__('Qty'), 430, $this->y, 'UTF-8');
                $page->drawText(Mage::helper('sales')->__('Tax'), 480, $this->y, 'UTF-8');
                $page->drawText(Mage::helper('sales')->__('Subtotal'), 535, $this->y, 'UTF-8');

                $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
                $this->y -=20;
            }
        } else {
            //version is below 1.7
            /* Add new table head */
            $page = $this->_getPdf()->newPage(Zend_Pdf_Page::SIZE_A4);
            $this->_getPdf()->pages[] = $page;
            $this->y = 800;

            if (!empty($settings['table_header'])) {
                $this->_setFontRegular($page);
                $page->setFillColor(new Zend_Pdf_Color_RGB(0.93, 0.92, 0.92));
                $page->setLineColor(new Zend_Pdf_Color_GrayScale(0.5));
                $page->setLineWidth(0.5);
                $page->drawRectangle(25, $this->y, 570, $this->y - 15);
                $this->y -=10;

                $page->setFillColor(new Zend_Pdf_Color_RGB(0.4, 0.4, 0.4));
                $page->drawText(Mage::helper('sales')->__('Product'), 35, $this->y, 'UTF-8');
                $page->drawText(Mage::helper('sales')->__('SKU'), 255, $this->y, 'UTF-8');
                $page->drawText(Mage::helper('sales')->__('Price'), 380, $this->y, 'UTF-8');
                $page->drawText(Mage::helper('sales')->__('Qty'), 430, $this->y, 'UTF-8');
                $page->drawText(Mage::helper('sales')->__('Tax'), 480, $this->y, 'UTF-8');
                $page->drawText(Mage::helper('sales')->__('Subtotal'), 535, $this->y, 'UTF-8');

                $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
                $this->y -=20;
            }
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