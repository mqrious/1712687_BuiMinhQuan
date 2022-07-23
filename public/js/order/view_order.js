$(function (){
    const UPDATE_ORDER_STATUS_URL = "order.php?action=updateOrderStatus";

    $("body").on("click", "button#updateOrderStatus", function(e) {
        let $statusSelectedOption = $("select#status option:selected");
        let $orderData = $(".order-data");
        let id = $orderData.data("id") || "";
        let registerCode = $orderData.data("register_code") || "";
        let status = $statusSelectedOption.val() || "";
        if (status === "" || id === "" || registerCode === "") {
            return;
        }
        let statusText = $statusSelectedOption.text().trim();
        if (!window.confirm(`Xác nhận thay đổi trạng thái đơn hàng thành: '${statusText}'?`)) {
            return;
        }
        let data = {
            id: id,
            registerCode: registerCode,
            status: status,
        };
        $.ajax({
            url: UPDATE_ORDER_STATUS_URL,
            method: "post",
            data: data,
            success: (response) => {
                console.log(response);
                alert(response.message);
                location.reload();
            },
            error: (errors) => {
                console.log(errors);
                alert(errors.responseJSON.message);
            }
        })
    })
});