<?php
/**
 * Phrase loop.
 *
 * @package LiftedPath
 */

$phrases    = function_exists( 'get_field' ) ? liftedpath_marquee_phrases() : array();
$is_preview = ! empty( $is_preview );

if ( array() === $phrases ) {
	if ( $is_preview ) {
		liftedpath_block_wrapper_open( 'div', 'liftedpath-marquee liftedpath-marquee--empty' );
		liftedpath_block_placeholder( __( 'Add phrases, or add them in LiftedPath settings.', 'liftedpath' ) );
		liftedpath_block_wrapper_close( 'div' );
	}
	return;
}
?>
<?php liftedpath_block_wrapper_open( 'div', 'liftedpath-marquee' ); ?>
	<div class="liftedpath-marquee__track">
		<?php echo liftedpath_marquee_group( $phrases ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- helper escapes. ?>
		<?php echo liftedpath_marquee_group( $phrases, true ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- helper escapes. ?>
	</div>
<?php liftedpath_block_wrapper_close( 'div' ); ?>
