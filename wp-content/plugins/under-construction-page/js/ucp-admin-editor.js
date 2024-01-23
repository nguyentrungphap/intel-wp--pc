/*
 * UnderConstructionPage PRO
 * Builder JS
 * (c) Web factory Ltd, 2015 - 2017
 */

 (function($) {
    $(document).ready(function(e) {

//Global variables
      var ucp_iframe,ucp_doc,ucp_body;

      var jQueryLoaded = false;
      var particlesJSLoaded = false;
      var fcountdownLoaded = false;
      window.ucp_save_confirm = false;
      var elements_html = {};
      var padding_sides = ['top','right','bottom','left'];

      elements_html.heading_l = '<div class="ucp-module-dd col-12 col-md-12 col-sm-12 col-xs-12" data-module-type="heading_l"><div class="ucp-element" data-element-type="heading_l" data-css-attr="color,font-size" data-attr="html"><div class="headingl"><h1>Sorry, we\'re doing some work on the site</h1></div></div></div>';
      elements_html.heading_s = '<div class="ucp-module-dd col-12 col-md-12 col-sm-12 col-xs-12" data-module-type="heading_s"><div class="ucp-element" data-element-type="heading_s" data-css-attr="color,font-size" data-attr="html"><div class="headings"><h2>Sorry, we\'re doing some work on the site</h2></div></div></div>';
      elements_html.text = '<div class="ucp-module-dd col-12 col-md-12 col-sm-12 col-xs-12" data-module-type="text"><div class="ucp-element" data-element-type="text" data-css-attr="color,font-size" data-attr="html"><div class="text">Thank you for being patient. We are doing some work on the site and will be back shortly.</div></div></div>';
      elements_html.image = '<div class="ucp-module-dd col-12 col-md-12 col-sm-12 col-xs-12" data-module-type="image"><div class="ucp-element" data-element-type="image" data-css-attr="border" data-attr="src"><img class="image" src="'+ucp_admin_editor_variables.ucp_plugin_url+'/images/original/rocket.png" alt="Rocket Launch" title="Rocket Launch"></div></div>';
      elements_html.video = '<div class="ucp-module-dd col-12 col-md-12 col-sm-12 col-xs-12" data-module-type="video"><div class="ucp-element" data-element-type="video" data-css-attr="border" data-attr="src"><div class="ucp-module-dd-overlay" style="width:100%;height:100%;z-index:9999;display:block;background: rgba(255, 255, 255, 0.07); position: absolute;"></div><iframe src="https://www.youtube.com/embed/ScMzIvxBSi4" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen width="600" height="400"></iframe></div></div>';

      var module_names = {};
      module_names.heading_l="Large Heading";
      module_names.heading_s="Small Heading";
      module_names.text="Text";
      module_names.input="Input";
      module_names.submit="Submit";
      module_names.image="Image";
      module_names.textarea="Textarea";
      module_names.video="Video";
      module_names.social="Social";
      module_names.newsletter="Newsletter";
      module_names.contact="Contact";
      module_names.countdown="Countdown Timer";
      module_names.countdown_timer="Countdown Timer";
      module_names.large_button="Large Button";
      module_names.divider="Divider";
      module_names.html="HTML";
      module_names.captcha="Captcha";
      module_names.terms_checkbox="Checkbox";
      module_names.gmap="Google Maps";

      var ucp_social_networks = {};
      ucp_social_networks.facebook='facebook-square';
      ucp_social_networks.twitter='twitter-square';
      ucp_social_networks.google='google-plus-square';
      ucp_social_networks.linkedin='linkedin-square';
      ucp_social_networks.youtube='youtube-square';
      ucp_social_networks.vimeo='vimeo-square';
      ucp_social_networks.pinterest='pinterest-square';
      ucp_social_networks.dribbble='dribbble';
      ucp_social_networks.behance='behance-square';
      ucp_social_networks.instagram='instagram';
      ucp_social_networks.tumblr='tumblr-square';
      ucp_social_networks.skype='skype';
      ucp_social_networks.whatsapp='whatsapp';
      ucp_social_networks.telegram='telegram';
      ucp_social_networks.envelope='envelope';
      ucp_social_networks.phone='phone-square';
      elements_html.social = '<div class="ucp-module-dd col-12 col-md-12 col-sm-12 col-xs-12" data-module-type="social"><div class="ucp-element" data-element-type="social" data-css-attr="color,font-size" data-attr="src" data-element-id="e19093"><div class="socialicons">';
      for(sn in ucp_social_networks){
        elements_html.social += '<a class="ucp-social-'+sn+'" title="'+sn.charAt(0).toUpperCase()+sn.slice(1)+'" href="#" target="_blank"><i class="fa fa-'+ucp_social_networks[sn]+' fa-3x"></i></a>';
      }
      elements_html.social += '</div></div></div>';

      ucp_terms_checkbox_html = '<input type="checkbox" name="terms" class="input_terms" /> <label>I understand the site\'s privacy policy and am willingly sharing my email address</label>';
      
      elements_html.newsletter = '<form method="post" action="" class="ucp-module-dd col-12 col-md-12 col-sm-12 col-xs-12" data-module-type="newsletter" data-processor="local" data-msg-success="You have been successfully subscribed!" data-msg-captcha="Captcha validation failed!" data-msg-error="An error occured!">';
      elements_html.newsletter += '<div class="ucp-element" data-element-type="input" data-css-attr="color,font-size" data-attr="html"><input type="text" name="name" class="input_name" placeholder="Name" value="" /></div>';
      elements_html.newsletter += '<div class="ucp-element" data-element-type="input" data-css-attr="color,font-size" data-attr="html"><input type="email" name="email" class="input_email" placeholder="Email" value="" /></div>';
      elements_html.newsletter += '<div class="ucp-element" data-element-type="terms_checkbox"><div class="ucp-terms-checkbox">'+ucp_terms_checkbox_html+'</div></div>';
      elements_html.newsletter += '<div class="ucp-element" data-element-type="captcha"><div class="ucp-captcha">'+ucp_admin_editor_variables.ucp_captcha_html+'</div></div>';
      elements_html.newsletter += '<div class="ucp-element" data-element-type="submit" data-css-attr="color,font-size" data-attr="html"><input type="submit" name="newsletter_submit" class="input_submit" value="Subscribe" /></div>';
      elements_html.newsletter += '</form>';

      elements_html.contact = '<form method="post" action="" data-processor="local" class="ucp-module-dd col-12 col-md-12 col-sm-12 col-xs-12" data-module-type="contact" data-admin-email="'+ucp_admin_editor_variables.admin_email+'" data-email-subject="Your message has been received!" data-email-body="Your message has been received and we will get back to you as soon as possible" data-msg-success="Your message has been successfully sent!" data-msg-captcha="Captcha validation failed!" data-msg-error="An error occured!">';
      elements_html.contact += '<div class="ucp-element" data-element-type="input" data-css-attr="color,font-size" data-attr="html"><input type="text" name="name" class="input_name" placeholder="Name" value="" /></div>';
      elements_html.contact += '<div class="ucp-element" data-element-type="input" data-css-attr="color,font-size" data-attr="html"><input type="email" name="email" class="input_email" placeholder="Email" value="" /></div>';
      elements_html.contact += '<div class="ucp-element" data-element-type="input" data-css-attr="color,font-size" data-attr="html"><input type="tel" name="phone" class="input_phone" placeholder="Phone" value="" /></div>';
      elements_html.contact += '<div class="ucp-element" data-element-type="textarea" data-css-attr="color,font-size" data-attr="html"><textarea name="message" class="input_message" placeholder="Message"></textarea></div>';
      elements_html.contact += '<div class="ucp-element" data-element-type="terms_checkbox"><div class="ucp-terms-checkbox">'+ucp_terms_checkbox_html+'</div></div>';
      elements_html.contact += '<div class="ucp-element" data-element-type="captcha"><div class="ucp-captcha">'+ucp_admin_editor_variables.ucp_captcha_html+'</div></div>';
      elements_html.contact += '<div class="ucp-element" data-element-type="submit" data-css-attr="color,font-size" data-attr="html" data-processor="local"><input type="submit" name="newsletter_submit" class="input_submit" value="Submit Message" /></div>';
      elements_html.contact += '</form>';

      elements_html.countdown = '<div class="ucp-module-dd col-12 col-md-12 col-sm-12 col-xs-12" data-module-type="countdown">';
      elements_html.countdown += '<div class="ucp-element" data-element-type="countdown_timer" data-css-attr="color,font-size" data-date="2018/5/21 14:30:00" data-style="ucp_countdown_text" data-attr="html"><div class="fcountdown-timer">10days 10:50:10</div></div>';
      elements_html.countdown += '</div>';

      elements_html.large_button = '<div class="ucp-module-dd col-12 col-md-12 col-sm-12 col-xs-12" data-module-type="large_button"><div class="ucp-element" data-element-type="large_button" data-css-attr="color,font-size" data-attr="html" data-hover-background-color="rgb(255, 207, 0)" data-hover-text-color="rgb(255, 255, 255)"><a href="#" target="_self" class="button-large">Call to action</a></div></div>';

      elements_html.divider = '<div class="ucp-module-dd col-12 col-md-12 col-sm-12 col-xs-12" data-module-type="divider"><div class="ucp-element" data-element-type="divider"><div class="divider"></div></div></div>';

      elements_html.html = '<div class="ucp-module-dd col-12 col-md-12 col-sm-12 col-xs-12" data-module-type="html"><div class="ucp-element" data-element-type="html" data-attr="html"><div class="html">HTML Block</div></div></div>';

      elements_html.gmap = '<div class="ucp-module-dd col-12 col-md-12 col-sm-12 col-xs-12" data-module-type="gmap"><div class="ucp-element" data-element-type="gmap"><div class="ucp-module-dd-overlay" style="width:100%;height:100%;z-index:9999;display:block;background: rgba(255, 255, 255, 0.07); position: absolute;"></div><iframe class="gmap" width="500px" height="200px" src="https://www.google.com/maps/embed/v1/place?q=New+York%2C+USA&attribution_source=Google+Maps+Widget&attribution_web_url=http%3A%2F%2Flocalhost%2Fgordan%2Fwp_gmw&attribution_ios_deep_link_id=comgooglemaps%3A%2F%2F%3Fdaddr%3DNew+York%2C+USA&maptype=roadmap&zoom=14&language=en&key=AIzaSyArcXkQ15FoOTS2Z7El2SJHDIlTMW7Rxxg" allowfullscreen></iframe></div></div>';

      var page_background = {};

      var css_attributes={};
      css_attributes.text={};
      css_attributes.text['color']='#000000';
      css_attributes.text['font-size']='18px';
      css_attributes.text['font-family']='Open Sans';
      css_attributes.text['font-weight']='normal';
      css_attributes.text['text-align']='center';

      css_attributes.heading_l={};
      css_attributes.heading_l['color']='#000000';
      css_attributes.heading_l['font-size']='36px';
      css_attributes.heading_l['font-family']='Open Sans';
      css_attributes.heading_l['font-weight']='bold';
      css_attributes.heading_l['text-align']='center';

      css_attributes.heading_s={};
      css_attributes.heading_s['color']='#000000';
      css_attributes.heading_s['font-size']='28px';
      css_attributes.heading_s['font-family']='Open Sans';
      css_attributes.heading_s['font-weight']='bold';
      css_attributes.heading_s['text-align']='center';


      css_attributes.countdown_timer={};
      css_attributes.countdown_timer['color']='#000000';
      css_attributes.countdown_timer['font-size']='18px';
      css_attributes.countdown_timer['font-family']='Open Sans';
      css_attributes.countdown_timer['font-weight']='normal';
      css_attributes.social={};
      css_attributes.social['color']='#000000';
      css_attributes.social['color|hover']='#FF9900';
      css_attributes.social['font-size']='14px';
      css_attributes.input={};
      css_attributes.input['color']='#FF0000';
      css_attributes.input['padding']='2px';
      css_attributes.input['font-size']='18px';
      css_attributes.input['font-family']='Open Sans';
      css_attributes.input['font-weight']='normal';
      css_attributes.input['background-color']='#FFF';
      css_attributes.input['border-color']='#444';
      css_attributes.input['border-width']='1px';
      css_attributes.input['border-style']='solid';

      css_attributes.textarea={};
      css_attributes.textarea['color']='#FF0000';
      css_attributes.textarea['padding']='2px';
      css_attributes.textarea['font-size']='18px';
      css_attributes.textarea['font-family']='Open Sans';
      css_attributes.textarea['font-weight']='normal';
      css_attributes.textarea['background-color']='#FFF';
      css_attributes.textarea['border-color']='#444';
      css_attributes.textarea['border-width']='1px';
      css_attributes.textarea['border-style']='solid';

      css_attributes.submit={};
      css_attributes.submit['color']='#FF0000';
      css_attributes.submit['padding']='2px';
      css_attributes.submit['font-size']='18px';
      css_attributes.submit['font-family']='Open Sans';
      css_attributes.submit['font-weight']='normal';
      css_attributes.submit['background-color']='#444';
      css_attributes.submit['border-color']='#444';
      css_attributes.submit['border-width']='1px';
      css_attributes.submit['border-style']='solid';
      css_attributes.large_button={};
      css_attributes.large_button['color']='#FF0000';
      css_attributes.large_button['font-size']='18px';
      css_attributes.large_button['font-family']='Open Sans';
      css_attributes.large_button['font-weight']='normal';
      css_attributes.large_button['background-color']='#444';
      css_attributes.large_button['float']='none';
      css_attributes.large_button['padding-left']='0';
      css_attributes.large_button['padding-right']='0';
      css_attributes.large_button['padding-bottom']='0';
      css_attributes.large_button['padding-left']='0';
      css_attributes.divider={};
      css_attributes.divider['height']='40px';
      css_attributes.gmap={};

      css_attributes.captcha={};
      css_attributes.captcha['color']='#000000';

      css_attributes.terms_checkbox={};


      var css_styles=[];
      var css_module_styles=[];


      var css_module_attributes={};
      css_module_attributes['padding']='0px';
      css_module_attributes['border-width']='0px';
      css_module_attributes['border-color']='#DDD';
      css_module_attributes['border-style']='none';
      css_module_attributes['background']='';

      var css_google_fonts = [];

      var editing_element_id = false;
      var border_styles = ['none','solid','dashed','dotted','double','groove','hidden','inset','outset','ridge'];

      //Initial Setup

      $( "#ucp-style-sidebar" ).resizable({
         handles: 'e',
         start: function(event, ui) {
              //add a mask over the Iframe to prevent IE from stealing mouse events
              $("#ucp_editor_preview").append('<div id="ucp_editor_preview_mask" style="background-color:rgba(0,0,0,0); position: absolute; z-index: 2; left: 0pt; top: 0pt; right: 0pt; bottom: 0pt;"></div>');
          },
          stop: function(event, ui) {
              //remove mask when dragging ends
              $("#ucp_editor_preview_mask").remove();
          },
          resize: function(event, ui) {
              var new_width = $(document).width() - ui.size.width;
              $('#ucp_editor_preview_wrapper').css('margin-left',ui.size.width);
          },
          maxWidth: 500,
          minWidth: 170
      });

      var sidebar_open=true;
      var sidebar_width=300;
      $('#ucp-sidebar-toggle').on('click',function(){
        if(sidebar_open){
          $('#ucp-style-sidebar').animate({'left':$('#ucp-style-sidebar').width()*-1},200);
          $('#ucp_editor_preview_wrapper').animate({'margin-left':0},200);
          $('#ucp-sidebar-toggle').html('<i class="fa fa-caret-right" aria-hidden="true"></i>');
          sidebar_open=false;
        } else {
          $('#ucp-style-sidebar').animate({'left':0},200);
          $('#ucp_editor_preview_wrapper').animate({'margin-left':$('#ucp-style-sidebar').width()},200);
          $('#ucp-sidebar-toggle').html('<i class="fa fa-caret-left" aria-hidden="true"></i>');
          sidebar_open=true;
        }
      });

      $('#ucp_editor_iframe').load(function(e) {
          ucp_iframe = this.contentWindow;

          ucp_doc = ucp_iframe.document;
          ucp_body = ucp_doc.body;
          head = ucp_doc.head;

          var jQueryLoaded = false;
          var jQuery;
          setTimeout(function(){

            function loadJQueryUI() {

                ucp_iframe.jQuery.ajax({
                    url: ucp_admin_editor_variables.ucp_plugin_url+'/js/jquery-ui.min.js',
                    dataType: 'script',
                    cache: true,
                    success: function () {
                        setup_ucpiFrame(false);
                        setup_iframe_dd();
                        setup_events();
                        ucp_read_page_css();
                        ucp_iframe.jQuery('.ucp-row').css('cursor','auto');
                        ucp_iframe.jQuery('.ucp-element').css('cursor','auto');
                        ucp_iframe.jQuery('.ucp-module').css('cursor','auto');
  
                        $('#ucp-editor-page-loader').fadeOut(800, function() { $(this).remove(); });
  
                    }
                });
            }
  
            loadJQueryUI();
           }, 2000);          
      });


      function setup_iframe_dd(){
         // Setup D&D
         $('.ucp-sidebar-module').each(function(){
            $(this).attr('draggable', 'true');
            $(this).on('dragstart', function (event) {
                event.originalEvent.dataTransfer.setData('module-type', $(this).data('module-type'));
                event.originalEvent.effectAllowed = 'copy';
                ucp_iframe.ucp_dd_module_type = $(this).data('module-type');

                scrollwhiledragging=setInterval(function(){
                  current = ucp_iframe.jQuery(ucp_doc).scrollTop();

                  if(typeof scrollwhiledraggingspeed !== 'undefined') {
                   ucp_iframe.jQuery(ucp_doc).scrollTop(current - scrollwhiledraggingspeed);
                 }
              }, 50);
            });

            $(this).on('dragend',function (e) {
              clearInterval(scrollwhiledragging);
            });
          });

          ucp_iframe.jQuery('#ucp-template').on('dragover','.ucp-module,.ucp-row', function(event){
            if(jQuery(this).find('.ucp-module').length > 0 && jQuery(this).attr('class').indexOf('ucp-row') >= 0 ){
              return;
            }

            if(!ucp_dd_currentElement){
              ucp_dd_currentElement = this;

              if (event.preventDefault){ event.preventDefault(); }
              if (event.stopPropagation){ event.stopPropagation(); }
              event.originalEvent.dataTransfer.dropEffect = 'copy';
            }
            ucp_calc_scroll(event);
            ucp_add_placeholder(this,event);

            return false;
          });

          ucp_iframe.jQuery('body').on('dragover',function(event){
            event.preventDefault();
          });

          function ucp_add_placeholder($this,event){

            if($this.id){
              ucp_placeholder_module = $this.id;
              var target_middle_y = ucp_iframe.jQuery('#'+$this.id).height()/2;
              var offset = ucp_iframe.jQuery('#'+$this.id).offset();
              y = event.pageY- offset.top;

              if(target_middle_y > y){
                ucp_placeholder_pos = 'top';
              } else {
                ucp_placeholder_pos = 'bottom';
              }
            } else {
              ucp_placeholder_module = null;
              var target_middle_y = $($this).height()/2;
              var offset = $($this).offset();
              y = event.pageY- offset.top;

              if(target_middle_y > y){
                ucp_placeholder_pos = 'top';
              } else {
                ucp_placeholder_pos = 'bottom';
              }
            }


            if( ucp_placeholder_pos !== ucp_placeholder_pos_old ){

              ucp_iframe.jQuery('.ucp-drop-placeholder').remove();
              ucp_placeholder_pos_old = ucp_placeholder_pos;

              if(ucp_placeholder_module){
                if( ucp_placeholder_pos === 'top' ){
                  ucp_iframe.jQuery('#'+$this.id).before('<div class="ucp-drop-placeholder col-12 col-md-12 col-sm-12 col-xs-12">'+elements_html[ucp_iframe.ucp_dd_module_type]+'</div>');
                } else {
                  ucp_iframe.jQuery('#'+$this.id).after('<div class="ucp-drop-placeholder col-12 col-md-12 col-sm-12 col-xs-12">'+elements_html[ucp_iframe.ucp_dd_module_type]+'</div>');
                }
              } else {
                if( ucp_placeholder_pos === 'top' ){
                  $($this).prepend('<div class="ucp-drop-placeholder col-12 col-md-12 col-sm-12 col-xs-12" style="background:"#000;">'+elements_html[ucp_iframe.ucp_dd_module_type]+'</div>');
                } else {
                  $($this).append('<div class="ucp-drop-placeholder col-12 col-md-12 col-sm-12 col-xs-12">'+elements_html[ucp_iframe.ucp_dd_module_type]+'</div>');
                }
              }
            }
          }
          ucp_iframe.jQuery('.ucp-row').on('dragleave',function(event){
            var elementPosition = this.getBoundingClientRect();

            if ( 'dragleave' === event.type && ! (
              event.clientX < elementPosition.left ||
              event.clientX >= elementPosition.right ||
              event.clientY < elementPosition.top ||
              event.clientY >= elementPosition.bottom
            ) ) {
              return;
            }

            ucp_dd_currentElement = ucp_placeholder_pos = ucp_placeholder_pos_old = null;
            ucp_iframe.jQuery('.ucp-drop-placeholder').remove();
          });

          ucp_iframe.jQuery('.ucp-row').on('drop',function(event){

            if(!ucp_iframe.ucp_dd_module_type){
              return;
            }

            ucp_iframe.jQuery('.ucp-drop-placeholder').remove();
            $(this).removeClass('ucp-dd-over');
            var module_type = event.originalEvent.dataTransfer.getData('module-type');


            if( !ucp_dd_currentElement ){
              return;
            }

            if(ucp_placeholder_module){
              if( ucp_placeholder_pos === 'top' ){
                ucp_iframe.jQuery('#'+ucp_placeholder_module).before(elements_html[module_type].replace('ucp-module-dd','ucp-module'));
              } else {
                ucp_iframe.jQuery('#'+ucp_placeholder_module).after(elements_html[module_type].replace('ucp-module-dd','ucp-module'));
              }
            } else {
              if( ucp_placeholder_pos === 'top' ){
                $(this).prepend(elements_html[module_type].replace('ucp-module-dd','ucp-module'));
              } else {
                $(this).append(elements_html[module_type].replace('ucp-module-dd','ucp-module'));
              }
            }

            ucp_iframe.jQuery('.ucp-module-dd-overlay').remove();
            ucp_dd_currentElement = ucp_placeholder_pos = ucp_iframe.ucp_dd_module_type = null;

            setup_ucpiFrame(true);

          });

          ucp_iframe.jQuery('body').on('dragover',function (e) {
            ucp_calc_scroll(e);
          });



          function ucp_calc_scroll(e){
            if (typeof e.originalEvent.y === 'undefined') {
              mouse_y = e.originalEvent.clientY;
            } else {
              mouse_y = e.originalEvent.clientY;
            }


            bottom_gap = 200;

            if (mouse_y > (ucp_iframe.jQuery(window).height() - bottom_gap)) {
              scrollwhiledraggingspeed=(ucp_iframe.jQuery(window).height() - bottom_gap) - mouse_y;
            } else if (mouse_y < bottom_gap) {
              scrollwhiledraggingspeed=bottom_gap-mouse_y;
            } else {
              scrollwhiledraggingspeed=0;
            }
          }
      } // setup_iframe_dd



      var disable_hover=false;
      var ucp_dd_module_type,ucp_dd_currentElement,ucp_placeholder_pos,ucp_placeholder_pos_old,ucp_drop_pos,ucp_placeholder_module;
      var scrollwhiledragging;
      var scrollwhiledraggingdirection='none';

      function setup_ucpiFrame(new_module){
         // Load iframe CSS
         ucp_iframe.jQuery('<link/>', {rel: 'stylesheet', href: ucp_admin_editor_variables.ucp_plugin_url+'/css/ucp-admin-iframe.css?ver='+Math.random()}).appendTo('head');
         ucp_iframe.jQuery('<link/>', {rel: 'stylesheet', href: ucp_admin_editor_variables.ucp_plugin_url+'/css/font-awesome/font-awesome.min.css'}).appendTo('head');
         ucp_iframe.jQuery('<link/>', {rel: 'stylesheet', href: ucp_admin_editor_variables.ucp_plugin_url+'/css/flipclock.css'}).appendTo('head');
         if(!ucp_iframe.jQuery().countdown){
           ucp_iframe.jQuery('<script/>', {type: 'text/javascript', src: ucp_admin_editor_variables.ucp_plugin_url+'/js/jquery.countdown.min.js'}).appendTo('head');
         }
         if(!ucp_iframe.jQuery().FlipClock){
           ucp_iframe.jQuery('<script/>', {type: 'text/javascript', src: ucp_admin_editor_variables.ucp_plugin_url+'/js/flipclock.min.js'}).appendTo('head');
         }

         $('.ucp-input-shortcode-message').remove();

         ucp_iframe.elements_html = elements_html;
         var rowsortable = ucp_iframe.jQuery('.ucp-row').sortable({
           connectWith:['.ucp-row'],
           handle:'.ucp-module-move',
           placeholder: {
                element: function(currentItem) {
                    module_type = $(currentItem).data('module-type');
                    module_id = $(currentItem).data('module-id');

                    if(typeof module_id === 'undefined'){
                      return '<div class="ucp-dragging-module"><div class="ucp_editor_preview_mask" style="background-color:rgba(0,0,0,0.2); position: absolute; z-index: 2; left: 0pt; top: 0pt; right: 0pt; bottom: 0pt;"></div>'+elements_html[module_type]+'</div>';
                    } else {
                      return ucp_iframe.jQuery('<div class="'+ucp_iframe.jQuery(currentItem).attr('class')+'" style="opacity:0.5;background:rgba(0,0,0,0.1);"><div class="ucp_editor_preview_mask" style="background-color:rgba(0,0,0,0); position: absolute; z-index: 2; left: 0pt; top: 0pt; right: 0pt; bottom: 0pt;"></div>'+ucp_iframe.jQuery(currentItem).html()+'</div>')[0];
                    }
                },
                update: function(container, p) {
                    return;
                }
           },
           update: function(event, ui) {
                module_id = $(ui.item).data('module-id');
                module_type = $(ui.item).data('module-type');
                if(typeof module_id === 'undefined'){
                  ui.item.replaceWith(elements_html[module_type]);
                }

           },
           start: function(event,ui){
              disable_hover = true;
           },
           stop: function(event,ui){
              disable_hover = false;
              var module_type = $(ui.item).data('module-type');

              module_id = $(ui.item).data('module-id');
              if(typeof module_id === 'undefined'){
                setup_ucpiFrame(true);
              }

           }
         });



         ucp_iframe.jQuery('.ucp-row').droppable({
            helper: 'clone'
         });

         var current_module_id = new_module_id = '';
         // Assign unique IDs
         ucp_iframe.jQuery("[class*=ucp-row]").each(function() {
            ucp_iframe.jQuery(this).children().each(function(){

              var ucpmid = Math.floor(Math.random() * 100000);
              var ucpeid = Math.floor(Math.random() * 100000);


              if(typeof ucp_iframe.jQuery(this).attr('data-module-id') === 'undefined' || ucp_iframe.jQuery(this).attr('data-module-id') == false ){

                //Add unique IDs to each module
                ucp_iframe.jQuery(this).attr('data-module-id','m'+ucpmid);
                ucp_iframe.jQuery(this).attr('id','ucp-m'+ucpmid);

                current_module_id = new_module_id = 'm'+ucpmid;
                css_styles['ucp-m'+ucpmid]={};

                //Add unique IDs to each component
                ucp_iframe.jQuery(this).children('[data-element-type!=""]').each(function(){
                  ucp_iframe.jQuery(this).attr('data-element-id','e'+ucpeid);
                  ucpeid++;
                });

              } else {
                current_module_id = ucp_iframe.jQuery(this).attr('data-module-id');
              }

              var module_css_id = 'ucp-'+current_module_id;

              if(typeof css_styles[module_css_id] === 'undefined'){
                css_styles[module_css_id]={};
              }

              if(typeof css_styles[module_css_id]['modulecss'] === 'undefined'){
                css_styles[module_css_id]['modulecss']={};
              }



              for(side in padding_sides){
                css_styles[module_css_id]['modulecss']['padding-'+padding_sides[side]] = ucp_iframe.jQuery(this).clone().appendTo('body').wrap('<div class="ucp-temp-module" style="display:none;"></div>').css('padding-'+padding_sides[side]);
                ucp_iframe.jQuery('.ucp-temp-module').remove();
              }

              if(css_styles[module_css_id]['modulecss']['padding-left'].indexOf('1500')>-1){
                css_styles[module_css_id]['modulecss']['full-width'] = true;
                css_styles[module_css_id]['modulecss']['box-sizing'] = 'content-box';
              } else {
                css_styles[module_css_id]['modulecss']['full-width'] = false;
                css_styles[module_css_id]['modulecss']['box-sizing'] = 'border-box';
              }

              css_styles[module_css_id]['modulecss']['margin-left']=ucp_iframe.jQuery(this).css('margin-left');
              css_styles[module_css_id]['modulecss']['margin-right']=ucp_iframe.jQuery(this).css('margin-right');
              css_styles[module_css_id]['modulecss']['margin-top']=ucp_iframe.jQuery(this).css('margin-top');
              css_styles[module_css_id]['modulecss']['margin-bottom']=ucp_iframe.jQuery(this).css('margin-bottom');


              
              if(ucp_iframe.jQuery(this).data('module-type') == 'image'){
                css_styles[module_css_id]['modulecss']['border-color']=ucp_iframe.jQuery(this).find('img').css('border-color');
                css_styles[module_css_id]['modulecss']['border-width']=ucp_iframe.jQuery(this).find('img').css('border-width');
                css_styles[module_css_id]['modulecss']['border-style']=ucp_iframe.jQuery(this).find('img').css('border-style');
              } else {
                css_styles[module_css_id]['modulecss']['border-color']=ucp_iframe.jQuery(this).css('border-color');
                css_styles[module_css_id]['modulecss']['border-width']=ucp_iframe.jQuery(this).css('border-width');
                css_styles[module_css_id]['modulecss']['border-style']=ucp_iframe.jQuery(this).css('border-style');
              }
              ucp_get_css_background(current_module_id);

            });
         });

         //Read Element CSS Style
         ucp_iframe.jQuery('[data-element-type]').each(function(){
           var element_id = ucp_iframe.jQuery(this).data('element-id');
           var module_id = ucp_iframe.jQuery('[data-element-id="' + element_id +'"]').closest( ".ucp-module" ).attr('id');

           if(typeof ucp_iframe.jQuery('[data-element-id="' + element_id +'"]').children().last().attr('class') !== typeof undefined){
             var element_class = ucp_iframe.jQuery('[data-element-id="' + element_id +'"]').children().last().attr('class').split(' ')[0];
           } else {
             ucp_iframe.jQuery('[data-element-id="' + element_id +'"]').children().last().attr('class','ucp-element-child');
             var element_class = 'ucp-element-child';
           }

           var element_type = ucp_iframe.jQuery(this).data('element-type');

           for(attr in css_attributes[element_type]){

              if(typeof css_styles[module_id] === 'undefined'){
                css_styles[module_id]={};
              }

              if(typeof css_styles[module_id][element_class] === 'undefined'){
                css_styles[module_id][element_class]={};
              }

              if( 'ucp-'+new_module_id == module_id){
                css_styles[module_id][element_class][attr]=css_attributes[element_type][attr];
              } else if(attr == 'font-family'){
                css_styles[module_id][element_class][attr]=ucp_iframe.jQuery('[data-element-id="' + element_id +'"]').children().last().css(attr).split(',')[0].replace(/"/g,'').trim();
              } else {
                css_styles[module_id][element_class][attr]=ucp_iframe.jQuery('[data-element-id="' + element_id +'"]').children().last().clone().appendTo(ucp_iframe.jQuery('[data-element-id="' + element_id +'"]').parent()).wrap('<div class="ucp-temp-module" style="display:none;"></div>').css(attr);
                ucp_iframe.jQuery('.ucp-temp-module').remove();
              }
           }


         });

         refresh_google_fonts();
         // Setup Events


         // Open new module in editor
         if(new_module){
           ucp_iframe.jQuery('.ucp-new-page-message').remove();
           if( ucp_iframe.jQuery('[data-module-id="' + new_module_id +'"]').data('module-type')=='countdown' ){
             var fcountdown_JS='jQuery(document).ready(function(e) {    jQuery("#ucp-'+new_module_id+' .fcountdown-timer").countdown("'+ucp_iframe.jQuery('[data-module-id="' + new_module_id +'"]').children().first().attr('data-date')+'", function(event) {   jQuery(this).text(event.strftime("%D days %H:%M:%S"));  }); });';
             ucp_iframe.jQuery('#ucp_template_fcountdown_js').remove();
             ucp_iframe.jQuery('<script/>', {id: 'ucp_template_fcountdown_js', text: fcountdown_JS}).appendTo('#ucp-template');
           } else if( ucp_iframe.jQuery('[data-module-id="' + new_module_id +'"]').data('module-type')=='newsletter' || ucp_iframe.jQuery('[data-module-id="' + new_module_id +'"]').data('module-type')=='contact' ){
             ucp_iframe.ucp_frontend.initialize_ucp_forms();
           }
           rebuild_iframe_css();
           ucp_open_module_editor(new_module_id,0);
         }
      }

//Media Libraries
      var unsplash_page=1;
      var total_pages=9999;
      var total_results=0;
      var unsplash_search_query='';
      var unsplash_image_large=true;

      var mediaUploader;
      $('#ucp-style-sidebar').on('click','.ucp_image_upload', function(e) {
        $this = $(this);
        e.preventDefault();

        if($(this).hasClass('ucp-image-large')){
          unsplash_image_large = true;
        } else {
          unsplash_image_large = false;
        }

        if (mediaUploader) {
          mediaUploader.open();
          return;
        }

        mediaUploader = wp.media.frames.file_frame = wp.media({
          title: 'Choose Image',
          button: {
            text: 'Choose Image'
          }, multiple: false
        });

        mediaUploader.on('open',function() {
          var image_input_id=$this.parent().children('.ucp_image').attr('id');

          if(!$('.media-frame-router .media-router .ucp-unsplash-images').length){
            $('.media-frame-router .media-router').append('<a href="#" class="media-menu-item ucp-unsplash-images">Images library</a>');
          } else {
            if(mediaUploader.content._mode == 'unsplash'){
              unsplash_search_query = '';
              ucp_get_unsplash_images(1);
            }
          }

          if(!$('.media-frame-router .media-router .ucp-ucporiginal-images').length){
            $('.media-frame-router .media-router').append('<a href="#" class="media-menu-item ucp-ucporiginal-images">Original UCP images</a>');
          } else {
            if(mediaUploader.content._mode == 'ucporiginal'){
              ucp_get_ucporiginal_images(1);
            }
          }

          if($('.media-toolbar .ucp-media-button-select').length){
            $('.media-toolbar .ucp-media-button-select').remove();
          }

          $('.media-button-select').after('<button type="button" disabled="disabled" ' + ( mediaUploader.content._mode == 'unsplash' || mediaUploader.content._mode == 'ucporiginal' ? '':' style="display:none" ' )+' class="button button-primary button-large media-button ucp-media-button-select" data-id="'+image_input_id+'">Choose Image</button>');

        });

        mediaUploader.on('select', function() {
          var attachment = mediaUploader.state().get('selection').first().toJSON();
          $this.parent().children('.ucp_image').val(attachment.url);
          $this.parent().children('.ucp_image').trigger('change');
        });

        mediaUploader.on('open', function() {
          $('.media-menu-item').removeClass('active');
          $('.ucp-unsplash-images').addClass('active');
          mediaUploader.content._mode='unsplash';
          //mediaUploader.router.view=[];
          $('.media-button-select').hide();
          $('.ucp-media-button-select').show();
          $('.media-modal-content .media-frame-content').html('<div class="unsplash_head"><button disabled="disabled" id="unsplash_search_btn" class="button button-primary">Search</button><input type="text" id="unsplash_search" placeholder="Search unsplash images..." /></div><div class="unsplash-browser"><div class="ucp-loader"><i class="fa fa-spinner fa-pulse fa-3x fa-fw margin-bottom"></i> Loading images ... </div> </div>');

          ucp_get_unsplash_images();
        });
        mediaUploader.open();
      });


      $('body').on('click','.media-frame-router .media-router .media-menu-item',function(){
        if($(this).hasClass('ucp-unsplash-images')){
          $('.media-menu-item').removeClass('active');
          $(this).addClass('active');
          mediaUploader.content._mode='unsplash';
          //mediaUploader.router.view=[];
          $('.media-button-select').hide();
          $('.ucp-media-button-select').show();
          $('.media-modal-content .media-frame-content').html('<div class="unsplash_head"><button disabled="disabled" id="unsplash_search_btn" class="button button-primary">Search</button><input type="text" id="unsplash_search" placeholder="Search unsplash images..." /></div><div class="unsplash-browser"><div class="ucp-loader"><i class="fa fa-spinner fa-pulse fa-3x fa-fw margin-bottom"></i> Loading images ... </div> </div>');

          ucp_get_unsplash_images();

        } else if($(this).hasClass('ucp-ucporiginal-images')){
          $('.media-menu-item').removeClass('active');
          $(this).addClass('active');
          mediaUploader.content._mode='ucporiginal';
          //mediaUploader.router.view=[];
          $('.media-button-select').hide();
          $('.ucp-media-button-select').show();
          $('.media-modal-content .media-frame-content').html('<div class="ucporiginal-browser"><div class="ucp-loader"><i class="fa fa-spinner fa-pulse fa-3x fa-fw margin-bottom"></i> Loading images ... </div> </div>');

          ucp_get_ucporiginal_images();

        } else {
          $('.media-button-select').show();
          $('.ucp-media-button-select').hide();
        }
      });

      $('body').on('keyup change','#unsplash_search',function(e){
       if($(this).val().length==0 || $(this).val().length>2){
          $('#unsplash_search_btn').removeAttr('disabled');
          if(e.which == 13){
            ucp_execute_search();
          }
        } else {
          $('#unsplash_search_btn').attr('disabled','disabled');
        }
      });

      $('body').on('click','#unsplash_search_btn',function(){
        ucp_execute_search();
      });

      function ucp_execute_search(){
        if($('#unsplash_search').val().length==0 || $('#unsplash_search').val().length>2){
          $('.unsplash-browser').html('<div class="ucp-loader"><i class="fa fa-spinner fa-pulse fa-3x fa-fw margin-bottom"></i> Searching images ... </div> ');
          unsplash_search_query=$('#unsplash_search').val();
          unsplash_page=1;
          ucp_get_unsplash_images();
        } else {
          $('#unsplash_search_btn').attr('disabed','disabled');
        }
      }

      function ucp_get_unsplash_images(){
        $.ajax({
          url: ajaxurl,
          method: 'POST',
          crossDomain: true,
          dataType: 'json',
          timeout: 30000,
          data: {
            action:'ucp_editor_unsplash_api',
            page:unsplash_page,
            per_page:40,
            search:unsplash_search_query,
          }
        }).success(function(response) {
          var unsplash_images='';
          var unsplash_html = '';
          if(response.success){
            if(response.data.results){
              unsplash_images = JSON.parse(response.data.results);
              total_results = response.data.total_results;
              total_pages = response.data.total_pages;

              for(i in unsplash_images){
                unsplash_html += '<div class="ucp-unsplash-image" data-id="'+unsplash_images[i]['id']+'" data-url="'+unsplash_images[i]['full']+'" data-name="'+unsplash_images[i]['name']+'">';
                unsplash_html += '<img src="'+unsplash_images[i]['thumb']+'">';
                unsplash_html += unsplash_images[i]['user'];
                unsplash_html += '</div>';
              }
             }


             unsplash_html+='<div class="ucp_unsplash_pagination">';

             if(total_pages>1){
                unsplash_html+=total_results+' results';
             }

             if(unsplash_page>1){
                unsplash_html+='<div id="ucp_unsplash_prev">previous</div>';
             }
             if(!total_pages || unsplash_page<total_pages){
                unsplash_html+='<div id="ucp_unsplash_next">next</div>';
             }
             unsplash_html+='</div>';
             $('.unsplash-browser').html(unsplash_html);
           } else {
             $('.unsplash-browser').html('<div class="ucp-loader">An error occured contacting the Unsplash API.<br /><span class="ucp-unsplash-retry">Click here to try again.</span></div>');
           }
        }).error(function(type) {
           $('.unsplash-browser').html('<div class="ucp-loader">An error occured contacting the Unsplash API.<br /><span class="ucp-unsplash-retry">Click here to try again.</span></div>');
        });
      }

      function ucp_get_ucporiginal_images(){
        var ucporiginalimages = ['ambulance','cyber_chick','cyber_chick_dark','forklift','hot_air_baloon','iot','laptop','light_bulb_off','light_bulb_on','lighthouse','loader','mad-designer','people','people_2','puzzles','rocket','rocket2','safe','setup','stop','under_construction','under_construction_text','windmill','bagger-catepillar','clouds','cone','dump-truck','excavator','mixer','mobile-crane','road-sign','steamroller','temporarily_closed','tractor-loader'];

        ucporiginal_html = '';
        for(i in ucporiginalimages){
          ucporiginal_html += '<div class="ucp-ucporiginal-image" data-url="'+ucp_admin_editor_variables.ucp_plugin_url+'/images/original/'+ucporiginalimages[i]+'.png">';
          ucporiginal_html += '<img src="'+ucp_admin_editor_variables.ucp_plugin_url+'/images/original/'+ucporiginalimages[i]+'.png">';
          ucporiginal_html += '</div>';
        }
        $('.ucporiginal-browser').html(ucporiginal_html);
      }

      $('body').on('click','.ucp-unsplash-retry',function(){
        $('.unsplash-browser').html('<div class="ucp-loader"><i class="fa fa-spinner fa-pulse fa-3x fa-fw margin-bottom"></i> Loading images ... </div> ');
        ucp_get_unsplash_images();
      });

      $('body').on('click','#ucp_unsplash_prev',function(){
        $('.unsplash-browser').html('<div class="ucp-loader"><i class="fa fa-spinner fa-pulse fa-3x fa-fw margin-bottom"></i> Loading images ... </div> ');
        unsplash_page--;
        ucp_get_unsplash_images();
      });

      $('body').on('click','#ucp_unsplash_next',function(){
        $('.unsplash-browser').html('<div class="ucp-loader"><i class="fa fa-spinner fa-pulse fa-3x fa-fw margin-bottom"></i> Loading images ... </div> ');
        unsplash_page++;
        ucp_get_unsplash_images();
      });

      $('body').on('click','.ucp-unsplash-image',function(){
        $('.ucp-unsplash-image').removeClass('ucp-unsplash-image-selected');
        $(this).addClass('ucp-unsplash-image-selected');
        $('.ucp-media-button-select').removeAttr('disabled');
      });

      $('body').on('click','.ucp-ucporiginal-image',function(){
        $('.ucp-ucporiginal-image').removeClass('ucp-ucporiginal-image-selected');
        $(this).addClass('ucp-ucporiginal-image-selected');
        $('.ucp-media-button-select').removeAttr('disabled');
      });

      $('body').on('click','.ucp-media-button-select',function(){
        $('.ucp-media-button-select').attr('disabled','disabled');
        if(mediaUploader.content._mode == 'unsplash'){
          var ucp_unsplash_id = '';
          var image_input_id = $(this).data('id');
          $('.ucp-unsplash-image-selected').each(function(){
            ucp_unsplash_id = $(this).data('id');
            ucp_unsplash_url = $(this).data('url');
            ucp_unsplash_name = $(this).data('name');
          });

          if(ucp_unsplash_id != ''){
             $('.media-modal-content .media-frame-content').html('<div class="unsplash-browser"><div class="ucp-loader"><i class="fa fa-spinner fa-pulse fa-3x fa-fw margin-bottom"></i> Downloading images ... </div> </div>');
             $.ajax({
                url: ajaxurl,
                method: 'POST',
                crossDomain: true,
                dataType: 'json',
                timeout: 30000,
                data: {
                  action:'ucp_editor_unsplash_download',
                  image_id:ucp_unsplash_id,
                  image_url:ucp_unsplash_url,
                  image_name:ucp_unsplash_name,
                  image_large:unsplash_image_large,
                }
              }).success(function(response) {
                if(response.success){
                  if(response.data){
                    $('#'+image_input_id).val(response.data);
                    $('#'+image_input_id).trigger('change');
                    mediaUploader.close();
                  }
                } else {
                  $('.unsplash-browser').html(response.data);
                  var message = 'An error occured downloading the image.';
                  if(response.data){
                    message = response.data;
                  }
                  $('.unsplash-browser').html('<div class="ucp-loader">'+message+'<br /><span class="ucp-unsplash-retry">Click here to return to browsing.</span></div>');
                }
              }).error(function(type) {
                $('.unsplash-browser').html('<div class="ucp-loader">An error occured downloading the image.<br /><span class="ucp-unsplash-retry">Click here to return to browsing.</span></div>');
              }).always(function(type){
                $('.ucp-media-button-select').removeAttr('disabled');
              });
            }
         } else if(mediaUploader.content._mode == 'ucporiginal'){
           $('.ucp-ucporiginal-image-selected').each(function(){
              ucp_ucporiginal_url = $(this).data('url');
           });
           var image_input_id = $(this).data('id');
           $('#'+image_input_id).val(ucp_ucporiginal_url);
           $('#'+image_input_id).trigger('change');
           mediaUploader.close();
         }
      });

//Page CSS/JS Read/Write
      function generate_page_css(page){
         var page_css = 'html body{';
         var page_background_type = $('#ucp_page_properties .ucp_editor_background').attr('data-background-type');
         switch(page_background_type){
          case 'transparent':
          page_css += 'background:transparent;';
          break;
          case 'color':
          page_css += 'background:'+$('#ucp_page_properties .background_color').val()+';';
          break;
          case 'gradient':
          var background_color_a = $('#ucp_page_properties .background_color_a').val();
          var background_color_b = $('#ucp_page_properties .background_color_b').val();
          var background_orientation = $('#ucp_page_properties .background_orientation').val();
            if(background_orientation == 'horizontal'){
              page_css += 'background: '+background_color_a+';';
              page_css += 'background: -moz-linear-gradient(left, '+background_color_a+' 0%, '+background_color_b+' 100%);';
              page_css += 'background: -webkit-linear-gradient(left, '+background_color_a+' 0%,'+background_color_b+' 100%);';
              page_css += 'background: linear-gradient(to right, '+background_color_a+' 0%,'+background_color_b+' 100%);';
            }
            if(background_orientation == 'vertical'){
              page_css += 'background: '+background_color_a+';';
              page_css += 'background: -moz-linear-gradient(top, '+background_color_a+' 0%, '+background_color_b+' 100%);';
              page_css += 'background: -webkit-linear-gradient(top, '+background_color_a+' 0%,'+background_color_b+' 100%);';
              page_css += 'background: linear-gradient(to bottom, '+background_color_a+' 0%,'+background_color_b+' 100%);';
            }
            if(background_orientation == 'radial'){
              page_css += 'background: '+background_color_a+';';
              page_css += 'background: -moz-radial-gradient(center, ellipse cover, '+background_color_a+' 0%, '+background_color_b+' 100%);';
              page_css += 'background: -webkit-radial-gradient(center, ellipse cover, '+background_color_a+' 0%,'+background_color_b+' 100%);';
              page_css += 'background: radial-gradient(ellipse at center, '+background_color_a+' 0%,'+background_color_b+' 100%);';
            }
          break;
          case 'image':
            var background_image_url = $('#ucp_page_properties .background_image').val();
            var background_image_size = $('#ucp_page_properties .background_size').val();
            var background_image_repeat = $('#ucp_page_properties .background_repeat').val();
            page_css += 'background: url(\''+background_image_url+'\'); background-size:'+background_image_size+'; background-repeat:'+background_image_repeat+';';
          break;
          case 'video':
            if(page){
              var background_video_fallback_src = $('#ucp_page_properties .background_video_fallback').val();

              var background_video_link = $('#ucp_page_properties .background_video').val();

              if(background_video_link.indexOf('vimeo') > 0){
                var background_video_url = $('#ucp_page_properties .background_video').val().replace('https://vimeo.com/','').replace('https://player.vimeo.com/video/','');

                ucp_iframe.jQuery('.video-background').remove();
                ucp_iframe.jQuery('#ucp-animated-background').remove();
                ucp_iframe.jQuery('#ucp_template_animation_js').remove();
                ucp_iframe.jQuery('#ucp-template').prepend('<div class="video-background" style="background-image:url('+background_video_fallback_src+');background-size: cover;background-position: center;"><div class="video-foreground"><iframe allow="autoplay; fullscreen" src="https://player.vimeo.com/video/'+background_video_url+'?autoplay=1&loop=1&background=1&autopause=0&controls=0&muted=1&byline=0&title=0" style="position:absolute;top:0;left:0;width:100%;height:100%;" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe></div><script src="https://player.vimeo.com/api/player.js"></script></div>');
             } else {
                var background_video_url = $('#ucp_page_properties .background_video').val().replace('https://www.youtube.com/watch?v=','').replace('https://www.youtube.com/embed/','').replace('https://youtu.be/','');

                ucp_iframe.jQuery('.video-background').remove();
                ucp_iframe.jQuery('#ucp-animated-background').remove();
                ucp_iframe.jQuery('#ucp_template_animation_js').remove();
                ucp_iframe.jQuery('#ucp-template').prepend('<div class="video-background" style="background-image:url('+background_video_fallback_src+');background-size: cover;background-position: center;"><div class="video-foreground"><iframe src="https://www.youtube.com/embed/'+background_video_url+'?controls=0&mute=1&showinfo=0&rel=0&autoplay=1&loop=1&playlist='+background_video_url+'" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe></div></div>');
              }

            }
          break;
          case 'animated':
            if(typeof ucp_iframe.pJS === 'undefined'){
              particlesJS = ucp_iframe.document.createElement('script');
              particlesJS.onload = particlesJS.onreadystatechange = function () {
                  if ((particlesJS.readyState && particlesJS.readyState !== 'complete' && particlesJS.readyState !== 'loaded') || particlesJSLoaded) {
                      return false;
                  }
                  particlesJS.onload = particlesJS.onreadystatechange = null;
                  particlesJSLoaded = true;
                  ucp_setup_page_animated_background();
              };

              particlesJS.src = ucp_admin_editor_variables.ucp_plugin_url+'/js/particles.min.js';
              ucp_iframe.document.body.appendChild(particlesJS);
            } else {
              ucp_setup_page_animated_background();
            }
          break;
        }

        page_css +='}';

        return page_css;
      }

      function ucp_setup_page_animated_background(){

         ucp_iframe.jQuery('.video-background').remove();
         color_b=$('#ucp_page_properties .animation_color_b').val();
         if(color_b.indexOf('#') == -1) color_b = rgb2hex(color_b);


         /* if particles is not loaded */
         if(!ucp_iframe.jQuery('#ucp-animated-background').length){
            ucp_iframe.jQuery('#ucp-template').prepend('<div id="ucp-animated-background" style="background:'+$('#ucp_page_properties .animation_color_a').val()+';"></div>');
            ucp_iframe.jQuery('<script/>', {id: 'ucp_template_animation_js_init', text: 'particlesJS("ucp-animated-background",{"interactivity":{"detect_on":"window","events":{"onhover":{"enable":true,"mode":"repulse"},"onclick":{"enable":true,"mode":"push"},"resize":true},"modes":{"grab":{"distance":400,"line_linked":{"opacity":1}},"bubble":{"distance":400,"size":40,"duration":2,"opacity":8,"speed":3},"repulse":{"distance":200,"duration":0.4},"push":{"particles_nb":4},"remove":{"particles_nb":2}}},"retina_detect":true});'}).appendTo('#ucp-template');
         }

         var particles_JS='';

         ucp_iframe.jQuery('#ucp-animated-background').css('background',$('#ucp_page_properties .animation_color_a').val());
         particles_JS+='window.pJSDom[0].pJS.particles.color.value="'+color_b+'";';
         particles_JS+='window.pJSDom[0].pJS.particles.line_linked.color="'+color_b+'";';


         var particle_animation = $('#ucp_page_properties .background_animation').val();
         switch(particle_animation){
           case 'particles':
             particles_JS+='window.pJSDom[0].pJS.particles.number.value=120;';
             particles_JS+='window.pJSDom[0].pJS.particles.opacity.random=false;';
             particles_JS+='window.pJSDom[0].pJS.particles.shape.type="circle";';
             particles_JS+='window.pJSDom[0].pJS.particles.opacity.value=0.5;';
             particles_JS+='window.pJSDom[0].pJS.particles.size.random=true;';
             particles_JS+='window.pJSDom[0].pJS.particles.size.anim.enable=false;';
             particles_JS+='window.pJSDom[0].pJS.particles.size.anim.speed=40;';
             particles_JS+='window.pJSDom[0].pJS.particles.size.anim.size_min=0.1;';
             particles_JS+='window.pJSDom[0].pJS.interactivity.detect_on="window";';
             particles_JS+='window.pJSDom[0].pJS.interactivity.events.onhover.enable=true;';
             particles_JS+='window.pJSDom[0].pJS.interactivity.events.onclick.enable=true;';
             particles_JS+='window.pJSDom[0].pJS.interactivity.modes.repulse.distance=100;';
             particles_JS+='window.pJSDom[0].pJS.tmp.obj.size_value=3;';
             particles_JS+='window.pJSDom[0].pJS.particles.line_linked.enable=true;';
             particles_JS+='window.pJSDom[0].pJS.particles.move.direction="none";';
             break;
           case 'circles':
             particles_JS+='window.pJSDom[0].pJS.tmp.obj.size_value=160;';
             particles_JS+='window.pJSDom[0].pJS.particles.shape.type="circle";';
             particles_JS+='window.pJSDom[0].pJS.particles.number.value=6;';
             particles_JS+='window.pJSDom[0].pJS.particles.opacity.value=0.3;';
             particles_JS+='window.pJSDom[0].pJS.particles.line_linked.enable=false;';
             particles_JS+='window.pJSDom[0].pJS.interactivity.events.onhover.enable=false;';
             particles_JS+='window.pJSDom[0].pJS.interactivity.events.onclick.enable=false;';
             particles_JS+='window.pJSDom[0].pJS.particles.move.direction="none";';
             break;
           case 'squares':
             particles_JS+='window.pJSDom[0].pJS.tmp.obj.size_value=120;';
             particles_JS+='window.pJSDom[0].pJS.particles.shape.type="polygon";';
             particles_JS+='window.pJSDom[0].pJS.particles.shape.polygon.nb_sides=4;';
             particles_JS+='window.pJSDom[0].pJS.particles.number.value=6;';
             particles_JS+='window.pJSDom[0].pJS.particles.opacity.value=0.3;';
             particles_JS+='window.pJSDom[0].pJS.particles.line_linked.enable=false;';
             particles_JS+='window.pJSDom[0].pJS.interactivity.events.onhover.enable=false;';
             particles_JS+='window.pJSDom[0].pJS.interactivity.events.onclick.enable=false;';
             particles_JS+='window.pJSDom[0].pJS.particles.move.direction="none";';
             break;
           case 'snow':
             particles_JS+='window.pJSDom[0].pJS.tmp.obj.size_value=10;';
             particles_JS+='window.pJSDom[0].pJS.particles.shape.type="circle";';
             particles_JS+='window.pJSDom[0].pJS.particles.number.value=400;';
             particles_JS+='window.pJSDom[0].pJS.particles.opacity.value=0.5;';
             particles_JS+='window.pJSDom[0].pJS.particles.line_linked.enable=false;';
             particles_JS+='window.pJSDom[0].pJS.interactivity.events.onhover.enable=false;';
             particles_JS+='window.pJSDom[0].pJS.interactivity.events.onclick.enable=false;';
             particles_JS+='window.pJSDom[0].pJS.particles.move.direction="bottom";';
             break;
         }

         particles_JS+='window.pJSDom[0].pJS.fn.particlesRefresh();';
         ucp_iframe.jQuery('#ucp_template_animation_js').remove();
         ucp_iframe.jQuery('<script/>', {id: 'ucp_template_animation_js', text: particles_JS}).appendTo('#ucp-template');
      }


      function ucp_get_css_background(module_id){
        //check if gradient is radial
        if(module_id == 'body'){
          selector = 'body';
        } else {
          selector = '[data-module-id="'+module_id+'"]';
        }

        var module_css_id = 'ucp-'+module_id;
        var background = {};

        background.background_type = 'transparent';
        background.background_orientation = 'linear';
        if(selector == 'body' && ucp_iframe.jQuery('.video-background').length){
          background.background_video = ucp_iframe.jQuery('.video-background .video-foreground iframe').attr('src').split('?')[0];
          background.background_video_fallback = ucp_iframe.jQuery('.video-background').css('background-image').replace('url(','').replace(')','').replace(/\"/gi, "");;
          background.background_type = 'video';
        } else if(selector == 'body' && ucp_iframe.jQuery('#ucp-animated-background').length){
          if(ucp_iframe.window.pJSDom[0].pJS.tmp.obj.size_value == 3){
            background.background_animation = 'particles';
          }
          if(ucp_iframe.window.pJSDom[0].pJS.tmp.obj.size_value == 10){
            background.background_animation = 'snow';
          }
          if(ucp_iframe.window.pJSDom[0].pJS.tmp.obj.size_value == 120){
            background.background_animation = 'squares';
          }
          if(ucp_iframe.window.pJSDom[0].pJS.tmp.obj.size_value == 160){
            background.background_animation = 'circles';
          }
          background.animation_color_a=ucp_iframe.jQuery('#ucp-animated-background').css('background');
          background.animation_color_b=ucp_iframe.window.pJSDom[0].pJS.particles.color.value;
          background.background_type = 'animated';
        } else {

          var background_image_read = ucp_iframe.jQuery(selector).css('background-image');
          background.background_size = ucp_iframe.jQuery(selector).css('background-size');
          background.background_repeat = ucp_iframe.jQuery(selector).css('background-repeat');

          if(typeof background_image_read !== 'undefined'){

            if(background_image_read.indexOf('url') > -1){
              background.background_image = background_image_read.replace(/ /g,'').replace(/"/g,'').replace(/'/g,'').replace('url(','').replace(')','');
              background.background_type = 'image';

              if(selector != 'body'){
                css_styles[module_css_id]['modulecss']['background-image'] = 'url('+background.background_image+')';
                css_styles[module_css_id]['modulecss']['background-size'] = background.background_size;
                css_styles[module_css_id]['modulecss']['background-repeat'] = background.background_repeat;
              }
            }

            if(background_image_read.indexOf('radial-gradient') == 0){
              background.background_type = 'gradient';
              background.background_orientation = 'radial';
            }

            if(background_image_read.indexOf('linear-gradient') == 0){
              background.background_type = 'gradient';
              if(background_image_read.indexOf('to right') > 0){
                background.background_orientation = 'horizontal';
              } else {
                background.background_orientation = 'vertical';
              }
            }
          } // background_image_read ! undefined
        }

        if(background.background_type == 'gradient'){
           var gradient_color_array = background_image_read.split('rgb');
           color_a_raw = gradient_color_array[1].replace(/ /g,'').split(')')[0].replace('a(','').replace('(','').split(',');
           color_b_raw = gradient_color_array[2].replace(/ /g,'').split(')')[0].replace('a(','').replace('(','').split(',');
           if(color_a_raw.length == 3) color_a_raw[3]=1;
           if(color_b_raw.length == 3) color_b_raw[3]=1;
           background.background_color_a = 'rgba('+color_a_raw.join(',')+')';
           background.background_color_b = 'rgba('+color_b_raw.join(',')+')';


            if(selector != 'body'){
              if(background.background_orientation == 'horizontal'){
                css_styles[module_css_id]['modulecss']['background|1'] = background.background_color_a;
                css_styles[module_css_id]['modulecss']['background|2'] = '-moz-linear-gradient(left, '+background.background_color_a+' 0%, '+background.background_color_b+' 100%)';
                css_styles[module_css_id]['modulecss']['background|3'] = '-webkit-linear-gradient(left, '+background.background_color_a+' 0%,'+background.background_color_b+' 100%)';
                css_styles[module_css_id]['modulecss']['background|4'] = 'linear-gradient(to right, '+background.background_color_a+' 0%,'+background.background_color_b+' 100%)';
              }

              if(background.background_orientation == 'vertical'){
                css_styles[module_css_id]['modulecss']['background|1'] = background.background_color_a;
                css_styles[module_css_id]['modulecss']['background|2'] = '-moz-linear-gradient(top, '+background.background_color_a+' 0%, '+background.background_color_b+' 100%)';
                css_styles[module_css_id]['modulecss']['background|3'] = '-webkit-linear-gradient(top, '+background.background_color_a+' 0%,'+background.background_color_b+' 100%)';
                css_styles[module_css_id]['modulecss']['background|4'] = 'linear-gradient(to bottom, '+background.background_color_a+' 0%,'+background.background_color_b+' 100%)';
              }


              if(background.background_orientation == 'radial'){
                css_styles[module_css_id]['modulecss']['background|1'] = background.background_color_a;
                css_styles[module_css_id]['modulecss']['background|2'] = '-moz-radial-gradient(center, ellipse cover, '+background.background_color_a+' 0%, '+background.background_color_b+' 100%)';
                css_styles[module_css_id]['modulecss']['background|3'] = '-webkit-radial-gradient(center, ellipse cover, '+background.background_color_a+' 0%,'+background.background_color_b+' 100%)';
                css_styles[module_css_id]['modulecss']['background|4'] = 'radial-gradient(ellipse at center, '+background.background_color_a+' 0%,'+background.background_color_b+' 100%)';
              }
            }
        }

        if(background.background_type == 'transparent'){
          var background_color_read = ucp_iframe.jQuery(selector).css('background-color');

          if(typeof background_color_read !== 'undefined' && background_color_read.length > 0 && background_color_read != 'rgba(0, 0, 0, 0)'){
             background.background_type = 'color';
             background.background_color = background_color_read;
             if(selector != 'body'){
               css_styles[module_css_id]['modulecss']['background-color'] = background.background_color;
             }
          }
        }

        return background;
        //if(ucp_iframe.jQuery(selector).css('background')
      }

      function ucp_read_page_css(){
        page_background = ucp_get_css_background('body');

        $('#ucp-template-custom-css').val( ucp_iframe.jQuery('#ucp_template_custom_style').html() );

        if(ucp_iframe.jQuery('#ucp_template_footer_js').length){
          $('#ucp-template-footer-code').val( ucp_iframe.jQuery('#ucp_template_footer_js').html().replace(/ucp_script_disabled/ig,'script') );
        }

        $('#ucp_page_properties .ucp_editor_background').attr('data-background-type',page_background.background_type);
        $('#ucp_page_properties .ucp_editor_background_styles_wrapper [data-background-type="'+page_background.background_type+'"]').addClass('ucp_editor_background_style_selected');
        generate_background_options_html('#ucp_page_properties',page_background);
        $('.ucp-tooltip').tooltipster({
               animation: 'fade',
               delay: 0,
            });
      }

      function rebuild_iframe_css(page){

         window.ucp_save_confirm = true;
         var css_styles_html = '';
         // Page Styles
         css_styles_html += generate_page_css(page);

         // Module Styles
         for( moduleid in css_styles ){
           if(css_styles[moduleid]['modulecss']['full-width'] == true){
             css_styles[moduleid]['modulecss']['padding-left']='1500px';
             css_styles[moduleid]['modulecss']['padding-right']='1500px';
             css_styles[moduleid]['modulecss']['margin-left']='-1500px';
             css_styles[moduleid]['modulecss']['margin-right']='-1500px';
             css_styles[moduleid]['modulecss']['box-sizing'] = 'content-box';
           } else if( css_styles[moduleid]['modulecss']['padding-left'] == '1500px' ){
             css_styles[moduleid]['modulecss']['padding-left']='0px';
             css_styles[moduleid]['modulecss']['padding-right']='0px';
             css_styles[moduleid]['modulecss']['margin-left']='0px';
             css_styles[moduleid]['modulecss']['margin-right']='0px';
             css_styles[moduleid]['modulecss']['box-sizing'] = 'border-box';
           }

           for( eclass in css_styles[moduleid] ){
             if(eclass=='modulecss'){
               css_styles_html += '#'+moduleid+'{';
               for(attr in css_styles[moduleid][eclass]){
                 if(attr != 'full-width'){
                   if( ucp_iframe.jQuery('#'+moduleid).data('module-type') == 'image' && ( attr == 'border-color' || attr == 'border-style' || attr == 'border-width' ) ){
                   } else {
                     css_styles_html += attr.split('|')[0]+':'+css_styles[moduleid][eclass][attr]+';';
                   }
                 }
               }
               css_styles_html += '}';

               if( ucp_iframe.jQuery('#'+moduleid).data('module-type') == 'image'){
                 css_styles_html += '#'+moduleid+' img{';
                 for(attr in css_styles[moduleid][eclass]){
                   if(attr == 'border-color' || attr == 'border-style' || attr == 'border-width'){
                     css_styles_html += attr.split('|')[0]+':'+css_styles[moduleid][eclass][attr]+';';
                   }
                 }
                 css_styles_html += '}';
               }

             } else {
               css_styles_html += '#'+moduleid+' .'+eclass+',#'+moduleid+' .'+eclass+' *{';
               for(attr in css_styles[moduleid][eclass]){
                 if( attr == 'font-family' ){
                   css_styles_html += attr+':\''+css_styles[moduleid][eclass][attr]+'\';';
                 } else {
                   css_styles_html += attr+':'+css_styles[moduleid][eclass][attr]+';';
                 }
               }
               css_styles_html += '}';

               if(eclass == 'button-large'){
                 css_styles_html += '#'+moduleid+' .'+eclass+':hover{';
                 css_styles_html += 'color:'+ucp_iframe.jQuery('#'+moduleid+' .ucp-element').attr('data-hover-text-color')+';';
                 css_styles_html += 'background-color:'+ucp_iframe.jQuery('#'+moduleid+' .ucp-element').attr('data-hover-background-color')+';';
                 css_styles_html += '}';
               }

               if( eclass.indexOf('input')>=0 && css_styles[moduleid][eclass]['color'] ){
                 css_styles_html += '#'+moduleid+' .'+eclass+'::-webkit-input-placeholder{';
                   css_styles_html += 'color:'+css_styles[moduleid][eclass]['color']+'; opacity:0.5;';
                 css_styles_html += '}';

                 css_styles_html += '#'+moduleid+' .'+eclass+':-moz-placeholder{';
                   css_styles_html += 'color:'+css_styles[moduleid][eclass]['color']+'; opacity:0.5;';
                 css_styles_html += '}';

                 css_styles_html += '#'+moduleid+' .'+eclass+'::-moz-placeholder{';
                   css_styles_html += 'color:'+css_styles[moduleid][eclass]['color']+'; opacity:0.5;';
                 css_styles_html += '}';

                 css_styles_html += '#'+moduleid+' .'+eclass+':-ms-input-placeholder{';
                   css_styles_html += 'color:'+css_styles[moduleid][eclass]['color']+'; opacity:0.5;';
                 css_styles_html += '}';
               }

               if(eclass == 'socialicons'){
                 css_styles_html += '#'+moduleid+' .'+eclass+' a{';
                   css_styles_html += 'color:'+css_styles[moduleid][eclass]['color']+';';
                 css_styles_html += '}';
               }
             }
           }
         }

         ucp_iframe.jQuery('#ucp_template_style').html(css_styles_html);
         if(!ucp_iframe.jQuery('#ucp-template #ucp_template_custom_style').length){
           ucp_iframe.jQuery('#ucp-template').append('<style id="ucp_template_custom_style"></style>');
         }

         if(!ucp_iframe.jQuery('#ucp-template #ucp_template_footer_js').length){
           ucp_iframe.jQuery('#ucp-template').append('<div id="ucp_template_footer_js"></div>');
         }

         ucp_iframe.jQuery('#ucp_template_custom_style').html( $('#ucp-template-custom-css').val() );

         ucp_iframe.jQuery('#ucp_template_footer_js').html( $('#ucp-template-footer-code').val().replace('<script','<ucp_script_disabled').replace('</script','</ucp_script_disabled'));

      }

//Fonts

      function get_google_font_variants(font){
        return_font_variants=[];

        if(typeof ucp_admin_editor_variables.ucp_google_fonts[get_google_font_slug(font)] !== 'undefined'){
          var font_variants = ucp_admin_editor_variables.ucp_google_fonts[get_google_font_slug(font)].variants.split(',');
          var font_has_bold = false;
          for(variant in font_variants){
             if( font_variants[variant].indexOf('italic') == -1 ){
               if( font_variants[variant] > 500 ){
                 font_has_bold = true;
               }
               if( font_variants[variant] == 'regular'){
                 return_font_variants.push('normal');
               } else {
                 return_font_variants.push(font_variants[variant]);
               }
             }
          }
          if(font_has_bold == false){
            return_font_variants.push('bold');
          }
        } else {
          return_font_variants.push('normal');
          return_font_variants.push('bold');
        }

        return return_font_variants;
      }

      function refresh_google_fonts(){
        var css_google_fonts = [];
        for(module in css_styles){
          for(element_class in css_styles[module]){
              for(attr in css_styles[module][element_class]){
                if(attr == 'font-family'){
                  google_font_slug = css_styles[module][element_class][attr].replace(/ /g,'+').trim();
                  if(typeof css_google_fonts[google_font_slug] === 'undefined' && google_font_slug != 'Helvetica+Neue'){
                    css_google_fonts[google_font_slug]=[];
                  }


                  if( typeof css_styles[module][element_class]['font-weight'] !== 'undefined' && $.inArray(css_styles[module][element_class]['font-weight'], css_google_fonts[google_font_slug] )){
                    css_google_fonts[google_font_slug].push(css_styles[module][element_class]['font-weight']);
                  }
                }
             }
          }
        }

        var google_font_string = '';
        for(font in css_google_fonts){
          google_font_string+=font;
          if(css_google_fonts[font].length > 0){
            google_font_string+=':'+css_google_fonts[font].join(',');
          }
          google_font_string+='|';
        }
        google_font_string = google_font_string.substring(0, google_font_string.length - 1);

        ucp_iframe.jQuery('#ucp-google-fonts-loader').remove();
        ucp_iframe.jQuery('<link/>', { rel: 'stylesheet', id:'ucp-google-fonts-loader', href: 'https://fonts.googleapis.com/css?family='+google_font_string }).appendTo('#ucp-template');
      }

      function get_font_variant(element_id){
         var current_font_weight = ucp_iframe.jQuery('[data-element-id="' + element_id +'"]').children().last().css("font-weight");
         var current_font_style = ucp_iframe.jQuery('[data-element-id="' + element_id +'"]').children().last().css("font-weight");
         if(current_font_weight == '400') current_font_weight = 'normal';

         if(current_font_style == 'italic'){
           if(current_font_weight == 'bold'){
             return 'bold italic';
           } else if(current_font_weight == 'regular'){
             return 'italic';
           } else {
             return current_font_weight+'italic';
           }
         } else {
           return current_font_weight;
         }
      }

//General Page Controls

      function generate_background_options_html(apply_to,background_options){


        var background_options_html='';
        var background_sizes = ['auto','cover','contain'];
        var background_repeats = ['repeat','repeat-x','repeat-y','no-repeat'];
        switch(background_options.background_type){
          case 'transparent':
          break;
          case 'color':
          background_options_html+='<div class="ucp_editor_background_options_container">';
          background_options_html+='<input type="text" name="background_color" data-apply="css" data-property="background-color" class="background_color sm_color_picker" value="'+background_options.background_color+'" />';
          background_options_html+='</div>';
          break;
          case 'gradient':
          background_options_html+='<div class="ucp_editor_background_options_container">';
          background_options_html+='<input type="text" name="background_color_a" data-apply="gradient" class="background_color_a sm_color_picker" value="'+background_options.background_color_a+'" />';
          background_options_html+='<input type="text" name="background_color_b" data-apply="gradient" class="background_color_b sm_color_picker" value="'+background_options.background_color_b+'" />';
          background_options_html+='<select  name="background_orientation"  data-apply="gradient" class="background_orientation">';
              if(background_options.background_orientation == 'horizontal'){
                background_options_html+='<option value="horizontal" selected>horizontal</option>';
              } else {
                background_options_html+='<option value="horizontal">horizontal</option>';
              }

              if(background_options.background_orientation == 'vertical'){
                background_options_html+='<option value="vertical" selected>vertical</option>';
              } else {
                background_options_html+='<option value="vertical">vertical</option>';
              }

              if(background_options.background_orientation == 'radial'){
                background_options_html+='<option value="radial" selected>radial</option>';
              } else {
                background_options_html+='<option value="radial">radial</option>';
              }

          background_options_html+='</select>';
          background_options_html+='</div>';
          break;
          case 'image':
          background_options_html+='<div class="ucp_editor_background_options_container">';
          background_options_html+='<input type="text" name="background_image" id="background_image_'+apply_to.replace('#','')+'" data-apply="background-image" class="background_image ucp_image" value="'+background_options.background_image+'" /><div class="button ucp_image_upload ucp-image-large">Upload</div>';
          background_options_html+='<select  name="background_size"  data-apply="background-image" class="background_size">';
            for(size in background_sizes){

              if(background_options.background_size == background_sizes[size]){
                background_options_html+='<option value="'+background_sizes[size]+'" selected>'+background_sizes[size]+'</option>';
              } else {
                background_options_html+='<option value="'+background_sizes[size]+'">'+background_sizes[size]+'</option>';
              }

            }
          background_options_html+='</select>';

          background_options_html+='<select name="background_repeat" data-apply="background-image" class="background_repeat">';
            for(repeat in background_repeats){

              if(background_options.background_repeat == background_repeats[repeat]){
                background_options_html+='<option value="'+background_repeats[repeat]+'" selected>'+background_repeats[repeat]+'</option>';
              } else {
                background_options_html+='<option value="'+background_repeats[repeat]+'">'+background_repeats[repeat]+'</option>';
              }

            }
          background_options_html+='</select>';

          background_options_html+='</div>';
          break;
          case 'video':
            background_options_html+='<div class="ucp_editor_background_options_container"><span title="YouTube / Vimeo URL" class="ucp-tooltip">';

            if(background_options.background_video == ''){
              background_options.background_video = 'https://www.youtube.com/embed/W0LHTWG-UmQ';
              background_options.background_video_fallback = '';
            }

            if(typeof background_options.background_video_fallback === 'undefined'){
              background_options.background_video_fallback = ''; 
            }
            background_options_html+='<input type="text" name="background_video" data-apply="background" class="background_video" value="'+background_options.background_video+'" />';
            background_options_html+='Fallback image: <br /><input type="text" id="background_video_fallback" name="background_video_fallback" data-apply="background_fallback" class="background_video_fallback ucp_image" value="'+background_options.background_video_fallback+'" /><div class="button ucp_image_upload ucp-image-large">Upload</div';

            background_options_html+='</span></div>';
            break;
          case 'animated':
            background_options_html+='<div class="ucp_editor_background_options_container"><span title="Select Animation" class="ucp-tooltip">';

            var animations = ['particles','squares','circles','snow'];
            background_options_html+='<select  name="background_animation" data-apply="animation" class="background_animation">';
              for(animation in animations){
                if(background_options.background_animation == animations[animation]){
                background_options_html+='<option value="'+animations[animation]+'" selected>'+animations[animation]+'</option>';
                } else {
                  background_options_html+='<option value="'+animations[animation]+'">'+animations[animation]+'</option>';
                }
              }
            background_options_html+='</select></span>';
            background_options_html+='<span title="Animation background color" class="ucp-tooltip"><input type="text" name="animation_color_a" data-apply="animation" class="animation_color_a sm_color_picker" value="'+background_options.animation_color_a+'" /></span>';
            background_options_html+='<span title="Animation elements color" class="ucp-tooltip"><input type="text" name="animation_color_b" data-apply="animation" class="animation_color_b sm_color_picker" value="'+background_options.animation_color_b+'" /></span>';

            background_options_html+='</div>';
            break;
        }

        if(apply_to == 'page' || apply_to == '#ucp_page_properties'){
          $('#ucp_page_properties .ucp_editor_background_options').html(background_options_html);
        } else {
          $('[data-module-id="'+apply_to+'"] .ucp_editor_background_options').html(background_options_html);
          $('[data-module-id="'+apply_to+'"] [data-background-type="'+background_options.background_type+'"]').addClass('ucp_editor_background_style_selected');
        }

        ucp_refresh_color_picker();
      }


//Module/Element Controls

      function ucp_open_module_editor(module_id,editing_element_id){
          $('.sp-container').hide();
          
          ucp_iframe.jQuery('[data-module-id]' ).removeClass('ucp-module-editing');
          ucp_iframe.jQuery('[data-module-id="'+module_id+'"]' ).addClass('ucp-module-editing');


          var module_type = ucp_iframe.jQuery('[data-module-id="'+module_id+'"]' ).attr('data-module-type');

          if( $('.ucp_sidebar_edit_fields').hasClass('ui-accordion') ){
            $('.ucp_sidebar_edit_fields').accordion('destroy');
            $('.ucp_sidebar_edit_fields').empty();
          }

          var active_accordion_index=0;
          var active_accordion=0;
          var element_ids_array = [];


          $('.ucp-sidebar-header').html('<span class="ucp-sidebar-module-close"><i class="fa fa-arrow-left" aria-hidden="true"></i></span><span class="ucp-sidebar-module-title">'+module_names[module_type]+' module</span></span>');


          //Print module controls
          sidebar_html = '<h3 class="ucp-element-title">'+module_names[module_type]+' module</h3><div data-module-id="'+module_id+'" data-module-type="'+module_type+'">'
          sidebar_html += ucp_module_edit_options_html(module_id,module_type);
          sidebar_html += '<br /><div class="ucp-element-css-selector"><span>Module Selector:</span><br /> #ucp-'+module_id+'</div>';
          sidebar_html += '</div>';

          ucp_iframe.jQuery('[data-module-id="'+module_id+'"]').find('[data-element-type]').each(function(){
            //Print each element controls
            active_accordion_index++;
            if(editing_element_id == ucp_iframe.jQuery(this).attr('data-element-id')){
              active_accordion = active_accordion_index;
            }
            if(ucp_iframe.jQuery(this).attr('data-element-type') == 'text' || ucp_iframe.jQuery(this).attr('data-element-type') == 'heading_l'  || ucp_iframe.jQuery(this).attr('data-element-type') == 'heading_s'  || ucp_iframe.jQuery(this).attr('data-element-type') == 'social' || ucp_iframe.jQuery(this).attr('data-element-type') == 'input' || ucp_iframe.jQuery(this).attr('data-element-type') == 'textarea' || ucp_iframe.jQuery(this).attr('data-element-type') == 'submit' || ucp_iframe.jQuery(this).attr('data-element-type') == 'large_button' || ucp_iframe.jQuery(this).attr('data-element-type') == 'countdown_timer' || ucp_iframe.jQuery(this).attr('data-element-type') == 'divider'){
              element_ids_array.push( ucp_iframe.jQuery(this).attr('data-element-id') );
            }
            sidebar_html += '<h3 class="ucp-element-title">'+module_names[ucp_iframe.jQuery(this).attr('data-element-type')]+' element</h3>';
            sidebar_html += '<div data-element-id="'+ucp_iframe.jQuery(this).attr('data-element-id')+'" data-element-type="'+ucp_iframe.jQuery(this).attr('data-element-type')+'">';
            sidebar_html += ucp_element_edit_options_html(ucp_iframe.jQuery(this).attr('data-element-id'),ucp_iframe.jQuery(this).attr('data-element-type'));
            sidebar_html += '<br /><div class="ucp-element-css-selector"><span>Element Selector:</span><br /> #ucp-'+module_id+' .ucp-element .'+ucp_iframe.jQuery(this).children(':not(.ucp-element-controls)').first().attr('class')+' </div>';
            sidebar_html += '</div>';
          });


          $('.ucp_sidebar_edit_fields').html(sidebar_html);
          $('#ucp-style-sidebar').addClass('ucp_sidebar_editing');

          ucp_refresh_color_picker();

          $('#ucp-style-sidebar .ucp-wysiwyg').summernote({
            height: 200,
            toolbar: [
              ['style', ['bold', 'italic', 'underline', 'clear']],
              ['insert', ['link']],
              ['font', ['strikethrough', 'superscript', 'subscript']],
              ['para', ['ul', 'ol', 'paragraph']],
              ['height', ['height']],
              ['codeview', ['codeview']]
            ],
            callbacks: {
              onChange: function(contents, $editable) {
                $(this).val(contents);
                $(this).trigger('change');
              }
            }
          });

          module_background = ucp_get_css_background(module_id);
          generate_background_options_html(module_id,module_background);

          $('.ucp_sidebar_edit_fields').accordion({active:active_accordion,collapsible: true});

          $( "#ucp-slider-"+module_id ).slider({
            value: parseInt($( "#ucp-slider-"+module_id ).data('border-width')),
            min: 0,
            max: 20,
            step: 1,
            create: function() {
              var module_id = $(this).data('module-id');
              var module_css_id = 'ucp-'+$(this).data('module-id');

              if(typeof css_styles[module_css_id] === 'undefined'){
                css_styles[module_css_id]={};
              }

              if(typeof css_styles[module_css_id]['modulecss'] === 'undefined'){
                css_styles[module_css_id]['modulecss']={};
              }

              css_styles[module_css_id]['modulecss']['border-width']=$( this ).slider( "value" )+'px';
              $( "#ucp-slider-handle-"+module_id ).text( $( this ).slider( "value" )+'px' );
            },
            slide: function( event, ui ) {
              var module_id = $(this).data('module-id');
              var module_css_id = 'ucp-'+$(this).data('module-id');

              if(typeof css_styles[module_css_id] === 'undefined'){
                css_styles[module_css_id]={};
              }

              if(typeof css_styles[module_css_id]['modulecss'] === 'undefined'){
                css_styles[module_css_id]['modulecss']={};
              }

              css_styles[module_css_id]['modulecss']['border-width']=ui.value + 'px';

              $( "#ucp-slider-handle-"+module_id ).text( ui.value + 'px' );
              rebuild_iframe_css();
            }
          });

          for(element_id in element_ids_array){

              $( "#ucp-font-size-slider-"+element_ids_array[element_id] ).slider({

                value:parseInt($( "#ucp-font-size-slider-"+element_ids_array[element_id] ).data('font-size')),
                min: 8,
                max: 80,
                step: 1,
                create: function() {
                  $( "#ucp-font-size-handle-"+element_ids_array[element_id] ).text( $( this ).slider( "value" )+'px' );
                },
                slide: function( event, ui ) {
                  var element_id = $(this).data('element-id');
                  var element_type = $(this).data('element-type');
                  var module_id = ucp_iframe.jQuery('[data-element-id="' + element_id +'"]').closest( ".ucp-module" ).attr('id');
                  var element_class = ucp_iframe.jQuery('[data-element-id="' + element_id +'"]').children().last().attr('class').split(' ')[0];

                  css_styles[module_id][element_class]['font-size'] = ui.value + 'px';

                  $( "#ucp-font-size-handle-"+element_id ).text( ui.value + 'px' );
                  rebuild_iframe_css();
                }
              });

              if( $( "#ucp-height-slider-"+element_ids_array[element_id]).length > 0 ){

                $( "#ucp-height-slider-"+element_ids_array[element_id] ).slider({

                  value:parseInt($( "#ucp-height-slider-"+element_ids_array[element_id] ).data('height')),
                  min: 0,
                  max: 300,
                  step: 1,
                  create: function() {
                    $( "#ucp-height-handle-"+element_ids_array[element_id] ).text( $( this ).slider( "value" )+'px' );
                  },
                  slide: function( event, ui ) {
                    var element_id = $(this).data('element-id');
                    var element_type = $(this).data('element-type');
                    var module_id = ucp_iframe.jQuery('[data-element-id="' + element_id +'"]').closest( ".ucp-module" ).attr('id');
                    var element_class = ucp_iframe.jQuery('[data-element-id="' + element_id +'"]').children().last().attr('class').split(' ')[0];

                    css_styles[module_id][element_class]['height'] = ui.value + 'px';

                    $( "#ucp-height-handle-"+element_id ).text( ui.value + 'px' );
                    rebuild_iframe_css();
                  }
                });
              }

          }

          $('.ucp-tooltip').tooltipster({
               animation: 'fade',
               delay: 0,
          });

        } //ucp_open_module_editor


        // Get Controls HTML
      function ucp_element_edit_options_html(element_id, element_type){
        element_options_html='';
        var module_id = ucp_iframe.jQuery('[data-element-id="' + element_id +'"]').closest( ".ucp-module" ).attr('id');

        var element_class = ucp_iframe.jQuery('[data-element-id="' + element_id +'"]').children().last().attr('class').split(' ')[0];
        var padding_units = ['px','%'];
        for(attr in css_attributes[element_class]){
          if(typeof css_styles[module_id][element_class] === 'undefined'){
            css_styles[module_id][element_class]={};
          }
          css_styles[module_id][element_class][attr]=ucp_iframe.jQuery('[data-element-id="' + element_id +'"]').children().last().css(attr).replace(/\"/ig,'');
        }

        switch (element_type){
          case 'text':
          case 'heading_l':
          case 'heading_s':
          case 'input':
          case 'textarea':
          case 'submit':
          case 'countdown_timer':
          case 'large_button':
            element_options_html+='<div>';

            //Text

            if(element_type == 'text' || element_type == 'heading_l' || element_type == 'heading_s'){
              //Check if element has data-html
              var element_html = ucp_iframe.jQuery('[data-element-id="' + element_id +'"]').children().last().attr('data-html');

              if(element_html){                
                element_html = $('<span/>').html(element_html).text();
              } else {                
                element_html = ucp_iframe.jQuery('[data-element-id="' + element_id +'"]').children().last().html();
              }
              element_options_html+='<label>Text:</label><textarea class="ucp-wysiwyg" type="text" id="'+element_id+'_html" name="'+element_id+'_html" data-apply="html">'+element_html+'</textarea>';
              
              element_options_html+=ucp_admin_editor_variables.weglot_editor_banner;

            } else if( element_type == 'input' ){
              element_options_html+='<label>Name:</label><input type="text" id="'+element_id+'_name" name="'+element_id+'_name" data-apply="attr" data-attr="name" value="'+ucp_iframe.jQuery('[data-element-id="' + element_id +'"]').children().last().attr('name')+'" /><br />';
              element_options_html+='<label>Placeholder:</label><input type="text" id="'+element_id+'_placeholder" name="'+element_id+'_placeholder" data-apply="attr" data-attr="placeholder" value="'+ucp_iframe.jQuery('[data-element-id="' + element_id +'"]').children().last().attr('placeholder')+'" />';
              element_options_html+=ucp_admin_editor_variables.weglot_editor_banner;
            } else if( element_type == 'submit' ){
              element_options_html+='<label>Name:</label><input type="text" id="'+element_id+'_name" class="ucp-form-submit-name" name="'+element_id+'_name" data-apply="attr" data-attr="name" value="'+ucp_iframe.jQuery('[data-element-id="' + element_id +'"]').children().last().attr('name')+'" /><br />';
              element_options_html+='<label>Text:</label><input type="text" id="'+element_id+'_value" name="'+element_id+'_value" class="ucp-form-submit-value" data-apply="attr" data-attr="value" value="'+ucp_iframe.jQuery('[data-element-id="' + element_id +'"]').children().last().attr('value')+'" />';
              element_options_html+=ucp_admin_editor_variables.weglot_editor_banner;
            } else if( element_type == 'countdown_timer' ){
              var ucp_months = ['01-Jan','02-Feb','03-Mar','04-Apr','05-May','06-Jun','07-Jul','08-Aug','09-Sep','10-Oct','11-Nov','12-Dec'];
              var current_date = ucp_iframe.jQuery('[data-element-id="' + element_id +'"]').attr('data-date');
              var timer_style = ucp_iframe.jQuery('[data-element-id="' + element_id +'"]').attr('data-style').replace('ucp_countdown_','');
              element_options_html+='<label>Timer Style:</label>';
              element_options_html+='<select id="'+element_id+'_timer_style" name="'+element_id+'_timer_style" data-apply="date" class="timer_style" style="width:80px;">';
                element_options_html+='<option value="text" '+(timer_style == 'text'?'selected':'')+'>Text</option>';
                element_options_html+='<option value="flip" '+(timer_style == 'flip'?'selected':'')+'>Flip</option>';
              element_options_html+='</select>';

              if(typeof current_date == 'undefined'){
                var date = new Date();
                date.setDate(date.getDate() + 1);
              } else {
                var date = new Date(current_date);
              }

              element_options_html+='<label>Timer End:</label>';
              element_options_html+='<div class="ucp-datepicker-wrap">';
              element_options_html+='<select id="'+element_id+'_mm" name="date_mm" data-apply="date" class="date_mm" style="width:80px;">';
              var current_month = date.getMonth();

              for(month in ucp_months){
                element_options_html+='<option value="'+(parseInt(month)+1)+'" '+(month == current_month?'selected':'')+' >'+ucp_months[month]+'</option>';
              }

              element_options_html+='</select>';
              element_options_html+='<input type="text" id="'+element_id+'_jj" name="'+element_id+'_jj" data-apply="date" class="date_jj" value="'+date.getDate()+'" size="2" maxlength="2" autocomplete="off">';
              element_options_html+='<input type="text" style="width:40px;" id="'+element_id+'_aa" name="'+element_id+'_aa" data-apply="date" class="date_aa" value="'+date.getFullYear()+'" size="4" maxlength="4" autocomplete="off">';
              element_options_html+=' @ ';
              element_options_html+='<input type="text" id="'+element_id+'_hh" name="'+element_id+'_hh" data-apply="date" class="date_hh" value="'+date.getHours()+'" size="2" maxlength="2" autocomplete="off">';
              element_options_html+='<input type="text" id="'+element_id+'_mn" name="'+element_id+'_mn" data-apply="date" class="date_mn" value="'+date.getMinutes()+'" size="2" maxlength="2" autocomplete="off">';
              element_options_html+='</div>';
            }


            if( element_type == 'large_button' ){
              var button_target = ucp_iframe.jQuery('[data-element-id="' + element_id +'"]').children().last().attr("target");
              element_options_html+='<label>Button Text:</label><input type="text" id="'+element_id+'_text" name="'+element_id+'_text" data-apply="html" value="'+ucp_iframe.jQuery('[data-element-id="' + element_id +'"]').children().last().html()+'" /><br />';
              element_options_html+='<label>Link URL:</label><input type="text" id="'+element_id+'_url" name="'+element_id+'_url" data-apply="attr" data-attr="href" value="'+ucp_iframe.jQuery('[data-element-id="' + element_id +'"]').children().last().attr("href")+'" /><br />';
              element_options_html+='<label>Link Target:</label><select id="'+element_id+'_target" name="'+element_id+'_target" data-apply="attr" data-attr="target" style="width:80px;">';
                element_options_html+='<option value="_self" '+(button_target == '_self'?'selected':'')+'>_self</option>';
                element_options_html+='<option value="_blank" '+(button_target == '_blank'?'selected':'')+'>_blank</option>';
              element_options_html+='</select>';

            }


            //Text Color
            element_options_html+='<label>Text Color:</label><input type="text" id="'+element_id+'_color" name="'+element_id+'_color" data-apply="css" data-property="color" class="sm_color_picker" value="'+ucp_iframe.jQuery('[data-element-id="' + element_id +'"]').children().last().css("color")+'" />';


            if( element_type == 'input' || element_type == 'textarea' || element_type == 'submit' || element_type == 'large_button' ){
              element_options_html+='<label>Background Color:</label><input type="text" id="'+element_id+'-background-color" name="'+element_id+'_color" data-apply="css" data-property="background-color" class="sm_color_picker" value="'+ucp_iframe.jQuery('[data-element-id="' + element_id +'"]').children().last().css("background-color")+'" />';

              element_options_html+='<label>Padding:</label><div class="ucp-sidebar-paddings-wrapper">';


              module_options_html+='<label>Padding:</label><div class="ucp-sidebar-paddings-wrapper">';

              for(side in padding_sides){
                var padding = ucp_iframe.jQuery('[data-element-id="' + element_id +'"]').children().last().clone().appendTo(ucp_iframe.jQuery('[data-element-id="' + element_id +'"]').parent()).wrap('<div class="ucp-temp-module" style="display:none;"></div>').css('padding-'+padding_sides[side]);

                ucp_iframe.jQuery('.ucp-temp-module').remove();
                var padding_units = ['px','%','em'];
                var paddingWithoutSpaces = padding.replace(/\s/g, '+');
                var paddingVal = parseFloat(padding);
                var padding_unit = padding.replace(paddingVal , '');

                if(padding_unit == 'px' || padding_unit == 'em' || padding_unit == '%'){
                  current_padding_unit = padding_unit;
                } else {
                  current_padding_unit = 'px';
                }
                element_options_html+='<div class="ucp-sidebar-padding-wrapper">';

                  element_options_html+='<span title="Padding '+padding_sides[side]+'" class="ucp-tooltip"><i class="ucp-icon icon-padding-'+padding_sides[side]+'" aria-hidden="true"></i><input class="ucp_sidebar_control_small ucp_sidebar_control_padding_'+padding_sides[side]+'" type="number" id="'+element_id+'_padding_'+padding_sides[side]+'" name="'+element_id+'_padding_'+padding_sides[side]+'" data-property="module-padding" value="'+paddingVal+'" /></span>';

                  element_options_html+='<span title="Padding unit" class="ucp-tooltip">';
                    element_options_html+='<select class="ucp_sidebar_control_padding_unit ucp_sidebar_control_padding_unit_'+padding_sides[side]+'" data-apply="css" data-property="module-padding">';
                    for(punit in padding_units){
                      element_options_html+='<option value="'+padding_units[punit]+'"';
                      if(current_padding_unit == padding_units[punit]){
                        element_options_html+=' selected="selected" ';
                      }
                      element_options_html+='>'+padding_units[punit]+'</option>';
                    }
                    element_options_html+='</select>';
                  element_options_html+='</span>';

                element_options_html+='</div>';
              }
              element_options_html+='</div>';
            }

            if( element_type == 'submit' || element_type == 'large_button' ){
              element_options_html+='<label>Hover Text Color:</label><input type="text" id="'+element_id+'-hover-text-color" name="'+element_id+'_color" data-apply="data" data-property="hover-text-color" class="sm_color_picker" value="'+ucp_iframe.jQuery('[data-element-id="' + element_id +'"]').attr("data-hover-text-color")+'" />';
              element_options_html+='<label>Hover Background Color:</label><input type="text" id="'+element_id+'-hover-background-color" name="'+element_id+'_color" data-apply="data" data-property="hover-background-color" class="sm_color_picker" value="'+ucp_iframe.jQuery('[data-element-id="' + element_id +'"]').attr("data-hover-background-color")+'" />';
            }



            element_options_html+='<label>Font:</label>';

            //Font Family
            var current_font = ucp_iframe.jQuery('[data-element-id="' + element_id +'"]').children().last().css("font-family").split(',')[0].replace(/"/g,'').trim();
            element_options_html+='<select id="'+element_id+'_font_family" name="'+element_id+'_font_family" data-apply="css" data-property="font-family">';

              for(font in ucp_admin_editor_variables.ucp_google_fonts){
                 element_options_html+='<option value="'+font+'"';
                 if(current_font == ucp_admin_editor_variables.ucp_google_fonts[font]['name']){
                   element_options_html+=' selected="selected" ';
                 }
                 element_options_html+='>'+ucp_admin_editor_variables.ucp_google_fonts[font]['name']+'</option>';
              }
            element_options_html+='</select>';

            //Font Weight
            var current_font_variant = get_font_variant(element_id);

            element_options_html+='<select id="'+element_id+'_font_weight" name="'+element_id+'_font_weight" data-apply="css" data-property="font-weight">';



            var font_variants = get_google_font_variants(current_font);

            for(variant in font_variants){
               element_options_html+='<option value="'+font_variants[variant]+'"';
               if(current_font_variant == font_variants[variant]){
                 element_options_html+=' selected="selected" ';
               }
               element_options_html+='>'+font_variants[variant]+'</option>';
            }
            element_options_html+='</select>';

            //Font Size
            element_options_html+='<div id="ucp-font-size-slider-'+element_id+'" class="ucp-font-size-slider" data-element-id="'+element_id+'" data-element-type="'+element_type+'" data-font-size="'+ucp_iframe.jQuery('[data-element-id="' + element_id +'"]').children().last().css('font-size')+'"><div id="ucp-font-size-handle-'+element_id+'" class="ucp-font-size-handle ui-slider-handle"></div></div>';

            //Font Alignment
            element_options_html+='<div class="ucp-font-align-wrapper" data-element-id="'+element_id+'" data-element-type="'+element_type+'">';
            element_options_html+='<span data-text-align="left" class="dashicons ucp-font-align dashicons-editor-alignleft"></span>';
            element_options_html+='<span data-text-align="center" class="dashicons ucp-font-align dashicons-editor-aligncenter"></span>';
            element_options_html+='<span data-text-align="right" class="dashicons ucp-font-align dashicons-editor-alignright"></span>';
            element_options_html+='<span data-text-align="justify" class="dashicons ucp-font-align dashicons-editor-justify"></span>';
            element_options_html+='</div>';


            if(element_type == 'input' || element_type == 'textarea' || element_type == 'submit'){
              element_options_html+='<label>Border:</label>';

              element_options_html+='<span title="Border color" class="ucp-tooltip"><img class="ucp_sidebar_control_icon" src="'+ucp_admin_editor_variables.ucp_plugin_url+'/images/icons/border_color.png" /><input type="text" id="'+element_id+'_color" name="'+element_id+'_color" data-apply="css" data-property="border-color" class="sm_color_picker" value="'+ucp_iframe.jQuery('[data-element-id="' + element_id +'"]').children().last().css('border-color')+'" /></span>';

              var current_border_style = ucp_iframe.jQuery('[data-element-id="' + element_id +'"]').children().last().css('border-style');

              element_options_html+='<span title="Border style" class="ucp-tooltip"><img class="ucp_sidebar_control_icon" src="'+ucp_admin_editor_variables.ucp_plugin_url+'/images/icons/border_style.png" /><select class="ucp_sidebar_control_medium" id="'+element_id+'_border_style" name="'+element_id+'_border_style" data-apply="css" data-property="border-style">';

              for(style in border_styles){
                 element_options_html+='<option value="'+border_styles[style]+'"';
                 if(current_border_style == border_styles[style]){
                   element_options_html+=' selected="selected" ';
                 }
                 element_options_html+='>'+border_styles[style]+'</option>';
              }
              element_options_html+='</select></span>';

              element_options_html+='<span title="Border thickness" class="ucp-tooltip"><img class="ucp_sidebar_control_icon" src="'+ucp_admin_editor_variables.ucp_plugin_url+'/images/icons/border_thickness.png" /><input class="ucp_sidebar_control_small" type="number" id="'+element_id+'_border_width" name="'+element_id+'_color" data-apply="css" data-property="border-width" value="'+parseInt(ucp_iframe.jQuery('[data-element-id="' + element_id +'"]').children().last().css('border-left-width'))+'" /></span>';
            }

            element_options_html+='</div>';
            break;
          case 'image':
            element_options_html+='<div>';
            element_options_html+='<label>Image:</label><input type="text" id="background_image_'+element_id+'" name="'+element_id+'_src" data-apply="attr" data-attr="src" class="ucp_image" value="'+ucp_iframe.jQuery('[data-element-id="' + element_id +'"]').find('img').attr("src")+'" /><div class="button ucp_image_upload">Upload</div><br />';
            if(ucp_iframe.jQuery('[data-element-id="' + element_id +'"]').children().find('a').length){
              var href = ucp_iframe.jQuery('[data-element-id="' + element_id +'"]').children().last('a').attr("href");
            } else {
              var href = '#';
            }
            element_options_html+='<label>Link:</label><input id="image_link_href_'+element_id+'" type="text" name="'+element_id+'_href" data-apply="attr" data-attr="href" class="image-anchor" value="'+href+'" /><br />';

            var image_sizes = {};
            image_sizes.width='Width';
            image_sizes.height='Height';

            var image_size = {};
            image_size.width='';
            image_size.height='';

            for(size in image_sizes){
              image_size[size] = ucp_iframe.jQuery('[data-element-id="' + element_id +'"]').find('img').clone().appendTo('body').wrap('<div class="ucp-temp-image" style="display:none;"></div>').css(size);
              if(!parseInt(image_size[size],10)) image_size[size] = '';

              var image_size_WithoutSpaces = image_size[size].replace(/\s/g, '+');
              var image_size_Val = parseFloat(image_size[size]);
              var image_size_unit = image_size[size].replace(image_size_Val , '');

              element_options_html+='<span title="'+image_sizes[size]+'" class="ucp-tooltip">';
                element_options_html+='<i class="ucp-icon icon-'+size+'" aria-hidden="true"></i><input type="number" name="'+element_id+'_'+size+'" class="image-'+size+'" style="width:18%; display:inline-block;" data-apply="attr" data-attr="'+size+'" value="'+(image_size[size]?image_size_Val:'')+'" />';

                element_options_html+='<select class="ucp_sidebar_control_padding_unit image-'+size+'-unit" data-apply="attr" data-attr="'+size+'">';
                for(punit in padding_units){
                  element_options_html+='<option value="'+padding_units[punit]+'"';
                  if(image_size_unit == padding_units[punit]){
                    element_options_html+=' selected="selected" ';
                  }
                  element_options_html+='>'+padding_units[punit]+'</option>';
                }
                element_options_html+='</select>';

              element_options_html+='</span>';

            }

            element_options_html+='</div>';
            ucp_iframe.jQuery('.ucp-temp-image').remove();


            break;
          case 'video':
            element_options_html+='<div>';
            element_options_html+='<input type="text" id="background_image_'+element_id+'" name="'+element_id+'_src" data-apply="attr" data-attr="src" class="ucp_video tooltipster" tile="Youtube/Vimeo Video URL" placeholder="Youtube/Vimeo Video URL" value="'+ucp_iframe.jQuery('[data-element-id="' + element_id +'"]').children().last().attr("src")+'" /><br />';
            var video_width = parseInt(ucp_iframe.jQuery('[data-element-id="' + element_id +'"]').children().last().attr("width"));
            var video_height = parseInt(ucp_iframe.jQuery('[data-element-id="' + element_id +'"]').children().last().attr("height"));
            element_options_html+='<span title="Width" class="ucp-tooltip"><i class="ucp-icon icon-width" aria-hidden="true"></i><input type="number" name="'+element_id+'_width" style="width:29%; display:inline-block;" class="ucp_sidebar_control_small video_width" data-apply="attr" data-attr="width" value="'+(video_width?video_width:'')+'" /> px</span>';
            element_options_html+='<span title="Height" class="ucp-tooltip"><i class="ucp-icon icon-height" aria-hidden="true"></i><input type="number" name="'+element_id+'_height" style="width:29%; display:inline-block;" class="ucp_sidebar_control_small video_height" data-apply="attr" data-attr="height" value="'+(video_height?video_height:'')+'" /> px</span>';
            element_options_html+='</div>';
            break;
          case 'social':
            element_options_html+='<label>Icon Color:</label><input type="text" class="sm_color_picker social-icons-color" id="'+element_id+'_color" name="'+element_id+'_color" data-apply="css" data-property="color" value="'+ucp_iframe.jQuery('[data-element-id="' + element_id +'"]').children().last().find('a').css("color")+'" />';
            //Font Size
            element_options_html+='<div id="ucp-font-size-slider-'+element_id+'" class="ucp-font-size-slider" data-element-id="'+element_id+'" data-element-type="'+element_type+'" data-font-size="'+ucp_iframe.jQuery('[data-element-id="' + element_id +'"]').children().last().css('font-size')+'"><div id="ucp-font-size-handle-'+element_id+'" class="ucp-font-size-handle ui-slider-handle"></div></div>';

            for(sn in ucp_social_networks){
              element_options_html+='<div class="ucp-social-network-control">';
              element_options_html+='<span class="ucp-tooltip" title="'+sn.charAt(0).toUpperCase()+sn.slice(1)+'"><i class="fa fa-'+ucp_social_networks[sn]+'"></i><input type="checkbox" name="social-'+sn+'" class="social-'+sn+'" '+(ucp_iframe.jQuery('[data-element-id="' + element_id +'"] .ucp-social-'+sn).length?'checked':'')+' /><input type="text" class="ucp_sidebar_control_social social-'+sn+'-url" name="social-'+sn+'-url" value="'+(ucp_iframe.jQuery('[data-element-id="' + element_id +'"] .ucp-social-'+sn).attr('href')?ucp_iframe.jQuery('[data-element-id="' + element_id +'"] .ucp-social-'+sn).attr('href'):'#')+'" /></span>';
              element_options_html+='</div>';
            }
            break;
          case 'divider':
            element_options_html+='<label>Height:</label><div id="ucp-height-slider-'+element_id+'" class="ucp-height-slider" data-element-id="'+element_id+'" data-element-type="'+element_type+'" data-height="'+ucp_iframe.jQuery('[data-element-id="' + element_id +'"]').children().last().css('height')+'"><div id="ucp-height-handle-'+element_id+'" class="ucp-height-handle ui-slider-handle"></div></div>';
            break;
          case 'gmap':
            var map_styles = ['roadmap','satellite','hybrid','terrain'];
            var map_options = ucp_iframe.jQuery('[data-element-id="' + element_id +'"]').children().last().attr('src').replace('https://www.google.com/maps/embed/v1/place?','').split('&');
            current_map_address = decodeURIComponent(map_options[0].replace('q=','').replace(/\+/g,' '));
            current_map_style = map_options[4].replace('maptype=','');
            current_map_zoom = map_options[5].replace('zoom=','');
            current_map_key = map_options[7].replace('key=','');

            element_options_html+='<label>API Key:</label><input type="text" id="'+element_id+'_key" name="'+element_id+'_key" data-apply="gmap" class="gmap_key" value="'+current_map_key+'" />';
            element_options_html+='<label>Address:</label><input type="text" id="'+element_id+'_address" name="'+element_id+'_address" data-apply="gmap" class="gmap_address" value="'+current_map_address+'" />';
            element_options_html+='<label>Zoom Level:</label><input type="text" id="'+element_id+'_address" name="'+element_id+'_address" data-apply="map_url" class="gmap_zoom" value="'+current_map_zoom+'" />';
            element_options_html+='<label>Map Style:</label><span title="Map style" class="ucp-tooltip">';
            element_options_html+='<select class="gmap_style" id="'+element_id+'_map_style" name="'+element_id+'_map_style" data-apply="gmap">';
            for(style in map_styles){
               element_options_html+='<option value="'+map_styles[style]+'"';
               if(current_map_style == map_styles[style]){
                 element_options_html+=' selected="selected" ';
               }
               element_options_html+='>'+map_styles[style]+'</option>';
            }
            element_options_html+='</select></span>';
            var map_width = ucp_iframe.jQuery('[data-element-id="' + element_id +'"]').children().last().attr("width").replace('px','');
            var map_height = ucp_iframe.jQuery('[data-element-id="' + element_id +'"]').children().last().attr("height").replace('px','');

            element_options_html+='<label>Map Size:</label><span title="Width" class="ucp-tooltip"><i class="ucp-icon icon-width" aria-hidden="true"></i><input type="number" name="'+element_id+'_width" style="width:29%; display:inline-block;" data-apply="gmap" class="gmap_width" value="'+(map_width?map_width:'500')+'" /> px</span>';
            element_options_html+='<span title="Height" class="ucp-tooltip"><i class="ucp-icon icon-height" aria-hidden="true"></i><input type="number" name="'+element_id+'_height" style="width:29%; display:inline-block;" data-apply="gmap" class="gmap_height" value="'+(map_height?map_height:'300')+'" /> px</span>';

            break;
          case 'html':
            if( !ucp_iframe.jQuery('[data-element-id="' + element_id +'"]').children().last().data('html') ){
                element_html_val = ucp_iframe.jQuery('[data-element-id="' + element_id +'"]').children().last().html();
            } else {
                element_html_val = ucp_iframe.jQuery('[data-element-id="' + element_id +'"]').children().last().data('html').replace('@!','[').replace('!@',']');
            }
            element_options_html+='<label>HTML:</label><textarea style="min-height:200px;" type="text" id="'+element_id+'_html" name="'+element_id+'_html" data-apply="html">'+element_html_val+'</textarea>';
            break;
          case 'captcha':
            element_options_html+='<label>Label Color:</label><input type="text" id="'+element_id+'_color" name="'+element_id+'_color" data-apply="css" data-property="color" class="sm_color_picker" value="'+ucp_iframe.jQuery('[data-element-id="' + element_id +'"]').children().last().css("color")+'" /><br /><br />';
            element_options_html+='<input type="checkbox" name="'+element_id+'_captcha" class="'+element_id+'_captcha captcha-enabled" data-apply="state" '+(ucp_iframe.jQuery('[data-element-id="' + element_id +'"]').children().last().html().length?'checked':'')+' /> Enable Captcha';
            break;
          case 'terms_checkbox':
            element_options_html+='<input type="checkbox" name="'+element_id+'_terms_checkbox" class="'+element_id+'_terms_checkbox terms-enabled" data-apply="state" '+(ucp_iframe.jQuery('[data-element-id="' + element_id +'"]').children().last().html().length?'checked':'')+' /> Enable Terms Checkbox';
            element_options_html+='<label>Text:</label><input type="text" name="'+element_id+'_label" class="'+element_id+'_label" data-apply="label" value="'+ucp_iframe.jQuery('[data-element-id="' + element_id +'"]').find('label').html()+'" />';
            element_options_html+='<label>Label Color:</label><input type="text" id="'+element_id+'_color" name="'+element_id+'_color" data-apply="label-color" data-property="label-color" class="sm_color_picker" value="'+ucp_iframe.jQuery('[data-element-id="' + element_id +'"]').find('label').css("color")+'" /><br /><br />';
            break;
          default:
            element_options_html+='<label>HTML:</label><textarea type="text" id="'+element_id+'_html" name="'+element_id+'_html" data-apply="html">'+ucp_iframe.jQuery('[data-element-id="' + element_id +'"]').children().last().html()+'</textarea>';
            break;
        }

        return element_options_html;
      }

      function jsUcfirst(string){
        return string.charAt(0).toUpperCase() + string.slice(1);
      }

      function ucp_module_edit_options_html(module_id, module_type){
        module_options_html='';
        var module_css_id = ucp_iframe.jQuery('[data-module-id="' + module_id +'"]').attr('id');
        var module_class = ucp_iframe.jQuery('[data-module-id="' + module_id +'"]').attr('class');
        var module_width = ucp_iframe.jQuery('[data-module-id="' + module_id +'"]').data('module-width');
        var module_type = ucp_iframe.jQuery('[data-module-id="' + module_id +'"]').data('module-type');

        for(attr in css_module_attributes){
            if(typeof css_module_styles[module_css_id] === 'undefined'){
              css_module_styles[module_css_id]={};
            }
            css_module_styles[module_css_id][attr]=ucp_iframe.jQuery('#' + module_css_id).css(attr);
        }

        var module_widths = {};
        module_widths[12]='1/1';
        module_widths[9]='3/4';
        module_widths[6]='1/2';
        module_widths[4]='1/3';
        module_widths[3]='1/4';

        module_options_html+='<div>';
        module_options_html+='<label>Module Width:</label>';
        module_options_html+='<div class="ucp-module-width-wrapper">';
          for(width in module_widths){
            module_options_html+='<div class="ucp-tooltip ucp-module-width-wrapper ucp-module-width ';
            if(module_width == width){
              module_options_html+='ucp-module-width-selected';
            }
            module_options_html+='" title="Make this module '+module_widths[width]+' of container width" data-module-width="'+width+'"><div class="module-width-label">'+module_widths[width]+'</div><div class="module-width-sprite">';

            switch(width){
              case '3':
              module_options_html+='<div class="module-width-solid module-width-'+width+'"></div><div class="module-width-stroke module-width-'+width+'"></div><div class="module-width-stroke module-width-'+width+'"></div><div class="module-width-stroke module-width-'+width+'"></div>';
              break;
              case '4':
              module_options_html+='<div class="module-width-solid module-width-'+width+'"></div><div class="module-width-stroke module-width-'+width+'"></div><div class="module-width-stroke module-width-'+width+'"></div>';
              break;
              case '6':
              module_options_html+='<div class="module-width-solid module-width-'+width+'"></div><div class="module-width-stroke module-width-'+width+'"></div>';
              break;
              case '9':
              module_options_html+='<div class="module-width-solid module-width-3"></div><div class="module-width-solid module-width-3"></div><div class="module-width-solid module-width-3"></div><div class="module-width-stroke module-width-3"></div>';
              break;
              case '12':
              module_options_html+='<div class="module-width-solid module-width-'+width+'"></div>';
              break;
            }
            module_options_html+='</div></div>';
          }
        module_options_html+='</div>';

        module_options_html+='<label>Padding:</label><div class="ucp-sidebar-paddings-wrapper">';

        for(side in padding_sides){
          var padding = ucp_iframe.jQuery('#'+module_css_id).clone().appendTo('body').wrap('<div class="ucp-temp-module" style="display:none;"></div>').css('padding-'+padding_sides[side]);
          ucp_iframe.jQuery('.ucp-temp-module').remove();
          var padding_units = ['px','%','em'];
          var paddingWithoutSpaces = padding.replace(/\s/g, '+');
          var paddingVal = parseFloat(padding);
          var padding_unit = padding.replace(paddingVal , '');

          if(padding_unit == 'px' || padding_unit == 'em' || padding_unit == '%'){
            current_padding_unit = padding_unit;
          } else {
            current_padding_unit = 'px';
          }


            module_options_html+='<div class="ucp-sidebar-padding-wrapper module-padding-'+padding_sides[side]+'" ';
            if((padding_sides[side] == 'left' || padding_sides[side] == 'right') && ucp_iframe.jQuery('#' + module_css_id).hasClass('col-12')){
               module_options_html+='style="display:none;"';
            }
            module_options_html+='>';


            module_options_html+='<span title="Padding '+padding_sides[side]+'" class="ucp-tooltip"><i class="ucp-icon icon-padding-'+padding_sides[side]+'" aria-hidden="true"></i><input class="ucp_sidebar_control_small ucp_sidebar_control_padding_'+padding_sides[side]+'" type="number" id="'+module_id+'_padding_'+padding_sides[side]+'" name="'+module_id+'_padding_'+padding_sides[side]+'" data-apply="css" data-property="module-padding" value="'+paddingVal+'" /></span>';

            module_options_html+='<span title="Padding unit" class="ucp-tooltip">';
              module_options_html+='<select class="ucp_sidebar_control_padding_unit ucp_sidebar_control_padding_unit_'+padding_sides[side]+'" data-apply="css" data-property="module-padding">';
              for(punit in padding_units){
                module_options_html+='<option value="'+padding_units[punit]+'"';
                if(current_padding_unit == padding_units[punit]){
                  module_options_html+=' selected="selected" ';
                }
                module_options_html+='>'+padding_units[punit]+'</option>';
              }
              module_options_html+='</select>';
            module_options_html+='</span>';

          module_options_html+='</div>';
        }
        module_options_html+='</div>';


        module_options_html+='<label>Border:</label>';

        if(module_type == 'image'){
          var module_border_color = ucp_iframe.jQuery('#' + module_css_id).find('img').css('border-color');
        } else {
          var module_border_color = ucp_iframe.jQuery('#' + module_css_id).css('border-color');
        }
        module_options_html+='<span title="Border color" class="ucp-tooltip"><i class="ucp-icon icon-solid-color-bg" aria-hidden="true"></i><input type="text" id="'+module_id+'_color" name="'+module_id+'_color" data-apply="css" data-property="border-color" class="sm_color_picker" value="'+module_border_color+'" /></span>';

        var current_border_style = ucp_iframe.jQuery('#' + module_css_id).css('border-style');

        if(module_type == 'image'){
          var current_border_style = ucp_iframe.jQuery('#' + module_css_id).find('img').css('border-style');
        } else {
          var current_border_style = ucp_iframe.jQuery('#' + module_css_id).css('border-style');
        }

        module_options_html+='<span title="Border style" class="ucp-tooltip"><i class="ucp-icon icon-border-style" aria-hidden="true"></i><select class="ucp_sidebar_control_medium" id="'+module_id+'_border_style" name="'+module_id+'_border_style" data-apply="css" data-property="border-style">';

        for(style in border_styles){
           module_options_html+='<option value="'+border_styles[style]+'"';
           if(current_border_style == border_styles[style]){
             module_options_html+=' selected="selected" ';
           }
           module_options_html+='>'+border_styles[style]+'</option>';
        }
        module_options_html+='</select></span>';

        if(module_type == 'image'){
          var current_border_thickness = parseInt(ucp_iframe.jQuery('#' + module_css_id).find('img').css('border-width'));
        } else {
          var current_border_thickness = parseInt(ucp_iframe.jQuery('#' + module_css_id).css('border-width'));
        }
        module_options_html+='<span title="Border thickness" class="ucp-tooltip"><i class="ucp-icon icon-border-thickness" aria-hidden="true"></i><input class="ucp_sidebar_control_small" type="number" id="'+module_id+'_border_width" name="'+module_id+'_color" data-apply="css" data-property="border-width" class="sm_color_picker" value="'+current_border_thickness+'" /></span>';

        module_options_html+='<div class="ucp_editor_background" data-applyto="'+module_id+'">';
          module_options_html+='<label>Background:</label>';
          module_options_html+='<div class="ucp_editor_background_styles_wrapper">';
            module_options_html+='<div title="Transparent background" class="ucp-tooltip ucp_editor_background_style" data-background-type="transparent"><i class="icon-transparent-background" aria-hidden="true"></i></div>';
            module_options_html+='<div title="Solid background" class="ucp-tooltip ucp_editor_background_style" data-background-type="color"><i class="icon-solid-color-bg" aria-hidden="true"></i></div>';
            module_options_html+='<div title="Gradient background" class="ucp-tooltip ucp_editor_background_style" data-background-type="gradient"><i class="icon-gradient-background" aria-hidden="true"></i></div>';
            module_options_html+='<div title="Image background" class="ucp-tooltip ucp_editor_background_style" data-background-type="image"><i class="icon-image-background" aria-hidden="true"></i></div>';
          module_options_html+='</div>';

          module_options_html+='<div class="ucp_editor_background_options"></div>';

          var current_module_margin = ucp_iframe.jQuery('#' + module_css_id).css('margin');
          module_options_html+='<br /><div class="module-full-width-option" style="'+(ucp_iframe.jQuery('#' + module_css_id).hasClass('col-12')?'':'display:none;')+'"><input type="checkbox" '+(current_module_margin.indexOf('1500px') > -1 ?' checked ':'')+' id="'+module_id+'_full_width_bg" name="'+module_id+'_full_width_bg" data-apply="css" data-property="full-width" value="'+ucp_iframe.jQuery('#' + module_css_id).css('border-color')+'" /> Full Width Background</div>';

        module_options_html+='</div>';


        if(module_type == 'newsletter' || module_type == 'contact'){
          var form_method = ucp_iframe.jQuery('[data-module-id="' + module_id +'"]').attr('method');

          var form_processor = {};
          form_processor.local = 'Local';

          if(ucp_admin_editor_variables.ucp_lc >=2 && ucp_admin_editor_variables.mailchimp_status == 1 ){
            form_processor.mailchimp = 'Local + Mailchimp';
          }

          if(ucp_admin_editor_variables.ucp_lc >=3 && ucp_admin_editor_variables.zapier_status == 1 ){
            form_processor.zapier = 'Local + Zapier';
          }

          if(ucp_admin_editor_variables.ucp_lc >=2 && module_type == 'contact' && ucp_admin_editor_variables.ar_status == 1 ){
            form_processor.autoresponder = 'Local + Autoresponder';
          }

          //form_processor.custom = 'Custom';

          var current_form_processor = ucp_iframe.jQuery('[data-module-id="' + module_id +'"]').attr('data-processor');

          module_options_html+='<hr />';


          module_options_html+='<label>Form Processor</label><select class="ucp-form-processor" data-apply="attr" data-attr="data-processor">';
            for(fp in form_processor){
              module_options_html+='<option value="'+fp+'" '+(fp == current_form_processor?'selected':'')+' >'+form_processor[fp]+'</option>';
            }
          module_options_html+='</select>';

          if( module_type == 'contact' ){
            module_options_html+='<label>Admin Email:</label><input type="text" id="'+module_id+'_admin_email" name="'+module_id+'_admin_email" class="ucp-form-admin-email" data-apply="attr" data-attr="data-admin-email" value="'+ucp_iframe.jQuery('[data-module-id="' + module_id +'"]').attr('data-admin-email')+'" /><br />';
            module_options_html+='<label>Confirmation Email Subject:</label><input type="text" id="'+module_id+'_email_subject" name="'+module_id+'_email_subject" class="ucp-form-email-subject" data-apply="attr" data-attr="data-email-subject" value="'+ucp_iframe.jQuery('[data-module-id="' + module_id +'"]').attr('data-email-subject')+'" /><br />';
            module_options_html+='<label>Confirmation Email Body:</label><textarea type="text" id="'+module_id+'_email_body" name="'+module_id+'_email_body" class="ucp-form-email-body" data-apply="attr" data-attr="data-email-body">'+ucp_iframe.jQuery('[data-module-id="' + module_id +'"]').attr('data-email-body')+'</textarea><br />';
          }

          /*
          module_options_html+='<div class="ucp-form-fields-custom" '+(current_form_processor !== 'custom'?'style="display:none;"':'')+'>';
          module_options_html+='<label>Form Method:</label><select type="text" id="'+module_id+'_method" name="'+module_id+'_method" class="ucp-form-submit-method" data-apply="attr" data-attr="method"><option value="POST" '+(form_method == 'POST'?' selected ':'' )+'>POST</option><option value="GET" '+(form_method == 'GET'?' selected ':'' )+'>GET</option></select><br />';
          module_options_html+='<label>Form Action:</label><input type="text" id="'+module_id+'_action" name="'+module_id+'_action" class="ucp-form-submit-action" data-apply="attr" data-attr="action" value="'+ucp_iframe.jQuery('[data-module-id="' + module_id +'"]').attr('action')+'" /><br />';
          module_options_html+='</div>';
          */
          module_options_html+='<br /><label>Submit Success Message:</label><input type="text" id="'+module_id+'_msg_success" name="'+module_id+'_msg_success" data-apply="attr" data-attr="data-msg-success" value="'+ucp_iframe.jQuery('#' + module_css_id).attr('data-msg-success')+'" />';
          module_options_html+='<br /><label>Submit Captcha Error Message:</label><input type="text" id="'+module_id+'_msg_captcha" name="'+module_id+'_msg_captcha" data-apply="attr" data-attr="data-msg-captcha" value="'+ucp_iframe.jQuery('#' + module_css_id).attr('data-msg-captcha')+'" />';
          module_options_html+='<br /><label>Submit Error Message:</label><input type="text" id="'+module_id+'_msg_error" name="'+module_id+'_msg_error" data-apply="attr" data-attr="data-msg-error" value="'+ucp_iframe.jQuery('#' + module_css_id).attr('data-msg-error')+'" />';
        }

        module_options_html+='</div>';


        return module_options_html;
      }

      //Events
      $(window).bind('beforeunload', function (e) {
          if(window.ucp_save_confirm == true){
            var message = 'Are you sure you want to leave the page? Any unsaved data will be lost.';
            e.returnValue = message;
            return message;
          }
      });

      $('#ucp-style-sidebar').on('click','.ucp_editor_background_style',function(){
        var background_options = {};
        background_options.background_type = $(this).data('background-type');

        var apply_to = $(this).parent().parent().data('applyto');
        $(this).parent().children('.ucp_editor_background_style').removeClass('ucp_editor_background_style_selected');
        $(this).addClass('ucp_editor_background_style_selected');

        background_options.background_color='';
        background_options.background_color_a='';
        background_options.background_color_b='';
        background_options.background_orientation='horizontal';
        background_options.background_image='';
        background_options.background_video='';        
        background_options.background_video_fallback='';
        background_options.background_animation='';
        background_options.animation_color_a='#333333';
        background_options.animation_color_b='rgb(103, 103, 103)';

        var module_css_id = 'ucp-'+apply_to;

        if(apply_to == 'page'){
          $('#ucp_page_properties .ucp_editor_background').attr('data-background-type',background_options.background_type);
          if( background_options.background_type != 'video' ){
            ucp_iframe.jQuery('.video-background').remove();
          }
          if( background_options.background_type != 'animated' ){
            ucp_iframe.jQuery('#ucp-animated-background').remove();
            ucp_iframe.jQuery('#ucp_template_animation_js').remove();
            ucp_iframe.jQuery('#ucp_template_animation_js_init').remove();
            ucp_iframe.pJSDom=[];
          }

          generate_background_options_html(apply_to,background_options);
        } else {
          if(background_options.background_type == 'transparent'){
            delete css_styles[module_css_id]['modulecss']['background|1'];
            delete css_styles[module_css_id]['modulecss']['background|2'];
            delete css_styles[module_css_id]['modulecss']['background|3'];
            delete css_styles[module_css_id]['modulecss']['background|4'];
            delete css_styles[module_css_id]['modulecss']['background-image'];
            delete css_styles[module_css_id]['modulecss']['background-color'];
            delete css_styles[module_css_id]['modulecss']['background-size'];
          } else if(background_options.background_type == 'image'){
            delete css_styles[module_css_id]['modulecss']['background|1'];
            delete css_styles[module_css_id]['modulecss']['background|2'];
            delete css_styles[module_css_id]['modulecss']['background|3'];
            delete css_styles[module_css_id]['modulecss']['background|4'];
            delete css_styles[module_css_id]['modulecss']['background-color'];
          } else if(background_options.background_type == 'color'){
            delete css_styles[module_css_id]['modulecss']['background|1'];
            delete css_styles[module_css_id]['modulecss']['background|2'];
            delete css_styles[module_css_id]['modulecss']['background|3'];
            delete css_styles[module_css_id]['modulecss']['background|4'];
            delete css_styles[module_css_id]['modulecss']['background-image'];
            delete css_styles[module_css_id]['modulecss']['background-size'];
          }
          generate_background_options_html(apply_to,background_options);
        }
         $('.ucp-tooltip').tooltipster({
               animation: 'fade',
               delay: 0,
            });
        if(apply_to == 'page'){
          rebuild_iframe_css(true);
        } else {
          rebuild_iframe_css();
        }
      });


      function setup_events(){
        $('.ucp_sidebar_edit').on('click','.ucp-font-align',function(){
          var element_id = $(this).closest( "[data-element-id]" ).data('element-id');
          var element_type = $(this).closest( "[data-element-id]" ).data('element-type');
          var module_id = ucp_iframe.jQuery('[data-element-id="' + element_id +'"]').closest( ".ucp-module" ).attr('id');
          var element_class = ucp_iframe.jQuery('[data-element-id="' + element_id +'"]').children().last().attr('class').split(' ')[0];

          css_styles[module_id][element_class]['text-align']=$(this).data('text-align');

          if(element_type == 'large_button'){
            if($(this).data('text-align') == 'left' || $(this).data('text-align') == 'right'){
              css_styles[module_id][element_class]['float']=$(this).data('text-align');
            } else {
              css_styles[module_id][element_class]['float']='none';
            }
          }

          rebuild_iframe_css();
        });

        $('.ucp_sidebar_edit').on('click','.ucp-module-width',function(){
          var module_id = $(this).closest( "[data-module-id]" ).data('module-id');
          var module_type = $(this).closest( "[data-module-id]" ).data('module-type');
          var new_width = $(this).data('module-width');
          if(new_width == 12){
            $(this).closest( "[data-module-id]" ).children().find('.module-full-width-option').show();
            $(this).closest( "[data-module-id]" ).children().find('.module-padding-left').hide();
            $(this).closest( "[data-module-id]" ).children().find('.module-padding-right').hide();
          } else {
            $(this).closest( "[data-module-id]" ).children().find('.module-full-width-option').hide();
            $(this).closest( "[data-module-id]" ).children().find('.module-padding-left').show();
            $(this).closest( "[data-module-id]" ).children().find('.module-padding-right').show();
          }

          $('[data-module-width]').removeClass('ucp-module-width-selected');
          $('[data-module-width="' + new_width +'"]').addClass('ucp-module-width-selected');
          ucp_iframe.jQuery('[data-module-id="' + module_id +'"]').attr( 'data-module-width', new_width );
          ucp_iframe.jQuery('[data-module-id="' + module_id +'"]').attr( 'class','ucp-module col-'+new_width+' col-md-'+new_width+' col-sm-12 col-xs-12' );
        });


        $('#ucp_page_properties').on('keyup blur change','textarea,input,select',function(){
          rebuild_iframe_css(true);
        });

        var ucp_reload_module_timeout;

        $('.ucp_sidebar_edit').on('change keyup blur mouseup','textarea,textarea,input[type="text"],input[type="number"]',function(){
          ucp_change_sidebar_field($(this));

        });

        $('.ucp_sidebar_edit').on('change','select,input[type="checkbox"]',function(){
          ucp_change_sidebar_field($(this));

        });

        function ucp_change_sidebar_field(sidebar_field){
          var element_id = sidebar_field.closest( "[data-element-id]" ).attr('data-element-id');

          if( typeof element_id !== 'undefined'){
            var element_type = sidebar_field.closest( "[data-element-id]" ).attr('data-element-type');
            var module_id = ucp_iframe.jQuery('[data-element-id="' + element_id +'"]').closest( ".ucp-module" ).attr('id');
            var element_class = ucp_iframe.jQuery('[data-element-id="' + element_id +'"]').children().last().attr('class').split(' ')[0];
            
            switch (element_type){
              case 'text':
              case 'heading_l':
              case 'heading_s':
              case 'html':
              case 'input':
              case 'submit':
              case 'textarea':
              case 'countdown_timer':
              case 'large_button':
              case 'captcha':
              case 'terms_checkbox':

                if(element_type == 'captcha' && sidebar_field.data('apply') == 'state'){
                  if(sidebar_field.is(':checked')){
                    ucp_iframe.jQuery( '[data-element-id="' + element_id +'"]').children().last().html(ucp_admin_editor_variables.ucp_captcha_html);
                  } else {
                    ucp_iframe.jQuery( '[data-element-id="' + element_id +'"]').children().last().html('');
                  }
                }

                if(element_type == 'terms_checkbox'){
                  if(sidebar_field.data('apply') == 'state'){
                    if(sidebar_field.is(':checked')){
                      ucp_iframe.jQuery( '[data-element-id="' + element_id +'"]').children().last().html(ucp_terms_checkbox_html);
                    } else {
                      ucp_iframe.jQuery( '[data-element-id="' + element_id +'"]').children().last().html('');
                    }
                  }

                  if(sidebar_field.data('apply') == 'label'){
                    ucp_iframe.jQuery( '[data-element-id="' + element_id +'"]').find('label').html(sidebar_field.val());
                  }

                  if(sidebar_field.data('apply') == 'label-color'){
                    ucp_iframe.jQuery( '[data-element-id="' + element_id +'"]').find('label').css('color',sidebar_field.val());
                  }
                }
                

                if(sidebar_field.data('apply') == 'html'){

                  var element_html = sidebar_field.val();
                  if(/\[.*?\]/.test(element_html)){
                    if(ucp_iframe.jQuery( '[data-element-id="' + element_id +'"]').children().last().attr('data-html') != ucp_escapeHtml(element_html)){
                      ucp_iframe.jQuery( '[data-element-id="' + element_id +'"]').children().last().attr('data-html', ucp_escapeHtml(element_html).replace(/\[/g,'@!').replace(/\]/g,'!@') );
                      $('.ucp_sidebar_edit .ucp-input-shortcode-message').remove();
                      sidebar_field.after('<div class="ucp-input-shortcode-message">The shortcodes inserted in the field will be converted after you have finished editing.</div>');
                      clearTimeout(ucp_reload_module_timeout);
                      ucp_reload_module_timeout = setTimeout(function(){
                         ucp_iframe.jQuery( '[data-element-id="' + element_id +'"]').children().last().html( element_html );
                         ucp_save_template(true,false,false,false);
                      }, 2000);
                    }
                  } else {
                    ucp_iframe.jQuery( '[data-element-id="' + element_id +'"]').children().last().html( sidebar_field.val() );
                    ucp_iframe.jQuery( '[data-element-id="' + element_id +'"]').children().last().removeAttr('data-html');
                  }
                }

                if(sidebar_field.data('property') == 'module-padding' ){
                 for(side in padding_sides){
                  css_styles[module_id][element_class]['padding-'+padding_sides[side]]=$('[data-element-id="'+element_id+'"] .ucp_sidebar_control_padding_'+padding_sides[side]).val() + $('[data-element-id="'+element_id+'"] .ucp_sidebar_control_padding_unit_'+padding_sides[side]).val();
                 }
                }
                if(sidebar_field.data('apply') == 'css'){
                  if(sidebar_field.data('property') == 'font-family'){
                    css_styles[module_id][element_class][sidebar_field.data('property')]=sidebar_field.find('option:selected').text();

                    var font_variants_id = sidebar_field.attr('id').replace('_family','_weight');
                    current_font_weight = $('#'+font_variants_id).val();

                    var font_weights = get_google_font_variants(sidebar_field.val());
                    var font_weights_html = '';

                    css_styles[module_id][element_class]['font-weight'] = 'regular';

                    for(weight in font_weights){
                       if( font_weights[weight].indexOf('italic') < 0 ){
                         font_weights_html+='<option value="'+font_weights[weight]+'"';
                         /*if(current_font_weight == font_weights[weight]){
                           font_weights_html+=' selected="selected" ';
                         }*/
                         font_weights_html+='>'+font_weights[weight]+'</option>';
                       }
                    }
                    $('#'+font_variants_id).empty().append(font_weights_html);
                    refresh_google_fonts();
                  } else if(sidebar_field.data('property') == 'border-width'){
                    css_styles[module_id][element_class][sidebar_field.data('property')]=sidebar_field.val()+'px';
                  } else {
                    css_styles[module_id][element_class][sidebar_field.data('property')]=sidebar_field.val();
                    if(sidebar_field.data('property') == 'font-weight'){
                      refresh_google_fonts();
                    }
                  }
                }
                if(sidebar_field.data('apply') == 'date'){
                   var ucp_countdown_date = $('[data-element-id="'+element_id+'"] .date_aa').val()+'/'+$('[data-element-id="'+element_id+'"] .date_mm').val()+'/'+$('[data-element-id="'+element_id+'"] .date_jj').val()+' '+$('[data-element-id="'+element_id+'"] .date_hh').val()+':'+$('[data-element-id="'+element_id+'"] .date_mn').val()+':00';
                   var timer_style = $('[data-element-id="'+element_id+'"] .timer_style').val();
                   var date_final = new Date(ucp_countdown_date);
                   var date_now = new Date();
                   var date_diff = (parseInt(date_final.getTime()/1000)) - parseInt((date_now.getTime()/1000));

                   var countdown_html = '<div class="ucp-element" data-element-type="countdown_timer" data-css-attr="color,font-size" data-date="'+ucp_countdown_date+'" data-element-id="' + element_id +'" data-diff="'+date_diff+'" data-attr="html" data-style="ucp_countdown_'+timer_style+'"><div class="fcountdown-timer"></div></div>';

                   if(timer_style == 'text'){
                     ucp_iframe.jQuery('[data-element-id=' + element_id +']').remove();
                     ucp_iframe.jQuery('#'+module_id).html(countdown_html);
                     var fcountdown_JS = 'jQuery("[data-element-id=\'' + element_id +'\'] .fcountdown-timer").countdown("'+ucp_countdown_date+'", function(event) {   jQuery(this).text(event.strftime("%D days %H:%M:%S"));  });';
                   }
                   if(timer_style == 'flip'){
                     ucp_iframe.jQuery('[data-element-id=' + element_id +']').remove();
                     ucp_iframe.jQuery('#'+module_id).html(countdown_html);
                     var fcountdown_JS = '\
                     var fc_date_final = new Date("'+ucp_countdown_date+'");\
                     var fc_date_now = new Date();\
                     var fc_date_diff = (parseInt(fc_date_final.getTime()/1000)) - parseInt((fc_date_now.getTime()/1000));\
                     jQuery("[data-element-id=\'' + element_id +'\'] .fcountdown-timer").FlipClock(fc_date_diff,{clockFace: \'DailyCounter\', countdown: true});';
                   }

                   ucp_iframe.jQuery('#ucp_template_fcountdown_js').remove();
                   ucp_iframe.jQuery('<script/>', {id: 'ucp_template_fcountdown_js', text: fcountdown_JS}).appendTo('#ucp-template');
                }
                if(sidebar_field.data('apply') == 'attr'){
                   if(sidebar_field.data('applyto') == 'form'){
                      ucp_iframe.jQuery( '[data-element-id="' + element_id +'"]').parent().attr( sidebar_field.data('attr'),sidebar_field.val() );
                   }

                   ucp_iframe.jQuery( '[data-element-id="' + element_id +'"]').children().last().attr( sidebar_field.data('attr'),sidebar_field.val() );
                }
                if(sidebar_field.data('apply') == 'data'){
                   ucp_iframe.jQuery( '[data-element-id="' + element_id +'"]').attr( 'data-'+sidebar_field.data('property'),sidebar_field.val() );
                }


                break;
              case 'captcha':

                break;
              case 'image':
                if(sidebar_field.data('apply') == 'attr'){
                  var href = sidebar_field.closest( "[data-element-id]" ).find('.image-anchor').val();
                  var src = sidebar_field.closest( "[data-element-id]" ).find('.ucp_image').val();
                  var width = sidebar_field.closest( "[data-element-id]" ).find('.image-width').val();
                  var height = sidebar_field.closest( "[data-element-id]" ).find('.image-height').val();
                  if(width > 0 ){
                    width = 'width:'+width+sidebar_field.closest( "[data-element-id]" ).find('.image-width-unit').val()+';';
                  } else {
                    width = 'width:auto;';
                  }

                  if(height > 0 ){
                    height = 'height:'+height+sidebar_field.closest( "[data-element-id]" ).find('.image-height-unit').val()+';';
                  } else {
                    height = 'height:auto;';
                  }

                  if(href && href != '#'){
                    ucp_iframe.jQuery('[data-element-id="' + element_id +'"]').children().last().replaceWith('<a class="image-link" href="'+href+'"><img src="'+src+'" style="'+height+width+'" /></a>');
                  } else {
                    ucp_iframe.jQuery('[data-element-id="' + element_id +'"]').children().last().replaceWith('<img class="image" src="'+src+'" style="'+height+width+'" />');
                  }
                }
                break;
              case 'video':
                if(sidebar_field.data('apply') == 'attr'){
                  ucp_iframe.jQuery('[data-element-id="' + element_id +'"]').children().last().attr( sidebar_field.data('attr'),video_url_parser(sidebar_field.val()) );
                  ucp_iframe.jQuery('[data-element-id="' + element_id +'"]').children().last().attr( 'width',$('[data-element-id="'+element_id+'"] .video_width').val() );
                  ucp_iframe.jQuery('[data-element-id="' + element_id +'"]').children().last().attr( 'height',$('[data-element-id="'+element_id+'"] .video_height').val() );
                }
                break;
              case 'gmap':
                var map_src = 'https://www.google.com/maps/embed/v1/place?';
                map_src += 'q='+encodeURIComponent($('[data-element-id="'+element_id+'"] .gmap_address').val());
                map_src += '&attribution_source=Google+Maps+Widget';
                map_src += '&attribution_web_url=http%3A%2F%2Flocalhost%2Fgordan%2Fwp_gmw';
                map_src += '&attribution_ios_deep_link_id=comgooglemaps%3A%2F%2F%3Fdaddr%3DNew+York%2C+USA';
                map_src += '&maptype='+$('[data-element-id="'+element_id+'"] .gmap_style').val();
                map_src += '&zoom='+$('[data-element-id="'+element_id+'"] .gmap_zoom').val();
                map_src += '&language=en';
                map_src += '&key='+$('[data-element-id="'+element_id+'"] .gmap_key').val();

                ucp_iframe.jQuery('[data-element-id="' + element_id +'"]').children().last().attr( 'src',map_src );
                ucp_iframe.jQuery('[data-element-id="' + element_id +'"]').children().last().attr( 'width',$('[data-element-id="'+element_id+'"] .gmap_width').val() );
                ucp_iframe.jQuery('[data-element-id="' + element_id +'"]').children().last().attr( 'height',$('[data-element-id="'+element_id+'"] .gmap_height').val() );
                break;
              case 'social':
                var ucp_social_html='<div class="socialicons">';
                var social_icons_color = sidebar_field.closest( "[data-element-id]" ).find('.social-icons-color').val();
                css_styles[module_id][element_class]['color']=social_icons_color;

                for(sn in ucp_social_networks){
                  if(sidebar_field.closest( "[data-element-id]" ).find('.social-'+sn).is(":checked")){
                    var href_value = '';
                    if(sn == 'envelope'){
                      href_value += 'mailto:';
                    }
                    if(sn == 'phone'){
                      href_value += 'tel:';
                    }
                    if(sn == 'skype'){
                      href_value += 'skype:';
                    }
                    href_value += sidebar_field.closest( "[data-element-id]" ).find('.social-'+sn+'-url').val();
                    ucp_social_html+='<a class="ucp-social-'+sn+'" title="'+sn+'" href="'+href_value+'" target="_blank"><i class="fa fa-'+ucp_social_networks[sn]+' fa-3x"></i></a>';
                  }
                }
                ucp_social_html+='</div>';
                ucp_iframe.jQuery('[data-element-id="' + element_id +'"]').html(ucp_social_html);
                break;

            }
          } else {

             var module_id = sidebar_field.closest( "[data-module-id]" ).attr('data-module-id');
             var module_css_id = 'ucp-'+module_id;

             if(typeof css_styles[module_css_id] === 'undefined'){
               css_styles[module_css_id]={};
             }

             if(typeof css_styles[module_css_id]['modulecss'] === 'undefined'){
               css_styles[module_css_id]['modulecss']={};
             }

             rebuild_iframe_css();

             if( sidebar_field.data('apply') == 'attr' ){
                if(sidebar_field.data('attr') == 'data-processor'){
                   var processor = sidebar_field.val();

                   if( processor === 'custom' ){
                    $('.ucp-form-fields-custom').show();
                   } else {
                    $('.ucp-form-fields-custom').hide();
                   }
                }

                if(sidebar_field.data('attr') == 'data-email-subject' || sidebar_field.data('attr') == 'data-email-body' ){
                  ucp_iframe.jQuery('[data-module-id="' + module_id +'"]').attr( sidebar_field.data('attr'), ucp_escapeHtml(sidebar_field.val()) );
                } else {
                  ucp_iframe.jQuery('[data-module-id="' + module_id +'"]').attr( sidebar_field.data('attr'), sidebar_field.val() );
                }
             }

             if( sidebar_field.data('apply') == 'css' ){

               if( sidebar_field.data('property') == 'full-width' ){
                 css_styles[module_css_id]['modulecss']['full-width']=sidebar_field.is(':checked');
                 $('[data-module-id="'+module_id+'"] [data-property="padding-left"]').prop('disabled', sidebar_field.is(':checked'));
                 $('[data-module-id="'+module_id+'"] [data-property="padding-right"]').prop('disabled', sidebar_field.is(':checked'));
                 if(sidebar_field.is(':checked')){
                   $('[data-module-id="'+module_id+'"] [data-property="padding-left"]').val('0px');
                   $('[data-module-id="'+module_id+'"] [data-property="padding-right"]').val('0px');
                 }
               } else if(sidebar_field.data('property') == 'module-padding' ){
                 for(side in padding_sides){
                  css_styles[module_css_id]['modulecss']['padding-'+padding_sides[side]]=$('[data-module-id="'+module_id+'"] .ucp_sidebar_control_padding_'+padding_sides[side]).val() + $('[data-module-id="'+module_id+'"] .ucp_sidebar_control_padding_unit_'+padding_sides[side]).val();
                 }
               } else if( sidebar_field.data('property') == 'border-width'){
                 css_styles[module_css_id]['modulecss'][sidebar_field.data('property')]=sidebar_field.val()+'px';
               } else {
                 css_styles[module_css_id]['modulecss'][sidebar_field.data('property')]=sidebar_field.val();
               }

             }

             if( sidebar_field.data('apply') == 'gradient' ){

               var background_color_a = $('[data-module-id="'+module_id+'"] .background_color_a').val();
               var background_color_b = $('[data-module-id="'+module_id+'"] .background_color_b').val();
               var background_orientation = $('[data-module-id="'+module_id+'"] .background_orientation').val();


                if(background_orientation == 'horizontal'){
                  css_styles[module_css_id]['modulecss']['background|1'] = background_color_a;
                  css_styles[module_css_id]['modulecss']['background|2'] = '-moz-linear-gradient(left, '+background_color_a+' 0%, '+background_color_b+' 100%)';
                  css_styles[module_css_id]['modulecss']['background|3'] = '-webkit-linear-gradient(left, '+background_color_a+' 0%,'+background_color_b+' 100%)';
                  css_styles[module_css_id]['modulecss']['background|4'] = 'linear-gradient(to right, '+background_color_a+' 0%,'+background_color_b+' 100%)';
                }

                if(background_orientation == 'vertical'){
                  css_styles[module_css_id]['modulecss']['background|1'] = background_color_a;
                  css_styles[module_css_id]['modulecss']['background|2'] = '-moz-linear-gradient(top, '+background_color_a+' 0%, '+background_color_b+' 100%)';
                  css_styles[module_css_id]['modulecss']['background|3'] = '-webkit-linear-gradient(top, '+background_color_a+' 0%,'+background_color_b+' 100%)';
                  css_styles[module_css_id]['modulecss']['background|4'] = 'linear-gradient(to bottom, '+background_color_a+' 0%,'+background_color_b+' 100%)';
                }


                if(background_orientation == 'radial'){
                  css_styles[module_css_id]['modulecss']['background|1'] = background_color_a;
                  css_styles[module_css_id]['modulecss']['background|2'] = '-moz-radial-gradient(center, ellipse cover, '+background_color_a+' 0%, '+background_color_b+' 100%)';
                  css_styles[module_css_id]['modulecss']['background|3'] = '-webkit-radial-gradient(center, ellipse cover, '+background_color_a+' 0%,'+background_color_b+' 100%)';
                  css_styles[module_css_id]['modulecss']['background|4'] = 'radial-gradient(ellipse at center, '+background_color_a+' 0%,'+background_color_b+' 100%)';
                }

             }

             if( sidebar_field.data('apply') == 'background-image' ){
               css_styles[module_css_id]['modulecss']['background-image']='url('+$('[data-module-id="'+module_id+'"] .background_image').val()+')';
               css_styles[module_css_id]['modulecss']['background-size']=$('[data-module-id="'+module_id+'"] .background_size').val();
               css_styles[module_css_id]['modulecss']['background-repeat']=$('[data-module-id="'+module_id+'"] .background_repeat').val();

             }

          }

        rebuild_iframe_css();
      }

      ucp_iframe.jQuery('.ucp-row').on('mouseenter','.ucp-module',function(){

          if( ucp_iframe.jQuery(this).find('div.ucp-module-controls').length === 0 && disable_hover == false ){
              var module_controls_html = '<div class="ucp-module-controls">';
              module_controls_html+='<div class="ucp-module-title">'+module_names[ucp_iframe.jQuery(this).data('module-type')]+'</div>';
              module_controls_html+='<div class="ucp-module-move"><i class="fa fa-arrows" aria-hidden="true"></i></div>';
              module_controls_html+='<div class="ucp-module-edit"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></div>';
              module_controls_html+='<div class="ucp-module-clone"><i class="fa fa-clone" aria-hidden="true"></i></div>';
              module_controls_html+='<div class="ucp-module-delete"><i class="fa fa-trash" aria-hidden="true"></i></div>';
              module_controls_html+='</div>';

              ucp_iframe.jQuery('[data-module-id="' + ucp_iframe.jQuery(this).attr('data-module-id') +'"]').prepend(module_controls_html);
              if( ucp_iframe.jQuery(this).attr('data-module-width') === '3' ){
                keepwithin = ucp_iframe.jQuery(this);
              } else {
                keepwithin = null;
              }

              ucp_iframe.jQuery( '.ucp-module-controls' ).position({
                of: ucp_iframe.jQuery(this),
                my: 'center bottom',
                at: 'center top',
                collision: 'fit fit',
                within: keepwithin
              });
           }
        });

        ucp_iframe.jQuery('.ucp-row').on('mouseleave','.ucp-module',function(){
          ucp_iframe.jQuery('[data-module-id]').children('.ucp-module-controls').remove();
        });

        // Module Element Hover
        ucp_iframe.jQuery('.ucp-row').on('mouseenter','[data-element-type]',function(){
          if( disable_hover == false ){
            ucp_iframe.jQuery('[data-element-id="' + ucp_iframe.jQuery(this).attr('data-element-id') +'"]').prepend('<div class="ucp-element-controls"><div class="ucp-element-edit"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></div></div>');
          }
        });

        ucp_iframe.jQuery('.ucp-row').on('mouseleave','[data-element-type]',function(){
          ucp_iframe.jQuery('[data-element-id="' + ucp_iframe.jQuery(this).attr('data-element-id') +'"]').children('.ucp-element-controls').remove();
          ucp_iframe.jQuery('[data-element-id="' + ucp_iframe.jQuery(this).attr('data-element-id') +'"]').children('.ucp-editor-highlight').remove();
        });

        // Module/Element Edit
        ucp_iframe.jQuery('.container').on('click','.ucp-element-edit, .ucp-module-edit',function(event){

          editing_element_id = ucp_iframe.jQuery(this).closest( '[data-element-type]' ).attr('data-element-id');

          if( typeof editing_element_id == 'undefined'){
            editing_element_id = 0;
          }

          var module_id = ucp_iframe.jQuery(this).closest( '[data-module-type]' ).attr('data-module-id');

          ucp_open_module_editor(module_id,editing_element_id);

          $('.ucp-tooltip').tooltipster({
               animation: 'fade',
               delay: 0,
          });
        });

        // Module Clone
        ucp_iframe.jQuery('.container').on('click','.ucp-module-clone',function(event){
          event.stopPropagation();
          var module_id = ucp_iframe.jQuery(this).closest( '[data-module-type]' ).data('module-id');
          var module_type = ucp_iframe.jQuery(this).closest( '[data-module-type]' ).data('module-type');
          $module_parent = ucp_iframe.jQuery(this).closest( '[data-module-type]' ).parent();
          $new_module = ucp_iframe.jQuery('[data-module-id="'+module_id+'"]').clone().insertAfter(ucp_iframe.jQuery(this).closest( '[data-module-type]' ));

          var new_ucpmid = Math.floor(Math.random() * 100000);
          $new_module.attr('data-module-id','m'+new_ucpmid);
          $new_module.attr('id','ucp-m'+new_ucpmid);
          $('ucp-module').removeClass('ucp-module-editing');
          $new_module.addClass('ucp-module-editing');
          css_styles['ucp-m'+new_ucpmid]=ucp_clone_object(css_styles['ucp-'+module_id]);

          $new_module.find('[data-element-id]').each(function(){
            var new_ucpeid = Math.floor(Math.random() * 100000);
            ucp_iframe.jQuery(this).attr('data-element-id','e'+new_ucpeid);
          });

          rebuild_iframe_css();
          ucp_open_module_editor('m'+new_ucpmid,0);

        });

        // Module Delete
        ucp_iframe.jQuery('.container').on('click','.ucp-module-delete',function(event){
          var module_id = ucp_iframe.jQuery(this).closest( '[data-module-type]' ).data('module-id');
          var module_type = ucp_iframe.jQuery(this).closest( '[data-module-type]' ).data('module-type');
          ucp_iframe.jQuery('[data-module-id="'+module_id+'"]').remove();
          ucp_close_module();
        });

        $('#ucp-style-sidebar').on('click','.ucp-sidebar-module-close',function(){
          ucp_close_module();
        });
     }

     function ucp_close_module(){
       ucp_iframe.jQuery('[data-module-id]' ).removeClass('ucp-module-editing');
       editing_element_id = false;
       $('.ucp-sidebar-header').html('<img src="'+ucp_admin_editor_variables.ucp_plugin_url+'/images/ucp_icon_w.png" alt="UnderConstructionPage PRO" title="UnderConstructionPage PRO"><span class="ucp-sidebar-title">UCP Builder</span>');
       $('#ucp-style-sidebar').removeClass('ucp_sidebar_editing');
     }

     function update_ucp_editing_highlight(){
      ucp_iframe.jQuery('.ucp-editor-highlight-editing').width(ucp_iframe.jQuery('[data-element-id="' + editing_element_id +'"]').width());
      ucp_iframe.jQuery('.ucp-editor-highlight-editing').height(ucp_iframe.jQuery('[data-element-id="' + editing_element_id +'"]').height());
      ucp_iframe.jQuery('.ucp-editor-highlight-editing').css('top',ucp_iframe.jQuery('[data-element-id="' + editing_element_id +'"]').position().top);
      ucp_iframe.jQuery('.ucp-editor-highlight-editing').css('left',ucp_iframe.jQuery('[data-element-id="' + editing_element_id +'"]').position().left);
     }



     function ucp_save_template(reload_iframe,new_name,activate,export_template){
      ucp_iframe.jQuery('[data-module-id]' ).removeClass('ucp-module-editing');
      ucp_iframe.jQuery('[data-module-id]').children('.ucp-module-controls').remove();
      if(export_template){
        window.do_export_template = true;
      }

      template_ucp = $('#ucp-template-ucp').val();

      if(template_ucp == 'true'){
        var template_id = 0;
        var template_name = $('#ucp-template-name').val();
      } else if(new_name){
        var template_id = 0;
        var template_name = new_name;
      } else {
        var template_id = $('#ucp-template-id').val();
        var template_name = $('#ucp-template-name').val();
      }

      if(activate){
        var activate = 'true';
      } else {
        var activate = 'false';
      }

      $('body').append('<div id="ucp-editor-save-loader" style="display:none;"><div class="ucp-loader"><i class="fa fa-spinner fa-pulse fa-5x fa-fw margin-bottom"></i><div id="ucp-editor-save-loader-message">Saving template ... </div></div></div>');
      $('#ucp-editor-save-loader').fadeIn(500);
      var template_thumb = '';
      var html_background = ucp_iframe.jQuery('body').css('background-color');

      html2canvas(ucp_iframe.jQuery('body'), {
        letterRendering:true,
        background:html_background,
        onrendered: function(canvas) {
          template_thumb = canvas.toDataURL();
          template_thumb_parts = template_thumb.match(/.{1,262000}/g); //2Mb
          window.thumnail_parts = 100/template_thumb_parts.length;
          window.thumnail_parts_sent = 0;
          
          var custom_template_thumbnail = $('#ucp-template-thumbnail').val();
          if(custom_template_thumbnail && custom_template_thumbnail.length>0){
            
          } else {
            custom_template_thumbnail = '';
          }

          $('#ucp-editor-save-loader-message').html('Saving template ... 1%');

          ucp_iframe.jQuery('[data-html]').each(function(){            
            $(this).html( $(this).data('html').replace('@!','[').replace('!@',']') );
          });

          //Save template data
          $.ajax({
            url: ajaxurl,
            method: 'POST',
            crossDomain: true,
            dataType: 'json',
            data: {
              action:'ucp_editor_save',
              template_id:template_id,
              template_type:$('#ucp-template-type').val(),
              template_version:$('#ucp-template-version').val(),
              template_thumbnail:custom_template_thumbnail,
              template_tags:$('#ucp-template-tags').val(),
              template_desc:$('#ucp-template-desc').val(),
              template_page_title:$('#ucp-template-page-title').val(),
              template_page_desc:$('#ucp-template-page-description').val(),
              template_name:template_name,
              template_html:ucp_iframe.jQuery('#ucp-template').html(),
              activate:activate
            }
          }).success(function(response) {

            if(response.data.template_id && parseInt(response.data.template_id)>0){
              $('#ucp-template-id').val(response.data.template_id);
              var template_iframe_src = ucp_admin_editor_variables['ucp_home_url']+'/?ucp_template_preview&ucp_editing=true&template='+response.data.template_slug;

              if(template_iframe_src !== $('#ucp_editor_iframe').attr('src')){
                $('#ucp_editor_iframe').attr('src',template_iframe_src);
                $('#ucp-template-name').val(template_name);
                $('#ucp-template-ucp').val('false');
                //ucp_iframe.location=template_iframe_src;
                window.history.pushState('ucp_editor', 'UCP Editor', ucp_admin_editor_variables['ucp_admin_url']+'edit.php?page=ucp_editor&template='+response.data.template_slug);
              }

              if(reload_iframe){
                 ucp_iframe.location.reload(true);
                 var doingreload = false;
              }

              $('#ucp-sidebar-footer-menu').html('');
              $('#ucp-sidebar-footer-menu').hide();
              $('.ucp-sidebar-footer-button-save').attr('data-open','false');

            }

            //If everything went OK send thumbnail parts
            if(response.data.template_id && custom_template_thumbnail == '' && !$('#ucp-template-thumbnail-set').is(':checked') && template_thumb_parts.length > 0){
              ucp_save_thumbnail(template_thumb_parts,response.data.thumbnail_id);
              $('#ucp-template-thumbnail-preview-wrapper').remove();
            } else {
              if(custom_template_thumbnail != ''){
                $('#ucp-template-thumbnail-preview-wrapper').html('<input type="checkbox" style="vertical-align:text-bottom;" id="ucp-template-thumbnail-set" checked value="1" /> <label for="ucp-template-thumbnail-set" style="color:#F00; display:inline;">Using custom thumbnail:</label><img id="ucp-template-thumbnail-preview" src="'+custom_template_thumbnail+'" style="max-width:100%;" />');
              }
              $('#ucp-sidebar-footer-menu').html('');
              $('#ucp-sidebar-footer-menu').hide();
              $('.ucp-sidebar-footer-button-save').attr('data-open','false');
              $('#ucp-editor-save-loader').fadeOut(500, function() { $(this).remove(); });
            }
          }).error(function(a,m) {
            $('#ucp-sidebar-footer-menu').html('');
            $('#ucp-sidebar-footer-menu').hide();
            $('.ucp-sidebar-footer-button-save').attr('data-open','false');
            $('#ucp-editor-save-loader').fadeOut(500, function() { $(this).remove(); });
            alert('An error occured and the template could not be saved!' + type.responseText);
          });

        }
      });

      window.ucp_save_confirm = false;
    }

    function ucp_save_thumbnail(template_thumb_parts,thumbnail_id){
        function send_thumbail_part(thumb_part, thumb_part_data, thumbnail_id) {
            return $.ajax({
                    url: ajaxurl,
                    method: 'POST',
                    crossDomain: true,
                    dataType: 'json',
                    data: {
                      action:'ucp_editor_save_thumbnail',
                      thumbnail_id:thumbnail_id,
                      thumbnail_parts:template_thumb_parts.length,
                      thumbnail_part:thumb_part,
                      thumbnail_data:thumb_part_data
                    }
                  }).then(function(result) {
                if(result){
                  window.thumnail_parts_sent++;
                  $('#ucp-editor-save-loader-message').html('Saving template ... '+Math.round(window.thumnail_parts_sent*window.thumnail_parts)+'%');
                  return result;
                } else {
                  return $.Deferred().reject(thumb_part).promise();
                }
            }, function() {
                return thumb_part;
            });
        }


        var dfrd = $.Deferred();
        var part = dfrd.promise();

        $.each(template_thumb_parts, function(thumb_part, thumb_part_data) {
            part = part.then( function() {
                return send_thumbail_part(thumb_part, thumb_part_data, thumbnail_id);
            });
        });

        part.then(function() {
          if(window.do_export_template){
            export_template();
          } else {
            $('#ucp-editor-save-loader').fadeOut(500, function() { $(this).remove(); });
          }
        }, function(id) {
          if(window.do_export_template){
            export_template();
          } else {
            $('#ucp-editor-save-loader').fadeOut(500, function() { $(this).remove(); });
          }
        });

        dfrd.resolve();
    }

    function export_template(){
      $('#ucp-editor-save-loader-message').html('Exporting template ... ');
      var template_id = $('#ucp-template-id').val();
      $.ajax({
        url: ajaxurl,
        method: 'POST',
        crossDomain: true,
        dataType: 'json',
        data: {
          action:'ucp_editor_export',
          template_id:template_id
        }
      }).success(function(response) {
        if(response.data.template_path){
          window.location.assign(response.data.template_path);
        }

        $('#ucp-sidebar-footer-menu').html('');
        $('#ucp-sidebar-footer-menu').hide();
        $('.ucp-sidebar-footer-button-save').attr('data-open','false');
        $('#ucp-editor-save-loader').fadeOut(500, function() { $(this).remove(); });
      }).error(function(response) {
        $('#ucp-sidebar-footer-menu').html('');
        $('#ucp-sidebar-footer-menu').hide();
        $('.ucp-sidebar-footer-button-save').attr('data-open','false');
        $('#ucp-editor-save-loader').fadeOut(500, function() { $(this).remove(); });
        alert('An error occured and the template could not be exported!');
      });
    }

    $('.ucp-sidebar-footer-button').on('click',function(){
      if($(this).attr('data-open') == 'true'){
         $('#ucp-sidebar-footer-menu').hide();
         $(this).removeAttr('data-open');
         return;
      }
      var footer_menu = $(this).data('action');

      var footer_menu_html='';

      $(this).attr('data-open','true');

      switch(footer_menu){
        case 'devices':
        footer_menu_html+='<ul>';
        footer_menu_html+='<li class="ucp-device-select" data-device="desktop"><i class="fa fa-desktop" aria-hidden="true"></i> Desktop <span>full width preview</span></li>';
        footer_menu_html+='<li class="ucp-device-select" data-device="tablet"><i class="fa fa-tablet" aria-hidden="true"></i> Tablet <span>768px preview</span></li>';
        footer_menu_html+='<li class="ucp-device-select" data-device="phone"><i class="fa fa-mobile" aria-hidden="true"></i> Phone <span>360px preview</span></li>';
        footer_menu_html+='</ul>';
        break;
        case 'save':
        footer_menu_html+='<ul>';
        template_ucp = $('#ucp-template-ucp').val();

        if(template_ucp != 'true'){
          footer_menu_html+='<li class="ucp-export"><i class="fa fa-floppy-o" aria-hidden="true"></i> Export Template</li>';
        }

        footer_menu_html+='<li class="ucp-save-activate"><i class="fa fa-floppy-o" aria-hidden="true"></i><i class="fa fa-check fa-secondary" aria-hidden="true"></i> Save and Activate</li>';
        footer_menu_html+='<li class="ucp-save-as"><i class="fa fa-floppy-o" aria-hidden="true"></i><i class="fa fa-star fa-secondary" aria-hidden="true"></i> Save As</li>';

        if(template_ucp == 'true'){
          footer_menu_html+='<li class="ucp-save-as-div"><input type="text" name="ucp-save-as-name" id="ucp-save-as-name" value="'+$('#ucp-template-name').val()+'" /> <div class="ucp-sidebar-footer-button-saveas">Save</div></li>';
        } else {
          footer_menu_html+='<li class="ucp-save-as-div" style="display:none;"><input type="text" name="ucp-save-as-name" id="ucp-save-as-name" value="'+$('#ucp-template-name').val()+'" /> <div class="ucp-sidebar-footer-button-saveas">Save</div></li>';
          footer_menu_html+='<li class="ucp-save"><i class="fa fa-floppy-o" aria-hidden="true"></i> Save</li>';
        }
        footer_menu_html+='</ul>';
        break;
        case 'history':
        footer_menu_html+='<ul>';
        footer_menu_html+='<li class="ucp-trash-all"><i class="fa fa-trash-o" aria-hidden="true"></i> Remove all modules</li>';
        footer_menu_html+='<li class="ucp-trash-confirm-div" style="display:none;">Are you sure you want to remove all modules on the page?<br /><br /><div class="ucp-sidebar-footer-button-trash-confirm">Yes</div><div class="ucp-sidebar-footer-button-trash-cancel">Cancel</div></li>';
        footer_menu_html+='<li class="ucp-undo-changes"><i class="fa fa-history" aria-hidden="true"></i> Undo all changes</li>';
        footer_menu_html+='</ul>';
        break;
        case 'close':
        location.href=ucp_admin_editor_variables.ucp_admin_url+'options-general.php?page=ucp';
        break;
      }

      $('#ucp-sidebar-footer-menu').html(footer_menu_html);
      $('#ucp-sidebar-footer-menu').show();
    });

    $('#ucp-sidebar-footer-menu').on('click','.ucp-save',function(){
      ucp_save_template(false,false,false,false);
    });

    $('#ucp-sidebar-footer-menu').on('click','.ucp-export',function(){
      ucp_save_template(false,false,false,true);
    });

    $('#ucp-sidebar-footer-menu').on('click','.ucp-save-as',function(){
      $('.ucp-save-as-div').show();
    });

    $('#ucp-sidebar-footer-menu').on('click','.ucp-sidebar-footer-button-saveas',function(){
      ucp_save_template(false,$('#ucp-save-as-name').val(),false,false);
    });

    $('#ucp-sidebar-footer-menu').on('click','.ucp-save-activate',function(){
      ucp_save_template(false,false,true,false);
    });


    $('#ucp-sidebar-footer-menu').on('click','.ucp-device-select', function(){

      $('#ucp_editor_preview').removeClass('ucp-editor-preview-desktop');
      $('#ucp_editor_preview').removeClass('ucp-editor-preview-tablet');
      $('#ucp_editor_preview').removeClass('ucp-editor-preview-mobile');

      switch($(this).data('device')){
        case 'desktop':
        $('#ucp_editor_preview').addClass('ucp-editor-preview-desktop');
        break;
        case 'tablet':
        $('#ucp_editor_preview').addClass('ucp-editor-preview-tablet');
        break;
        case 'phone':
        $('#ucp_editor_preview').addClass('ucp-editor-preview-mobile');
        break;
      }



    });

    $('#ucp-sidebar-footer-menu').on('click','.ucp-undo-changes', function(){
      location.reload();
    });

    $('#ucp-sidebar-footer-menu').on('click','.ucp-trash-all', function(){
      $('.ucp-trash-confirm-div').show();
    });

    $('#ucp-sidebar-footer-menu').on('click','.ucp-sidebar-footer-button-trash-cancel', function(){
      $('.ucp-trash-confirm-div').hide();
    });

    $('#ucp-sidebar-footer-menu').on('click','.ucp-sidebar-footer-button-trash-confirm', function(){
      ucp_iframe.jQuery('.ucp-row').each(function(){
        ucp_iframe.jQuery(this).html('');
      });
      ucp_close_module();
      $('.ucp-trash-confirm-div').hide();
      $('#ucp-sidebar-footer-menu').hide();
      setup_ucpiFrame();

    });



    $('.ucp-sidebar-tabs li').on('click', function(){
      $( '.ucp-sidebar-tabs li' ).removeClass('active');
      $(this).addClass('active');
      $( '.ucp-sidebar-tab' ).hide();
      $( '#'+$(this).data('tab') ).show();
    });

    $('#ucp_reset').on('click',function(){
      $.post(ajaxurl, {action:'ucp_editor_reset',template:ucp_iframe.document.documentElement.innerHTML} , function(response) {
          location.reload();
        }, "json").always(function(){ $('#ucp_reset').css('opacity','1'); });
    });

// Utilities

      function rgb2hex(rgb){
       rgb = rgb.match(/^rgba?[\s+]?\([\s+]?(\d+)[\s+]?,[\s+]?(\d+)[\s+]?,[\s+]?(\d+)[\s+]?/i);
       return (rgb && rgb.length === 4) ? "#" +
        ("0" + parseInt(rgb[1],10).toString(16)).slice(-2) +
        ("0" + parseInt(rgb[2],10).toString(16)).slice(-2) +
        ("0" + parseInt(rgb[3],10).toString(16)).slice(-2) : '';
      }

      function ucp_refresh_color_picker(){
        $('.sm_color_picker').spectrum({
            preferredFormat: "rgb",
            showAlpha:true,
            allowEmpty:true,
            showInitial: true,
            showInput: true,
            move: function(color) {
                $(this).val(color.toRgbString());
                $(this).trigger('keyup');
            }
        });
      }


      function ucp_escapeHtml(text) {
          var map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
          };

          return text.replace(/[&<>"']/g, function(m) { return map[m]; });
        }

        function ucp_reload_module(module_id){

        }

        function video_url_parser(url){
          if(url.indexOf('youtube')>0){
            var regExp = /^.*((youtube\/)|(v\/)|(\/u\/\w\/)|(embed\/)|(watch\?))\??v?=?([^#\&\?]*).*/;
            var match = url.match(regExp);
            var youtube_id = (match&&match[7].length==11)? match[7] : false;
            return 'https://www.youtube.com/embed/'+youtube_id;
          } else if(url.indexOf('youtu.be')>0){
            var regExp = /^.*((youtu.be\/)|(v\/)|(\/u\/\w\/)|(embed\/)|(watch\?))\??v?=?([^#\&\?]*).*/;
            var match = url.match(regExp);
            var youtube_id = (match&&match[7].length==11)? match[7] : false;
            return 'https://www.youtube.com/embed/'+youtube_id;
          } else if(url.indexOf('vimeo')>0){
            var regExp = /^.*(vimeo\.com\/)((channels\/[A-z]+\/)|(groups\/[A-z]+\/videos\/))?([0-9]+)/;
            var match = url.match(regExp);
            var vimeo_id = (match&&match[5].length>5)? match[5] : false;
            return 'https://player.vimeo.com/video/'+vimeo_id;
          }
        }



        function ucp_array_unique(value, index, self) {
            return self.indexOf(value) === index;
        }

        function get_google_font_slug(name)
        {
            return name.toLowerCase().replace(/ /g,'_').replace(/[^\w-]+/g,'');
        }


        function ucp_clone_object(obj) {
            var copy;

            // Handle the 3 simple types, and null or undefined
            if (null == obj || "object" != typeof obj) return obj;

            // Handle Date
            if (obj instanceof Date) {
                copy = new Date();
                copy.setTime(obj.getTime());
                return copy;
            }

            // Handle Array
            if (obj instanceof Array) {
                copy = [];
                for (var i = 0, len = obj.length; i < len; i++) {
                    copy[i] = clone(obj[i]);
                }
                return copy;
            }

            // Handle Object
            if (obj instanceof Object) {
                copy = {};
                for (var attr in obj) {
                    if (obj.hasOwnProperty(attr)) copy[attr] = ucp_clone_object(obj[attr]);
                }
                return copy;
            }

            throw new Error("Unable to copy obj! Its type isn't supported.");
        }


          $('body').on('click', '.open-weglot-upsell', function(e) {
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
                                        $(this).siblings().find('span.ui-dialog-title').html(ucp_admin_editor_variables.weglot_dialog_upsell_title);
                                      },
                                      'close': function(event, ui) { },
                                      'autoOpen': false,
                                      'closeOnEscape': true
          });
          
          $(window).resize(function(e) {
            $('#weglot-upsell-dialog').dialog("option", "position", {my: "center", at: "center", of: window});
          });

          window.closeWeglotInstaller = function(e) {
            $('.weglot-installer-popup').remove();
          };
        
        
          jQuery('#install-weglot').on('click',function(e){
            $('#weglot-upsell-dialog').dialog('close');
            const urlParams = new URLSearchParams(window.location.search);
            jQuery('body').append('<div class="weglot-installer-popup"><iframe src="' + ucp_admin_editor_variables.weglot_install_url + '&editor=true" style="width:100%;height:100%;border:none;" /></div>');
            e.preventDefault();
            return false;
          });
        
          function ucp_fix_dialog_close(event, ui) {
            jQuery('.ui-widget-overlay').bind('click', function(){
              jQuery('#' + event.target.id).dialog('close');
            });
          } // ucp_fix_dialog_close

   });


})( jQuery );
