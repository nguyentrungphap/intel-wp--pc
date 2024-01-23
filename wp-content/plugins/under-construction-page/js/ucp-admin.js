/*
 * UnderConstructionPage PRO
 * Main backend JS
 * (c) Web factory Ltd, 2015 - 2017
 */

// global object for all UCP functions
var UCP = {};

UCP.init = function() {
};

UCP.init3rdParty = function($) {
  // init tabs




  $('#ucp_tabs').tabs({
    activate: function(event, ui) {
        Cookies.set('ucp_tabs', $('#ucp_tabs').tabs('option', 'active'), { expires: 365 });
    },
    active: Cookies.get('ucp_tabs')
  }).show();

  // init 2nd level of tabs
  $('.ucp-tabs-2nd-level').each(function() {
    $(this).tabs({
      activate: function(event, ui) {
        Cookies.set($(this).attr('id'), $(this).tabs('option', 'active'), { expires: 365 });
      },
      active: Cookies.get($(this).attr('id'))
    });
  });

  // init select2
  $('#whitelisted_users').select2({ 'placeholder': ucp_vars.whitelisted_users_placeholder});

  // init clipboard plugin
  clipboard = new Clipboard('.clipboard-copy');
  clipboard.on('success', function(e) {
    alert('Copied!');
    e.clearSelection();
  }); // clipboard

  // init datepickers
  $('.datepicker').each(function() {
    options = { format: ($(this).data('format'))? $(this).data('format'): "%Y-%m-%d %H:%i",
                firstDOW: 1,
                earliest: ($(this).data('earliest') == 'now')? new Date(): '',
                labelTitle: $(this).data('title')? $(this).data('title'): 'Select a Date &amp; Time' };

    $(this).AnyTime_picker(options);
  });

  $('#leads').DataTable({
    "order": [[ 1, "asc" ]],
    "columns": [
    { "orderable": false },
    null,
    null,
    null,
    null,
    null,
    null,
    { "orderable": false }
    ],
    dom: 'Bfrtip',
    buttons: [
   'copy', 'csv', 'excel', 'pdf', 'print'
  ]
  });
}; // init3rdParty

UCP.initUI = function($) {
  // universal button to close UI dialog in any dialog
  $('.ucp-close-ui-dialog').on('click', function(e) {
    e.preventDefault();

    parent = $(this).closest('.ui-dialog-content');
    $(parent).dialog('close');

    return false;
  }); // close-ui-dialog

  // autosize textareas
  $.each($('#ucp_tabs textarea[data-autoresize]'), function() {
    var offset = this.offsetHeight - this.clientHeight;

    var resizeTextarea = function(el) {
        $(el).css('height', 'auto').css('height', el.scrollHeight + offset + 2);
    };
    $(this).on('keyup input click', function() { resizeTextarea(this); }).removeAttr('data-autoresize');
  }); // autosize textareas

  // show all user roles
  $('#show-all-roles').on('click', function(e) {
    e.preventDefault();

    $(this).hide();
    $('#more-roles').show();

    return false;
  }); // show all user roles

  // reset/clear datepicker
  $('.clear-datepicker').on('click', function(e) {
    e.preventDefault();

    $(this).prevAll('input.datepicker').val('');

    return false;
  }); // clear datepicker
}; // initUI


UCP.fix_dialog_close = function(event, ui) {
  jQuery('.ui-widget-overlay').bind('click', function(){
    jQuery('#' + event.target.id).dialog('close');
  });
} // fix_dialog_close

UCP.parse_form_html = function(form_html) {
  var $ = jQuery.noConflict();
  data = {'action_url': '', 'email_field': '', 'name_field': '', 'extra_data': '', 'method': '', 'email_fields_extra': ''};

  html = $.parseHTML('<div id="parse-form-tmp" style="display: none;">' + form_html + '</div>');

  data.action_url = $('form', html).attr('action');
  if ($('form', html).attr('method')) {
    data.method = $('form', html).attr('method').toLowerCase();
  }

  email_fields = $('input[type=email]', html);
  if (email_fields.length == 1) {
    data.email_field = $('input[type=email]', html).attr('name');
  }

  inputs = '';
  $('input', html).each(function(ind, el) {
    type = $(el).attr('type');
    if (type == 'email' || type == 'button' || type == 'reset' || type == 'submit') {
      return;
    }

    name = $(el).attr('name');
    name_tmp = name.toLowerCase();

    if (!data.email_field && (name_tmp == 'email' || name_tmp == 'from' || name_tmp == 'emailaddress')) {
      data.email_field = name;
    } else if (name_tmp == 'name' || name_tmp == 'fname' || name_tmp == 'firstname') {
      data.name_field = name;
    } else {
      data.email_fields_extra += name + ", ";
      data.extra_data += name + '=' + $(el).attr('value') + '&';
    }
  }); // foreach

  data.email_fields_extra = data.email_fields_extra.replace(/\, $/g, '');
  data.extra_data = data.extra_data.replace(/&$/g, '');

  return data;
} // parse_form_html


