<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://webadictos.com
 * @since      1.0.0
 *
 * @package    Wa_Theme_Manager
 * @subpackage Wa_Theme_Manager/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wa_Theme_Manager
 * @subpackage Wa_Theme_Manager/admin
 * @author     Daniel Medina <admin@webadictos.com.mx>
 */
class Wa_Theme_Manager_Admin
{

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	private $active_modules = array();

	/**
	 * Prefix for options
	 */

	protected $prefix;

	/**
	 * Prefix for options
	 */

	protected $prefix_posts;


	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct($plugin_name, $version)
	{

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->prefix = "wa_theme_options_";
		$this->prefix_posts = "wa_meta_";
	}


	public function wa_register_metaboxes()
	{



		$metabox_id = $this->prefix_posts . 'article_metabox';

		$_metaboxes = array(
			$metabox_id => array(
				'id'            => $metabox_id,
				'title'         => esc_html__('Opciones del artículo', 'cmb2'),
				'object_types'  => array('post'), // Post type
				'context'    => 'side',
				'priority'   => 'high',
				'wa_metabox_fields' => apply_filters("wa_theme_get_{$metabox_id}_fields", array(), $this->prefix_posts),
			)
		);

		// $_metaboxes = array();


		$metaboxes = apply_filters('wa_theme_set_metaboxes', $_metaboxes, $this->prefix_posts);

		if (is_array($metaboxes)) {

			// print_r($metaboxes);

			foreach ($metaboxes as $metabox) {

				if (isset($metabox['wa_metabox_fields']) && is_array($metabox['wa_metabox_fields']) && count($metabox['wa_metabox_fields']) > 0) {

					$_metabox = new_cmb2_box($metabox);

					foreach ($metabox['wa_metabox_fields'] as $field) {


						$cmbOptions = $_metabox->add_field($field);
					}
				}
			}
		}
	}




	public function wa_register_metaboxes_old()
	{
		$prefix = $this->prefix_posts; //'wa_post_';


		$cmb_metabox = new_cmb2_box(array(
			'id'            => $prefix . 'metabox',
			'title'         => esc_html__('Opciones del artículo', 'cmb2'),
			'object_types'  => array('post', 'page'), // Post type
			'context'    => 'side',
			'priority'   => 'high',
		));


		$cmb_metabox->add_field(array(
			'name' => 'Opciones de Publicidad',
			'type' => 'title',
			'id'   => $prefix . 'opciones_ads'
		));
		$cmb_metabox->add_field(array(
			'name' => 'Posición del banner inRead',
			'desc' => 'Párrafo donde aparecerá el banner inRead',
			'id'   => $prefix . 'inread_paragraph',
			'type' => 'text',
			'default' => '3',
			'attributes' => array(
				'type' => 'number',
				'pattern' => '\d*',
			),
			//'sanitization_cb' => 'intval',
			//	'escape_cb'       => 'intval',
		));

		$cmb_metabox->add_field(array(
			'name' => 'Desactivar anuncios',
			'id'   => $prefix . 'hide_adunits',
			'type' => 'checkbox',
			'desc' => "Marque si desea desactivar bloques de anuncios en la nota",
		));

		$cmb_metabox->add_field(array(
			'name'    => 'Desactivar Bloques de anuncios',
			'desc'    => 'Marque los bloques a desactivar',
			'id'      => $prefix . 'adunits',
			'type'    => 'multicheck',
			'options' => array(
				'ros-t-a' => 'Super Banner A',
				'ros-t-b' => 'Super Banner B',
				'ros-footer' => 'Footer',
				'ros-inread' => 'Primer inRead',
				'ros-b-notas' => 'inRead secundarios (cada 5 párrafos)',
				'ros-i' => 'Interstitial',
				'ros-b-a' => 'Box Banner A',
				'ros-b-b' => 'Box Banner B',
			),
			'attributes'    => array(
				'data-conditional-id'     => $prefix . 'hide_adunits',
				'data-conditional-value'  => 'on',
			),
		));

		$cmb_metabox->add_field(array(
			'name' => 'Scroll Infinito',
			'type' => 'title',
			'id'   => $prefix . 'opciones_scroll',
			'show_on_cb' => array($this, 'showIfScrollIsEnabled')
		));

		$cmb_metabox->add_field(array(
			'name'    => 'Desactivar Scroll',
			'desc'    => 'Marque para desactivar el scroll en esta nota.',
			'id'      => $prefix . 'disable_scroll',
			'type' => 'checkbox',
			'show_on_cb' => array($this, 'showIfScrollIsEnabled')

		));

		$cmb_metabox->add_field(array(
			'name'      	=> 'Notas siguientes en el scroll',
			'id'        	=> $prefix . 'posts_scroll',
			'type'      	=> 'post_search_ajax',
			'desc'			=> 'Elige notas que deseas que aparezcan enseguida en el scroll. Estas notas aparecerán primero en el scroll, antes que las predeterminadas',
			// Optional :
			'limit'      	=> 5, 		// Limit selection to X items only (default 1)
			'maxitems'      => 5,
			'sortable' 	 	=> true, 	// Allow selected items to be sortable (default false)
			'query_args'	=> array(
				'post_type'			=> array('post'),
				'post_status'		=> array('publish'),
				'posts_per_page'	=> 5,
				'date_query' => array(
					'after' => date('Y-m-d', strtotime('-5 years'))
				)
			)
		));
	}


	public function showIfScrollIsEnabled($field)
	{
		// Returns true if current user's ID is 1, else false

		if (isset($GLOBALS['theme_setup']['scroll']['enableScroll'])) {
			return $GLOBALS['theme_setup']['scroll']['enableScroll'];
		}
		return true;
		// Use $field->object_id if you need
		// the current object (post, user, etc) ID.
	}


	/**
	 * Hook in and register a metabox to handle a theme options page and adds a menu item.
	 */

