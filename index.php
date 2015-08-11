<?php
/*
Plugin Name: Cyberchimp Add Ons
Description: Added functionality for the Cyberchimp Free themes
Version: 1
Author: CyberChimps
Author URI: http://www.cyberchimps.com
License: GPL2
*/

// Load default core settings
require_once(dirname( __FILE__ ) . '/options-core.php' );

// Load theme upsell.
require_once( dirname( __FILE__ ) . '/theme-upsell.php' );

/* If the user can't edit theme options, no use running this plugin */
add_action( 'init', 'cyberchimps_edit_themes_role_check_free' );
function cyberchimps_edit_themes_role_check_free() {
    	if( current_user_can( 'edit_theme_options' )) {
		// If the user can edit theme options, let the fun begin!
		add_action( 'admin_init', 'cyberchimps_admin_init' );
		add_action( 'admin_menu', 'cyberchimps_admin_add_page' );
		add_action( 'wp_before_admin_bar_render', 'cyberchimps_admin_bar' );
	}
}

// create the admin menu for the theme options page
function cyberchimps_admin_add_page() {
    $cyberchimps_page = add_theme_page(
            __( 'Theme Options Page', 'cyberchimps_core' ), __( 'Theme Options', 'cyberchimps_core' ), 'edit_theme_options', 'cyberchimps-theme-options', 'cyberchimps_options_page'
    );

    add_action( "admin_print_styles-$cyberchimps_page", 'cyberchimps_load_styles' );
    add_action( "admin_print_scripts-$cyberchimps_page", 'cyberchimps_load_scripts' );
}

function cyberchimps_load_styles() {

    // Set template directory uri
    $directory_uri = plugin_dir_url( __FILE__ );

    wp_enqueue_style( 'bootstrap', $directory_uri . 'bootstrap/css/bootstrap.css' );
    wp_enqueue_style( 'bootstrap-responsive', $directory_uri . 'bootstrap/css/bootstrap-responsive.css', 'bootstrap' );
    wp_enqueue_style( 'cyberchimps-responsive', $directory_uri . 'bootstrap/css/cyberchimps-responsive.css', array( 'bootstrap', 'bootstrap-responsive' ) );
    wp_enqueue_style( 'plugin_option_styles', $directory_uri . 'css/options-style.css', array( 'bootstrap', 'bootstrap-responsive' ) );

    wp_enqueue_style( 'color-picker', $directory_uri . 'css/colorpicker.css' );
    wp_enqueue_style( 'thickbox' );
}

function cyberchimps_load_scripts() {

    // Set template directory uri
    $directory_uri = plugin_dir_url( __FILE__ );

    wp_enqueue_media();
    wp_enqueue_script( 'theme-option-media-uploader-3.6', get_template_directory_uri() . '/cyberchimps/options/lib/js/media-uploader-new.min.js', array( 'jquery' ) );
    //wp_enqueue_script( 'theme-option-media-uploader-3.5', plugins_url( 'cyberchimp-add-on/js/media-uploader-new.min.js' , dirname(__FILE__) ) , array( 'jquery' ) );
    
    // Enqueued scripts
    wp_enqueue_script( 'jquery-ui-core' );
    wp_enqueue_script( 'jquery-ui-sortable' );

    // Adding JS to support drag and drop in theme options
    wp_enqueue_script( 'jquery-touch-punch-min', $directory_uri . 'js/touch-punch.min.js', array( 'jquery' ) );
    wp_enqueue_script( 'jquery-touch-sense', $directory_uri . 'js/touch-sensitive.min.js', array( 'jquery' ) );

    wp_enqueue_script( 'thickbox' );
    wp_enqueue_script( 'color-picker', $directory_uri . 'js/colorpicker.min.js', array( 'jquery' ), '', true );
    wp_enqueue_script( 'media-uploader', $directory_uri . 'js/options-medialibrary-uploader.min.js', array( 'jquery' ), '', true );
    wp_enqueue_script( 'options-custom', $directory_uri . 'js/options-custom.min.js', array( 'jquery' ), '', true );
    wp_enqueue_script( 'bootstrap-js', $directory_uri . 'bootstrap/js/bootstrap.min.js', array( 'jquery' ), '', true );
    wp_enqueue_script( 'google-fonts', $directory_uri . 'js/font_inline_plugin.min.js', array( 'jquery' ), '', true );
}

// add core and theme settings to options page
add_action( 'admin_init', 'cyberchimps_admin_init' );

function cyberchimps_admin_init() {

    // Load options media uploader
    require_once dirname( __FILE__ ) . '/options-medialibrary-uploader.php';

    // register theme options settings
    register_setting( 'cyberchimps_options', 'cyberchimps_options', 'cyberchimps_options_validate' );

    // add all core settings
    // Create sections
    $sections_list = cyberchimps_get_sections();
    cyberchimps_create_sections( $sections_list );

    // Create fields
    $fields_list = cyberchimps_get_fields();
    cyberchimps_create_fields( $fields_list );
}

