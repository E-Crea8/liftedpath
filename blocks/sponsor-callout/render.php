<?php
/**
 * Sponsorship amount strip.
 *
 * @package LiftedPath
 */

$figure = liftedpath_block_string( function_exists( 'get_field' ) ? get_field( 'figure' ) : '' );
$text   = liftedpath_block_string( function_exists( 'get_field' ) ? get_field( 'text' ) : '' );
$link   = function_exists( 'get_field' ) ? get_field( 'link' ) : null;
$action = '';

if ( is_array( $link ) && ! empty( $link['url'] ) && ! empty( $link['title'] ) ) {
	$target = '';
	$rel    = '';

	if ( isset( $link['target'] ) && '_blank' === $link['target'] ) {
		$target = ' target="_blank"';
		$rel    = ' rel="noopener noreferrer"';
	}

	$action = '<a class="liftedpath-text-link" href="' . esc_url( $link['url'] ) . '"' . $target . $rel . '>' . esc_html( $link['title'] ) . '</a>';
}
$is_preview = ! empty( $is_preview );
$empty      = '' === $figure && '' === $text && '' === $action;

if ( $empty ) {
	if ( $is_preview ) {
		liftedpath_block_wrapper_open( 'section', 'liftedpath-section liftedpath-sponsor liftedpath-sponsor--empty' );
		liftedpath_block_placeholder( __( 'Add an amount and a link.', 'liftedpath' ) );
		liftedpath_block_wrapper_close( 'section' );
	}
	return;
}
?>
<?php liftedpath_block_wrapper_open( 'section', 'liftedpath-section liftedpath-sponsor' ); ?>
	<div class="liftedpath-section__inner">
		<p class="liftedpath-sponsor__row">
			<?php if ( '' !== $figure ) : ?>
				<strong><?php echo esc_html( $figure ); ?></strong>
			<?php endif; ?>
			<?php if ( '' !== $text ) : ?>
				<span><?php echo esc_html( $text ); ?></span>
			<?php endif; ?>
			<?php echo $action; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- link markup is escaped. ?>
		</p>
	</div>
<?php liftedpath_block_wrapper_close( 'section' ); ?>
