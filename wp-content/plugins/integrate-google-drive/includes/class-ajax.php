<?php

namespace IGD;

defined( 'ABSPATH' ) || exit;
class Ajax
{
    private static  $instance = null ;
    public function __construct()
    {
        // Preview content
        add_action( 'wp_ajax_igd_preview', [ $this, 'preview' ] );
        add_action( 'wp_ajax_nopriv_igd_preview', [ $this, 'preview' ] );
        // Get share URL
        add_action( 'wp_ajax_igd_get_share_link', [ $this, 'get_share_link' ] );
        add_action( 'wp_ajax_nopriv_igd_get_share_link', [ $this, 'get_share_link' ] );
        // Generate thumbnail
        add_action( 'wp_ajax_igd_get_preview_thumbnail', [ $this, 'get_preview_thumbnail' ] );
        add_action( 'wp_ajax_nopriv_igd_get_preview_thumbnail', [ $this, 'get_preview_thumbnail' ] );
        // Delete Shortcode
        add_action( 'wp_ajax_igd_delete_shortcode', [ $this, 'delete_shortcode' ] );
        add_action( 'wp_ajax_nopriv_igd_delete_shortcode', [ $this, 'delete_shortcode' ] );
        add_action( 'wp_ajax_igd_get_shortcodes', [ $this, 'get_shortcodes' ] );
        add_action( 'wp_ajax_nopriv_igd_get_shortcodes', [ $this, 'get_shortcodes' ] );
        // Clear cache files
        add_action( 'wp_ajax_igd_clear_cache', [ $this, 'clear_cache' ] );
        // Download file
        add_action( 'wp_ajax_igd_download', [ $this, 'download' ] );
        add_action( 'wp_ajax_nopriv_igd_download', [ $this, 'download' ] );
        // Get download status
        add_action( 'wp_ajax_igd_download_status', [ $this, 'get_download_status' ] );
        add_action( 'wp_ajax_nopriv_igd_download_status', [ $this, 'get_download_status' ] );
        // Zip Download
        add_action( 'wp_ajax_igd_download_zip', [ $this, 'download_zip' ] );
        add_action( 'wp_ajax_nopriv_igd_download_zip', [ $this, 'download_zip' ] );
        // Get upload direct url
        add_action( 'wp_ajax_igd_get_upload_url', [ $this, 'get_upload_url' ] );
        add_action( 'wp_ajax_nopriv_igd_get_upload_url', [ $this, 'get_upload_url' ] );
        // Stream
        add_action( 'wp_ajax_igd_stream', [ $this, 'stream_content' ] );
        add_action( 'wp_ajax_nopriv_igd_stream', [ $this, 'stream_content' ] );
        // Handle admin  notice
        add_action( 'wp_ajax_igd_hide_review_notice', [ $this, 'hide_review_notice' ] );
        add_action( 'wp_ajax_igd_review_feedback', [ $this, 'handle_review_feedback' ] );
        // Hide Recommended Plugins
        add_action( 'wp_ajax_igd_hide_recommended_plugins', [ $this, 'hide_recommended_plugins' ] );
        // Upload post process
        add_action( 'wp_ajax_igd_file_uploaded', [ $this, 'upload_post_process' ] );
        add_action( 'wp_ajax_nopriv_igd_file_uploaded', [ $this, 'upload_post_process' ] );
        // Remove uploaded files
        add_action( 'wp_ajax_igd_upload_remove_file', [ $this, 'remove_upload_file' ] );
        add_action( 'wp_ajax_nopriv_igd_upload_remove_file', [ $this, 'remove_upload_file' ] );
        // Move File
        add_action( 'wp_ajax_igd_move_file', [ $this, 'move_file' ] );
        add_action( 'wp_ajax_nopriv_igd_move_file', [ $this, 'move_file' ] );
        // Rename File
        add_action( 'wp_ajax_igd_rename_file', [ $this, 'rename_file' ] );
        add_action( 'wp_ajax_nopriv_igd_rename_file', [ $this, 'rename_file' ] );
        // Copy Files
        add_action( 'wp_ajax_igd_copy_file', [ $this, 'copy_file' ] );
        add_action( 'wp_ajax_nopriv_igd_copy_file', [ $this, 'copy_file' ] );
        // New Folder
        add_action( 'wp_ajax_igd_new_folder', [ $this, 'new_folder' ] );
        add_action( 'wp_ajax_nopriv_igd_new_folder', [ $this, 'new_folder' ] );
        // Switch Account
        add_action( 'wp_ajax_igd_switch_account', [ $this, 'switch_account' ] );
        add_action( 'wp_ajax_nopriv_igd_switch_account', [ $this, 'switch_account' ] );
        // Delete Account
        add_action( 'wp_ajax_igd_delete_account', [ $this, 'delete_account' ] );
        add_action( 'wp_ajax_nopriv_igd_delete_account', [ $this, 'delete_account' ] );
        // Save Settings
        add_action( 'wp_ajax_igd_save_settings', [ $this, 'save_settings' ] );
        add_action( 'wp_ajax_nopriv_igd_save_settings', [ $this, 'save_settings' ] );
        // Get Files
        add_action( 'wp_ajax_igd_get_files', [ $this, 'get_files' ] );
        add_action( 'wp_ajax_nopriv_igd_get_files', [ $this, 'get_files' ] );
        // Update Shortcode
        add_action( 'wp_ajax_igd_update_shortcode', [ $this, 'update_shortcode' ] );
        add_action( 'wp_ajax_nopriv_igd_update_shortcode', [ $this, 'update_shortcode' ] );
        // Duplicate Shortcode
        add_action( 'wp_ajax_igd_duplicate_shortcode', [ $this, 'duplicate_shortcode' ] );
        add_action( 'wp_ajax_nopriv_igd_duplicate_shortcode', [ $this, 'duplicate_shortcode' ] );
        // Update User Folders
        add_action( 'wp_ajax_igd_update_user_folders', [ $this, 'update_user_folders' ] );
        add_action( 'wp_ajax_nopriv_igd_update_user_folders', [ $this, 'update_user_folders' ] );
        // Get Users Data
        add_action( 'wp_ajax_igd_get_users_data', [ $this, 'get_users_data' ] );
        add_action( 'wp_ajax_nopriv_igd_get_users_data', [ $this, 'get_users_data' ] );
        // Get Shortcode Content
        add_action( 'wp_ajax_igd_get_shortcode_content', [ $this, 'get_shortcode_content' ] );
        add_action( 'wp_ajax_nopriv_igd_get_shortcode_content', [ $this, 'get_shortcode_content' ] );
        // Get File
        add_action( 'wp_ajax_igd_get_file', [ $this, 'get_file' ] );
        add_action( 'wp_ajax_nopriv_igd_get_file', [ $this, 'get_file' ] );
        // Search Files
        add_action( 'wp_ajax_igd_search_files', [ $this, 'search_files' ] );
        add_action( 'wp_ajax_nopriv_igd_search_files', [ $this, 'search_files' ] );
        // Get Embed Content
        add_action( 'wp_ajax_igd_get_embed_content', [ $this, 'get_embed_content' ] );
        add_action( 'wp_ajax_nopriv_igd_get_embed_content', [ $this, 'get_embed_content' ] );
        // Delete Files
        add_action( 'wp_ajax_igd_delete_files', [ $this, 'delete_files' ] );
        add_action( 'wp_ajax_nopriv_igd_delete_files', [ $this, 'delete_files' ] );
        // Create Doc
        add_action( 'wp_ajax_igd_create_doc', [ $this, 'create_doc' ] );
        add_action( 'wp_ajax_nopriv_igd_create_doc', [ $this, 'create_doc' ] );
        // Get Export Data
        add_action( 'wp_ajax_igd_get_export_data', [ $this, 'get_export_data' ] );
        add_action( 'wp_ajax_nopriv_igd_get_export_data', [ $this, 'get_export_data' ] );
        // Import Data
        add_action( 'wp_ajax_igd_import_data', [ $this, 'import_data' ] );
        add_action( 'wp_ajax_nopriv_igd_import_data', [ $this, 'import_data' ] );
        // Update Description
        add_action( 'wp_ajax_igd_update_description', [ $this, 'update_description' ] );
        add_action( 'wp_ajax_nopriv_igd_update_description', [ $this, 'update_description' ] );
        // Send photo proof selection
        add_action( 'wp_ajax_igd_photo_proof', [ $this, 'photo_proof' ] );
        add_action( 'wp_ajax_nopriv_igd_photo_proof', [ $this, 'photo_proof' ] );
        // photo proof download
        add_action( 'wp_ajax_igd_photo_proof_download', [ $this, 'photo_proof_download' ] );
        add_action( 'wp_ajax_nopriv_igd_photo_proof_download', [ $this, 'photo_proof_download' ] );
    }
    
