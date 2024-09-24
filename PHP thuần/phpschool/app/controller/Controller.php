<?php

require __DIR__ . '/../modal/Connect.php';

class Controller{
    
    protected $db;
    public function __construct() {
        $this->db = new Connect();
    }

    public function index(){
        require __DIR__ . "/../view/hello.php";
    }
}