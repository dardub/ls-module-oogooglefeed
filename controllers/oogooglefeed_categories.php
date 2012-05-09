 <?
  class OOGoogleFeed_Categories extends Backend_Controller
  {
     public $implement = 'Db_ListBehavior, Db_FormBehavior';
     public $list_model_class = 'OOGoogleFeed_Taxonomy';
     public $list_record_url = null;
     public $form_model_class = 'OOGoogleFeed_Taxonomy';
     public $form_redirect = null;
     public $list_data_context = 'list';
     public $list_custom_body_cells = null;
	 	 public $list_custom_head_cells = null;
     public $list_render_as_tree = true;
     public $list_no_pagination = true;    
     public $list_no_sorting = false;
     public $list_node_expanded_default = false;
     
     public $form_create_title = 'New Category';
     public $form_edit_title = 'Edit Category';
     public $form_not_found_message = 'Category not found';
     
        
     
    public function __construct()
    {
       parent::__construct();
       $this->app_module_name = 'Related Categories';
       $this->app_tab = 'Related Categories';
       $this->list_record_url = url('/gdrelatedcategories/categories/edit/');
       $this->form_redirect = url('/gdrelatedcategories/categories/');
       
			if (Phpr::$router->action == 'index')
      {
  			$this->list_custom_body_cells = PATH_APP.'/phproad/modules/db/behaviors/db_listbehavior/partials/_list_body_cb.htm';
				$this->list_custom_head_cells = PATH_APP.'/phproad/modules/db/behaviors/db_listbehavior/partials/_list_head_cb.htm';
      }
      
    }
    public function index()
    {
      
       $this->app_page_title = 'Categories for related products';
       $this->list_cell_partial = PATH_APP.'/modules/shop/controllers/shop_categories/_category_row_controls.htm';

      
      
    }
    
    public function index_onSomeEvent()
    {

        Phpr::$response->ajaxReportException('Got here', true, true);
    }

    
    protected function aindex_onDisableEnableSelected()
    {
      Phpr::$response->ajaxReportException('Got here', true, true);
    
    
    
    }
    
    protected function index_onEnableSelected()
    {

        try
  			{
  				$categories_ids = post('list_ids', array());
          

  				
  				if (!count($categories_ids))
  					throw new Phpr_ApplicationException('Please select categories(s) to enable or disable.');
            
                  $categories_processed = 0;
            			foreach ($categories_ids as $category_id)
            			{
                                                                 
            				try
            				{       
                      $category = new OOGoogleFeed_Taxonomy();
                      $category = $category->find($category_id);
                      
                     
                      
                      $category->visible = 1;                      
                      $category->save(); 
                      
                      $parents = $this->get_parents_list($category);
                      
                      foreach ($parents as $parent)
                      {
                        
                        $parent->visible = 1; 
                        $parent->save();
                      }                      
            
            					$categories_processed++;
                        
            				}
            				catch (Exception $ex)
            				{
            					throw new Phpr_ApplicationException($ex);
            				               
            					break;
            				}
            			}
                  
            if ($categories_processed)
      			{

      				if ($categories_processed > 1)
      					Phpr::$session->flash['success'] = $categories_processed.' products have been successfully updated.';
      				else
      					Phpr::$session->flash['success'] = '1 product has been successfully updated.';
      			}
            
            $this->render_partial('index_content');

  			   
  			}
  			catch (Exception $ex)
  			{
  				throw new Phpr_ApplicationException($ex);
  			}
    
    
    }
    
    protected function index_onDisableSelected()
    {

        try
  			{
  				$categories_ids = post('list_ids', array());
          

  				
  				if (!count($categories_ids))
  					throw new Phpr_ApplicationException('Please select categories(s) to enable or disable.');
            
                  $categories_processed = 0;
            			foreach ($categories_ids as $category_id)
            			{
            				try
            				{  

                      $category = new OOGoogleFeed_Taxonomy();
                      $category = $category->find($category_id);
                      
                      $category->visible = $category->visible == 0;                   
                      $category->save(); 
                      
                      $parents = $this->get_parents_list($category);
                      
                      foreach ($parents as $parent)
                      {

                        $parent->visible = $parent->visible == 0; 
                        $parent->save();

                      }
                                            
            					$categories_processed++;
                        
            				}
            				catch (Exception $ex)
            				{
            					throw new Phpr_ApplicationException($ex);
            				               
            					break;
            				}
            			}
                  
            if ($categories_processed)
      			{

      				if ($categories_processed > 1)
      					Phpr::$session->flash['success'] = $categories_processed.' products have been successfully updated.';
      				else
      					Phpr::$session->flash['success'] = '1 product has been successfully updated.';
      			}
            
            $this->render_partial('index_content');

  			   
  			}
  			catch (Exception $ex)
  			{
  				throw new Phpr_ApplicationException($ex);
  			}
    
    
    }
    
    /**
     *
     *  Returns true if a category has visible siblings
     *  Returns false for root level categories     
     */              
    
    protected function has_active_siblings($category)
    {
    
    
       $sibling = Db_DbHelper::queryArray('select * from gd_google_taxonomy where visible=1 and parent_id=:parent_id and id<>:id', array('parent_id'=>$category->parent_id, 'id'=>$category->id));
       
       return count($sibling) > 0;
      
    
    }
    
    protected function get_parents_list($category)
    {
      
      $parent_id = $category->parent_id > 0 ? $category->parent_id : false;
    
      /*
       * Creates array of parent categories
       * breaks out of loop if a category has siblings
       */
      $parents = array();
                    
      while($parent_id)
      {
        $parent = $this->get_parent($parent_id);
         
        if($this->has_active_siblings($category))
          break;
        
        $parent_id = $parent->parent_id > 0 ? $parent->parent_id : false;
        $parents[] = $parent;
      
      }
      
      return $parents;
    
    
    }
    
    protected function get_parent($id)
    {

          $parent = new OOGoogleFeed_Taxonomy();
          $parent = $parent->find($id);  
        

        return $parent;
    
    }
    
    
    
    
  }
    

?>
