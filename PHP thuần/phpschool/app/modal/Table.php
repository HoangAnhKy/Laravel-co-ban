<?php

namespace modal;
require __DIR__ . '/Connect.php';
class Table extends \Connect {

    public function setValue($key = "", $value =""){
        if ( property_exists($this, $key) && !empty($key) && !empty($value)){
            return $this->{$key} = $value;
        }
        return null;
    }
    public function getDisplayCreated(){
        return !empty($this->created_at) ? $this->created_at->format('Y-m-d') : "";
    }

    public function getUpdated(){
        return !empty($this->updated_at) ? $this->updated_at->format('Y-m-d') : "";

    }
}