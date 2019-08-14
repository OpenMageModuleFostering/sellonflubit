<?php

/**
 * Sales Order Invoice Pdf default items renderer
 *
 * @category   Flubit
 * @package    Flubit_Sales
 */
class Flubit_Flubit_Model_Order_Pdf_Items_Invoice_Default extends Mage_Sales_Model_Order_Pdf_Items_Invoice_Default
{
    /**
     * Draw item line
     */
    public function draw()
    {
    $magentoVersion = Mage::getVersion();
	
	
        $order  = $this->getOrder();
        $flubitOrder = False;

        if ($order->getFlubitOrderId()) {
            $flubitOrder = True;
        }
        $tax_percentage = $order->getFlubitOrderTaxRate();
        //$tax_rate = (($tax_percentage / 100) + 1);
        //$base_price = $_item->getBasePrice();
        //$base_tax_amount = $base_price - ($base_price / $tax_rate);
        $item   = $this->getItem();
        $original_price = '';
        if($flubitOrder) {
        $itemData = $item->getOrderItem()->getData();
        $original_price = round($itemData['original_price'],2);
        }
        $price = $item->getPrice();
        //$tax_amount = $price - ($price / $tax_rate);
        
        
        
        $pdf    = $this->getPdf();
        $page   = $this->getPage();
        $lines  = array();
		if (version_compare($magentoVersion, '1.7', '>=')){   
        if(!$flubitOrder) {
            // draw Product name
            $lines[0] = array(array(
                    'text' => Mage::helper('core/string')->str_split($item->getName(), 35, true, true),
                    'feed' => 35,
            ));

            // draw SKU
            $lines[0][] = array(
                'text' => Mage::helper('core/string')->str_split($this->getSku($item), 17),
                'feed' => 290,
                'align' => 'right'
            );

            // draw QTY
            $lines[0][] = array(
                'text' => $item->getQty() * 1,
                'feed' => 435,
                'align' => 'right'
            );

            // draw item Prices
            $i = 0;
            $prices = $this->getItemPricesForDisplay();
            $feedPrice = 395;
            $feedSubtotal = $feedPrice + 170;
            foreach ($prices as $priceData) {
                if (isset($priceData['label'])) {
                    // draw Price label
                    $lines[$i][] = array(
                        'text' => $priceData['label'],
                        'feed' => $feedPrice,
                        'align' => 'right'
                    );
                    // draw Subtotal label
                    $lines[$i][] = array(
                        'text' => $priceData['label'],
                        'feed' => $feedSubtotal,
                        'align' => 'right'
                    );
                    $i++;
                }
                // draw Price
                $lines[$i][] = array(
                    'text' => $priceData['price'],
                    'feed' => $feedPrice,
                    'font' => 'bold',
                    'align' => 'right'
                );
                // draw Subtotal
                $lines[$i][] = array(
                    'text' => $priceData['subtotal'],
                    'feed' => $feedSubtotal,
                    'font' => 'bold',
                    'align' => 'right'
                );
                $i++;
            }

            // draw Tax
            $lines[0][] = array(
                'text' => $order->formatPriceTxt($item->getTaxAmount()),
                'feed' => 495,
                'font' => 'bold',
                'align' => 'right'
            );
  
        } else {
        // draw Product name
        $lines[0] = array(array(
            'text' => Mage::helper('core/string')->str_split($item->getName(), 15, true, true),
            'feed' => 35,
        ));

        // draw SKU
        $lines[0][] = array(
            'text'  => Mage::helper('core/string')->str_split($this->getSku($item), 8),
            'feed'  => 175,
            'align' => 'right'
        );
        
        // draw FBP
        $lines[0][] = array(
            'text'  => $order->formatPriceTxt($original_price),
            'feed'  => 250,
            'align' => 'right'
        );

        // draw QTY
        $lines[0][] = array(
            'text'  => $item->getQty() * 1,
            'feed'  => 275,
            'align' => 'right'
        );

        // draw item Prices
        $i = 0;
        $prices = $this->getItemPricesForDisplay();
        $feedPrice = 375;
        $feedSubtotal = $feedPrice + 170;
        foreach ($prices as $priceData){
            if (isset($priceData['label'])) {
                // draw Price label
                $lines[$i][] = array(
                    'text'  => $priceData['label'],
                    'feed'  => $feedPrice,
                    'align' => 'right'
                );
                // draw Subtotal label
                $lines[$i][] = array(
                    'text'  => $priceData['label'],
                    'feed'  => $feedSubtotal,
                    'align' => 'right'
                );
                $i++;
            }
            // draw Price
            $lines[$i][] = array(
                'text'  => $priceData['price'],
                'feed'  => $feedPrice,
                'font'  => 'bold',
                'align' => 'right'
            );
            // draw Subtotal
            $lines[$i][] = array(
                'text'  => $priceData['subtotal'],
                'feed'  => $feedSubtotal,
                'font'  => 'bold',
                'align' => 'right'
            );
            $i++;
        }
        
        // draw Tax
           $lines[0][] = array(
            'text'  => $tax_percentage,
            'feed'  => 425,
            'font'  => 'bold',
            'align' => 'right'
        );
        }
        

        // custom options
        $options = $this->getItemOptions();
        if ($options) {
            foreach ($options as $option) {
                // draw options label
                $lines[][] = array(
                    'text' => Mage::helper('core/string')->str_split(strip_tags($option['label']), 40, true, true),
                    'font' => 'italic',
                    'feed' => 35
                );

                if ($option['value']) {
                    if (isset($option['print_value'])) {
                        $_printValue = $option['print_value'];
                    } else {
                        $_printValue = strip_tags($option['value']);
                    }
                    $values = explode(', ', $_printValue);
                    foreach ($values as $value) {
                        $lines[][] = array(
                            'text' => Mage::helper('core/string')->str_split($value, 30, true, true),
                            'feed' => 40
                        );
                    }
                }
            }
        }

        $lineBlock = array(
            'lines'  => $lines,
            'height' => 20
        );
		} else {
			// Magento Version is smaller than 1.7
			// draw Product name
			
		if(!$flubitOrder) {
		
        $lines[0] = array(array(
            'text' => Mage::helper('core/string')->str_split($item->getName(), 60, true, true),
            'feed' => 35,
        ));

        // draw SKU
        $lines[0][] = array(
            'text'  => Mage::helper('core/string')->str_split($this->getSku($item), 25),
            'feed'  => 255
        );

        // draw QTY
        $lines[0][] = array(
            'text'  => $item->getQty()*1,
            'feed'  => 435
        );

        // draw Price
        $lines[0][] = array(
            'text'  => $order->formatPriceTxt($item->getPrice()),
            'feed'  => 395,
            'font'  => 'bold',
            'align' => 'right'
        );

        // draw Tax
        $lines[0][] = array(
            'text'  => $order->formatPriceTxt($item->getTaxAmount()),
            'feed'  => 495,
            'font'  => 'bold',
            'align' => 'right'
        );

        // draw Subtotal
        $lines[0][] = array(
            'text'  => $order->formatPriceTxt($item->getRowTotal()),
            'feed'  => 565,
            'font'  => 'bold',
            'align' => 'right'
        );
		
		} 
		else {
			
        $lines[0] = array(array(
            'text' => Mage::helper('core/string')->str_split($item->getName(), 30, true, true),
            'feed' => 35,
        ));

        // draw SKU
        $lines[0][] = array(
            'text'  => Mage::helper('core/string')->str_split($this->getSku($item), 15),
            'feed'  => 190
        );

		
        // draw Original price
        $lines[0][] = array(
            'text'  => $order->formatPriceTxt($original_price),
            'feed'  => 270,
            'font'  => 'bold',
            'align' => 'right'
        );
		
		 // draw Price
        $lines[0][] = array(
            'text'  => $order->formatPriceTxt($item->getPrice()),
            'feed'  => 370,
            'font'  => 'bold',
            'align' => 'right'
        );
		
        // draw QTY
        $lines[0][] = array(
            'text'  => $item->getQty()*1,
            'feed'  => 390
        );

       

        // draw Tax
        $lines[0][] = array(
            'text'  => $tax_percentage,
            'feed'  => 446,
            'font'  => 'bold',
            'align' => 'right'
        );

        // draw Subtotal
        $lines[0][] = array(
            'text'  => $order->formatPriceTxt($item->getRowTotal()),
            'feed'  => 564,
            'font'  => 'bold',
            'align' => 'right'
        );
		
		}
		 
        // custom options
        $options = $this->getItemOptions();
        if ($options) {
            foreach ($options as $option) {
                // draw options label
                $lines[][] = array(
                    'text' => Mage::helper('core/string')->str_split(strip_tags($option['label']), 70, true, true),
                    'font' => 'italic',
                    'feed' => 35
                );

                if ($option['value']) {
                    $_printValue = isset($option['print_value']) ? $option['print_value'] : strip_tags($option['value']);
                    $values = explode(', ', $_printValue);
                    foreach ($values as $value) {
                        $lines[][] = array(
                            'text' => Mage::helper('core/string')->str_split($value, 50, true, true),
                            'feed' => 40
                        );
                    }
                }
            }
        }

        $lineBlock = array(
            'lines'  => $lines,
            'height' => 20
        );
		} // end else of version check
	
        $page = $pdf->drawLineBlocks($page, array($lineBlock), array('table_header' => true));
        $this->setPage($page);
    }
	//} // end checking version
	
	//}
}
