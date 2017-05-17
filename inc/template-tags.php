<?php
/**
 * Custom template tags for this theme
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package anatta
 */

if ( ! function_exists( 'anatta_posted_on' ) ) :
/**
 * Prints HTML with meta information for the current post-date/time and author.
 */
function anatta_posted_on() {
	$time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
	if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
		$time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time>' . esc_html__(' and Updated on ', 'anatta') . '<time class="updated" datetime="%3$s">%4$s</time>';
	}

	$time_string = sprintf( $time_string,
		esc_attr( get_the_date( 'c' ) ),
		esc_html( get_the_date() ),
		esc_attr( get_the_modified_date( 'c' ) ),
		esc_html( get_the_modified_date() )
	);

	$posted_on = sprintf(
		esc_html_x( 'Posted on %s', 'post date', 'anatta' ), $time_string );

	$byline = sprintf(
		esc_html_x( 'by %s', 'post author', 'anatta' ),
		'<span class="author vcard"><a class="url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . esc_html( get_the_author() ) . '</a></span>'
	);

	echo '<p class="posted-on">' . $posted_on . '</span><span class="byline"> ' . $byline . '</p>'; // WPCS: XSS OK.

}
endif;

if ( ! function_exists( 'anatta_entry_footer' ) ) :
/**
 * Prints HTML with meta information for the categories, tags and comments.
 */
function anatta_entry_footer() {
	// Hide category and tag text for pages.
	if ( 'post' === get_post_type() ) {
		/* translators: used between list items, there is a space after the comma */
		$categories_list = get_the_category_list( esc_html__( ', ', 'anatta' ) );
		if ( $categories_list && anatta_categorized_blog() ) {
			printf( '<p class="cat-links">' . esc_html__( 'Posted in %1$s', 'anatta' ) . '</p>', $categories_list ); // WPCS: XSS OK.
		}

		/* translators: used between list items, there is a space after the comma */
		$tags_list = get_the_tag_list( '', esc_html__( ', ', 'anatta' ) );
		if ( $tags_list ) {
			printf( '<p class="tags-links">' . esc_html__( 'Tagged %1$s', 'anatta' ) . '</p>', $tags_list ); // WPCS: XSS OK.
		}
	}

	if ( ! is_single() && ! post_password_required() && ( comments_open() || get_comments_number() ) ) {
		echo '<span class="comments-link">';
		/* translators: %s: post title */
		comments_popup_link( sprintf( wp_kses( __( 'Leave a Comment<span class="screen-reader-text"> on %s</span>', 'anatta' ), array( 'span' => array( 'class' => array() ) ) ), get_the_title() ) );
		echo '</span>';
	}

	edit_post_link(
		sprintf(
			/* translators: %s: Name of current post */
			esc_html__( 'Edit %s', 'anatta' ),
			the_title( '<span class="screen-reader-text">"', '"</span>', false )
		),
		'<span class="edit-link">',
		'</span>'
	);
}
endif;

/**
 * Returns true if a blog has more than 1 category.
 *
 * @return bool
 */
function anatta_categorized_blog() {
	if ( false === ( $all_the_cool_cats = get_transient( 'anatta_categories' ) ) ) {
		// Create an array of all the categories that are attached to posts.
		$all_the_cool_cats = get_categories( array(
			'fields'     => 'ids',
			'hide_empty' => 1,
			// We only need to know if there is more than one category.
			'number'     => 2,
		) );

		// Count the number of categories that are attached to the posts.
		$all_the_cool_cats = count( $all_the_cool_cats );

		set_transient( 'anatta_categories', $all_the_cool_cats );
	}

	if ( $all_the_cool_cats > 1 ) {
		// This blog has more than 1 category so anatta_categorized_blog should return true.
		return true;
	} else {
		// This blog has only 1 category so anatta_categorized_blog should return false.
		return false;
	}
}

/**
 * Flush out the transients used in anatta_categorized_blog.
 */
function anatta_category_transient_flusher() {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	// Like, beat it. Dig?
	delete_transient( 'anatta_categories' );
}
add_action( 'edit_category', 'anatta_category_transient_flusher' );
add_action( 'save_post',     'anatta_category_transient_flusher' );
