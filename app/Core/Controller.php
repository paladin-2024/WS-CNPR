<?php
namespace App\Core;

class Controller
{
    protected function render($view, $data = [], $layout = 'main')
    {
        extract($data);
        $content = $this->getViewContent($view, $data);
        require ROOT_DIR . '/app/Views/layouts/' . $layout . '.php';
    }

    private function getViewContent($view, $data)
    {
        extract($data);
        ob_start();
        require ROOT_DIR . '/app/Views/' . $view . '.php';
        return ob_get_clean();
    }

    protected function redirect($url)
    {
        header('Location: ' . BASE_PATH . $url);
        exit;
    }

    protected function json($data, $code = 200)
    {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    protected function isAjax()
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }
}
