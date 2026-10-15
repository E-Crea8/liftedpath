<?php
/**
 * Footer CTA 2 — work-page closing band with optional photograph.
 *
 * @package LiftedPath
 */

$image   = function_exists( 'get_field' ) ? get_field( 'image' ) : null;
$opacity = function_exists( 'get_field' ) ? get_field( 'overlay_opacity' ) : 72;
$eyebrow = liftedpath_block_string( function_exists( 'get_field' ) ? get_field( 'eyebrow' ) : '' );
$heading = liftedpath_block_string( function_exists( 'get_field' ) ? get_field( 'heading' ) : '' );
$text    = liftedpath_block_string( function_exists( 'get_field' ) ? get_field( 'text' ) : '' );
$button  = liftedpath_block_button( function_exists( 'get_field' ) ? get_field( 'button' ) : null );
$photo   = liftedpath_block_image( $image, 'liftedpath-hero', 'liftedpath-footer-cta__photo', true );
$is_preview = ! empty( $is_preview );

if ( null === $opacity || false === $opacity || '' === $opacity ) {
	$opacity = 72;
}
$opacity = max( 0, min( 100, (int) $opacity ) );
$alpha   = number_format( $opacity / 100, 2, '.', '' );
$has_photo = '' !== $photo;
$class     = 'liftedpath-section liftedpath-footer-cta' . ( $has_photo ? ' liftedpath-footer-cta--photo' : '' );
$style     = $has_photo ? '--footer-cta-overlay:' . $alpha . ';' : '';

$empty = '' === $heading && '' === $eyebrow && '' === $text && '' === $button && ! $has_photo;

if ( $empty ) {
	if ( $is_preview ) {
		liftedpath_block_wrapper_open( 'section', 'liftedpath-section liftedpath-footer-cta liftedpath-footer-cta--empty' );
		liftedpath_block_placeholder( __( 'Add a heading, button, or photograph.', 'liftedpath' ) );
		liftedpath_block_wrapper_close( 'section' );
	}
	return;
}
?>
<?php liftedpath_block_wrapper_open( 'section', $class, $style ); ?>
	<?php if ( $has_photo ) : ?>
		<?php echo $photo; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- attachment image is escaped. ?>
		<span class="liftedpath-footer-cta__shade" aria-hidden="true"></span>
	<?php endif; ?>
	<div class="liftedpath-section__inner liftedpath-footer-cta__inner">
		<div class="liftedpath-footer-cta__copy">
			<?php if ( '' !== $eyebrow ) : ?>
				<span class="liftedpath-footer-cta__eyebrow"><?php echo esc_html( $eyebrow ); ?></span>
			<?php endif; ?>
			<?php if ( '' !== $heading ) : ?>
				<h2><?php echo esc_html( $heading ); ?></h2>
			<?php endif; ?>
			<?php if ( '' !== $text ) : ?>
				<p><?php echo esc_html( $text ); ?></p>
			<?php endif; ?>
		</div>
		<?php echo $button; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- link markup is escaped. ?>
	</div>
<?php liftedpath_block_wrapper_close( 'section' ); ?>
