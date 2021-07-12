 $(document).on('click','.openModal', function(){
      var id = $(this).data('id');
      $('#user_id').val(id);
      $('#new_password').val('');
      $('#confirm_password').val('');
      $("label.error").remove();
      $('#changePassword').modal('show');
    })

    $('#update_password').click(function(){

      var validator = $('#updateForm').validate({ 
        rules: {
            user_id: {
                required: true
            },
            password: {
                required: true,
                minlength:8,
                maxlength:15
            },
            password_confirm: {
                required: true,
                equalTo:'#new_password'
            }
        },
        messages:{
          password:{
            required:'Please enter a new password'
          },
          password_confirm:{
            required:'Please confirm new password',
            equalTo:"The confirmed password doesn't match new password"
          }
        }
      });
      
      validator.form();
     
      if (validator.valid()) 
        {
      
        }
      else 
        {
            return false;
        }
  
      var password   = $('#new_password').val();
      var c_password = $('#confirm_password').val();
      var user_id    = $('#user_id').val();
     
        $.ajax({
            type:'post',
            url: "{{ route('user_password_change') }}",
            data: {
                    "id"         : user_id, 
                    "password"   : password,
                    "c_password" : c_password,  
                    "_token": "{{ csrf_token() }}"
                  },
            beforeSend: function () {
                $('#update_password').html('Updating..<span id="loader" style="visibility: hidden;"><i class="fa fa-spinner fa-spin fa-1x fa-fw"></i><span class="sr-only">a</span></span>');
                $('#loader').css('visibility', 'visible');
            },
            success: function (data) {
              $('#update_password').html('Update Password');
              $('#loader').css('visibility', 'hidden');
              if(data.type == 'error'){
                toastr.error(data.message);
              }else{
                $('#changePassword').modal('hide');
                toastr.success(data.message);
              }
            },
            error: function () {
              $('#update_password').html('Update Password');
              $('#loader').css('visibility', 'hidden');
              toastr.error('Something went wrong');
            }
        })
    })