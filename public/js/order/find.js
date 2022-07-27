$(function () {
    const FIND_ORDER_URL = "order.php?action=findOrder";
    const CANCEL_ORDER_URL = "order.php?action=cancelOrder&q=21";

    let $phoneNumberInput = $("input#phoneNumber");
    let $registerCodeInput = $("input#registerCode");

    // Data functions
    const validate = function (success) {
        clearErrors();
        let registerCode = $registerCodeInput.val() || '';
        let phoneNumber = $phoneNumberInput.val() || '';

        let passed = true;

        if (phoneNumber === '') {
            displayError($phoneNumberInput, "Vui lòng nhập số điện thoại đăng ký");
            passed = false;
        }
        if (registerCode === '') {
            displayError($registerCodeInput, "Vui lòng nhập mã đăng ký");
            passed = false;
        }
        if (passed) {
            let data = {
                registerCode: registerCode,
                phoneNumber: phoneNumber,
            }
            success(data);
        }
    }

    // Event functions
    $("body").on("click", "button#lookUp", function (e) {
        validate(
            (data) => {
                $.ajax({
                    url: FIND_ORDER_URL,
                    method: "post",
                    data: data,
                    success: (response) => {
                        $('.order-info-container').html(response);
                    },
                    error: (errors) => {
                        console.log(errors);
                        alert(errors.responseText);
                    }
                });
            }
        );
    })

    $("body").on("click", "button#cancel", function (e) {
        if (!window.confirm("Bạn chắc chắn muốn huỷ đơn hàng?")) {
            return;
        }
        let id = $(".order-data").data("id") || "";
        let registerCode = $(".order-data").data("register_code") || "";
        let data = {
            "id": id,
            "registerCode": registerCode
        };
        $.ajax({
            url: CANCEL_ORDER_URL,
            method: "post",
            data: data,
            success: (response) => {
                alert(response.message);
                location.reload();
            },
            error: (errors) => {
                console.log(errors);
                alert(errors.responseText);
            }
        });
    });

});