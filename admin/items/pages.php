<?php

class PMM_Item_Pages extends PMM_Item {

	public function __construct() {
		parent::__construct(array(
			'slug' => 'pages',
			'label' => 'Pages',
			'options' => array('label', 'title', 'classes'),
		));
	}
	
	public function display() {
    	$html='';
    	
        $html.='<div class="pages-list pmm-item-list">';
            foreach (get_pages() as $page) :
                $html.='<div id="page-'.$page->ID.'" class="page pmm-item" data-type="page">';
                    $html.=$page->post_title;
                    $html.=$this->edit_link();
                    $html.=$this->display_options();
                    $html.='<input type="hidden" name="page_id" value="'.$page->ID.'" />';
                $html.='</div>';
            endforeach; 
        $html.='</div>';
        
        return $html;  	
	}

}
