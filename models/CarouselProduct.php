<?php
require_once __DIR__ .'/BaseModel.php';

class CarouselProduct extends BaseModel {
    protected $table = 'carousel_products';
    
    public function __construct() {
        parent::__construct();
    }
    
    // Métodos específicos para CarouselProduct podem ser adicionados aqui, se necessário.
    // Ex: getFeaturedProducts(), getProductsByCategory($category), etc.
}
?>