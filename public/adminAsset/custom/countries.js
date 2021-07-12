
//validation
$(document).ready(function () {

    $('#countryForm').validate({ // initialize the plugin
        rules: {
            name: {
                required: true,
            },
            country_code: {
                required: true,
            },
        },
        // messages: {
        //     name: {
        //         required: "Please choose a Brand",
        //     },
        // },
        submitHandler: function (form) { // for demo
            form.submit();
        }
    });
});