<?php

use modal\Courses;

require __DIR__ . "/Controller.php";
require __DIR__ . '/../modal/Courses.php';

class CourseController extends Controller
{

    private $modal;
    private $layout;

    public function __construct()
    {
        parent::__construct();
        $this->modal = new Courses();
        $this->layout = __DIR__ . "/../view/course/";
    }

    public function index()
    {
        $sql_count = "SELECT COUNT(id) as count FROM {$this->modal->alias}";

        $offset = (($_GET['page'] ?? 1) - 1) * LIMIT;

        $search = $_GET["search"] ?? "";

        $sql = "SELECT * FROM {$this->modal->alias}";

        if ($search !== ""){
            $sql .= " WHERE name LIKE '%{$search}%' ";
            $sql_count .= " WHERE name LIKE '%{$search}%' ";
        }

        $sql .= " LIMIT {$offset}, " . LIMIT;
        $row = (int)ceil($this->modal->selectOne($sql_count)["count"] / LIMIT);
        $courses = $this->modal->select($sql, ucfirst($this->modal->alias));

        require $this->layout . "index.php";
    }

    public function create()
    {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $course_save = new Courses($_POST);
            $sql = "INSERT INTO {$this->modal->alias} (name) VALUES ('{$course_save->name}')";
            if ($this->modal->exec($sql)) {
                header("Location: " . BASE_URL . "/course");
                exit;
            }
        } else {
            require $this->layout . "create.php";
        }
    }

    public function edit($id = null)
    {
        if (isset($id) && is_numeric($id)) {
            if ($_SERVER["REQUEST_METHOD"] === "POST") {
                $course_save = new Courses($_POST);
                $sql = "UPDATE {$this->modal->alias} SET name='{$course_save->name}' WHERE id={$id}";

                if ($this->modal->exec($sql)) {
                    header("Location: " . BASE_URL . "/course");
                    exit;
                }
            } else {
                $sql = "SELECT * FROM {$this->modal->alias} WHERE id = {$id}";
                $course = $this->modal->selectOne($sql, ucfirst($this->modal->alias));
                require $this->layout . "edit.php";
            }
        }
    }

    public function delete($id = null)
    {
        if (isset($id) && is_numeric($id)) {
            $sql = "DELETE FROM {$this->modal->alias} WHERE id={$id}";

            if ($this->modal->exec($sql)) {
                header("Location: " . BASE_URL . "/course");
                exit;
            }
        }
    }
}