jQuery(document).ready(function($) {
  old_settings = $('#ucp_form *').not('.skip-save').serialize();

  UCP.initUI($);
  UCP.init3rdParty($);

  // select theme via thumb
  // todo
  /*
  $('.ucp-thumb').on('click', function(e) {
    e.preventDefault();

    theme_id = $(this).data('theme-id');
    $('.ucp-thumb').removeClass('active');
    $(this).addClass('active');
    $('#theme_id').val(theme_id);

    return false;
  });
  */

  // open dialog to configure universal autoresponder
  $('#ucp_tabs').on('click', '.configure-autoresponder', function(e) {
    e.preventDefault();

    $('#autoresponder-config-dialog').dialog('option', 'title', 'Auto Configure Universal Autoresponder').dialog('open');

    return false;
  }); // auto_configure_autoresponder

  $('#status').on('change',function(){
    console.log('change status');
  });
  // delete single access link
  $('a.delete-access-link').on('click', function(e) {
    e.preventDefault();

    parent_row = $(this).closest('tr');
    original_bg = $(parent_row).css('background-color');
    $(parent_row).css('background-color', 'rgba(255,0,0,0.2)');
    link_id = $(parent_row).data('link-id');
    link_name = $(parent_row).find('.link_name').text();

    test = confirm('Are you sure you want to delete the "' + link_name  + '" access link? There is NO undo!');

    if (test == true) {
      $.post(ajaxurl, {
               action: 'ucp_access_links',
               _ajax_nonce: ucp_vars.nonce_access_links,
               sub_action: 'delete',
               link_id: link_id},
             function(response) {
               if (response.success) {
                 $(parent_row).hide(500);
               } else {
                 alert(response.data);
               }
             });
    } else {
      $(parent_row).css('background-color', original_bg);
    }

    return false;
  }); // delete access link


  // delete single affiliate link
  $('a.delete-affiliate-link').on('click', function(e) {
    e.preventDefault();

    parent_row = $(this).closest('tr');
    original_bg = $(parent_row).css('background-color');
    $(parent_row).css('background-color', 'rgba(255,0,0,0.2)');
    link_id = $(parent_row).data('link-id');
    link_name = $(parent_row).find('.link_name').text();

    test = confirm('Are you sure you want to delete the "' + link_name  + '" affiliate link? There is NO undo!');

    if (test == true) {
      $.post(ajaxurl, {
               action: 'ucp_affiliate_links',
               _ajax_nonce: ucp_vars.nonce_affiliate_links,
               sub_action: 'delete',
               link_id: link_id},
             function(response) {
               if (response.success) {
                 $(parent_row).hide(500);
               } else {
                 alert(response.data);
               }
             });
    } else {
      $(parent_row).css('background-color', original_bg);
    }

    return false;
  }); // delete affiliate link


  // open dialog to add access link
  $('a.add-access-link').on('click', function(e) {
    e.preventDefault();

    $('#access-link-dialog').dialog('option', 'title', 'Add New Direct Access Link').dialog('open');
    $('#link_name').val('');
    $('#link_expire_type').val('').trigger('change');
    $('#link_expire_value').val('10');
    $('#save-access-link').hide();
    $('#save-new-access-link').show();

    return false;
  }); // add_access_link


  // open dialog to add affiliate link
  $('a.add-affiliate-link').on('click', function(e) {
    e.preventDefault();

    $('#affiliate-link-dialog').dialog('option', 'title', 'Add New Affiliate Link').dialog('open');
    $('#link_name').val('');
    $('#save-affiliate-link').hide();
    $('#save-new-affiliate-link').show();

    return false;
  }); // add_affiliate_link


  // open dialog to edit access link
  $('a.edit-access-link').on('click', function(e) {
    e.preventDefault();
    parent = $(this).closest('tr');

    $('#access-link-dialog').dialog('option', 'title', 'Edit Direct Access Link').dialog('open');
    $('#link_id').val($(parent).data('link-id'));
    $('#link_name').val($(parent).data('link-name'));
    $('#link_expire_type').val($(parent).data('link-expire-type')).trigger('change');
    if ($(parent).data('link-expire-type') == 'date') {
      $('#link_expire_value2').val($(parent).data('link-expire-value'));
    } else if ($(parent).data('link-expire-type') != '') {
      $('#link_expire_value').val($(parent).data('link-expire-value'));
    }

    $('#save-access-link').show();
    $('#save-new-access-link').hide();

    return false;
  }); // edit_access_link


  // open dialog to edit affiliate link
  $('a.edit-affiliate-link').on('click', function(e) {
    e.preventDefault();
    parent = $(this).closest('tr');

    $('#affiliate-link-dialog').dialog('option', 'title', 'Edit Affiliate Link').dialog('open');
    $('#link_id2').val($(parent).data('link-id'));
    $('#link_name2').val($(parent).data('link-name'));

    $('#save-affiliate-link').show();
    $('#save-new-affiliate-link').hide();

    return false;
  }); // edit_affiliate_link


  // ajax, save new access link
  $('#save-new-access-link').on('click', function(e) {
    e.preventDefault();
    error = false;
    button = this;

    $(button).addClass('loading');
    $('#link_name').removeClass('error');
    $('#link_expire_value').removeClass('error');

    if ($('#link_name').val().trim().length < 3 || $('#link_name').val().trim().length > 128) {
      $('#link_name').addClass('error');
      error = true;
    }

    if ($('#link_expire_type').val() == 'sessions' && ($('#link_expire_value').val() < 1 || $('#link_expire_value').val() > 9999999 || !$.isNumeric($('#link_expire_value').val()))) {
      $('#link_expire_value').addClass('error');
      error = true;
    }

    if ($('#link_expire_type').val() == 'date' && $('#link_expire_value2').val().length != 16) {
      $('#link_expire_value2').addClass('error');
      error = true;
    }

    if (error) {
      $(button).removeClass('loading');
      return;
    }

    $.post(ajaxurl, {
             action: 'ucp_access_links',
             _ajax_nonce: ucp_vars.nonce_access_links,
             sub_action: 'add',
             link_name: $('#link_name').val(),
             link_expire_type: $('#link_expire_type').val(),
             link_expire_value: $('#link_expire_value').val(),
             link_expire_value2: $('#link_expire_value2').val()
          },
          function(response) {
            if (response.success) {
              alert('New Direct Access Link has been added.');
              window.location.reload(true);
            } else {
              alert(response.data);
              $(button).removeClass('loading');
            }
          }).fail(function() {
            alert('Undocumented error. Please reload the page and try again.');
            $(button).removeClass('loading');
          });

    return false;
  }); // save_new_access_link


  // ajax, save new affiliate link
  $('#save-new-affiliate-link').on('click', function(e) {
    e.preventDefault();
    error = false;
    button = this;

    $(button).addClass('loading');
    $('#link_name').removeClass('error');

    if ($('#link_name2').val().trim().length < 3 || $('#link_name2').val().trim().length > 128) {
      $('#link_name2').addClass('error');
      error = true;
    }

    if (error) {
      $(button).removeClass('loading');
      return;
    }

    $.post(ajaxurl, {
             action: 'ucp_affiliate_links',
             _ajax_nonce: ucp_vars.nonce_affiliate_links,
             sub_action: 'add',
             link_name: $('#link_name2').val(),
          },
          function(response) {
            if (response.success) {
              alert('New Affiliate Link has been added.');
              window.location.reload(true);
            } else {
              alert(response.data);
              $(button).removeClass('loading');
            }
          }).fail(function() {
            alert('Undocumented error. Please reload the page and try again.');
            $(button).removeClass('loading');
          });

    return false;
  }); // save_new_affiliate_link


  // ajax, save changes to access link
  $('#save-access-link').on('click', function(e) {
    e.preventDefault();
    error = false;
    button = this;

    $(button).addClass('loading');
    $('#link_name').removeClass('error');
    $('#link_expire_value').removeClass('error');

    if ($('#link_name').val().trim().length < 3 || $('#link_name').val().trim().length > 128) {
      $('#link_name').addClass('error');
      error = true;
    }

    if ($('#link_expire_type').val() == 'sessions' && ($('#link_expire_value').val() < 1 || $('#link_expire_value').val() > 9999999 || !$.isNumeric($('#link_expire_value').val()))) {
      $('#link_expire_value').addClass('error');
      error = true;
    }

    if ($('#link_expire_type').val() == 'date' && $('#link_expire_value2').val().length != 16) {
      $('#link_expire_value2').addClass('error');
      error = true;
    }

    if (error) {
      $(button).removeClass('loading');
      return;
    }

    $.post(ajaxurl, {
             action: 'ucp_access_links',
             _ajax_nonce: ucp_vars.nonce_access_links,
             sub_action: 'edit',
             link_id: $('#link_id').val(),
             link_name: $('#link_name').val(),
             link_expire_type: $('#link_expire_type').val(),
             link_expire_value: $('#link_expire_value').val(),
             link_expire_value2: $('#link_expire_value2').val()
          },
          function(response) {
            if (response.success) {
              alert('Changes saved.');
              window.location.reload(true);
            } else {
              alert(response.data);
              $(button).removeClass('loading');
            }
          }).fail(function() {
            alert('Undocumented error. Please reload the page and try again.');
            $(button).removeClass('loading');
          });

    return false;
  }); // save_access_link


  // ajax, save changes to affiliate link
  $('#save-affiliate-link').on('click', function(e) {
    e.preventDefault();
    error = false;
    button = this;

    $(button).addClass('loading');
    $('#link_name2').removeClass('error');

    if ($('#link_name2').val().trim().length < 3 || $('#link_name2').val().trim().length > 128) {
      $('#link_name2').addClass('error');
      error = true;
    }

    if (error) {
      $(button).removeClass('loading');
      return;
    }

    $.post(ajaxurl, {
             action: 'ucp_affiliate_links',
             _ajax_nonce: ucp_vars.nonce_affiliate_links,
             sub_action: 'edit',
             link_id: $('#link_id2').val(),
             link_name: $('#link_name2').val(),
          },
          function(response) {
            if (response.success) {
              alert('Changes saved.');
              window.location.reload(true);
            } else {
              alert(response.data);
              $(button).removeClass('loading');
            }
          }).fail(function() {
            alert('Undocumented error. Please reload the page and try again.');
            $(button).removeClass('loading');
          });

    return false;
  }); // save_affiliate_link


  // toggle start/end date fields
  $('#end_date_toggle').on('change', function(e, is_triggered) {
    if ($(this).is(':checked')) {
      if (is_triggered) {
        $('#end_date_wrapper').show();
      } else {
        $('#end_date_wrapper').slideDown();
      }
    } else {
      if (is_triggered) {
        $('#end_date_wrapper').hide();
      } else {
        $('#end_date_wrapper').slideUp();
      }
    }
  }).triggerHandler('change', true);

  $('#start_date_toggle').on('change', function(e, is_triggered) {
    if ($(this).is(':checked')) {
      if (is_triggered) {
        $('#start_date_wrapper').show();
      } else {
        $('#start_date_wrapper').slideDown();
      }
    } else {
      if (is_triggered) {
        $('#start_date_wrapper').hide();
      } else {
        $('#start_date_wrapper').slideUp();
      }
    }
  }).triggerHandler('change', true);
  // toggle start/end date fields

  $('#status_toggle').on('change', function(e, is_triggered) {
    if ($(this).is(':checked')) {
      if (is_triggered) {
        $('#status_wrapper').show();
      } else {
        $('#status_wrapper').slideDown();
      }
    } else {
      if (is_triggered) {
        $('#status_wrapper').hide();
      } else {
        $('#status_wrapper').slideUp();
      }
    }
  }).triggerHandler('change', true);
  // toggle HTTP response code visibility

    $('#ga_tracking_toggle').on('change', function(e, is_triggered) {
    if ($(this).is(':checked')) {
      if (is_triggered) {
        $('#ga_tracking_wrapper').show();
      } else {
        $('#ga_tracking_wrapper').slideDown();
      }
    } else {
      if (is_triggered) {
        $('#ga_tracking_wrapper').hide();
      } else {
        $('#ga_tracking_wrapper').slideUp();
      }
    }
  }).triggerHandler('change', true);
  
  
  $('#site_password_toggle').on('change', function(e, is_triggered) {
    if ($(this).is(':checked')) {
      if (is_triggered) {
        $('#site_password_wrapper').show();
      } else {
        $('#site_password_wrapper').slideDown();
      }
    } else {
      if (is_triggered) {
        $('#site_password_wrapper').hide();
      } else {
        $('#site_password_wrapper').slideUp();
      }
    }
  }).triggerHandler('change', true);


  // based on link expiration type show/hide inputs
  $('#link_expire_type').on('change', function(e) {
    type = $(this).val();

    if (type == 'sessions' || type == 'ip') {
      $('#link_expire_value_container_number').show();
      $('#link_expire_value_container_date').hide();
    } else if (type == 'date') {
      $('#link_expire_value_container_number').hide();
      $('#link_expire_value_container_date').show();
    } else {
      $('#link_expire_value_container_number').hide();
      $('#link_expire_value_container_date').hide();
    }
  }).trigger('change'); // add_access_link


  // show/hide redirect URL field
  $('#redirect_toggle').on('change', function(e) {
    if($(this).is(":checked")) {
      $('#redirect_wrapper').show();
    } else {
      $('#redirect_wrapper').hide();
    }
  }).trigger('change'); // redirect_toggle


  // show/hide URL rules
  $('#url_rules_type').on('change', function(e) {
    rule = $(this).val();

    if(rule != '0') {
      $('#url_rules_wrapper').show();
    } else {
      $('#url_rules_wrapper').hide();
    }
  }).trigger('change'); // redirect_toggle


  // ajax, delete all access links
  $('a.delete-all-access-links').on('click', function(e) {
    e.preventDefault();

    rows = $('#direct_access_links tr').not('.header');

    if (confirm('Are you sure you want to delete all direct access links? There is NO undo!')) {
      $.post(ajaxurl, {
             action: 'ucp_access_links',
             _ajax_nonce: ucp_vars.nonce_access_links,
             sub_action: 'delete_all'
      });
      $(rows).hide(500);
      $('#no-access-links').show(500);
    } // if confirm

    return false;
  }); // delete all access links

  // delete all leads
  $('#delete-all-leads').on('click', function(e) {
    if (confirm('Are you sure you want to delete all leads? There is NO undo!')) {
      return true;
    } else {
      e.preventDefault();
      return false;
    }
  }); // delete all leads


  // ajax, delete all affiliate links
  $('a.delete-all-affiliate-links').on('click', function(e) {
    e.preventDefault();

    rows = $('#affiliate_links tr').not('.header');

    if (confirm('Are you sure you want to delete all affiliate links? There is NO undo!')) {
      $.post(ajaxurl, {
             action: 'ucp_affiliate_links',
             _ajax_nonce: ucp_vars.nonce_affiliate_links,
             sub_action: 'delete_all'
      });
      $(rows).hide(500);
      $('#no-affiliate-links').show(500);
    } // if confirm

    return false;
  }); // delete all affiliate links


  // init access links dialog
  $('#access-link-dialog').dialog({'dialogClass': 'wp-dialog ucp-dialog ucp-access-link-dialog',
                               'modal': 1,
                               'resizable': false,
                               'zIndex': 9999,
                               'width': 700,
                               'height': 'auto',
                               'show': 'fade',
                               'hide': 'fade',
                               'open': function(event, ui) { UCP.fix_dialog_close(event, ui); },
                               'close': function(event, ui) { },
                               'autoOpen': false,
                               'closeOnEscape': true
                              });

  // init affiliate links dialog
  $('#affiliate-link-dialog').dialog({'dialogClass': 'wp-dialog ucp-dialog ucp-affiliate-link-dialog',
                               'modal': 1,
                               'resizable': false,
                               'zIndex': 9999,
                               'width': 700,
                               'height': 'auto',
                               'show': 'fade',
                               'hide': 'fade',
                               'open': function(event, ui) { UCP.fix_dialog_close(event, ui); },
                               'close': function(event, ui) { },
                               'autoOpen': false,
                               'closeOnEscape': true
                              });


  // init autoresponder config dialog
  $('#autoresponder-config-dialog').dialog({'dialogClass': 'wp-dialog ucp-dialog ucp-autoresponder-dialog',
                               'modal': 1,
                               'resizable': false,
                               'zIndex': 9999,
                               'width': 700,
                               'height': 'auto',
                               'show': 'fade',
                               'hide': 'fade',
                               'open': function(event, ui) { UCP.fix_dialog_close(event, ui); },
                               'close': function(event, ui) { },
                               'autoOpen': false,
                               'closeOnEscape': true
                              });


  // fix when opening datepicker
  $('.show-datepicker').on('click', function(e) {
    e.preventDefault();

    $(this).prevAll('input.datepicker').focus();

    return false;
  });


  // fix for enter press in license field
  $('#license_key').on('keypress', function(e) {
    if (e.which == 13) {
      e.preventDefault();
      $('#submit-license').trigger('click');
      return false;
    }
  }); // if enter on license key field


  $('#submit-license').on('click',function(e){
    e.preventDefault();
    $(this).addClass('loading');

     $.get(ajaxurl, {
       action:'ucp_validate_license',
       license_key: $('#license_key').val(),
       _rand: Math.floor(Math.random() * 9999) + 1
     },
     function(response) {
       if(response.success){
         location.reload();
       } else {
         verify_licence_ajax();
       }
       $(this).removeClass('loading');
     }).fail(function() {
       alert('Undocumented error. Please reload the page and try again.');
     }).always(function() {
       $(this).removeClass('loading');
     });
  });

  function verify_licence_ajax(){
    $.get(ucp_vars.lc_ep, {
         action:'validate_license',
         license_key: $('#license_key').val(),
         code_base: ucp_vars.code_base,
         version: ucp_vars.version,
         site: ucp_vars.site,
         _rand: Math.floor(Math.random() * 9999) + 1
      },
      function(response) {
        if(response.success){
          $.post(ajaxurl, { license_key:$('#license_key').val(),
                            license_active: response.data.license_active,
                            license_type: response.data.license_type,
                            license_expires: response.data.license_expires,
                            _ajax_nonce: ucp_vars.nonce_lc_check,
                            action: 'ucp_save_lc'},
          function(response) {
            if (response.success) {
              location.reload();
            } else {
              alert('Undocumented error. Please reload the page and try again.');
            }
          }).fail(function() {
            alert('Something is not right. Please reload the page and try again');
          }).always(function() {
            $('#submit-license').removeClass('loading');
          });

          if(response.data.error){
            alert(response.data.error);
          }
        } else {
          if(response.data.error){
            alert(response.data.error);
          } else {
            alert('Undocumented error. Please reload the page and try again.');
          }
        }
        $('#submit-license').removeClass('loading');
      }).fail(function() {
        alert('Undocumented error. Please reload the page and try again.');
      }).always(function() {
        $('#submit-license').removeClass('loading');
      });
  }

  $('#ucp-refresh-templates').on('click',function(e){
    e.preventDefault();
    ucp_vars.refresh_templates = false;
    $(this).addClass('loading');
    ucp_refresh_templates();
  });

  function ucp_stripslashes(str) {
      str = str.replace(/\\'/g,'\'');
      str = str.replace(/\\"/g,'"');
      str = str.replace(/\\\\/g,'\\');
      return str;
  };


  var template_images_xhr = new XMLHttpRequest();
  var template_save_data_xhr = new XMLHttpRequest();
  var template_images_data = {};
  var template_images = {};
  var template_image_id = 0;
  var template_html = '';
  var template_slug = '';
  var template_name = '';
  var template_type = '';
  var template_version = '';
  var total_template_images = 0;
  var total_template_images_downloaded = 0;
  template_images_xhr.onreadystatechange = function(){
      if (this.readyState == 4 && this.status == 200){
        template_images_data[template_image_id] = this.response;
        download_template_image();
      }
  }

  template_save_data_xhr.onreadystatechange = function(){
    if (this.readyState == 4 && this.status == 200){
      location.reload();
    }
  };

  function download_template_image(){
    var found_image = false;
    for(image_id in template_images){
      if(template_images_data[image_id] == null){
        found_image = true;
        template_image_id = image_id;
        template_images_xhr.open('GET', template_images[template_image_id]);
        template_images_xhr.responseType = 'blob';
        template_images_xhr.send();
        total_template_images_downloaded++;
        $('#ucp-editor-page-loader').html('<div class="ucp-loader"><i class="fa fa-spinner fa-pulse fa-5x fa-fw margin-bottom"></i> Installing template ... '+Math.round(total_template_images_downloaded*total_template_images)+'%</div>');
        break;
      }
    }

    if(!found_image){
      send_template_data();
    }

  }

  function send_template_data(){

    var template_data = new FormData();
    template_data.append('action', 'ucp_install_template_js');
    template_data.append('name', template_name);
    template_data.append('slug', template_slug);
    template_data.append('type', template_type);
    template_data.append('version', template_version);
    template_data.append('html', template_html);
    for(image_id in template_images_data){
      template_data.append('image_'+image_id, template_images_data[image_id]);
    }
    template_save_data_xhr.open('POST', ajaxurl, true);
    template_save_data_xhr.send(template_data);
  }


  $('.ucp-install-template').on('click',function(e){
    e.preventDefault();
    $template_button = $(this);
    $(this).addClass('loading');

     $.get(ajaxurl, {
       action:'ucp_install_template',
       template: $(this).data('template-slug'),
       _rand: Math.floor(Math.random() * 9999) + 1
     },
     function(response) {
       if(response.success){
         location.reload();
       } else {
         ucp_install_template_ajax($template_button);
       }
       $(this).removeClass('loading');
     }).fail(function() {
       alert('Undocumented error. Please reload the page and try again.');
     }).always(function() {
       $(this).removeClass('loading');
     });





  });

  function ucp_install_template_ajax($this){
    $('body').append('<div id="ucp-editor-page-loader"><div class="ucp-loader"><i class="fa fa-spinner fa-pulse fa-5x fa-fw margin-bottom"></i> Installing template ... 1%</div></div>');
    template_html = '';
    template_name = $this.data('template-name');
    template_slug = $this.data('template-slug');
    template_type = $this.data('template-type');
    template_version = $this.data('template-version');
    template_images_data = {};
    template_images = {};
    template_image_id = 0;

    $.get('https://templates.underconstructionpage.com/', {
         ucp_action:'get_template',
         template_slug:template_slug,
         license_key: $('#license_key').val(),
         code_base: ucp_vars.code_base,
         version: ucp_vars.version,
         site: ucp_vars.site,
         _rand: Math.floor(Math.random() * 9999) + 1
      },
      function(response) {
        template_html = ucp_stripslashes(response);

        var image_regex = /(http[s]?:\/\/.*?.\.(?:png|jpg|gif|svg|jpeg))/gi;
        var found_images = template_html.match(image_regex);
        total_template_images = found_images.length;
        for(image_id in found_images){
          if(found_images[image_id].indexOf('captcha.php')>0){

          } else {
            template_images[image_id]=found_images[image_id];
            template_html = template_html.replace(found_images[image_id],'#!ucpimage#'+image_id+'#ucpimage!#');
          }
        }

        download_template_image();

        $this.removeClass('loading');
      }).fail(function() {
        alert('Undocumented error. Please reload the page and try again.');
      }).always(function() {
        $this.removeClass('loading');
      });
  }


  if(ucp_vars.refresh_templates == true){
    ucp_refresh_templates();
  }

  function ucp_refresh_templates(){
    $.get('https://templates.underconstructionpage.com/', {
         ucp_action:'get_templates',
         license_key: $('#license_key').val(),
         code_base: ucp_vars.code_base,
         version: ucp_vars.version,
         site: ucp_vars.site,
         _rand: Math.floor(Math.random() * 9999) + 1
      },
      function(response) {

        $.post(ajaxurl, {  templates:response,
                           _ajax_nonce: ucp_vars.nonce_ucp_refresh_templates,
                           action: 'ucp_refresh_templates'},
         function(response) {
           if(ucp_vars.refresh_templates == true){
             return false;
           }
           if(response.success) {
             location.reload();
           } else {
             alert('Undocumented error. Please reload the page and try again.');
           }
         }).fail(function() {
           alert('Something is not right. Please reload the page and try again');
         }).always(function() {
           $(this).removeClass('loading');
         });

        $(this).removeClass('loading');
      }).fail(function() {
        alert('Undocumented error. Please reload the page and try again.');
      }).always(function() {
        $(this).removeClass('loading');
      });
  }


  // fix for enter press in support email
  $('#support_email').on('keypress', function(e) {
    if (e.which == 13) {
      e.preventDefault();
      $('#ucp-send-support-message').trigger('click');
      return false;
    }
  }); // if enter on support email


  // send support message
  $('#ucp-send-support-message').on('click', function(e) {
    e.preventDefault();
    button = $(this);

    if ($('#support_email').val().length < 5 || $('#support_email').is(':invalid')) {
      alert('We need your email address, don\'t you think?');
      $('#support_email').select().focus();
      return false;
    }

    if ($('#support_message').val().length < 15) {
      alert('An empty message won\'t do anybody any good.');
      $('#support_message').select().focus();
      return false;
    }

    button.addClass('loading');
    $.post(ajaxurl, { support_email: $('#support_email').val(),
                      support_message: $('#support_message').val(),
                      support_info: $('#support_info:checked').val(),
                      _ajax_nonce: ucp_vars.nonce_submit_support_message,
                      action: 'ucp_submit_support_message'},
    function(data) {
      if (data.success) {
        alert('Message sent! Our agents will get back to you ASAP.');
      } else {
        alert(data.message);
      }
    }).fail(function() {
      alert('Something is not right. Please reload the page and try again');
    }).always(function() {
      button.removeClass('loading');
    });

    return false;
  });


  // warning if there are unsaved changes when previewing
  $('.settings_page_ucp').on('click', '#ucp_preview', function(e) {
    if ($('#ucp_form *').not('.skip-save').serialize() != old_settings) {
      if (!confirm('There are unsaved changes that will not be visible in the preview. Please save changes first.\nContinue?')) {
        e.preventDefault();
        return false;
      }
    }

    return true;
  });

  // todo
  $('#autoresponder-config-dialog').on('change keyup paste mouseup', '#autoresponder_html', function(e) {
    if ($(this).val().length < 10 || $(this).val().indexOf('</form>') == -1) {
      $('#form-fields-preview').html('<i>' + $('#form-fields-preview').data('default') + '</i>');
      $('#fill-form-values').hide();
      return true;
    }

    data = UCP.parse_form_html($(this).val());
    preview = '';

    if (data.action_url) {
      preview += 'Action URL: <code>' + data.action_url + '</code><br>';
    } else {
      preview += 'Action URL: <b>not detected</b><br>';
    }
    if (data.method) {
      preview += 'Method: <code>' + data.method + '</code><br>';
    } else {
      preview += 'Method: <b>not detected</b>, using GET<br>';
    }
    if (data.email_field) {
      preview += 'Email field name: <code>' + data.email_field + '</code><br>';
    } else {
      preview += 'Email field name: <b>not detected</b><br>';
      if (data.email_fields_extra) {
        preview += 'Possible email field names: <code>' + data.email_fields_extra + '</code><br>';
      }
    }
    if (data.name_field) {
      preview += 'Name field name: <code>' + data.name_field + '</code><br>';
    } else {
      preview += 'Name field name: <b>not detected</b><br>';
    }
    if (data.extra_data) {
      preview += 'Extra data: <code>' + data.extra_data + '</code><br>';
    } else {
      preview += 'Extra data: <b>not detected</b><br>';
    }
    $('#form-fields-preview').html(preview);

    $('#fill-form-values').show();
    return true;
  });


  $('#autoresponder-config-dialog').on('click', '#fill-form-values', function(e) {
    if ($('#autoresponder_html').val().length < 10) {
      alert('Paste the form HTML code first, please.');
      return false;
    }

    data = UCP.parse_form_html($('#autoresponder_html').val());

    $('#autoresponder_action_url').val(data.action_url);
    if (data.method) {
      $('#autoresponder_method').val(data.method);
    } else {
      $('#autoresponder_method').val('get');
    }
    $('#autoresponder_email_field').val(data.email_field);
    $('#autoresponder_name_field').val(data.name_field);
    $('#autoresponder_extra_data').val(data.extra_data);


    $('#autoresponder-config-dialog .ucp-close-ui-dialog').trigger('click');
    return false;
  }); // fill_form_values


  // refresh MC lists in advanced settings
  $('.settings_page_ucp').on('click', '#refresh-mc-lists', function(e) {
    e.preventDefault();
    button = $(this);
    lists_dropdown = $('#mc_list');
    api_key = $('#mc_api_key').val();

    if (api_key.length < 30 || api_key.indexOf('-') == -1) {
      alert('Please enter a valid MailChimp API key before refreshing the lists.');
      return false;
    }

    button.addClass('loading');

    $.get(ajaxurl, {
           action: 'ucp_get_mc_lists',
           _ajax_nonce: 33, // todo ucp._nonce_dismiss_pointer,
           mc_api_key: api_key
        },
        function(response) {
          if (response.success) {
            lists_dropdown.empty();
            if (response.data.length) {
              $.each(response.data, function(ind, option) {
                lists_dropdown.append($('<option></option>').val(option.val).html(option.label));
              });
              alert('Lists have been refreshed. Don\'t forget to Save Changes after choosing a list.');
            } else {
              alert('No lists found in MailChimp account.');
              lists_dropdown.append($('<option></option>').val('').html('no MailChimp lists available'));
            }
          } else {
            alert(response.data);
          }
        }).fail(function() {
          alert('Undocumented error. Please reload the page and try again.');
        }).always(function() {
          button.removeClass('loading');
        });

    return false;
  }); // refresh MC lists


  // helper for linking anchors in different tabs
  $('.settings_page_ucp').on('click', '.change_tab', function(e) {
    e.preventDefault();

    tab_name = 'ucp_' + $(this).data('tab');
    tab_id = $('#ucp_tabs ul.ui-tabs-nav li[aria-controls="' + tab_name + '"]').attr('aria-labelledby').replace('ui-id-', '')
    if (!tab_id) {
      console.log('Invalid tab name - ' + tab_name);
      return false;
    }

    $('#ucp_tabs').tabs('option', 'active', tab_id - 1);

    if ($(this).data('tab2')) {
      tab_name2 = 'tab_' + $(this).data('tab2');
      tmp = $('#' + tab_name + ' ul.ui-tabs-nav li[aria-controls="' + tab_name2 + '"]');
      tab_id = $('#' + tab_name + ' ul.ui-tabs-nav li').index(tmp);
      if (tab_id == -1) {
        console.log('Invalid secondary tab name - ' + tab_name2);
        return false;
      }

      $('#' + tab_name + ' .ucp-tabs-2nd-level').tabs('option', 'active', tab_id);
    } // if secondary tab


    // get the link anchor and scroll to it
    target = this.href.split('#')[1];
    if (target) {
      $.scrollTo('#' + target, 500, {offset: {top:-50, left:0}});
    } else {
      $.scrollTo(0, 500, {offset: {top:0, left:0}});
    }

    return false;
  }); // change tab

  // helper for linking anchors in different tabs
  $('.settings_page_ucp').on('click', '.confirm_action', function(e) {
    message = $(this).data('confirm');

    if (!message || confirm(message)) {
      return true;
    } else {
      e.preventDefault();
      return false;
    }
  }); // confirm action before link click

//---------------------------

if (!ucp_vars.stats) {
  $('#ucp-chart').remove();
  $('.chart-container').html('<img src="' + ucp_vars.plugin_url + 'images/' + $('.chart-container').data('placeholder') + '">');
} else {
  Chart.defaults.global.defaultFontColor = '#23282d';
  Chart.defaults.global.defaultFontFamily = '-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Oxygen-Sans,Ubuntu,Cantarell,"Helvetica Neue",sans-serif';
  Chart.defaults.global.defaultFontSize = 12;

ucp_chart = new Chart($('#ucp-chart'), {
    type: 'bar',
    data: {
        labels: ucp_vars.stats.dates,
        datasets: [
        {
            label: 'Views',
            yAxisID: 'yleft',
            xAxisID: 'xdown',
            data: ucp_vars.stats.views,
            backgroundColor: 'rgba(20, 20, 20, 0.2)',
            hoverBackgroundColor: 'rgba(255, 121, 0, 0.6)',
            borderWidth: 0
        },{
            label: 'Sessions',
            yAxisID: 'yleft',
            xAxisID: 'xdown',
            data: ucp_vars.stats.sessions,
            hoverBackgroundColor: 'rgba(255, 121, 0, 0.8)',
            backgroundColor: 'rgba(15, 15, 15, 0.2)',
            borderWidth: 0
        },
        {
            label: 'Conversions',
            yAxisID: 'yright',
            xAxisID: 'xdown',
            data: ucp_vars.stats.conversions,
            type: 'line',
            backgroundColor: false,
            fill: false,
            lineTension: 0,
            spanGaps: true,
            borderColor: 'rgba(255, 121, 0, 1)',
            pointBackgroundColor: 'rgba(255, 121, 0, 1)',
            pointBorderColor: 'rgba(255, 121, 0, 1)',
            borderDash: [4,4],
            pointRadius: 3,
            borderWidth: 2
        }
        ]
    },
    options: {
        legend: false,
        tooltips: {
                mode: 'index',
                intersect: false,
                callbacks: { title: function(value, values) {
                    index = value[0].index;
                    return moment(values.labels[index], 'YYYY-MM-DD').format('dddd, MMMM Do');
                }},
                displayColors: false
            },
        title: {
            display: true,
            position: 'top',
            fontSize: 15,
            padding: 15,
            text: ucp_vars.stats.totals.views + ' views, ' + ucp_vars.stats.totals.sessions + ' sessions & ' + ucp_vars.stats.totals.conversions + ' conversions in the last ' + ucp_vars.stats.totals.days + ' days'
        },
        scales: {
            xAxes: [{
                id: 'xdown',
                stacked: true,
                ticks: { callback: function(value, index, values) {
                        return moment(value, 'YYYY-MM-DD').format('MMM Do');
                    } },
                categoryPercentage: 0.85,
                time: { unit: 'day', displayFormats: {day: 'MMM Do'} , tooltipFormat: 'dddd, MMMM Do'},
                gridLines: { display: false },
            }],
            yAxes: [{
                id: 'yleft',
                position: 'left',
                type: 'linear',
                scaleLabel: {
                  display: true,
                  labelString: 'Views / Sessions'
                },
                gridLines: { display: false},
                stacked: false,
                ticks: {
                    beginAtZero: false,
                    maxTicksLimit: 12,
                    callback: function(value, index, values) { return Math.round(value); }
                }
            },
            {
                id: 'yright',
                position: 'right',
                type: 'linear',
                gridLines: { display: false},
                scaleLabel: {
                 display: true,
                 labelString: 'Conversions'
                },
                stacked: false,
                ticks: {
                    beginAtZero: true,
                    maxTicksLimit: 12,
                    callback: function(value, index, values) { return Math.round(value); }
                }
            }]
        }
    }
}); // chart
}


  $('#ucp-search-templates').on('change mouseup keyup focus blur search', function(e) {
    e.preventDefault();

    if (!$(this).val()) {
      $('#tab_templates').find('.ucp-thumb').show();
      return;
    }

    search_string = new RegExp($(this).val(), 'i');
    
    $('#tab_templates').find('.ucp-thumb').each(function(){
      if( search_string.test($(this).data('template-desc')) || search_string.test($(this).data('template-tags')) || search_string.test($(this).data('template-name')) ){
        $(this).show();
      } else {
        $(this).hide();
      }
    });
  }).trigger('search');


  $('.settings_page_ucp').on('click', '.open-weglot-upsell', function(e) {
    e.preventDefault();

    $(this).blur();

    $('#weglot-upsell-dialog').dialog('open');

    return false;
  });

  $('#weglot-upsell-dialog').dialog({'dialogClass': 'wp-dialog ucp-dialog weglot-upsell-dialog',
                              'modal': 1,
                              'resizable': false,
                              'title': 'Translate your under construction page to any language',
                              'zIndex': 9999,
                              'width': 550,
                              'height': 'auto',
                              'show': 'fade',
                              'hide': 'fade',
                              'open': function(event, ui) {
                                ucp_fix_dialog_close(event, ui);
                                $(this).siblings().find('span.ui-dialog-title').html(ucp_vars.weglot_dialog_upsell_title);
                              },
                              'close': function(event, ui) { },
                              'autoOpen': false,
                              'closeOnEscape': true
  });
  $(window).resize(function(e) {
    $('#weglot-upsell-dialog').dialog("option", "position", {my: "center", at: "center", of: window});
  });


  jQuery('#install-weglot').on('click',function(e){
    $('#weglot-upsell-dialog').dialog('close');
    jQuery('body').append('<div style="width:550px;height:450px; position:fixed;top:10%;left:50%;margin-left:-275px; color:#444; background-color: #fbfbfb;border:1px solid #DDD; border-radius:4px;box-shadow: 0px 0px 0px 4000px rgba(0, 0, 0, 0.85);z-index: 9999999;"><iframe src="' + ucp_vars.weglot_install_url + '" style="width:100%;height:100%;border:none;" /></div>');
    jQuery('#wpwrap').css('pointer-events', 'none');
    e.preventDefault();
    return false;
  });

  function ucp_fix_dialog_close(event, ui) {
    jQuery('.ui-widget-overlay').bind('click', function(){
      jQuery('#' + event.target.id).dialog('close');
    });
  } // ucp_fix_dialog_close


}); // on ready
