<?php
/**
* Plugin Name: Uncached
* Description: Prevents caching of scripts and stylesheets in browsers for site users (does not affect server caching)
* Version: 1.1
* Author: Tyxan Ltd
* Author URI: https://tyxan.com
* License: GPL2
*
*/


/*
* Plugins main functionality - disable caching if activated
*/
function tyxan_disable_user_caching_js($tag, $handle) {
  $options = get_option( 'tyxan_uncached' );
  if($options['is_activated']=="1"){
        $all_scripts = wp_scripts();
        $all_scripts = array_keys( $all_scripts->groups );

        foreach($all_scripts as $script) {
            if ($script === $handle) {
                return str_replace("' id", "&test=".time()."' id", $tag);
            }
        }

      
    }
    return $tag;

}
add_filter('script_loader_tag', 'tyxan_disable_user_caching_js', 10, 2);

function tyxan_disable_user_caching_css($tag, $handle) {
  $options = get_option( 'tyxan_uncached' );
  if($options['is_activated']=="1"){
       
         $all_styles = wp_styles();
        $all_styles = array_keys($all_styles->registered);
        foreach($all_styles as $style) {
            if ($style === $handle) {
                return str_replace("' media", "&test=".time()."' media", $tag);
            }
        }
    }
    return $tag;

}
add_filter('style_loader_tag', 'tyxan_disable_user_caching_css', 10, 2);



/*
* Add shortcut button to the top black bar menu
*/
add_action('admin_bar_menu', 'tyxan_add_shortcut_button', 100);
function tyxan_add_shortcut_button( $admin_bar ){
  global $pagenow;
  $options = get_option( 'tyxan_uncached' );
  if($options['is_activated']=="1"){
    $button_text = "Uncached Active";
    $button_class = "uncached_active";
  } else{
    $button_text = "Uncached Disabled";
    $button_class = "uncached_disabled";
  }
  $admin_bar->add_menu( array( 'id'=>'uncached_status','title'=>$button_text, 'href' => get_admin_url() . 'options-general.php?page=tyxan_uncached', 'meta' => array('class' => $button_class) ) );
}


/*
* Add settings page in settings menu
*/
function tyxan_uncached_add_settings_page() {
    add_options_page( 'Uncached Settings', 'Uncached Settings', 'manage_options', 'tyxan_uncached', 'tyxan_uncached_render_plugin_settings_page' );
}
add_action( 'admin_menu', 'tyxan_uncached_add_settings_page' );



/*
* Contents of registered settings page
*/
function tyxan_uncached_render_plugin_settings_page() {
    $all_styles = wp_styles();
    ?>
    <h2>Uncached Settings</h2>
    <form action="options.php" method="post">
        <?php 
        settings_fields( 'tyxan_uncached' );
        do_settings_sections( 'tyxan_uncached' ); ?>
        <input name="submit" class="button button-primary" type="submit" value="<?php esc_attr_e( 'Save' ); ?>" />
    </form>
    <?php
}

/*
* Register settings in the db
*/
function tyxan_uncached_register_settings() {
    register_setting( 'tyxan_uncached', 'tyxan_uncached', 'tyxan_uncached_validate' );
    add_settings_section( 'tyxan_uncached_settings', '', 'tyxan_uncached_plugin_section_text', 'tyxan_uncached' );
    add_settings_field( 'tyxan_uncached_is_activated', 'Active', 'tyxan_uncached_is_activated', 'tyxan_uncached', 'tyxan_uncached_settings' );
}
add_action( 'admin_init', 'tyxan_uncached_register_settings' );



/*
* Validate the checkbox
*/
function tyxan_uncached_validate( $input ) {
    if($input['is_activated'] == "0" || $input['is_activated'] == "1"){
        return $input;
    } else {
        $input['is_activated'] == 0;
        return $input;
    }  
}



/*
* Contents of registered settings page
*/
function tyxan_uncached_plugin_section_text() {
    echo '<p>Enable or disable adding time in ms to scripts to prevent browser caching</p>';
}

/*
* Contents of registered settings page
*/
function tyxan_uncached_is_activated() {
    $options = get_option( 'tyxan_uncached' );
    echo "<input id='tyxan_uncached_is_activated' name='tyxan_uncached[is_activated]' type='checkbox' value='1' ". checked( 1, $options['is_activated'], false )." />";
}

?>