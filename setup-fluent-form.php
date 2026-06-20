<?php
/**
 * Create editable Fluent Forms quote forms required by the SRS.
 *
 * Run with:
 * php /opt/homebrew/bin/wp eval-file setup-fluent-form.php --path=wordpress
 */

global $wpdb;

$forms_table = $wpdb->prefix . 'fluentform_forms';
$meta_table  = $wpdb->prefix . 'fluentform_form_meta';
$now         = current_time( 'mysql' );

$demo_ids = $wpdb->get_col(
	"SELECT id FROM {$forms_table} WHERE title IN ('Contact Form Demo', 'Subscription Form')"
);
if ( $demo_ids ) {
	$demo_ids_sql = implode( ',', array_map( 'absint', $demo_ids ) );
	$wpdb->query( "DELETE FROM {$meta_table} WHERE form_id IN ({$demo_ids_sql})" );
	$wpdb->query( "DELETE FROM {$forms_table} WHERE id IN ({$demo_ids_sql})" );
}

function dbr_ff_text_field( $index, $name, $label, $placeholder = '', $required = false, $required_message = 'This field is required' ) {
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
					'message' => $required_message,
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

function dbr_ff_email_field( $index, $label, $placeholder, $required_message, $email_message ) {
	return array(
		'index'          => $index,
		'element'        => 'input_email',
		'attributes'     => array(
			'type'        => 'email',
			'name'        => 'email',
			'value'       => '',
			'id'          => '',
			'class'       => '',
			'placeholder' => $placeholder,
		),
		'settings'       => array(
			'container_class'  => '',
			'label'            => $label,
			'label_placement'  => '',
			'help_message'     => '',
			'admin_field_label'=> $label,
			'validation_rules' => array(
				'required' => array( 'value' => false, 'message' => $required_message, 'global' => true ),
				'email'    => array( 'value' => true, 'message' => $email_message, 'global' => true ),
			),
			'conditional_logics' => array(),
		),
		'editor_options' => array(
			'title'      => 'Email Address',
			'icon_class' => 'icon-envelope-o',
			'template'   => 'inputText',
		),
		'uniqElKey'      => 'dbr_email',
	);
}

function dbr_ff_select_field( $index, $name, $label, $options, $placeholder, $required = false, $required_message = 'This field is required' ) {
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
			'placeholder'        => $placeholder,
			'validation_rules'   => array(
				'required' => array(
					'value'   => $required,
					'message' => $required_message,
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

function dbr_ff_textarea_field( $index, $label, $placeholder, $required_message ) {
	return array(
		'index'          => $index,
		'element'        => 'textarea',
		'attributes'     => array(
			'name'        => 'message',
			'value'       => '',
			'id'          => '',
			'class'       => '',
			'placeholder' => $placeholder,
			'rows'        => 4,
			'cols'        => 2,
		),
		'settings'       => array(
			'container_class'  => '',
			'label'            => $label,
			'admin_field_label'=> $label,
			'label_placement'  => '',
			'help_message'     => '',
			'validation_rules' => array(
				'required' => array( 'value' => false, 'message' => $required_message, 'global' => true ),
			),
			'conditional_logics' => array(),
		),
		'editor_options' => array(
			'title'      => 'Text Area',
			'icon_class' => 'icon-paragraph',
			'template'   => 'inputTextarea',
		),
		'uniqElKey'      => 'dbr_message',
	);
}

function dbr_upsert_fluent_form( $config ) {
	global $wpdb;

	$forms_table = $wpdb->prefix . 'fluentform_forms';
	$meta_table  = $wpdb->prefix . 'fluentform_form_meta';
	$now         = current_time( 'mysql' );

	$form_fields = array(
		'fields'       => array(
			dbr_ff_text_field( 1, 'name', $config['labels']['name'], $config['placeholders']['name'], true, $config['required_message'] ),
			dbr_ff_text_field( 2, 'phone', $config['labels']['phone'], '+971...', true, $config['required_message'] ),
			dbr_ff_text_field( 3, 'whatsapp', $config['labels']['whatsapp'], '+971...', false, $config['required_message'] ),
			dbr_ff_email_field( 4, $config['labels']['email'], 'you@example.com', $config['required_message'], $config['email_message'] ),
			dbr_ff_text_field( 5, 'job_location', $config['labels']['job_location'], $config['placeholders']['location'], true, $config['required_message'] ),
			dbr_ff_select_field( 6, 'service_required', $config['labels']['service_required'], $config['services'], $config['select_placeholder'], true, $config['required_message'] ),
			dbr_ff_text_field( 7, 'date_needed', $config['labels']['date_needed'], $config['placeholders']['date'], false, $config['required_message'] ),
			dbr_ff_select_field( 8, 'operator_required', $config['labels']['operator_required'], $config['operator_options'], $config['select_placeholder'], false, $config['required_message'] ),
			dbr_ff_text_field( 9, 'attachment_required', $config['labels']['attachment_required'], $config['placeholders']['attachment'], false, $config['required_message'] ),
			dbr_ff_textarea_field( 10, $config['labels']['message'], $config['placeholders']['message'], $config['required_message'] ),
		),
		'submitButton' => array(
			'uniqElKey'      => 'dbr_submit',
			'element'        => 'button',
			'attributes'     => array(
				'type'  => 'submit',
				'class' => '',
			),
			'settings'       => array(
				'align'            => $config['align'],
				'button_style'     => 'default',
				'container_class'  => '',
				'help_message'     => '',
				'background_color' => '#f4bf2a',
				'button_size'      => 'md',
				'color'            => '#171717',
				'button_ui'        => array(
					'type' => 'default',
					'text' => $config['submit'],
				),
			),
			'editor_options' => array(
				'title' => 'Submit Button',
			),
		),
	);

	$existing_id = (int) $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$forms_table} WHERE title = %s LIMIT 1", $config['title'] ) );
	$form_data   = array(
		'title'               => $config['title'],
		'status'              => 'published',
		'appearance_settings' => null,
		'form_fields'         => wp_json_encode( $form_fields, JSON_UNESCAPED_UNICODE ),
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
		'template_name'               => $config['template'],
		'formSettings'                => wp_json_encode(
			array(
				'confirmation' => array(
					'redirectTo'           => 'samePage',
					'messageToShow'        => $config['confirmation'],
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
			),
			JSON_UNESCAPED_UNICODE
		),
		'notifications'               => wp_json_encode(
			array(
				'name'           => $config['notification_name'],
				'sendTo'         => array(
					'type'  => 'email',
					'email' => '{wp.admin_email}',
					'field' => 'email',
				),
				'fromName'       => '',
				'fromEmail'      => '',
				'replyTo'        => '',
				'bcc'            => '',
				'subject'        => $config['notification_subject'],
				'message'        => '<p>{all_data}</p><p>This form submitted at: {embed_post.permalink}</p>',
				'conditionals'   => array( 'status' => false ),
				'enabled'        => true,
				'email_template' => '',
			),
			JSON_UNESCAPED_UNICODE
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

	return $form_id;
}

$english_form_id = dbr_upsert_fluent_form(
	array(
		'title'                => 'Bobcat Quote Request',
		'template'             => 'bobcat_quote_request_en',
		'align'                => 'left',
		'submit'               => 'Send Quote Request',
		'required_message'     => 'This field is required',
		'email_message'        => 'This field must contain a valid email',
		'confirmation'         => 'Thank you. We will review your bobcat rental request and reply shortly. For urgent booking, WhatsApp +971 54 738 8695.',
		'notification_name'    => 'Admin Quote Notification',
		'notification_subject' => 'New Bobcat Rental Quote Request',
		'labels'               => array(
			'name'                => 'Name',
			'phone'               => 'Phone',
			'whatsapp'            => 'WhatsApp',
			'email'               => 'Email',
			'job_location'        => 'Job location',
			'service_required'    => 'Service required',
			'date_needed'         => 'Date needed',
			'operator_required'   => 'Operator included',
			'attachment_required' => 'Attachment required',
			'message'             => 'Message',
		),
		'placeholders'         => array(
			'name'       => 'Your name',
			'location'   => 'Fujairah, Dubai, Sharjah...',
			'date'       => 'YYYY-MM-DD',
			'attachment' => 'Bucket, forks, auger...',
			'message'    => 'Tell us about the job, access and duration.',
		),
		'select_placeholder'   => '- Select -',
		'services'             => array( 'Site cleaning', 'Loading and material handling', 'Levelling or grading', 'Backfilling', 'Other skid steer work' ),
		'operator_options'     => array( 'Yes, operator required', 'Discuss details' ),
	)
);

$arabic_form_id = dbr_upsert_fluent_form(
	array(
		'title'                => 'طلب سعر بوبكات',
		'template'             => 'bobcat_quote_request_ar',
		'align'                => 'right',
		'submit'               => 'إرسال طلب السعر',
		'required_message'     => 'هذا الحقل مطلوب',
		'email_message'        => 'يرجى إدخال بريد إلكتروني صحيح',
		'confirmation'         => 'شكرا لك. سنراجع طلب تأجير البوبكات ونرد عليك قريبا. للحجز العاجل، واتساب +971 54 738 8695.',
		'notification_name'    => 'تنبيه طلب سعر',
		'notification_subject' => 'طلب سعر جديد لتأجير البوبكات',
		'labels'               => array(
			'name'                => 'الاسم',
			'phone'               => 'الهاتف',
			'whatsapp'            => 'واتساب',
			'email'               => 'البريد الإلكتروني',
			'job_location'        => 'موقع العمل',
			'service_required'    => 'الخدمة المطلوبة',
			'date_needed'         => 'التاريخ المطلوب',
			'operator_required'   => 'المشغل متوفر',
			'attachment_required' => 'الملحق المطلوب',
			'message'             => 'الرسالة',
		),
		'placeholders'         => array(
			'name'       => 'اسمك',
			'location'   => 'الفجيرة، دبي، الشارقة...',
			'date'       => 'YYYY-MM-DD',
			'attachment' => 'بكت، فورك، أوجر...',
			'message'    => 'اكتب تفاصيل العمل والدخول والمدة.',
		),
		'select_placeholder'   => '- اختر -',
		'services'             => array( 'تنظيف المواقع', 'التحميل ومناولة المواد', 'التسوية أو التمهيد', 'الردم', 'عمل آخر للسكيد ستير' ),
		'operator_options'     => array( 'نعم، أحتاج مشغلا', 'مناقشة التفاصيل' ),
	)
);

$contact = get_page_by_path( 'contact' );
if ( $contact ) {
	wp_update_post(
		array(
			'ID'           => $contact->ID,
			'post_content' => '<p>Call or WhatsApp 24/7 for current machine availability, operator-led bobcat rental and dispatch timing.</p><ul><li>Phone: +971 54 738 8695</li><li>WhatsApp: +971 54 738 8695</li><li>Headquarters: Dibba, Fujairah, United Arab Emirates</li><li>Service areas: all UAE emirates by delivery discussion</li></ul><p>Delivery can be free near Fujairah. Other UAE locations are quoted by distance, date, timing and booking duration.</p><h2>Quote request form</h2>[fluentform id="' . $english_form_id . '"]',
		)
	);

	if ( function_exists( 'pll_get_post' ) ) {
		$arabic_contact_id = pll_get_post( $contact->ID, 'ar' );
		if ( $arabic_contact_id ) {
			wp_update_post(
				array(
					'ID'           => $arabic_contact_id,
					'post_content' => '<p>اتصل أو راسلنا واتساب 24/7 لمعرفة توفر الآلة، تأجير البوبكات مع مشغل، ووقت الإرسال.</p><ul><li>الهاتف: +971 54 738 8695</li><li>واتساب: +971 54 738 8695</li><li>المقر: دبا، الفجيرة، الإمارات العربية المتحدة</li><li>مناطق الخدمة: جميع إمارات الدولة حسب مناقشة التوصيل</li></ul><p>يمكن أن يكون التوصيل مجانيا قرب الفجيرة. يتم تسعير باقي مناطق الإمارات حسب المسافة والتاريخ والتوقيت ومدة الحجز.</p><h2>نموذج طلب السعر</h2>[fluentform id="' . $arabic_form_id . '"]',
				)
			);
		}
	}
}

WP_CLI::success( 'Created Fluent Forms quote forms EN #' . $english_form_id . ' and AR #' . $arabic_form_id . ', then embedded them on Contact pages.' );
