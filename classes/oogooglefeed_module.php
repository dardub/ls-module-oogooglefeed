<?
  class OOGoogleFeed_Module extends Core_ModuleBase
  {
    /**
     * Google product feed
     * Author: Darren Dub
     * 
     * @return Core_ModuleInfo
     */
    protected function createModuleInfo()
    {
      return new Core_ModuleInfo(
        "Google Products Feed",
        "Allows Google Products Feed",
        "Open Orchard" );
    }
    

    
    public function listTabs($tabCollection)
    {
      $menu_item = $tabCollection->tab('gdgoogleproducts', 'Google Products', 'categories', 90);

    }
    
    
      
    public function subscribeEvents()
  	{
  	  Backend::$events->addEvent('shop:onExtendCategoryModel', $this, 'extend_category_model');
  	  Backend::$events->addEvent('shop:onExtendCategoryForm', $this, 'extend_category_form');
  	}
	
  	public function extend_category_model($category)
  	{

  	  $category->define_column('gd_google_taxonomy_id', 'Taxonomy Id');
      $category->add_relation('belongs_to', 'taxonomies', array('class_name'=>'GdGoogleProducts_Taxonomy', 'foreign_key'=>'gd_google_taxonomy_id', 'conditions'=>'visible=1'));
      $category->define_multi_relation_column('taxonomies', 'taxonomies', 'Categories', '@name')->defaultInvisible()->validation();

  	}
  	
  	public function extend_category_form($category)
  	{
          $category->add_form_field('taxonomies')->tab('Google Categories');
  	}
    

  }
?>
