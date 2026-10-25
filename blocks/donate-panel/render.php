<?php
/**
 * Gift choices. The editor canvas does not submit. The public site opens hosted checkout.
 *
 * @package LiftedPath
 */

$eyebrow       = liftedpath_block_string( function_exists( 'get_field' ) ? get_field( 'eyebrow' ) : '' );
$heading       = liftedpath_block_string( function_exists( 'get_field' ) ? get_field( 'heading' ) : '' );
$intro         = liftedpath_block_string( function_exists( 'get_field' ) ? get_field( 'intro' ) : '' );
$one_off       = liftedpath_block_string( function_exists( 'get_field' ) ? get_field( 'one_off_label' ) : '' );
$monthly       = liftedpath_block_string( function_exists( 'get_field' ) ? get_field( 'monthly_label' ) : '' );
$button_label  = liftedpath_block_string( function_exists( 'get_field' ) ? get_field( 'button_label' ) : '' );
$note          = liftedpath_block_string( function_exists( 'get_field' ) ? get_field( 'note' ) : '' );
$payment_note  = liftedpath_block_string( function_exists( 'get_field' ) ? get_field( 'payment_note' ) : '' );
$aside_heading      = liftedpath_block_string( function_exists( 'get_field' ) ? get_field( 'aside_heading' ) : '' );
$aside_text         = liftedpath_block_string( function_exists( 'get_field' ) ? get_field( 'aside_text' ) : '' );
$bank_heading       = liftedpath_block_string( function_exists( 'get_field' ) ? get_field( 'bank_heading' ) : '' );
$bank_number        = liftedpath_block_string( function_exists( 'get_field' ) ? get_field( 'bank_account_number' ) : '' );
$bank_account_name  = liftedpath_block_string( function_exists( 'get_field' ) ? get_field( 'bank_account_name' ) : '' );
$bank_name          = liftedpath_block_string( function_exists( 'get_field' ) ? get_field( 'bank_name' ) : '' );
$bank_currency      = liftedpath_block_string( function_exists( 'get_field' ) ? get_field( 'bank_currency' ) : '' );
$bank_mobile_order  = liftedpath_block_string( function_exists( 'get_field' ) ? get_field( 'bank_mobile_order' ) : '' );
$aside_image   = function_exists( 'get_field' ) ? get_field( 'aside_image' ) : null;
$usd_rate      = function_exists( 'get_field' ) ? (float) get_field( 'usd_rate' ) : 0;
$gbp_rate      = function_exists( 'get_field' ) ? (float) get_field( 'gbp_rate' ) : 0;
$amount_rows   = liftedpath_block_rows( function_exists( 'get_field' ) ? get_field( 'amounts' ) : array() );
$design_rows   = liftedpath_block_rows( function_exists( 'get_field' ) ? get_field( 'designations' ) : array() );
$is_preview    = ! empty( $is_preview );
$instance      = isset( $block['id'] ) ? sanitize_html_class( (string) $block['id'] ) : 'liftedpath';
$allowed       = array( 'greatest-need', 'education', 'skills', 'mentorship', 'orphanage', 'season' );
$amounts       = array();
$designations  = array();

if ( $usd_rate <= 0 ) {
	$usd_rate = 1500;
}

if ( $gbp_rate <= 0 ) {
	$gbp_rate = 2000;
}

if ( '' === $one_off ) {
	$one_off = __( 'One-off', 'liftedpath' );
}

if ( '' === $monthly ) {
	$monthly = __( 'Monthly', 'liftedpath' );
}

if ( '' === $button_label ) {
	$button_label = __( 'Arrange my gift', 'liftedpath' );
}

foreach ( $amount_rows as $row ) {
	if ( ! is_array( $row ) ) {
		continue;
	}

	$amount = isset( $row['amount'] ) ? (float) $row['amount'] : 0;
	$title  = liftedpath_block_string( $row['title'] ?? '' );
	$detail = liftedpath_block_string( $row['detail'] ?? '' );

	if ( $amount <= 0 || '' === $title ) {
		continue;
	}

	$amounts[] = array(
		'amount' => $amount,
		'title'  => $title,
		'detail' => $detail,
	);
}

