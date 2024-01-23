/*
 * UnderConstructionPage PRO
 * (c) Web factory Ltd, 2015 - 2017
 */

 var ucp_frontend = (function($) {
    $(document).ready(function(){

        var hash = location.hash;
        var ucp_pass_form_visible = false;
        
        $(document).on('hashchange',function(){ 
            hash = location.hash;
            $('#ucp-template').addClass('ucp-blur');
            $('#ucp-access-form').fadeIn(500);
            ucp_pass_form_visible = true;
        });

        if(ucp_frontend_variables.ucp_locked || hash == '#access-site-form'){
            $('#ucp-template').addClass('ucp-blur');
            $('#ucp-access-form').fadeIn(500);
            ucp_pass_form_visible = true;
        }

        $('#ucp-access-show-form').on('click',function(e){
            e.stopPropagation();
            $('#ucp-template').addClass('ucp-blur');
            $('#ucp-access-form').fadeIn(500);
            ucp_pass_form_visible = true;
        });

        $('#ucp-access-password').on('keypress',function(e) {
            if(e.which == 13) {
                check_ucp_pass();
            }
        });

        $('#ucp-access-check-password').on('click',function(){
            check_ucp_pass();
        });

        $(document).click(function(event) { 
            $target = $(event.target);  
            if(!ucp_frontend_variables.ucp_locked && !$target.closest('#ucp-access-form').length && ucp_pass_form_visible) {
              ucp_pass_form_visible = false;
              $('#ucp-access-form').fadeOut(500);
              $('#ucp-template').removeClass('ucp-blur');
            }        
        });

        function check_ucp_pass(){
            if($('#ucp-access-password').val().length<4){
                $('#ucp-access-response').hide();
                $('#ucp-access-response').html('<span>Wrong password</span>');
                $('#ucp-access-response').fadeIn(500).delay(2000).fadeOut(500);
            } else {
                $.ajax({
                    url: ucp_frontend_variables.ucp_ajax_url,
                    method: 'POST',
                    crossDomain: true,
                    dataType: 'json',
                    data: {
                    action:'ucp_check_login',
                    pass:$('#ucp-access-password').val()
                    }
                }).done(function(response) {
                    if(response.success){
                        location.reload();
                    } else {
                        $('#ucp-access-response').hide();
                        $('#ucp-access-response').html('<span>'+response.data+'</span>');
                        $('#ucp-access-response').fadeIn(500).delay(2000).fadeOut(500);
                    }
                }).fail(function(type) {
                    $('#ucp-access-response').hide();
                    $('#ucp-access-response').html('<span>An error occured! Please try again later</span>');
                    $('#ucp-access-response').fadeIn(500).delay(2000).fadeOut(500);
                });
            }
        }
    });

    function ucp_setup_forms(){
      $('form.ucp-module').on('submit',function(e){
       var form_id = this.id;
       var ucp_processor = $(this).data('processor');
       var fields = {};
       var form_type = $(this).data('module-type');
       var form_sendto = $(this).data('processor');

       if(form_type == 'contact'){
        var form_admin_email = $(this).data('admin-email');
        var form_email_subject = $(this).data('email-subject');
        var form_email_body = $(this).data('email-body');
       }

       if($(this).find('.input_terms').length>0){
          if($(this).find('.input_terms:checked').length>0){
            //checked
          } else {
            alert('You need to check the box "'+$(this).parent().find('label').html()+'"');
            return false;
          }
       }

       if(ucp_processor == 'mailchimp' || ucp_processor == 'zapier' || ucp_processor == 'local' || ucp_processor == 'autoresponder' ){
          e.preventDefault();

          $(this).find('input[type="text"],input[type="email"],input[type="tel"],textarea').each(function(){
            fields[$(this).attr('name')] = $(this).val();
          });

          $.ajax({
            url: ucp_frontend_variables.ucp_ajax_url,
            method: 'POST',
            crossDomain: true,
            dataType: 'json',
            data: {
              action:'ucp_submit_form',
              form_type:form_type,
              form_sendto:form_sendto,
              form_admin_email:form_admin_email,
              form_email_subject:form_email_subject,
              form_email_body:form_email_body,
              fields:fields
            }
          }).done(function(response) {
            if(response.data == 'success' || response.data == 'captcha'){
              alert($('#'+form_id).attr('data-msg-'+response.data));
            } else {
              alert($('#'+form_id).attr('data-msg-error') + ':' + response.data);
            }
          }).fail(function(type) {

          });
       }
      });
    }

    return { // public interface
      initialize_ucp_forms: function () {
        ucp_setup_forms();
      }
    };

})( jQuery );

jQuery(document).ready(function(e) {
  ucp_frontend.initialize_ucp_forms();
});
