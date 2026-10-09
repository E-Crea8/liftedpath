<?php
/**
 * About story: copy beside one photograph.
 *
 * @package LiftedPath
 * @since 0.19.8
 */

$eyebrow    = liftedpath_block_string( function_exists( 'get_field' ) ? get_field( 'eyebrow' ) : '' );
$heading    = liftedpath_block_string( function_exists( 'get_field' ) ? get_field( 'heading' ) : '' );
$body       = liftedpath_block_string( function_exists( 'get_field' ) ? get_field( 'body' ) : '' );
$image      = function_exists( 'get_field' ) ? get_field( 'image' ) : 0;
$alt        = liftedpath_block_string( function_exists( 'get_field' ) ? get_field( 'image_alt' ) : '' );
$is_preview = ! empty( $is_preview );
$paragraphs = liftedpath_block_paragraphs( $body );
$photo_id   = liftedpath_attachment_id( $image );
$photo_attr = array( 'class' => 'liftedpath-story__image' );

if ( '' !== $alt ) {
	$photo_attr['alt'] = $alt;
}

$photo_html = $photo_id > 0 ? wp_get_attachment_image( $photo_id, 'liftedpath-portrait', false, $photo_attr ) : '';
$photo      = is_string( $photo_html ) ? $photo_html : '';

$empty = '' === $eyebrow && '' === $heading && array() === $paragraphs && '' === $photo;

if ( $empty ) {
	if ( $is_preview ) {
		liftedpath_block_wrapper_open( 'section', 'liftedpath-section liftedpath-story liftedpath-story--empty' );
		liftedpath_block_placeholder( __( 'Add the story heading, paragraphs, and photograph.', 'liftedpath' ) );
		liftedpath_block_wrapper_close( 'section' );
	}
	return;
}
?>
<?php liftedpath_block_wrapper_open( 'section', 'liftedpath-section liftedpath-story' ); ?>
	<div class="liftedpath-section__inner liftedpath-story__layout">
		<div class="liftedpath-story__copy">
			<?php if ( '' !== $eyebrow ) : ?>
				<p class="liftedpath-eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
			<?php endif; ?>
			<?php if ( '' !== $heading ) : ?>
				<h2><?php echo esc_html( $heading ); ?></h2>
			<?php endif; ?>
			<?php foreach ( $paragraphs as $paragraph ) : ?>
				<p><?php echo esc_html( $paragraph ); ?></p>
			<?php endforeach; ?>
		</div>
		<?php if ( '' !== $photo ) : ?>
			<div class="liftedpath-story__photo"><?php echo $photo; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- attachment image with escaped attributes. ?></div>
		<?php endif; ?>
	</div>
<?php liftedpath_block_wrapper_close( 'section' ); ?>
