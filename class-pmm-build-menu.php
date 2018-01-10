<?php

class PMM_Build_Menu {
    
    public $menu_id = 0;
    
    public $menu_object_id = 0;
    
    public $menu_items = '';
        
    public function __construct($menu_id=0) {
        $this->menu_id = $menu_id;
        
        $menu_object = wp_get_nav_menu_object($menu_id);
        
        if (!isset($menu_object->term_id))
            return;
            
        $this->menu_object_id = $menu_object->term_id;
        $this->menu_items = wp_get_nav_menu_items($this->menu_object_id);
    }
    
    public function display() {
        return $this->build_menu();
    }

    protected function build_menu() {
        $html='';
        $layout = $this->get_columns_and_rows();
        
        $html.=$this->build_primary_nav();
        
        $html.='<div id="pmm-menu-'.$this->menu_id.'" class="pmm-menu columns-'.count($layout).'">';
        
            foreach ($layout as $column => $blocks) :
                $html.=$this->add_column($column, $blocks);
            endforeach;
        
        $html.='</div>';

        return $html;
    }

    public function build_primary_nav() {
        $html='';        
        $primary_nav_items = array();
        
        if (empty($this->menu_items))
            return $primary_nav_items;
        
        // pul out primary nav items.
        foreach ($this->menu_items as $menu_item) :
            if ('primary' === $menu_item->pmm_nav_type)
                $primary_nav_items[] = $menu_item;
        endforeach;
        
        // sort by order.
        usort($primary_nav_items, function($a, $b) {
           return $a->pmm_order - $b->pmm_order; 
        });
            
        // get our items.
        foreach ($primary_nav_items as $primary_nav_item) :
            if (isset($primary_nav_item->pmm_item_type) && '' !== $primary_nav_item->pmm_item_type)
                $html.='<div class="pmm-item pmm-primary-nav-item"><a href="'.get_permalink($primary_nav_item->ID).'">'.$primary_nav_item->title.'</a></div>';            
        endforeach;
        
        return $html;
    }

/*
    public function get_subnav($sub_nav_id = 0) {       
        $html='';
        $sub_menu_items = $this->get_sub_nav_items($sub_nav_id);
        $layout = $this->get_columns_and_rows($sub_menu_items);

        foreach ($layout as $column => $blocks) :
            $html.=$this->add_column($column, $blocks, $sub_menu_items);
        endforeach;

        return $html;

    }
*/
    
/*
    protected function get_sub_nav_items($sub_nav_id = 0) {
        $sub_menu_items = array();
           
        foreach ($this->menu_items as $menu_item) :       
            if ($menu_item->pmm_nav_type == 'subnav' && $menu_item->pmm_menu_primary_nav === $sub_nav_id) :
                $sub_menu_items[] = $menu_item;
            endif;
        endforeach;
        
        return $sub_menu_items;
    }
*/
    
    protected function get_columns_and_rows() {
        $columns_and_rows = array();
        
        // get column (as key) and array of blocks (as value).
        foreach ($this->menu_items as $item) :
            $columns_and_rows[$item->pmm_column][] = $item->pmm_block;
        endforeach;
        
        // make blocks unique.
        foreach ($columns_and_rows as $column => $blocks) :
            $columns_and_rows[$column] = array_values( array_unique( $blocks ) );
        endforeach;
        
        return $columns_and_rows;            
    }
    
    protected function add_column($id, $blocks) {
        $html='';
        
        $html.='<div id="pmm-column-'.$id.'" class="pmm-column">';            
            foreach ($blocks as $block) :
                $html.=$this->add_block($id, $block);
            endforeach;
        $html.='</div>';
        
        return $html;        
    }


    protected function add_block($column_id, $block_id) {
        $html='';
        
        $html.='<div id="pmm-block-'.$column_id.'-'.$block_id.'" class="pmm-block">';
            $html.=$this->add_items($column_id, $block_id);
        $html.='</div>';
        
        return $html;
    }
    
    protected function add_items($column_id, $block_id) {
        $html='';
        $items=array();
        
        // get items in column and block.
        foreach ($this->menu_items as $menu_item) :
            if ($menu_item->pmm_column == $column_id && $menu_item->pmm_block == $block_id) :
                $items[] = $menu_item;
            endif;
        endforeach;
        
        if (empty($items))
            return;
            
        // double check order.
        usort($items, function($a, $b) {
           return $a->pmm_order - $b->pmm_order; 
        });

        foreach ($items as $item) :
            if (isset($item->pmm_item_type) && '' !== $item->pmm_item_type)
                $html.='<div class="pmm-item"><a href="'.get_permalink($item->ID).'">'.$item->title.'</a></div>';
        endforeach;
        
        return $html;
    }    

}