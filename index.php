<?php 
/*
Plugin Name: Vote Counter 
Plugin URI:  https://github.com/
Description: Counts the votes in a gravity form w ID 4 where the first field is checkboxes
Version:     1.0
Author:      Tom Woodward
Author URI:  http://bionicteaching.com
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Domain Path: /languages
Text Domain: my-toolset

*/
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );


add_action('wp_enqueue_scripts', 'vote_counter_load_scripts');

function vote_counter_load_scripts() {                           
    $deps = array('jquery');
    $version= '1.0'; 
    $in_footer = true;    
    wp_enqueue_script('vote-counter-main-js', plugin_dir_url( __FILE__) . 'js/vote-counter-main.js', $deps, $version, $in_footer); 
    wp_enqueue_style( 'vote-counter-main-css', plugin_dir_url( __FILE__) . 'css/vote-counter-main.css');
}


function vote_counter_shortcode() {
  $search_criteria = array();
  $sorting         = array();
  $paging          = array( 'offset' => 0, 'page_size' => 500);
  $total_count     = 0;
  $vote_1 = 0;
  $vote_2 = 0;
  $vote_3 = 0;
  $vote_4 = 0;
  $vote_5 = 0;
  $candidate_1 ='';
  $candidate_2 ='';
  $candidate_3 ='';
  $candidate_4 ='';
  $candidate_5 ='';
  $html ='';
  
  //IF I want to make it smarter at some point and move beyond 5 options etc.
  //$field = GFFormsModel::get_field( 1, 1 );
  //print("<pre>".print_r($field['choices'],true)."</pre>");
 
  $form_id = 4;//FORM ID
  
  $entries = GFAPI::get_entries($form_id, $search_criteria, $sorting, $paging, $total_count );
  foreach ($entries as $key => $value) {  
      //print("<pre>".print_r($value,true)."</pre>");
      if ($value['1.1']){
        $vote_1 = $vote_1+1;
        $candidate_1 = $value['1.1'];
      }
      if ($value['1.2']){
        $vote_2 = $vote_2+1;
        $candidate_2 = $value['1.2'];
      }
      if ($value['1.3']){
        $vote_3 = $vote_3+1;
        $candidate_3 = $value['1.3'];
      }
      if ($value['1.4']){
        $vote_4 = $vote_4+1;
        $candidate_4 = $value['1.4'];
      }
      if ($value['1.5']){
        $vote_5 = $vote_5+1;
        $candidate_5 = $value['1.5'];
      }     

   } 
     $html .= '<ul><li>' . $candidate_1 . ' - ' . $vote_1 . ' votes</li>';
     $html .= '<li>' . $candidate_2 . ' - ' . $vote_2 . ' votes</li>';
     $html .= '<li>' . $candidate_3 . ' - ' . $vote_3 . ' votes</li>';
     $html .= '<li>' . $candidate_4 . ' - ' . $vote_4 . ' votes</li>';
     $html .= '<li>' . $candidate_5 . ' - ' . $vote_5 . ' votes</li></ul>';
      

    return  $html;
}
add_shortcode( 'vote-counter', 'vote_counter_shortcode' );



//PREVENT DUPLICATE VOTING BY COOKIE -- yeah, not strong but it is what it is
//from https://wordpress.stackexchange.com/questions/244613/gravity-forms-skip-form-if-already-filled-out-using-cookie
add_action( 'gform_after_submission_4', 'wpse_set_submitted_cookie', 10, 2 );

function wpse_set_submitted_cookie( $entry, $form ) {

    // Set a third parameter to specify a cookie expiration time, 
    // otherwise it will last until the end of the current session.

    setcookie( 'wpse_form_submitted', 'true' );
}

add_action( 'template_redirect', 'wpse_protect_confirmation_page' );

function wpse_protect_confirmation_page() {
    if( is_page( 'vote' ) && isset( $_COOKIE['wpse_form_submitted'] ) ) {
        wp_redirect( home_url( '/already-voted/' ) );
        exit();
    }
}




//LOGGER -- like frogger but more useful

if ( ! function_exists('write_log')) {
   function write_log ( $log )  {
      if ( is_array( $log ) || is_object( $log ) ) {
         error_log( print_r( $log, true ) );
      } else {
         error_log( $log );
      }
   }
}

  //print("<pre>".print_r($a,true)."</pre>");
