<?php



/**

 * Class Flubit Model Mysql Flubit Collection

 * 

 * @package Flubit

 * @category Flubit_Model

 * @author Flubit team

 */

class Flubit_Flubit_Model_Mysql4_Flubit_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract {



    /**

     * Constructor for Flubit call

     * 

     */

    public function _construct() {

        $this->_init("flubit/flubit");

    }

    public function addCategoryFilter(Mage_Catalog_Model_Category $category)
    {


        return $this;
    }

	
	
}