// create and display theme options page
function cyberchimps_options_page() {
    ?>

    <div class="wrap">
        <?php do_action( 'cyberchimps_options_before_container' ); ?>
        <div class="container-fluid cc-options">

            <form action="options.php" method="post" id="cyberchimps_options_page">
                <?php
                settings_fields( 'cyberchimps_options' );
                $headings_list = cyberchimps_get_headings();
                $sections_list = cyberchimps_get_sections();

                do_action( 'cyberchimps_options_form_start' )
                ?>
                <!-- header -->
                <div class="row-fluid cc-header">
                    <div class="span4">
                        <div class="cc-title">
                            <div class="icon32" id="icon-tools"><br/></div>
                            <h2><?php printf( '%1s ' . __( 'Options', 'cyberchimps_core' ), apply_filters( 'cyberchimps_current_theme_name', 'CyberChimps' ) ); ?></h2>
                        </div>
                        <!-- cc-title -->
                    </div>
                    <!-- span4 -->
                    <div class="span8">
                        <ul class="cc-header-links">
                            <li><a href="<?php echo apply_filters( 'cyberchimps_support_forum', 'http://cyberchimps.com/forum/free/' ); ?>"
                                   target="_blank"><?php _e( 'Support', 'cyberchimps_core' ); ?></a></li>
                            <li><a href="<?php echo apply_filters( 'cyberchimps_documentation', 'http://cyberchimps.com/guides/' ); ?>"
                                   target="_blank"><?php _e( 'Instructions', 'cyberchimps_core' ); ?></a></li>
                            <li><a href="<?php echo apply_filters( 'cyberchimps_options_buy_link', 'http://cyberchimps.com/store/' ); ?>"
                                   target="_blank"><?php _e( 'Buy Themes', 'cyberchimps_core' ); ?></a></li>
                                <?php if ( has_filter( 'cyberchimps_upgrade_pro_title', 'cyberchimps_upgrade_bar_pro_title' ) ): ?>
                                <li><a href="<?php echo apply_filters( 'cyberchimps_upgrade_link', '' ); ?>" target="_blank"><?php echo apply_filters( 'cyberchimps_upgrade_pro_title', '' ); ?></a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                    <!-- span8 -->
                </div>
                <!-- row-fluid -->
                <!-- end header -->

                <!-- start sub menu -->
                <div class="row-fluid">
                    <div class="span12">
                        <div class="cc-submenu">
                            <div class="cc-collapse">
                                <!-- mobile menu button -->
                                <div class="cc-mobile-menu">
                                    <a class="btn" data-toggle="modal" href="#cc-mobile-modal">
                                        <i class="icon-th-list"></i>
                                    </a>
                                </div>
                                <!-- cc-mobil-menu -->

                                <div class="cc-social-container-subheader hidden-phone">
                                    <div class="cc-social twitter">
                                        <a href="https://twitter.com/cyberchimps" class="twitter-follow-button" data-show-count="false" data-size="small">Follow @cyberchimps</a>
                                        <script>!function( d, s, id ) {
                                                var js, fjs = d.getElementsByTagName( s )[0];
                                                if ( !d.getElementById( id ) ) {
                                                    js = d.createElement( s );
                                                    js.id = id;
                                                    js.src = "//platform.twitter.com/widgets.js";
                                                    fjs.parentNode.insertBefore( js, fjs );
                                                }
                                            }( document, "script", "twitter-wjs" );</script>
                                    </div>
                                    <!-- cc-scoial -->
                                    <div class="cc-social facebook">
                                        <iframe
                                            src="//www.facebook.com/plugins/like.php?href=http%3A%2F%2Fcyberchimps.com%2F&amp;send=false&amp;layout=button_count&amp;width=200&amp;show_faces=false&amp;action=like&amp;colorscheme=light&amp;font&amp;height=21"
                                            scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:200px; height:21px;" allowTransparency="true"></iframe>
                                    </div>
                                    <!-- cc-scoial -->
                                </div>
                                <!-- cc-social-container -->
                            </div>
                            <!-- cc-collapse -->


                            <div class="cc-submenu-links">
                                <input type="submit" id="cyberchimps_options_submit" class="btn btn-primary" name="update" value="<?php esc_attr_e( 'Save Options', 'cyberchimps_core' ); ?>"/>
                                <button class="reset-button btn" name="reset" onclick="return confirm( '<?php print esc_js( __( 'Click OK to reset. Any theme settings will be lost!', 'cyberchimps_core' ) );
                            ?>' );">
                                        <?php esc_attr_e( 'Restore Defaults', 'cyberchimps_core' ); ?>
                                </button>
                            </div>
                            <!-- cc-submenu-links -->
                            <div class="clear"></div>
                        </div>
                        <!-- cc-submenu -->

                        <!-- hidden mobile menu -->
                        <div class="modal hide" id="cc-mobile-modal">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&#215;</button>
                                <div class="cc-mobile-title"></div>
                                <h3>Navigation</h3>
                            </div>
                            <div class="modal-body">
                                <ul class="cc-parent nav-tab-wrapper">
                                    <?php
                                    foreach ( $headings_list as $heading ) {

                                        $jquery_click_hook = preg_replace( '/[^a-zA-Z0-9._\-]/', '', strtolower( $heading['id'] ) );

                                        echo '<li class="cc-has-children">';
                                        echo '<a id="' . esc_attr( $jquery_click_hook ) . '-tab" title="' . esc_attr( $heading['title'] ) . '" href="' . esc_attr( '#' . $jquery_click_hook ) . '">' . esc_html( $heading['title'] ) . '</a>';
                                        echo '</li>';
                                    }
                                    ?>
                                </ul>
                            </div>
                            <div class="modal-footer">
                                <a href="#" class="btn" data-dismiss="modal">Close</a>
                            </div>
                        </div>
                        <!-- end mobile menu -->

                    </div>
                    <!-- span12 -->
                </div>
                <!-- row fluid -->
                <!-- end sub menu -->

                <!-- start left menu -->
                <div class="row-fluid cc-content">
                    <div class="span3">
                        <div class="cc-left-menu">
                            <ul class="cc-parent nav-tab-wrapper">
                                <?php
                                foreach ( $headings_list as $heading ) {

                                    $jquery_click_hook = preg_replace( '/[^a-zA-Z0-9._\-]/', '', strtolower( $heading['id'] ) );

                                    echo '<li class="cc-has-children">';
                                    echo '<a id="' . esc_attr( $jquery_click_hook ) . '-tab" title="' . esc_attr( $heading['title'] ) . '" href="' . esc_attr( '#' . $jquery_click_hook ) . '">' . esc_html(
                                            $heading['title'] ) . '<span class="glyphicon glyphicon-chevron-down"></span></a><div class="cc-menu-arrow"><div></div></div>';

                                    echo '<ul class="cc-child">';
                                    foreach ( $sections_list as $section ) {
                                        if ( in_array( $heading['id'], $section ) ) {
                                            $jquery_click_section_hook = '';
                                            $jquery_click_section_hook = preg_replace( '/[^a-zA-Z0-9._\-]/', '', strtolower( $section['id'] ) );

                                            echo '<li><a id="' . esc_attr( $jquery_click_section_hook ) . '-tab" title="' . esc_attr( $section['label'] ) . '" href="' . esc_attr( '#' . $jquery_click_section_hook ) . '">' . esc_html( $section['label'] ) . '</a></li>';
                                        }
                                    }
                                    echo '</ul>';
                                    echo '</li>';
                                }
                                ?>
                                <li id="left-menu-save">
                                    <input type="submit" id="cyberchimps_options_submit" class="btn btn-primary" name="update" value="<?php esc_attr_e( 'Save Options', 'cyberchimps_core' ); ?>"/>
                                </li>
                            </ul>
                        </div>
                        <!-- cc-left-menu -->
                    </div>
                    <!-- span3 -->
                    <!-- end left menu -->

                    <!-- start main content -->
                    <div class="span9">
                        <div class="cc-main-content">
                            <?php
                            foreach ( $headings_list as $heading ) {

                                $jquery_click_hook = preg_replace( '/[^a-zA-Z0-9._\-]/', '', strtolower( $heading['id'] ) );

                                echo '<div class="group cc-content-section" id="' . esc_attr( $jquery_click_hook ) . '">';
                                echo '<h2>' . esc_html( $heading['title'] ) . '</h2>';
                                if ( isset( $heading['description'] ) ) {
                                    echo '<p>' . esc_html( $heading['description'] ) . '</p>';
                                }
                                cyberchimps_do_settings_sections( $heading['id'] );
                                echo '</div>';
                            }
                            ?>
                        </div>
                        <!-- cc-main-content -->
                    </div>
                    <!-- span9 -->
                </div>
                <!-- row fluid -->
                <!-- end main content -->

                <!-- start footer -->
                <div class="row-fluid">
                    <div class="cc-footer">
                        <div class="span3">
                            <div class="cc-logo">
                                <a href="http://cyberchimps.com" title="<?php esc_attr_e( 'CyberChimps WordPress Themes', 'cyberchimps_core' ); ?>"><img
                                        src="<?php echo plugin_dir_url( __FILE__ ); ?>/images/options/chimp.png"
                                        alt="<?php esc_attr_e( 'CyberChimps WordPress Themes', 'cyberchimps_core' ); ?>"/><span>CyberChimps</span></a>
                            </div>
                            <!-- cc-logo -->
                        </div>
                        <!-- span3 -->
                        <div class="span9">
                            <div class="cc-social-container">
                                <div class="cc-social twitter">
                                    <a href="https://twitter.com/cyberchimps" class="twitter-follow-button" data-show-count="false" data-size="small">Follow @cyberchimps</a>
                                    <script>!function( d, s, id ) {
                                            var js, fjs = d.getElementsByTagName( s )[0];
                                            if ( !d.getElementById( id ) ) {
                                                js = d.createElement( s );
                                                js.id = id;
                                                js.src = "//platform.twitter.com/widgets.js";
                                                fjs.parentNode.insertBefore( js, fjs );
                                            }
                                        }( document, "script", "twitter-wjs" );</script>
                                </div>
                                <!-- cc-scoial -->
                                <div class="cc-social facebook">
                                    <iframe
                                        src="//www.facebook.com/plugins/like.php?href=http%3A%2F%2Fcyberchimps.com%2F&amp;send=false&amp;layout=button_count&amp;width=200&amp;show_faces=false&amp;action=like&amp;colorscheme=light&amp;font&amp;height=21"
                                        scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:200px; height:21px;" allowTransparency="true"></iframe>
                                </div>
                                <!-- cc-scoial -->
                            </div>
                            <!-- cc-social-container -->
                            <div class="footer-links">
                                <input type="submit" id="cyberchimps_options_submit" class="btn btn-primary" name="update" value="<?php esc_attr_e( 'Save Options', 'cyberchimps_core' ); ?>"/>
                                <button class="reset-button btn" name="reset" onclick="return confirm( '<?php print esc_js( __( 'Click OK to reset. Any theme settings will be lost!', 'cyberchimps_core' ) );
                        ?>' );">
                                        <?php esc_attr_e( 'Restore Defaults', 'cyberchimps_core' ); ?>
                                </button>
                            </div>
                            <!-- footer-links -->
                        </div>
                        <!-- span 9 -->
                        <div class="clear"></div>
                    </div>
                    <!-- cc-footer -->
                </div>
                <!-- row fluid -->
                <!-- end footer -->

            </form>

        </div>
        <!-- container-fluid -->
    </div><!-- wrap -->
    <?php
}

