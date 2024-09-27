<?php
namespace modal;

require_once  __DIR__."/Table.php";
class Courses extends Table {

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

    public function getCourse (){
        $sql = "SELECT id, name FROM {$this->alias}";
        $result = $this->select($sql, $this->alias);
        return $result;
    }
}