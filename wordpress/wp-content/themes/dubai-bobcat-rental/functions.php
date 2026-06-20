<?php
/**
 * Theme setup for UAE Equipment Rental.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function dbr_setup() {
	load_theme_textdomain( 'dubai-bobcat-rental', get_template_directory() . '/languages' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'custom-logo' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );

	register_nav_menus(
		array(
			'primary' => __( 'Primary Menu', 'dubai-bobcat-rental' ),
			'footer'  => __( 'Footer Menu', 'dubai-bobcat-rental' ),
		)
	);
}
add_action( 'after_setup_theme', 'dbr_setup' );

function dbr_enqueue_assets() {
	wp_enqueue_style( 'dbr-style', get_stylesheet_uri(), array(), '1.1.6' );
	wp_enqueue_script( 'dbr-script', get_template_directory_uri() . '/assets/site.js', array(), '1.1.6', true );
	wp_localize_script(
		'dbr-script',
		'dbrBusiness',
		array(
			'whatsapp' => dbr_get_business_value( 'whatsapp', '+971547388695' ),
			'lang'     => dbr_current_lang(),
			'messages' => array(
				'intro'   => dbr_text( 'Hello, I need a CAT 226B bobcat rental quote in the UAE.', 'مرحبا، أحتاج عرض سعر لتأجير بوبكات CAT 226B في الإمارات.' ),
				'invalid' => dbr_text( 'Please add name, phone, job location and service before sending.', 'يرجى إضافة الاسم ورقم الهاتف وموقع العمل ونوع الخدمة قبل الإرسال.' ),
				'opening' => dbr_text( 'Opening WhatsApp with your quote details.', 'سيتم فتح واتساب مع تفاصيل طلب السعر.' ),
				'openMenu'=> dbr_text( 'Open menu', 'فتح القائمة' ),
				'closeMenu'=> dbr_text( 'Close menu', 'إغلاق القائمة' ),
				'name'    => dbr_text( 'Name', 'الاسم' ),
				'phone'   => dbr_text( 'Phone', 'الهاتف' ),
				'wa'      => dbr_text( 'WhatsApp', 'واتساب' ),
				'loc'     => dbr_text( 'Location', 'الموقع' ),
				'service' => dbr_text( 'Service', 'الخدمة' ),
				'date'    => dbr_text( 'Date', 'التاريخ' ),
				'operator'=> dbr_text( 'Operator', 'المشغل' ),
				'attach'  => dbr_text( 'Attachment', 'الملحق' ),
				'message' => dbr_text( 'Message', 'الرسالة' ),
			),
		)
	);
}
add_action( 'wp_enqueue_scripts', 'dbr_enqueue_assets' );

function dbr_current_lang() {
	if ( function_exists( 'pll_current_language' ) ) {
		$lang = pll_current_language( 'slug' );
		if ( $lang ) {
			return $lang;
		}
	}

	return is_rtl() ? 'ar' : 'en';
}

function dbr_is_ar() {
	return 'ar' === dbr_current_lang();
}

function dbr_text( $english, $arabic ) {
	return dbr_is_ar() ? $arabic : $english;
}

function dbr_home_url( $path = '/' ) {
	if ( '/' === $path && function_exists( 'pll_home_url' ) ) {
		return pll_home_url();
	}

	return home_url( $path );
}

function dbr_page_url( $slug, $fallback = '' ) {
	$page = get_page_by_path( $slug );
	if ( ! $page ) {
		$matches = get_posts(
			array(
				'name'        => $slug,
				'post_type'   => 'page',
				'post_status' => 'publish',
				'numberposts' => 1,
			)
		);
		$page = $matches ? $matches[0] : null;
	}
	if ( $page instanceof WP_Post ) {
		$page_id = $page->ID;
		if ( function_exists( 'pll_get_post' ) ) {
			$translated_id = pll_get_post( $page_id, dbr_current_lang() );
			if ( $translated_id ) {
				$page_id = $translated_id;
			}
		}
		return get_permalink( $page_id );
	}

	return home_url( $fallback ? $fallback : '/' . trim( $slug, '/' ) . '/' );
}

function dbr_primary_nav_items() {
	if ( dbr_is_ar() ) {
		return array(
			array( 'label' => 'الرئيسية', 'url' => dbr_home_url( '/' ) ),
			array( 'label' => 'تأجير بوبكات', 'url' => dbr_page_url( 'bobcat-rental-dubai', '/services/bobcat-rental-dubai/' ) ),
			array( 'label' => 'CAT 226B', 'url' => dbr_page_url( 'cat-226b-skid-steer-loader', '/machines/cat-226b-skid-steer-loader/' ) ),
			array( 'label' => 'الخدمات', 'url' => dbr_page_url( 'services', '/services/' ) ),
			array( 'label' => 'مناطق الخدمة', 'url' => dbr_page_url( 'service-areas', '/service-areas/' ) ),
			array( 'label' => 'المدونة', 'url' => dbr_page_url( 'blog', '/blog/' ) ),
			array( 'label' => 'اتصل بنا', 'url' => dbr_page_url( 'contact', '/contact/' ) ),
		);
	}

	return array(
		array( 'label' => 'Home', 'url' => dbr_home_url( '/' ) ),
		array( 'label' => 'Bobcat Rental UAE', 'url' => dbr_page_url( 'bobcat-rental-dubai', '/services/bobcat-rental-dubai/' ) ),
		array( 'label' => 'CAT 226B', 'url' => dbr_page_url( 'cat-226b-skid-steer-loader', '/machines/cat-226b-skid-steer-loader/' ) ),
		array( 'label' => 'Services', 'url' => dbr_page_url( 'services', '/services/' ) ),
		array( 'label' => 'Service Areas', 'url' => dbr_page_url( 'service-areas', '/service-areas/' ) ),
		array( 'label' => 'Blog', 'url' => dbr_page_url( 'blog', '/blog/' ) ),
		array( 'label' => 'Contact', 'url' => dbr_page_url( 'contact', '/contact/' ) ),
	);
}

function dbr_redirect_legacy_paths() {
	$request_path = isset( $_SERVER['REQUEST_URI'] ) ? wp_parse_url( wp_unslash( $_SERVER['REQUEST_URI'] ), PHP_URL_PATH ) : '';
	$request_path = trim( (string) $request_path, '/' );

	if ( 'contact-and-quote-request' === $request_path ) {
		wp_safe_redirect( dbr_page_url( 'contact', '/contact/' ), 301 );
		exit;
	}
}
add_action( 'template_redirect', 'dbr_redirect_legacy_paths' );

function dbr_get_ar_translations() {
	return array(
		'Language switcher' => 'مبدل اللغة',
		'Open menu' => 'فتح القائمة',
		'Close menu' => 'إغلاق القائمة',
		'Skip to content' => 'تخطي إلى المحتوى',
		'CAT 226B bobcat with operator' => 'بوبكات CAT 226B مع مشغل',
		'Primary navigation' => 'القائمة الرئيسية',
		'Call' => 'اتصال',
		'WhatsApp' => 'واتساب',
		'Mobile contact actions' => 'أزرار التواصل للجوال',
		'Rental Pages' => 'صفحات التأجير',
		'Bobcat Rental Dubai' => 'تأجير بوبكات دبي',
		'CAT 226B Specs' => 'مواصفات CAT 226B',
		'UAE Service Areas' => 'مناطق الخدمة في الإمارات',
		'Contact' => 'اتصل بنا',
		'Quote Form' => 'نموذج طلب السعر',
		'CAT 226B bobcat and skid steer loader rental with operator for site cleaning, loading, levelling, backfilling and compact material handling across the UAE.' => 'تأجير بوبكات وسكيد ستير لودر CAT 226B مع مشغل لتنظيف المواقع والتحميل والتسوية والردم ومناولة المواد داخل الإمارات.',
		'Home' => 'الرئيسية',
		'Blog' => 'المدونة',
		'Breadcrumb' => 'مسار التصفح',
		'Bobcat Rental UAE' => 'تأجير بوبكات في الإمارات',
		'CAT 226B skid steer loader rental with operator for site cleaning, loading, levelling, trench backfilling and compact material handling across the UAE.' => 'تأجير سكيد ستير لودر CAT 226B مع مشغل لتنظيف المواقع والتحميل والتسوية وردم الخنادق ومناولة المواد في أنحاء الإمارات.',
		'WhatsApp Quote' => 'طلب سعر واتساب',
		'Call Now' => 'اتصل الآن',
		'Rental highlights' => 'مميزات التأجير',
		'24/7 enquiries' => 'استفسارات 24/7',
		'Call or WhatsApp any time.' => 'اتصل أو راسلنا واتساب في أي وقت.',
		'Operator included' => 'المشغل متوفر',
		'Bobcat is supplied with operator.' => 'يتم توفير البوبكات مع مشغل.',
		'Fujairah HQ' => 'المقر في الفجيرة',
		'Free delivery near Fujairah.' => 'توصيل مجاني قرب الفجيرة.',
		'UAE coverage' => 'تغطية داخل الإمارات',
		'Delivery quoted by location.' => 'يتم تسعير التوصيل حسب الموقع.',
		'Primary service' => 'الخدمة الرئيسية',
		'Bobcat rental with operator for compact UAE jobsites.' => 'تأجير بوبكات مع مشغل لمواقع العمل الصغيرة في الإمارات.',
		'Many buyers search for "bobcat rental", while the technical machine type is a skid steer loader. The available machine is a CAT 226B supplied with an operator for practical site work.' => 'كثير من العملاء يبحثون عن "تأجير بوبكات"، بينما الاسم الفني للآلة هو سكيد ستير لودر. الآلة المتوفرة هي CAT 226B ويتم توفيرها مع مشغل لأعمال الموقع العملية.',
		'The service is suited to construction, landscaping and maintenance jobs where manoeuvrability, fast loading and tight-area access matter. Minimum booking is flexible, with half-day, full-day and multi-day work discussed by location and scope.' => 'الخدمة مناسبة لأعمال البناء والتنسيق والصيانة عندما تكون المناورة والتحميل السريع والدخول للمساحات الضيقة مهمة. مدة الحجز مرنة، ويتم مناقشة نصف يوم أو يوم كامل أو عدة أيام حسب الموقع ونطاق العمل.',
		'Common jobs' => 'الأعمال الشائعة',
		'Site cleaning' => 'تنظيف المواقع',
		'Move debris, loose material and construction waste from active or recently completed jobsites.' => 'نقل المخلفات والمواد السائبة ونفايات البناء من مواقع العمل النشطة أو المنتهية حديثا.',
		'Loading and handling' => 'التحميل والمناولة',
		'Support bucket loading, unloading, short-haul material movement and practical site logistics.' => 'دعم التحميل والتفريغ ونقل المواد لمسافات قصيرة وتنظيم العمل داخل الموقع.',
		'Levelling and backfilling' => 'التسوية والردم',
		'Prepare compact surfaces, fill trenches and support interlock or landscaping preparation.' => 'تجهيز الأسطح الصغيرة وردم الخنادق ودعم تجهيز الإنترلوك أو أعمال التنسيق.',
		'Machine page content' => 'محتوى صفحة الآلة',
		'CAT 226B skid steer loader specifications' => 'مواصفات سكيد ستير لودر CAT 226B',
		'This machine is backed by supplied photos and registration details. Public specs below help visitors judge job fit without exposing licence dates or private document details.' => 'هذه الآلة مدعومة بصور وتفاصيل تسجيل متوفرة. المواصفات العامة أدناه تساعد الزائر على تقييم مناسبة الآلة للعمل دون نشر تواريخ الملكية أو تفاصيل المستندات الخاصة.',
		'Machine type' => 'نوع الآلة',
		'Model year' => 'سنة الموديل',
		'Rated operating capacity' => 'سعة التشغيل المقدرة',
		'Operating weight' => 'وزن التشغيل',
		'Attachment examples' => 'أمثلة الملحقات',
		'Bucket' => 'بكت',
		'Forks' => 'فورك',
		'Auger' => 'أوجر',
		'Broom' => 'مكنسة',
		'Trencher' => 'حفار خنادق',
		'Hammer' => 'هامر',
		'Loading and cleaning' => 'تحميل وتنظيف',
		'Pallet movement' => 'نقل البالتات',
		'Post holes' => 'حفر فتحات الأعمدة',
		'Surface sweeping' => 'كنس الأسطح',
		'Narrow trenching' => 'حفر خنادق ضيقة',
		'Breaking support' => 'دعم أعمال التكسير',
		'Customer feedback' => 'آراء العملاء',
		'Recent WhatsApp reviews from bobcat rental jobs.' => 'آراء حديثة عبر واتساب من أعمال تأجير البوبكات.',
		'Short customer notes shared after site cleaning, sand shifting, levelling and compact construction support work.' => 'ملاحظات قصيرة من العملاء بعد أعمال تنظيف مواقع ونقل رمل وتسوية ودعم أعمال بناء صغيرة.',
		'Customer feedback via WhatsApp' => 'رأي عميل عبر واتساب',
		'Excellent bobcat rental service in Dubai. The machine arrived on time, the operator knew the work, and the site cleaning was completed properly. Highly recommended.' => 'خدمة تأجير بوبكات ممتازة في دبي. وصلت الآلة في الوقت المحدد، وكان المشغل يعرف العمل جيدا، وتم تنظيف الموقع بشكل صحيح. ننصح بها.',
		'We hired their skid steer loader for sand shifting and levelling work. Professional service, fair pricing and a quick response on WhatsApp.' => 'استأجرنا السكيد ستير لنقل الرمل وأعمال التسوية. خدمة احترافية، سعر مناسب، ورد سريع على واتساب.',
		'Good experience with UAE Equipment Rental. The CAT bobcat was in good condition and helped us finish construction site work faster.' => 'تجربة جيدة مع UAE Equipment Rental. كان بوبكات CAT بحالة جيدة وساعدنا على إنهاء عمل موقع البناء بشكل أسرع.',
		'Reliable bobcat rental service with operator. They handled debris removal and land levelling smoothly, and I would use their service again.' => 'خدمة تأجير بوبكات موثوقة مع مشغل. تعاملوا مع إزالة المخلفات وتسوية الأرض بسلاسة، وسأستخدم خدمتهم مرة أخرى.',
		'Very helpful team and fast service. We needed a bobcat urgently for a small construction job in Dubai, and they arranged it quickly.' => 'فريق متعاون وخدمة سريعة. كنا نحتاج بوبكات بشكل عاجل لعمل بناء صغير في دبي وتم ترتيبه بسرعة.',
		'Real machine media' => 'صور وفيديوهات الآلة',
		'See the CAT 226B before booking.' => 'شاهد CAT 226B قبل الحجز.',
		'These supplied photos and video help buyers confirm they are requesting the right compact loader before dispatch is discussed.' => 'تساعد هذه الصور والفيديوهات العملاء على التأكد من طلب الآلة المناسبة قبل مناقشة الإرسال.',
		'CAT 226B skid steer loader with bucket in Fujairah yard' => 'سكيد ستير لودر CAT 226B مع بكت في ساحة الفجيرة',
		'CAT 226B with bucket attachment' => 'CAT 226B مع ملحق البكت',
		'Bobcat rental phone number displayed on the machine' => 'رقم تأجير البوبكات ظاهر على الآلة',
		'Direct booking number on machine' => 'رقم الحجز المباشر على الآلة',
		'Short machine walkaround video' => 'فيديو قصير للآلة',
		'Get availability' => 'تحقق من التوفر',
		'Send the job details for a fast quote.' => 'أرسل تفاصيل العمل للحصول على سعر سريع.',
		'Include location, date, duration, work type and attachment requirement. Operator is provided with the bobcat. Delivery is free near Fujairah and quoted for other UAE locations.' => 'اذكر الموقع والتاريخ والمدة ونوع العمل والملحق المطلوب. يتم توفير المشغل مع البوبكات. التوصيل مجاني قرب الفجيرة ويتم تسعيره لباقي مناطق الإمارات.',
		'24/7 enquiry path through call and WhatsApp' => 'استفسارات 24/7 عبر الاتصال وواتساب',
		'Built for contractors, supervisors and landscaping teams' => 'مناسب للمقاولين والمشرفين وفرق تنسيق المواقع',
		'Flexible minimum booking: half-day, full-day or longer work by scope' => 'مدة حجز مرنة: نصف يوم أو يوم كامل أو مدة أطول حسب العمل',
		'Quote request form' => 'نموذج طلب السعر',
		'Name' => 'الاسم',
		'Phone' => 'الهاتف',
		'Job location' => 'موقع العمل',
		'Service needed' => 'الخدمة المطلوبة',
		'Choose service' => 'اختر الخدمة',
		'Loading and material handling' => 'التحميل ومناولة المواد',
		'Levelling or grading' => 'التسوية أو التمهيد',
		'Backfilling' => 'الردم',
		'Other skid steer work' => 'عمل آخر للسكيد ستير',
		'Date needed' => 'التاريخ المطلوب',
		'Operator included' => 'المشغل متوفر',
		'Yes, operator required' => 'نعم، أحتاج مشغلا',
		'Discuss details' => 'مناقشة التفاصيل',
		'Attachment required' => 'الملحق المطلوب',
		'Message' => 'الرسالة',
		'Send Job Details on WhatsApp' => 'إرسال تفاصيل العمل عبر واتساب',
		'Service areas' => 'مناطق الخدمة',
		'Real service areas across the UAE.' => 'مناطق خدمة فعلية في أنحاء الإمارات.',
		'Headquarters are in Dibba, Fujairah. Delivery is free near Fujairah, while other emirates are quoted by distance, timing and job duration.' => 'المقر في دبا، الفجيرة. التوصيل مجاني قرب الفجيرة، أما باقي الإمارات فيتم تسعيرها حسب المسافة والتوقيت ومدة العمل.',
		'Nearest dispatch area with free local delivery discussion.' => 'أقرب منطقة إرسال مع إمكانية التوصيل المحلي مجانا.',
		'Common contractor and site-cleaning demand areas.' => 'مناطق شائعة لطلبات المقاولين وتنظيف المواقع.',
		'Available by job location, timing and duration.' => 'متاح حسب موقع العمل والتوقيت والمدة.',
		'Long-distance delivery quoted after job details are shared.' => 'يتم تسعير التوصيل للمسافات الطويلة بعد إرسال تفاصيل العمل.',
		'Fujairah & Dibba' => 'الفجيرة ودبا',
		'Dubai & Sharjah' => 'دبي والشارقة',
		'Ajman & RAK' => 'عجمان ورأس الخيمة',
		'Ajman, RAK & UAQ' => 'عجمان ورأس الخيمة وأم القيوين',
		'Abu Dhabi & Al Ain' => 'أبوظبي والعين',
		'Rental guides' => 'أدلة التأجير',
		'Build trust before the quote.' => 'معلومات تساعد قبل طلب السعر.',
		'Support content helps buyers understand machine fit, price factors, operator requirements and common Dubai jobsite use cases.' => 'يساعد المحتوى العملاء على فهم مناسبة الآلة وعوامل السعر ومتطلبات المشغل واستخدامات مواقع العمل في دبي.',
		'Guide' => 'دليل',
		'View all guides' => 'عرض كل الأدلة',
		'Buyer questions' => 'أسئلة العملاء',
		'Bobcat rental FAQs' => 'أسئلة شائعة عن تأجير البوبكات',
		'Is this a Bobcat or a skid steer loader?' => 'هل هذه بوبكات أم سكيد ستير لودر؟',
		'Customers often use "bobcat" as a general rental term. The available machine is a CAT 226B skid steer loader.' => 'غالبا يستخدم العملاء كلمة بوبكات كاسم شائع للتأجير. الآلة المتوفرة هي سكيد ستير لودر CAT 226B.',
		'Can I hire it with an operator?' => 'هل يمكن استئجارها مع مشغل؟',
		'Yes. The bobcat is provided with an operator for rental jobs.' => 'نعم. يتم توفير البوبكات مع مشغل لأعمال التأجير.',
		'What information is needed for a quote?' => 'ما المعلومات المطلوبة للحصول على سعر؟',
		'Send job location, work type, date, expected duration and any attachment requirement. Photos or a map pin help speed up the quote.' => 'أرسل موقع العمل ونوع العمل والتاريخ والمدة المتوقعة وأي ملحق مطلوب. الصور أو رابط الموقع يساعدان على تسريع السعر.',
		'Which attachments are possible?' => 'ما الملحقات الممكنة؟',
		'Common skid steer tools include buckets, forks, augers, brooms, trenchers and hammers, depending on actual availability.' => 'تشمل أدوات السكيد ستير الشائعة البكت والفورك والأوجر والمكنسة وحفار الخنادق والهامر حسب التوفر الفعلي.',
		'Is delivery free?' => 'هل التوصيل مجاني؟',
		'Delivery can be free near Fujairah. For Dubai, Sharjah, Abu Dhabi and other UAE locations, delivery cost is discussed after the job location and duration are known.' => 'يمكن أن يكون التوصيل مجانيا قرب الفجيرة. لدبي والشارقة وأبوظبي وباقي مناطق الإمارات، يتم تحديد تكلفة التوصيل بعد معرفة الموقع والمدة.',
		'What is the minimum booking duration?' => 'ما أقل مدة حجز؟',
		'Standard jobs are usually discussed as half-day, full-day or multi-day bookings. If the job is small, send details and we will confirm what is practical.' => 'عادة تتم مناقشة الأعمال كنصف يوم أو يوم كامل أو عدة أيام. إذا كان العمل صغيرا، أرسل التفاصيل وسنؤكد الخيار العملي.',
		'Rental service' => 'خدمة تأجير',
		'Machine profile' => 'صفحة الآلة',
		'Local dispatch' => 'التوصيل المحلي',
		'Quote request' => 'طلب سعر',
		'UAE equipment rental' => 'تأجير معدات في الإمارات',
		'Send Job Details' => 'إرسال تفاصيل العمل',
		'Quote checklist' => 'قائمة تفاصيل السعر',
		'For a useful quote, send:' => 'للحصول على سعر دقيق، أرسل:',
		'Job location or map pin' => 'موقع العمل أو رابط الخريطة',
		'Work type and site access' => 'نوع العمل وإمكانية دخول الموقع',
		'Date, duration and delivery timing' => 'التاريخ والمدة ووقت التوصيل',
		'Attachment requirement if known' => 'الملحق المطلوب إن وجد',
		'Fast path' => 'الطريقة الأسرع',
		'Confirm machine fit before booking.' => 'تأكد من مناسبة الآلة قبل الحجز.',
		'The CAT 226B is best for compact-site support. Share the job details and we will confirm if it suits the work before dispatch is promised.' => 'آلة CAT 226B مناسبة لدعم المواقع الصغيرة. شارك تفاصيل العمل وسنؤكد إن كانت مناسبة قبل تأكيد الإرسال.',
		'Check Availability' => 'تحقق من التوفر',
		'Use case' => 'استخدام',
		'Loading support' => 'دعم التحميل',
		'Move loose debris, sand, soil and site waste faster than manual labour.' => 'نقل المخلفات والرمل والتربة ونفايات الموقع أسرع من العمل اليدوي.',
		'Support bucket loading, short-distance material movement and practical site logistics.' => 'دعم التحميل بالبكت ونقل المواد لمسافات قصيرة وتنظيم العمل داخل الموقع.',
		'Handle compact trench backfilling, surface preparation and small plot grading.' => 'تنفيذ ردم الخنادق الصغيرة وتجهيز السطح وتسوية المساحات المحدودة.',
		'Machine proof' => 'إثبات الآلة',
		'Real CAT 226B photos are now used on the website.' => 'يتم استخدام صور حقيقية لآلة CAT 226B في الموقع.',
		'The machine is a Caterpillar 226B. Public listing details show model year 2015, yellow colour and American origin.' => 'الآلة هي Caterpillar 226B. التفاصيل العامة تظهر موديل 2015 واللون الأصفر والمنشأ الأمريكي.',
		'Local SEO rule' => 'قاعدة الخدمة المحلية',
		'UAE-wide service with Fujairah headquarters.' => 'خدمة داخل الإمارات مع مقر في الفجيرة.',
		'The machine can be delivered across the UAE. Delivery is free near Fujairah and quoted for other locations.' => 'يمكن توصيل الآلة داخل الإمارات. التوصيل مجاني قرب الفجيرة ويتم تسعيره لباقي المواقع.',
		'Headquarters area and best delivery zone.' => 'منطقة المقر وأفضل نطاق للتوصيل.',
		'Available for contractor and construction support jobs.' => 'متاح لأعمال المقاولين ودعم مواقع البناء.',
		'View Dubai page' => 'عرض صفحة دبي',
		'Delivery quoted by exact location and working duration.' => 'يتم تسعير التوصيل حسب الموقع الدقيق ومدة العمل.',
		'Long-distance booking available after details are discussed.' => 'الحجز للمسافات الطويلة متاح بعد مناقشة التفاصيل.',
		'Direct contact' => 'تواصل مباشر',
		'Call or WhatsApp for urgent availability.' => 'اتصل أو راسل واتساب للتوفر العاجل.',
		'Helpful reading before you book' => 'قراءات مفيدة قبل الحجز',
		'Bobcat Rental Guides for Dubai Jobsites' => 'أدلة تأجير البوبكات لمواقع العمل في دبي',
		'Practical articles for contractors and site supervisors comparing bobcat rental, skid steer loader jobs, CAT 226B specifications, attachments, pricing and booking details.' => 'مقالات عملية للمقاولين ومشرفي المواقع حول تأجير البوبكات وأعمال السكيد ستير ومواصفات CAT 226B والملحقات والأسعار والحجز.',
		'Content clusters' => 'محاور المحتوى',
		'Machine specs and use cases' => 'مواصفات الآلة والاستخدامات',
		'Pricing and booking guides' => 'أدلة الأسعار والحجز',
		'Dubai jobsite applications' => 'استخدامات مواقع العمل في دبي',
		'Read guide' => 'قراءة الدليل',
		'No guides found yet.' => 'لا توجد أدلة حتى الآن.',
		'Guide details' => 'تفاصيل الدليل',
		'UAE rental guide' => 'دليل تأجير في الإمارات',
		'%d min read' => 'قراءة %d دقائق',
		'CAT 226B with operator' => 'CAT 226B مع مشغل',
		'CAT 226B skid steer loader with bucket attachment' => 'سكيد ستير لودر CAT 226B مع ملحق البكت',
		'CAT 226B bobcat supplied with operator for UAE jobsites' => 'بوبكات CAT 226B مع مشغل لمواقع العمل في الإمارات',
		'Before you book' => 'قبل الحجز',
		'The fastest quote comes from clear job details: location, access, work type, date, expected duration and attachment requirement.' => 'أسرع سعر يأتي من تفاصيل عمل واضحة: الموقع، الدخول، نوع العمل، التاريخ، المدة المتوقعة والملحق المطلوب.',
		'Need this machine?' => 'تحتاج هذه الآلة؟',
		'Send the job details for a quote.' => 'أرسل تفاصيل العمل للحصول على سعر.',
		'Operator is provided with the bobcat. Delivery is free near Fujairah and quoted for other UAE locations.' => 'يتم توفير المشغل مع البوبكات. التوصيل مجاني قرب الفجيرة ويتم تسعيره لباقي مناطق الإمارات.',
		'Location or map pin' => 'الموقع أو رابط الخريطة',
		'Work type and access notes' => 'نوع العمل وملاحظات الدخول',
		'Date and expected duration' => 'التاريخ والمدة المتوقعة',
		'Quick contact' => 'تواصل سريع',
		'24/7 call or WhatsApp' => 'اتصال أو واتساب 24/7',
		'More rental guides' => 'المزيد من أدلة التأجير',
	);
}

function dbr_translate_ar( $translation, $text, $domain ) {
	if ( 'dubai-bobcat-rental' !== $domain || ! dbr_is_ar() ) {
		return $translation;
	}

	$translations = dbr_get_ar_translations();
	return $translations[ $text ] ?? $translation;
}
add_filter( 'gettext', 'dbr_translate_ar', 10, 3 );

function dbr_get_business_value( $key, $fallback = '' ) {
	$value = get_theme_mod( 'dbr_' . $key, $fallback );
	return is_string( $value ) ? $value : $fallback;
}

function dbr_phone_href() {
	return 'tel:' . preg_replace( '/[^0-9+]/', '', dbr_get_business_value( 'phone', '+971 54 738 8695' ) );
}

function dbr_whatsapp_href( $message = '' ) {
	if ( '' === $message ) {
		$message = dbr_text( 'Hello, I need a CAT 226B bobcat rental quote in the UAE.', 'مرحبا، أحتاج عرض سعر لتأجير بوبكات CAT 226B في الإمارات.' );
	}
	$number = preg_replace( '/[^0-9]/', '', dbr_get_business_value( 'whatsapp', '+971 54 738 8695' ) );
	return 'https://wa.me/' . $number . '?text=' . rawurlencode( $message );
}

function dbr_language_switcher() {
	if ( ! function_exists( 'pll_the_languages' ) ) {
		return;
	}

	$languages = pll_the_languages(
		array(
			'raw'                    => 1,
			'hide_if_empty'          => 0,
			'hide_if_no_translation' => 0,
		)
	);

	if ( empty( $languages ) || ! is_array( $languages ) ) {
		return;
	}
	?>
	<div class="language-switcher" aria-label="<?php esc_attr_e( 'Language switcher', 'dubai-bobcat-rental' ); ?>">
		<?php foreach ( $languages as $language ) : ?>
			<a
				href="<?php echo esc_url( $language['url'] ); ?>"
				lang="<?php echo esc_attr( $language['locale'] ); ?>"
				<?php echo ! empty( $language['current_lang'] ) ? 'aria-current="true"' : ''; ?>
			>
				<?php echo esc_html( strtoupper( $language['slug'] ) ); ?>
			</a>
		<?php endforeach; ?>
	</div>
	<?php
}

function dbr_customize_register( $wp_customize ) {
	$wp_customize->add_section(
		'dbr_business',
		array(
			'title'       => __( 'Business Details', 'dubai-bobcat-rental' ),
			'description' => __( 'Review business details before production launch. These values power CTAs and schema.', 'dubai-bobcat-rental' ),
			'priority'    => 35,
		)
	);

	$fields = array(
		'business_name' => array( 'Business name', 'UAE Equipment Rental' ),
		'legal_name'    => array( 'Legal / licence name', 'UAE Equipment Rental' ),
		'phone'         => array( 'Phone number', '+971 54 738 8695' ),
		'whatsapp'      => array( 'WhatsApp number', '+971 54 738 8695' ),
		'address'       => array( 'Address', 'Headquarters: Dibba, Fujairah, United Arab Emirates' ),
		'hours'         => array( 'Opening hours', '24/7' ),
		'service_areas' => array( 'Service areas', 'Dibba, Fujairah, Dubai, Sharjah, Ajman, Ras Al Khaimah, Umm Al Quwain, Abu Dhabi, Al Ain, UAE' ),
	);

	foreach ( $fields as $key => $data ) {
		$wp_customize->add_setting(
			'dbr_' . $key,
			array(
				'default'           => $data[1],
				'sanitize_callback' => 'sanitize_text_field',
			)
		);
		$wp_customize->add_control(
			'dbr_' . $key,
			array(
				'label'   => $data[0],
				'section' => 'dbr_business',
				'type'    => 'text',
			)
		);
	}
}
add_action( 'customize_register', 'dbr_customize_register' );

function dbr_schema() {
	if ( ! is_front_page() ) {
		return;
	}

	$site_url  = dbr_home_url( '/' );
	$image_url = get_template_directory_uri() . '/assets/bobcat-hero.jpg';
	$areas     = array_map( 'trim', explode( ',', dbr_get_business_value( 'service_areas', 'Dubai' ) ) );

	$schema = array(
		'@context' => 'https://schema.org',
		'@graph'   => array(
			array(
				'@type'       => 'LocalBusiness',
				'@id'         => $site_url . '#business',
				'name'        => dbr_get_business_value( 'business_name', 'UAE Equipment Rental' ),
				'legalName'   => dbr_get_business_value( 'legal_name', 'UAE Equipment Rental' ),
				'url'         => $site_url,
				'telephone'   => dbr_get_business_value( 'phone', '+971 54 738 8695' ),
				'address'     => dbr_get_business_value( 'address', 'Headquarters: Dibba, Fujairah, United Arab Emirates' ),
				'areaServed'  => $areas,
				'openingHours'=> 'Mo-Su 00:00-23:59',
				'priceRange'  => '$$',
				'image'       => $image_url,
				'description' => dbr_text(
					'CAT 226B bobcat and skid steer loader rental with operator across the UAE from Dibba, Fujairah.',
					'تأجير بوبكات وسكيد ستير لودر CAT 226B مع مشغل داخل الإمارات من دبا، الفجيرة.'
				),
			),
			array(
				'@type'       => 'Service',
				'@id'         => $site_url . '#service',
				'serviceType' => dbr_text( 'Bobcat rental UAE', 'تأجير بوبكات في الإمارات' ),
				'provider'    => array( '@id' => $site_url . '#business' ),
				'areaServed'  => $areas,
				'description' => dbr_text(
					'CAT 226B skid steer loader rental with operator for site cleaning, loading, grading, backfilling and material handling.',
					'تأجير سكيد ستير لودر CAT 226B مع مشغل لتنظيف المواقع والتحميل والتسوية والردم ومناولة المواد.'
				),
			),
			array(
				'@type'      => 'FAQPage',
				'@id'        => $site_url . '#faq',
				'mainEntity' => array(
					array(
						'@type'          => 'Question',
						'name'           => dbr_text( 'Is this a Bobcat or a skid steer loader?', 'هل هذه بوبكات أم سكيد ستير لودر؟' ),
						'acceptedAnswer' => array(
							'@type' => 'Answer',
							'text'  => dbr_text( 'Many customers search for bobcat rental, but the available machine is a CAT 226B skid steer loader.', 'كثير من العملاء يبحثون عن تأجير بوبكات، لكن الآلة المتوفرة هي سكيد ستير لودر CAT 226B.' ),
						),
					),
					array(
						'@type'          => 'Question',
						'name'           => dbr_text( 'Can I rent the machine with an operator?', 'هل يمكن استئجار الآلة مع مشغل؟' ),
						'acceptedAnswer' => array(
							'@type' => 'Answer',
							'text'  => dbr_text( 'The bobcat is provided with an operator. Availability and delivery cost depend on date, job location and work type.', 'يتم توفير البوبكات مع مشغل. التوفر وتكلفة التوصيل يعتمدان على التاريخ وموقع العمل ونوع الخدمة.' ),
						),
					),
				),
			),
		),
	);

	echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
}
add_action( 'wp_head', 'dbr_schema' );

function dbr_get_breadcrumb_items() {
	$items = array(
		array(
			'name' => __( 'Home', 'dubai-bobcat-rental' ),
			'url'  => dbr_home_url( '/' ),
		),
	);

	if ( is_home() ) {
		$posts_page_id = (int) get_option( 'page_for_posts' );
		$items[]       = array(
			'name' => $posts_page_id ? get_the_title( $posts_page_id ) : __( 'Blog', 'dubai-bobcat-rental' ),
			'url'  => $posts_page_id ? get_permalink( function_exists( 'pll_get_post' ) ? ( pll_get_post( $posts_page_id, dbr_current_lang() ) ?: $posts_page_id ) : $posts_page_id ) : dbr_page_url( 'blog', '/blog/' ),
		);
		return $items;
	}

	if ( is_singular( 'post' ) ) {
		$posts_page_id = (int) get_option( 'page_for_posts' );
		$items[]       = array(
			'name' => $posts_page_id ? get_the_title( $posts_page_id ) : __( 'Blog', 'dubai-bobcat-rental' ),
			'url'  => $posts_page_id ? get_permalink( function_exists( 'pll_get_post' ) ? ( pll_get_post( $posts_page_id, dbr_current_lang() ) ?: $posts_page_id ) : $posts_page_id ) : dbr_page_url( 'blog', '/blog/' ),
		);
		$items[]       = array(
			'name' => get_the_title(),
			'url'  => get_permalink(),
		);
		return $items;
	}

	if ( is_page() && ! is_front_page() ) {
		$ancestors = array_reverse( get_post_ancestors( get_the_ID() ) );
		foreach ( $ancestors as $ancestor_id ) {
			$items[] = array(
				'name' => get_the_title( $ancestor_id ),
				'url'  => get_permalink( $ancestor_id ),
			);
		}
		$items[] = array(
			'name' => get_the_title(),
			'url'  => get_permalink(),
		);
	}

	return $items;
}

function dbr_breadcrumbs() {
	if ( is_front_page() ) {
		return;
	}

	$items = dbr_get_breadcrumb_items();
	if ( count( $items ) < 2 ) {
		return;
	}
	?>
	<nav class="breadcrumbs" aria-label="<?php esc_attr_e( 'Breadcrumb', 'dubai-bobcat-rental' ); ?>">
		<?php foreach ( $items as $index => $item ) : ?>
			<?php if ( $index > 0 ) : ?>
				<span aria-hidden="true">/</span>
			<?php endif; ?>
			<?php if ( $index + 1 === count( $items ) ) : ?>
				<strong><?php echo esc_html( $item['name'] ); ?></strong>
			<?php else : ?>
				<a href="<?php echo esc_url( $item['url'] ); ?>"><?php echo esc_html( $item['name'] ); ?></a>
			<?php endif; ?>
		<?php endforeach; ?>
	</nav>
	<?php
}

function dbr_breadcrumb_schema() {
	if ( is_front_page() ) {
		return;
	}

	$items = dbr_get_breadcrumb_items();
	if ( count( $items ) < 2 ) {
		return;
	}

	$list = array();
	foreach ( $items as $index => $item ) {
		$list[] = array(
			'@type'    => 'ListItem',
			'position' => $index + 1,
			'name'     => $item['name'],
			'item'     => $item['url'],
		);
	}

	$schema = array(
		'@context'        => 'https://schema.org',
		'@type'           => 'BreadcrumbList',
		'itemListElement' => $list,
	);

	echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
}
add_action( 'wp_head', 'dbr_breadcrumb_schema' );

function dbr_favicon() {
	echo '<link rel="icon" href="' . esc_url( get_template_directory_uri() . '/assets/favicon.svg' ) . '" type="image/svg+xml">' . "\n";
}
add_action( 'wp_head', 'dbr_favicon' );

function dbr_robots_txt( $output, $public ) {
	if ( ! $public ) {
		return $output;
	}

	$output  = "User-agent: *\n";
	$output .= "Allow: /\n\n";
	$output .= 'Sitemap: ' . home_url( '/sitemap_index.xml' ) . "\n";

	return $output;
}
add_filter( 'robots_txt', 'dbr_robots_txt', 10, 2 );