/**
 * forked version of core function do_settings_sections()
 * modified core code call cyberchimps_do_settings_fields() and apply markup for section title and description
 * returns mixed data
 */
function cyberchimps_do_settings_sections( $page ) {
    global $wp_settings_sections, $wp_settings_fields;

    if ( !isset( $wp_settings_sections ) || !isset( $wp_settings_sections[$page] ) ) {
        return;
    }

    foreach ( (array) $wp_settings_sections[$page] as $section ) {
        $jquery_click_section_hook = '';
        $jquery_click_section_hook = preg_replace( '/[^a-zA-Z0-9._\-]/', '', strtolower( $section['id'] ) );

        echo '<div class="section-group" id="' . esc_attr( $jquery_click_section_hook ) . '">';
        if ( $section['title'] ) {
            echo "<h3><span class='glyphicon glyphicon-chevron-down'></span> {$section['title']}</h3>\n";
        }

        // wrapper div of all field-container divs
        echo '<div class="field-container-wrapper">';

        //Hook before section options
        do_action( $jquery_click_section_hook . '_before' );

        call_user_func( $section['callback'], $section );

        if ( isset( $wp_settings_fields ) && isset( $wp_settings_fields[$page] ) && isset( $wp_settings_fields[$page][$section['id']] ) ) {
            cyberchimps_do_settings_fields( $page, $section['id'] );
        }

        //Hook after section options
        do_action( $jquery_click_section_hook . '_after' );

        echo '</div>'; // .field-container ends
        echo '<div class="clear"></div></div>';
    }
}

/**
 * forked version of core function do_settings_fields()
 * modified core code to remove table cell markup and apply custom markup
 * returns mixed data
 */
function cyberchimps_do_settings_fields( $page, $section ) {
    global $wp_settings_fields;

    if ( !isset( $wp_settings_fields ) || !isset( $wp_settings_fields[$page] ) || !isset( $wp_settings_fields[$page][$section] ) ) {
        return;
    }

    foreach ( (array) $wp_settings_fields[$page][$section] as $field ) {
        call_user_func( $field['callback'], $field['args'] );
    }
}

