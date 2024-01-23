<?php
/*
 * UnderConstructionPage PRO
 * Interface - Tab - Design
 * (c) Web factory Ltd, 2015 - 2018
 */

class UCP_tab_design extends UCP {
  static function display() {
    global $wpdb;

    $options = self::get_options();
    $default_options = self::default_options();

    $img_path = UCP_PLUGIN_URL . 'images/thumbnails/';
    $uploads = wp_upload_dir();
    $custom_img_path = $uploads['baseurl'].'/ucp/';
    $themes = $wpdb->get_results('SELECT * FROM ' . $wpdb->ucp_templates);
    
    echo '<div id="tabs_design" class="ui-tabs ucp-tabs-2nd-level">';

      echo '<ul>';
      echo '<li><a href="#tab_pages">Your Pages</a></li>';
      echo '<li><a href="#tab_templates">Templates</a></li>';
      echo '</ul>';


      echo '<div id="tab_pages" class="ucp-tab-content">';
      
      if (!self::is_weglot_active()) {
        echo '<div class="ucp-notice-small"><p><b>NEW</b> - Make your under construction page and your website <b>multilingual</b> with the Weglot Translate plugin.<br>To enable this feature, <a href="#" class="open-weglot-upsell">install the Weglot Translate freemium plugin</a>.</p></div>';
      } else {
        echo '<div class="ucp-notice-small"><p>To configure <b>multilingual</b> options open <a href="' . admin_url('admin.php?page=weglot-settings') . '">Weglot configuration page</a>.</p></div>';
      }

      echo '<div class="ucp-design-header">';
        echo '<a href="'.admin_url('edit.php?page=ucp_editor').'" class="button button-primary" style="float:left;">Create a new page from blank template</a>';
      echo '</div>';

      $templates = get_option(UCP_TEMPLATES_KEY);

      foreach ($themes as $theme_data) {
        if(is_array($templates) && array_key_exists($theme_data->slug,$templates['templates']) && $theme_data->type != 'user'){
            $templates['templates'][$theme_data->slug]['status']='installed';
            $templates['templates'][$theme_data->slug]['installed_version']=$theme_data->version;
            continue;
          }

        $template_thumbnail_src = $custom_img_path .'template-'. $theme_data->slug . '.png';
        if($theme_data->slug == 'ucp-template-mad-designer-default'){
            $template_thumbnail_src = UCP_PLUGIN_URL.'images/template-mad-designer-default.png';
        }

        echo '<div class="ucp-thumb '.( $options['theme'] == $theme_data->slug?'active':'' ).'" data-theme-id="' . $theme_data->slug . '"><img src="'.$template_thumbnail_src.'" alt="' . stripslashes($theme_data->name) . '" title="' . stripslashes($theme_data->name) . '" />';
            echo '<div class="bottom">';
            echo '<span class="title">' . stripslashes($theme_data->name);
            if ( is_plugin_active('ucp-tools/ucp-tools.php') ) {
              echo ' - '.$theme_data->type;
            }
            echo '</span>';
            echo '<span class="tools">';
              echo '<a href="' . get_home_url() . '/?ucp_template_preview&template='.$theme_data->slug.'" target="_blank" class="button button-secondary">Preview</a>';
              echo '<a href="' . add_query_arg( array('action' => 'ucp_delete_template', 'template' => $theme_data->slug, 'redirect' => urlencode($_SERVER['REQUEST_URI'])), admin_url('admin.php')) . '" class="button button-secondary">Delete</a>';
              echo '<a href="' . admin_url( 'edit.php?page=ucp_editor&template='.$theme_data->slug) . '" class="button button-primary">Edit</a>';
              if ($theme_data->slug !== $options['theme']) {
                echo '<a href="' . add_query_arg(array('action' => 'ucp_activate_template', 'template' => $theme_data->slug, 'redirect' => urlencode($_SERVER['REQUEST_URI'])), admin_url('admin.php')) . '" class="button button-primary">Activate</a>';
              }
            echo '</span>';
          echo '</div>';
        echo '</div>';
      }

      echo '<div class="textleft" style="border-top: 1px solid #dddddd;padding-top: 10px;">';
        echo 'Import a template: ';
        echo '<input type="file" name="template-zip" value="" />';
        echo '<input type="submit" class="button button-primary" name="import_template_zip" value="Import Template" />';
      echo '</div>';

      echo '</div>';

      echo '<div style="display: none;" id="tab_templates" class="ucp-tab-content">';

      if (!self::is_weglot_active()) {
        echo '<div class="ucp-notice-small"><p><b>NEW</b> - Make your under construction page and your website <b>multilingual</b> with the Weglot Translate plugin.<br>To enable this feature, <a href="#" class="open-weglot-upsell">install the Weglot Translate freemium plugin</a>.</p></div>';
      } else {
        echo '<div class="ucp-notice-small"><p>To configure <b>multilingual</b> options open <a href="' . admin_url('admin.php?page=weglot-settings') . '">Weglot configuration page</a>.</p></div>';
      }
      
      echo '<div class="ucp-design-header">';
        echo '<a href="'.admin_url('edit.php?page=ucp_editor').'" class="button button-primary" style="float:left;">Create a new page from blank template</a>';
        echo '<div class="ucp-search-templates-wrapper"><input type="search" name="ucp-search-templates" id="ucp-search-templates" placeholder="Filter templates" val="" /><span style="display: none;" class="dashicons dashicons-search"></span></div>';
      echo '</div>';

      if(is_array($templates) && !is_plugin_active('ucp-tools/ucp-tools.php') ){
        foreach($templates['templates'] as $template_slug => $template){
          echo '<div class="ucp-thumb" data-template-name="' . stripslashes($template['name']) . '" data-template-tags="'.(isset($template['tags'])?$template['tags']:'').'" data-template-desc="'.(isset($template['desc'])?$template['desc']:'').'" data-theme-id="' . $template_slug . '"><img src="'.UCP_TEMPLATES_URL.'/app/wp-content/uploads/ucp/template-'. str_replace('ucp-template-','',$template_slug) . '.png" alt="' . $template['name'] . '" title="' . $template['name'] . '" />';
            
            echo '<div class="bottom">';
              if(isset($template['tags'])){
                echo '<span class="tags">' . $template['tags'] . '</span>';
              }
              echo '<span class="title">' . stripslashes($template['name']) . '<small> - '. (isset($template['installed_version'])?$template['installed_version']:$template['version']) .'</small></span>';
              if(isset($template['desc'])){
                echo '<span class="desc">' . $template['desc'] . '</span>';
              }
              echo '<span class="tools">';
                if( isset($template['status']) && $template['status'] == 'installed'){
                  echo '<a href="'.UCP_TEMPLATES_URL.'/?ucp_template_preview&template='.str_replace('ucp-template-','',$template_slug).'" target="_blank" class="button button-secondary">Preview</a>';
                  echo '<a href="' . admin_url('edit.php?page=ucp_editor&template='.$template_slug).'" class="button button-primary">New Page Based On This Template</a>';

                  if( version_compare($template['installed_version'],$template['version'],'<') ){
                    echo '<a href="' . add_query_arg(array('action' => 'ucp_install_template', 'template' => $template_slug, 'redirect' => urlencode($_SERVER['REQUEST_URI'])), admin_url('admin.php')) . '" class="button button-primary">Update to '.$template['version'].'</a>';
                  }
                } else {
                  echo '<a href="'.UCP_TEMPLATES_URL.'/?ucp_template_preview&template='.str_replace('ucp-template-','',$template_slug).'" target="_blank" class="button button-secondary">Preview</a>';
                  echo '<a class="button button-primary ucp-install-template" data-template-slug="'.$template_slug.'" data-template-name="'.stripslashes($template['name']).'" data-template-type="'.$template['type'].'" data-template-version="'.$template['version'].'" href="' . add_query_arg(array('action' => 'ucp_install_template', 'template' => $template_slug, 'redirect' => urlencode($_SERVER['REQUEST_URI'])), admin_url('admin.php')) . '">Install</a>';
                }
              echo '</span>';
            echo '</div>';
          echo '</div>';
        }
      }
      echo '<div class="textright"><a id="ucp-refresh-templates" class="button button-primary">Refresh Templates</a></div>';

      echo '</div>';
    echo '</div>';


    $tmp = md5(get_site_url());
    if ($tmp[0] < '6') {
      $tweet = 'I need more themes for the free Under Construction #wordpress plugin. When are they coming out? @webfactoryltd';
      $url = 'https://wordpress.org/plugins/under-construction-page/';
    } elseif ($tmp[0] < 'a') {
      $tweet = 'I need more themes for the free Under Construction Page #wordpress plugin. When are they coming out? @webfactoryltd';
      $url = 'https://underconstructionpage.com/';
    } else {
      $tweet = 'When will you make more themes for the free Under Construction Page plugin for #wordpress? @webfactoryltd';
      $url = 'https://underconstructionpage.com/';
    }
  } // display
} // class UCP_tab_design
