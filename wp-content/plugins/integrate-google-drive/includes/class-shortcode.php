<?php

namespace IGD;

defined( 'ABSPATH' ) || exit;
class Shortcode
{
    /**
     * @var null
     */
    protected static  $instance = null ;
    private  $type = null ;
    private  $data ;
    public function __construct()
    {
        add_shortcode( 'integrate_google_drive', [ $this, 'render_shortcode' ] );
    }
    
    /**
     * @param array $atts
     * @param null $data
     *
     * @return false|string|void
     */
    public function render_shortcode( $atts = array(), $data = null )
    {
        $this->fetch_data( $atts, $data );
        // If the shortcode is not found, return nothing
        if ( empty($this->data) ) {
            return;
        }
        if ( $this->check_status() ) {
            return;
        }
        wp_enqueue_style( 'igd-frontend' );
        if ( !$this->check_should_show() ) {
            return $this->get_access_denied_placeholder();
        }
        if ( !$this->check_use_private_folders() ) {
            return $this->get_access_denied_placeholder();
        }
        $this->check_file_actions_permissions();
        $this->get_initial_search_term();
        $this->enqueue_scripts();
        $this->set_filters();
        $this->set_notifications();
        $this->check_folders();
        $this->set_account();
        return $this->generate_html();
    }
    
    private function fetch_data( $atts, $data )
    {
        // Get the shortcode ID from attributes
        if ( empty($data) ) {
            
            if ( !empty($atts['data']) ) {
                $data = json_decode( base64_decode( $atts['data'] ), true );
            } elseif ( !empty($atts['id']) ) {
                $id = intval( $atts['id'] );
                
                if ( $id ) {
                    $shortcode = Shortcode_Builder::instance()->get_shortcode( $id );
                    if ( !empty($shortcode) ) {
                        $data = unserialize( $shortcode->config );
                    }
                }
            
            }
        
        }
        $this->type = ( !empty($data['type']) ? $data['type'] : '' );
        $this->data = $data;
    }
    
    private function check_status()
    {
        $status = ( !empty($this->data['status']) ? $this->data['status'] : 'on' );
        // Check shortcode status
        if ( 'off' == $status ) {
            return true;
        }
        return false;
    }
    
    private function check_file_actions_permissions()
    {
        // Check file actions Permissions
        
        if ( in_array( $this->type, [
            'browser',
            'gallery',
            'media',
            'search',
            'slider'
        ] ) ) {
            // Preview
            $this->data['preview'] = !isset( $this->data['preview'] ) || !empty($this->data['preview']) && $this->check_permissions( 'preview' );
            // Download
            $this->data['download'] = !empty($this->data['download']) && $this->check_permissions( 'download' );
            // Delete
            $this->data['canDelete'] = !empty($this->data['canDelete']) && $this->check_permissions( 'canDelete' );
            // Rename
            $this->data['rename'] = !empty($this->data['rename']) && $this->check_permissions( 'rename' );
            // Upload
            $this->data['upload'] = !empty($this->data['upload']) && $this->check_permissions( 'upload' );
            // New Folder
            $this->data['newFolder'] = !empty($this->data['newFolder']) && $this->check_permissions( 'newFolder' );
            // moveCopy
            $this->data['moveCopy'] = !empty($this->data['moveCopy']) && $this->check_permissions( 'moveCopy' );
            // Share
            $this->data['allowShare'] = !empty($this->data['allowShare']) && $this->check_permissions( 'allowShare' );
            // Search
            $this->data['allowSearch'] = 'search' == $this->type || !empty($this->data['allowSearch']) && $this->check_permissions( 'allowSearch' );
            // Create
            $this->data['createDoc'] = !empty($this->data['createDoc']) && $this->check_permissions( 'createDoc' );
            // Edit
            $this->data['edit'] = !empty($this->data['edit']) && $this->check_permissions( 'edit' );
            // Direct Link
            $this->data['directLink'] = !empty($this->data['directLink']) && $this->check_permissions( 'directLink' );
            // Details
            $this->data['details'] = !empty($this->data['details']) && $this->check_permissions( 'details' );
            // Details
            $this->data['comment'] = !empty($this->data['comment']) && $this->check_permissions( 'comment' );
            // photoProof
            $this->data['photoProof'] = !empty($this->data['photoProof']) && $this->check_permissions( 'photoProof' );
        }
    
    }
    
