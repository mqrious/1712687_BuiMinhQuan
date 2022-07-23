<?php

class Account extends Model
{
    public string $tableName = 'TAIKHOAN';

    public string $username = '';
    public string $password = '';
    public int $type = 0;
    public int $status = 0;

    public function attributes(): array
    {
        return [
            'username' => 'TenTK',
            'password' => 'MatKhau',
            'type' => 'LoaiTK',
            'status' => 'TinhTrang'
        ];
    }
}