<?php

  /**
   * List of product records
   * light-weight version of shop_products
   *
   **/           

	class OOGoogleFeed_Product extends Db_ActiveRecord
	{
		public $table_name = 'shop_products';

		public $implement = 'Db_AutoFootprints';
		public $auto_footprints_visible = true;

		public $enabled = true;
    
    
    public $belongs_to = array(
			'manufacturer'=>array('class_name'=>'Shop_Manufacturer', 'foreign_key'=>'manufacturer_id')
    );
    
    		public $has_many = array(
            'images'=>array('class_name'=>'Db_File', 'foreign_key'=>'master_object_id', 'conditions'=>"master_object_class='Shop_Product' and field='images'", 'order'=>'sort_order, id', 'delete'=>true)
        );
    
    /******************************************************************************
     *  Discounted Pricing Area
     *  
     **********************************************************************************/
                   
    
   	/**
		 * Returns the product discounted price for the specified cart item quantity.
		 * If there are no price rules defined for the product, returns the product original price 
		 * (taking into account tier prices)
		 */
		public function get_discounted_price_no_tax($quantity, $customer_group_id = null)
		{
			if ($customer_group_id === null )
				$customer_group_id = Cms_Controller::get_customer_group_id();
        
        
			if (!strlen($this->price_rules_compiled))
				return $this->price_no_tax($quantity, $customer_group_id);

			$price_rules = array();
			try
			{
				$price_rules = unserialize($this->price_rules_compiled);
			} catch (Exception $ex)
			{
				throw new Phpr_ApplicationException('Error loading price rules for the "'.$this->name.'" product');
			}

			if (!array_key_exists($customer_group_id, $price_rules))
				return $this->price_no_tax($quantity, $customer_group_id);

			$price_tiers = $price_rules[$customer_group_id];
			$price_tiers = array_reverse($price_tiers, true);

			foreach ($price_tiers as $tier_quantity=>$price)
			{
				if ($tier_quantity <= $quantity)
					return round($price, 2);
			}

			return $this->price_no_tax($quantity, $customer_group_id);
		}
		
		/**
		 * Returns the product discounted price for the specified cart item quantity, with taxes included
		 * If there are no price rules defined for the product, returns the product original price 
		 * (taking into account tier prices).
		 * Includes tax if the "Display catalog/cart prices including tax" option is enabled
		 */
		public function get_discounted_price($quantity = 1, $customer_group_id = null)
		{
			$price = $this->get_discounted_price_no_tax($quantity, $customer_group_id);
			
			$include_tax = Shop_CheckoutData::display_prices_incl_tax();
			if (!$include_tax)
				return $price;

			return Shop_TaxClass::get_total_tax($this->tax_class_id, $price) + $price;
		}
		
		/**
		 * Returns TRUE if there are active catalog-level price rules affecting the product price
		 */
		public function is_discounted()
		{
			return $this->price_no_tax() <> $this->get_discounted_price_no_tax(1);
		}
    
   	/*
		 * Returns product price. Use it method instead of accessing the price field directly
		 */
		public function price_no_tax($quantity = 1, $customer_group_id = null)
		{
			if ($customer_group_id === null)
				$customer_group_id = Cms_Controller::get_customer_group_id();

			return $this->eval_tier_price($customer_group_id, $quantity);
		}
    
    		/**
		 * Returns the product price, taking into account the tier price settings
		 * @param int $quantity Product quantity
		 */
		public function eval_tier_price($quantity)
		{
			$price_tiers = $this->list_tier_prices();
			$price_tiers = array_reverse($price_tiers, true);

			foreach ($price_tiers as $tier_quantity=>$price)
			{
				if ($tier_quantity <= $quantity)
					return $price;
			}
			
			return $this->price;
		}
    
    public function list_tier_prices()
		{
			if (!strlen($this->tier_price_compiled))
				return array();
				
			try
			{
				$result = unserialize($this->tier_price_compiled);
				return $result;
			} catch (Exception $ex)
			{
				throw new Phpr_ApplicationException('Error loading tier prices for the "'.$this->name.'" product');
			}
		}
    
    /***********************************************************************************************
     *  
     *    Product Images Area
     *    
     ************************************************************************************************/
     
    public function image_url($index, $width, $height, $returnJpeg = true, $params = array('mode' => 'keep_ratio'))
		{
			if ($index < 0 || $index > $this->images->count-1)
				return null;

			return $this->images[$index]->getThumbnailPath($width, $height, $returnJpeg, $params);
		}                 
		
   /**
    *
    * Categories
    * 
    */
    
        		
		/**
		 * Returns a list of categories the product belongs to.
		 * This method is more effective in terms of memory usage 
		 * than the Shop_Product::$categories and Shop_Product::$category_list fields.
		 * Use it when you need to load category lists for multiple products a time.
		 * @return Db_DataCollection
		 */
		public function list_categories()
		{
			if ($this->category_cache !== null)
				return $this->category_cache;

			$master_product_id = $this->grouped ? $this->product_id : $this->id;
			$category_ids = Db_DbHelper::scalarArray('select shop_category_id from shop_products_categories where shop_product_id=:id', array('id'=>$master_product_id));

			$this->category_cache = array();
			foreach ($category_ids as $category_id)
			{
				$category = Shop_Category::find_category($category_id, false);
				if ($category)
					$this->category_cache[] = $category;
			}
			
			$this->category_cache = new Db_DataCollection($this->category_cache);
			return $this->category_cache;
		}               


    
    }
    

    
    
 ?>   