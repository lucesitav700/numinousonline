<?php

namespace IGD;

defined( 'ABSPATH' ) || exit;
class Elementor
{
    /**
     * @var null
     */
    protected static  $instance = null ;
    public function __construct()
    {
        add_action( 'elementor/frontend/before_enqueue_scripts', [ $this, 'frontend_scripts' ] );
        add_action( 'elementor/editor/before_enqueue_scripts', [ $this, 'editor_scripts' ] );
        // Register default widgets
        add_action( 'elementor/elements/categories_registered', [ $this, 'add_categories' ] );
        add_action( 'elementor/widgets/register', [ $this, 'register_widgets' ] );
        add_filter( 'elementor/editor/localize_settings', [ $this, 'promote_pro_elements' ] );
    }
    
    public function elementor_form_may_create_entry_folder( $record )
    {
        $fields = $record->get( 'fields' );
        // Return early if fields are empty
        if ( empty($fields) ) {
            return;
        }
        // Prepare igd_fields array and populate tags array with field values
        $igd_fields = [];
        foreach ( $fields as $field ) {
            if ( $field['type'] == 'google_drive_upload' ) {
                $igd_fields[$field['id']] = $field;
            }
        }
        if ( empty($igd_fields) ) {
            return;
        }
        $form_fields = $record->get_form_settings( 'form_fields' );
        foreach ( $igd_fields as $id => $field ) {
            $field_data_key = array_search( $id, array_column( $form_fields, 'custom_id' ) );
            
            if ( $field_data_key === false ) {
                continue;
                // Exit early if field not found
            }
            
            $value = $field['value'];
            if ( empty($value) ) {
                continue;
            }
            $files = [];
            // Fetch file ids from the value text
            preg_match_all( '/file\\/d\\/(.*?)\\/view/', $value, $matches );
            $file_ids = $matches[1];
            if ( empty($file_ids) ) {
                continue;
            }
            foreach ( $file_ids as $file_id ) {
                $files[] = App::instance()->get_file_by_id( $file_id );
            }
            
            if ( empty($files) ) {
                continue;
                // Exit early if no files
            }
            
            $field_data = $form_fields[$field_data_key];
            $igd_data = json_decode( $field_data['module_data'], true );
            if ( empty($igd_data['createEntryFolders']) ) {
                continue;
            }
            $entry_folder_name_template = ( isset( $igd_data['entryFolderNameTemplate'] ) ? $igd_data['entryFolderNameTemplate'] : 'Entry (%entry_id%) - %form_title% ' );
            $tag_data = [
                'name' => $entry_folder_name_template,
                'form' => [
                'form_title' => $record->get_form_settings( 'form_name' ),
            ],
            ];
            
            if ( false !== strpos( $entry_folder_name_template, '%entry_id%' ) ) {
                $submit_actions = $record->get_form_settings( 'submit_actions' );
                
                if ( in_array( 'save-to-database', $submit_actions ) ) {
                    global  $wpdb ;
                    $table = "{$wpdb->prefix}e_submissions";
                    $submission_id = $wpdb->get_var( "SELECT MAX(ID) FROM {$table}" );
                    $tag_data['form']['entry_id'] = $submission_id;
                }
            
            }
            
            if ( igd_contains_tags( 'user', $entry_folder_name_template ) ) {
                if ( is_user_logged_in() ) {
                    $tag_data['user'] = get_userdata( get_current_user_id() );
                }
            }
            
            if ( igd_contains_tags( 'post', $entry_folder_name_template ) ) {
                $referrer = wp_get_referer();
                
                if ( !empty($referrer) ) {
                    $post_id = url_to_postid( $referrer );
                    
                    if ( !empty($post_id) ) {
                        $tag_data['post'] = get_post( $post_id );
                        if ( $tag_data['post']->post_type == 'product' ) {
                            $tag_data['wc_product'] = wc_get_product( $post_id );
                        }
                    }
                
                }
            
            }
            
            $extra_tags = [];
            // Populate tags array with field values
            foreach ( $fields as $form_field ) {
                if ( $form_field['type'] == 'google_drive_upload' ) {
                    continue;
                }
                $extra_tags['%' . $form_field['id'] . '%'] = ( is_array( $form_field['value'] ) ? implode( ', ', $form_field['value'] ) : $form_field['value'] );
            }
            $folder_name = igd_replace_template_tags( $tag_data, $extra_tags );
            // Check Private Folders
            $private_folders = !empty($igd_data['privateFolders']);
            
            if ( $private_folders && is_user_logged_in() ) {
                $folders = get_user_option( 'folders', get_current_user_id() );
                if ( !empty($folders) ) {
                    $folders = array_values( array_filter( (array) $folders, function ( $item ) {
                        return igd_is_dir( $item );
                    } ) );
                }
                if ( !empty($folders) ) {
                    $igd_data['folders'] = $folders;
                }
            }
            
            $upload_folder = ( !empty($igd_data['folders'][0]) ? $igd_data['folders'][0] : [
                'id'        => 'root',
                'accountId' => '',
            ] );
            $merge_folders = ( isset( $igd_data['mergeFolders'] ) ? filter_var( $igd_data['mergeFolders'], FILTER_VALIDATE_BOOLEAN ) : false );
            Uploader::instance( $upload_folder['accountId'] )->create_entry_folder_and_move(
                $files,
                $folder_name,
                $upload_folder,
                $merge_folders
            );
        }
    }
    
