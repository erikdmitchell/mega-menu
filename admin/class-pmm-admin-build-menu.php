<?php
    
class PMM_Admin_Build_Menu {
    
    public $menu_id = 0;
    
    public $menu_object_id = 0;
    
    public $menu_items = 0;
    
    public function __construct($menu_id=0) {
        $this->menu_id = $menu_id;
        
        $menu_object = wp_get_nav_menu_object($menu_id);
        
        if (!isset($menu_object->term_id))
            return;
            
        $this->menu_object_id = $menu_object->term_id;
            
        return $this->build_menu();
    }

    protected function build_menu() {
        $html='';
        $this->menu_items = wp_get_nav_menu_items($this->menu_object_id);
        $layout = $this->get_layout();
        
        foreach ($layout as $column => $blocks) :
            $html.=$this->add_column($column, $blocks);
        endforeach;
        
        
        
echo '<pre>';
//print_r($layout);
//print_r($this->menu_items);
echo '</pre>';

echo $html;

/*
  <div id="pmm-column-0" class="pmm-column ui-sortable" style="width: 480px;">
    <div class="block-actions ui-sortable-handle">
        <div class="add-block-wrap"><a href="#" class="add-block">Add Block</a></div>
    </div>
    
    <div id="pmm-block-0-0" class="pmm-block ui-sortable-handle ui-sortable">
-- ITEM --
        <div class="page pmm-item ui-draggable ui-draggable-handle editable" data-type="page" id="pmm-item-0-0-0" uid="_ah0pt7qt4" style="width: 430px; right: auto; height: auto; bottom: auto;">
            About
            <a href="" class="edit-item">Edit</a>
            <div class="options">
                <div class="option-field"><label for="label">Navigation Label<br><input type="text" id="label" class="label" name="pmm_menu_items[_ah0pt7qt4][label]" value="About"></label></div>
                <div class="option-field"><label for="title">Title Attribute<br><input type="text" id="title" class="title" name="pmm_menu_items[_ah0pt7qt4][title]" value=""></label></div>
                <div class="option-field"><label for="classes">CSS Classes<br><input type="text" id="classes" class="classes" name="pmm_menu_items[_ah0pt7qt4][classes]" value=""></label></div>
            </div>
            
            <input type="hidden" name="pmm_menu_items[_ah0pt7qt4][page_id]" value="1086"><input type="hidden" id="column" name="pmm_menu_items[_ah0pt7qt4][column]" value="0">
            <input type="hidden" id="block" name="pmm_menu_items[_ah0pt7qt4][block]" value="0"><input type="hidden" id="order" name="pmm_menu_items[_ah0pt7qt4][order]" value="0">
            <input type="hidden" id="db_id" name="pmm_menu_items[_ah0pt7qt4][db_id]">
-- ITEM --            
        </div>
    </div>
    
    <div id="pmm-block-0-1" class="pmm-block ui-sortable"></div>
</div>  
*/
    }
    
    protected function get_layout() {
        $layout = array();
        
        // get column (as key) and array of blocks (as value).
        foreach ($this->menu_items as $item) :
            $layout[$item->pmm_column][] = $item->pmm_block;
        endforeach;
        
        // make blocks unique.
        foreach ($layout as $column => $blocks) :
            $layout[$column] = array_values( array_unique( $blocks ) );
        endforeach;
        
        return $layout;            
    }
    
    protected function add_column($id, $blocks) {
        $html='';
        
        $html.='<div id="pmm-column-'.$id.'" class="pmm-column">';
            $html.='<div class="block-actions">';
                $html.='<div class="add-block-wrap">';
                    $html.='<a href="#" class="add-block">Add Block</a>';
                $html.='</div>';
            $html.='</div>';
            
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
            $html.=PickleMegaMenu()->admin->items[$item->pmm_item_type]->load_item($item->ID);
        endforeach;
        
        return $html;
    }    
}