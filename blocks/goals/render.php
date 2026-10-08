<?php
/**
 * Future goals.
 *
 * @package LiftedPath
 */

$eyebrow    = liftedpath_block_string( function_exists( 'get_field' ) ? get_field( 'eyebrow' ) : '' );
$heading    = liftedpath_block_string( function_exists( 'get_field' ) ? get_field( 'heading' ) : '' );
$intro      = liftedpath_block_string( function_exists( 'get_field' ) ? get_field( 'intro' ) : '' );
$rows       = liftedpath_block_rows( function_exists( 'get_field' ) ? get_field( 'goals' ) : array() );
$is_preview = ! empty( $is_preview );
$goals      = array();

foreach ( $rows as $row ) {
	if ( ! is_array( $row ) ) {
		continue;
	}

	$title = liftedpath_block_string( $row['title'] ?? '' );
	$text  = liftedpath_block_string( $row['text'] ?? '' );

	if ( '' === $title && '' === $text ) {
		continue;
	}

	$goals[] = array(
		'title' => $title,
		'text'  => $text,
	);
}

$empty = '' === $heading && '' === $eyebrow && '' === $intro && array() === $goals;

if ( $empty ) {
	if ( $is_preview ) {
		liftedpath_block_wrapper_open( 'section', 'liftedpath-section liftedpath-goals liftedpath-goals--empty' );
		liftedpath_block_placeholder( __( 'Add a heading and goals.', 'liftedpath' ) );
		liftedpath_block_wrapper_close( 'section' );
	}
	return;
}
?>
<?php liftedpath_block_wrapper_open( 'section', 'liftedpath-section liftedpath-goals' ); ?>
	<div class="liftedpath-section__inner liftedpath-goals__layout">
		<div>
			<?php if ( '' !== $eyebrow ) : ?>
				<p class="liftedpath-eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
			<?php endif; ?>
			<?php if ( '' !== $heading ) : ?>
				<h2><?php echo esc_html( $heading ); ?></h2>
			<?php endif; ?>
			<?php if ( '' !== $intro ) : ?>
				<p class="liftedpath-lead"><?php echo esc_html( $intro ); ?></p>
			<?php endif; ?>
		</div>
		<?php if ( array() !== $goals ) : ?>
			<ol class="liftedpath-goals__list">
				<?php foreach ( $goals as $index => $goal ) : ?>
					<li>
						<span class="liftedpath-goals__index"><?php echo esc_html( sprintf( '%02d', $index + 1 ) ); ?></span>
						<?php if ( '' !== $goal['title'] ) : ?>
							<h3><?php echo esc_html( $goal['title'] ); ?></h3>
						<?php endif; ?>
						<?php if ( '' !== $goal['text'] ) : ?>
							<p><?php echo esc_html( $goal['text'] ); ?></p>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ol>
		<?php endif; ?>
	</div>
<?php liftedpath_block_wrapper_close( 'section' ); ?>
