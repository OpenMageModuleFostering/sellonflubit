<?php



/**

 * Class admin Flubit Grid

 * 

 * @package Flubit

 * @category Flubit_Block

 * @author Flubit team

 */

class Flubit_Flubit_Block_Adminhtml_Flubit_Grid extends Mage_Adminhtml_Block_Widget_Grid {



    /**

     * 

     * Construct for autoload and set property

     */

    public function __construct() {

        try {

            parent::__construct();

            $this->setId('flubitGrid');

            $this->setDefaultSort('flubit_id');

            $this->setDefaultDir('DESC');

            $this->setSaveParametersInSession(true);

        } catch (Exception $e) {

            Mage::log(__LINE__ . 'Exception Flubit_Flubit_Block_Adminhtml_Flubit_Grid  __construct ' . $e, null, Flubit_Flubit_Helper_Data::FLUBIT_EXCEPTIONS);

        }

    }



    /**

     * 

     * private method for prepare collection

     */

    protected function _prepareCollection() {

        try {

            $attributeModel = Mage::getModel('eav/entity_attribute')->loadByCode('catalog_product', 'price');

            $entity_id = $attributeModel->attribute_id;
			$flubit_ean_attributeId = Mage::getResourceModel('eav/entity_attribute')->getIdByCode('catalog_product', 'flubit_ean');
			$flubit_upc_attributeId = Mage::getResourceModel('eav/entity_attribute')->getIdByCode('catalog_product', 'flubit_upc');
			$flubit_asin_attributeId = Mage::getResourceModel('eav/entity_attribute')->getIdByCode('catalog_product', 'flubit_asin');
			$flubit_isbn_attributeId = Mage::getResourceModel('eav/entity_attribute')->getIdByCode('catalog_product', 'flubit_isbn');
			$flubit_mpn_attributeId = Mage::getResourceModel('eav/entity_attribute')->getIdByCode('catalog_product', 'flubit_mpn');
			$flubit_brand_attributeId = Mage::getResourceModel('eav/entity_attribute')->getIdByCode('catalog_product', 'flubit_brand');
			// custom attributes
			$flubit_custom_attributes = $this ->getCustomAttributes();



            $collection = Mage::getModel('flubit/flubit')->getCollection();


            $this->setCollection($collection);

            //custom code

            $collection->getSelect()->join(array('product_entity' => Mage::getConfig()->getTablePrefix() . 'catalog_product_entity'), 'product_entity.sku = main_table.sku', array('product_entity.entity_id'));



            $collection->getSelect()->join(array('product_price' => Mage::getConfig()->getTablePrefix() . 'catalog_product_entity_decimal'), 'product_price.entity_id = product_entity.entity_id and product_price.attribute_id = ' . $entity_id, array('mprice' => 'ROUND(product_price.`value`,2)'));
			
			// the above line has issues with multistore the below modification shoulñd be uncommented so it use default store
            //$collection->getSelect()->join(array('product_price' => Mage::getConfig()->getTablePrefix() . 'catalog_product_entity_decimal'), 'product_price.entity_id = product_entity.entity_id and product_price.store_id=0 and product_price.attribute_id = ' . $entity_id, array('mprice' => 'ROUND(product_price.`value`,2)'));

            $collection->getSelect()->join(array('flubit1' => Mage::getConfig()->getTablePrefix() . 'catalog_product_entity_varchar'), 'flubit1.entity_id = product_entity.entity_id and flubit1.attribute_id = ' . $flubit_ean_attributeId, array('flubit_ean' => 'flubit1.value'));
            $collection->getSelect()->join(array('flubit2' => Mage::getConfig()->getTablePrefix() . 'catalog_product_entity_varchar'), 'flubit2.entity_id = product_entity.entity_id and flubit2.attribute_id = ' . $flubit_upc_attributeId, array('flubit_upc' => 'flubit2.value'));
            $collection->getSelect()->join(array('flubit3' => Mage::getConfig()->getTablePrefix() . 'catalog_product_entity_varchar'), 'flubit3.entity_id = product_entity.entity_id and flubit3.attribute_id = ' . $flubit_asin_attributeId, array('flubit_asin' => 'flubit3.value'));
            $collection->getSelect()->join(array('flubit4' => Mage::getConfig()->getTablePrefix() . 'catalog_product_entity_varchar'), 'flubit4.entity_id = product_entity.entity_id and flubit4.attribute_id = ' . $flubit_isbn_attributeId, array('flubit_isbn' => 'flubit4.value'));
            $collection->getSelect()->join(array('flubit5' => Mage::getConfig()->getTablePrefix() . 'catalog_product_entity_varchar'), 'flubit5.entity_id = product_entity.entity_id and flubit5.attribute_id = ' . $flubit_mpn_attributeId, array('flubit_mpn' => 'flubit5.value'));
            $collection->getSelect()->join(array('flubit6' => Mage::getConfig()->getTablePrefix() . 'catalog_product_entity_varchar'), 'flubit6.entity_id = product_entity.entity_id and flubit6.attribute_id = ' . $flubit_brand_attributeId, array('flubit_brand' => 'flubit6.value'));
			
			if (count($flubit_custom_attributes) > 0) { // only if we have ids
				foreach ($flubit_custom_attributes as $flubit_custom_attribute) {
				// Add our custom attributes
        //$collection->addAttributeToSelect($flubit_custom_attributeId);	
					
            $collection->getSelect()->joinLeft(array('flubit_'.$flubit_custom_attribute['id'] => Mage::getConfig()->getTablePrefix() . 'catalog_product_entity_int'), 'flubit_'.$flubit_custom_attribute['id'].'.entity_id = product_entity.entity_id and flubit_'.$flubit_custom_attribute['id'].'.attribute_id = ' . $flubit_custom_attribute['id'], array($flubit_custom_attribute['code'] => 'flubit_'.$flubit_custom_attribute['id'].'.value'));
					
				}
			}
			
			//$collection->getSelect()->joinLeft(array('category_id' => Mage::getConfig()->getTablePrefix() . 'catalog_category_product'),'category_id.product_id=product_entity.entity_id',array('category_id' => 'category_id.category_id'));

			
 
			//echo $collection->getSelect()->__toString(); exit;


            return parent::_prepareCollection();

        } catch (Exception $e) {

            Mage::log(__LINE__ . 'Exception Flubit_Flubit_Block_Adminhtml_Flubit_Grid  _prepareCollection ' . $collection->getSelect()->__toString() . $e, null, Flubit_Flubit_Helper_Data::FLUBIT_EXCEPTIONS);

        }

    }