    public function check_permissions( $permission_type )
    {
        $typeUserKeyMap = [
            'preview'     => 'previewUsers',
            'download'    => 'downloadUsers',
            'upload'      => 'uploadUsers',
            'allowShare'  => 'shareUsers',
            'createDoc'   => 'createDocUsers',
            'edit'        => 'editUsers',
            'directLink'  => 'directLinkUsers',
            'details'     => 'detailsUsers',
            'allowSearch' => 'searchUsers',
            'canDelete'   => 'deleteUsers',
            'rename'      => 'renameUsers',
            'moveCopy'    => 'moveCopyUsers',
            'newFolder'   => 'newFolderUsers',
            'comment'     => 'commentUsers',
            'photoProof'  => 'photoProofUsers',
        ];
        $userKey = ( isset( $typeUserKeyMap[$permission_type] ) ? $typeUserKeyMap[$permission_type] : null );
        $users = ( $userKey && isset( $this->data[$userKey] ) ? $this->data[$userKey] : [ 'everyone' ] );
        
        if ( in_array( 'everyone', $users ) ) {
            return true;
        } elseif ( is_user_logged_in() ) {
            $current_user = wp_get_current_user();
            if ( !empty(array_intersect( $current_user->roles, $users )) ) {
                // If matches roles
                return true;
            }
            if ( in_array( $current_user->ID, $users ) ) {
                // If current user_id
                return true;
            }
        }
        
        return false;
    }
    
    private function get_initial_search_term()
    {
        
        if ( !empty($this->data['allowSearch']) && !empty($this->data['initialSearchTerm']) && strpos( $this->data['initialSearchTerm'], '%' ) !== false ) {
            $search_template = $this->data['initialSearchTerm'];
            $tag_args = [
                'name' => $search_template,
            ];
            // Add user data
            if ( igd_contains_tags( 'user', $search_template ) ) {
                if ( is_user_logged_in() ) {
                    $tag_args['user'] = get_userdata( get_current_user_id() );
                }
            }
            // Add the current post to the args
            
            if ( igd_contains_tags( 'post', $search_template ) ) {
                global  $post ;
                
                if ( !empty($post) ) {
                    $tag_args['post'] = $post;
                    // if post is a product get the product
                    
                    if ( $post->post_type == 'product' ) {
                        $product = wc_get_product( $post->ID );
                        if ( !empty($product) ) {
                            $tag_args['wc_product'] = $product;
                        }
                    }
                
                }
            
            }
            
            $this->data['initialSearchTerm'] = igd_replace_template_tags( $tag_args );
        }
    
    }
    
    private function check_use_private_folders()
    {
        return true;
    }
    
    protected function check_folders()
    {
        
        if ( in_array( $this->type, [
            'browser',
            'gallery',
            'media',
            'slider',
            'embed'
        ] ) && !empty($this->data['folders']) ) {
            $is_single_folder = count( $this->data['folders'] ) == 1 && igd_is_dir( reset( $this->data['folders'] ) );
            
            if ( $is_single_folder ) {
                $this->process_single_folder();
            } else {
                $this->get_files_from_server();
            }
        
        }
    
    }
    
    public function process_single_folder()
    {
        $folder = reset( $this->data['folders'] );
        $this->data['initParentFolder'] = $folder;
        
        if ( is_array( $folder ) ) {
            $folder_id = $folder['id'];
            $args = [
                'folder'      => $folder,
                'sort'        => ( !empty($this->data['sort']) ? $this->data['sort'] : [] ),
                'fileNumbers' => ( !empty($this->data['fileNumbers']) ? $this->data['fileNumbers'] : -1 ),
                'filters'     => ( !empty($this->data['filters']) ? $this->data['filters'] : [] ),
            ];
            // lazy load items
            if ( in_array( $this->type, [ 'browser', 'gallery' ] ) ) {
                if ( !isset( $this->data['lazyLoad'] ) || !empty($this->data['lazyLoad']) ) {
                    $args['limit'] = ( !empty($this->data['lazyLoadNumber']) ? $this->data['lazyLoadNumber'] : 100 );
                }
            }
            // Set from server true only for browser and gallery
            
            if ( in_array( $this->type, [ 'browser', 'gallery' ] ) ) {
                $args['from_server'] = true;
                $transient = get_transient( 'igd_latest_fetch_' . $folder_id );
                
                if ( $transient ) {
                    $args['from_server'] = false;
                } else {
                    set_transient( 'igd_latest_fetch_' . $folder_id, true, 60 * MINUTE_IN_SECONDS );
                }
            
            }
            
            // Fetch files
            $account_id = ( !empty($folder['accountId']) ? $folder['accountId'] : '' );
            $files_data = App::instance( $account_id )->get_files( $args );
            if ( isset( $files_data['files'] ) ) {
                $this->data['folders'] = array_values( $files_data['files'] );
            }
            // Update the arguments for the next iteration
            $pageNumber = ( !empty($files_data['nextPageNumber']) ? $files_data['nextPageNumber'] : 0 );
            $this->data['initParentFolder']['pageNumber'] = $pageNumber;
        }
    
    }
    
