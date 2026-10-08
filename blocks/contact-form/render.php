<?php
/**
 * Contact details and a message form. The form does not send.
 *
 * @package LiftedPath
 */

$eyebrow       = liftedpath_block_string( function_exists( 'get_field' ) ? get_field( 'eyebrow' ) : '' );
$heading       = liftedpath_block_string( function_exists( 'get_field' ) ? get_field( 'heading' ) : '' );
$intro         = liftedpath_block_string( function_exists( 'get_field' ) ? get_field( 'intro' ) : '' );
$form_eyebrow  = liftedpath_block_string( function_exists( 'get_field' ) ? get_field( 'form_eyebrow' ) : '' );
$form_heading  = liftedpath_block_string( function_exists( 'get_field' ) ? get_field( 'form_heading' ) : '' );
$button_label  = liftedpath_block_string( function_exists( 'get_field' ) ? get_field( 'button_label' ) : '' );
$note          = liftedpath_block_string( function_exists( 'get_field' ) ? get_field( 'note' ) : '' );
$rows          = liftedpath_block_rows( function_exists( 'get_field' ) ? get_field( 'reasons' ) : array() );
$is_preview    = ! empty( $is_preview );
$allowed       = array( 'volunteer', 'sponsor', 'partner', 'in-kind', 'other' );
$reasons       = array();
$instance      = isset( $block['id'] ) ? sanitize_html_class( (string) $block['id'] ) : 'liftedpath';

foreach ( $rows as $row ) {
	if ( ! is_array( $row ) ) {
		continue;
	}

	$value = liftedpath_block_string( $row['reason'] ?? '' );
	$label = liftedpath_block_string( $row['label'] ?? '' );

	if ( ! in_array( $value, $allowed, true ) || '' === $label ) {
		continue;
	}

	$reasons[] = array(
		'value' => $value,
		'label' => $label,
	);
}

if ( array() === $reasons ) {
	$reasons = array(
		array( 'value' => 'volunteer', 'label' => __( 'Volunteer my time', 'liftedpath' ) ),
		array( 'value' => 'sponsor', 'label' => __( 'Sponsor a child or program', 'liftedpath' ) ),
		array( 'value' => 'partner', 'label' => __( 'Partner as a school or organisation', 'liftedpath' ) ),
		array( 'value' => 'in-kind', 'label' => __( 'Make an in-kind donation', 'liftedpath' ) ),
		array( 'value' => 'other', 'label' => __( 'Something else', 'liftedpath' ) ),
	);
}

if ( '' === $button_label ) {
	$button_label = __( 'Send message', 'liftedpath' );
}

$phone   = liftedpath_block_string( function_exists( 'get_field' ) ? get_field( 'phone', 'option' ) : '' );
$email   = liftedpath_block_string( function_exists( 'get_field' ) ? get_field( 'email', 'option' ) : '' );
$address = liftedpath_block_string( function_exists( 'get_field' ) ? get_field( 'postal_address', 'option' ) : '' );
$empty   = '' === $heading && '' === $eyebrow && '' === $intro && '' === $form_heading && '' === $form_eyebrow;

if ( $empty ) {
	if ( $is_preview ) {
		liftedpath_block_wrapper_open( 'section', 'liftedpath-section liftedpath-contact liftedpath-contact--empty' );
		liftedpath_block_placeholder( __( 'Add a heading for the contact section.', 'liftedpath' ) );
		liftedpath_block_wrapper_close( 'section' );
	}
	return;
}

$tel = preg_replace( '/[^0-9+]/', '', $phone );
?>
<?php liftedpath_block_wrapper_open( 'section', 'liftedpath-section liftedpath-contact' ); ?>
	<div class="liftedpath-section__inner liftedpath-contact__layout">
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
			<?php if ( '' !== $phone ) : ?>
				<a class="liftedpath-contact__row" href="<?php echo esc_url( 'tel:' . $tel ); ?>">
					<?php echo liftedpath_block_icon( 'phone' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static icon. ?>
					<span><small><?php esc_html_e( 'Phone', 'liftedpath' ); ?></small><?php echo esc_html( $phone ); ?></span>
				</a>
			<?php endif; ?>
			<?php if ( '' !== $email && is_email( $email ) ) : ?>
				<a class="liftedpath-contact__row" href="<?php echo esc_url( 'mailto:' . $email ); ?>">
					<?php echo liftedpath_block_icon( 'mail' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static icon. ?>
					<span><small><?php esc_html_e( 'Email', 'liftedpath' ); ?></small><?php echo esc_html( $email ); ?></span>
				</a>
			<?php endif; ?>
			<?php if ( '' !== $address ) : ?>
				<div class="liftedpath-contact__row">
					<?php echo liftedpath_block_icon( 'map-pin' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static icon. ?>
					<span><small><?php esc_html_e( 'Office', 'liftedpath' ); ?></small><?php echo esc_html( $address ); ?></span>
				</div>
			<?php endif; ?>
		</div>
		<form class="liftedpath-contact__panel" method="post" action="#" onsubmit="return false;">
			<?php if ( '' !== $form_eyebrow ) : ?>
				<p class="liftedpath-eyebrow"><?php echo esc_html( $form_eyebrow ); ?></p>
			<?php endif; ?>
			<?php if ( '' !== $form_heading ) : ?>
				<h2><?php echo esc_html( $form_heading ); ?></h2>
			<?php endif; ?>
			<label class="liftedpath-field" for="<?php echo esc_attr( 'contact-name-' . $instance ); ?>">
				<?php esc_html_e( 'Full name', 'liftedpath' ); ?>
				<input id="<?php echo esc_attr( 'contact-name-' . $instance ); ?>" type="text" name="full_name" autocomplete="name" placeholder="<?php esc_attr_e( 'Your full name', 'liftedpath' ); ?>">
			</label>
			<label class="liftedpath-field" for="<?php echo esc_attr( 'contact-email-' . $instance ); ?>">
				<?php esc_html_e( 'Email', 'liftedpath' ); ?>
				<input id="<?php echo esc_attr( 'contact-email-' . $instance ); ?>" type="email" name="email" autocomplete="email" placeholder="<?php esc_attr_e( 'you@example.com', 'liftedpath' ); ?>">
			</label>
			<label class="liftedpath-field" for="<?php echo esc_attr( 'contact-reason-' . $instance ); ?>">
				<?php esc_html_e( 'I would like to', 'liftedpath' ); ?>
				<select id="<?php echo esc_attr( 'contact-reason-' . $instance ); ?>" name="reason">
					<?php foreach ( $reasons as $reason ) : ?>
						<option value="<?php echo esc_attr( $reason['value'] ); ?>"><?php echo esc_html( $reason['label'] ); ?></option>
					<?php endforeach; ?>
				</select>
			</label>
			<label class="liftedpath-field" for="<?php echo esc_attr( 'contact-message-' . $instance ); ?>">
				<?php esc_html_e( 'Message', 'liftedpath' ); ?>
				<textarea id="<?php echo esc_attr( 'contact-message-' . $instance ); ?>" name="message" rows="5" placeholder="<?php esc_attr_e( 'Tell us a little about how you would like to help', 'liftedpath' ); ?>"></textarea>
			</label>
			<button class="liftedpath-button" type="button">
				<?php echo esc_html( $button_label ); ?>
				<?php echo liftedpath_block_arrow(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static icon. ?>
			</button>
			<?php if ( '' !== $note ) : ?>
				<p class="liftedpath-form-note"><?php echo esc_html( $note ); ?></p>
			<?php endif; ?>
		</form>
	</div>
<?php liftedpath_block_wrapper_close( 'section' ); ?>