    public function photo_proof_download()
    {
        $filedata = ( isset( $_GET['filedata'] ) ? igd_sanitize_array( $_GET['filedata'] ) : [] );
        // Create a CSV file in memory
        $output = fopen( 'php://output', 'w' );
        // Send headers to force download
        header( 'Content-Type: text/csv' );
        header( 'Content-Disposition: attachment; filename="photo_proof-' . date( 'Y-m-d' ) . '.csv"' );
        // Write the CSV column headers
        fputcsv( $output, [ 'id', 'name', 'url' ] );
        // Write the filedata to the CSV
        foreach ( $filedata as $row ) {
            $file_url = sprintf( 'https://drive.google.com/file/d/%1$s/view', $row['id'] );
            fputcsv( $output, [ $row['id'], $row['name'], $file_url ] );
        }
        // Close the file pointer and exit
        fclose( $output );
        exit;
    }
    
    public function photo_proof()
    {
        $to = ( !empty($_POST['email']) ? sanitize_email( $_POST['email'] ) : get_option( 'admin_email' ) );
        $message = ( !empty($_POST['message']) ? sanitize_textarea_field( $_POST['message'] ) : '' );
        $selected = ( !empty($_POST['selected']) ? igd_sanitize_array( $_POST['selected'] ) : [] );
        $referrer = ( !empty($_SERVER['HTTP_REFERER']) ? esc_url_raw( $_SERVER['HTTP_REFERER'] ) : '' );
        $shortcode_id = ( !empty($_POST['shortcode_id']) ? sanitize_text_field( $_POST['shortcode_id'] ) : '' );
        ob_start();
        include_once IGD_INCLUDES . '/views/photo-proof-email__premium_only.php';
        $content = ob_get_clean();
        $subject = __( 'Client Photo Proof Selection', 'integrate-google-drive' );
        $headers = [ 'Content-Type: text/html; charset=UTF-8', 'From: ' . get_bloginfo( 'name' ) . ' <' . get_option( 'admin_email' ) . '>' ];
        
        if ( is_user_logged_in() ) {
            $user = wp_get_current_user();
            $headers[] = 'Reply-To: ' . $user->user_email;
        }
        
        wp_mail(
            $to,
            $subject,
            $content,
            $headers
        );
        wp_send_json_success();
    }
    
