$(function () {
    //Initialize Select2 Elements
    $('.select2').select2()
});

  
//validation
$(document).ready(function () {

    $('#brandForm').validate({ // initialize the plugin
        rules: {
            name: {
                required: true,
            }
        },
        messages: {
            name: {
                required: 'Brand name is required',
            }
        },
        submitHandler: function (form) { // for demo
            form.submit();
        }
    });
});