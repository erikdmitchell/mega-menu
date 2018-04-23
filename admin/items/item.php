<?php
    
class PMM_Item {
    
    public $label;
	
	public $slug;
	
	public $options;

	public function __construct($args='') {
		$default_args=array(
			'slug' => '',
			'label' => '',
			'options' => array(
                'label' => array(
                    'slug' => 'label',
    			    'label' => 'Navigation Label',
    			    'default' => 'post_title',
    			    'input' => 'text',
			    ), 
			    'title' => array(
    			    'slug' => 'title',
			        'label' => 'Title Attribute',
			        'default' => '',
			        'input' => 'text',
			    ), 
			    'classes' => array(
    			    'slug' => 'classes',
    			    'label' => 'CSS Classes',
    			    'default' => '',
    			    'input' => 'text',
			    ),
			),
		);
		$args=pmm_wp_parse_args($args, $default_args);

		$this->slug=$args['slug'];
		$this->label=$args['label'];
		$this->options=$args['options'];
	}

    public function display() {
        return "<p>This should be overridden by your class.</p>";
    }
    
    public function load_item($id=0) {
        return "<p>This should be overridden by your class.</p>";
    }
    
    public function edit_link() {
        if (!empty($this->options))
            return '<a href="" class="edit-item">Edit</a>';
            
        return;
    }
    
    public function display_options($defaults=array()) {
        $html='';
        
        $html.='<div class="options">';
        
            foreach ($this->options as $type => $option) :
                $html.='<div class="option-field '.$type.'">';               
                    $html.=$this->option_field($option, $defaults);
                $html.='</div>';
            endforeach;
        
        $html.='</div>';
        
        return $html;
    }
    
    protected function option_field($args='', $defaults_args='') {
        $field='';

        if (isset($args['default']) && !empty($args['default']) && isset($defaults_args[$args['default']])) :
            $value = $defaults_args[$args['default']];
        else :
            $value='';
        endif;        

        switch ($args['input']):
            default:
                $field.='<label for="'.$args['slug'].'">';
				    $field.=$args['label'].'<br>';
                    $field.='<input type="text" id="'.$args['slug'].'" class="'.$args['slug'].'" name="'.$args['slug'].'" value="'.$value.'">';
				$field.='</label>';
        endswitch;
        
        return $field;
    }
    
}