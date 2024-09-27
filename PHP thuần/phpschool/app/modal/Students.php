<?php

namespace modal;
require_once __DIR__."/Table.php";
class Students extends Table
{
    public $alias = "students";
    public $id = null;
    public $name = null;
    public $course_id = null;
    public $birthday = null;
    public $created_at = null;
    public $updated_at = null;

    // contain

    public $courses = null;

    public function __construct($student = [])
    {
        if (!empty($student)){
            $this->id = $student['id'] ?? "";
            $this->course_id = $student['course_id'] ?? "";
            $this->name = $student['name'] ?? "";
            $this->birthday = !empty($student["birthday"]) ? new \DateTime($student["birthday"]) : null;
            $this->created_at = !empty($student["created_at"]) ? new \DateTime($student["created_at"]) : null;
            $this->updated_at = !empty($student["updated_at"]) ? new \DateTime($student["updated_at"]) : null;
        }
    }

    public function Age(){
        $now = new \DateTime();
        return !empty($this->birthday) ? $now->diff($this->birthday)->y : "";
    }
    public function __get($property)
    {
        if (method_exists($this, $property)) {
            return $this->$property();
        }
    }
}