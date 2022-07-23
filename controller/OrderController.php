<?php

class OrderController extends Controller
{
    private const SUCCESS_ORDER_REGISTER_CODE = 'success-order-register-code';
    private const SUCCESS_ORDER_PHONE_NUMBER = 'success-order-phone-number';

    // Web functions
    public function create()
    {
        $model = new Service();
        $services = $model->all();

        $TITLE = 'Đặt dịch vụ';
        $VIEW = './view/order/create.phtml';
        require './layout/app.phtml';
    }

    public function find()
    {
        $model = new Order();
        $order = $model->first();

        $TITLE = "Đơn hàng $order->id";
        $VIEW = './view/order/find.phtml';
        require './layout/app.phtml';
    }

    public function success()
    {
        if (!sessionHas(self::SUCCESS_ORDER_PHONE_NUMBER) || !sessionHas(self::SUCCESS_ORDER_REGISTER_CODE)) {
            header('Location: index.php');
            return;
        }
        $phoneNumber = (string)sessionGet(self::SUCCESS_ORDER_PHONE_NUMBER);
        if (strlen($phoneNumber) >= 10 && substr($phoneNumber, 0, 1) == '0') {
            $phoneNumber = substr($phoneNumber, 1);
        }
        $registerCode = (string)sessionGet(self::SUCCESS_ORDER_REGISTER_CODE);

        $model = new Order();
        $order = $model->where('phoneNumber', '=', $phoneNumber)
            ->where('registerCode', '=', $registerCode)
            ->first();

        sessionRemove(self::SUCCESS_ORDER_PHONE_NUMBER);
        sessionRemove(self::SUCCESS_ORDER_REGISTER_CODE);
        $VIEW = './view/order/success.phtml';
        require './layout/app.phtml';
    }

    // API methods
    public function createOrder()
    {
        try {
            $newOrder = Order::fromPost($_POST);
            $model = new Order();
            $registerCode = $this->getRandomCode();
            $existedOrder = $model->where('registerCode', '=', $registerCode)
                ->where('phoneNumber', '=', $newOrder->phoneNumber)
                ->first();
            while ($existedOrder != null) {
                $registerCode = $this->getRandomCode();
                $existedOrder = $model->where('registerCode', '=', $registerCode)
                    ->where('phoneNumber', '=', $newOrder->phoneNumber)
                    ->first();
            }
            $newOrder->registerCode = $registerCode;
            $newOrder->create();
            sessionSet(self::SUCCESS_ORDER_REGISTER_CODE, $newOrder->registerCode);
            sessionSet(self::SUCCESS_ORDER_PHONE_NUMBER, $newOrder->phoneNumber);

            $this->response(self::HTTP_OK, $newOrder, 'Tạo đơn hàng thành công.');
        } catch (Exception $e) {
            $this->response(self::HTTP_BAD_REQUEST, [], 'Dữ liệu cung cấp không hợp lệ.');
        }
    }

    public function findOrder()
    {
        $phoneNumber = $_POST['phoneNumber'] ?? false;
        $registerCode = $_POST['registerCode'] ?? false;
        if (!$phoneNumber || !$registerCode) {
            $this->response(self::HTTP_BAD_REQUEST, [], 'Dữ liệu không hợp lệ.');
            return;
        }
        $model = new Order();
        $order = $model->where('phoneNumber', '=', $phoneNumber)
            ->where('registerCode', '=', $registerCode)
            ->first();
        if ($order == null) {
            $this->response(self::HTTP_NOT_FOUND, [], 'Không tìm thấy đơn hàng');
            return;
        }
        require './view/order/order_info.phtml.php';
    }

    public function cancelOrder()
    {
        $this->updateOrderWithStatus(EOrderStatus::CANCELLED);
    }

    public function updateOrderStatus()
    {
        $status = $_POST['status'] ?? false;
        if (!$status) {
            $this->response(self::HTTP_BAD_REQUEST, [], 'Trạng thái không hợp lệ.');
            return;
        }
        $this->updateOrderWithStatus($status);
    }

    private function updateOrderWithStatus(string $status)
    {
        $id = (int)$_POST['id'] ?? false;
        $registerCode = $_POST['registerCode'] ?? false;
        if (!$id || !$registerCode) {
            $this->response(self::HTTP_NOT_FOUND, [], 'Không tìm thấy đơn hàng hợp lệ');
            return;
        }
        $model = new Order();
        $order = $model->where('id', '=', $id)
            ->where('registerCode', '=', $registerCode)
            ->first();
        if ($order == null) {
            $this->response(self::HTTP_NOT_FOUND, [], 'Không tìm thấy đơn hàng hợp lệ');
            return;
        }
        $order->update([
            'status' => $status
        ]);
        $this->response(self::HTTP_OK, [], 'Cập nhật trạng thái đơn hàng thành công.');
    }

    // Private functions
    private function getRandomCode(): string
    {
        return sprintf('%05d', rand(0, 999999999));
    }
}
