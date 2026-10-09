<?php
/**
 * Team profiles, two to four columns.
 *
 * @package LiftedPath
 */

$eyebrow    = liftedpath_block_string( function_exists( 'get_field' ) ? get_field( 'eyebrow' ) : '' );
$heading    = liftedpath_block_string( function_exists( 'get_field' ) ? get_field( 'heading' ) : '' );
$rows       = liftedpath_block_rows( function_exists( 'get_field' ) ? get_field( 'members' ) : array() );
$is_preview = ! empty( $is_preview );
$members    = array();

foreach ( $rows as $row ) {
	if ( ! is_array( $row ) ) {
		continue;
	}

	$name     = liftedpath_block_string( $row['name'] ?? '' );
	$position = liftedpath_block_string( $row['position'] ?? '' );
	$image_id = liftedpath_attachment_id( $row['image'] ?? 0 );

	if ( '' === $name && '' === $position && $image_id <= 0 ) {
		continue;
	}

	$members[] = array(
		'name'     => $name,
		'position' => $position,
		'image'    => $image_id,
	);

	if ( count( $members ) >= 4 ) {
		break;
	}
}

$empty = '' === $heading && '' === $eyebrow && array() === $members;

if ( $empty ) {
	if ( $is_preview ) {
		liftedpath_block_wrapper_open( 'section', 'liftedpath-section liftedpath-team liftedpath-team--empty' );
		liftedpath_block_placeholder( __( 'Add a heading and team members.', 'liftedpath' ) );
		liftedpath_block_wrapper_close( 'section' );
	}
	return;
}

$columns = max( 1, min( 4, count( $members ) ) );
$style   = '';
$colors  = array(
	'--team-overlay' => liftedpath_block_color( function_exists( 'get_field' ) ? get_field( 'overlay_color' ) : '' ),
	'--team-name'    => liftedpath_block_color( function_exists( 'get_field' ) ? get_field( 'name_color' ) : '' ),
	'--team-role'    => liftedpath_block_color( function_exists( 'get_field' ) ? get_field( 'position_color' ) : '' ),
);

foreach ( $colors as $name => $color ) {
	if ( '' !== $color ) {
		$style .= $name . ':' . $color . ';';
	}
}
?>
<?php liftedpath_block_wrapper_open( 'section', 'liftedpath-section liftedpath-team', $style ); ?>
	<div class="liftedpath-section__inner">
		<?php if ( '' !== $eyebrow || '' !== $heading ) : ?>
			<div class="liftedpath-team__heading">
				<?php if ( '' !== $eyebrow ) : ?>
					<p class="liftedpath-eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
				<?php endif; ?>
				<?php if ( '' !== $heading ) : ?>
					<h2><?php echo esc_html( $heading ); ?></h2>
				<?php endif; ?>
			</div>
		<?php endif; ?>
		<?php if ( array() !== $members ) : ?>
			<ul class="liftedpath-team__grid" data-columns="<?php echo esc_attr( (string) $columns ); ?>">
				<?php foreach ( $members as $member ) : ?>
					<?php
					$initial = '';
					$photo   = '';

					if ( '' !== $member['name'] ) {
						$letter  = function_exists( 'mb_substr' ) ? mb_substr( $member['name'], 0, 1 ) : substr( $member['name'], 0, 1 );
						$initial = function_exists( 'mb_strtoupper' ) ? mb_strtoupper( $letter ) : strtoupper( $letter );
					}

					if ( $member['image'] > 0 ) {
						$html  = wp_get_attachment_image(
							$member['image'],
							'liftedpath-card',
							false,
							array(
								'class' => 'liftedpath-team__photo',
								'alt'   => $member['name'],
							)
						);
						$photo = is_string( $html ) ? $html : '';
					}
					?>
					<li>
						<article class="liftedpath-team__card">
							<div class="liftedpath-team__media">
								<?php if ( '' !== $photo ) : ?>
									<?php echo $photo; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- attachment image is escaped. ?>
								<?php else : ?>
									<span class="liftedpath-team__fallback" aria-hidden="true"><?php echo esc_html( $initial ); ?></span>
								<?php endif; ?>
							</div>
							<div class="liftedpath-team__info">
								<?php if ( '' !== $member['name'] ) : ?>
									<h3><?php echo esc_html( $member['name'] ); ?></h3>
								<?php endif; ?>
								<?php if ( '' !== $member['position'] ) : ?>
									<p class="liftedpath-team__role"><?php echo esc_html( $member['position'] ); ?></p>
								<?php endif; ?>
							</div>
						</article>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
	</div>
<?php liftedpath_block_wrapper_close( 'section' ); ?>
