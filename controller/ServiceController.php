<?php

require_once 'Controller.php';

class ServiceController extends Controller
{
    // Web functions
    public function show()
    {
        $id = (int)$_GET['id'] ?? false;
        if (!$id) {
            $this->responseWithView(self::HTTP_BAD_REQUEST, 'Yêu cầu không hợp lệ');
            return;
        }
        $model = new Service();
        $service = $model->where('id', '=', $id)->first();
        if (!service) {
            $this->responseWithView(self::HTTP_NOT_FOUND, "Không tìm thấy dịch vụ với mã $id");
            return;
        }
        $VIEW = '';
        require './layout/app.phtml';
    }

    // API methods
    public function getAll()
    {
        $model = new Service();
        $services = $model->all();

        $this->response(self::HTTP_OK, $services);
    }

}