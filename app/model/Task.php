<?php


class Task
{
    protected static $dsn = "mysql:host=localhost;dbname=todolist";
    protected static $user = 'root';
    protected static $password = '';
    private $db;

    public function __construct() {
        try {
            $pdo = new PDO(self::$dsn, self::$user, self::$password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->db = $pdo;
        } catch (PDOException $e) {
            echo $e->getMessage();
            die();
        }
    }

    public function getTasks() {
        try {
            $sql = 'SELECT * FROM tasks ORDER BY time';
            $query = $this->db->prepare($sql);
            $query->execute();
            $tasks = $query->fetchAll();
            $this->prepareTime($tasks);
            $this->prepareStatus($tasks);
            return $tasks;
        } catch (PDOException $e) {
            echo $e->getMessage();
            die();
        }
    }

    // Prepare time data for each task
    private function prepareTime(&$tasks) {
        $now_time = date("Y-m-d H:i:s");
        for ($task = 0; $task < count($tasks); $task++) {
            $task_time = new DateTime($tasks[$task]['time']);
            // Calculate how much time passed after task's creation
            $diff = $task_time->diff(new DateTime($now_time));
            // Store calculated values in an array
            $diff_array[] = $diff->y; // Years
            $diff_array[] = $diff->m; // Months
            $diff_array[] = $diff->d; // Days
            $diff_array[] = $diff->h; // Hours
            $diff_array[] = $diff->i; // Minutes
            $diff_array[] = $diff->s; // Seconds
            // Find first value of time that is not 0 - it will be the biggest unit
            for ($time_value = 0; $time_value < count($diff_array); $time_value++) {
                if ($diff_array[$time_value] != 0) {
                    $tasks[$task]['time'] = 'Pirms ' . $diff_array[$time_value];
                    // Add unit name
                    switch ($time_value) {
                        case 0:
                            $tasks[$task]['time'] .= ' gadiem/-a';
                            break;
                        case 1:
                            $tasks[$task]['time'] .= ' mēnešiem/-a';
                            break;
                        case 2:
                            $tasks[$task]['time'] .= ' dienām/-as';
                            break;
                        case 3:
                            $tasks[$task]['time'] .= ' stundām/-as';
                            break;
                        case 4:
                            $tasks[$task]['time'] .= ' minūtēm/-es';
                            break;
                        case 5:
                            $tasks[$task]['time'] .= ' sekundēm/-es';
                            break;
                    }
                    // Right value found, so no need to search anymore
                    break;
                } else {
                    // Passed less than 1 second
                    $tasks[$task]['time'] = 'Tikko';
                }
            }
            // Unset array for new task
            $diff_array = [];
        }
    }

    // Prepare status for each task
    private function prepareStatus(&$tasks) {
        for ($task = 0; $task < count($tasks); $task++) {
            if ($tasks[$task]['status'] == 0) {
                $tasks[$task]['status'] = '';
            } else {
                $tasks[$task]['status'] = 'checked';
            }
        }
    }

    public function changeTaskStatus($id, $status) {
        try {
            $sql = 'UPDATE tasks SET status = :status WHERE id = :id';
            $query = $this->db->prepare($sql);
            $query->bindparam(":id", $id);
            $query->bindparam(":status", $status);
            $query->execute();
        } catch (PDOException $e) {
            echo $e->getMessage();
            die();
        }
    }

    public function addTask($title, $description) {
        $time = date("Y-m-d H:i:s");
        try {
            $sql = 'INSERT INTO tasks (title, description, time) VALUES (:title, :description, :time)';
            $query = $this->db->prepare($sql);
            $query->bindparam(":title", $title);
            $query->bindparam(":description", $description);
            $query->bindparam(":time", $time);
            $query->execute();
        } catch (PDOException $e) {
            echo $e->getMessage();
            die();
        }
    }

    public function getTaskInfo($id) {
        try {
            $sql = 'SELECT title, description, status FROM tasks WHERE id = :id';
            $query = $this->db->prepare($sql);
            $query->bindparam(":id", $id);
            $query->execute();
            return $query->fetch();
        } catch (PDOException $e) {
            echo $e->getMessage();
            die();
        }
    }

    public function saveTask($id, $title, $description) {
        try {
            $sql = 'UPDATE tasks SET title = :title, description = :description WHERE id = :id';
            $query = $this->db->prepare($sql);
            $query->bindparam(":id", $id);
            $query->bindparam(":title", $title);
            $query->bindparam(":description", $description);
            $query->execute();
            return $query;
        } catch (PDOException $e) {
            echo $e->getMessage();
            die();
        }
    }

    public function deleteTask($id) {
        try {
            $sql = 'DELETE FROM tasks WHERE id = :id';
            $query = $this->db->prepare($sql);
            $query->bindparam(":id", $id);
            $query->execute();
            return $query;
        } catch (PDOException $e) {
            echo $e->getMessage();
            die();
        }
    }
}