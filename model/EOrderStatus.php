<?php

abstract class EOrderStatus
{
    const CREATED = 'DAKHOITAO';
    const CONFIRMED = 'DAXACNHAN';
    const IN_PROGRESS = 'DANGTIENHANH';
    const FINISHED = 'HOANTAT';
    const CANCELLED = 'HUY';
}