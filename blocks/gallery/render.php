<?php
/**
 * Photographs and videos in a mosaic or an even grid.
 *
 * @package LiftedPath
 */

$eyebrow       = liftedpath_block_string( function_exists( 'get_field' ) ? get_field( 'eyebrow' ) : '' );
$heading       = liftedpath_block_string( function_exists( 'get_field' ) ? get_field( 'heading' ) : '' );
$intro         = liftedpath_block_string( function_exists( 'get_field' ) ? get_field( 'intro' ) : '' );
$layout_raw    = liftedpath_block_string( function_exists( 'get_field' ) ? get_field( 'layout' ) : '' );
$columns_raw   = liftedpath_block_string( function_exists( 'get_field' ) ? get_field( 'columns' ) : '' );
$captions_raw  = function_exists( 'get_field' ) ? get_field( 'show_captions' ) : null;
$rows          = liftedpath_block_rows( function_exists( 'get_field' ) ? get_field( 'items' ) : array() );
$is_preview    = ! empty( $is_preview );
$layout        = 'grid' === $layout_raw ? 'grid' : 'mosaic';
$columns       = in_array( $columns_raw, array( '2', '4' ), true ) ? $columns_raw : '3';
$show_captions = ( null === $captions_raw || '' === $captions_raw ) ? true : (bool) $captions_raw;
$paragraphs    = liftedpath_block_paragraphs( $intro );

if ( ! function_exists( 'liftedpath_gallery_pack' ) ) {
	/**
	 * Place mosaic tiles so every cell through the last row is filled.
	 *
	 * @param array<int, string> $tiles Tile sizes.
	 * @return array<int, array{col: int, row: int, colSpan: int, rowSpan: int}>
	 */
	function liftedpath_gallery_pack( array $tiles, int $columns, bool $mosaic ): array {
		if ( $columns <= 1 ) {
			$placed = array();
			$count  = count( $tiles );
			for ( $index = 0; $index < $count; $index++ ) {
				$placed[] = array(
					'col'     => 0,
					'row'     => $index,
					'colSpan' => 1,
					'rowSpan' => 1,
				);
			}
			return $placed;
		}

		$occupied = array();
		$taken    = static function ( int $row, int $col ) use ( &$occupied ): bool {
			return ! empty( $occupied[ $row ][ $col ] );
		};
		$mark     = static function ( int $row, int $col ) use ( &$occupied, $columns ): void {
			if ( ! isset( $occupied[ $row ] ) ) {
				$occupied[ $row ] = array_fill( 0, $columns, false );
			}
			$occupied[ $row ][ $col ] = true;
		};
		$fits     = static function ( int $row, int $col, int $col_span, int $row_span ) use ( $taken, $columns ): bool {
			if ( $col < 0 || ( $col + $col_span ) > $columns ) {
				return false;
			}
			for ( $row_offset = 0; $row_offset < $row_span; $row_offset++ ) {
				for ( $col_offset = 0; $col_offset < $col_span; $col_offset++ ) {
					if ( $taken( $row + $row_offset, $col + $col_offset ) ) {
						return false;
					}
				}
			}
			return true;
		};
		$first_free = static function () use ( $taken, $columns ): array {
			for ( $row = 0; $row < 240; $row++ ) {
				for ( $col = 0; $col < $columns; $col++ ) {
					if ( ! $taken( $row, $col ) ) {
						return array(
							'row' => $row,
							'col' => $col,
						);
					}
				}
			}
			return array(
				'row' => 0,
				'col' => 0,
			);
		};

		$placed = array();
		$count  = count( $tiles );
		foreach ( array_values( $tiles ) as $index => $tile ) {
			$spot      = $first_free();
			$shape     = $mosaic ? (string) $tile : 'standard';
			$remaining = $count - $index - 1;
			$col_span  = 1;
			$row_span  = 1;
			if ( 'wide' === $shape && $fits( $spot['row'], $spot['col'], min( 2, $columns ), 1 ) ) {
				$col_span = min( 2, $columns );
			}
			if ( 'tall' === $shape ) {
				$holes = ( $columns - $spot['col'] - 1 ) + ( $columns - 1 );
				if ( $fits( $spot['row'], $spot['col'], 1, 2 ) && $remaining >= $holes ) {
					$row_span = 2;
				}
			}
			for ( $row_offset = 0; $row_offset < $row_span; $row_offset++ ) {
				for ( $col_offset = 0; $col_offset < $col_span; $col_offset++ ) {
					$mark( $spot['row'] + $row_offset, $spot['col'] + $col_offset );
				}
			}
			$placed[] = array(
				'col'     => $spot['col'],
				'row'     => $spot['row'],
				'colSpan' => $col_span,
				'rowSpan' => $row_span,
			);
		}

		for ( $row = 0, $row_count = count( $occupied ); $row < $row_count; $row++ ) {
			for ( $col = 0; $col < $columns; $col++ ) {
				if ( $taken( $row, $col ) ) {
					continue;
				}
				foreach ( $placed as $key => $cell ) {
					if ( $cell['row'] === $row && 1 === $cell['rowSpan'] && ( $cell['col'] + $cell['colSpan'] ) === $col ) {
						$placed[ $key ]['colSpan']++;
						$mark( $row, $col );
						break;
					}
				}
			}
			for ( $col = $columns - 1; $col >= 0; $col-- ) {
				if ( $taken( $row, $col ) ) {
					continue;
				}
				foreach ( $placed as $key => $cell ) {
					if ( $cell['row'] === $row && 1 === $cell['rowSpan'] && $cell['col'] === ( $col + 1 ) ) {
						$placed[ $key ]['col'] = $col;
						$placed[ $key ]['colSpan']++;
						$mark( $row, $col );
						break;
					}
				}
			}
		}

		return $placed;
	}
}

