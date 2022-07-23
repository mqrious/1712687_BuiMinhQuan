<?php

require_once('model/Model.php');

class Service extends Model
{
    private const CURRENCY_SYMBOL = 'VNĐ';

    public string $tableName = 'DICHVU';

    public int $id = 0;
    public string $name = '';
    public string $type = '';
    public float $price = 0.0;

    public function attributes(): array
    {
        return [
            'id' => 'MaDV',
            'name' => 'TenDV',
            'type' => 'LoaiDV',
            'price' => 'DonGia'
        ];
    }

    public function getPrice(): string
    {
        return number_format($this->price, 0, ",", ".") . self::CURRENCY_SYMBOL;
    }
}