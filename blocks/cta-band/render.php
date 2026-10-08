<?php
/**
 * Closing band.
 *
 * @package LiftedPath
 */

$tone    = function_exists( 'get_field' ) ? get_field( 'tone' ) : 'photo';
$image   = function_exists( 'get_field' ) ? get_field( 'image' ) : null;
$eyebrow = function_exists( 'get_field' ) ? get_field( 'eyebrow' ) : '';
$heading = function_exists( 'get_field' ) ? get_field( 'heading' ) : '';
$text    = function_exists( 'get_field' ) ? get_field( 'text' ) : '';
$button  = function_exists( 'get_field' ) ? get_field( 'button' ) : null;

$tone    = is_string( $tone ) && in_array( $tone, array( 'photo', 'teal', 'paper' ), true ) ? $tone : 'photo';
$eyebrow = is_string( $eyebrow ) ? $eyebrow : '';
$heading = is_string( $heading ) ? $heading : '';
$text    = is_string( $text ) ? $text : '';
$photo   = ( 'photo' === $tone ) ? liftedpath_block_image( $image, 'liftedpath-hero', 'liftedpath-cta__photo' ) : '';
$is_preview = ! empty( $is_preview );
$empty      = '' === $heading && '' === $eyebrow && '' === $text && '' === $photo && '' === liftedpath_block_button( $button );

if ( $empty ) {
	if ( $is_preview ) {
		liftedpath_block_wrapper_open( 'section', 'liftedpath-cta liftedpath-cta--empty' );
		liftedpath_block_placeholder( __( 'Add a heading and a button.', 'liftedpath' ) );
		liftedpath_block_wrapper_close( 'section' );
	}
	return;
}
?>
<?php liftedpath_block_wrapper_open( 'section', 'liftedpath-cta liftedpath-cta--' . $tone ); ?>
	<?php echo $photo; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- attachment image is escaped. ?>
	<div class="liftedpath-cta__content">
		<?php if ( '' !== $eyebrow ) : ?>
			<p class="liftedpath-cta__eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
		<?php endif; ?>
		<?php if ( '' !== $heading ) : ?>
			<h2><?php echo esc_html( $heading ); ?></h2>
		<?php endif; ?>
		<?php if ( '' !== $text ) : ?>
			<p><?php echo esc_html( $text ); ?></p>
		<?php endif; ?>
		<?php echo liftedpath_block_button( $button ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- helper escapes. ?>
	</div>
<?php liftedpath_block_wrapper_close( 'section' ); ?>
