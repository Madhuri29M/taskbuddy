
//validation
$(document).ready(function () {

    $('#functional_areaForm').validate({ // initialize the plugin
        rules: {
            name: {
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