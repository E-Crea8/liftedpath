<?php
/**
 * Inner page hero.
 *
 * @package LiftedPath
 * @since 0.19.8
 */

$image   = function_exists( 'get_field' ) ? get_field( 'image' ) : null;
$video   = function_exists( 'get_field' ) ? get_field( 'video' ) : null;
$variant = function_exists( 'get_field' ) ? get_field( 'variant' ) : '';
$eyebrow = function_exists( 'get_field' ) ? get_field( 'eyebrow' ) : '';
$heading = function_exists( 'get_field' ) ? get_field( 'heading' ) : '';
$intro   = function_exists( 'get_field' ) ? get_field( 'intro' ) : '';
$crumb   = function_exists( 'get_field' ) ? get_field( 'breadcrumb_label' ) : '';
$current = function_exists( 'get_field' ) ? get_field( 'breadcrumb_current' ) : '';

$variant = is_string( $variant ) && 'work' === $variant ? 'work' : 'default';
$eyebrow = is_string( $eyebrow ) ? $eyebrow : '';
$heading = is_string( $heading ) ? $heading : '';
$intro   = is_string( $intro ) ? $intro : '';
$crumb   = is_string( $crumb ) && '' !== $crumb ? $crumb : __( 'Home', 'liftedpath' );
$current = is_string( $current ) ? trim( $current ) : '';
$photo      = liftedpath_block_image( $image, 'liftedpath-hero', 'liftedpath-page-hero__photo' );
$clip       = liftedpath_hero_video( $video, 'liftedpath-page-hero__video', liftedpath_block_poster( $image ) );
$is_preview = ! empty( $is_preview );
$empty      = '' === $heading && '' === $eyebrow && '' === $intro && '' === $photo && '' === $clip;
$class      = 'liftedpath-page-hero' . ( 'work' === $variant ? ' liftedpath-page-hero--work' : '' );

if ( $empty ) {
	if ( $is_preview ) {
		liftedpath_block_wrapper_open( 'section', 'liftedpath-page-hero liftedpath-page-hero--empty' );
		liftedpath_block_placeholder( __( 'Add a photograph or video, and a heading.', 'liftedpath' ) );
		liftedpath_block_wrapper_close( 'section' );
	}
	return;
}
?>
<?php liftedpath_block_wrapper_open( 'section', $class ); ?>
	<?php echo $photo; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- attachment image is escaped. ?>
	<?php echo $clip; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- helper escapes. ?>
	<div class="liftedpath-page-hero__shade" aria-hidden="true"></div>
	<div class="liftedpath-page-hero__content">
		<?php if ( '' !== $eyebrow && 'work' !== $variant ) : ?>
			<p class="liftedpath-page-hero__eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
		<?php endif; ?>
		<?php if ( '' !== $heading ) : ?>
			<h1><?php echo esc_html( $heading ); ?></h1>
		<?php endif; ?>
		<?php if ( '' !== $intro ) : ?>
			<p class="liftedpath-page-hero__intro"><?php echo esc_html( $intro ); ?></p>
		<?php endif; ?>
		<p class="liftedpath-page-hero__crumbs">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php echo esc_html( $crumb ); ?></a>
			<?php if ( '' !== $current ) : ?>
				<?php echo liftedpath_block_chevron(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static icon. ?>
				<span><?php echo esc_html( $current ); ?></span>
			<?php endif; ?>
		</p>
	</div>
<?php liftedpath_block_wrapper_close( 'section' ); ?>
