<?php
require_once __DIR__ .'/BaseModel.php';

class CarouselProduct extends BaseModel {
    protected $table = 'carousel_products';
    
    public function __construct() {
        parent::__construct();
    }
    
    // Métodos específicos para CarouselProduct podem ser adicionados aqui, se necessário.
    // Ex: getFeaturedProducts(), getProductsByCategory($category), etc.

    // Get latest records
    public function latest($limit = 4) {
        try {
            // Assume 'created_at' for ordering, fallback to 'id' if not present
            $orderColumn = property_exists($this, 'orderByColumn') ? $this->orderByColumn : 'created_at';
            
            // Check if the table has 'created_at' column, otherwise use primary key or another suitable column
            // This is a simplified check, a more robust solution would query schema
            $columns = $this->query($this->table . '?select=' . $orderColumn . '&limit=1');
            if (empty($columns)) {
                $orderColumn = $this->primaryKey;
            }

            $query = $this->table . '?order=' . $orderColumn . '.desc&limit=' . $limit;
            return $this->query($query);
        } catch (Exception $e) {
            error_log("Error in latest() for table " . $this->table . ": " . $e->getMessage());
            return [];
        }
    }
}
?>