    public function update_description()
    {
        $id = ( !empty($_POST['id']) ? sanitize_text_field( $_POST['id'] ) : '' );
        $account_id = ( !empty($_POST['accountId']) ? sanitize_text_field( $_POST['accountId'] ) : '' );
        $description = ( !empty($_POST['description']) ? sanitize_text_field( $_POST['description'] ) : '' );
        $update_file = App::instance( $account_id )->update_description( $id, $description );
        wp_send_json_success( $update_file );
    }
    
    public function import_data()
    {
        $settings = ( !empty($_POST['settings']) ? igd_sanitize_array( $_POST['settings'] ) : [] );
        $shortcodes = ( !empty($_POST['shortcodes']) ? igd_sanitize_array( $_POST['shortcodes'] ) : [] );
        $user_files = ( !empty($_POST['user_files']) ? igd_sanitize_array( $_POST['user_files'] ) : [] );
        $events = ( !empty($_POST['events']) ? igd_sanitize_array( $_POST['events'] ) : [] );
        if ( !empty($settings) ) {
            update_option( 'igd_settings', $settings );
        }
        
        if ( !empty($shortcodes) ) {
            $shortcode_builder = Shortcode_Builder::instance();
            $shortcode_builder->delete_shortcode();
            foreach ( $shortcodes as $shortcode ) {
                $shortcode_builder->update_shortcode( $shortcode, true );
            }
        }
        
        if ( !empty($user_files) ) {
            foreach ( $user_files as $user_id => $files ) {
                update_user_option( $user_id, 'folders', $files );
            }
        }
        wp_send_json_success();
    }
    
    public function get_export_data( $request )
    {
        $type = ( !empty($_POST['$type']) ? sanitize_text_field( $_POST['$type'] ) : 'all' );
        $export_data = array();
        // Settings
        if ( 'all' == $type || 'settings' == $type ) {
            $export_data['settings'] = igd_get_settings();
        }
        // Shortcodes
        if ( 'all' == $type || 'shortcodes' == $type ) {
            $export_data['shortcodes'] = Shortcode_Builder::instance()->get_shortcodes();
        }
        // User Private Files
        
        if ( 'all' == $type || 'user_files' == $type ) {
            $user_files = array();
            $users = get_users();
            foreach ( $users as $user ) {
                $folders = get_user_option( 'folders', $user->ID );
                $user_files[$user->ID] = ( !empty($folders) ? $folders : array() );
            }
            $export_data['user_files'] = $user_files;
        }
        
        wp_send_json_success( $export_data );
    }
    
    public function create_doc()
    {
        $name = ( !empty($_POST['name']) ? sanitize_text_field( $_POST['name'] ) : 'Untitled' );
        $type = ( !empty($_POST['type']) ? sanitize_text_field( $_POST['type'] ) : 'doc' );
        $folder_id = ( !empty($_POST['folder_id']) ? sanitize_text_field( $_POST['folder_id'] ) : 'root' );
        $account_id = ( !empty($_POST['account_id']) ? sanitize_text_field( $_POST['account_id'] ) : '' );
        $mime_type = 'application/vnd.google-apps.document';
        
        if ( $type == 'sheet' ) {
            $mime_type = 'application/vnd.google-apps.spreadsheet';
        } elseif ( $type == 'slide' ) {
            $mime_type = 'application/vnd.google-apps.presentation';
        }
        
        try {
            $item = App::instance( $account_id )->getService()->files->create( new \IGDGoogle_Service_Drive_DriveFile( [
                'name'     => $name,
                'mimeType' => $mime_type,
                'parents'  => [ $folder_id ],
            ] ), [
                'fields' => '*',
            ] );
            // add new folder to cache
            $file = igd_file_map( $item, $account_id );
            Files::add_file( $file );
            // Insert log
            do_action(
                'igd_insert_log',
                'create',
                $file['id'],
                $account_id
            );
            wp_send_json_success( $file );
        } catch ( \Exception $e ) {
            wp_send_json_error( array(
                'error' => $e->getMessage(),
            ) );
        }
    }
    
    public function delete_files()
    {
        $file_ids = ( !empty($_POST['file_ids']) ? igd_sanitize_array( $_POST['file_ids'] ) : [] );
        $account_id = ( !empty($_POST['account_id']) ? sanitize_text_field( $_POST['account_id'] ) : '' );
        //send email notification
        if ( igd_get_settings( 'deleteNotifications', true ) ) {
            do_action(
                'igd_send_notification',
                'delete',
                $file_ids,
                $account_id
            );
        }
        wp_send_json_success( App::instance( $account_id )->delete( $file_ids, $account_id ) );
    }
    