function cyberchimps_get_headings() {
    $headings_list = array();

    // pull in both default sections and users custom sections
    return apply_filters( 'cyberchimps_heading_list', $headings_list );
}

function cyberchimps_get_sections() {
    $sections_list = array();

    // pull in both default sections and users custom sections
    return apply_filters( 'cyberchimps_section_list', $sections_list );
}

function cyberchimps_get_fields() {
    $fields_list = array();

    // pull in both default fields and users custom fields
    return apply_filters( 'cyberchimps_field_list', $fields_list );
}

function cyberchimps_create_sections( $sections ) {
    if ( empty( $sections ) ) {
        return false;
    }

    // add in error checking and proper validation, escaping, and translation calls
    foreach ( $sections as $section ) {
        if ( cyberchimps_section_exists( $section['heading'], $section['id'] ) ) {
            continue;
        } else {
            add_settings_section(
                    $section['id'], $section['label'], 'cyberchimps_sections_callback', $section['heading']
            );
        }
    }
}

function cyberchimps_drag_drop_field( $value ) {

    // Set directory uri
    $directory_uri = get_template_directory_uri();

    $option_name = 'cyberchimps_options';
    $settings = get_option( $option_name );

    $val = '';
    $output = '';

    // If the option is already saved, ovveride $val
    if ( ( $value['type'] != 'heading' ) && ( $value['type'] != 'info' ) ) {
        if ( isset( $settings[( $value['id'] )] ) ) {

            // Assign empty array if the array returns null
            if ( $settings[( $value['id'] )] != "" ) {
                $val = $settings[( $value['id'] )];
            } else {
                $val = null;
            }
            // Striping slashes of non-array options
            if ( !is_array( $val ) ) {
                $val = stripslashes( $val );
            }
        }
    }
    // Set default value to $val
    if ( empty( $val ) ) {
        if ( isset( $value['std'] ) ) {
            if ( is_array( $value['std'] ) ) {
                $val = array_keys( $value['std'] );
            } else {
                $val = array_keys( explode( $value['std'] ) );
            }
        }
    }

    $output .= "<div class='section_order' id=" . esc_attr( $value['id'] ) . ">";
    $output .= "<div class='left_list span6'>";
    $output .= "<div class='inactive'>Inactive Elements</div>";
    $output .= "<div class='list_items'>";
    if ( is_array( $val ) ) {
        foreach ( $value['options'] as $key => $option ) {
            if ( in_array( $key, $val ) ) {
                continue;
            }
            $output .= "<div class='list_item'>";
            $output .= '<span class="glyphicon glyphicon-minus-sign action"></span>';
            $output .= "<span data-key='{$key}'>{$option}</span>";
            $output .= "</div>";
        }
    }
    $output .= "</div>";
    $output .= "</div>";
    $output .= '<div class="options-arrow span1 hidden-phone"><span class="glyphicon glyphicon-arrow-right"></span></div>';
    $output .= "<div class='right_list span5'>";
    $output .= "<div class='active'>Active Elements</div>";
    $output .= "<div class='drag'>Drag & Drop Elements</div>";
    $output .= "<div class='list_items'>";
    if ( is_array( $val ) ) {
        foreach ( $val as $key ) {
            if ( !array_key_exists( $key, $value['options'] ) ) {
                continue;
            }
            $output .= "<div class='list_item'>";
            $output .= '<span class="glyphicon glyphicon-minus-sign action"></span>';
            $output .= "<span data-key='{$key}'>{$value['options'][$key]}</span>";
            $output .= "</div>";
        }
    }
    $output .= "</div>";
    $output .= "<input class='blog-section-order-tracker' type='hidden'  name='cyberchimps_options[blog_section_order_tracker]' />";
    $output .= "</div>";
    $output .= '<div id="values" data-key="' . $option_name . '"></div>';
    $output .= '<div class="clear"></div>';
    $output .= "</div>";

    echo $output;
}

function cyberchimps_sections_callback( $section_passed ) {
    $sections = cyberchimps_get_sections();

    if ( empty( $sections ) && empty( $section_passed ) ) {
        return false;
    }

    foreach ( $sections as $section ) {
        if ( $section_passed['id'] == $section['id'] ) {
            echo '<p>';
            if ( isset( $section['description'] ) ) {
                echo $section['description'];
            }
            echo '</p>';
        }
    }
}

/**
 * custom function that checks if the section has been run through add_settings_section() function
 * returns bool value true if section exists and false if it does not
 */
function cyberchimps_section_exists( $heading, $section ) {
    global $wp_settings_sections;

    if ( isset( $wp_settings_sections[$heading][$section] ) ) {
        return true;
    }

    return false;
}

function cyberchimps_create_fields( $fields ) {
    if ( empty( $fields ) ) {
        return false;
    }

    // loop through and create each field
    foreach ( $fields as $field_args ) {
        $field_defaults = array(
            'id' => false,
            'name' => __( 'Default Field', 'cyberchimps_core' ),
            'callback' => 'cyberchimps_fields_callback',
            'section' => 'cyberchimps_default_section',
            'heading' => 'cyberchimps_default_heading',
        );
        $field_args = wp_parse_args( $field_args, $field_defaults );

        if ( empty( $field_args['id'] ) ) {
            continue;
        } elseif ( !cyberchimps_section_exists( $field_args['heading'], $field_args['section'] ) ) {
            continue;
        } else {
            add_settings_field(
                    $field_args['id'], $field_args['name'], $field_args['callback'], $field_args['heading'], $field_args['section'], $field_args
            );
        }
    }
}

