<?php
/*
Plugin Name: Content Fetcher
Plugin URI: https://wpsupports.net/
Description: Fetch content from any website and show on your website using a simple shortcode
Version: 1.1
Author: Ruhul Amin
Text Domain: content-fetcher
*/
if ( !defined( 'HDOM_TYPE_ELEMENT' ) ){
    include_once('dom.php');
}

function content_fetcher( $atts ) {
    $a = shortcode_atts( array(
        'url' => '',
        'with' => '',
        'limit' => -1, // Default to -1 for no limit
    ), $atts );
    
    $url = $a['url'];
    $with = $a['with'];
    $limit = (int) $a['limit']; // Cast to integer
    
    // Parse the base URL for relative path handling
    $parsed_url = parse_url($url);
    $base_url = $parsed_url['scheme'] . '://' . $parsed_url['host'];
    
    $html = file_get_html($url);
    $output = ''; // Initialize an empty string to hold all elements
    $count = 0;   // Initialize a counter
    
    if($html){
        foreach($html->find($with) as $element) {
            // Break if the limit is reached (when limit is greater than 0)
            if ($limit > 0 && $count >= $limit) {
                break;
            }
            
            // Update image URLs
            foreach ($element->find('img') as $img) {
                $img->src = (strpos($img->src, 'http') === 0) ? $img->src : $base_url . '/' . ltrim($img->src, '/');
            }
            
            // Update link URLs
            foreach ($element->find('a') as $link) {
                $link->href = (strpos($link->href, 'http') === 0) ? $link->href : $base_url . '/' . ltrim($link->href, '/');
            }
            
            $output .= $element; // Append each element to the output string
            $count++; // Increment the counter
        }
        
        return $output; // Return all elements after the loop finishes
    }
    
    return 'Content could not be retrieved.';
}

add_shortcode( 'get_content_from', 'content_fetcher' );