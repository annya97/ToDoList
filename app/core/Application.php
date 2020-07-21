<?php


class Application
{
    protected $controller = 'publicController';
    protected $action = 'all_tasks';
    protected $params = [];

    public function __construct() {
        $this->prepareUrl();
        if (file_exists(CONTROLLER . $this->controller . '.php')) {
            // Make new application controller
            $this->controller = new $this->controller;
            if (method_exists($this->controller, $this->action)) {
                // Do things according to user's request
                call_user_func_array([$this->controller, $this->action], $this->params);
            }
        }
    }

    // Get name of controller, action (method) and parameters
    protected function prepareUrl() {
        // Get requested URL e.g. public/all_tasks
        $request = trim($_SERVER['REQUEST_URI'], '/');
        if (!empty($request)) {
            // Save in array all parts from requested URL
            $url = explode('/', $request);
            // Set controller name that is in $url[0] e.g. publicController
            $this->controller = isset($url[0]) ? $url[0] . 'Controller' : 'publicController';
            // Set action name that is in $url[1] e.g. all_tasks
            $this->action = isset($url[1]) ? $url[1] : 'all_tasks';
            // Take out controller name and action name from array
            unset($url[0], $url[1]);
            // The rest of $url are parameters if any
            $this->params = !empty($url) ? array_values($url) : [];
        }
    }
}