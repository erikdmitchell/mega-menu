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
    	
        $html.='<ul class="pages-list item-list">';
            foreach (get_pages() as $page) :
                $html.='<li id="page-'.$page->ID.'" class="page item" data-type="page">'.$page->post_title.'</li>';
            endforeach; 
        $html.='</ul>';
        
        return $html;  	
	}

}
