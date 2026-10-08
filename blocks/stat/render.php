<?php
/**
 * One figure.
 *
 * @package LiftedPath
 */

$figure = function_exists( 'get_field' ) ? get_field( 'figure' ) : '';
$label  = function_exists( 'get_field' ) ? get_field( 'label' ) : '';
$detail = function_exists( 'get_field' ) ? get_field( 'detail' ) : '';

$figure = is_string( $figure ) ? $figure : '';
$label  = is_string( $label ) ? $label : '';
$detail = is_string( $detail ) ? $detail : '';
$is_preview = ! empty( $is_preview );

if ( '' === $figure && '' === $label && '' === $detail ) {
	if ( $is_preview ) {
		liftedpath_block_wrapper_open( 'article', 'liftedpath-stat liftedpath-stat--empty' );
		liftedpath_block_placeholder( __( 'Add a figure and a label.', 'liftedpath' ) );
		liftedpath_block_wrapper_close( 'article' );
	}
	return;
}
?>
<?php liftedpath_block_wrapper_open( 'article', 'liftedpath-stat' ); ?>
	<?php if ( '' !== $figure ) : ?>
		<strong class="liftedpath-stat__figure"><?php echo esc_html( $figure ); ?></strong>
	<?php endif; ?>
	<?php if ( '' !== $label ) : ?>
		<h3><?php echo esc_html( $label ); ?></h3>
	<?php endif; ?>
	<?php if ( '' !== $detail ) : ?>
		<p><?php echo esc_html( $detail ); ?></p>
	<?php endif; ?>
<?php liftedpath_block_wrapper_close( 'article' ); ?>
