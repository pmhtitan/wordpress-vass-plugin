<?php
/**
 * Plugins primary file, in charge of including all other dependencies.
 *
 * @package Countries Shortcode JR
 *
 * @wordpress-plugin
 * Plugin Name: Countries Shortcode JR
 * Plugin URI:
 * Description: Imprime información de los países europeos en un listado.
 * Author: Pablo Moras
 * Version: 0.1
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Author URI: https://www.linkedin.com/in/pablo-moras/
 * Text Domain: countries-jr
 */

// Comprueba que el archivo no puede ser accedido de forma directa.
if (!defined('ABSPATH')) {
    die('We\'re sorry, but you can not directly access this file.');
}

/*
* Creamos nuestro CPT
*/ 
function countries_post_type() {
 
// Definimos las UI labels para el Custom Post Type
    $labels = array(
        'name'                => _x( 'Countries', 'Post Type General Name', 'twentytwenty' ),
        'singular_name'       => _x( 'Country', 'Post Type Singular Name', 'twentytwenty' ),
        'menu_name'           => __( 'Countries', 'twentytwenty' ),
        'parent_item_colon'   => __( 'Parent Country', 'twentytwenty' ),
        'all_items'           => __( 'All Countries', 'twentytwenty' ),
        'view_item'           => __( 'View Country', 'twentytwenty' ),
        'search_items'        => __( 'Search Country', 'twentytwenty' ),
        'not_found'           => __( 'Not Found', 'twentytwenty' ),
        'not_found_in_trash'  => __( 'Not found in Trash', 'twentytwenty' ),
    );
     
// Definimos el resto de opciones para el Custom Post Type     
    $args = array(
        'label'               => __( 'Countries', 'twentytwenty' ),
        'description'         => __( 'Country news and reviews', 'twentytwenty' ),
        'labels'              => $labels,
        'supports'            => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'comments', 'revisions', 'custom-fields', ),
        'taxonomies'          => array( 'genres' ),
        'hierarchical'        => false,
        'public'              => false,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'show_in_nav_menus'   => true,
        'show_in_admin_bar'   => true,
        'menu_position'       => 7,
        'can_export'          => true,
        'has_archive'         => true,
        'exclude_from_search' => false,
        'publicly_queryable'  => true,
        'capability_type'     => 'post',
        'show_in_rest' => true,
 
    );
     
    // Registramos el Custom Post Type
    register_post_type( 'Countries', $args ); 
}

function insert_post_countries(){

    $url = "https://restcountries.com/v3.1/region/europe/?fields=name,capital,population";
    $arguments =  array(
        'method' => 'GET',
    );
    // Recogemos la peticion (GET) a la API, nos devolverá un JSON y será encapsulado en un objeto WP vitaminado
    $response = wp_remote_get($url,$arguments);

    if ( is_wp_error( $response ) ) {
        $error_message = $response->get_error_message();
        return "Error al cargar : $error_message";
    } else {
        $countries_data_decode = json_decode($response['body']);

        foreach($countries_data_decode as $item){
            $post_title           = $item->name->common; 
            $country_capital      = $item->capital[0]; // la api devuelve un array adicional, asi que accedemos a este.
            $country_population   = number_format($item->population,0,'.', '.'); // formateado con separador de miles
            
            $new_post = [
                'post_title' => $post_title,
                'post_type'  => 'countries',
                'post_status' => 'publish', 
                'meta_input' => ['country_capital' => $country_capital, 'country_population' => $country_population]
            ];

            wp_insert_post($new_post);
        }
    }
   
}

function callback_countries_shortcode()
{
    function get_countries()
    {
        $url = "https://restcountries.com/v3.1/region/europe/?fields=name,capital,population";
        $arguments =  array(
            'method' => 'GET',
        );
        $response = wp_remote_get($url,$arguments);

        if ( is_wp_error( $response ) ) {
            $error_message = $response->get_error_message();
            return "Error al cargar : $error_message";
        } else {
            $contenido = "";
            $countries_data_decode = json_decode($response['body']);

            foreach($countries_data_decode as $item){
                $country_name         = $item->name->common; // post title
                $country_capital      = $item->capital[0]; // author
                $country_population   = number_format($item->population,0,'.', '.'); // up votes

                $contenido .= "<ul>";
                $contenido .= "<li>" . $country_name .  "</li>";
                $contenido .= "<li>" . $country_capital .  "</li>";
                $contenido .= "<li>" . $country_population .  "</li>";
                $contenido .= "</ul>";
            }

            return $contenido;
        }
    }
}

 
/* Hook into the 'init' action so that the function
* Containing our post type registration is not 
* unnecessarily executed. 
*/
 
add_action( 'init', 'countries_post_type', 0 );

add_shortcode('vass-wp-countries', 'get_countries');

add_action('init', 'callback_countries_shortcode');
add_action('init', 'insert_post_countries');

?>