if ( ! function_exists( 'liftedpath_gallery_track' ) ) {
	/**
	 * CSS grid line for one packed cell.
	 *
	 * @param array{col: int, row: int, colSpan: int, rowSpan: int} $cell Packed cell.
	 */
	function liftedpath_gallery_track( array $cell, string $axis ): string {
		$start = ( 'col' === $axis ? (int) $cell['col'] : (int) $cell['row'] ) + 1;
		$span  = 'col' === $axis ? (int) $cell['colSpan'] : (int) $cell['rowSpan'];
		return $start . ' / span ' . $span;
	}
}

$items = array();

foreach ( $rows as $row ) {
	if ( ! is_array( $row ) ) {
		continue;
	}

	$kind      = 'video' === liftedpath_block_string( $row['media_kind'] ?? '' ) ? 'video' : 'image';
	$image_id  = liftedpath_attachment_id( $row['image'] ?? 0 );
	$video_id  = liftedpath_attachment_id( $row['video'] ?? 0 );
	$video_url = liftedpath_block_string( $row['video_url'] ?? '' );
	$caption   = liftedpath_block_string( $row['caption'] ?? '' );
	$tile_raw  = liftedpath_block_string( $row['tile'] ?? '' );
	$tile      = in_array( $tile_raw, array( 'wide', 'tall' ), true ) ? $tile_raw : 'standard';
	$visual    = $image_id > 0 || ( 'video' === $kind && ( $video_id > 0 || '' !== $video_url ) );

	if ( ! $visual && ! $is_preview ) {
		continue;
	}

	if ( ! $visual && '' === $caption ) {
		continue;
	}

	$items[] = array(
		'kind'      => $kind,
		'image'     => $image_id,
		'video'     => $video_id,
		'video_url' => $video_url,
		'caption'   => $caption,
		'tile'      => $tile,
	);

	if ( count( $items ) >= 24 ) {
		break;
	}
}

$empty = '' === $eyebrow && '' === $heading && array() === $paragraphs && array() === $items;

