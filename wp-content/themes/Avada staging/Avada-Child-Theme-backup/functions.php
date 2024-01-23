<?php

function theme_enqueue_styles() {
    wp_enqueue_style( 'child-style', get_stylesheet_directory_uri() . '/style.css', array( 'avada-stylesheet' ) );
}
add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles' );

function avada_lang_setup() {
	$lang = get_stylesheet_directory() . '/languages';
	load_child_theme_textdomain( 'Avada', $lang );
}
add_action( 'after_setup_theme', 'avada_lang_setup' );

function customJSFooter(){
?>
<script>
if(document.querySelector('.single-post')){
	document.querySelector('.title-heading-left').innerText = "You may also like ...";
}
</script>
<style>
.mega-contact-us-CTA a.mega-menu-link {
    color: #fff !important;
    font-family: ralewaysemi !important;
}
</style>
<?php
}

add_action( 'wp_footer', 'customJSFooter' );

add_action( 'widgets_init', 'main_menu_widgets_init' );

function main_menu_widgets_init() {
    register_sidebar( array(
        'name' => __( 'Mobile CTA', 'Avada' ),
        'id' => 'mobile-cta',
        'description' => __( 'Widgets in this area will be shown on all posts and pages.', 'Avada' ),
        'before_widget' => '<div id="%1$s" class="widget %2$s top_cta_mobile">',
		'after_widget'  => '</div>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
    ) );

}