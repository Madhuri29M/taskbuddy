
//validation
$(document).ready(function () {

    $('#languageForm').validate({ // initialize the plugin
        rules: {
            name: {
                required: true,
            },
            label: {
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