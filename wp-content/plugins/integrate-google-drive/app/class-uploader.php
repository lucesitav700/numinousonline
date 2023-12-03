<?php

namespace IGD;

defined( 'ABSPATH' ) || exit();

class Uploader {

	protected static $instance = null;

	public $client;
	public $app;
	public $account_id;

	public function __construct( $account_id = null ) {
		$this->account_id = $account_id;
		$this->client     = Client::instance( $account_id )->get_client();
		$this->app        = App::instance( $account_id );
	}

	public function get_resume_url( $data ) {

		$dataFields = [
			'name',
			'path',
			'size',
			'type',
			'folderId',
			'uploadFileName',
			'wcOrderId',
			'wcProductId',
			'queueIndex'
		];

		foreach ( $dataFields as $field ) {
			$data[ $field ] = ! empty( $data[ $field ] ) ? sanitize_text_field( $data[ $field ] ) : '';
		}

		$data['overwrite']             = isset( $data['overwrite'] ) && filter_var( $data['overwrite'], FILTER_VALIDATE_BOOLEAN );
		$data['isWooCommerceUploader'] = ! empty( $data['isWooCommerceUploader'] );

		if ( ! empty( $data['path'] ) ) {
			$last_folders = $this->create_folder_structure( $data['path'], $data['folderId'] );

			$path_key         = trim( $data['path'], '/' );
			$data['folderId'] = ! empty( $last_folders[ $path_key ] ) ? $last_folders[ $path_key ] : $data['folderId'];
		}

		if ( $data['isWooCommerceUploader'] ) {
			$order   = $data['wcOrderId'] ? wc_get_order( $data['wcOrderId'] ) : null;
			$product = $data['wcProductId'] ? wc_get_product( $data['wcProductId'] ) : null;

			$folder           = WooCommerce_Uploads::instance()->get_upload_folder( $product, $order );
			$data['folderId'] = $folder['id'];
		}

		// Handle name template
		$name_template = ! empty( $data['uploadFileName'] ) ? $data['uploadFileName'] : '%file_name%%file_extension%';

		$tag_args = [
			'name' => $name_template,
			'file' => [
				'file_name'      => pathinfo( $data['name'], PATHINFO_FILENAME ),
				'file_extension' => ! empty( pathinfo( $data['name'], PATHINFO_EXTENSION ) ) ? '.' . pathinfo( $data['name'], PATHINFO_EXTENSION ) : '',
				'queue_index'    => $data['queueIndex'],
			]
		];


		if ( igd_contains_tags( 'user', $name_template ) ) {
			if ( is_user_logged_in() ) {
				$tag_args['user'] = get_userdata( get_current_user_id() );
			}
		}

		if ( igd_contains_tags( 'post', $name_template ) ) {
			$referrer = wp_get_referer();

			if ( ! empty( $referrer ) ) {
				$post_id = url_to_postid( $referrer );
				if ( ! empty( $post_id ) ) {
					$tag_args['post'] = get_post( $post_id );
					if ( $tag_args['post']->post_type == 'product' ) {
						$tag_args['wc_product'] = wc_get_product( $post_id );
					}
				}
			}
		}

		$name = igd_replace_template_tags( $tag_args );

		$file_exist = $data['overwrite'] ? $this->app->get_file_by_name( $name, $data['folderId'], current_user_can( 'manage_options' ) ) : false;

		try {

			$file = new \IGDGoogle_Service_Drive_DriveFile();
			$file->setName( $name );
			$file->setMimeType( $data['type'] );

			$this->client->setDefer( true );

			if ( false !== $file_exist ) {
				$request = $this->app->getService()->files->update( $file_exist['id'], $file, [
					'fields'            => $this->app->file_fields,
					'supportsAllDrives' => true
				] );
			} else {
				$file->setParents( [ $data['folderId'] ] );

				$request = $this->app->getService()->files->create( $file, [
					'fields'            => $this->app->file_fields,
					'supportsAllDrives' => true
				] );
			}

			$request_headers           = $request->getRequestHeaders();
			$request_headers['Origin'] = $_SERVER['HTTP_ORIGIN'];
			$request->setRequestHeaders( $request_headers );

			$chunkSizeBytes = 50 * 1024 * 1024;
			$media          = new \IGDGoogle_Http_MediaFileUpload( $this->client, $request, $data['type'], null, true, $chunkSizeBytes );
			$media->setFileSize( $data['size'] );

			$url = $media->getResumeUri();

			$this->client->setDefer( false );

			return $url;

		} catch ( \Exception $exception ) {
			return [ 'error' => $exception->getMessage() ];
		}
	}