	public function wa_theme_manager_settings_page()
	{
		$prefix = $this->prefix; //'wa_theme_options_';


		$defaultOptionsPages = array('wa_theme_options_page' => array(
			'id'           => 'wa_theme_options_page',
			'title'        => esc_html__('Configuración del tema', 'cmb2'),
			'object_types' => array('options-page'),
			'option_key'      => 'wa_theme_options', // The option key and admin menu page slug.
			'icon_url'        => 'dashicons-editor-table', // Menu icon. Only applicable if 'parent_slug' is left empty.
			'menu_title'      => esc_html__('Theme Manager', 'cmb2'), // Falls back to 'title' (above).
			'capability'      => 'edit_posts', // Cap required to view options-page.
			'position'        => 5, // Menu position. Only applicable if 'parent_slug' is left empty.
			'autoload'        => false, // Defaults to true, the options-page option will be autloaded.
			'has_tabs' => true,
			'vertical_tabs' => true,
			'wa_fields' => apply_filters('wa_theme_get_wa_theme_options_page_fields', array(
				$prefix . 'theme_setup' =>
				array(
					'id'          => $prefix . 'theme_setup',
					'type'        => 'group',
					'description' => 'Configuración general del tema',
					'repeatable'  => false, // use false if you want non-repeatable group
					'options'     => array(
						'group_title'       => __('General', 'cmb2'), // since version 1.1.4, {#} gets replaced by row number
						// 'add_button'        => __( 'Add Another Entry', 'cmb2' ),
						// 'remove_button'     => __( 'Remove Entry', 'cmb2' ),
						'sortable'          => false,
						'closed'         => false, // true to have the groups closed by default
						// 'remove_confirm' => esc_html__( 'Are you sure you want to remove?', 'cmb2' ), // Performs confirmation before removing group.
					),
					'tab_name' => 'General',
					'tab_icon' => 'dashicons-admin-settings',
					'wa_group_fields' => apply_filters('wa_theme_get_wa_theme_options_theme_setup_fields', array(
						'logo' =>
						array(
							'name'    => 'Logo',
							'desc'    => 'Seleccione el logo del sitio.',
							'id'          => 'logo',
							'type'    => 'file',
							// Optional:
							'options' => array(
								'url' => true, // Hide the text input for the url
							),
							'text'    => array(
								'add_upload_file_text' => 'Agregar imagen' // Change upload button text. Default: "Add or Upload File"
							),
							// query_args are passed to wp.media's library query.
							'query_args' => array(
								//'type' => 'application/pdf', // Make library only display PDFs.
								// Or only allow gif, jpg, or png images
								'type' => array(
									'image/gif',
									'image/jpeg',
									'image/png',
									'image/svg+xml',
								),
							),
							'preview_size' => 'thumbnail', // Image size to use when previewing in the admin.
						),
						'logo_dark' =>
						array(
							'name'    => 'Logo para fondo oscuro',
							'desc'    => 'Seleccione el logo del sitio para fondos oscuros.',
							'id'          => 'logo_dark',
							'type'    => 'file',
							// Optional:
							'options' => array(
								'url' => true, // Hide the text input for the url
							),
							'text'    => array(
								'add_upload_file_text' => 'Agregar imagen' // Change upload button text. Default: "Add or Upload File"
							),
							// query_args are passed to wp.media's library query.
							'query_args' => array(
								//'type' => 'application/pdf', // Make library only display PDFs.
								// Or only allow gif, jpg, or png images
								'type' => array(
									'image/gif',
									'image/jpeg',
									'image/png',
									'image/svg+xml',
								),
							),
							'preview_size' => 'thumbnail', // Image size to use when previewing in the admin.
						),
						'logo_navbar' => array(
							'name'    => 'Logo navbar fixed',
							'desc'    => 'Seleccione el logo del sitio para la barra de menú fija.',
							'id'          => 'logo_navbar',
							'type'    => 'file',
							// Optional:
							'options' => array(
								'url' => true, // Hide the text input for the url
							),
							'text'    => array(
								'add_upload_file_text' => 'Agregar imagen' // Change upload button text. Default: "Add or Upload File"
							),
							// query_args are passed to wp.media's library query.
							'query_args' => array(
								//'type' => 'application/pdf', // Make library only display PDFs.
								// Or only allow gif, jpg, or png images
								'type' => array(
									'image/gif',
									'image/jpeg',
									'image/png',
									'image/svg+xml',
								),
							),
							'preview_size' => 'thumbnail', // Image size to use when previewing in the admin.
						),
						'logo_footer' => array(
							'name'    => 'Logo footer',
							'desc'    => 'Seleccione el logo del sitio para el footer.',
							'id'          => 'logo_footer',
							'type'    => 'file',
							// Optional:
							'options' => array(
								'url' => true, // Hide the text input for the url
							),
							'text'    => array(
								'add_upload_file_text' => 'Agregar imagen' // Change upload button text. Default: "Add or Upload File"
							),
							// query_args are passed to wp.media's library query.
							'query_args' => array(
								//'type' => 'application/pdf', // Make library only display PDFs.
								// Or only allow gif, jpg, or png images
								'type' => array(
									'image/gif',
									'image/jpeg',
									'image/png',
									'image/svg+xml',
								),
							),
							'preview_size' => 'thumbnail', // Image size to use when previewing in the admin.
						),
						'default_image' => array(
							'name'    => 'Imagen predeterminada',
							'desc'    => 'Seleccione la imagen predeterminada para mostrar en caso de que no exista imagen destacada.',
							'id'          => 'default_image',
							'type'    => 'file',
							// Optional:
							'options' => array(
								'url' => true, // Hide the text input for the url
							),
							'text'    => array(
								'add_upload_file_text' => 'Agregar imagen' // Change upload button text. Default: "Add or Upload File"
							),
							// query_args are passed to wp.media's library query.
							'query_args' => array(
								//'type' => 'application/pdf', // Make library only display PDFs.
								// Or only allow gif, jpg, or png images
								'type' => array(
									'image/gif',
									'image/jpeg',
									'image/png',
									'image/svg+xml',
								),
							),
							'preview_size' => 'thumbnail', // Image size to use when previewing in the admin.
						),

						'refreshPage' => array(
							'name'             => '¿Refrescar página?',
							'desc'             => 'Indica si deseas que la página se refresque cada determinados segundos en caso de inactividad.',
							'id'               => 'refreshPage',
							'type'             => 'select',
							'show_option_none' => false,
							'default'          => 1,
							'options'          => array(
								0 => __('No', 'cmb2'),
								1   => __('Si', 'cmb2'),
							),
						),
						'refreshTime' => array(
							'name' => 'Introduzca un valor en segundos',
							'desc' => 'A los cuantos segundos debe refrescarse la página. Default: 60 (1 minuto)',
							'id'   => 'refreshTime',
							'type' => 'text',
							'default' => 60,
							'attributes' => array(
								'type' => 'number',
								'pattern' => '\d*',
							),
						),
						'enableDMP' => array(
							'name'             => '¿Habilitar DMP?',
							'desc'             => 'Indica si deseas activar el seguimiento del usuario. (Función experimental).',
							'id'               => 'enableDMP',
							'type'             => 'select',
							'show_option_none' => false,
							'default'          => 1,
							'options'          => array(
								0 => __('No', 'cmb2'),
								1   => __('Si', 'cmb2'),
							),
						)


					), $prefix),
				)


			)),
		));



		$optionsPage = apply_filters('wa_theme_set_options_page', $defaultOptionsPages, $prefix);

		if (is_array($optionsPage)) {


			foreach ($optionsPage as $optionPage) {


				if (isset($optionPage['has_tabs']) && $optionPage['has_tabs']) {
					if (is_array($optionPage['wa_fields'])) {
						foreach ($optionPage['wa_fields'] as $field) {

							$optionPage['tabs'][] = array(
								'id'    => 'tab-' . uniqid(),
								'icon' => $field['tab_icon'],
								'title' => $field['tab_name'],
								'fields' => array(
									$field['id'],
								),
							);
						}
					}
				}


				$themeOptions = new_cmb2_box($optionPage);

				if (is_array($optionPage['wa_fields'])) {
					foreach ($optionPage['wa_fields'] as $field) {


						$cmbOptions = $themeOptions->add_field($field);

						if ($field['type'] === "group" && is_array($field['wa_group_fields'])) {
							foreach ($field['wa_group_fields'] as $fieldGroupId => $fieldGroupValue) {
								$themeOptions->add_group_field($cmbOptions, $fieldGroupValue);
							}
						}
					}
				}
			}
		}
	}
	public function wa_theme_manager_settings_page_dos()
	{
		$prefix = $this->prefix; //'wa_theme_options_';


		$defaultOptionsPages = array(
			'id'           => 'wa_theme_options_page',
			'title'        => esc_html__('Configuración del tema', 'cmb2'),
			'object_types' => array('options-page'),

			/*
		 * The following parameters are specific to the options-page box
		 * Several of these parameters are passed along to add_menu_page()/add_submenu_page().
		 */

			'option_key'      => 'wa_theme_options', // The option key and admin menu page slug.
			'icon_url'        => 'dashicons-editor-table', // Menu icon. Only applicable if 'parent_slug' is left empty.
			'menu_title'      => esc_html__('Theme Manager', 'cmb2'), // Falls back to 'title' (above).
			// 'parent_slug'     => 'themes.php', // Make options page a submenu item of the themes menu.
			'capability'      => 'edit_posts', // Cap required to view options-page.
			'position'        => 5, // Menu position. Only applicable if 'parent_slug' is left empty.
			// 'admin_menu_hook' => 'network_admin_menu', // 'network_admin_menu' to add network-level options page.
			// 'display_cb'      => false, // Override the options-page form output (CMB2_Hookup::options_page_output()).
			// 'save_button'     => esc_html__( 'Save Theme Options', 'cmb2' ), // The text for the options-page save button. Defaults to 'Save'.
			// 'disable_settings_errors' => true, // On settings pages (not options-general.php sub-pages), allows disabling.
			//'message_cb'      => 'instyle_options_page_message_callback',
			// 'tab_group'       => '', // Tab-group identifier, enables options page tab navigation.
			// 'tab_title'       => null, // Falls back to 'title' (above).
			'autoload'        => false, // Defaults to true, the options-page option will be autloaded.
		);

		$optionsPage = apply_filters('wa_theme_set_options_page', $defaultOptionsPages);

		print_r($optionsPage);

		/**
		 * Registers options page menu item and form.
		 */
		$themeOptions = new_cmb2_box(array(
			'id'           => 'wa_theme_options_page',
			'title'        => esc_html__('Configuración del tema', 'cmb2'),
			'object_types' => array('options-page'),

			/*
		 * The following parameters are specific to the options-page box
		 * Several of these parameters are passed along to add_menu_page()/add_submenu_page().
		 */

			'option_key'      => 'wa_theme_options', // The option key and admin menu page slug.
			'icon_url'        => 'dashicons-editor-table', // Menu icon. Only applicable if 'parent_slug' is left empty.
			'menu_title'      => esc_html__('Theme Manager', 'cmb2'), // Falls back to 'title' (above).
			// 'parent_slug'     => 'themes.php', // Make options page a submenu item of the themes menu.
			'capability'      => 'edit_posts', // Cap required to view options-page.
			'position'        => 5, // Menu position. Only applicable if 'parent_slug' is left empty.
			// 'admin_menu_hook' => 'network_admin_menu', // 'network_admin_menu' to add network-level options page.
			// 'display_cb'      => false, // Override the options-page form output (CMB2_Hookup::options_page_output()).
			// 'save_button'     => esc_html__( 'Save Theme Options', 'cmb2' ), // The text for the options-page save button. Defaults to 'Save'.
			// 'disable_settings_errors' => true, // On settings pages (not options-general.php sub-pages), allows disabling.
			//'message_cb'      => 'instyle_options_page_message_callback',
			// 'tab_group'       => '', // Tab-group identifier, enables options page tab navigation.
			// 'tab_title'       => null, // Falls back to 'title' (above).
			'autoload'        => false, // Defaults to true, the options-page option will be autloaded.
		));


		$generalOptions = $themeOptions->add_field(array(
			'id'          => $prefix . 'theme_setup',
			'type'        => 'group',
			'description' => 'Configuración general del tema',
			'repeatable'  => false, // use false if you want non-repeatable group
			'options'     => array(
				'group_title'       => __('General', 'cmb2'), // since version 1.1.4, {#} gets replaced by row number
				// 'add_button'        => __( 'Add Another Entry', 'cmb2' ),
				// 'remove_button'     => __( 'Remove Entry', 'cmb2' ),
				'sortable'          => false,
				'closed'         => true, // true to have the groups closed by default
				// 'remove_confirm' => esc_html__( 'Are you sure you want to remove?', 'cmb2' ), // Performs confirmation before removing group.
			),
		));



		$themeOptions->add_group_field($generalOptions, array(
			'name'    => 'Logo',
			'desc'    => 'Seleccione el logo del sitio.',
			'id'          => 'logo',
			'type'    => 'file',
			// Optional:
			'options' => array(
				'url' => true, // Hide the text input for the url
			),
			'text'    => array(
				'add_upload_file_text' => 'Agregar imagen' // Change upload button text. Default: "Add or Upload File"
			),
			// query_args are passed to wp.media's library query.
			'query_args' => array(
				//'type' => 'application/pdf', // Make library only display PDFs.
				// Or only allow gif, jpg, or png images
				'type' => array(
					'image/gif',
					'image/jpeg',
					'image/png',
					'image/svg+xml',
				),
			),
			'preview_size' => 'thumbnail', // Image size to use when previewing in the admin.
		));

		$themeOptions->add_group_field($generalOptions, array(
			'name'    => 'Logo para fondo oscuro',
			'desc'    => 'Seleccione el logo del sitio para fondos oscuros.',
			'id'          => 'logo_dark',
			'type'    => 'file',
			// Optional:
			'options' => array(
				'url' => true, // Hide the text input for the url
			),
			'text'    => array(
				'add_upload_file_text' => 'Agregar imagen' // Change upload button text. Default: "Add or Upload File"
			),
			// query_args are passed to wp.media's library query.
			'query_args' => array(
				//'type' => 'application/pdf', // Make library only display PDFs.
				// Or only allow gif, jpg, or png images
				'type' => array(
					'image/gif',
					'image/jpeg',
					'image/png',
					'image/svg+xml',
				),
			),
			'preview_size' => 'thumbnail', // Image size to use when previewing in the admin.
		));

		$themeOptions->add_group_field($generalOptions, array(
			'name'    => 'Logo navbar fixed',
			'desc'    => 'Seleccione el logo del sitio para la barra de menú fija.',
			'id'          => 'logo_navbar',
			'type'    => 'file',
			// Optional:
			'options' => array(
				'url' => true, // Hide the text input for the url
			),
			'text'    => array(
				'add_upload_file_text' => 'Agregar imagen' // Change upload button text. Default: "Add or Upload File"
			),
			// query_args are passed to wp.media's library query.
			'query_args' => array(
				//'type' => 'application/pdf', // Make library only display PDFs.
				// Or only allow gif, jpg, or png images
				'type' => array(
					'image/gif',
					'image/jpeg',
					'image/png',
					'image/svg+xml',
				),
			),
			'preview_size' => 'thumbnail', // Image size to use when previewing in the admin.
		));

		$themeOptions->add_group_field($generalOptions, array(
			'name'    => 'Logo footer',
			'desc'    => 'Seleccione el logo del sitio para el footer.',
			'id'          => 'logo_footer',
			'type'    => 'file',
			// Optional:
			'options' => array(
				'url' => true, // Hide the text input for the url
			),
			'text'    => array(
				'add_upload_file_text' => 'Agregar imagen' // Change upload button text. Default: "Add or Upload File"
			),
			// query_args are passed to wp.media's library query.
			'query_args' => array(
				//'type' => 'application/pdf', // Make library only display PDFs.
				// Or only allow gif, jpg, or png images
				'type' => array(
					'image/gif',
					'image/jpeg',
					'image/png',
					'image/svg+xml',
				),
			),
			'preview_size' => 'thumbnail', // Image size to use when previewing in the admin.
		));

		$themeOptions->add_group_field($generalOptions, array(
			'name'    => 'Imagen predeterminada',
			'desc'    => 'Seleccione la imagen predeterminada para mostrar en caso de que no exista imagen destacada.',
			'id'          => 'default_image',
			'type'    => 'file',
			// Optional:
			'options' => array(
				'url' => true, // Hide the text input for the url
			),
			'text'    => array(
				'add_upload_file_text' => 'Agregar imagen' // Change upload button text. Default: "Add or Upload File"
			),
			// query_args are passed to wp.media's library query.
			'query_args' => array(
				//'type' => 'application/pdf', // Make library only display PDFs.
				// Or only allow gif, jpg, or png images
				'type' => array(
					'image/gif',
					'image/jpeg',
					'image/png',
					'image/svg+xml',
				),
			),
			'preview_size' => 'thumbnail', // Image size to use when previewing in the admin.
		));


		$themeOptions->add_group_field($generalOptions, array(
			'name'    => 'Fondo del menu',
			'desc'    => 'Seleccione la imagen de fondo para el menú.',
			'id'          => 'menu_background',
			'type'    => 'file',
			// Optional:
			'options' => array(
				'url' => true, // Hide the text input for the url
			),
			'text'    => array(
				'add_upload_file_text' => 'Agregar imagen' // Change upload button text. Default: "Add or Upload File"
			),
			// query_args are passed to wp.media's library query.
			'query_args' => array(
				//'type' => 'application/pdf', // Make library only display PDFs.
				// Or only allow gif, jpg, or png images
				'type' => array(
					'image/gif',
					'image/jpeg',
					'image/png',
					'image/svg+xml',
				),
			),
			'preview_size' => 'thumbnail', // Image size to use when previewing in the admin.
		));


		$themeOptions->add_group_field($generalOptions, array(
			'name'    => 'Fondo de productos',
			'desc'    => 'Seleccione la imagen de fondo de los productos.',
			'id'          => 'fondo_productos',
			'type'    => 'file',
			// Optional:
			'options' => array(
				'url' => true, // Hide the text input for the url
			),
			'text'    => array(
				'add_upload_file_text' => 'Agregar imagen' // Change upload button text. Default: "Add or Upload File"
			),
			// query_args are passed to wp.media's library query.
			'query_args' => array(
				//'type' => 'application/pdf', // Make library only display PDFs.
				// Or only allow gif, jpg, or png images
				'type' => array(
					'image/gif',
					'image/jpeg',
					'image/png',
					'image/svg+xml',
				),
			),
			'preview_size' => 'thumbnail', // Image size to use when previewing in the admin.
		));





		$themeOptions->add_group_field($generalOptions, array(
			'name'             => '¿Refrescar página?',
			'desc'             => 'Indica si deseas que la página se refresque cada determinados segundos en caso de inactividad.',
			'id'               => 'refreshPage',
			'type'             => 'select',
			'show_option_none' => false,
			'default'          => 1,
			'options'          => array(
				0 => __('No', 'cmb2'),
				1   => __('Si', 'cmb2'),
			),
		));

		$themeOptions->add_group_field($generalOptions, array(
			'name' => 'Introduzca un valor en segundos',
			'desc' => 'A los cuantos segundos debe refrescarse la página. Default: 60 (1 minuto)',
			'id'   => 'refreshTime',
			'type' => 'text',
			'default' => 60,
			'attributes' => array(
				'type' => 'number',
				'pattern' => '\d*',
			),
		));

		$themeOptions->add_group_field($generalOptions, array(
			'name'             => '¿Habilitar DMP?',
			'desc'             => 'Indica si deseas activar el seguimiento del usuario. (Función experimental).',
			'id'               => 'enableDMP',
			'type'             => 'select',
			'show_option_none' => false,
			'default'          => 1,
			'options'          => array(
				0 => __('No', 'cmb2'),
				1   => __('Si', 'cmb2'),
			),
		));



		$scrollOptions = $themeOptions->add_field(array(
			'id'          => $prefix . 'scroll',
			'type'        => 'group',
			'description' => 'Configuración del scroll infinito',
			'repeatable'  => false, // use false if you want non-repeatable group
			'options'     => array(
				'group_title'       => __('Configuración del scroll infinito', 'cmb2'), // since version 1.1.4, {#} gets replaced by row number
				// 'add_button'        => __( 'Add Another Entry', 'cmb2' ),
				// 'remove_button'     => __( 'Remove Entry', 'cmb2' ),
				'sortable'          => false,
				'closed'         => true, // true to have the groups closed by default
				// 'remove_confirm' => esc_html__( 'Are you sure you want to remove?', 'cmb2' ), // Performs confirmation before removing group.
			),
		));

		$themeOptions->add_group_field($scrollOptions, array(
			'name'             => '¿Habilitar scroll infinito en notas?',
			'desc'             => 'Indica si deseas activar el scroll infinito en las notas del sitio.',
			'id'               => 'enableScroll',
			'type'             => 'select',
			'show_option_none' => false,
			'default'          => 1,
			'options'          => array(
				0 => __('No', 'cmb2'),
				1   => __('Si', 'cmb2'),
			),
		));


		$themeOptions->add_group_field($scrollOptions, array(
			'name' => 'Notas a mostrar en el scroll infinito',
			'desc' => 'Indica cuantas notas quieres que aparezcan en el scroll infinito',
			'id'   => 'items',
			'type' => 'text',
			'default' => 5,
			'attributes' => array(
				'type' => 'number',
				'pattern' => '\d*',
			),
		));


		$themeOptions->add_group_field($scrollOptions, array(
			'name'             => 'Criterio para seleccionar notas para el scroll',
			'desc'             => 'Selecciona el criterio por medio del cual deseas que se elijan las notas del scroll infinito.',
			'id'               => 'criterio',
			'type'             => 'select',
			'show_option_none' => false,
			'default'          => "ultimas",
			'options'          => array(
				"ultimas" => __('Notas recientes', 'cmb2'),
				"seccion_principal"  => __('Categoría principal de la nota actual', 'cmb2'),
				"seccion_todas"  => __('Categorías de la nota actual', 'cmb2'),
				"tags"  => __('Etiquetas de la nota actual', 'cmb2'),
			),
		));


		$themeOptions->add_group_field($scrollOptions, array(
			'name'             => '¿Habilitar notas promocionadas en el scroll?',
			'desc'             => 'Indica si deseas activar las notas promocionadas en el scroll infinito.',
			'id'               => 'enablePromoted',
			'type'             => 'select',
			'show_option_none' => false,
			'default'          => 1,
			'options'          => array(
				0 => __('No', 'cmb2'),
				1   => __('Si', 'cmb2'),
			),
		));


		$themeOptions->add_group_field($scrollOptions, array(
			'name' => 'TTL de notas promocionadas',
			'desc' => 'Indica cuanto tiempo en segundos, un mismo usuario no podrá ver la misma nota promocionada en el scroll infinito.',
			'id'   => 'promotedTTL',
			'type' => 'text',
			'default' => 86400,
			'attributes' => array(
				'type' => 'number',
				'pattern' => '\d*',
			),
		));



		$mapOptions = $themeOptions->add_field(array(
			'id'          => $prefix . 'map',
			'type'        => 'group',
			'description' => 'Configuración del mapa',
			'repeatable'  => false, // use false if you want non-repeatable group
			'options'     => array(
				'group_title'       => __('Configuración del mapa', 'cmb2'), // since version 1.1.4, {#} gets replaced by row number
				// 'add_button'        => __( 'Add Another Entry', 'cmb2' ),
				// 'remove_button'     => __( 'Remove Entry', 'cmb2' ),
				'sortable'          => false,
				'closed'         => true, // true to have the groups closed by default
				// 'remove_confirm' => esc_html__( 'Are you sure you want to remove?', 'cmb2' ), // Performs confirmation before removing group.
			),
		));

		$themeOptions->add_group_field($mapOptions, array(
			'name' => 'Api Key Google',
			'desc' => 'Introduce el API de Google.',
			'id'   => 'api',
			'type' => 'text',
		));

		$themeOptions->add_group_field($mapOptions, array(
			'name' => 'Zoom Inicial',
			'desc' => 'Introduce el zoom inicial del mapa.',
			'id'   => 'zoom',
			'type' => 'text',
			'attributes' => array(
				'type' => 'number',
				'pattern' => '\d*',
			),
		));

		$themeOptions->add_group_field($mapOptions, array(
			'name' => 'Centro del mapa',
			'desc' => 'Arrastra el marcador a la ubicación',
			'id' => 'map_center',
			'type' => 'pw_map',
			'split_values' => true, // Save latitude and longitude as two separate fields
		));

		$themeOptions->add_group_field($mapOptions, array(
			'name'    => 'Imagen del marcador',
			'desc'    => 'Seleccione la imagen de los marcadores.',
			'id'          => 'marker',
			'type'    => 'file',
			// Optional:
			'options' => array(
				'url' => true, // Hide the text input for the url
			),
			'text'    => array(
				'add_upload_file_text' => 'Agregar imagen' // Change upload button text. Default: "Add or Upload File"
			),
			// query_args are passed to wp.media's library query.
			'query_args' => array(
				//'type' => 'application/pdf', // Make library only display PDFs.
				// Or only allow gif, jpg, or png images
				'type' => array(
					'image/gif',
					'image/jpeg',
					'image/png',
					'image/svg+xml',
				),
			),
			'preview_size' => 'thumbnail', // Image size to use when previewing in the admin.
		));

		$themeOptions->add_group_field($mapOptions, array(
			'name'    => 'Imagen del marcador (Activo)',
			'desc'    => 'Seleccione la imagen de los marcadores.',
			'id'          => 'markerActive',
			'type'    => 'file',
			// Optional:
			'options' => array(
				'url' => true, // Hide the text input for the url
			),
			'text'    => array(
				'add_upload_file_text' => 'Agregar imagen' // Change upload button text. Default: "Add or Upload File"
			),
			// query_args are passed to wp.media's library query.
			'query_args' => array(
				//'type' => 'application/pdf', // Make library only display PDFs.
				// Or only allow gif, jpg, or png images
				'type' => array(
					'image/gif',
					'image/jpeg',
					'image/png',
					'image/svg+xml',
				),
			),
			'preview_size' => 'thumbnail', // Image size to use when previewing in the admin.
		));


		$themeOptions->add_group_field($mapOptions, array(
			'name'    => 'Imagen para Mi ubicación',
			'desc'    => 'Seleccione la imagen para identificar la ubicación del usuario.',
			'id'          => 'mylocation',
			'type'    => 'file',
			// Optional:
			'options' => array(
				'url' => true, // Hide the text input for the url
			),
			'text'    => array(
				'add_upload_file_text' => 'Agregar imagen' // Change upload button text. Default: "Add or Upload File"
			),
			// query_args are passed to wp.media's library query.
			'query_args' => array(
				//'type' => 'application/pdf', // Make library only display PDFs.
				// Or only allow gif, jpg, or png images
				'type' => array(
					'image/gif',
					'image/jpeg',
					'image/png',
					'image/svg+xml',
				),
			),
			'preview_size' => 'thumbnail', // Image size to use when previewing in the admin.
		));





		$adsOptions = $themeOptions->add_field(array(
			'id'          => $prefix . 'ads',
			'type'        => 'group',
			'description' => 'Configuración de ads',
			'repeatable'  => false, // use false if you want non-repeatable group
			'options'     => array(
				'group_title'       => __('Configuración de bloques de anuncio en el sitio', 'cmb2'), // since version 1.1.4, {#} gets replaced by row number
				// 'add_button'        => __( 'Add Another Entry', 'cmb2' ),
				// 'remove_button'     => __( 'Remove Entry', 'cmb2' ),
				'sortable'          => false,
				'closed'         => true, // true to have the groups closed by default
				// 'remove_confirm' => esc_html__( 'Are you sure you want to remove?', 'cmb2' ), // Performs confirmation before removing group.
			),
		));


		$themeOptions->add_group_field($adsOptions, array(
			'name' => 'Google Ad Manager Network',
			'desc' => 'Introduce el ID del network de Google Ad Manager.',
			'id'   => 'network',
			'type' => 'text',
			'attributes' => array(
				'type' => 'number',
				'pattern' => '\d*',
			),
		));

		$themeOptions->add_group_field($adsOptions, array(
			'name' => 'Prefijo del bloque superior',
			'desc' => 'Si los bloques de anuncio del sitio tienen un bloque de nivel superior, escríbe el código',
			'id'   => 'prefix',
			'type' => 'text',
		));

		$themeOptions->add_group_field($adsOptions, array(
			'name'             => '¿Desplegar banners al interactuar?',
			'desc'             => 'Al habilitarlo, los banners se cargarán cuando el usuario interactue con el sitio.',
			'id'               => 'loadOnScroll',
			'type'             => 'select',
			'show_option_none' => false,
			'default'          => 0,
			'options'          => array(
				0 => __('No', 'cmb2'),
				1   => __('Si', 'cmb2'),
			),
		));


		$themeOptions->add_group_field($adsOptions, array(
			'name'             => '¿Refrescar bloques cada determinado tiempo?',
			'desc'             => 'Indica si deseas que todos los bloques de anuncio se refresquen cada determinados segundos. Al habilitar esto todos los bloques del sitio harán un refresh.',
			'id'               => 'refreshAllAdUnits',
			'type'             => 'select',
			'show_option_none' => false,
			'default'          => 0,
			'options'          => array(
				0 => __('No', 'cmb2'),
				1   => __('Si', 'cmb2'),
			),
		));

		$themeOptions->add_group_field($adsOptions, array(
			'name' => 'Tiempo en segundos para refrescar los adunits',
			'desc' => 'Indica cuanto tiempo en segundos se refrescarán todos los bloques.',
			'id'   => 'timeToRefreshAllAdUnits',
			'type' => 'text',
			'default' => 60,
			'attributes' => array(
				'type' => 'number',
				'pattern' => '\d*',
			),
		));


		$themeOptions->add_group_field($adsOptions, array(
			'name'             => '¿Refrescar bloques según viewability?',
			'desc'             => 'Indica si deseas que los bloques visibles se refresquen cada determinado tiempo. Al habilitar esto todos los bloques visibles harán un refresh.',
			'id'               => 'refreshAds',
			'type'             => 'select',
			'show_option_none' => false,
			'default'          => 1,
			'options'          => array(
				0 => __('No', 'cmb2'),
				1   => __('Si', 'cmb2'),
			),
		));

		$themeOptions->add_group_field($adsOptions, array(
			'name' => 'Tiempo en segundos para refrescar los adunits',
			'desc' => 'Indica cuanto tiempo en segundos se refrescarán todos los bloques visibles.',
			'id'   => 'refresh_time',
			'type' => 'text',
			'default' => 30,
			'attributes' => array(
				'type' => 'number',
				'pattern' => '\d*',
			),
		));


		$themeOptions->add_group_field($adsOptions, array(
			'name'             => '¿Habilitar banner inRead?',
			'desc'             => 'Al habilitarlo, se insertará un banner después del párrafo que se especifique de cada nota.',
			'id'               => 'enableInRead',
			'type'             => 'select',
			'show_option_none' => false,
			'default'          => 0,
			'options'          => array(
				0 => __('No', 'cmb2'),
				1   => __('Si', 'cmb2'),
			),
		));

		$themeOptions->add_group_field($adsOptions, array(
			'name' => 'Insertar inRead después del párrafo #',
			'desc' => 'Indica después de que párrafo del texto se insertará el banner inread',
			'id'   => 'inReadParagraph',
			'type' => 'text',
			'default' => 3,
			'attributes' => array(
				'type' => 'number',
				'pattern' => '\d*',
			),
		));


		$themeOptions->add_group_field($adsOptions, array(
			'name'             => '¿Habilitar múltiples banners inRead?',
			'desc'             => 'Al habilitarlo, se insertarán banners dentro del texto 5 párrafos después del primer inRead, esto dependiendo de la longitud del artículo.',
			'id'               => 'enableMultipleInRead',
			'type'             => 'select',
			'show_option_none' => false,
			'default'          => 0,
			'options'          => array(
				0 => __('No', 'cmb2'),
				1   => __('Si', 'cmb2'),
			),
		));

		$themeOptions->add_group_field($adsOptions, array(
			'name' => 'Máximo de banners inRead',
			'desc' => 'Indica el número máximo de banners inRead se podrán insertar en un texto',
			'id'   => 'inReadLimit',
			'type' => 'text',
			'default' => 3,
			'attributes' => array(
				'type' => 'number',
				'pattern' => '\d*',
			),
		));



		$socialOptions = $themeOptions->add_field(array(
			'id'          => $prefix . 'social',
			'type'        => 'group',
			'description' => 'Configuración de redes sociales',
			'repeatable'  => false, // use false if you want non-repeatable group
			'options'     => array(
				'group_title'       => __('Configuración de redes sociales', 'cmb2'), // since version 1.1.4, {#} gets replaced by row number
				// 'add_button'        => __( 'Add Another Entry', 'cmb2' ),
				// 'remove_button'     => __( 'Remove Entry', 'cmb2' ),
				'sortable'          => false,
				'closed'         => true, // true to have the groups closed by default
				// 'remove_confirm' => esc_html__( 'Are you sure you want to remove?', 'cmb2' ), // Performs confirmation before removing group.
			),
		));


		$themeOptions->add_group_field($socialOptions, array(
			'name' => 'Token de Instagram',
			'desc' => 'Token de Instagram para poder tener acceso a los últimos posts',
			'id'   => 'igtoken',
			'type' => 'text',
		));

		$themeOptions->add_group_field($socialOptions, array(
			'name' => 'YouTube API Key',
			'desc' => '',
			'id'   => 'GoogleAPIKey',
			'type' => 'text',
		));
		$themeOptions->add_group_field($socialOptions, array(
			'name' => 'YouTube Channel ID',
			'desc' => '',
			'id'   => 'youtube_channel',
			'type' => 'text',
		));


		/**
		 * Registers secondary options page, and set main item as parent.
		 */
		$secondary_options = new_cmb2_box(array(
			'id'           => $prefix . 'codes_page',
			'title'        => esc_html__('Códigos', 'cmb2'),
			'object_types' => array('options-page'),
			'option_key'   => $prefix . 'codes',
			'parent_slug'  => 'wa_theme_options',
			'capability'      => 'manage_options', // Cap required to view options-page.
			'vertical_tabs' => true, // Set vertical tabs, default false

			'tabs' => array(
				array(
					'id'    => 'socio-tab-1',
					'icon' => 'dashicons-editor-code',
					'title' => 'Header',
					'fields' => array(
						$prefix . 'codes_header',
					),
				),
				array(
					'id'    => 'socio-tab-2',
					'icon' => 'dashicons-editor-code',
					'title' => 'Body',
					'column' => true,
					'fields' => array(
						$prefix . 'codes_body',
					),
				),
				array(
					'id'    => 'socio-tab-3',
					'icon' => 'dashicons-editor-code',
					'title' => 'Footer',
					'column' => true,
					'fields' => array(
						$prefix . 'codes_footer',
					),
				),
			)
		));



		$blog_group_id = $secondary_options->add_field(array(
			'id'          => $prefix . 'codes_header',
			'type'        => 'group',
			'title' => 'Códigos antes del &lt;/head&gt;',
			'desc'       => 'Códigos antes del  &lt;/head&gt;',
			'repeatable'  => true,
			'options'     => array(
				'group_title'   => 'Código {#}',
				'add_button'    => 'Agregar Código',
				'remove_button' => 'Quitar Código',
				'closed'        => true,  // Repeater fields closed by default - neat & compact.
				'sortable'      => true,  // Allow changing the order of repeated groups.
			),
		));
		$secondary_options->add_group_field($blog_group_id, array(
			'name' => 'Identificador',
			'desc' => 'Escribe un identificador para el código',
			'id'   => 'identificador',
			'type' => 'text',
		));
		$secondary_options->add_group_field($blog_group_id, array(
			'name' => 'Código',
			'desc' => 'Código a insertar antes del &lt;/head&gt;',
			'id'   => 'codigo',
			'type' => 'textarea',
			'sanitization_cb' => array($this, 'accept_html_values_sanitize'),
		));

		$secondary_options->add_group_field($blog_group_id, array(
			'name'             => 'Mostrar en',
			'id'               => 'mostrar_en',
			'type'             => 'select',
			'desc' => "Selecciona donde se debe desplegar el código",
			'show_option_none' => false,
			'options'          => array(
				'ros'   => 'Todo el sitio',
				'single'   => 'En artículos solamente',
				'archive' => 'En canales solamente',
				'page' => 'En páginas',
				'search' => 'En búsquedas',

			)
		));


		$secondary_options->add_group_field($blog_group_id, array(
			'name'             => 'Post IDS',
			'id'               => 'posts_ids',
			'type'        => 'post_search_text', // This field type
			// post type also as array
			'post_type'   => array('post', 'page'),
			// Default is 'checkbox', used in the modal view to select the post type
			'select_type' => 'checkbox',
			// Will replace any selection with selection from modal. Default is 'add'
			'select_behavior' => 'add',
			'desc' => "Escribe las IDS de los posts/páginas donde se debe mostrar",
		));

		$secondary_options->add_group_field($blog_group_id, array(
			'name' => 'Habilitar',
			'id'   => 'enable_code',
			'type' => 'checkbox',
			'desc' => "Marque si desea activar este código",
		));



		$codeBody = $secondary_options->add_field(array(
			'id'          => $prefix . 'codes_body',
			'type'        => 'group',
			'title' => 'Códigos al inicio de &lt;body&gt;',
			'desc'       => 'Códigos al inicio de &lt;body&gt;',
			'repeatable'  => true,
			'options'     => array(
				'group_title'   => 'Código {#}',
				'add_button'    => 'Agregar Código',
				'remove_button' => 'Quitar Código',
				'closed'        => true,  // Repeater fields closed by default - neat & compact.
				'sortable'      => true,  // Allow changing the order of repeated groups.
			),
		));
		$secondary_options->add_group_field($codeBody, array(
			'name' => 'Identificador',
			'desc' => 'Escribe un identificador para el código',
			'id'   => 'identificador',
			'type' => 'text',
		));
		$secondary_options->add_group_field($codeBody, array(
			'name' => 'Código',
			'desc' => 'Código a insertar antes del &lt;/head&gt;',
			'id'   => 'codigo',
			'type' => 'textarea',
			'sanitization_cb' => array($this, 'accept_html_values_sanitize'),
		));

		$secondary_options->add_group_field($codeBody, array(
			'name'             => 'Mostrar en',
			'id'               => 'mostrar_en',
			'type'             => 'select',
			'desc' => "Selecciona donde se debe desplegar el código",
			'show_option_none' => false,
			'options'          => array(
				'ros'   => 'Todo el sitio',
				'single'   => 'En artículos solamente',
				'archive' => 'En canales solamente',
				'page' => 'En páginas',
				'search' => 'En búsquedas',

			)
		));

		$secondary_options->add_group_field($codeBody, array(
			'name'             => 'Post IDS',
			'id'               => 'posts_ids',
			'type'        => 'post_search_text', // This field type
			// post type also as array
			'post_type'   => array('post', 'page'),
			// Default is 'checkbox', used in the modal view to select the post type
			'select_type' => 'checkbox',
			// Will replace any selection with selection from modal. Default is 'add'
			'select_behavior' => 'add',
			'desc' => "Escribe las IDS de los posts/páginas donde se debe mostrar",
		));


		$secondary_options->add_group_field($codeBody, array(
			'name' => 'Habilitar',
			'id'   => 'enable_code',
			'type' => 'checkbox',
			'desc' => "Marque si desea activar este código",
		));


		$codeFooterGroup = $secondary_options->add_field(array(
			'id'          => $prefix . 'codes_footer',
			'type'        => 'group',
			'title' => 'Códigos antes de &lt;/body&gt;',
			'desc'       => 'Códigos antes de &lt;/body&gt;',
			'repeatable'  => true,
			'options'     => array(
				'group_title'   => 'Código {#}',
				'add_button'    => 'Agregar Código',
				'remove_button' => 'Quitar Código',
				'closed'        => true,  // Repeater fields closed by default - neat & compact.
				'sortable'      => true,  // Allow changing the order of repeated groups.
			),
		));
		$secondary_options->add_group_field($codeFooterGroup, array(
			'name' => 'Identificador',
			'desc' => 'Escribe un identificador para el código',
			'id'   => 'identificador',
			'type' => 'text',
		));
		$secondary_options->add_group_field($codeFooterGroup, array(
			'name' => 'Código',
			'desc' => 'Código a insertar antes del &lt;/head&gt;',
			'id'   => 'codigo',
			'type' => 'textarea',
			'sanitization_cb' => array($this, 'accept_html_values_sanitize'),
		));

		$secondary_options->add_group_field($codeFooterGroup, array(
			'name'             => 'Mostrar en',
			'id'               => 'mostrar_en',
			'type'             => 'select',
			'desc' => "Selecciona donde se debe desplegar el código",
			'show_option_none' => false,
			'options'          => array(
				'ros'   => 'Todo el sitio',
				'single'   => 'En artículos solamente',
				'archive' => 'En canales solamente',
				'page' => 'En páginas',
				'search' => 'En búsquedas',

			)
		));

		$secondary_options->add_group_field($codeFooterGroup, array(
			'name'             => 'Post IDS',
			'id'               => 'posts_ids',
			'type'        => 'post_search_text', // This field type
			// post type also as array
			'post_type'   => array('post', 'page'),
			// Default is 'checkbox', used in the modal view to select the post type
			'select_type' => 'checkbox',
			// Will replace any selection with selection from modal. Default is 'add'
			'select_behavior' => 'add',
			'desc' => "Escribe las IDS de los posts/páginas donde se debe mostrar",
		));



		$secondary_options->add_group_field($codeFooterGroup, array(
			'name' => 'Habilitar',
			'id'   => 'enable_code',
			'type' => 'checkbox',
			'desc' => "Marque si desea activar este código",
		));




		/**
		 * Registers secondary options page, and set main item as parent.
		 */
		$promote_options = new_cmb2_box(array(
			'id'           => $prefix . 'options_promote_page',
			'title'        => esc_html__('Notas patrocinadas', 'cmb2'),
			'object_types' => array('options-page'),
			'option_key'   => $prefix . 'promote',
			'parent_slug'  => 'wa_theme_options',
			'capability'      => 'edit_posts', // Cap required to view options-page.
		));

		$promote_options->add_field(array(
			'name'      	=> __('Notas patrocinadas', 'cmb2'),
			'id'        	=> $prefix . 'posts_promoted',
			'type'      	=> 'post_search_ajax',
			'desc'			=> 'Comienza escribiendo el título de la nota.<br>Las notas patrocinadas aparecerán enseguida en el scroll infinito, ayudando a conseguir más pageviews.',
			// Optional :
			'limit'      	=> 5, 		// Limit selection to X items only (default 1)
			'maxitems'      => 5,
			'sortable' 	 	=> true, 	// Allow selected items to be sortable (default false)
			'query_args'	=> array(
				'post_type'			=> array('post'),
				'post_status'		=> array('publish'),
				'posts_per_page'	=> 5,
				'date_query' => array(
					'after' => date('Y-m-d', strtotime('-2 years'))
				)
			)
		));
	}

