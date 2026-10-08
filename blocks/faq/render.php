<?php
/**
 * One-open donor questions.
 *
 * @package LiftedPath
 */

$eyebrow    = liftedpath_block_string( function_exists( 'get_field' ) ? get_field( 'eyebrow' ) : '' );
$heading    = liftedpath_block_string( function_exists( 'get_field' ) ? get_field( 'heading' ) : '' );
$rows       = liftedpath_block_rows( function_exists( 'get_field' ) ? get_field( 'items' ) : array() );
$is_preview = ! empty( $is_preview );
$items      = array();
$instance   = isset( $block['id'] ) ? sanitize_html_class( (string) $block['id'] ) : 'liftedpath';

foreach ( $rows as $row ) {
	if ( ! is_array( $row ) ) {
		continue;
	}

	$question = liftedpath_block_string( $row['question'] ?? '' );
	$answer   = liftedpath_block_string( $row['answer'] ?? '' );

	if ( '' === $question && '' === $answer ) {
		continue;
	}

	$items[] = array(
		'question' => $question,
		'answer'   => $answer,
	);
}

$empty = '' === $heading && '' === $eyebrow && array() === $items;

if ( $empty ) {
	if ( $is_preview ) {
		liftedpath_block_wrapper_open( 'section', 'liftedpath-section liftedpath-faq liftedpath-faq--empty' );
		liftedpath_block_placeholder( __( 'Add a heading and questions.', 'liftedpath' ) );
		liftedpath_block_wrapper_close( 'section' );
	}
	return;
}
?>
<?php liftedpath_block_wrapper_open( 'section', 'liftedpath-section liftedpath-faq' ); ?>
	<div class="liftedpath-section__inner">
		<?php if ( '' !== $eyebrow ) : ?>
			<p class="liftedpath-eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
		<?php endif; ?>
		<?php if ( '' !== $heading ) : ?>
			<h2><?php echo esc_html( $heading ); ?></h2>
		<?php endif; ?>
		<?php if ( array() !== $items ) : ?>
			<div class="liftedpath-faq__list">
				<?php foreach ( $items as $item ) : ?>
					<details class="liftedpath-faq__item" name="<?php echo esc_attr( 'liftedpath-faq-' . $instance ); ?>">
						<summary>
							<span><?php echo esc_html( '' !== $item['question'] ? $item['question'] : $item['answer'] ); ?></span>
							<span class="liftedpath-faq__chevron"><?php echo liftedpath_block_icon( 'chevron' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static icon. ?></span>
						</summary>
						<?php if ( '' !== $item['answer'] && '' !== $item['question'] ) : ?>
							<p><?php echo esc_html( $item['answer'] ); ?></p>
						<?php endif; ?>
					</details>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
<?php liftedpath_block_wrapper_close( 'section' ); ?>
