<?php
namespace modal;
class Courses {

    public $alias = "courses";
    public $id;
    public $name;
    public $created_at;
    public $updated_at;

    public function __construct($object = null){
        if (!empty($object)){
            $this->id = $object["id"] ?? "";
            $this->name = $object["name"] ?? "";
            $this->created_at = !empty($object["created_at"]) ? new \DateTime($object["created_at"]) : null;
            $this->updated_at = !empty($object["updated_at"]) ? new \DateTime($object["updated_at"]) : null;
        }
    }

    public function getDisplayCreated(){
        return !empty($this->created_at) ? $this->created_at->format('Y-m-d') : "";
    }

    public function getUpdated(){
        return !empty($this->updated_at) ? $this->updated_at->format('Y-m-d') : "";

    }

    public function setName($name){
        return $this->name = $name;
    }
}