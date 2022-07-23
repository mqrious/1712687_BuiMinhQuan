<?php

class AdminController extends Controller
{
    public function index()
    {
        $VIEW = './view/admin/index.phtml';
        require './layout/app.phtml';
    }

    public function login()
    {
        $VIEW = './view/admin/login.phtml';
        require './layout/app.phtml';
    }

    // API methods
    public function loginAdmin()
    {
        $username = $_POST['username'] ?? false;
        $password = $_POST['password'] ?? false;
        if (!$username || !$password) {
            $this->responseNotFound();
            return;
        }
        $model = new Account();
        $account = $model->where('username', '=', $username)
            ->where('password', '=', $password)
            ->first();
        if ($account == null) {
            $this->response(self::HTTP_NOT_FOUND, [], "Sai tài khoản hoặc mật khẩu, vui lòng thử lại.");
            return;
        }
        sessionLogin($account);
        $this->response(self::HTTP_OK, [], 'Đăng nhập thành công');
    }

    public function logout()
    {
        sessionLogout();
        header('Location: index.php');
    }
}