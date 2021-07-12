
//validation
$(document).ready(function () {

    $('#planForm').validate({ // initialize the plugin
        rules: {
            name: {
                required: true,
            },
            description: {
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