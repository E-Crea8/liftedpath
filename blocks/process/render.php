<?php
/**
 * Numbered process steps.
 *
 * @package LiftedPath
 */

$eyebrow = liftedpath_block_string( function_exists( 'get_field' ) ? get_field( 'eyebrow' ) : '' );
$heading = liftedpath_block_string( function_exists( 'get_field' ) ? get_field( 'heading' ) : '' );
$button  = liftedpath_block_button( function_exists( 'get_field' ) ? get_field( 'button' ) : null );
$rows    = liftedpath_block_rows( function_exists( 'get_field' ) ? get_field( 'steps' ) : array() );
$is_preview = ! empty( $is_preview );
$steps      = array();

foreach ( $rows as $row ) {
	if ( ! is_array( $row ) ) {
		continue;
	}

	$title = liftedpath_block_string( $row['title'] ?? '' );
	$text  = liftedpath_block_string( $row['text'] ?? '' );

	if ( '' === $title && '' === $text ) {
		continue;
	}

	$steps[] = array(
		'title' => $title,
		'text'  => $text,
	);
}

$empty = '' === $heading && '' === $eyebrow && '' === $button && array() === $steps;

if ( $empty ) {
	if ( $is_preview ) {
		liftedpath_block_wrapper_open( 'section', 'liftedpath-section liftedpath-process liftedpath-process--empty' );
		liftedpath_block_placeholder( __( 'Add a heading and steps.', 'liftedpath' ) );
		liftedpath_block_wrapper_close( 'section' );
	}
	return;
}
?>
<?php liftedpath_block_wrapper_open( 'section', 'liftedpath-section liftedpath-process' ); ?>
	<div class="liftedpath-section__inner">
		<div class="liftedpath-process__heading">
			<div>
				<?php if ( '' !== $eyebrow ) : ?>
					<p class="liftedpath-eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
				<?php endif; ?>
				<?php if ( '' !== $heading ) : ?>
					<h2><?php echo esc_html( $heading ); ?></h2>
				<?php endif; ?>
			</div>
			<?php echo $button; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- link markup is escaped. ?>
		</div>
		<?php if ( array() !== $steps ) : ?>
			<ol class="liftedpath-process__grid">
				<?php foreach ( $steps as $index => $step ) : ?>
					<li>
						<span><?php echo esc_html( sprintf( '%02d', $index + 1 ) ); ?></span>
						<?php if ( '' !== $step['title'] ) : ?>
							<h3><?php echo esc_html( $step['title'] ); ?></h3>
						<?php endif; ?>
						<?php if ( '' !== $step['text'] ) : ?>
							<p><?php echo esc_html( $step['text'] ); ?></p>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ol>
		<?php endif; ?>
	</div>
<?php liftedpath_block_wrapper_close( 'section' ); ?>
