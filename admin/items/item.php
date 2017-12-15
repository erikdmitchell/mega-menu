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
        return '<div class="options">OPTIONS</div>';
    }
    
}