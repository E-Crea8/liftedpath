<?php
/**
 * Home hero.
 *
 * @package LiftedPath
 */

$image     = function_exists( 'get_field' ) ? get_field( 'image' ) : null;
$video     = function_exists( 'get_field' ) ? get_field( 'video' ) : null;
$portraits = function_exists( 'get_field' ) ? get_field( 'portraits' ) : array();
$eyebrow   = function_exists( 'get_field' ) ? get_field( 'eyebrow' ) : '';
$heading   = function_exists( 'get_field' ) ? get_field( 'heading' ) : '';
$intro     = function_exists( 'get_field' ) ? get_field( 'intro' ) : '';
$button    = function_exists( 'get_field' ) ? get_field( 'button' ) : null;
$figure    = function_exists( 'get_field' ) ? get_field( 'figure' ) : '';
$label     = function_exists( 'get_field' ) ? get_field( 'label' ) : '';
$detail    = function_exists( 'get_field' ) ? get_field( 'detail' ) : '';

$eyebrow = is_string( $eyebrow ) ? $eyebrow : '';
$heading = is_string( $heading ) ? $heading : '';
$intro   = is_string( $intro ) ? $intro : '';
$figure  = is_string( $figure ) ? $figure : '';
$label   = is_string( $label ) ? $label : '';
$detail  = is_string( $detail ) ? $detail : '';

$portrait_ids = liftedpath_portrait_ids( $portraits );
$photo        = liftedpath_block_image( $image, 'liftedpath-hero', 'liftedpath-hero__photo' );
$clip         = liftedpath_hero_video( $video, 'liftedpath-hero__video', liftedpath_block_poster( $image ) );
$is_preview   = ! empty( $is_preview );
$empty        = '' === $heading && '' === $eyebrow && '' === $intro && '' === $photo && '' === $clip && array() === $portrait_ids;

if ( $empty ) {
	if ( $is_preview ) {
		liftedpath_block_wrapper_open( 'section', 'liftedpath-hero liftedpath-hero--empty' );
		liftedpath_block_placeholder( __( 'Add a photograph or video, a heading, and a button.', 'liftedpath' ) );
		liftedpath_block_wrapper_close( 'section' );
	}
	return;
}
?>
<?php liftedpath_block_wrapper_open( 'section', 'liftedpath-hero' ); ?>
	<?php echo $photo; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- attachment image is escaped. ?>
	<?php echo $clip; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- helper escapes. ?>
	<div class="liftedpath-hero__shade" aria-hidden="true"></div>
	<div class="liftedpath-hero__content">
		<?php if ( '' !== $eyebrow || array() !== $portrait_ids ) : ?>
			<p class="liftedpath-hero__eyebrow<?php echo array() !== $portrait_ids ? ' has-portraits' : ''; ?>">
				<?php if ( array() !== $portrait_ids ) : ?>
					<span class="liftedpath-hero__portraits">
						<?php
						foreach ( $portrait_ids as $portrait_id ) {
							echo liftedpath_block_image( $portrait_id, 'thumbnail', 'liftedpath-hero__portrait', true ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						}
						?>
					</span>
				<?php endif; ?>
				<?php echo esc_html( $eyebrow ); ?>
			</p>
		<?php endif; ?>
		<?php if ( '' !== $heading ) : ?>
			<h1><?php echo esc_html( $heading ); ?></h1>
		<?php endif; ?>
		<?php if ( '' !== $intro ) : ?>
			<p class="liftedpath-hero__intro"><?php echo esc_html( $intro ); ?></p>
		<?php endif; ?>
		<?php echo liftedpath_block_button( $button ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- helper escapes. ?>
	</div>
	<?php if ( '' !== $figure || '' !== $label || '' !== $detail ) : ?>
		<aside class="liftedpath-hero__stat">
			<?php if ( '' !== $figure ) : ?>
				<strong><?php echo esc_html( $figure ); ?></strong>
			<?php endif; ?>
			<?php if ( '' !== $label ) : ?>
				<span><?php echo esc_html( $label ); ?></span>
			<?php endif; ?>
			<?php if ( '' !== $detail ) : ?>
				<small><?php echo esc_html( $detail ); ?></small>
			<?php endif; ?>
		</aside>
	<?php endif; ?>
<?php liftedpath_block_wrapper_close( 'section' ); ?>
