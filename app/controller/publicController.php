<?php


class publicController extends Controller
{
    // List of all tasks page
    public function all_tasks() {
        $this->model('Task');
        $tasks = $this->model->getTasks();
        $token = hash_hmac('sha256', 'this is all_tasks page', $_SESSION['token']);
        for ($task = 0; $task < count($tasks); $task++) {
            $tasks[$task]['token'] = $token;
        }
        $this->view('main\all_tasks', $tasks);
        $this->view->page_title = 'Darāmo lietu saraksts';
        $this->view->render();
        if (isset($_POST['token']) && hash_equals($token, $_POST['token'])) {
            // If any checkbox pressed
            if (isset($_POST['checkbox'])) {
                $id = $_POST['checkbox'];
                $task_info = $this->model->getTaskInfo($id);
                // Set task status to opposite value e.g. !0 == 1
                $this->model->changeTaskStatus($id, !$task_info['status']);
                $this->refreshPage();
            }
        }
    }

    // Add new task page
    public function add_task() {
        $this->model('Task');
        $token = hash_hmac('sha256', 'this is add_task page', $_SESSION['token']);
        $this->view('main\add_task', ['token' => $token]);
        $this->view->page_title = 'Darāmo lietu saraksts - pievienot jaunu';
        $this->view->render();
        if (isset($_POST['token']) && hash_equals($token, $_POST['token'])) {
            // If add button pressed
            if (isset($_POST['add_btn'])) {
                $title = strip_tags($_POST['title']);
                $description = strip_tags($_POST['description']);
                $this->model->addTask($title, $description);
                $this->redirect('/public/all_tasks');
            }
        }
    }

    // Edit task page
    public function edit_task($id) {
        $this->model('Task');
        $task_info = $this->model->getTaskInfo($id);
        if (!empty($task_info)) {
            $token = hash_hmac('sha256', 'this is edit_task page', $_SESSION['token']);
            $task_info['token'] = $token;
            $this->view('main\edit_task', $task_info);
            $this->view->page_title = 'Darāmo lietu saraksts - labot';
            $this->view->render();
            if (isset($_POST['token']) && hash_equals($token, $_POST['token'])) {
                // If save button pressed
                if (isset($_POST['save_btn'])) {
                    $title = strip_tags($_POST['title']);
                    $description = strip_tags($_POST['description']);
                    $this->model->saveTask($id, $title, $description);
                    $this->redirect('/public/all_tasks');
                }
                // If delete button pressed
                if (isset($_POST['delete_btn'])) {
                    $this->model->deleteTask($id);
                    $this->redirect('/public/all_tasks');
                }
            }
        }
    }

    private function redirect($url) {
        header("Location: $url");
    }

    private function refreshPage() {
        echo "<meta http-equiv='refresh' content='0'>";
    }
}