    public function get_embed_content()
    {
        $data = ( !empty($_POST['data']) ? igd_sanitize_array( $_POST['data'] ) : [] );
        $content = igd_get_embed_content( $data );
        wp_send_json_success( $content );
    }
    
    public function search_files()
    {
        $folders = ( !empty($_POST['folders']) ? igd_sanitize_array( $_POST['folders'] ) : [] );
        $keyword = ( !empty($_POST['keyword']) ? sanitize_text_field( $_POST['keyword'] ) : '' );
        $account_id = ( !empty($_POST['accountId']) ? sanitize_text_field( $_POST['accountId'] ) : '' );
        $sort = ( !empty($_POST['sort']) ? igd_sanitize_array( $_POST['sort'] ) : [] );
        $full_text_search = ( isset( $_POST['fullTextSearch'] ) ? filter_var( $_POST['fullTextSearch'], FILTER_VALIDATE_BOOLEAN ) : true );
        $file_numbers = ( !empty($_POST['fileNumbers']) ? intval( $_POST['fileNumbers'] ) : 1000 );
        $filters = ( !empty($_POST['filters']) ? igd_sanitize_array( $_POST['filters'] ) : [] );
        $files = App::instance( $account_id )->get_search_files(
            $keyword,
            $folders,
            $sort,
            $full_text_search
        );
        if ( !empty($files['error']) ) {
            wp_send_json_success( $files );
        }
        // Filter files
        if ( !empty($files) && igd_should_filter_files( $filters ) ) {
            $files = array_values( array_filter( $files, function ( $item ) use( $filters ) {
                return igd_should_allow( $item, $filters );
            } ) );
        }
        // Handle maximum file to show
        if ( $file_numbers > 0 && count( $files ) > $file_numbers ) {
            $files = array_slice( $files, 0, $file_numbers );
        }
        $data = [
            'files' => array_values( $files ),
        ];
        wp_send_json_success( $data );
    }
    
    public function get_file()
    {
        $file_id = ( !empty($_POST['id']) ? sanitize_text_field( $_POST['id'] ) : '' );
        $account_id = ( !empty($_POST['accountId']) ? sanitize_text_field( $_POST['accountId'] ) : '' );
        $file = App::instance( $account_id )->get_file_by_id( $file_id );
        wp_send_json_success( $file );
    }
    
    public function get_shortcode_content()
    {
        $data = ( !empty($_POST['data']) ? igd_sanitize_array( $_POST['data'] ) : [] );
        $html = Shortcode::instance()->render_shortcode( [], $data );
        wp_send_json_success( $html );
    }
    
    public function get_users_data()
    {
        $search = ( !empty($_POST['search']) ? sanitize_text_field( $_POST['search'] ) : '' );
        $role = ( !empty($_POST['role']) ? sanitize_text_field( $_POST['role'] ) : '' );
        $page = ( !empty($_POST['page']) ? intval( $_POST['page'] ) : 1 );
        $number = ( !empty($_POST['number']) ? intval( $_POST['number'] ) : 999 );
        $offset = 10 * ($page - 1);
        $args = [
            'number' => $number,
            'role'   => ( 'all' != $role ? $role : '' ),
            'offset' => $offset,
            'search' => ( !empty($search) ? "*{$search}*" : '' ),
        ];
        $user_data = Private_Folders::instance()->get_user_data( $args );
        wp_send_json_success( $user_data );
    }
    
    public function update_user_folders()
    {
        $user_id = ( !empty($_POST['id']) ? intval( $_POST['id'] ) : 0 );
        $folders = ( !empty($_POST['folders']) ? igd_sanitize_array( $_POST['folders'] ) : [] );
        update_user_option( $user_id, 'folders', $folders );
        wp_send_json_success();
    }
    
    public function duplicate_shortcode()
    {
        $ids = ( !empty($_POST['ids']) ? igd_sanitize_array( $_POST['ids'] ) : [] );
        $data = [];
        if ( !empty($ids) ) {
            foreach ( $ids as $id ) {
                $data[] = Shortcode_Builder::instance()->duplicate_shortcode( $id );
            }
        }
        wp_send_json_success( $data );
    }
    
    public function update_shortcode()
    {
        $data = ( !empty($_POST['data']) ? json_decode( base64_decode( $_POST['data'] ), true ) : [] );
        $id = Shortcode_Builder::instance()->update_shortcode( $data );
        $data = [
            'id'         => $id,
            'config'     => $data,
            'title'      => $data['title'],
            'status'     => $data['status'],
            'created_at' => ( !empty($data['created_at']) ? $data['created_at'] : date( 'Y-m-d H:i:s', time() ) ),
        ];
        wp_send_json_success( $data );
    }
    
