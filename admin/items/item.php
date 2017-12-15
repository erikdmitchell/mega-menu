<?php
    
class PMM_Item {
    
    public $label;
	
	public $slug;
	
	public $options;

	public function __construct($args='') {
		$default_args=array(
			'slug' => '',
			'label' => '',
			'options' => array(),
		);
		$args=pmm_wp_parse_args($args, $default_args);

		$this->slug=$args['slug'];
		$this->label=$args['label'];
		$this->options=$args['options'];
	}

    public function display() {
        return "<p>This should be overridden by your class.</p>";
    }
    
    public function edit_link() {
        if (!empty($this->options))
            return '<a href="" class="edit-item">Edit</a>';
            
        return;
    }
    
    public function display_options() {
        $html='';
        
        $html.='<div class="options">';
        
            foreach ($this->options as $option) :
                $html.='<div class="option-field">';
                    $html.=$this->option_field($option);
                $html.='</div>';
            endforeach;
        
        $html.='</div>';
        
        return $html;
    }
    
    protected function option_field($type='') {
        $field='';
        
        switch ($type):
            default:
                $field.='<label for="edit-menu-item-title-1252">';
				    $field.='Navigation Label<br>';
                    $field.='<input type="text" id="edit-menu-item-title-1252" class="widefat edit-menu-item-title" name="menu-item-title[1252]" value="Home">';
				$field.='</label>';
        endswitch;
        
        return $field;
    }
    
}