    public function get_files_from_server()
    {
        //First of all filters the folders
        if ( igd_should_filter_files( $this->data['filters'] ) ) {
            $this->check_filters();
        }
        $folder_ids = array_column( $this->data['folders'], 'id' );
        $cache_key = 'igd_latest_fetch_' . md5( implode( '', $folder_ids ) );
        
        if ( !get_transient( $cache_key ) ) {
            set_transient( $cache_key, true, 60 * MINUTE_IN_SECONDS );
            //Get files from server
            $account_id = reset( $this->data['folders'] )['accountId'];
            $app = App::instance( $account_id );
            $client = $app->client;
            $service = $app->getService();
            $batch = new \IGDGoogle_Http_Batch( $client );
            $client->setUseBatch( true );
            foreach ( $this->data['folders'] as $key => $folder ) {
                $request = $service->files->get( $folder['id'], [
                    'supportsAllDrives' => true,
                    'fields'            => $app->file_fields,
                ] );
                $batch->add( $request, ( $key ?: '-1' ) );
            }
            $batch_result = $batch->execute();
            $client->setUseBatch( false );
            if ( !empty($batch_result) ) {
                foreach ( $batch_result as $key => $file ) {
                    $index = max( 0, str_replace( 'response-', '', $key ) );
                    
                    if ( is_a( $file, 'IGDGoogle_Service_Exception' ) || is_a( $file, 'IGDGoogle_Exception' ) ) {
                        unset( $this->data['folders'][$index] );
                        continue;
                    }
                    
                    
                    if ( isset( $this->data['fileNumbers'] ) && $this->data['fileNumbers'] > 0 && count( $this->data['folders'] ) > $this->data['fileNumbers'] ) {
                        unset( $this->data['folders'][$index] );
                        continue;
                    }
                    
                    $file = igd_file_map( $file, $account_id );
                    $this->data['folders'][$index] = $file;
                }
            }
            // Get files from folders if slider and premium user
            if ( igd_fs()->can_use_premium_code__premium_only() && $this->type == 'slider' && !empty($this->data['folders']) ) {
                $this->get_files_from_slider_folders( $app );
            }
        }
        
        if ( isset( $this->data['fileNumbers'] ) && $this->data['fileNumbers'] > 0 && count( $this->data['folders'] ) > $this->data['fileNumbers'] ) {
            $this->data['folders'] = array_values( array_slice( $this->data['folders'], 0, $this->data['fileNumbers'] ) );
        }
    }
    
    private function get_files_from_slider_folders( $app )
    {
        $folder_ids = array_column( array_filter( $this->data['folders'], 'igd_is_dir' ), 'id' );
        $args = [
            'q' => "trashed=false and ('" . implode( "' in parents or '", $folder_ids ) . "' in parents) ",
        ];
        $files_data = $app->get_files( $args );
        $files = $files_data['files'];
        
        if ( !empty($files) ) {
            $filtered_files = array_filter( $files, function ( $file ) {
                return !igd_is_dir( $file ) && !empty($file['thumbnailLink']);
            } );
            $this->data['folders'] = array_values( array_merge( $this->data['folders'], $filtered_files ) );
        }
    
    }
    
    private function set_filters()
    {
        $filters = [
            'allowExtensions'       => ( !empty($this->data['allowExtensions']) ? str_replace( ' ', '', $this->data['allowExtensions'] ) : '' ),
            'allowAllExtensions'    => ( !empty($this->data['allowAllExtensions']) ? $this->data['allowAllExtensions'] : false ),
            'allowExceptExtensions' => ( !empty($this->data['allowExceptExtensions']) ? str_replace( ' ', '', $this->data['allowExceptExtensions'] ) : '' ),
            'allowNames'            => ( !empty($this->data['allowNames']) ? $this->data['allowNames'] : '' ),
            'allowAllNames'         => ( !empty($this->data['allowAllNames']) ? $this->data['allowAllNames'] : '' ),
            'allowExceptNames'      => ( !empty($this->data['allowExceptNames']) ? $this->data['allowExceptNames'] : '' ),
            'nameFilterOptions'     => ( isset( $this->data['nameFilterOptions'] ) ? $this->data['nameFilterOptions'] : [ 'files' ] ),
            'showFiles'             => ( isset( $this->data['showFiles'] ) ? $this->data['showFiles'] : true ),
            'showFolders'           => ( isset( $this->data['showFolders'] ) ? $this->data['showFolders'] : true ),
        ];
        if ( 'gallery' == $this->type ) {
            $filters['isGallery'] = true;
        }
        if ( 'media' == $this->type ) {
            $filters['isMedia'] = true;
        }
        $this->data['filters'] = $filters;
    }
    