    function metform_may_create_entry_folder(
        $form_id,
        $form_data,
        $form_settings,
        $attributes
    )
    {
        $input_widgets = \Metform\Widgets\Manifest::instance()->get_input_widgets();
        $widget_input_data = get_post_meta( $form_id, '_elementor_data', true );
        $widget_input_data = json_decode( $widget_input_data );
        $widgets = \MetForm\Core\Entries\Map_El::data( $widget_input_data, $input_widgets )->get_el();
        // Return early if fields are empty
        if ( empty($widgets) ) {
            return;
        }
        // Prepare igd_fields array and populate tags array with field values
        $igd_widgets = [];
        foreach ( $widgets as $key => $field ) {
            if ( $field->widgetType == 'mf-igd-uploader' ) {
                $igd_widgets[$key] = $field;
            }
        }
        if ( empty($igd_widgets) ) {
            return;
        }
        foreach ( $igd_widgets as $key => $widget ) {
            if ( !isset( $form_data[$key] ) ) {
                continue;
            }
            $value = $form_data[$key];
            if ( empty($value) ) {
                continue;
            }
            $files = [];
            // Fetch file ids from the value text
            preg_match_all( '/file\\/d\\/(.*?)\\/view/', $value, $matches );
            $file_ids = $matches[1];
            if ( empty($file_ids) ) {
                continue;
            }
            foreach ( $file_ids as $file_id ) {
                $files[] = App::instance()->get_file_by_id( $file_id );
            }
            $igd_data = json_decode( $widget->module_data, true );
            $create_entry_folder = !empty($igd_data['createEntryFolders']);
            if ( !$create_entry_folder ) {
                continue;
            }
            $entry_folder_name_template = ( !empty($igd_data['entryFolderNameTemplate']) ? $igd_data['entryFolderNameTemplate'] : 'Entry (%entry_id%) - %form_title%' );
            $tag_data = [
                'name' => $entry_folder_name_template,
                'form' => [
                'form_title' => $form_settings['form_title'],
                'form_id'    => $form_id,
            ],
            ];
            if ( false !== strpos( $entry_folder_name_template, '%entry_id%' ) ) {
                
                if ( $form_settings['store_entries'] ) {
                    global  $wpdb ;
                    $entry_id = $wpdb->get_var( "SELECT ID FROM {$wpdb->posts} WHERE post_type = 'metform-entry' ORDER BY ID DESC LIMIT 1" );
                    $tag_data['form']['entry_id'] = $entry_id;
                }
            
            }
            if ( igd_contains_tags( 'user', $entry_folder_name_template ) ) {
                if ( is_user_logged_in() ) {
                    $tag_data['user'] = get_userdata( get_current_user_id() );
                }
            }
            
            if ( igd_contains_tags( 'post', $entry_folder_name_template ) ) {
                $referrer = wp_get_referer();
                
                if ( !empty($referrer) ) {
                    $post_id = url_to_postid( $referrer );
                    
                    if ( !empty($post_id) ) {
                        $tag_data['post'] = get_post( $post_id );
                        if ( $tag_data['post']->post_type == 'product' ) {
                            $tag_data['wc_product'] = wc_get_product( $post_id );
                        }
                    }
                
                }
            
            }
            
            $extra_tags = [];
            foreach ( $widgets as $field_key => $widget_field ) {
                $field_value = $form_data[$key];
                // Handle array values, such as checkboxes
                if ( is_array( $field_value ) ) {
                    $field_value = implode( ', ', $field_value );
                }
                $extra_tags['%' . $field_key . '%'] = $field_value;
            }
            $folder_name = igd_replace_template_tags( $tag_data, $extra_tags );
            // Check Private Folders
            $private_folders = !empty($igd_data['privateFolders']);
            
            if ( $private_folders && is_user_logged_in() ) {
                $folders = get_user_option( 'folders', get_current_user_id() );
                if ( !empty($folders) ) {
                    $folders = array_values( array_filter( (array) $folders, function ( $item ) {
                        return igd_is_dir( $item );
                    } ) );
                }
                if ( !empty($folders) ) {
                    $igd_data['folders'] = $folders;
                }
            }
            
            $upload_folder = ( !empty($igd_data['folders'][0]) ? $igd_data['folders'][0] : [
                'id'        => 'root',
                'accountId' => '',
            ] );
            $merge_folders = ( isset( $igd_data['mergeFolders'] ) ? filter_var( $igd_data['mergeFolders'], FILTER_VALIDATE_BOOLEAN ) : false );
            Uploader::instance( $upload_folder['accountId'] )->create_entry_folder_and_move(
                $files,
                $folder_name,
                $upload_folder,
                $merge_folders
            );
        }
    }
    
