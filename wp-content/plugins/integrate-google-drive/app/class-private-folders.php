<?php

namespace IGD;

class Private_Folders {
	/**
	 * @var null
	 */
	protected static $instance = null;

	public function __construct() {
	}

	public function create_user_folder( $user_id = null, $data = [] ) {

		if ( ! $user_id ) {
			$user_id = get_current_user_id();
		}

		$user = get_userdata( $user_id );
		if ( ! $user ) {
			return [];
		}

		@set_time_limit( 60 );

		if ( ! empty( $data ) ) {
			$name_template   = ! empty( $data['nameTemplate'] ) ? $data['nameTemplate'] : '%user_login% (%user_email%)';
			$parent_folder   = ! empty( $data['parentFolder'] ) ? $data['parentFolder'] : [];
			$template_folder = ! empty( $data['templateFolder'] ) ? $data['templateFolder'] : null;
		} else {
			$name_template   = igd_get_settings( 'nameTemplate', '%user_login% (%user_email%)' );
			$parent_folder   = igd_get_settings( 'parentFolder' );
			$template_folder = igd_get_settings( 'templateFolder' );
		}

		if ( empty( $parent_folder ) ) {
			$parent_folder = [
				'id'        => Account::instance()->get_active_account()['root_id'],
				'accountId' => Account::instance()->get_active_account()['id'],
			];
		}

		$data = [
			'parent' => $parent_folder,
			'name'   => $name_template,
			'user'   => $user,
		];

		if ( igd_contains_tags( 'post', $name_template ) ) {
			$data['post'] = get_post();
		}

		$user_folder = $this->create_folder( $data );

		update_user_option( $user_id, 'folders', [ $user_folder ] );

		// Check if the template folder should be copied to the user folder
		if ( ! empty( $template_folder ) ) {
			// Check if the template folder is the same as the parent folder or the template folder and the parent folder not in the same account
			if ( ( $template_folder['id'] != $parent_folder['id'] ) || ( $template_folder['accountId'] == $parent_folder['accountId'] ) ) {
				try {
					$app = App::instance( $parent_folder['accountId'] );
					$app->copy_folder( $template_folder, $user_folder['id'] );
				} catch ( \Exception $e ) {
					error_log( $e->getMessage() );
				}
			}
		}

		return [ $user_folder ];
	}

	public function create_folder( $data ) {

		$parent_folder = ! empty( $data['parent'] ) ? $data['parent'] : [
			'id'        => 'root',
			'accountId' => Account::instance()->get_active_account()['id']
		];

		$app         = App::instance( $parent_folder['accountId'] );
		$folder_name = igd_replace_template_tags( $data );


		$merge_folders = igd_get_settings( 'mergeFolders', false );
		$folder_exist  = false;

		// Check if folder is already exists
		if ( $merge_folders ) {
			$folder_exist = $app->get_file_by_name( $folder_name, $parent_folder['id'], current_user_can( 'manage_options' ) );
		}

		if ( ! $folder_exist ) {
			try {
				return $app->new_folder( $folder_name, $parent_folder['id'] );
			} catch ( \Exception $e ) {
				error_log( $e->getMessage() );

				return [];
			}
		}

		return $folder_exist;
	}

	public function delete_user_folder( $user_id ) {
		$folders = get_user_option( 'folders', $user_id );
		if ( empty( $folders ) ) {
			return;
		}

		$account_id = $folders[0]['accountId'];
		$folder_ids = wp_list_pluck( $folders, 'id' );

		try {
			App::instance( $account_id )->delete( $folder_ids, $account_id );
		} catch ( \Exception $e ) {
			error_log( $e->getMessage() );

			return;
		}
	}

	/**
	 * Get users data
	 *
	 * @return array
	 */
	public function get_user_data( $args = [] ) {

		$default = [
			'number'  => 999,
			'offset'  => 0,
			'role'    => '',
			'search'  => '',
			'order'   => 'asc',
			'orderby' => 'ID',
			'fields'  => 'all_with_meta',
		];

		$args = wp_parse_args( $args, $default );

		$users_query = new \WP_User_Query( $args );

		$data = [
			'roles' => count_users()["avail_roles"],
			'total' => $users_query->get_total(),
		];

		$results = $users_query->get_results();

		// Users Data
		$users = [];

		foreach ( $results as $user ) {

			// Gravatar
			$display_gravatar = igd_get_user_gravatar( $user->ID );

			$folders = get_user_option( 'folders', $user->ID );

			$users[] = [
				'id'       => $user->ID,
				'avatar'   => $display_gravatar,
				'username' => $user->user_login,
				'name'     => $user->display_name,
				'email'    => $user->user_email,
				'role'     => implode( ', ', $this->get_role_list( $user ) ),
				'folders'  => ! empty( $folders ) ? $folders : [],
			];
		}

		$data['users'] = $users;

		return $data;
	}

	/**
	 * Get user role list
	 *
	 * @param $user
	 *
	 * @return mixed|void
	 */
	public function get_role_list( $user ) {

		$wp_roles = wp_roles();

		$role_list = [];
		foreach ( $user->roles as $role ) {
			if ( isset( $wp_roles->role_names[ $role ] ) ) {
				$role_list[ $role ] = translate_user_role( $wp_roles->role_names[ $role ] );
			}
		}

		if ( empty( $role_list ) ) {
			$role_list['none'] = _x( 'None', 'No user roles', 'integrate-google-drive' );
		}

		return apply_filters( 'get_role_list', $role_list, $user );
	}

	public static function view() { ?>
        <script>
            var igdUserData = <?php echo json_encode( self::instance()->get_user_data( [ 'number' => 10 ] ) ) ?>;
        </script>
        <div id="igd-private-folders-app"></div>
	<?php }

	/**
	 * @return Private_Folders|null
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

}