function cyberchimps_fields_callback( $value ) {
    global $allowedtags;

    $option_name = 'cyberchimps_options';
    $settings = get_option( $option_name );

    $val = '';
    $select_value = '';
    $checked = '';
    $output = '';

    // Set default value to $val
    if ( isset( $value['std'] ) ) {
        $val = $value['std'];
    }

    // If the option is already saved, ovveride $val
    if ( ( $value['type'] != 'heading' ) && ( $value['type'] != 'info' ) ) {
        if ( isset( $settings[( $value['id'] )] ) ) {
            $val = $settings[( $value['id'] )];
            // Striping slashes of non-array options
            if ( !is_array( $val ) ) {
                $val = stripslashes( $val );
            }
        }
    }
    // If there is no class set make it blank
    if ( !isset( $value['class'] ) ) {
        $value['class'] = '';
    }

    // If there is a description save it for labels
    $explain_value = '';
    if ( isset( $value['desc'] ) ) {
        $explain_value = $value['desc'];
    }

    // add extra field wrapper class
    $wrapper_class = ( $value['class'] ) ? ' ' . $value['class'] . '-container' : '';

    // field wrapper
    $output .= '<div class="field-container' . $wrapper_class . '">';

    // Output field name
    if ( $value['name'] && $value['type'] != 'info' && $value['type'] != 'welcome' && $value['type'] != 'toggle' ) {
        $output .= '<label for="' . esc_attr( $value['id'] ) . '">' . $value['name'] . '</label>';
    }

    switch ( $value['type'] ) {

        // Basic text input
        case 'text':
            $output .= '<input id="' . esc_attr( $value['id'] ) . '" class="of-input ' . esc_attr( $value['class'] ) . '" name="' . esc_attr( $option_name . '[' . $value['id'] . ']' ) . '" type="text" value="' . esc_attr( $val ) . '" />';
            break;

        // Same as text but allows some basic a, href, title, br, em and strong html, check the sanitization
        case 'text_html':
            $output .= '<input id="' . esc_attr( $value['id'] ) . '" class="of-input ' . esc_attr( $value['class'] ) . '" name="' . esc_attr( $option_name . '[' . $value['id'] . ']' ) . '" type="text" value="' . esc_attr( $val ) . '" />';
            break;

        // Textarea
        case 'textarea':
            $rows = '8';

            if ( isset( $value['settings']['rows'] ) ) {
                $custom_rows = $value['settings']['rows'];
                if ( is_numeric( $custom_rows ) ) {
                    $rows = $custom_rows;
                }
            }

            $val = stripslashes( $val );
            $output .= '<textarea id="' . esc_attr( $value['id'] ) . '" class="of-input" name="' . esc_attr( $option_name . '[' . $value['id'] . ']' ) . '" rows="' . $rows . '">' . esc_textarea( $val ) . '</textarea>';
            break;

        // Unfiltered Textarea
        case 'unfiltered_textarea':
            $rows = '8';

            if ( isset( $value['settings']['rows'] ) ) {
                $custom_rows = $value['settings']['rows'];
                if ( is_numeric( $custom_rows ) ) {
                    $rows = $custom_rows;
                }
            }

            $val = stripslashes( $val );
            $output .= '<textarea id="' . esc_attr( $value['id'] ) . '" class="of-input ' . esc_attr( $value['class'] ) . '" name="' . esc_attr( $option_name . '[' . $value['id'] . ']' ) . '" rows="'
                    .
                    $rows .
                    '">' .
                    esc_textarea(
                            $val ) . '</textarea>';
            break;

        // css Textarea
        case 'csstextarea':
            $rows = '8';

            if ( isset( $value['settings']['rows'] ) ) {
                $custom_rows = $value['settings']['rows'];
                if ( is_numeric( $custom_rows ) ) {
                    $rows = $custom_rows;
                }
            }

            $val = stripslashes( $val );
            $output .= '<textarea id="' . esc_attr( $value['id'] ) . '" class="of-input" name="' . esc_attr( $option_name . '[' . $value['id'] . ']' ) . '" rows="' . $rows . '">' . strip_tags( $val ) . '</textarea>';
            $output .= '<div id="custom-css-msg"></div>';
            break;

        // Select Box
        case 'select':
            $output .= '<select class="of-input ' . esc_attr( $value['class'] ) . '" name="' . esc_attr( $option_name . '[' . $value['id'] . ']' ) . '" id="' . esc_attr( $value['id'] ) . '">';

            foreach ( $value['options'] as $key => $option ) {
                $selected = '';
                if ( $val != '' ) {
                    $selected = selected( $val, $key, false );
                }
                $output .= '<option value="' . esc_attr( $key ) . '" ' . $selected . '>' . esc_html( $option ) . '</option>';
            }
            $output .= '</select>';
            break;

        // Radio Box
        case "radio":
            $name = $option_name . '[' . $value['id'] . ']';
            $val = ( $val != '' ) ? $val : $value['std'];
            foreach ( $value['options'] as $key => $option ) {
                $id = $option_name . '-' . $value['id'] . '-' . $key;
                $output .= '<div class="radio-container ' . esc_attr( $value['class'] ) . '"><input class="of-input of-radio" type="radio" name="' . esc_attr( $name ) . '" id="' . esc_attr( $id ) . '" value="' . esc_attr( $key ) . '" ' . checked( $val, $key, false ) . ' /><label for="' . esc_attr( $id ) . '" class="of-radio">' . esc_html( $option ) . '</label></div>';
            }
            break;

        // Image Selectors
        case "images":
            $name = $option_name . '[' . $value['id'] . ']';
            $output .= '<div class="images-radio-container">';
            foreach ( $value['options'] as $key => $option ) {
                $selected = '';
                $checked = '';
                if ( $val != '' ) {
                    if ( $val == $key ) {
                        $selected = ' of-radio-img-selected';
                        $checked = ' checked="checked"';
                    }
                }
                $output .= '<div class="images-radio-subcontainer"><input type="radio" id="' . esc_attr( $value['id'] . '_' . $key ) . '" class="of-radio-img-radio" value="' . esc_attr( $key ) . '" name="' . esc_attr( $name ) . '" ' . $checked . ' />';
                $output .= '<div class="of-radio-img-label">' . esc_html( $key ) . '</div>';
                $output .= '<img src="' . esc_url( $option ) . '" alt="' . $option . '" class="of-radio-img-img' . $selected . '" onclick="document.getElementById(\'' . esc_attr( $value['id'] . '_' . $key ) . '\').checked=true;" /></div>';
            }
            $output .= '</div>';
            break;

        // Checkbox
        case "checkbox":
            $output .= '<div class="checkbox-container"><input id="' . esc_attr( $value['id'] ) . '" class="checkbox of-input" type="checkbox" name="' . esc_attr( $option_name . '[' . $value['id'] . ']' ) . '" ' . checked( $val, 1, false ) . ' />';
            $output .= '<label class="right-label" for="' . esc_attr( $value['id'] ) . '">' . wp_kses( $explain_value, $allowedtags ) . '</label></div>';
            break;

        // Multicheck
        case "multicheck":
            foreach ( $value['options'] as $key => $option ) {
                $checked = '';
                $label = $option;
                $option = preg_replace( '/[^a-zA-Z0-9._\-]/', '', strtolower( $key ) );

                $id = $option_name . '-' . $value['id'] . '-' . $option;
                $name = $option_name . '[' . $value['id'] . '][' . $option . ']';

                if ( isset( $val[$option] ) ) {
                    $checked = checked( $val[$option], 1, false );
                }

                $output .= '<div class="checkbox-container"><input id="' . esc_attr( $id ) . '" class="checkbox of-input" type="checkbox" name="' . esc_attr( $name ) . '" ' . $checked . ' /><label for="' . esc_attr( $id ) . '" class="right-label">' . esc_html( $label ) . '</label></div>';
            }
            break;

        // Toggle Switch
        case "toggle":
            $checked = "";
            if ( $val ) {
                $checked = 'checked="checked"';
            }
            $output .= '<div class="toggle-container" id="' . esc_attr( $value['id'] ) . '_container"><label for="' . esc_attr( $value['id'] ) . '">' . $value['name'] . '</label><input id="' . esc_attr( $value['id'] ) . '"' . $checked . 'class="checkbox-toggle of-input" type="checkbox" name="' . esc_attr( $option_name . '[' . $value['id'] . ']' ) . '" ' . checked( $val, 1, false ) . ' /></div>';
            break;

        // Color picker
        case "color":
            $output .= '<div class="input-prepend ' . $value['class'] . '"><div id="' . esc_attr( $value['id'] . '_picker' ) . '" class="add-on colorSelector"><div style="' . esc_attr( 'background-color:' . $val ) . '"></div></div>';
            $output .= '<input class="of-color" name="' . esc_attr( $option_name . '[' . $value['id'] . ']' ) . '" id="' . esc_attr( $value['id'] ) . '" type="text" value="' . esc_attr( $val ) . '" /></div>';
            break;

        // Uploader
        case "upload":
            $output .= cyberchimps_medialibrary_uploader( $value['class'], $value['id'], $val, null, $explain_value );
            break;

        // Typography
        case 'typography':

            unset( $font_size, $font_style, $font_face, $font_color );

            $typography_defaults = array(
                'size' => '',
                'face' => '',
                'style' => '',
                'color' => ''
            );

            $typography_stored = wp_parse_args( $val, $typography_defaults );

            $typography_options = array(
                'sizes' => cyberchimps_recognized_font_sizes(),
                'faces' => cyberchimps_recognized_font_faces(),
                'styles' => cyberchimps_recognized_font_styles(),
                'color' => true
            );

            if ( isset( $value['options'] ) ) {
                $typography_options = wp_parse_args( $value['options'], $typography_options );
            }

            // Font Size
            if ( $typography_options['sizes'] ) {
                $font_size = '<select class="of-typography of-typography-size" name="' . esc_attr( $option_name . '[' . $value['id'] . '][size]' ) . '" id="' . esc_attr( $value['id'] . '_size' ) . '">';
                $sizes = $typography_options['sizes'];
                foreach ( $sizes as $i ) {
                    $size = $i . 'px';
                    $font_size .= '<option value="' . esc_attr( $size ) . '" ' . selected( $typography_stored['size'], $size, false ) . '>' . esc_html( $size ) . '</option>';
                }
                $font_size .= '</select>';
            }

            // Font Face
            if ( $typography_options['faces'] ) {
                $font_face = '<select class="of-typography of-typography-face" name="' . esc_attr( $option_name . '[' . $value['id'] . '][face]' ) . '" id="' . esc_attr( $value['id'] . '_face' ) . '">';
                $faces = $typography_options['faces'];
                foreach ( $faces as $key => $face ) {
                    $font_face .= '<option value="' . esc_attr( $key ) . '" ' . selected( $typography_stored['face'], $key, false ) . '>' . esc_html( $face ) . '</option>';
                }
                $font_face .= '</select>';
            }

            // Font Styles
            if ( $typography_options['styles'] ) {
                $font_style = '<select class="of-typography of-typography-style" name="' . $option_name . '[' . $value['id'] . '][style]" id="' . $value['id'] . '_style">';
                $styles = $typography_options['styles'];
                foreach ( $styles as $key => $style ) {
                    $font_style .= '<option value="' . esc_attr( $key ) . '" ' . selected( $typography_stored['style'], $key, false ) . '>' . $style . '</option>';
                }
                $font_style .= '</select>';
            }

            // Font Color
            if ( $typography_options['color'] ) {
                $font_color = '<div class="input-prepend of-typography"><div id="' . esc_attr( $value['id'] ) . '_color_picker" class="add-on colorSelector"><div style="' . esc_attr( 'background-color:' . $typography_stored['color'] ) . '"></div></div>';
                $font_color .= '<input class="of-color of-typography of-typography-color" name="' . esc_attr( $option_name . '[' . $value['id'] . '][color]' ) . '" id="' . esc_attr( $value['id'] . '_color' ) . '" type="text" value="' . esc_attr( $typography_stored['color'] ) . '" /></div>';
            }

            // Allow modification/injection of typography fields
            $typography_fields = compact( 'font_size', 'font_face', 'font_style', 'font_color' );
            $typography_fields = apply_filters( 'cyberchimps_typography_fields', $typography_fields, $typography_stored, $option_name, $value );
            $output .= implode( '', $typography_fields );

            break;

        // Background
        case 'background':

            $background = $val;

            // Background Color
            $output .= '<div class="input-prepend"><div id="' . esc_attr( $value['id'] ) . '_color_picker" class="add-on colorSelector"><div style="' . esc_attr( 'background-color:' . $background['color'] ) . '"></div></div>';
            $output .= '<input class="of-color of-background of-background-color" name="' . esc_attr( $option_name . '[' . $value['id'] . '][color]' ) . '" id="' . esc_attr( $value['id'] . '_color' ) . '" type="text" value="' . esc_attr( $background['color'] ) . '" /></div>';

            // Background Image - New AJAX Uploader using Media Library
            if ( !isset( $background['image'] ) ) {
                $background['image'] = '';
            }

            $output .= cyberchimps_medialibrary_uploader( $value['class'], $value['id'], $background['image'], null, '', 0, 'image' );
            $class = 'of-background-properties';
            if ( '' == $background['image'] ) {
                $class .= ' hide';
            }
            $output .= '<div class="' . esc_attr( $class ) . '">';

            // Background Repeat
            $output .= '<select class="of-background of-background-repeat" name="' . esc_attr( $option_name . '[' . $value['id'] . '][repeat]' ) . '" id="' . esc_attr( $value['id'] . '_repeat' ) . '">';
            $repeats = cyberchimps_recognized_background_repeat();

            foreach ( $repeats as $key => $repeat ) {
                $output .= '<option value="' . esc_attr( $key ) . '" ' . selected( $background['repeat'], $key, false ) . '>' . esc_html( $repeat ) . '</option>';
            }
            $output .= '</select>';

            // Background Position
            $output .= '<select class="of-background of-background-position" name="' . esc_attr( $option_name . '[' . $value['id'] . '][position]' ) . '" id="' . esc_attr( $value['id'] . '_position' ) . '">';
            $positions = cyberchimps_recognized_background_position();

            foreach ( $positions as $key => $position ) {
                $output .= '<option value="' . esc_attr( $key ) . '" ' . selected( $background['position'], $key, false ) . '>' . esc_html( $position ) . '</option>';
            }
            $output .= '</select>';

            // Background Attachment
            $output .= '<select class="of-background of-background-attachment" name="' . esc_attr( $option_name . '[' . $value['id'] . '][attachment]' ) . '" id="' . esc_attr( $value['id'] . '_attachment' ) . '">';
            $attachments = cyberchimps_recognized_background_attachment();

            foreach ( $attachments as $key => $attachment ) {
                $output .= '<option value="' . esc_attr( $key ) . '" ' . selected( $background['attachment'], $key, false ) . '>' . esc_html( $attachment ) . '</option>';
            }
            $output .= '</select>';
            $output .= '</div>';

            break;

        // Editor
        case 'editor':
            $output .= '<div class="explain">' . wp_kses( $explain_value, $allowedtags ) . '</div>' . "\n";
            echo $output;
            $textarea_name = esc_attr( $option_name . '[' . $value['id'] . ']' );
            $default_editor_settings = array(
                'textarea_name' => $textarea_name,
                'media_buttons' => false,
                'tinymce' => array( 'plugins' => 'wordpress' )
            );
            $editor_settings = array();
            if ( isset( $value['settings'] ) ) {
                $editor_settings = $value['settings'];
            }

            $editor_settings = array_merge( $default_editor_settings, $editor_settings );
            wp_editor( $val, $value['id'], $editor_settings );
            $output = '';
            break;

        // Info
        case "info":
            $id = '';
            $class = 'section';
            if ( isset( $value['id'] ) ) {
                $id = 'id="' . esc_attr( $value['id'] ) . '" ';
            }
            if ( isset( $value['type'] ) ) {
                $class .= ' section-' . $value['type'];
            }
            if ( isset( $value['class'] ) ) {
                $class .= ' ' . $value['class'];
            }

            $output .= '<div ' . $id . 'class="' . esc_attr( $class ) . '">' . "\n";
            if ( isset( $value['name'] ) ) {
                $output .= '<h4 class="heading">' . esc_html( $value['name'] ) . '</h4>' . "\n";
            }
            if ( $value['desc'] ) {
                $output .= apply_filters( 'cyberchimps_sanitize_info', $value['desc'] ) . "\n";
            }
            $output .= '</div>' . "\n";
            break;

        // Welcome
        case "welcome":
            $id = '';
            $class = 'section';
            if ( isset( $value['id'] ) ) {
                $id = 'id="' . esc_attr( $value['id'] ) . '" ';
            }
            if ( isset( $value['type'] ) ) {
                $class .= ' section-' . $value['type'];
            }
            if ( isset( $value['class'] ) ) {
                $class .= ' ' . $value['class'];
            }
            $output .= '<div ' . $id . 'class="' . esc_attr( $class ) . '">' . "\n";
            if ( isset( $value['name'] ) ) {
                $output .= '<h4 class="heading">' . esc_html( apply_filters( 'cyberchimps_help_sub_heading', $value['name'] ) ) . '</h4>' . "\n";
            }
            $output .= apply_filters( 'cyberchimps_sanitize_info', apply_filters( 'cyberchimps_help_description', '' ) ) . "\n";

            $output .= '</div>' . "\n";
            break;

        case "export":
            $output .= "<textarea rows='10'>" . esc_html( serialize( $settings ) ) . "</textarea>";
            $output .= '<br/><a class="btn btn-info export-option" href="data:text/octet-stream;charset=utf-8,' . esc_html( serialize( $settings ) ) . '" download="theme-option-backup.txt">Download</a>';
            break;

        case "import":
            $output .= "<textarea name='import' rows='10'></textarea>";
            $output .= "<br/><input type='file' id='import_file' name='import_file' />";
            break;
    }

    if ( ( $value['type'] != "heading" ) && ( $value['type'] != "info" ) && ( $value['type'] != "welcome" ) && ( $value['type'] != "upload" ) ) {
        if ( ( $value['type'] != "checkbox" ) && ( $value['type'] != "editor" ) ) {
            $output .= '<div class="desc">' . wp_kses( $explain_value, $allowedtags ) . '</div>' . "\n";
        }
    }

    // end field wrapper
    $output .= '</div>';

    echo $output;
}