    private function set_notifications()
    {
        if ( empty($this->data['enableNotification']) ) {
            return;
        }
        $notifications = [
            'downloadNotification'        => ( isset( $this->data['downloadNotification'] ) ? $this->data['downloadNotification'] : true ),
            'uploadNotification'          => ( isset( $this->data['uploadNotification'] ) ? $this->data['uploadNotification'] : true ),
            'deleteNotification'          => ( isset( $this->data['deleteNotification'] ) ? $this->data['deleteNotification'] : true ),
            'playNotification'            => ( isset( $this->data['playNotification'] ) ? $this->data['playNotification'] : 'media' == $this->type ),
            'searchNotification'          => ( isset( $this->data['searchNotification'] ) ? $this->data['searchNotification'] : 'search' == $this->type ),
            'viewNotification'            => ( isset( $this->data['viewNotification'] ) ? $this->data['viewNotification'] : true ),
            'notificationEmail'           => ( !empty($this->data['notificationEmail']) ? $this->data['notificationEmail'] : '%admin_email%' ),
            'skipCurrentUserNotification' => ( isset( $this->data['skipCurrentUserNotification'] ) ? $this->data['skipCurrentUserNotification'] : true ),
        ];
        $this->data['notifications'] = $notifications;
    }
    
    private function check_filters()
    {
        
        if ( in_array( $this->type, [
            'browser',
            'gallery',
            'media',
            'search',
            'slider',
            'embed'
        ] ) && !empty($this->data['folders']) ) {
            $filters = $this->data['filters'];
            $this->data['folders'] = array_values( array_filter( $this->data['folders'], function ( $item ) use( $filters ) {
                return igd_should_allow( $item, $filters );
            } ) );
        }
    
    }
    
    protected function set_account()
    {
        // Set active account
        
        if ( !empty($this->data['folders']) ) {
            $folder = reset( $this->data['folders'] );
            $this->data['account'] = Account::instance()->get_accounts( $folder['accountId'] );
        }
    
    }
    
    private function generate_html()
    {
        $width = ( !empty($this->data['moduleWidth']) ? $this->data['moduleWidth'] : '100%' );
        $height = ( !empty($this->data['moduleHeight']) ? $this->data['moduleHeight'] : '' );
        switch ( $this->type ) {
            case 'embed':
                $html = igd_get_embed_content( $this->data );
                break;
            case 'download':
                $html = $this->get_download_links_html();
                break;
            case 'view':
                $html = $this->get_view_links_html();
                break;
            default:
                ob_start();
                ?>
                <div class="igd igd-shortcode-wrap igd-shortcode-<?php 
                echo  esc_attr( $this->type ) ;
                ?>"
                     data-shortcode-data="<?php 
                echo  base64_encode( json_encode( $this->data ) ) ;
                ?>"
                     style="width: <?php 
                echo  esc_attr( $width ) ;
                ?>;  <?php 
                echo  ( !empty($height) ? esc_attr( 'height:' . $height ) . ';' : '' ) ;
                ?>"
                ></div>
				<?php 
                $html = ob_get_clean();
                break;
        }
        return $html;
    }
    
