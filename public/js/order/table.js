$(function () {
    const ORDER_QUERY_URL = "order.php?action=queryOrder";

    const getFormData = function () {
        const $form = $('form');
        let data = {};
        $('input:not(:disabled, [type=checkbox])').each(function (index, element) {
            if ($(element).val()) {
                data[element.id] = $(element).val();
            }
        });
        $('select').each(function (index, element) {
            let $selectedOption = $(element).find('option:selected').first();
            if ($selectedOption.val()) {
                data[element.id] = $selectedOption.val();
            }
        });
        return data;
    }

    const executeQuery = function () {
        const data = getFormData();
        console.log(data);
        $.ajax({
            url: ORDER_QUERY_URL,
            method: 'post',
            data: data,
            success: function (response) {
                console.log(response);
                $('div#result').html(response);
            },
            error: function (errors) {
                console.log(errors);
                alert(errors.responseText);
            }
        });
    }

    // Event functions
    $('body').on('click', 'button#query', function (e) {
        executeQuery();
    });

    $('form').bind('reset', function (e) {
        setTimeout(function () {
            executeQuery();
        }, 1);
    });

    $('body').on('click', '.pagination li.page-item:not(.disabled) > a', function (e) {
        const page = $(this).attr('id');
        console.log(page);
        if (!$.isNumeric(page)) {
            return;
        }
        const data = getFormData();
        data['page'] = page;
        console.log(data);
        $.ajax({
            url: ORDER_QUERY_URL,
            method: "post",
            data: data,
            success: function (response) {
                console.log(response);
                $('div#result').html(response);
            },
            error: function (errors) {
                console.log(errors);
                alert(errors.responseText);
            }
        });
    });

    $("body").on("click", "button.viewOrder", function(e) {
        let id = $(this).attr("id") || "";
        window.location = `order.php?action=viewOrder&id=${id}`;
    })
});