/**
 * Validate options
 *
 * Validate theme options before updating to database.
 */
function cyberchimps_options_validate( $input ) {

    global $wp_filesystem;
    /*
     * Import functionality
     *
     * Both the copy/paste and file upload options are active. First it checks for file, if any file is uploaded then
     * it processes that. Otherwise it checks if anything is sent with the textarea for the import.
     */

    // Check if any file is uplaoded
    if ( isset( $_FILES['import_file'] ) && $_FILES['import_file']['name'] ) {

        // Initialise WP filesystem.
        WP_Filesystem( request_filesystem_credentials( 'options.php', '', false, false, null ) );

        // Get the text of the uploaded file and trim it to remove space from either end.
        $import_file_text = trim( $wp_filesystem->get_contents( $_FILES['import_file']['tmp_name'] ) );

        if ( $import_file_text ) {
            $string = stripslashes( $import_file_text );

            // check string is serialized and unserialize it
            if ( is_serialized( $string ) ) {
                $try = unserialize( ( $string ) );
            }

            // make sure $try is set with the unserialized data
            if ( $try ) {
                add_settings_error( 'cyberchimps_options', 'imported_success', __( 'Options Imported', 'cyberchimps_core' ), 'updated fade' );

                return $try;
            } else {
                add_settings_error( 'cyberchimps_options', 'imported_failed', __( 'Invalid Data for Import', 'cyberchimps_core' ), 'error fade' );
            }
        }
    }
    // If no file is uploaded then check for the texarea field for improt option.
    else {
        if ( isset( $_POST['import'] ) ) {
            if ( trim( $_POST['import'] ) ) {

                $string = stripslashes( trim( $_POST['import'] ) );

                // check string is serialized and unserialize it
                if ( is_serialized( $string ) ) {
                    $try = unserialize( ( $string ) );
                }

                // make sure $try is set with the unserialized data
                if ( $try ) {
                    add_settings_error( 'cyberchimps_options', 'imported_success', __( 'Options Imported', 'cyberchimps_core' ), 'updated fade' );

                    return $try;
                } else {
                    add_settings_error( 'cyberchimps_options', 'imported_failed', __( 'Invalid Data for Import', 'cyberchimps_core' ), 'error fade' );
                }
            }
        }
    }

    /*
     * Restore Defaults.
     *
     * In the event that the user clicked the "Restore Defaults"
     * button, the options defined in the theme's options.php
     * file will be added to the option for the active theme.
     */
    if ( isset( $_POST['reset'] ) ) {
        add_settings_error( 'cyberchimps_options', 'restore_defaults', __( 'Default options restored.', 'cyberchimps_core' ), 'updated fade' );

        return cyberchimps_get_default_values();
    }

    /*
     * Update Settings
     *
     * This used to check for $_POST['update'], but has been updated
     * to be compatible with the theme customizer introduced in WordPress 3.4
     */ else {
        $clean = array();
        $options = cyberchimps_get_fields();
        foreach ( $options as $option ) {
            if ( !isset( $option['id'] ) ) {
                continue;
            }

            if ( !isset( $option['type'] ) ) {
                continue;
            }

            $id = preg_replace( '/[^a-zA-Z0-9._\-]/', '', strtolower( $option['id'] ) );

            // Set upload to false if it wasn't sent in the $_POST
            if ( 'info' == $option['type'] && !isset( $input[$id] ) ) {
                $input[$id] = false;
            }

            // Set upload to false if it wasn't sent in the $_POST
            if ( 'upload' == $option['type'] && !isset( $input[$id] ) ) {
                $input[$id] = false;
            }

            // Set radio to false if it wasn't sent in the $_POST
            if ( 'radio' == $option['type'] && !isset( $input[$id] ) ) {
                $input[$id] = false;
            }

            // Set toggle to false if it wasn't sent in the $_POST
            if ( 'toggle' == $option['type'] && !isset( $input[$id] ) ) {
                $input[$id] = false;
            }

            // Set checkbox to false if it wasn't sent in the $_POST
            if ( 'checkbox' == $option['type'] && !isset( $input[$id] ) ) {
                $input[$id] = false;
            }

            // Set checkbox to false if it wasn't sent in the $_POST
            if ( 'images' == $option['type'] && !isset( $input[$id] ) ) {
                $input[$id] = false;
            }

            // Set each item in the multicheck to false if it wasn't sent in the $_POST
            if ( 'multicheck' == $option['type'] && !isset( $input[$id] ) ) {
                foreach ( $option['options'] as $key => $value ) {
                    $input[$id][$key] = false;
                }
            }

            // Catch any other id's that have not been set and set them to false. For some themes we remove options and we don't want them causing a problem
            if ( !isset( $input[$id] ) ) {
                $input[$id] = false;
            }

            // For a value to be submitted to database it must pass through a sanitization filter
            if ( has_filter( 'cyberchimps_sanitize_' . $option['type'] ) ) {
                $clean[$id] = apply_filters( 'cyberchimps_sanitize_' . $option['type'], $input[$id], $option );
            }
        }

        do_action( 'cyberchimps_options_before_save', $input );

        add_settings_error( 'cyberchimps_options', 'save_options', __( 'Options saved.', 'cyberchimps_core' ), 'updated fade' );

        return $clean;
    }
}

