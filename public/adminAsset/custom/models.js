$(function () {
    //Initialize Select2 Elements
    $('.select2').select2()
});

//validation
$(document).ready(function () {

    $('#modelForm').validate({ // initialize the plugin
        rules: {
            brand_id: {
                required: true,
            },
            name: {
                required: true,
            }
        },
        messages: {
            brand_id: {
                required: "Please choose a Brand",
            },
            name: {
                required: 'Model name is required',
            }
        },
        submitHandler: function (form) { // for demo
            form.submit();
        }
    });
});