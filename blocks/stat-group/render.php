<?php
/**
 * Grid of Stat blocks.
 *
 * @package LiftedPath
 */

$is_preview = ! empty( $is_preview );
$inner      = isset( $content ) && is_string( $content ) ? trim( $content ) : '';

if ( '' === $inner && ! $is_preview ) {
	return;
}
?>
<?php liftedpath_block_wrapper_open( 'section', 'liftedpath-stat-group' ); ?>
	<InnerBlocks allowedBlocks='["liftedpath/stat"]' class="liftedpath-stat-group__grid" />
<?php liftedpath_block_wrapper_close( 'section' ); ?>