    public function get_files()
    {
        $posted = ( !empty($_POST['data']) ? igd_sanitize_array( $_POST['data'] ) : [] );
        $active_account = Account::instance()->get_active_account();
        if ( empty($active_account) ) {
            wp_send_json_error( __( 'No active account found', 'integrate-google-drive' ) );
        }
        $args = [
            'folder'      => [
            'id'         => $active_account['root_id'],
            'accountId'  => $active_account['id'],
            'pageNumber' => 1,
        ],
            'sort'        => [
            'sortBy'        => 'name',
            'sortDirection' => 'asc',
        ],
            'from_server' => false,
            'filters'     => [],
        ];
        // Merge request params
        $args = wp_parse_args( $posted, $args );
        $account_id = ( !empty($folder['accountId']) ? $folder['accountId'] : $active_account['id'] );
        $file_numbers = ( !empty($args['fileNumbers']) ? intval( $args['fileNumbers'] ) : 0 );
        $refresh = !empty($args['refresh']);
        $folder = $args['folder'];
        
        if ( !empty($args['from_server']) ) {
            $transient = get_transient( 'igd_latest_fetch_' . $folder['id'] );
            
            if ( $transient ) {
                $args['from_server'] = false;
            } else {
                set_transient( 'igd_latest_fetch_' . $folder['id'], true, 60 * MINUTE_IN_SECONDS );
            }
        
        }
        
        // Check if shortcut folder
        if ( !empty($folder['shortcutDetails']) ) {
            $args['folder']['id'] = $folder['shortcutDetails']['targetId'];
        }
        // Reset cache and get new files
        
        if ( $refresh ) {
            $refresh_args = $args;
            $refresh_args['folder']['pageNumber'] = 1;
            $refresh_args['from_server'] = true;
            $data = App::instance( $account_id )->get_files( $refresh_args );
        } else {
            $data = App::instance( $account_id )->get_files( $args );
        }
        
        // Handle maximum file to show
        if ( $file_numbers > 0 && count( $data['files'] ) > $file_numbers ) {
            $data['files'] = array_slice( $data['files'], 0, $file_numbers );
        }
        if ( !empty($data['error']) ) {
            wp_send_json_success( $data );
        }
        if ( empty($folder['pageNumber']) || $folder['pageNumber'] == 1 ) {
            $data['breadcrumbs'] = igd_get_breadcrumb( $folder );
        }
        wp_send_json_success( $data );
    }
    
    public function save_settings()
    {
        $settings = ( !empty($_POST['settings']) ? json_decode( base64_decode( $_POST['settings'] ), true ) : [] );
        update_option( 'igd_settings', $settings );
        wp_send_json_success();
    }
    
    public function delete_account()
    {
        $id = ( !empty($_POST['id']) ? sanitize_text_field( $_POST['id'] ) : '' );
        Account::instance()->delete_account( $id );
        wp_send_json_success();
    }
    
    public function switch_account()
    {
        $id = ( !empty($_POST['id']) ? sanitize_text_field( $_POST['id'] ) : '' );
        wp_send_json_success( Account::instance()->set_active_account( $id ) );
    }
    
    public function new_folder()
    {
        $folder_name = ( !empty($_POST['name']) ? sanitize_text_field( $_POST['name'] ) : '' );
        $parent_id = ( !empty($_POST['parent_id']) ? sanitize_text_field( $_POST['parent_id'] ) : '' );
        $account_id = ( !empty($_POST['account_id']) ? sanitize_text_field( $_POST['account_id'] ) : '' );
        $new_folder = App::instance( $account_id )->new_folder( $folder_name, $parent_id );
        wp_send_json_success( $new_folder );
    }
    
    public function copy_file()
    {
        $files = ( !empty($_POST['files']) ? igd_sanitize_array( $_POST['files'] ) : [] );
        $folder_id = ( !empty($_POST['folder_id']) ? sanitize_text_field( $_POST['folder_id'] ) : '' );
        $account_id = ( !empty($files[0]['accountId']) ? sanitize_text_field( $files[0]['accountId'] ) : '' );
        $copied_files = App::instance( $account_id )->copy( $files, $folder_id );
        wp_send_json_success( $copied_files );
    }
    
    public function rename_file()
    {
        $name = ( !empty($_POST['name']) ? sanitize_text_field( $_POST['name'] ) : '' );
        $file_id = ( !empty($_POST['id']) ? sanitize_text_field( $_POST['id'] ) : '' );
        $account_id = ( !empty($_POST['accountId']) ? sanitize_text_field( $_POST['accountId'] ) : '' );
        wp_send_json_success( App::instance( $account_id )->rename( $name, $file_id ) );
    }
    
    public function move_file()
    {
        $file_ids = ( !empty($_POST['file_ids']) ? igd_sanitize_array( $_POST['file_ids'] ) : '' );
        $folder_id = ( !empty($_POST['folder_id']) ? sanitize_text_field( $_POST['folder_id'] ) : '' );
        $account_id = ( !empty($_POST['account_id']) ? sanitize_text_field( $_POST['account_id'] ) : '' );
        wp_send_json_success( App::instance( $account_id )->move_file( $file_ids, $folder_id ) );
    }
    
