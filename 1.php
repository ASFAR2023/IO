<?php
  /* Template Name: posts receive Template */


ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

header("Access-Control-Allow-Origin: https://ta3limy.net");
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Allow-Headers: Content-Type");

// // Check if the current URL matches the desired page URL
// if (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], 'https://www.ta3limy.net/post-list-frpm-folder/') !== false) {
//     // Check if this is a POST request
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      
      
      
  


require_once('/www/wwwroot/test.ta3limy.net/wp-load.php');
require_once('/www/wwwroot/test.ta3limy.net/wp-blog-header.php');
/*
https://ta3limy.net/wp-content/aldoros/
التربية الاخلاقية للصف الرابع المنهاج الاماراتي/حل درس ا

لسعادة مفتاح الحياة للصف الرابع المنهاج الاماراتي.webp

*/

function set_featured_image_from_external_url($url, $post_id){
// Add Featured Image to Post
$image_url        = $url; // Define the image URL here
$image_name       =  basename($image_url);
$upload_dir       = wp_upload_dir(); // Set upload folder
$image_data       = file_get_contents($image_url); // Get image data
$unique_file_name = wp_unique_filename( $upload_dir['path'], $image_name ); // Generate unique name
$filename         = basename( $unique_file_name ); // Create image file name

// Check folder permission and define file location
if( wp_mkdir_p( $upload_dir['path'] ) ) {
  $file = $upload_dir['path'] . '/' . $filename;
} else {
  $file = $upload_dir['basedir'] . '/' . $filename;
}

// Create the image  file on the server
file_put_contents( $file, $image_data );

// Check image file type
$wp_filetype = wp_check_filetype( $filename, null );

// Set attachment data
$attachment = array(
    'post_mime_type' => $wp_filetype['type'],
    'post_title'     => sanitize_file_name( $filename ),
    'post_content'   => '',
    'post_status'    => 'inherit'
);

// Create the attachment
$attach_id = wp_insert_attachment( $attachment, $file, $post_id );

// Include image.php
require_once(ABSPATH . 'wp-admin/includes/image.php');

// Define attachment metadata
$attach_data = wp_generate_attachment_metadata( $attach_id, $file );

// Assign metadata to attachment
wp_update_attachment_metadata( $attach_id, $attach_data );

// And finally assign featured image to post
set_post_thumbnail( $post_id, $attach_id );

}


if (function_exists('wp_insert_post') && function_exists('term_exists')) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Get the values from the form POST
        $fileLink = $_POST['file_link'];
        $post_title = isset($_POST['post_title']) ? sanitize_text_field($_POST['post_title']) : '';
        $post_content = isset($_POST['post_content']) ? wp_kses_post($_POST['post_content']) : '';
        $post_content = wp_kses_post($_POST['post_content']) . '';
        $post_category = isset($_POST['post_category']) ? sanitize_text_field($_POST['post_category']) : '';
        $folderName = isset($_POST['post_folderName']) ? sanitize_text_field($_POST['post_folderName']) : '';

//  echo $fileLink;

//  echo file_get_contents($fileLink); // Get image data


        if (!empty($post_title) && !empty($post_content)) {
            // Custom database query to check if post title exists
            global $wpdb;
            $post_exists = $wpdb->get_var($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE post_title = %s", $post_title));


$category = get_term_by('name', $post_category, 'category');
    $category_id = $category->term_id;

            if (!$post_exists) {
                // Continue with post creation
                $post_data = array(
                    'post_title' => $post_title,
                    'post_content' => $post_content,
                    'post_status' => 'pending', // Set post status to "draft"
                    'post_category' => array($category_id), // Assign the category
                 //  'tags_input' => array($post_category), // Assign the tag
                );

                // Insert the post
                $post_id = wp_insert_post($post_data);




    
    $attach_id = set_featured_image_from_external_url( $fileLink, $post_id );


            update_post_meta($post_id, 'rank_math_focus_keyword', strtolower(get_the_title($post_id)));

    
                if (!is_wp_error($post_id)) {
                    // Post created successfully
                    $response = array(
                        'status' => 'success',
                        'message2' => 'Image uploaded successfully. Attachment ID: ' . $attach_id,

                        'message' => 'Draft post created successfully. Post ID: ' . $post_id,
                    );
                } else {
                    // Error creating post
                    $response = array(
                        'status' => 'error',
                        'message' => 'Error creating draft post: ' . $post_id->get_error_message(),
                    );
                }
                
                
                
            } else {
                // Post title already exists
                $response = array(
                    'status' => 'already exists',
                    'message' => 'A post with the same title already exists.',
                );
            }
            
        } else {
            // Handle missing or empty fields
            $response = array(
                'status' => 'error',
                'message' => 'Missing or empty fields in the POST request.',
            );
        }

        header('Content-Type: application/json');
        echo json_encode($response);
        
        
        
        
    }
} else {
    // WordPress functions are not available
    $response = array(
        'status' => 'error',
        'message' => 'WordPress functions are not available.',
    );

    // Return the response as JSON
    header('Content-Type: application/json');
    echo json_encode($response);
}

  }
// } else {
//     // If the page URL doesn't match, return a 403 Forbidden status
//     header('HTTP/1.0 403 Forbidden');
//     exit('Access is forbidden for this page.');
// }
