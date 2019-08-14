<?php
/**
 * Sales Order Creditmemo Pdf default items renderer
 *
 * @category   Mage
 * @package    Mage_Sales
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Flubit_Flubit_Model_Order_Pdf_Items_Creditmemo_Default extends Mage_Sales_Model_Order_Pdf_Items_Creditmemo_Default
{
    /**
     * Draw process
     */
    public function draw()
    {
        $order  = $this->getOrder();
        $flubitOrder = False;

        if ($order->getFlubitOrderId()) {
            $flubitOrder = True;
        }
        $tax_percentage = $order->getFlubitOrderTaxRate();
        $item   = $this->getItem();
        $original_price = '';
        if($flubitOrder) {
        $itemData = $item->getOrderItem()->getData();
        $original_price = round($itemData['original_price'],2);
        }
        $price = $item->getPrice();
        //$tax_amount = $price - ($price / $tax_rate);
        
        $item   = $this->getItem();
        $pdf    = $this->getPdf();
        $page   = $this->getPage();
        $lines  = array();
        
        $magentoVersion = Mage::getVersion();
        if (version_compare($magentoVersion, '1.7', '>=')) {
        if(!$flubitOrder) {
        // draw Product name
        $lines[0] = array(array(
            'text' => Mage::helper('core/string')->str_split($item->getName(), 35, true, true),
            'feed' => 35,
        ));

        // draw SKU
        $lines[0][] = array(
            'text'  => Mage::helper('core/string')->str_split($this->getSku($item), 17),
            'feed'  => 255,
            'align' => 'right'
        );

        // draw Total (ex)
        $lines[0][] = array(
            'text'  => $order->formatPriceTxt($item->getRowTotal()),
            'feed'  => 330,
            'font'  => 'bold',
            'align' => 'right',
        );

        // draw Discount
        $lines[0][] = array(
            'text'  => $order->formatPriceTxt(-$item->getDiscountAmount()),
            'feed'  => 380,
            'font'  => 'bold',
            'align' => 'right'
        );

        // draw QTY
        $lines[0][] = array(
            'text'  => $item->getQty() * 1,
            'feed'  => 445,
            'font'  => 'bold',
            'align' => 'right',
        );

        // draw Tax
        $lines[0][] = array(
            'text'  => $order->formatPriceTxt($item->getTaxAmount()),
            'feed'  => 495,
            'font'  => 'bold',
            'align' => 'right'
        );

        // draw Total (inc)
        $subtotal = $item->getRowTotal() + $item->getTaxAmount() + $item->getHiddenTaxAmount()
            - $item->getDiscountAmount();
        $lines[0][] = array(
            'text'  => $order->formatPriceTxt($subtotal),
            'feed'  => 565,
            'font'  => 'bold',
            'align' => 'right'
        );
    } else { // if flubit product
        // draw flubit Product name
            $lines[0] = array(array(
                    'text' => Mage::helper('core/string')->str_split($item->getName(), 30, true, true),
                    'feed' => 35,
            ));

            // draw flubit SKU
            $lines[0][] = array(
                'text' => Mage::helper('core/string')->str_split($this->getSku($item), 15),
                'feed' => 220,
                'align' => 'right'
            );

            // draw FBP
            $lines[0][] = array(
                'text' => $order->formatPriceTxt($original_price),
                'feed' => 265,
                'font' => 'bold',
                'align' => 'right',
            );
            
             // draw Price sold at
            $lines[0][] = array(
                'text' => $order->formatPriceTxt($price),
                'feed' => 350,
                'font' => 'bold',
                'align' => 'right',
            );

            //  flubit draw QTY
            $lines[0][] = array(
                'text' => $item->getQty() * 1,
                'feed' => 375,
                'font' => 'bold',
                'align' => 'right',
            );

            // draw flubit  Tax rate
            $lines[0][] = array(
                'text' => $tax_percentage,
                'feed' => 420,
                'font' => 'bold',
                'align' => 'right'
            );

            // draw Total (inc)
            $subtotal = $item->getRowTotal() + $item->getTaxAmount() + $item->getHiddenTaxAmount() - $item->getDiscountAmount();
            $lines[0][] = array(
                'text' => $order->formatPriceTxt($subtotal),
                'feed' => 550,
                'font' => 'bold',
                'align' => 'right'
            );
            
    }

        // draw options
        $options = $this->getItemOptions();
        if ($options) {
            foreach ($options as $option) {
                // draw options label
                $lines[][] = array(
                    'text' => Mage::helper('core/string')->str_split(strip_tags($option['label']), 40, true, true),
                    'font' => 'italic',
                    'feed' => 35
                );

                // draw options value
                $_printValue = isset($option['print_value']) ? $option['print_value'] : strip_tags($option['value']);
                $lines[][] = array(
                    'text' => Mage::helper('core/string')->str_split($_printValue, 30, true, true),
                    'feed' => 40
                );
            }
        }

        $lineBlock = array(
            'lines'  => $lines,
            'height' => 20
        );

        $page = $pdf->drawLineBlocks($page, array($lineBlock), array('table_header' => true));
        $this->setPage($page);
        } else {
        // if version is lower than 1.6
        if ($flubitOrder) {
                $leftBound = 35;
                $rightBound = 565;
                //flubit order
                $x = $leftBound;
                // draw Product name
                $lines[0] = array(array(
                        'text' => Mage::helper('core/string')->str_split($item->getName(), 60, true, true),
                        'feed' => $x,
                ));
                //flubit order
                $x += 140;
                // draw SKU
                $lines[0][] = array(
                    'text' => Mage::helper('core/string')->str_split($this->getSku($item), 25),
                    'feed' => $x
                );
                //flubit order
                $x += 90;
                // draw Total (ex)
                $lines[0][] = array(
                    'text' => $order->formatPriceTxt($original_price),
                    'feed' => $x,
                    'font' => 'bold',
                    'align' => 'right',
                    'width' => 10,
                );
               
                //flubit order
                $x += 40;
                // draw Discount flubit tax percentage
                $lines[0][] = array(
                    'text' => $order->formatPriceTxt($price),
                    'feed' => $x,
                    'font' => 'bold',
                    'align' => 'right',
                    'width' => 50,
                );
                //flubit order
                $x += 50;
                // draw QTY
                $lines[0][] = array(
                    'text' => $item->getQty() * 1,
                    'feed' => $x,
                    'font' => 'bold',
                    'align' => 'center',
                    'width' => 30,
                );
                //flubit order
                $x += 30;
                 // draw flubit proice sold at
                $lines[0][] = array(
                    'text' => $tax_percentage,
                    'feed' => $x,
                    'font' => 'bold',
                    'align' => 'right',
                    'width' => 45,
                );
                //flubit order
                $x += 45;
                // draw Subtotal
                $lines[0][] = array(
                    'text' => $order->formatPriceTxt($price),
                    'feed' => $rightBound,
                    'font' => 'bold',
                    'align' => 'right'
                );
            } else {
                $leftBound = 35;
                $rightBound = 560;

                $x = $leftBound;
                // draw Product name
                $lines[0] = array(array(
                        'text' => Mage::helper('core/string')->str_split($item->getName(), 60, true, true),
                        'feed' => $x,
                ));

                $x += 220;
                // draw SKU
                $lines[0][] = array(
                    'text' => Mage::helper('core/string')->str_split($this->getSku($item), 25),
                    'feed' => $x
                );

                $x += 100;
                // draw Total (ex)
                $lines[0][] = array(
                    'text' => $order->formatPriceTxt($item->getRowTotal()),
                    'feed' => $x,
                    'font' => 'bold',
                    'align' => 'right',
                    'width' => 50,
                );

                $x += 50;
                // draw Discount
                $lines[0][] = array(
                    'text' => $order->formatPriceTxt(-$item->getDiscountAmount()),
                    'feed' => $x,
                    'font' => 'bold',
                    'align' => 'right',
                    'width' => 50,
                );

                $x += 50;
                // draw QTY
                $lines[0][] = array(
                    'text' => $item->getQty() * 1,
                    'feed' => $x,
                    'font' => 'bold',
                    'align' => 'center',
                    'width' => 30,
                );

                $x += 30;
                // draw Tax
                $lines[0][] = array(
                    'text' => $order->formatPriceTxt($item->getTaxAmount()),
                    'feed' => $x,
                    'font' => 'bold',
                    'align' => 'right',
                    'width' => 45,
                );

                $x += 45;
                // draw Subtotal
                $lines[0][] = array(
                    'text' => $order->formatPriceTxt($item->getRowTotal() + $item->getTaxAmount() - $item->getDiscountAmount()),
                    'feed' => $rightBound,
                    'font' => 'bold',
                    'align' => 'right'
                );
            }
            // draw options
            $options = $this->getItemOptions();
            if ($options) {
                foreach ($options as $option) {
                    // draw options label
                    $lines[][] = array(
                        'text' => Mage::helper('core/string')->str_split(strip_tags($option['label']), 70, true, true),
                        'font' => 'italic',
                        'feed' => $leftBound
                    );

                    // draw options value
                    $_printValue = isset($option['print_value']) ? $option['print_value'] : strip_tags($option['value']);
                    $lines[][] = array(
                        'text' => Mage::helper('core/string')->str_split($_printValue, 50, true, true),
                        'feed' => $leftBound + 5
                    );
                }
            }

            $lineBlock = array(
                'lines' => $lines,
                'height' => 10
            );

            $page = $pdf->drawLineBlocks($page, array($lineBlock), array('table_header' => true));
            $this->setPage($page);
            
            
    }
    }
    
}
