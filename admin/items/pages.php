<?php

class PMM_Item_Pages extends PMM_Item {

	public function __construct() {
		parent::__construct(array(
			'slug' => 'pages',
			'label' => 'Pages',
		));
	}
	
	public function display() {
    	$html='';
    	
        $html.='<div class="pages-list pmm-item-list">';
            foreach (get_pages() as $page) :
                $html.='<div id="page-'.$page->ID.'" class="page pmm-item" data-type="page">';
                    $html.=$page->post_title;
                    $html.='<a href="" class="edit-item">Edit</a>';
                    $html.='<div class="options">OPTIONS</div>';
                $html.='</div>';
            endforeach; 
        $html.='</div>';
        
        return $html;  	
	}

}
