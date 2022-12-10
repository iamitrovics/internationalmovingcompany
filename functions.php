<?php
/**
 * Theme functions and definitions
 *
 * @package HelloElementorChild
 */

/**
 * Load child theme css and optional scripts
 *
 * @return void
 */
function hello_elementor_child_enqueue_scripts() {
	wp_enqueue_style(
		'hello-elementor-child-style',
		get_stylesheet_directory_uri() . '/style.css',
		[
			'hello-elementor-theme-style',
		],
		'1.0.0'
	);
}
add_action( 'wp_enqueue_scripts', 'hello_elementor_child_enqueue_scripts', 20 );

add_filter( 'wpcf7_autop_or_not', '__return_false' );

function cf7_post_to_third_party($form)
{
    $formMappings = array(
        'first_name' => array('your-first'),
		'last_name' => array('your-last'),
		'email' => array('your-email'),
		'phone' => array('your-tel'),
		'move_date' => array('your-date'),
		'move_size' => array('home-size'),
		'from_zip' => array('zip-from'),
		'to_zip' => array('zip-to'),
		'car_trailer' => array('your-trailer'),
		'car_make' => array('car-make'),
		'car_model' => array('car-model'),
		'car_year' => array('car-year'),
        'source_details[url]' => array('dynamichidden-672'),
        'source_details[title]' => array('dynamichidden-673'),
		'move_type' => array('dynamichidden-100')
    );
    $handler = new MovingSoftFormHandler($formMappings);
    $handler->setOrigin('https://www.internationalmovingcompany.com')->handleCF7($form, [401]);
}
add_action('wpcf7_mail_sent', 'cf7_post_to_third_party', 10, 1);


function skip_mail_when_testing($f){
    $submission = WPCF7_Submission::get_instance();
    $handler = new MovingSoftFormHandler();
    
    return $handler->getIP() == '206.189.212.83'; //testing Bot IP address
}
add_filter('wpcf7_skip_mail','skip_mail_when_testing');


function fb_filter_query( $query, $error = true ) {

    if ( is_search() ) {
        $query->is_search = false;
        $query->query_vars['s'] = false;
        $query->query['s'] = false;

        if ( $error == true )
            $query->is_404 = true;
    }
}

add_action( 'parse_query', 'fb_filter_query' );
add_filter( 'get_search_form', function() { return null;} );

if (current_user_can('manage_options')) {
	function lwp_2629_user_edit_ob_start() {ob_start();}
	add_action( 'personal_options', 'lwp_2629_user_edit_ob_start' );
	function lwp_2629_insert_nicename_input( $user ) {
		$content = ob_get_clean();
		$regex = '/<tr(.*)class="(.*)\buser-user-login-wrap\b(.*)"(.*)>([\s\S]*?)<\/tr>/';
		$nicename_row = sprintf(
			'<tr class="user-user-nicename-wrap"><th><label for="user_nicename">%1$s</label></th><td><input type="text" name="user_nicename" id="user_nicename" value="%2$s" class="regular-text" />' . "\n" . '<span class="description">%3$s</span></td></tr>',
			esc_html__( 'Nicename' ),
			esc_attr( $user->user_nicename ),
			esc_html__( 'Must be unique.' )
		);
		echo preg_replace( $regex, '\0' . $nicename_row, $content );
	}
	add_action( 'show_user_profile', 'lwp_2629_insert_nicename_input' );
	add_action( 'edit_user_profile', 'lwp_2629_insert_nicename_input' );
	function lwp_2629_profile_update( $errors, $update, $user ) {
		if ( !$update ) return;
		if ( empty( $_POST['user_nicename'] ) ) {
			$errors->add(
				'empty_nicename',
				sprintf(
					'<strong>%1$s</strong>: %2$s',
					esc_html__( 'Error' ),
					esc_html__( 'Please enter a Nicename.' )
				),
				array( 'form-field' => 'user_nicename' )
			);
		} else {
			$user->user_nicename = $_POST['user_nicename'];
		}
	}
	add_action( 'user_profile_update_errors', 'lwp_2629_profile_update', 10, 3 );
	}

	
// fix canonical paginate
function remake_wpseo_canonical($canonical) {
	global $post;

	$paged = get_query_var('paged');

	if (isset($paged) && (int) $paged > 0){
		return trailingslashit(trailingslashit($canonical) . 'page/' . $paged);

		return $url;
	}

	return $canonical;
}

add_filter('wpseo_opengraph_url', 'remake_wpseo_canonical');


// Move Yoast to bottom
function yoasttobottom() {
	return 'low';
}

function my_custom_function_name() {
	$app_id = 966242223397117;
	$tag = '<meta property="fb:app_id" content="966242223397117" />';
	echo sprintf($tag, $num);
  }
  add_action( 'wp_head', 'my_custom_function_name' );