    /**

     * 

     * private method for prepare Columns

     */

    protected function _prepareColumns() {
		
		
		$this->addExportType('*/*/exportCsv', Mage::helper('flubit')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('flubit')->__('XML'));

        $this->addColumn('flubit_id', array(

            'header' => Mage::helper('flubit')->__('ID'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'flubit_id',

        ));

        $this->addColumn('sku', array(

            'header' => Mage::helper('flubit')->__('SKU'),
            'align' => 'left',
            'index' => 'sku',
            'width' => '200',
            'filter_index' => 'main_table.sku'

        ));

        $this->addColumn('name', array(

            'header' => Mage::helper('flubit')->__('Name'),
            'align' => 'left',
            'index' => 'name',

        ));
		
        $this->addColumn('flubit_ean', array(

            'header' => Mage::helper('flubit')->__('EAN'),
            'align' => 'left',
            'index' => 'flubit_ean',
            'filter_index' => 'flubit1.value',

        ));
        $this->addColumn('flubit_upc', array(

            'header' => Mage::helper('flubit')->__('UPC'),
            'align' => 'left',
            'index' => 'flubit_upc',
            'filter_index' => 'flubit2.value',

        ));
        $this->addColumn('flubit_asin', array(

            'header' => Mage::helper('flubit')->__('ASIN'),
            'align' => 'left',
            'index' => 'flubit_asin',
            'filter_index' => 'flubit3.value',

        ));
        $this->addColumn('flubit_isbn', array(

            'header' => Mage::helper('flubit')->__('ISBN'),
            'align' => 'left',
            'index' => 'flubit_isbn',
            'filter_index' => 'flubit4.value',

        ));
        $this->addColumn('flubit_mpn', array(
            'header' => Mage::helper('flubit')->__('MPN'),
            'align' => 'left',
            'index' => 'flubit_mpn',
            'filter_index' => 'flubit5.value',

        ));
        $this->addColumn('flubit_brand', array(

            'header' => Mage::helper('flubit')->__('Brand'),
            'align' => 'left',
            'index' => 'flubit_brand',
            'filter_index' => 'flubit6.value',

        ));
		
		// custom attributes

		$flubit_custom_attributes = $this ->getCustomAttributes();
		if (count($flubit_custom_attributes) > 0) {
			foreach ($flubit_custom_attributes as $flubit_custom_attribute) {
			$this->addColumn($flubit_custom_attribute['code'], array(
        		'header'=> Mage::helper('catalog')->__($flubit_custom_attribute['label']),
        		'width' => '100px',
        		'index' => $flubit_custom_attribute['code'],
        		'filter_index' => 'flubit_'.$flubit_custom_attribute['id'].'.value',
        		'type'  => 'options',
        		'options' => $this->_getAttributeOptions($flubit_custom_attribute['id']),

			));
			}
		}



	    $this->addColumn('category_list', array(
            'header'    => Mage::helper('flubit')->__('Category'),
			'sortable'  => false,
			'width' => '250px',
			'type'  => 'options',
			'options'   => $this->toOptionArray(),
			'renderer'  => 'flubit/adminhtml_flubit_grid_render_category',
			'filter_condition_callback' => array($this, 'filterCallback'),
            ),'name');
			

        $this->addColumn('active_status', array(

            'header' => Mage::helper('flubit')->__('Product Status'),
            'align' => 'left',
            'index' => 'active_status',
            'type' => 'options',
            'options' => array(
                0 => 'Inactive',
                1 => 'Active',
            ),

        ));
		
		 $this->addColumn('user_disabled', array(

            'header' => Mage::helper('flubit')->__('Overide State'),
            'align' => 'left',
            'index' => 'user_disabled',
            'type' => 'options',
            'options' => array(
                0 => 'Enabled',
                1 => 'Manually Disabled',
            ),

        ));

        $this->addColumn('qty', array(

            'header' => Mage::helper('flubit')->__('Stock Qty'),
            'width' => '50px',
            'index' => 'qty',

        ));

        $this->addColumn('mprice', array(

            'header' => Mage::helper('flubit')->__('Product Price'),
            'width' => '120px',
            'index' => 'mprice',
            'filter_index' => 'product_price.`value`'

        ));

        $this->addColumn('price', array(

            'header' => Mage::helper('flubit')->__('Flubit Price'),
            'width' => '120px',
            'renderer' => 'flubit/adminhtml_widget_grid_column_renderer_inline',
            'index' => 'price',

        ));

        $this->addColumn('use_global_price', array(

            'header' => Mage::helper('flubit')->__('Use Global Price'),
            'width' => '60px',
            'align' => 'center',
            'renderer' => 'flubit/adminhtml_widget_grid_column_renderer_inlineCheckbox',
            'index' => 'use_global_price',
            'filter' => false,
            'sortable' => false,

        ));



        $this->addColumn('action', array(

            'header' => Mage::helper('flubit')->__('Action'),
            'width' => '100',
            'align' => 'center',
            'renderer' => 'flubit/adminhtml_widget_grid_column_renderer_inlineUpdateButton',
            'filter' => false,
            'sortable' => false,

        ));
		



        return parent::_prepareColumns();

    }
	
	
	protected function _getAttributeOptions($attribute_code)
    {
        $attribute = Mage::getModel('eav/config')->getAttribute('catalog_product', $attribute_code);
        $options = array();
        foreach( $attribute->getSource()->getAllOptions(true, true) as $option ) {
            $options[$option['value']] = $option['label'];
        }
        return $options;
    }
	
	
	/** add mass actions to grid **/

	protected function _prepareMassaction()
		{
			$this->setMassactionIdField('flubit_id');
			$this->getMassactionBlock()->setFormFieldName('flubit_id'); 
 
			
			$statuses =
		 		array(
            	'inactive'   => Mage::helper('flubit')->__('Manually Disable'),
            	'active'    => Mage::helper('flubit')->__('Enable')
        		);

        	array_unshift($statuses, array('label'=>'', 'value'=>''));
        	$this->getMassactionBlock()->addItem('status', array(
             	'label'=> Mage::helper('catalog')->__('State Overide'),
             	'url'  => $this->getUrl('*/*/massaction'),
             	'additional' => array(
                    			'visibility' => array(
                         		'name' => 'status',
                         		'type' => 'select',
                         		'class' => 'required-entry',
                         		'label' => Mage::helper('flubit')->__('Status'),
                         		'values' => $statuses
                     							)
             					),    
				'confirm' => Mage::helper('flubit')->__('Are you sure?')
       		));
			
			
			
			$prices =
		 		array(
            	'global'    => Mage::helper('flubit')->__('Use Global Price'),
            	'manual'   => Mage::helper('flubit')->__('Use Manual Price')
        		);

        	array_unshift($prices, array('label'=>'', 'value'=>''));
        	$this->getMassactionBlock()->addItem('price', array(
             	'label'=> Mage::helper('catalog')->__('Change price mode'),
             	'url'  => $this->getUrl('*/*/massaction'),
             	'additional' => array(
                    			'visibility' => array(
                         		'name' => 'price',
                         		'type' => 'select',
                         		'class' => 'required-entry',
                         		'label' => Mage::helper('flubit')->__('Price mode'),
                         		'values' => $prices
                     							)
             					),    
				'confirm' => Mage::helper('flubit')->__('Are you sure?')
       		));
			
			$this->getMassactionBlock()->addItem('blank', array(
             	'label'=> Mage::helper('catalog')->__('----------'),
             	'url'  => ''
       		));
			
			
        	$this->getMassactionBlock()->addItem('changepriceFixed', array(
             	'label'=> Mage::helper('catalog')->__('Override with Fixed price'),
             	'url'  => $this->getUrl('*/*/massaction'),
             	'additional' => array(
                    			'visibility' => array(
                         		'name' => 'priceIncreaseFixed',
                         		'type' => 'text',
                         		'class' => 'required-entry',
                         		'label' => Mage::helper('flubit')->__('Amount')
                     							)
             					),    
				'confirm' => Mage::helper('flubit')->__('Are you sure?')
       		));
        	$this->getMassactionBlock()->addItem('changeprice', array(
             	'label'=> Mage::helper('catalog')->__('Increase Prices'),
             	'url'  => $this->getUrl('*/*/massaction'),
             	'additional' => array(
                    			'visibility' => array(
                         		'name' => 'priceIncrease',
                         		'type' => 'text',
                         		'class' => 'required-entry',
                         		'label' => Mage::helper('flubit')->__('Amount')
                     							)
             					),    
				'confirm' => Mage::helper('flubit')->__('Are you sure?')
       		));
        	$this->getMassactionBlock()->addItem('changepricecent', array(
             	'label'=> Mage::helper('catalog')->__('Increase Prices by %'),
             	'url'  => $this->getUrl('*/*/massaction'),
             	'additional' => array(
                    			'visibility' => array(
                         		'name' => 'priceIncreaseCent',
                         		'type' => 'text',
                         		'class' => 'required-entry',
                         		'label' => Mage::helper('flubit')->__('% Amount')
                     							)
             					),    
				'confirm' => Mage::helper('flubit')->__('Are you sure?')
       		));
        	$this->getMassactionBlock()->addItem('changepricedown', array(
             	'label'=> Mage::helper('catalog')->__('Decrease Prices'),
             	'url'  => $this->getUrl('*/*/massaction'),
             	'additional' => array(
                    			'visibility' => array(
                         		'name' => 'priceDecrease',
                         		'type' => 'text',
                         		'class' => 'required-entry',
                         		'label' => Mage::helper('flubit')->__('Amount')
                     							)
             					),    
				'confirm' => Mage::helper('flubit')->__('Are you sure?')
       		));
        	$this->getMassactionBlock()->addItem('changepricedowncent', array(
             	'label'=> Mage::helper('catalog')->__('Decrease Prices %'),
             	'url'  => $this->getUrl('*/*/massaction'),
             	'additional' => array(
                    			'visibility' => array(
                         		'name' => 'priceDecreaseCent',
                         		'type' => 'text',
                         		'class' => 'required-entry',
                         		'label' => Mage::helper('flubit')->__('% Amount')
                     							)
             					),    
				'confirm' => Mage::helper('flubit')->__('Are you sure?')
       		));
			
 
	return $this;
	}


    /**

     * Method for Get Row Url

     * 

     * @param Mixed $row

     * @return String

     */
	
	 
	public function filterCallback($collection, $column)
    {
		if (!$value = $column->getFilter()->getValue()) 
		{
    		return $collection;
		}
		
        $conditions = array(
            'cat_pro.product_id=product_entity.entity_id',
            $collection->getConnection()->quoteInto('cat_pro.category_id=?', $value)
        );
        $joinCond = join(' AND ', $conditions);

        $fromPart = $collection->getSelect()->getPart(Zend_Db_Select::FROM);
        if (isset($fromPart['cat_pro'])) {
            $fromPart['cat_pro']['joinCondition'] = $joinCond;
            $this->getSelect()->setPart(Zend_Db_Select::FROM, $fromPart);
        }
        else { // we are joining the category table with an and on the cat id
            $collection->getSelect()->join(
                array('cat_pro' => $collection->getTable('catalog/category_product')),
                $joinCond
            );
        }
		
		return $collection;
		
    }
	
	
	
	
	   public function toOptionArray($addEmpty = true)
    {
        $options = array();
        foreach ($this->load_tree() as $category) {
            $options[$category['value']] =  $category['label'];
        }

        return $options;
    }



    public function buildCategoriesMultiselectValues(Varien_Data_Tree_Node $node, $values, $level = 0)
    {
        $level++;

        $values[$node->getId()]['value'] =  $node->getId();
        $values[$node->getId()]['label'] = str_repeat("--", $level) . $node->getName();

        foreach ($node->getChildren() as $child)
        {
            $values = $this->buildCategoriesMultiselectValues($child, $values, $level);
        }

        return $values;
    }

    public function load_tree()
    {
        $store = Mage::app()->getFrontController()->getRequest()->getParam('store', 0);
        $parentId = $store ? Mage::app()->getStore($store)->getRootCategoryId() : 1;  // Current store root category

        $tree = Mage::getResourceSingleton('catalog/category_tree')->load();

        $root = $tree->getNodeById($parentId);

        if($root && $root->getId() == 1)
        {
            $root->setName(Mage::helper('catalog')->__('Root'));
        }

        $collection = Mage::getModel('catalog/category')->getCollection()
        ->setStoreId($store)
        ->addAttributeToSelect('name')
        ->addAttributeToSelect('is_active');

        $tree->addCollectionData($collection, true);

        return $this->buildCategoriesMultiselectValues($root, array());
    }
	
	
	 
    public function getCustomAttributes() {
		
		$customAttributes = Mage::getStoreConfig('flubit_section/flubit_grid/product_grid_attributes');
			if (strlen($customAttributes)>2) {
				
				$customAttributes = explode(',',$customAttributes);
				$i = 0;
				
				foreach ($customAttributes as $customAttribute) {
					 $flubit_custom_attributeId = Mage::getResourceModel('eav/entity_attribute')->getIdByCode('catalog_product', trim($customAttribute));	
					if ($flubit_custom_attributeId > 0) { // make sure we have found an id
					 	$flubit_custom_attributeLabel = Mage::getSingleton('eav/config')->getAttribute('catalog_product',trim($customAttribute))->getFrontendLabel();
						$flubit_custom_attributes[$i]['id'] = $flubit_custom_attributeId;
						$flubit_custom_attributes[$i]['code'] = $customAttribute;
						$flubit_custom_attributes[$i]['label'] = $flubit_custom_attributeLabel; // these need improving to get the label
						$i++;
					}
				}
				
			}
		return $flubit_custom_attributes;
	}
	 
	 

    public function getRowUrl($row) {

        try {

            if ($row['sku']) {

                $productId = Mage::getModel('catalog/product')->getIdBySku($row['sku']);

                return $this->getUrl('adminhtml/catalog_product/edit', array('id' => $productId));

            }

        } catch (Exception $e) {

            Mage::log(__LINE__ . 'Exception Flubit_Flubit_Block_Adminhtml_Flubit_Grid  _prepareCollection ' . $e, null, Flubit_Flubit_Helper_Data::FLUBIT_EXCEPTIONS);

        }

    }



}