if ( $empty ) {
	if ( $is_preview ) {
		liftedpath_block_wrapper_open( 'section', 'liftedpath-section liftedpath-gallery liftedpath-gallery--empty' );
		liftedpath_block_placeholder( __( 'Add a heading and at least one photograph or video.', 'liftedpath' ) );
		liftedpath_block_wrapper_close( 'section' );
	}
	return;
}
?>
<?php liftedpath_block_wrapper_open( 'section', 'liftedpath-section liftedpath-gallery' ); ?>
	<div class="liftedpath-section__inner">
		<?php if ( '' !== $eyebrow || '' !== $heading || array() !== $paragraphs ) : ?>
			<div class="liftedpath-gallery__heading">
				<?php if ( '' !== $eyebrow ) : ?>
					<p class="liftedpath-eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
				<?php endif; ?>
				<?php if ( '' !== $heading ) : ?>
					<h2><?php echo esc_html( $heading ); ?></h2>
				<?php endif; ?>
				<?php foreach ( $paragraphs as $paragraph ) : ?>
					<p class="liftedpath-lead"><?php echo esc_html( $paragraph ); ?></p>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
		<?php if ( array() !== $items ) : ?>
			<?php
			$tile_names = array();
			foreach ( $items as $item ) {
				$tile_names[] = $item['tile'];
			}
			$mosaic = 'mosaic' === $layout;
			$packs  = array(
				1 => liftedpath_gallery_pack( $tile_names, 1, $mosaic ),
				2 => liftedpath_gallery_pack( $tile_names, 2, $mosaic ),
				3 => liftedpath_gallery_pack( $tile_names, 3, $mosaic ),
				4 => liftedpath_gallery_pack( $tile_names, 4, $mosaic ),
			);
			?>
			<ul class="liftedpath-gallery__grid" data-columns="<?php echo esc_attr( $columns ); ?>" data-layout="<?php echo esc_attr( $layout ); ?>">
				<?php foreach ( $items as $index => $item ) : ?>
					<?php
					$image_html = '';
					$alt        = $item['caption'];

					if ( $item['image'] > 0 ) {
						$stored = get_post_meta( $item['image'], '_wp_attachment_image_alt', true );
						if ( is_string( $stored ) && '' !== $stored ) {
							$alt = $stored;
						}

						$html       = wp_get_attachment_image(
							$item['image'],
							'large',
							false,
							array(
								'class' => 'liftedpath-gallery__photo',
								'alt'   => $alt,
							)
						);
						$image_html = is_string( $html ) ? $html : '';
					}

					$video_src = '';
					$poster    = '';
					if ( 'video' === $item['kind'] && '' === $item['video_url'] && $item['video'] > 0 ) {
						$mime = get_post_mime_type( $item['video'] );
						$url  = wp_get_attachment_url( $item['video'] );
						if ( is_string( $mime ) && str_starts_with( $mime, 'video/' ) && is_string( $url ) ) {
							$video_src = $url;
						}
					}
					if ( $item['image'] > 0 ) {
						$poster_url = wp_get_attachment_image_url( $item['image'], 'large' );
						$poster     = is_string( $poster_url ) ? $poster_url : '';
					}

					$item_class = 'standard' === $item['tile'] ? '' : 'is-' . $item['tile'];
					$cell_1     = $packs[1][ $index ];
					$cell_2     = $packs[2][ $index ];
					$cell_3     = $packs[3][ $index ];
					$cell_4     = $packs[4][ $index ];
					$item_style = sprintf(
						'--c1:%1$s;--r1:%2$s;--c2:%3$s;--r2:%4$s;--c3:%5$s;--r3:%6$s;--c4:%7$s;--r4:%8$s;',
						liftedpath_gallery_track( $cell_1, 'col' ),
						liftedpath_gallery_track( $cell_1, 'row' ),
						liftedpath_gallery_track( $cell_2, 'col' ),
						liftedpath_gallery_track( $cell_2, 'row' ),
						liftedpath_gallery_track( $cell_3, 'col' ),
						liftedpath_gallery_track( $cell_3, 'row' ),
						liftedpath_gallery_track( $cell_4, 'col' ),
						liftedpath_gallery_track( $cell_4, 'row' )
					);
					?>
					<li<?php echo '' !== $item_class ? ' class="' . esc_attr( $item_class ) . '"' : ''; ?> style="<?php echo esc_attr( $item_style ); ?>">
						<figure class="liftedpath-gallery__tile<?php echo '' === $image_html && '' === $video_src ? ' is-empty' : ''; ?>">
							<span class="liftedpath-gallery__media">
								<?php if ( '' !== $image_html ) : ?>
									<?php echo $image_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_get_attachment_image escapes. ?>
								<?php endif; ?>
								<?php if ( '' !== $video_src ) : ?>
									<video class="liftedpath-gallery__video" controls playsinline<?php echo '' !== $poster ? ' poster="' . esc_url( $poster ) . '"' : ''; ?> preload="metadata" src="<?php echo esc_url( $video_src ); ?>"></video>
								<?php endif; ?>
							</span>
							<?php if ( 'video' === $item['kind'] ) : ?>
								<span class="liftedpath-gallery__play" aria-hidden="true"></span>
							<?php endif; ?>
							<?php if ( $show_captions && '' !== $item['caption'] ) : ?>
								<figcaption class="liftedpath-gallery__caption"><?php echo esc_html( $item['caption'] ); ?></figcaption>
							<?php endif; ?>
						</figure>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
	</div>
<?php liftedpath_block_wrapper_close( 'section' ); ?>