    public function remove_upload_file()
    {
        $id = ( !empty($_POST['id']) ? $_POST['id'] : '' );
        $account_id = ( !empty($_POST['account_id']) ? $_POST['account_id'] : '' );
        $nonce = ( !empty($_POST['nonce']) ? $_POST['nonce'] : '' );
        $is_woocommerce = ( !empty($_POST['isWooCommerceUploader']) ? filter_var( $_POST['isWooCommerceUploader'], FILTER_VALIDATE_BOOLEAN ) : false );
        $product_id = ( !empty($_POST['wcProductId']) ? sanitize_text_field( $_POST['wcProductId'] ) : 0 );
        $order_id = ( !empty($_POST['wcOrderId']) ? intval( $_POST['wcOrderId'] ) : 0 );
        $item_id = ( !empty($_POST['wcItemId']) ? intval( $_POST['wcItemId'] ) : 0 );
        if ( !wp_verify_nonce( $nonce, 'wp_rest' ) ) {
            wp_send_json_error( [
                'success' => false,
                'message' => __( 'Invalid request', 'integrate-google-drive' ),
            ] );
        }
        //Remove uploaded files from Google Drive
        App::instance( $account_id )->delete( [ $id ], $account_id );
        //Remove uploaded files from woocommerce order meta-data
        if ( $is_woocommerce ) {
            
            if ( $item_id ) {
                $files = array_filter( wc_get_order_item_meta( $item_id, '_igd_files', false ) );
                
                if ( !empty($files) ) {
                    foreach ( $files as $key => $file ) {
                        if ( $file['id'] === $id ) {
                            unset( $files[$key] );
                        }
                    }
                    wc_update_order_item_meta( $item_id, '_igd_files', $files );
                }
            
            } else {
                //Remove uploaded files from wc session
                $files = WC()->session->get( 'igd_product_files_' . $product_id, [] );
                
                if ( !empty($files) ) {
                    foreach ( $files as $key => $file ) {
                        if ( $file['id'] === $id ) {
                            unset( $files[$key] );
                        }
                    }
                    WC()->session->set( 'igd_product_files_' . $product_id, $files );
                }
            
            }
        
        }
        wp_send_json_success( [
            'success' => true,
        ] );
    }
    
    public function upload_post_process()
    {
        $file = ( !empty($_POST['file']) ? igd_sanitize_array( $_POST['file'] ) : [] );
        $account_id = ( !empty($file['accountId']) ? sanitize_text_field( $file['accountId'] ) : '' );
        Uploader::instance( $account_id )->upload_post_process( $file );
        //Save uploaded files in the order meta-data for order-received page and my-account page
        $order_id = ( !empty($_POST['wcOrderId']) ? intval( $_POST['wcOrderId'] ) : false );
        $item_id = ( !empty($_POST['wcItemId']) ? intval( $_POST['wcItemId'] ) : false );
        $product_id = ( !empty($_POST['wcProductId']) ? intval( $_POST['wcProductId'] ) : false );
        
        if ( $item_id ) {
            if ( function_exists( 'wc_add_order_item_meta' ) ) {
                wc_add_order_item_meta( $item_id, '_igd_files', $file );
            }
        } elseif ( $product_id ) {
            //Save uploaded files in the session for checkout page
            
            if ( function_exists( 'WC' ) ) {
                $files = WC()->session->get( 'igd_product_files_' . $product_id, [] );
                $files[] = $file;
                WC()->session->set( 'igd_product_files_' . $product_id, $files );
            }
        
        }
    
    }
    
    public function hide_recommended_plugins()
    {
        update_option( "igd_hide_recommended_plugins", true );
        wp_send_json_success();
    }
    
    public function hide_review_notice()
    {
        update_option( 'igd_rating_notice', 'off' );
    }
    
    public function handle_review_feedback()
    {
        $feedback = ( !empty($_POST['feedback']) ? sanitize_textarea_field( $_POST['feedback'] ) : '' );
        
        if ( !empty($feedback) ) {
            $feedback = sanitize_textarea_field( $feedback );
            $website_url = get_bloginfo( 'url' );
            /* translators: %s: User feedback */
            $feedback = sprintf( __( 'Feedback: %s', 'integrate-google-drive' ), $feedback );
            $feedback .= '<br>';
            /* translators: %s: Website URL */
            $feedback .= sprintf( __( 'Website URL: %s', 'integrate-google-drive' ), $website_url );
            /* translators: %s: Plugin name */
            $subject = sprintf( __( 'Feedback for %s', 'integrate-google-drive' ), 'Integrate Google Drive' );
            $to = 'israilahmed5@gmail.com';
            $headers = [ 'Content-Type: text/html; charset=UTF-8', 'From: ' . get_bloginfo( 'name' ) . ' <' . get_bloginfo( 'admin_email' ) . '>' ];
            wp_mail(
                $to,
                $subject,
                $feedback,
                $headers
            );
            $this->hide_review_notice();
            wp_send_json_success();
        } else {
            wp_send_json_error();
        }
    
    }
    
    public function get_upload_url()
    {
        $data = ( !empty($_POST['data']) ? igd_sanitize_array( $_POST['data'] ) : [] );
        $account_id = ( !empty($data['accountId']) ? sanitize_text_field( $data['accountId'] ) : '' );
        $url = Uploader::instance( $account_id )->get_resume_url( $data );
        if ( isset( $url['error'] ) ) {
            wp_send_json_error( $url );
        }
        wp_send_json_success( $url );
    }
    
    public function get_share_link()
    {
        $file = ( !empty($_POST['file']) ? igd_sanitize_array( $_POST['file'] ) : [] );
        $embed_link = igd_get_embed_url( $file );
        if ( !$embed_link ) {
            wp_send_json_error( [
                'message' => __( 'Something went wrong! Preview is not available', 'integrate-google-drive' ),
            ] );
        }
        $view_link = str_replace( [ 'edit?usp=drivesdk', 'preview?rm=minimal', 'preview' ], 'view', $embed_link );
        // Insert log
        do_action(
            'igd_insert_log',
            'share',
            $file['id'],
            $file['accountId']
        );
        wp_send_json_success( [
            'embedLink' => $embed_link,
            'viewLink'  => $view_link,
        ] );
    }
    
