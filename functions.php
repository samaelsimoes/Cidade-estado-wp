<?php

add_theme_support( 'post-thumbnails' ); 
add_theme_support( 'menus' );
add_theme_support( 'widgets' );

//permite upload de arquivos svg
function cc_mime_types($mimes) {
  $mimes['svg'] = 'image/svg+xml';
  return $mimes;
}

add_filter('upload_mimes', 'cc_mime_types');

//cria um menu de navegação
register_nav_menus(

  array( 'responsive' => __( 'responsive', 'theme' ) )
);

register_nav_menus(

  array( 'main-menu' => __( 'main-menu', 'theme' ) )
);

//adiciona campo de descrição das páginas
add_action('init', 'theme_custom_init');

//post supports
function theme_custom_init() {
  add_post_type_support( 'page', 'excerpt' );
  add_post_type_support( 'post', 'excerpt' );
}

//cria uma area de widgets
function theme_widgets_init() {
  register_sidebar( array (
  'name' => __( 'Widgets', 'theme' ),
  'id' => 'primary-widget-area',
  'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
  'after_widget' => "</li>",
  'before_title' => '<h3 class="widget-title">',
  'after_title' => '</h3>',
  ) );
}
add_action( 'widgets_init', 'theme_widgets_init' );

function custom_post_type_banner() {

  $labels = array(

    'name'                => _x( 'Banner', 'Post Type General Name', 'text_domain' ),
    'singular_name'       => _x( 'Banner', 'Post Type Singular Name', 'text_domain' ),
    'menu_name'           => __( 'Banner','text_domain' ),
  );

  $args = array(

    'label'               => __( 'Banner', 'text_domain' ),
    'description'         => __( 'Product information pages', 'text_domain' ),
    'labels'              => $labels,
    'taxonomies'          => array( ),
    'hierarchical'        => false,
    'public'              => true,
    'show_ui'             => true,
    'show_in_menu'        => true,
    'show_in_nav_menus'   => true,
    'show_in_admin_bar'   => true,
    'menu_position'       => 5,
    'can_export'          => true,
    'has_archive'         => true,
    'exclude_from_search' => false,
    'publicly_queryable'  => true,
    'capability_type'     => 'page',
    'supports'        => array('title', 'editor', 'post-formats')
  );
  
  register_post_type( 'Banner', $args );
}

add_action( 'init', 'custom_post_type_banner', 0 );

//cria campo de busca para estado e cidade
function get_regioes() {

  global $wpdb;

  $regioes = $wpdb->get_results("SELECT * FROM `estado_wp` ", OBJECT);

  foreach($regioes as $estado) {
    $tag['raw_values'][] = $estado;  
    $tag['values'][] = $estado;  
    $tag['labels'][] = $estado;
  } 
  print_r($tag);
}

add_action('wp_ajax_get_cities_by_ajax', 'get_municipios_estados_by_ajax_callback');
add_action('wp_ajax_nopriv_get_cities_by_ajax', 'get_municipios_estados_by_ajax_callback');

/** Cria custom select de estados para CF7 **/
/** [select name custom:estados] **/
function cf7_custom_select($tag, $unused){ //pegando estados

  $options = (array)$tag['options'];

  foreach ($options as $option) 
      if (preg_match('%^custom:([-0-9a-zA-Z_]+)$%', $option, $matches)) 
          $term = $matches[1];

  //check if post_type is set
  if(!isset($term))
      return $tag;

  if($term=="estado" ) {

    global $wpdb;
    $estados = $wpdb->get_results("SELECT * FROM `estado_wp` ", OBJECT);

    foreach ($estados as $estado) { 

      $tag['raw_values'][] = $estado->cod_uf;  
      $tag['values'][] = $estado->cod_uf;  
      $tag['labels'][] = $estado->estado;
    }
  }
   
  return $tag; 
}
add_filter( 'wpcf7_form_tag', 'cf7_custom_select', 10, 2);

function get_estados_cf7_by_ajax_callback() {// pegando cidades

  global $wpdb;

  $municipios = $wpdb->get_results("SELECT `cod_agencia`, municipio_limitrofes FROM `municipio_wp`where cod_uf = " .$_POST['estado'], OBJECT);

  $tag = array();

  foreach ($municipios as $cidade) { 
    $tag['raw_values'][] = $cidade->cod_agencia;  
    $tag['values'][] = $cidade->cod_agencia;  
    $tag['labels'][] = $cidade->municipio_limitrofes;
  }

  echo json_encode($tag);    
  wp_die();
}

add_action('wp_ajax_get_estados_cf7_by_ajax', 'get_estados_cf7_by_ajax_callback');
add_action('wp_ajax_nopriv_get_estados_cf7_by_ajax', 'get_estados_cf7_by_ajax_callback');
?>