	public function accept_html_values_sanitize($original_value, $args, $cmb2_field)
	{
		return $original_value; // Unsanitized value.
	}


	public function hide_if_published($field)
	{
		// Don't show this field if not in the cats category.
		//error_log("POST ".$field->object_id);
		//error_log(get_post_status( $field->object_id ));
		if (get_post_status($field->object_id) == "publish") {
			return false;
		}
		return true;
	}



	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles()
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wa_Theme_Manager_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wa_Theme_Manager_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		//	wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/wa-theme-manager-admin.css', array(), $this->version, 'all');
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts()
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wa_Theme_Manager_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wa_Theme_Manager_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		//wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/wa-theme-manager-admin.js', array('jquery'), $this->version, false);
	}

	public function getModulos()
	{
		$modulos = array();
		//REQUIRE INC
		$Directory = new RecursiveDirectoryIterator(get_stylesheet_directory() . '/template-parts/modulos/');
		$Iterator = new RecursiveIteratorIterator($Directory);
		$Regex = new RegexIterator($Iterator, '/^.+\.php$/i', RecursiveRegexIterator::GET_MATCH);

		foreach ($Regex as $yourfiles) {
			//print_r($yourfiles);
			//require_once $yourfiles[0];
			$modulos[] = $yourfiles[0];
		}

		return $modulos;
	}

	public function getModulosCallback($field)
	{
		$modulos = $this->getModulos();
		$term_options = array();
		$par = array(
			'Module' => 'Module Name',
			'ID' => 'Module ID',
			'Author' => 'Author',
			'Description' => 'Description',
			'Fields' => 'Fields',
		);
		foreach ($modulos as $modulo) {
			$filedata = get_file_data($modulo, $par);
			if ($filedata['Module'] != "") {
				$this->active_modules[$filedata['ID']] = $filedata;
				$term_options[$filedata['ID']] = $filedata['Module'];
			}
		}

		return $term_options;
	}

	public function getSidebarsCallback($field)
	{
		$term_options = array();
		foreach ($GLOBALS['wp_registered_sidebars'] as $sidebar) {

			$term_options[$sidebar['id']] = ucwords($sidebar['name']);
		}

		return $term_options;
	}

	public function checkModuleInfo($module, $key, $value)
	{
	}
}
