<?php

/**
 * Plugin Name:       ShortCode Plugin
 * Plugin URI:        https://example.com/plugins/the-basics/
 * Description:       This is my Shorcode Plugin Which i can use in any wordpress website.
 * 
 * Version:           1.10.1
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Rabia Gull
 * Author URI:        https://author.example.com/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       shortcode-plugin
 * Domain Path:       /languages
 */
 



/// Basic Shortcode 
add_shortcode('headings', 'show_some_static_headings');

function show_some_static_headings() {
    return "<h1 style='color:red; font-size:34px; font-weight:bold;'>

    My Blogging site........
    </h1>";
}

/// Passing Parameter in shortcodes


add_shortcode("student", "handle_student_data");

function handle_student_data($attributes){
  $attributes = shortcode_atts(array(
     "name" => "Default Student",
     "email" => "Default Email"
   ), $attributes, "student");

   return "<h1> Student Data: Name - {$attributes['name']},  Email - {$attributes['email']}  </h1>";
}







// Shortcode with DB Operation
add_shortcode( 'list-posts', 'handle_posts_lists' );

function handle_posts_lists() {
    global $wpdb;

    $table_name = $wpdb->prefix . 'posts';

    $posts = $wpdb->get_results(
        "SELECT post_title 
         FROM {$table_name} 
         WHERE post_type = 'post' 
         AND post_status = 'publish'"
    );

    if ( empty( $posts ) ) {
        return '<p>No posts found.</p>';
    }

    $outputHtml = '<ul class="list-posts-shortcode">';

    foreach ( $posts as $post ) {
        $outputHtml .= '<li>' . esc_html( $post->post_title ) . '</li>';
    }

    $outputHtml .= '</ul>';

    return $outputHtml;
}

// Shortcode using WP_Query (FULL POST DATA)
add_shortcode( 'list-posts', 'handle_posts_lists_wp_query' );

function handle_posts_lists_wp_query( $attributes ) {

    // Get current page
    $paged = ( get_query_var('paged') ) ? get_query_var('paged') : 1;

    $attributes = shortcode_atts(
        array(
            'number' => 6,
        ),
        $attributes,
        'list-posts'
    );

    $query = new WP_Query(
        array(
            'posts_per_page' => intval( $attributes['number'] ),
            'post_status'    => 'publish',
            'paged'          => $paged,
        )
    );

    if ( $query->have_posts() ) {

        $outputHtml = '<div class="scp-post-wrapper">';

        while ( $query->have_posts() ) {
            $query->the_post();

            $outputHtml .= '<div class="scp-post-card">';

            // Featured Image
            if ( has_post_thumbnail() ) {
                $outputHtml .= '<div class="scp-post-image">
                    <a href="' . esc_url( get_permalink() ) . '">
                        ' . get_the_post_thumbnail( get_the_ID(), 'medium' ) . '
                    </a>
                </div>';
            }

            $outputHtml .= '<div class="scp-post-content">';

            $outputHtml .= '<h3 class="scp-post-title">
                <a href="' . esc_url( get_permalink() ) . '">' . esc_html( get_the_title() ) . '</a>
            </h3>';

            $outputHtml .= '<p class="scp-post-meta">
                By ' . esc_html( get_the_author() ) . ' | ' . esc_html( get_the_date() ) . '
            </p>';

            $outputHtml .= '<p class="scp-post-excerpt">' . esc_html( get_the_excerpt() ) . '</p>';

            $outputHtml .= '<a class="scp-read-more" href="' . esc_url( get_permalink() ) . '">Read More</a>';

            $outputHtml .= '</div></div>';
        }

        $outputHtml .= '</div>'; // wrapper

        /* Pagination */
        $outputHtml .= '<div class="scp-pagination">';
        $outputHtml .= paginate_links( array(
            'total'   => $query->max_num_pages,
            'current' => $paged,
        ) );
        $outputHtml .= '</div>';

        wp_reset_postdata();

        return $outputHtml;
    }

    return '<p>No posts found.</p>';
}


?>