
//validation
$(document).ready(function () {

    $('#stateForm').validate({ // initialize the plugin
        rules: {
            name: {
                required: true,
            },
            country_id: {
                required: true,
            }
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