	public function upload_post_process( $file ) {

		// Format file data
		$file['accountId'] = $this->account_id;
		$file['type']      = $file['mimeType'];
		$file['created']   = $file['createdTime'];
		$file['updated']   = $file['modifiedTime'];

		// Permission users
		$users       = [];
		$permissions = ! empty( $file['permissions'] ) ? $file['permissions'] : [];

		if ( count( $permissions ) > 0 ) {
			foreach ( $permissions as $permission ) {
				$users[ $permission['id'] ] = [
					'type'   => $permission['type'],
					'role'   => $permission['role'],
					'domain' => ! empty( $permission['domain'] ) ? $permission['domain'] : null,
				];
			}
		}

		$file['permissions'] = array_merge( $file['capabilities'], [ 'users' => $users ] );

		//exportAs
		$file['exportAs'] = igd_get_export_as( $file['mimeType'] );

		Files::add_file( $file, $file['parents'][0] );

		do_action( 'igd_insert_log', 'upload', $file['id'], $this->account_id );

	}

	public function create_folder_structure( $path, $parent_folder ) {

		$folders = array_filter( explode( '/', $path ) );

		$last_folders = [];

		$app = App::instance( $this->account_id );

		foreach ( $folders as $key => $name ) {

			// current folder path
			$folder_path = implode( '/', array_slice( $folders, 0, $key + 1 ) );

			$last_folder = array_slice( $last_folders, 0, $key );
			$last_folder = ! empty( $last_folder ) ? end( $last_folder ) : $parent_folder;

			// Check if folder is already exists
			$folder_exists = $app->get_file_by_name( $name, $last_folder, current_user_can( 'manage_options' ) );

			if ( $folder_exists ) {
				$last_folders[ $folder_path ] = $folder_exists['id'];

				continue;
			}

			// Create folder if not exists
			try {

				// add last folder id to the array
				$new_folder                   = $app->new_folder( $name, $last_folder );
				$last_folders[ $folder_path ] = $new_folder['id'];

			} catch ( \Exception $ex ) {

				error_log( 'Integrate Google Drive - Message: ' . sprintf( 'Failed to create new folders: %s', $ex->getMessage() ) );
			}

		}

		return $last_folders;

	}

	public function create_entry_folder_and_move( $files = [], $folder_name = '', $upload_folder = [], $merge_folders = false ) {
		$file_ids = array_column( $files, 'id' );

		$folder = [];

		// Check if folder is already exists
		if ( $merge_folders ) {
			$folder_exist = $this->app->get_file_by_name( $folder_name, $upload_folder['id'], current_user_can( 'manage_options' ) );

			if ( $folder_exist ) {
				$folder = $folder_exist;
			}

		}

		if ( empty( $folder ) ) {
			$folder = $this->app->new_folder( $folder_name, $upload_folder['id'] );
		}

		$this->app->move_file( $file_ids, $folder['id'] );

		return $folder;

	}

	public static function instance( $account_id = null ) {
		if ( is_null( self::$instance ) || self::$instance->account_id != $account_id ) {
			self::$instance = new self( $account_id );
		}

		return self::$instance;
	}

}