    public function clear_cache()
    {
        $nonce = ( !empty($_POST['nonce']) ? sanitize_text_field( $_POST['nonce'] ) : '' );
        if ( !wp_verify_nonce( $nonce, 'wp_rest' ) ) {
            wp_send_json_error( [
                'message' => __( 'Invalid nonce', 'integrate-google-drive' ),
            ] );
        }
        igd_delete_cache();
        wp_send_json_success();
    }
    
    public function get_shortcodes()
    {
        $page = ( !empty($_POST['page']) ? intval( $_POST['page'] ) : 1 );
        $per_page = ( !empty($_POST['per_page']) ? intval( $_POST['per_page'] ) : 10 );
        $args = [];
        $args['offset'] = 10 * ($page - 1);
        $args['limit'] = $per_page;
        $shortcodes = Shortcode_Builder::instance()->get_shortcodes( $args );
        $return_data = [];
        $formatted = [];
        if ( !empty($shortcodes) ) {
            foreach ( $shortcodes as $shortcode ) {
                $shortcode->config = maybe_unserialize( $shortcode->config );
                $shortcode->locations = ( !empty($shortcode->locations) ? array_values( maybe_unserialize( $shortcode->locations ) ) : [] );
                $formatted[] = $shortcode;
            }
        }
        $return_data['shortcodes'] = $formatted;
        $return_data['total'] = Shortcode_Builder::instance()->get_shortcodes_count();
        wp_send_json_success( $return_data );
    }
    
    public function delete_shortcode()
    {
        $id = ( !empty($_POST['id']) ? intval( $_POST['id'] ) : '' );
        $nonce = ( !empty($_POST['nonce']) ? sanitize_text_field( $_POST['nonce'] ) : '' );
        if ( !wp_verify_nonce( $nonce, 'wp_rest' ) ) {
            wp_send_json_error( [
                'message' => __( 'Invalid nonce', 'integrate-google-drive' ),
            ] );
        }
        Shortcode_Builder::instance()->delete_shortcode( $id );
        wp_send_json_success();
    }
    
    public function preview()
    {
        $file_id = sanitize_text_field( $_REQUEST['file_id'] );
        $account_id = sanitize_text_field( $_REQUEST['account_id'] );
        $popout = true;
        if ( !empty($_REQUEST['popout']) ) {
            $popout = filter_var( $_REQUEST['popout'], FILTER_VALIDATE_BOOLEAN );
        }
        if ( !empty($_REQUEST['direct_link']) ) {
            $popout = false;
        }
        $download = true;
        if ( !empty($_REQUEST['download']) ) {
            $download = filter_var( $_REQUEST['download'], FILTER_VALIDATE_BOOLEAN );
        }
        $app = App::instance( $account_id );
        $file = $app->get_file_by_id( $file_id );
        $preview_url = igd_get_embed_url(
            $file,
            false,
            false,
            true,
            $popout,
            $download
        );
        
        if ( !$preview_url ) {
            _e( 'Something went wrong! Preview is not available', 'integrate-google-drive' );
            die;
        }
        
        do_action(
            'igd_insert_log',
            'preview',
            $file_id,
            $account_id
        );
        header( 'Location: ' . $preview_url );
        die;
    }
    
    public function download()
    {
        $account_id = ( !empty($_REQUEST['accountId']) ? sanitize_text_field( $_REQUEST['accountId'] ) : '' );
        $file_id = ( !empty($_REQUEST['id']) ? sanitize_text_field( $_REQUEST['id'] ) : '' );
        $mimetype = ( !empty($_REQUEST['mimetype']) ? sanitize_text_field( $_REQUEST['mimetype'] ) : 'default' );
        try {
            $file = App::instance( $account_id )->get_file_by_id( $file_id );
        } catch ( \Exception $e ) {
            _e( 'Something went wrong! File may be deleted or moved to trash.', 'integrate-google-drive' );
            die;
        }
        //insert download log
        do_action(
            'igd_insert_log',
            'download',
            $file_id,
            $account_id
        );
        //send email notification
        if ( igd_get_settings( 'downloadNotifications', true ) ) {
            do_action(
                'igd_send_notification',
                'download',
                [ $file_id ],
                $account_id
            );
        }
        //check if shortcut file then get the original file
        if ( igd_is_shortcut( $file['type'] ) ) {
            $file = App::instance( $account_id )->get_file_by_id( $file['shortcutDetails']['targetId'] );
        }
        // get the last-modified-date of this very file
        $updated_date = $file['updated'];
        // get a unique hash of this file (etag)
        $etag_file = md5( $updated_date );
        // get the HTTP_IF_MODIFIED_SINCE header if set
        $if_modified_since = ( isset( $_SERVER['HTTP_IF_MODIFIED_SINCE'] ) ? sanitize_text_field( $_SERVER['HTTP_IF_MODIFIED_SINCE'] ) : false );
        // get the HTTP_IF_NONE_MATCH header if set (etag: unique file hash)
        $etag_header = ( isset( $_SERVER['HTTP_IF_NONE_MATCH'] ) ? trim( sanitize_text_field( $_SERVER['HTTP_IF_NONE_MATCH'] ) ) : false );
        header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s', strtotime( $updated_date ) ) . ' GMT' );
        header( "Etag: {$etag_file}" );
        header( 'Expires: ' . gmdate( 'D, d M Y H:i:s', time() + 60 * 5 ) . ' GMT' );
        header( 'Cache-Control: must-revalidate' );
        
        if ( $if_modified_since && $etag_header && strpos( $if_modified_since, $etag_file ) !== false ) {
            header( 'HTTP/1.1 304 Not Modified' );
            exit;
        }
        
        $download = Download::instance( $file, false, $mimetype );
        $download->start_download();
        exit;
    }
    
