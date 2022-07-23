<?php

class Controller
{
    const HTTP_OK = 200;
    const HTTP_BAD_REQUEST = 400;
    const HTTP_NOT_FOUND = 404;

    public function response($status, $data, $message = '')
    {
        http_response_code($status);
        header('Content-Type: application/json');
        $responseBody = [
            'data' => $data,
            'status' => $status,
            'message' => $message
        ];
        echo json_encode($responseBody, JSON_PRETTY_PRINT);
    }

    public function responseWithView($status, $message)
    {
        http_response_code($status);
        echo $message;
    }

    public function responseNotFound()
    {
        http_response_code(404);
        echo 'Not found';
        die();
    }
}