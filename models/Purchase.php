<?php
require_once __DIR__ . '/BaseModel.php';

class Purchase extends BaseModel {
    protected $table = 'purchases';

    public function __construct() {
        parent::__construct();
    }
}
?>