    function metform_register_widgets( $widget_list )
    {
        $widget_list[] = 'mf-igd-uploader';
        return $widget_list;
    }
    
    public function register_form_fields( $fields_manager )
    {
        include_once IGD_INCLUDES . '/elementor/class-elementor-form__premium_only.php';
        $fields_manager->register( new Google_Drive_Upload() );
    }
    
    public function promote_pro_elements( $config )
    {
        $promotion_widgets = [];
        if ( isset( $config['promotionWidgets'] ) ) {
            $promotion_widgets = $config['promotionWidgets'];
        }
        $combine_array = array_merge( $promotion_widgets, [
            [
            'name'       => 'igd_browser',
            'title'      => __( 'File Browser', 'integrate-google-drive' ),
            'icon'       => 'igd-browser',
            'categories' => '["integrate_google_drive"]',
        ],
            [
            'name'       => 'igd_uploader',
            'title'      => __( 'File Uploader', 'integrate-google-drive' ),
            'icon'       => 'igd-uploader',
            'categories' => '["integrate_google_drive"]',
        ],
            [
            'name'       => 'igd_media',
            'title'      => __( 'Media Player', 'integrate-google-drive' ),
            'icon'       => 'igd-media',
            'categories' => '["integrate_google_drive"]',
        ],
            [
            'name'       => 'igd_search',
            'title'      => __( 'Search Box', 'integrate-google-drive' ),
            'icon'       => 'igd-search',
            'categories' => '["integrate_google_drive"]',
        ],
            [
            'name'       => 'igd_slider',
            'title'      => __( 'Slider Carousel', 'integrate-google-drive' ),
            'icon'       => 'igd-slider',
            'categories' => '["integrate_google_drive"]',
        ]
        ] );
        $config['promotionWidgets'] = $combine_array;
        return $config;
    }
    
    public function editor_scripts()
    {
        wp_enqueue_style(
            'igd-elementor-editor',
            IGD_ASSETS . '/css/elementor-editor.css',
            [],
            IGD_VERSION
        );
        wp_style_add_data( 'igd-elementor-editor', 'rtl', 'replace' );
    }
    
    public function frontend_scripts()
    {
        if ( isset( $_GET['elementor-preview'] ) ) {
            Enqueue::instance()->admin_scripts( '', false );
        }
        Enqueue::instance()->frontend_scripts();
        wp_enqueue_script(
            'igd-elementor',
            IGD_ASSETS . '/js/elementor.js',
            [ 'jquery' ],
            IGD_VERSION,
            true
        );
    }
    
    public function register_widgets( $widgets_manager )
    {
        include_once IGD_INCLUDES . '/elementor/class-elementor-shortcodes-widget.php';
        
        if ( method_exists( $widgets_manager, 'register' ) ) {
            $widgets_manager->register( new Shortcodes_Widget() );
        } else {
            $widgets_manager->register_widget_type( new Shortcodes_Widget() );
        }
        
        include_once IGD_INCLUDES . '/elementor/class-elementor-gallery-widget.php';
        include_once IGD_INCLUDES . '/elementor/class-elementor-embed-widget.php';
        include_once IGD_INCLUDES . '/elementor/class-elementor-download-widget.php';
        include_once IGD_INCLUDES . '/elementor/class-elementor-view-widget.php';
        
        if ( method_exists( $widgets_manager, 'register' ) ) {
            $widgets_manager->register( new Shortcodes_Widget() );
            $widgets_manager->register( new Gallery_Widget() );
            $widgets_manager->register( new Embed_Widget() );
            $widgets_manager->register( new Download_Widget() );
            $widgets_manager->register( new View_Widget() );
        } else {
            $widgets_manager->register_widget_type( new Shortcodes_Widget() );
            $widgets_manager->register_widget_type( new Gallery_Widget() );
            $widgets_manager->register_widget_type( new Embed_Widget() );
            $widgets_manager->register_widget_type( new Download_Widget() );
            $widgets_manager->register_widget_type( new View_Widget() );
        }
    
    }
    
    public function add_categories( $elements_manager )
    {
        $elements_manager->add_category( 'integrate_google_drive', [
            'title' => __( 'Integrate Google Drive', 'integrate-google-drive' ),
            'icon'  => 'fa fa-plug',
        ] );
    }
    
    /**
     * @return Elementor|null
     */
    public static function instance()
    {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

}
Elementor::instance();