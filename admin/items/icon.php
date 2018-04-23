<?php

class PMM_Item_Icon extends PMM_Item {

	public function __construct() {
		parent::__construct(array(
			'slug' => 'icon',
			'label' => 'Icon',
			'options' => array(
			    'label' => array(
    			    'label' => 'Link Text',
    			    'default' => '',
			    ),  
			    'title' => array(), 
			    'classes' => array(),
                'icon' => array(
    			    'slug' => 'icon-class',
    			    'label' => 'Icon Class',
    			    'default' => '',
    			    'input' => 'text',
			    ),
                'url' => array(
    			    'slug' => 'url',
    			    'label' => 'URL',
    			    'default' => '',
    			    'input' => 'url',
			    ),
			 ),
		));
	}
	
	public function display() {
    	$html='';
    	
        $html.='<div class="icon-item pmm-item-list">';
            $html.=$this->single_item();
        $html.='</div>';
        
        return $html;  	
	}
	
	protected function single_item($id=0) {	
        $html='';
        $object_id = get_post_meta($id, '_menu_item_object_id', true);
        
        if (empty($object_id))
            $object_id = $id;
        
        $html.='<div id="page-'.$id.'" class="icon pmm-item" data-type="icon">';
            $html.='<span class="pmm-item-title">Custom Icon</span>';
            $html.=$this->edit_link();
            $html.=$this->display_options();
            $html.='<input type="hidden" name="id" value="'.$id.'" />';
            $html.='<input type="hidden" name="item_type" value="custom" />';
            $html.='<input type="hidden" name="object_id" value="'.$object_id.'" />'; 
            $html.='<input type="hidden" name="object" value="icon" />';                       
        $html.='</div>';    	
        
        return $html;
	}
	
    public function load_item($id=0) {       
        return $this->single_item($id);
    }	

}
