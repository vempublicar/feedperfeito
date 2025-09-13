<?php
require_once __DIR__ .'/BaseModel.php';

class MultipleProduct extends BaseModel {
    protected $table = 'multiple_products';
    
    public function __construct() {
        parent::__construct();
    }
    
    // Métodos específicos para MultipleProduct podem ser adicionados aqui, se necessário.
}
?>