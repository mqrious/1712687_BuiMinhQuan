$(function () {
    // Constants
    const LOGIN_API = "admin.php?action=login";
    const $usernameInput = $("input#username");
    const $passwordInput = $("input#password");
    const $form = $("#sign-in-form");

    // Variables

    // Data functions

    const validate = function (success) {
        clearErrors();
        let username = $usernameInput.val() || '';
        let password = $passwordInput.val() || '';
        let passed = true;
        if (username === '') {
            passed = false;
            displayError($usernameInput, "Vui lòng nhập tên tài khoản");
        }
        if (password === '') {
            passed = false;
            displayError($passwordInput, "Vui lòng nhập mật khẩu");
        }
        if (passed) {
            let data = {
                username: username,
                password: password
            };
            success(data);
        }
    }

    // Event functions
    $("body").on("click", "button#login", function (e) {
        validate(
            (data) => {
                console.log(data);
                $.ajax({
                    url: LOGIN_API,
                    method: "post",
                    data: data,
                    success: function (response) {
                        console.log(response);
                        alert(response.message);
                        $form[0].reset();
                        window.location = "admin.php";
                    },
                    error: function (errors) {
                        console.log(errors);
                        alert(errors.responseText);
                    }
                });
            }
        )
    })

});