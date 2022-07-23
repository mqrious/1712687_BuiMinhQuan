<?php
/**
 * @var Order $order
 */
?>

<?php if (isset($order)): ?>
    <div class="order-data" data-id="<?php echo $order->id ?>" data-register_code="<?php echo $order->registerCode ?>" hidden></div>
    <div class="d-flex w-100 justify-content-between align-items-end">
        <h3 class="mb-1">Đơn hàng #<?php echo $order->id ?></h3>
        <p class="mb-1">Trạng thái: <?php echo $order->getStatus() ?></p>
    </div>
    <div class="d-flex w-100 justify-content-between">
        <p class="mb-1">Ngày đặt: <?php echo $order->getStartDate() ?></p>
        <p class="mb-1">Ngày kết thúc: <?php echo $order->getFinishDate() ?></p>
    </div>
    <div class="d-flex w-100 justify-content-between">
        <p class="mb-1">Tên khách hàng: <?php echo $order->customerName ?></p>
        <p class="mb-1">Số điện thoại: <?php echo $order->phoneNumber ?></p>
        <p class="mb-1">Địa chỉ: <?php echo $order->address ?></p>
    </div>
    <p class="mb-1">Dịch vụ: <?php echo $order->service()->name ?>
        (đơn giá: <?php echo $order->service()->getPrice() ?>)</p>
    <p class="mb-1">Số lượng: <?php echo $order->unit ?></p>
    <h5 class="mb-1"><b>Thành tiền: <?php echo $order->getTotalPrice() ?></b></h5>
    <?php if ($order->cancellable() == true): ?>
        <button id="cancel" type="button" class="btn btn-danger">Huỷ đơn hàng</button>
    <?php endif; ?>
<?php endif; ?>
