<?php
require_once __DIR__ .'/BaseModel.php';

class FeedProduct extends BaseModel {
    protected $table = 'feed_products';
    
    public function __construct() {
        parent::__construct();
    }
    
    // Métodos específicos para FeedProduct podem ser adicionados aqui, se necessário.
    // Ex: getFeaturedProducts(), getProductsByCategory($category), etc.
}
?>