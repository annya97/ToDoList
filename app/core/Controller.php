<?php


class Controller
{
    protected $view;
    protected $model;

    public function view($name, $data = []) {
        $this->view = new View($name, $data);
        return $this->view;
    }

    public function model($name, $data = []) {
        if (file_exists(MODEL . $name . '.php')) {
            require MODEL . $name . '.php';
            $this->model = new $name;
        }
    }
}