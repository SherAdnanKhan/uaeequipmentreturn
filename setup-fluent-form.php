<?php
/**
 * Create the editable Fluent Forms quote form required by the SRS.
 *
 * Run with:
 * php /opt/homebrew/bin/wp eval-file setup-fluent-form.php --path=wordpress
 */

global $wpdb;

$forms_table = $wpdb->prefix . 'fluentform_forms';
$meta_table  = $wpdb->prefix . 'fluentform_form_meta';
$now         = current_time( 'mysql' );

function dbr_ff_text_field( $index, $name, $label, $placeholder = '', $required = false ) {
	return array(
		'index'          => $index,
		'element'        => 'input_text',
		'attributes'     => array(
			'type'        => 'text',
			'name'        => $name,
			'value'       => '',
			'class'       => '',
			'placeholder' => $placeholder,
		),
		'settings'       => array(
			'container_class'  => '',
			'label'            => $label,
			'label_placement'  => '',
			'admin_field_label'=> $label,
			'help_message'     => '',
			'validation_rules' => array(
				'required' => array(
					'value'   => $required,
					'message' => 'This field is required',
					'global'  => true,
				),
			),
			'conditional_logics' => array(),
		),
		'editor_options' => array(
			'title'      => 'Simple Text',
			'icon_class' => 'icon-text-width',
			'template'   => 'inputText',
		),
		'uniqElKey'      => 'dbr_' . $name,
	);
}

function dbr_ff_select_field( $index, $name, $label, $options, $required = false ) {
	return array(
		'index'          => $index,
		'element'        => 'select',
		'attributes'     => array(
			'name'  => $name,
			'value' => '',
			'id'    => '',
			'class' => '',
		),
		'settings'       => array(
			'label'              => $label,
			'admin_field_label'  => $label,
			'help_message'       => '',
			'container_class'    => '',
			'label_placement'    => '',
			'placeholder'        => '- Select -',
			'validation_rules'   => array(
				'required' => array(
					'value'   => $required,
					'message' => 'This field is required',
					'global'  => true,
				),
			),
			'conditional_logics' => array(),
		),
		'options'        => array_combine( $options, $options ),
		'editor_options' => array(
			'title'      => 'Dropdown',
			'icon_class' => 'icon-caret-square-o-down',
			'element'    => 'select',
			'template'   => 'select',
		),
		'uniqElKey'      => 'dbr_' . $name,
	);
}

$form_fields = array(
	'fields'       => array(
		dbr_ff_text_field( 1, 'name', 'Name', 'Your name', true ),
		dbr_ff_text_field( 2, 'phone', 'Phone', '+971...', true ),
		dbr_ff_text_field( 3, 'whatsapp', 'WhatsApp', '+971...', false ),
		array(
			'index'          => 4,
			'element'        => 'input_email',
			'attributes'     => array(
				'type'        => 'email',
				'name'        => 'email',
				'value'       => '',
				'id'          => '',
				'class'       => '',
				'placeholder' => 'you@example.com',
			),
			'settings'       => array(
				'container_class'  => '',
				'label'            => 'Email',
				'label_placement'  => '',
				'help_message'     => '',
				'admin_field_label'=> 'Email',
				'validation_rules' => array(
					'required' => array( 'value' => false, 'message' => 'This field is required', 'global' => true ),
					'email'    => array( 'value' => true, 'message' => 'This field must contain a valid email', 'global' => true ),
				),
				'conditional_logics' => array(),
			),
			'editor_options' => array(
				'title'      => 'Email Address',
				'icon_class' => 'icon-envelope-o',
				'template'   => 'inputText',
			),
			'uniqElKey'      => 'dbr_email',
		),
		dbr_ff_text_field( 5, 'job_location', 'Job location', 'Fujairah, Dubai, Sharjah...', true ),
		dbr_ff_select_field( 6, 'service_required', 'Service required', array( 'Site cleaning', 'Loading and material handling', 'Levelling or grading', 'Backfilling', 'Other skid steer work' ), true ),
		dbr_ff_text_field( 7, 'date_needed', 'Date needed', 'YYYY-MM-DD', false ),
		dbr_ff_select_field( 8, 'operator_required', 'Operator included', array( 'Yes, operator required', 'Discuss details' ), false ),
		dbr_ff_text_field( 9, 'attachment_required', 'Attachment required', 'Bucket, forks, auger...', false ),
		array(
			'index'          => 10,
			'element'        => 'textarea',
			'attributes'     => array(
				'name'        => 'message',
				'value'       => '',
				'id'          => '',
				'class'       => '',
				'placeholder' => 'Tell us about the job, access and duration.',
				'rows'        => 4,
				'cols'        => 2,
			),
			'settings'       => array(
				'container_class'  => '',
				'label'            => 'Message',
				'admin_field_label'=> 'Message',
				'label_placement'  => '',
				'help_message'     => '',
				'validation_rules' => array(
					'required' => array( 'value' => false, 'message' => 'This field is required', 'global' => true ),
				),
				'conditional_logics' => array(),
			),
			'editor_options' => array(
				'title'      => 'Text Area',
				'icon_class' => 'icon-paragraph',
				'template'   => 'inputTextarea',
			),
			'uniqElKey'      => 'dbr_message',
		),
	),
	'submitButton' => array(
		'uniqElKey'      => 'dbr_submit',
		'element'        => 'button',
		'attributes'     => array(
			'type'  => 'submit',
			'class' => '',
		),
		'settings'       => array(
			'align'            => 'left',
			'button_style'     => 'default',
			'container_class'  => '',
			'help_message'     => '',
			'background_color' => '#f4bf2a',
			'button_size'      => 'md',
			'color'            => '#171717',
			'button_ui'        => array(
				'type' => 'default',
				'text' => 'Send Quote Request',
			),
		),
		'editor_options' => array(
			'title' => 'Submit Button',
		),
	),
);