    public function get_preview_thumbnail()
    {
        $id = sanitize_text_field( $_REQUEST['id'] );
        $account_id = ( !empty($_REQUEST['accountId']) ? sanitize_text_field( $_REQUEST['accountId'] ) : '' );
        $size = sanitize_key( $_REQUEST['size'] );
        $file = App::instance( $account_id )->get_file_by_id( $id );
        
        if ( 'large' === $size ) {
            $thumbnail_attributes = '=s0';
        } else {
            
            if ( 'gallery' === $size ) {
                $thumbnail_attributes = '=h300-nu-iv1';
            } else {
                $thumbnail_attributes = '=w200-h190-p-k-nu-iv1';
            }
        
        }
        
        $thumbnail_file = $id . $thumbnail_attributes . '.png';
        
        if ( file_exists( IGD_CACHE_DIR . '/thumbnails/' . $thumbnail_file ) && filemtime( IGD_CACHE_DIR . '/thumbnails/' . $thumbnail_file ) === strtotime( $file['updated'] ) ) {
            $url = IGD_CACHE_URL . '/thumbnails/' . $thumbnail_file;
            $img_info = getimagesize( $url );
            header( "Content-type: {$img_info['mime']}" );
            readfile( $url );
            exit;
        }
        
        $download_link = "https://lh3.google.com/u/0/d/{$id}{$thumbnail_attributes}";
        try {
            $client = Client::instance( $account_id )->get_client();
            $request = new \IGDGoogle_Http_Request( $download_link, 'GET' );
            $client->getIo()->setOptions( [
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_FOLLOWLOCATION => true,
            ] );
            $httpRequest = $client->getAuth()->authenticatedRequest( $request );
            // Save the thumbnail locally
            $headers = $httpRequest->getResponseHeaders();
            if ( !stristr( $headers['content-type'], 'image' ) ) {
                return;
            }
            if ( !file_exists( IGD_CACHE_DIR . '/thumbnails' ) ) {
                @mkdir( IGD_CACHE_DIR . '/thumbnails', 0755 );
            }
            if ( !is_writable( IGD_CACHE_DIR . '/thumbnails' ) ) {
                @chmod( IGD_CACHE_DIR . '/thumbnails', 0755 );
            }
            @file_put_contents( IGD_CACHE_DIR . '/thumbnails/' . $thumbnail_file, $httpRequest->getResponseBody() );
            //New SDK: $response->getBody()
            touch( IGD_CACHE_DIR . '/thumbnails/' . $thumbnail_file, strtotime( $file['updated'] ) );
            echo  $httpRequest->getResponseBody() ;
        } catch ( \Exception $e ) {
            echo  esc_html( $e->getMessage() ) ;
        }
        exit;
    }
    
    public function get_download_status()
    {
        $id = ( !empty($_REQUEST['id']) ? sanitize_text_field( $_REQUEST['id'] ) : '' );
        $status = get_transient( 'igd_download_status_' . $id );
        wp_send_json_success( $status );
    }
    
    public function stream_content()
    {
        $file_id = ( !empty($_REQUEST['id']) ? sanitize_text_field( $_REQUEST['id'] ) : '' );
        $account_id = ( !empty($_REQUEST['accountId']) ? sanitize_text_field( $_REQUEST['accountId'] ) : '' );
        $app = App::instance( $account_id );
        $file = $app->get_file_by_id( $file_id );
        //check if shortcut file then get the original file
        if ( igd_is_shortcut( $file['type'] ) ) {
            $file = $app->get_file_by_id( $file['shortcutDetails']['targetId'] );
        }
        do_action(
            'igd_insert_log',
            'stream',
            $file['id'],
            $account_id
        );
        Download::instance( $file, true )->start_download();
        exit;
    }
    
    public function download_zip()
    {
        $file_ids = ( !empty($_REQUEST['file_ids']) ? json_decode( base64_decode( sanitize_text_field( $_REQUEST['file_ids'] ) ) ) : [] );
        $request_id = ( !empty($_REQUEST['id']) ? sanitize_text_field( $_REQUEST['id'] ) : '' );
        $account_id = ( !empty($_REQUEST['accountId']) ? sanitize_text_field( $_REQUEST['accountId'] ) : '' );
        //send email notification
        if ( igd_get_settings( 'downloadNotifications', true ) ) {
            do_action(
                'igd_send_notification',
                'download',
                $file_ids,
                $account_id
            );
        }
        igd_download_zip( $file_ids, $request_id, $account_id );
        exit;
    }
    
    public static function instance()
    {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

}
Ajax::instance();