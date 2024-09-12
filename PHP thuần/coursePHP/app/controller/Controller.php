<?php

namespace controller;

use modal\Connect;

require __DIR__ . "/../modal/Connect.php";

class Controller
{
    protected $db;

    public function __construct()
    {
        $this->db = new Connect();
    }
}