if ( array() === $amounts ) {
	$amounts = array(
		array(
			'amount' => 15000,
			'title'  => __( 'School essentials', 'liftedpath' ),
			'detail' => __( 'Can help provide learning materials and school essentials for a pupil.', 'liftedpath' ),
		),
		array(
			'amount' => 50000,
			'title'  => __( 'Education support', 'liftedpath' ),
			'detail' => __( 'Can contribute toward tuition and related costs for a student.', 'liftedpath' ),
		),
		array(
			'amount' => 120000,
			'title'  => __( 'Skills & tools', 'liftedpath' ),
			'detail' => __( 'Can help fund practical training and tools for a young person.', 'liftedpath' ),
		),
		array(
			'amount' => 300000,
			'title'  => __( 'Family care', 'liftedpath' ),
			'detail' => __( 'Can help provide food and essentials to families facing hardship.', 'liftedpath' ),
		),
	);
}

foreach ( $design_rows as $row ) {
	if ( ! is_array( $row ) ) {
		continue;
	}

	$value = liftedpath_block_string( $row['designation'] ?? '' );
	$label = liftedpath_block_string( $row['label'] ?? '' );

	if ( ! in_array( $value, $allowed, true ) || '' === $label ) {
		continue;
	}

	$designations[] = array(
		'value' => $value,
		'label' => $label,
	);
}

if ( array() === $designations ) {
	$designations = array(
		array( 'value' => 'greatest-need', 'label' => __( 'Wherever the need is greatest', 'liftedpath' ) ),
		array( 'value' => 'education', 'label' => __( 'Education Sponsorship / Scholarship', 'liftedpath' ) ),
		array( 'value' => 'skills', 'label' => __( 'Skills Acquisition & Vocational Training', 'liftedpath' ) ),
		array( 'value' => 'mentorship', 'label' => __( 'Mentorship & Coaching', 'liftedpath' ) ),
		array( 'value' => 'orphanage', 'label' => __( 'Orphanage Visitations', 'liftedpath' ) ),
		array( 'value' => 'season', 'label' => __( 'Share the Joy of the Season', 'liftedpath' ) ),
	);
}

$image = liftedpath_block_image( $aside_image, 'liftedpath-card', 'liftedpath-donate__photo' );
$empty = '' === $heading && '' === $eyebrow && '' === $intro && '' === $aside_heading && '' === $image;

if ( $empty ) {
	if ( $is_preview ) {
		liftedpath_block_wrapper_open( 'section', 'liftedpath-section liftedpath-donate liftedpath-donate--empty' );
		liftedpath_block_placeholder( __( 'Add a heading for the gift panel.', 'liftedpath' ) );
		liftedpath_block_wrapper_close( 'section' );
	}
	return;
}

$show_bank         = '' !== $bank_number || '' !== $bank_account_name || '' !== $bank_name || '' !== $bank_currency;
$section_class     = 'liftedpath-section liftedpath-donate';

if ( $show_bank && 'after' !== $bank_mobile_order ) {
	$section_class .= ' is-bank-before';
}

