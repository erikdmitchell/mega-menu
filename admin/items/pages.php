<?php

class PMM_Item_Pages extends PMM_Item {

	public function __construct() {
		parent::__construct(array(
			'slug' => 'page',
			'label' => 'Pages',
			'options' => array('label' => array(), 'title' => array(), 'classes' => array()),
		));
	}
	
	public function display() {
    	$html='';
    	
        $html.='<div class="pages-list pmm-item-list">';
            foreach (get_pages() as $page) :
                $html.=$this->single_item($page->ID);
            endforeach; 
        $html.='</div>';
        
        return $html;  	
	}
	
	protected function single_item($id=0) {	
        $html='';
        $object_id = get_post_meta($id, '_menu_item_object_id', true);
        
        if (empty($object_id))
            $object_id = $id;
        
        $html.='<div id="page-'.$id.'" class="page pmm-item" data-type="page">';
            $html.='<span class="pmm-item-title">'.get_the_title($object_id).'</span>';
            $html.=$this->edit_link();
            $html.=$this->display_options(array(
                'post_title' => get_the_title($object_id),
            ));
            $html.='<input type="hidden" name="id" value="'.$id.'" />';
            $html.='<input type="hidden" name="item_type" value="post_type" />';
            $html.='<input type="hidden" name="object_id" value="'.$object_id.'" />'; 
            $html.='<input type="hidden" name="object" value="page" />';                       
        $html.='</div>';    	
        
        return $html;
	}
	
    public function load_item($id=0) {       
        return $this->single_item($id);
    }	

}
