<?php

class PMM_Item_Pages extends PMM_Item {

	public function __construct() {
		parent::__construct(array(
			'slug' => 'pages',
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
        
        $html.='<div id="page-'.$id.'" class="page pmm-item" data-type="page">';
            $html.=get_the_title($id);
            $html.=$this->edit_link();
            $html.=$this->display_options(array(
                'post_title' => get_the_title($id),
            ));
            $html.='<input type="hidden" name="page_id" value="'.$id.'" />';
        $html.='</div>';    	
        
        return $html;
	}
	
    public function load_item($id=0) {
        return $this->single_item($id);
    }	

}
