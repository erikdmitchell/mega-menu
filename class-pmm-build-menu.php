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
        $html='';        
        
        if (empty($this->menu_items))
            return;
            
        $primary_nav_items = $this->get_primary_nav_items();
        
        $html.='<ul class="pmm-mega-menu">';
        
        foreach ($primary_nav_items as $primary_nav_item) :
            $classes = array("pmm-mega-menu-item", "pmm-mega-menu-primary-nav-item", "pmm-mega-menu-item-{$primary_nav_item->post_name}", "pmm-mega-menu-item-{$primary_nav_item->ID}");
            
            if ($this->has_subnav($primary_nav_item->pmm_order))
                $classes[] = 'pmm-mega-menu-item-has-children';       

            $html.='<li id="pmm-mega-menu-item-'.$primary_nav_item->ID.'" class="'.implode(' ', $classes).'">';
                $html.='<a class="pmm-mega-menu-link" href="'.get_permalink($primary_nav_item->ID).'">'.$primary_nav_item->title.'</a>';
                
                    if ($this->has_subnav($primary_nav_item->pmm_order))
                        $html.=$this->get_subnav($primary_nav_item->pmm_order);
                
            $html.='</li>';            
        endforeach;
        
        $html.='</ul>';
        
        return $html;
        
    }
    
    protected function get_primary_nav_items() {
        $primary_nav_items = array();
        
        // pul out primary nav items.
        foreach ($this->menu_items as $menu_item) :
            if ('primary' === $menu_item->pmm_nav_type)
                $primary_nav_items[] = $menu_item;
        endforeach;
        
        // sort by order.
        usort($primary_nav_items, function($a, $b) {
           return $a->pmm_order - $b->pmm_order; 
        });
        
        return $primary_nav_items;       
    }
    
    protected function has_subnav($sub_nav_id = 0) {
        foreach ($this->menu_items as $menu_item) :       
            if ($menu_item->pmm_nav_type == 'subnav' && $menu_item->pmm_menu_primary_nav === $sub_nav_id) :
                return true;
            endif;
        endforeach;
        
        return false;       
    }

    public function get_subnav($sub_nav_id = 0) {       
        $html='';
        $sub_menu_items = $this->get_sub_nav_items($sub_nav_id);
        $layout = $this->get_columns_and_rows($sub_menu_items);
        
        $html.='<ul class="pmm-mega-menu-sub pmm-mega-menu-columns-'.count($layout).'">';
        
            foreach ($layout as $column => $blocks) :
                $html.=$this->add_column($column, $blocks, $sub_menu_items);
            endforeach;
        
        $html.='</ul>';

        return $html;

    }
    
    protected function get_sub_nav_items($sub_nav_id = 0) {
        $sub_menu_items = array();
           
        foreach ($this->menu_items as $menu_item) :       
            if ($menu_item->pmm_nav_type == 'subnav' && $menu_item->pmm_menu_primary_nav === $sub_nav_id) :
                $sub_menu_items[] = $menu_item;
            endif;
        endforeach;
        
        return $sub_menu_items;
    }
    
    protected function get_columns_and_rows($menu_items = '') {
        $columns_and_rows = array();
        
        // get column (as key) and array of blocks (as value).
        foreach ($menu_items as $item) :
            $columns_and_rows[$item->pmm_column][] = $item->pmm_block;
        endforeach;
        
        // make blocks unique.
        foreach ($columns_and_rows as $column => $blocks) :
            $columns_and_rows[$column] = array_values( array_unique( $blocks ) );
        endforeach;
        
        return $columns_and_rows;            
    }
    
    protected function add_column($id, $blocks, $menu_items) {
        $html='';
        
        $html.='<li id="pmm-mega-menu-column-'.$id.'" class="pmm-mega-menu-column">';            
            foreach ($blocks as $block) :
                $html.=$this->add_block($id, $block, $menu_items);
            endforeach;
        $html.='</li>';
        
        return $html;        
    }


    protected function add_block($column_id, $block_id, $menu_items) {
        $html='';
        
        $html.='<ul id="pmm-mega-menu-row-'.$column_id.'-'.$block_id.'" class="pmm-mega-menu-row">';
            $html.=$this->add_items($column_id, $block_id, $menu_items);
        $html.='</ul>';
        
        return $html;
    }
    
    protected function add_items($column_id, $block_id, $menu_items) {
        $html='';
        $items=array();
        
        // get items in column and block.
        foreach ($menu_items as $menu_item) :
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
            $classes = array("pmm-mega-menu-item", "pmm-mega-menu-item-{$item->post_name}", "pmm-mega-menu-item-{$item->ID}");
    
            if (isset($item->pmm_item_type) && '' !== $item->pmm_item_type)
                $html.='<li id="pmm-mega-menu-item-'.$item->ID.'" class="'.implode(' ', $classes).'"><a class="pmm-mega-menu-link" href="'.get_permalink($item->ID).'">'.$item->title.'</a></li>';
        endforeach;
        
        return $html;
    }    

}