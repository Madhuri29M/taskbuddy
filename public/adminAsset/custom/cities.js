
//validation
$(document).ready(function () {

    $('#cityForm').validate({ // initialize the plugin
        rules: {
            name: {
                required: true,
            },
            state_id: {
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


// Add Variant Page AJAX By KodyTL starts
$( document ).ready(function() {
  $('#country_id').change(function () {
        var country_id = $(this).val();
        $.ajax({
            type: "POST",
            headers: {
                'X-CSRF-TOKEN': $('input[name="_token"]').val()
            },
            url: ajaxUrl,
            data: {country_id: country_id},
            cache: false,
            beforeSend: function () {
                $('#states').html('<option value="">Loading...</option>');
            },
            complete: function () {
                //$('#getBranches').html('');
            },
            success: function (data) {

                if (data != "" && data != "404") {
                    $('#states').html('')
                    $("#states").html(data);
                }
            }
        });
  });
            // alert(date);
});