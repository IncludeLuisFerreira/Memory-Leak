<?php

namespace App\Http\Controllers;

abstract class Controller
{
    protected function render(string $view, array $data = []): void
    {
        extract($data);
        $viewPath = __DIR__ . "/../../../views/$view.php";
        if (file_exists($viewPath)) {
            include $viewPath;
        } else {
            echo "View $view not found";
        }
    }

    protected function json(array $data, int $status = 200): void
    {
        header('Content-Type: application/json');
        http_response_code($status);
        echo json_encode($data);
    }

    protected function redirect(string $url): void
    {
        header("Location: $url");
        exit;
    }
}