$existing_id = (int) $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$forms_table} WHERE title = %s LIMIT 1", 'Bobcat Quote Request' ) );

$form_data = array(
	'title'               => 'Bobcat Quote Request',
	'status'              => 'published',
	'appearance_settings' => null,
	'form_fields'         => wp_json_encode( $form_fields ),
	'has_payment'         => 0,
	'type'                => 'form',
	'conditions'          => null,
	'created_by'          => 1,
	'updated_at'          => $now,
);

if ( $existing_id ) {
	$wpdb->update( $forms_table, $form_data, array( 'id' => $existing_id ) );
	$form_id = $existing_id;
} else {
	$form_data['created_at'] = $now;
	$wpdb->insert( $forms_table, $form_data );
	$form_id = (int) $wpdb->insert_id;
}

$metas = array(
	'template_name'               => 'bobcat_quote_request',
	'formSettings'                => wp_json_encode(
		array(
			'confirmation' => array(
				'redirectTo'           => 'samePage',
				'messageToShow'        => 'Thank you. We will review your bobcat rental request and reply shortly. For urgent booking, WhatsApp +971 54 738 8695.',
				'customPage'           => null,
				'samePageFormBehavior' => 'hide_form',
				'customUrl'            => null,
			),
			'restrictions' => array(
				'limitNumberOfEntries' => array( 'enabled' => false ),
				'scheduleForm'         => array( 'enabled' => false ),
				'requireLogin'         => array( 'enabled' => false ),
				'denyEmptySubmission'  => array( 'enabled' => false ),
			),
			'layout'       => array(
				'labelPlacement'       => 'top',
				'helpMessagePlacement' => 'with_label',
				'errorMessagePlacement'=> 'inline',
				'asteriskPlacement'    => 'asterisk-right',
			),
			'delete_entry_on_submission' => 'no',
		)
	),
	'notifications'               => wp_json_encode(
		array(
			'name'           => 'Admin Quote Notification',
			'sendTo'         => array(
				'type'  => 'email',
				'email' => '{wp.admin_email}',
				'field' => 'email',
			),
			'fromName'       => '',
			'fromEmail'      => '',
			'replyTo'        => '',
			'bcc'            => '',
			'subject'        => 'New Bobcat Rental Quote Request',
			'message'        => '<p>{all_data}</p><p>This form submitted at: {embed_post.permalink}</p>',
			'conditionals'   => array( 'status' => false ),
			'enabled'        => true,
			'email_template' => '',
		)
	),
	'_primary_email_field'        => 'email',
	'advancedValidationSettings'  => wp_json_encode( array( 'status' => false ) ),
	'step_data_persistency_status'=> 'no',
);

foreach ( $metas as $meta_key => $value ) {
	$existing_meta = (int) $wpdb->get_var(
		$wpdb->prepare( "SELECT id FROM {$meta_table} WHERE form_id = %d AND meta_key = %s LIMIT 1", $form_id, $meta_key )
	);

	if ( $existing_meta ) {
		$wpdb->update( $meta_table, array( 'value' => $value ), array( 'id' => $existing_meta ) );
	} else {
		$wpdb->insert(
			$meta_table,
			array(
				'form_id'  => $form_id,
				'meta_key' => $meta_key,
				'value'    => $value,
			)
		);
	}
}

$contact = get_page_by_path( 'contact' );
if ( $contact ) {
	wp_update_post(
		array(
			'ID'           => $contact->ID,
			'post_content' => '<p>Call or WhatsApp 24/7 for current machine availability, operator-led bobcat rental and dispatch timing.</p><ul><li>Phone: +971 54 738 8695</li><li>WhatsApp: +971 54 738 8695</li><li>Headquarters: Dibba, Fujairah, United Arab Emirates</li><li>Service areas: all UAE emirates by delivery discussion</li></ul><p>Delivery can be free near Fujairah. Other UAE locations are quoted by distance, date, timing and booking duration.</p><h2>Quote request form</h2>[fluentform id="' . $form_id . '"]',
		)
	);
}

WP_CLI::success( 'Created Fluent Forms quote form ID ' . $form_id . ' and embedded it on Contact.' );