    /**
     * Check if the shortcode should be shown.
     *
     * @param array $this ->>data
     *
     * @return bool
     */
    public function check_should_show()
    {
        $display_for = ( isset( $this->data['displayFor'] ) ? $this->data['displayFor'] : 'everyone' );
        if ( $display_for === 'everyone' ) {
            return true;
        }
        if ( $display_for !== 'loggedIn' || !is_user_logged_in() ) {
            return false;
        }
        $display_users = ( isset( $this->data['displayUsers'] ) ? $this->data['displayUsers'] : [] );
        $display_everyone = filter_var( ( isset( $this->data['displayEveryone'] ) ? $this->data['displayEveryone'] : false ), FILTER_VALIDATE_BOOLEAN );
        $display_except = ( isset( $this->data['displayExcept'] ) ? $this->data['displayExcept'] : [] );
        $current_user = wp_get_current_user();
        $user_roles = array_filter( $display_users, 'is_string' );
        $except_user_roles = array_filter( $display_except, 'is_string' );
        // if display_everyone is true and the user is not in the exception list
        if ( $display_everyone && !in_array( $current_user->ID, $display_except ) && empty(array_intersect( $current_user->roles, $except_user_roles )) ) {
            return true;
        }
        // if the users list contains 'everyone' or the user's role or the user's ID
        if ( in_array( 'everyone', $user_roles ) || !empty(array_intersect( $current_user->roles, $user_roles )) || in_array( $current_user->ID, $display_users ) ) {
            return true;
        }
        // if no users specified and either display_everyone is true with no exceptions or display_everyone is false
        if ( empty($display_users) && ($display_everyone && empty($except_users) || !$display_everyone) ) {
            return true;
        }
        return false;
    }
    
    public function get_access_denied_placeholder()
    {
        $default_message = '<img width="100" src="' . IGD_ASSETS . '/images/access-denied.png" >
<h3 class="placeholder-title">Access Denied</h3>
<p class="placeholder-description">We\'re sorry, but your account does not currently have access to this content. To gain access, please contact the site administrator who can assist in linking your account to the appropriate content. Thank you.</p>';
        $message = igd_get_settings( 'accessDeniedMessage', $default_message );
        return sprintf( '<div class="igd-access-denied-placeholder">%s</div>', $message );
    }
    
    public function enqueue_scripts()
    {
        wp_enqueue_script( 'igd-frontend' );
    }
    
    public function get_view_links_html()
    {
        $items = ( isset( $this->data['folders'] ) ? $this->data['folders'] : [] );
        $html = '';
        if ( empty($items) ) {
            return $html;
        }
        $should_send_notification = !empty($this->data['notifications']) && !empty($this->data['notifications']['viewNotification']) && !empty($this->data['notifications']['notificationEmail']);
        $notification_data_html = ( $should_send_notification ? ' data-notification-email="' . esc_attr( $this->data['notifications']['notificationEmail'] ) . '" data-skip-current-user-notification="' . esc_attr( $this->data['notifications']['skipCurrentUserNotification'] ) . '"' : '' );
        foreach ( $items as $item ) {
            $name = $item['name'];
            $view_link = $item['webViewLink'];
            $file_data_html = ( !empty($should_send_notification) ? ' data-id="' . esc_attr( $item['id'] ) . '" data-account-id="' . esc_attr( $item['accountId'] ) . '"' : '' );
            $data_html = $notification_data_html . $file_data_html;
            $html .= sprintf(
                '<a href="%1$s" class="igd-view-link" target="_blank" %2$s>%3$s</a>',
                $view_link,
                $data_html,
                $name
            );
        }
        return $html;
    }
    
    public function get_download_links_html()
    {
        $items = ( isset( $this->data['folders'] ) ? $this->data['folders'] : [] );
        $html = '';
        if ( empty($items) ) {
            return $html;
        }
        $should_send_notification = !empty($this->data['notifications']) && !empty($this->data['notifications']['viewNotification']) && !empty($this->data['notifications']['notificationEmail']);
        $notification_data_html = ( $should_send_notification ? ' data-notification-email="' . esc_attr( $this->data['notifications']['notificationEmail'] ) . '" data-skip-current-user-notification="' . esc_attr( $this->data['notifications']['skipCurrentUserNotification'] ) . '"' : '' );
        foreach ( $items as $item ) {
            $id = $item['id'];
            $account_id = $item['accountId'];
            $name = $item['name'];
            $download_link = ( igd_is_dir( $item ) ? admin_url( "admin-ajax.php?action=igd_download_zip&file_ids=" . base64_encode( json_encode( [ $id ] ) ) . "&accountId={$account_id}" ) : admin_url( "admin-ajax.php?action=igd_download&id={$id}&accountId={$account_id}" ) );
            $file_data_html = ( !empty($should_send_notification) ? ' data-id="' . esc_attr( $id ) . '" data-account-id="' . esc_attr( $account_id ) . '"' : '' );
            $data_html = $notification_data_html . $file_data_html;
            $html .= sprintf(
                '<a href="%1$s" class="igd-download-link" %2$s>%3$s</a>',
                $download_link,
                $data_html,
                $name
            );
        }
        return $html;
    }
    
    /**
     * @return Shortcode|null
     */
    public static function instance()
    {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

}
Shortcode::instance();