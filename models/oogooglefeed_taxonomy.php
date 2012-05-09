<?php

	class OOGoogleFeed_Taxonomy extends Db_ActiveRecord
	{
		public $table_name = 'gd_google_taxonomy';
		
		public $implement = 'Db_AutoFootprints, Db_Act_As_Tree, Db_ModelAttachments';
		public $act_as_tree_parent_key = 'parent_id';
		public $act_as_tree_sql_filter = null;
		public $auto_footprints_visible = true;

		protected static $cache = array();
		protected $api_added_columns = array();
		protected static $product_count_cache = null;
     
		public $belongs_to = array(
			'parent'=>array('class_name'=>'OOGoogleFeed_Taxonomy', 'foreign_key'=>'parent_id')
		);
		


		public static function create($init_columns = false)
		{
			if ($init_columns)
				return new self();
			else 
				return new self(null, array('no_column_init'=>true, 'no_validation'=>true));
		}
    
       public function define_columns($context = null)
      {
          //$this->define_column('id', '#');
          $this->define_column('name', 'Category Name');
          $this->define_column('parent_id', 'Parent ID');
          $this->define_column('created_at', 'created at');
          $this->define_column('updated_at', 'updated at');
          $this->define_column('visible', 'Visible');
          $this->define_relation_column('parent', 'parent', 'Parent category', db_varchar, '@name')->invisible();
          
      }    
            
		

	}
?>