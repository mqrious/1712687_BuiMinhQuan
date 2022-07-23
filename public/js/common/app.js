const clearErrors = function () {
    $("span.error").remove();
};

const displayError = function ($input, errorMessage) {
    $input.after(`<span class="error">${errorMessage}</span>`);
};