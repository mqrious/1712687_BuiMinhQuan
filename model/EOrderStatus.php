<?php

abstract class EOrderStatus
{
    const CREATED = 'DAKHOITAO';
    const CONFIRMED = 'DAXACNHAN';
    const IN_PROGRESS = 'DANGTIENHANH';
    const FINISHED = 'HOANTAT';
    const CANCELLED = 'HUY';

    public static function getOrderStatuses(): array
    {
        return [
            'DAKHOITAO' => 'Đã khởi tạo',
            'DAXACNHAN' => 'Đã xác nhận',
            'DANGTIENHANH' => 'Đang tiến hành',
            'HOANTAT' => 'Hoàn tất',
            'HUY' => 'Huỷ',
        ];
    }

    public static function getAvailableStatuses(string $currentStatus): array
    {
        $availableStatuses = EOrderStatus::getOrderStatuses();
        if (!in_array($currentStatus, array_keys($availableStatuses))) {
            return [];
        }
        $currentStatusIndex = array_search($currentStatus, array_keys($availableStatuses));
        $availableStatuses = array_slice($availableStatuses, $currentStatusIndex + 1, 5);
        array_pop($availableStatuses); # Remove Cancelled status;
        return $availableStatuses;
    }

}