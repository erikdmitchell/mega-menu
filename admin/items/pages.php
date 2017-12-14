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
    	
        $html.='<ul class="pages-list">';
            foreach (get_pages() as $page) :
                $html.='<li id="page-'.$page->ID.'"><label for="page_'.$page->ID.'"><input type="checkbox" name="page[]" id="page_'.$page->ID.'" value="'.$page->ID.'">'.$page->post_title.'</label></li>';
            endforeach; 
        $html.='</ul>';
        
        echo $html;  	
	}

}
