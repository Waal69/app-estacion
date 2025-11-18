<?php
class View {
    private $template;
    private $data = [];
    
    public function __construct($template) {
        $this->template = $template;
    }
    
    public function assign($key, $value) {
        $this->data[$key] = $value;
    }
    
    public function render() {
        extract($this->data);
        
        ob_start();
        include "views/{$this->template}.php";
        $content = ob_get_clean();
        
        include "views/layout.php";
    }
}