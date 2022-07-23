<?php

require_once('model/Model.php');

class Order extends Model
{
    private const CURRENCY_SYMBOL = 'VNĐ';

    public string $tableName = 'DONHANG';

    public int $id = 0;
    public string $customerName = '';
    public string $phoneNumber = '';
    public string $address = '';
    public string $startAt = '';
    public string $status = '';
    public ?string $finishAt = null;
    public int $serviceId = 0;
    public float $unit = 0.0;
    public float $totalPrice = 0.0;
    public string $note = '';
    public string $registerCode = '';

    public function attributes(): array
    {
        return [
            'id' => 'MaDH',
            'customerName' => 'TenKH',
            'phoneNumber' => 'DienThoai',
            'address' => 'DiaChi',
            'startAt' => 'ThoiGianBD',
            'status' => 'TrangThai',
            'finishAt' => 'ThoiGianKT',
            'serviceId' => 'MaDV',
            'unit' => 'SoLuong',
            'totalPrice' => 'ThanhTien',
            'note' => 'GhiChu',
            'registerCode' => 'MaDangKy',
        ];
    }

    public static function fromPost($post)
    {
        $instance = new self();
        $instance->customerName = $post['customerName'];
        $instance->phoneNumber = (string)$post['phoneNumber'];
        $instance->address = $post['address'];
        $instance->startAt = date('Y-m-d H:i:s');
        $instance->status = EOrderStatus::CREATED;
        $instance->finishAt = null;
        $instance->note = $post['note'];
        $instance->serviceId = $post['serviceId'];
        $instance->unit = (float)$post['unit'];
        $instance->totalPrice = (float)$post['totalPrice'];
        return $instance;
    }

    public function getTotalPrice(): string
    {
        return number_format($this->totalPrice, 0, ",", ".") . self::CURRENCY_SYMBOL;
    }

    public function getStatus(): string
    {
        switch($this->status)
        {
            case EOrderStatus::CREATED: return 'Đã khởi tạo';
            case EOrderStatus::CONFIRMED: return 'Đã xác nhận';
            case EOrderStatus::IN_PROGRESS: return 'Đang tiến hành';
            case EOrderStatus::FINISHED: return 'Hoàn tất';
            case EOrderStatus::CANCELLED: return 'Huỷ';
        }
        return '';
    }

    public function getStartDate(): string
    {
        return $this->startAt == '' ? '' : date("H:i:s d-m-Y", strtotime($this->startAt));
    }

    public function getFinishDate(): string
    {
        return $this->finishAt == '' ? '' : date("H:i:s d-m-Y", strtotime($this->finishAt));
    }

    public function getNote(): string
    {
        return $this->note == '' ? 'Không' : $this->note;
    }

    public function getPhoneNumber(): string
    {
        return strlen($this->phoneNumber) == 10 ? $this->phoneNumber : '0'.$this->phoneNumber;
    }

    public function cancellable(): bool
    {
        return ($this->status === EOrderStatus::CREATED);
    }

    // Relationships
    public function service()
    {
        return $this->hasOne(Service::class, 'serviceId', 'id');
    }

}