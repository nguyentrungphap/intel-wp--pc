<?php

defined('ABSPATH') ?: die;

get_header('wpshopify');

global $post;

$Products = WP_Shopify\Factories\Render\Products\Products_Factory::build();
$DB_Products = WP_Shopify\Factories\DB\Products_Factory::build();

$args = apply_filters('wps_products_single_args', [
   'product_id' => $DB_Products->get_product_ids_from_post_ids($post->ID),
   'dropzone_product_buy_button' => '#product_buy_button',
   'dropzone_product_title' => '#product_title',
   'dropzone_product_description' => '#product_description',
   'dropzone_product_pricing' => '#product_pricing',
   'dropzone_product_gallery' => '#product_gallery',
   'hide_wrapper' => true,
   'limit' => 1
]);

$Products->products($args);

?>

<section class="wps-container">

   <?= do_action('wps_breadcrumbs') ?>

   <div class="wps-product-single row">

      <div class="wps-product-single-gallery col">
         <div id="product_gallery"></div>
      </div>

      <div class="wps-product-single-content col">

        <h1>this is Ethan's overwrite template</h1>
         <div id="product_title">
            <?php

            // Renders title server-side for SEO
            $Products->title([
               'post_id' => $post->ID,
               'render_from_server' => true
            ]);

            ?>
         </div>

         <div id="product_pricing"></div>

         <div id="product_description">

            <?php

            // Renders description server-side for SEO
            $Products->description([
               'post_id' => $post->ID,
               'render_from_server' => true
            ]);

            ?>

         </div>

         <div id="product_buy_button"></div>

      </div>

   </div>

   <?php

   // Renders description server-side for SEO
   // $Products->products([
   //    'items_per_row' => 4,
   //    'limit' => 4,
   //    'excludes' => ['description', 'buy-button'],
   //    'show_featured_only' => true,
   //    'link_to' => 'single'
   // ]);

   ?>

</section>

<?php


get_footer('wpshopify');
