<?php

use modal\Courses;
use modal\Students;

require __DIR__ . "/Controller.php";
require __DIR__ . '/../modal/Students.php';
require __DIR__ . '/../modal/Courses.php';
class StudentsController extends Controller{

    private $modal;
    private $layout;
    public function __construct()
    {
        parent::__construct();
        $this->modal = new Students();
        $this->layout = __DIR__ . "/../view/students/";
    }

    public function index()
    {
        $sql_count = "SELECT COUNT(id) as count FROM {$this->modal->alias}";

        $offset = (($_GET['page'] ?? 1) - 1) * LIMIT;

        $search = $_GET["search"] ?? "";

        $sql = "SELECT {$this->modal->alias}.*, JSON_ARRAY(
            JSON_OBJECT(
                    'id', courses.id,
                    'name', courses.name
                )
        ) AS courses FROM {$this->modal->alias} 
                JOIN courses on {$this->modal->alias}.course_id = courses.id
                ";

        if ($search !== ""){
            $sql .= " WHERE {$this->modal->alias}.name LIKE '%{$search}%' ";
            $sql_count .= " WHERE {$this->modal->alias}.name LIKE '%{$search}%' ";
        }

        $sql .= " LIMIT {$offset}, " . LIMIT;
        $row = (int)ceil($this->modal->selectOne($sql_count)["count"] / LIMIT);
        $students = $this->modal->select($sql, ucfirst($this->modal->alias), ["courses"]);

        require $this->layout . "index.php";
    }

    public function create()
    {
        $course = new Courses();
        $courses = $course->getCourse();
        if ($_SERVER["REQUEST_METHOD"] === "POST"){
            $student = new Students($_POST);
            $sql = "INSERT INTO {$this->modal->alias} (name, course_id, birthday) VALUES ('{$student->name}', '{$student->course_id}', '{$student->birthday->format("Y-m-d H:i:s")}')";
            if ($this->modal->exec($sql)) {
                header("Location: " . BASE_URL . "/students");
                exit;
            }
        }else{
            require $this->layout . "create.php";
        }
    }
}