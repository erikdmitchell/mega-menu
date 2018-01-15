<?php
    
class PMM_Admin_Build_Menu {
    
    public $menu_id = 0;
    
    public $menu_object_id = 0;
        
    public function __construct($menu_id=0) {      
        $this->menu_id = $menu_id;
        
        $menu_object = wp_get_nav_menu_object($menu_id);
        
        if (!isset($menu_object->term_id))
            return;
            
        $this->menu_object_id = $menu_object->term_id;
    }

    public function get_subnav($sub_nav_id = 0) {       
        $html='';
        $sub_menu_items = $this->get_sub_nav_items($sub_nav_id);
        $layout = $this->get_columns_and_rows($sub_menu_items);

        foreach ($layout as $column => $rows) :
            $html.=$this->add_column($column, $rows, $sub_menu_items);
        endforeach;

        return $html;

    }
    
    protected function get_sub_nav_items($sub_nav_id = 0) {
        $sub_menu_items = array();
        $menu_items = wp_get_nav_menu_items($this->menu_object_id);
           
        foreach ($menu_items as $menu_item) :       
            if ($menu_item->pmm_nav_type != 'primary' && $menu_item->pmm_menu_primary_nav === $sub_nav_id) :
                $sub_menu_items[] = $menu_item;
            endif;
        endforeach;
        
        return $sub_menu_items;
    }
    
    protected function get_columns_and_rows($menu_items = '') {
        $layout = array();
        
        // get column (as key) and array of rows (as value).
        if (!isset($menu_items) || empty($menu_items))
            return $layout;
        
        foreach ($menu_items as $item) :
            $layout[$item->pmm_column][] = $item->pmm_row;
        endforeach;
        
        // make rows unique.
        foreach ($layout as $column => $rows) :
            $layout[$column] = array_values( array_unique( $rows ) );
        endforeach;
        
        return $layout;            
    }
    
    protected function add_column($id, $rows, $menu_items) {
        $html='';
        
        $html.='<div id="pmm-column-'.$id.'" class="pmm-column">';
            $html.='<div class="row-actions">';
                $html.='<div class="add-row-wrap">';
                    $html.='<a href="#" class="add-row">Add Row</a>';
                $html.='</div>';
            $html.='</div>';
            
            foreach ($rows as $row) :
                $html.=$this->add_row($id, $row, $menu_items);
            endforeach;
        $html.='</div>';
        
        return $html;
    }


    protected function add_row($column_id, $row_id, $menu_items) {
        $html='';
        
        $html.='<div id="pmm-row-'.$column_id.'-'.$row_id.'" class="pmm-row">';
            $html.=$this->add_items($column_id, $row_id, $menu_items);
        $html.='</div>';
        
        return $html;
    }
    
    protected function add_items($column_id, $row_id, $menu_items) {
        $html='';
        $items=array();
        
        // get items in column and row.
        foreach ($menu_items as $menu_item) :
            if ($menu_item->pmm_column == $column_id && $menu_item->pmm_row == $row_id) :
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
                $html.=PickleMegaMenu()->admin->items[$item->pmm_item_type]->load_item($item->ID);
        endforeach;
        
        return $html;
    }
    
    public function build_primary_nav() {
        $html='';        
        $menu_items = wp_get_nav_menu_items($this->menu_object_id);        
        $primary_nav_items = array();
        
        if (empty($menu_items))
            return $primary_nav_items;
        
        // pul out primary nav items.
        foreach ($menu_items as $menu_item) :
            if ('primary' === $menu_item->pmm_nav_type)
                $primary_nav_items[] = $menu_item;
        endforeach;
        
        // sort by order.
        usort($primary_nav_items, function($a, $b) {
           return $a->pmm_order - $b->pmm_order; 
        });
            
        // get our items.
        foreach ($primary_nav_items as $primary_nav_item) :
            if (isset($primary_nav_item->pmm_item_type) && '' !== $primary_nav_item->pmm_item_type) :
                $html.=PickleMegaMenu()->admin->items[$primary_nav_item->object]->load_item($primary_nav_item->ID);
            endif;
        endforeach;
        
        return $html;
    }

}