/**
 * Format Configuration Array.
 *
 * Get an array of all default values as set in
 * options.php. The 'id','std' and 'type' keys need
 * to be defined in the configuration array. In the
 * event that these keys are not present the option
 * will not be included in this function's output.
 *
 * @return    array     Rey-keyed options configuration array.
 *
 * @access    private
 */
function cyberchimps_get_default_values() {
    $output = array();
    $config = cyberchimps_get_fields();
    foreach ( (array) $config as $option ) {
        if ( !isset( $option['id'] ) ) {
            continue;
        }
        if ( !isset( $option['std'] ) ) {
            continue;
        }
        if ( !isset( $option['type'] ) ) {
            continue;
        }
        if ( has_filter( 'cyberchimps_sanitize_' . $option['type'] ) ) {
            $output[$option['id']] = apply_filters( 'cyberchimps_sanitize_' . $option['type'], $option['std'], $option );
        }
    }

    return $output;
}

/**
 * Add Theme Options menu item to Admin Bar.
 */
function cyberchimps_admin_bar() {
    global $wp_admin_bar;

    $wp_admin_bar->add_menu( array(
        'parent' => 'appearance',
        'id' => 'cyberchimps_options_page',
        'title' => __( 'Theme Options', 'cyberchimps_core' ),
        'href' => admin_url( 'themes.php?page=cyberchimps-theme-options' )
    ) );
}