$currencies = array( 'NGN', 'USD', 'GBP' );
$symbols    = array(
	'NGN' => '₦',
	'USD' => '$',
	'GBP' => '£',
);
?>
<?php liftedpath_block_wrapper_open( 'section', $section_class ); ?>
	<div class="liftedpath-section__inner liftedpath-donate__layout">
		<form class="liftedpath-donate__form" method="post" action="#" onsubmit="return false;">
			<?php if ( '' !== $eyebrow ) : ?>
				<p class="liftedpath-eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
			<?php endif; ?>
			<?php if ( '' !== $heading ) : ?>
				<h2><?php echo esc_html( $heading ); ?></h2>
			<?php endif; ?>
			<?php if ( '' !== $intro ) : ?>
				<p class="liftedpath-lead"><?php echo esc_html( $intro ); ?></p>
			<?php endif; ?>

			<h3><?php esc_html_e( 'Frequency', 'liftedpath' ); ?></h3>
			<div class="liftedpath-segment" role="radiogroup" aria-label="<?php esc_attr_e( 'Frequency', 'liftedpath' ); ?>">
				<label>
					<input type="radio" name="<?php echo esc_attr( 'frequency-' . $instance ); ?>" value="one-off" checked>
					<span><?php echo esc_html( $one_off ); ?></span>
				</label>
				<label>
					<input type="radio" name="<?php echo esc_attr( 'frequency-' . $instance ); ?>" value="monthly">
					<span><?php echo esc_html( $monthly ); ?></span>
				</label>
			</div>

			<div class="liftedpath-donate__impact">
				<h3><?php esc_html_e( 'Choose an impact', 'liftedpath' ); ?></h3>
				<div class="liftedpath-segment" role="radiogroup" aria-label="<?php esc_attr_e( 'Currency', 'liftedpath' ); ?>">
					<?php foreach ( $currencies as $code ) : ?>
						<label>
							<input type="radio" name="<?php echo esc_attr( 'currency-' . $instance ); ?>" value="<?php echo esc_attr( $code ); ?>" data-currency-choice="<?php echo esc_attr( $code ); ?>" <?php checked( 'NGN', $code ); ?>>
							<span><?php echo esc_html( $code . ' · ' . $symbols[ $code ] ); ?></span>
						</label>
					<?php endforeach; ?>
				</div>
			</div>

			<div class="liftedpath-donate__amounts">
				<?php foreach ( $amounts as $index => $item ) : ?>
					<label class="liftedpath-donate__choice">
						<input class="liftedpath-donate__amount" type="radio" name="<?php echo esc_attr( 'amount-' . $instance ); ?>" value="<?php echo esc_attr( (string) $item['amount'] ); ?>" data-index="<?php echo esc_attr( (string) $index ); ?>" <?php checked( 0, $index ); ?>>
						<strong>
							<?php foreach ( $currencies as $code ) : ?>
								<span class="liftedpath-money" data-currency="<?php echo esc_attr( $code ); ?>"><?php echo esc_html( liftedpath_block_money( $item['amount'], $code, $usd_rate, $gbp_rate ) ); ?></span>
							<?php endforeach; ?>
						</strong>
						<span class="liftedpath-donate__choice-title"><?php echo esc_html( $item['title'] ); ?></span>
					</label>
				<?php endforeach; ?>
			</div>

			<?php foreach ( $amounts as $index => $item ) : ?>
				<?php if ( '' === $item['detail'] ) : ?>
					<?php continue; ?>
				<?php endif; ?>
				<p class="liftedpath-donate__detail" data-for="<?php echo esc_attr( (string) $index ); ?>">
					<?php echo liftedpath_block_icon( liftedpath_donate_detail_icon( $item['title'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static icon. ?>
					<span><?php echo esc_html( $item['detail'] ); ?></span>
				</p>
			<?php endforeach; ?>

			<p class="liftedpath-donate__rate" data-currency="NGN"><?php esc_html_e( 'Select a gift amount or enter your own below.', 'liftedpath' ); ?></p>
			<p class="liftedpath-donate__rate" data-currency="USD">
				<?php
				echo esc_html(
					sprintf(
						/* translators: 1: naira amount, 2: one dollar. */
						__( 'Indicative conversion: %1$s = %2$s. The amount you confirm is the amount charged.', 'liftedpath' ),
						liftedpath_block_money( $usd_rate, 'NGN', $usd_rate, $gbp_rate ),
						liftedpath_block_money( $usd_rate, 'USD', $usd_rate, $gbp_rate )
					)
				);
				?>
			</p>
			<p class="liftedpath-donate__rate" data-currency="GBP">
				<?php
				echo esc_html(
					sprintf(
						/* translators: 1: naira amount, 2: one pound. */
						__( 'Indicative conversion: %1$s = %2$s. The amount you confirm is the amount charged.', 'liftedpath' ),
						liftedpath_block_money( $gbp_rate, 'NGN', $usd_rate, $gbp_rate ),
						liftedpath_block_money( $gbp_rate, 'GBP', $usd_rate, $gbp_rate )
					)
				);
				?>
			</p>

			<label class="liftedpath-field" for="<?php echo esc_attr( 'gift-custom-' . $instance ); ?>">
				<?php foreach ( $currencies as $code ) : ?>
					<span class="liftedpath-money" data-currency="<?php echo esc_attr( $code ); ?>">
						<?php
						echo esc_html(
							sprintf(
								/* translators: %s: currency code. */
								__( 'Other amount (%s)', 'liftedpath' ),
								$code
							)
						);
						?>
					</span>
				<?php endforeach; ?>
				<input id="<?php echo esc_attr( 'gift-custom-' . $instance ); ?>" type="number" name="amount_custom" min="1" step="1" inputmode="numeric">
			</label>

			<label class="liftedpath-field" for="<?php echo esc_attr( 'gift-name-' . $instance ); ?>">
				<?php esc_html_e( 'Full name', 'liftedpath' ); ?>
				<input id="<?php echo esc_attr( 'gift-name-' . $instance ); ?>" type="text" name="full_name" autocomplete="name">
			</label>

			<label class="liftedpath-field" for="<?php echo esc_attr( 'gift-email-' . $instance ); ?>">
				<?php esc_html_e( 'Email', 'liftedpath' ); ?>
				<input id="<?php echo esc_attr( 'gift-email-' . $instance ); ?>" type="email" name="email" autocomplete="email">
			</label>

			<label class="liftedpath-field" for="<?php echo esc_attr( 'gift-designation-' . $instance ); ?>">
				<?php esc_html_e( 'Direct my gift to', 'liftedpath' ); ?>
				<select id="<?php echo esc_attr( 'gift-designation-' . $instance ); ?>" name="designation">
					<?php foreach ( $designations as $item ) : ?>
						<option value="<?php echo esc_attr( $item['value'] ); ?>"><?php echo esc_html( $item['label'] ); ?></option>
					<?php endforeach; ?>
				</select>
			</label>

			<button class="liftedpath-button" type="button">
				<?php echo esc_html( $button_label ); ?>
				<?php echo liftedpath_block_arrow(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static icon. ?>
			</button>
			<?php if ( '' !== $note ) : ?>
				<p class="liftedpath-form-note"><?php echo esc_html( $note ); ?></p>
			<?php endif; ?>
			<?php if ( '' !== $payment_note ) : ?>
				<p class="liftedpath-form-note"><?php echo esc_html( $payment_note ); ?></p>
			<?php endif; ?>
		</form>

		<?php $show_aside = '' !== $image || '' !== $aside_heading || '' !== $aside_text; ?>
		<?php if ( $show_bank || $show_aside ) : ?>
			<aside class="liftedpath-donate__rail">
				<?php if ( $show_bank ) : ?>
					<div class="liftedpath-donate__bank">
						<?php if ( '' !== $bank_heading ) : ?>
							<h3><?php echo esc_html( $bank_heading ); ?></h3>
						<?php endif; ?>
						<dl>
							<?php if ( '' !== $bank_number ) : ?>
								<div>
									<dt><?php esc_html_e( 'Account number', 'liftedpath' ); ?></dt>
									<dd><?php echo esc_html( $bank_number ); ?></dd>
								</div>
							<?php endif; ?>
							<?php if ( '' !== $bank_account_name ) : ?>
								<div>
									<dt><?php esc_html_e( 'Account name', 'liftedpath' ); ?></dt>
									<dd><?php echo esc_html( $bank_account_name ); ?></dd>
								</div>
							<?php endif; ?>
							<?php if ( '' !== $bank_name ) : ?>
								<div>
									<dt><?php esc_html_e( 'Bank', 'liftedpath' ); ?></dt>
									<dd><?php echo esc_html( $bank_name ); ?></dd>
								</div>
							<?php endif; ?>
							<?php if ( '' !== $bank_currency ) : ?>
								<div>
									<dt><?php esc_html_e( 'Currency', 'liftedpath' ); ?></dt>
									<dd><?php echo esc_html( $bank_currency ); ?></dd>
								</div>
							<?php endif; ?>
						</dl>
					</div>
				<?php endif; ?>
				<?php if ( $show_aside ) : ?>
					<div class="liftedpath-donate__aside">
						<?php echo $image; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- attachment markup is escaped. ?>
						<div>
							<?php echo liftedpath_block_icon( 'graduation-cap' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static icon. ?>
							<?php if ( '' !== $aside_heading ) : ?>
								<strong><?php echo esc_html( $aside_heading ); ?></strong>
							<?php endif; ?>
							<?php if ( '' !== $aside_text ) : ?>
								<p><?php echo esc_html( $aside_text ); ?></p>
							<?php endif; ?>
						</div>
					</div>
				<?php endif; ?>
			</aside>
		<?php endif; ?>
	</div>
<?php liftedpath_block_wrapper_close( 'section' ); ?>
