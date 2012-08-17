<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Devot:ee Monitor
 *
 * @package		ExpressionEngine
 * @subpackage	Add-ons
 * @category	Accessories
 * @author		Visual Chefs, LLC
 * @copyright	Copyright (c) 2011-2012, Visual Chefs, LLC
 */
class Devotee_acc {

	/**
	 * Accessory information
	 */
	public $name = 'Devot:ee Monitor';
	public $id = 'devot-ee';
	public $version = '1.1.0';
	public $description = 'Monitor your add-ons for updates.';
	public $sections = array();

	/**
	 * @var		object
	 * @access	protected
	 */
	protected $EE;

	/**
	 * @var		array
	 * @access	protected
	 */
	protected $_addons = array();

	/**
	 * @var		string
	 * @access	protected
	 */
	protected $_cache_path;

	/**
	 * @var		int
	 * @access	protected
	 */
	protected $_cache_time;

	/**
	 * @var     string
	 * @access  protected
	 */
	protected $theme_url;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->EE =& get_instance();

		// set cache settings
		$this->_cache_path = APPPATH.'cache/devotee/';
		$this->_cache_time = 60*60; // 1 hour

		// create cache folder if it doesn't exist
		if(!is_dir($this->_cache_path))
		{
			mkdir($this->_cache_path, DIR_WRITE_MODE);
		}

		// set theme url
		$this->theme_url = $this->EE->config->item('theme_folder_url') . 'third_party/devotee/';
	}

	/**
	 * Install accessory
	 */
	public function install()
	{
		return TRUE;
	}

	/**
	 * Update accessory
	 */
	public function update()
	{
		return TRUE;
	}

	/**
	 * Uninstall accessory
	 */
	public function uninstall()
	{
		return TRUE;
	}

	/**
	 * Set accessory sections
	 */
	public function set_sections()
	{
		$this->sections['Add-on Information'] = $this->_get_addons();

		// add assets to cp
		$this->EE->cp->add_to_head('<link rel="stylesheet" href="' . $this->theme_url . 'styles/accessory.css?v=' . $this->version . '" />');
		$this->EE->cp->add_to_head('<script type="text/javascript" src="' . $this->theme_url . 'scripts/accessory.js?v=' . $this->version . '"></script>');
	}

	/**
	 * Get installed addon information
	 *
	 * @access	protected
	 * @return	string
	 */
	protected function _get_addons()
	{
		$this->EE->load->helper(array('file', 'language'));

		// load json services if not available in php
		if( ! function_exists('json_decode'))
		{
			$this->EE->load->library('Services_json');
		}

		// cache file
		$cache_file = $this->_cache_path.'addons';

		// if cache is still good, use it
		if(file_exists($cache_file) AND filemtime($cache_file) > (time() - $this->_cache_time))
		{
			$updates = read_file($cache_file);
		}
		// fetch new content if cache expired
		else
		{
			$this->EE->load->helper('directory');
			$this->EE->load->library('addons');
			$this->EE->load->model('addons_model');
			$this->EE->load->library('api');

			// scan third_party folder
			$map = directory_map(PATH_THIRD, 2);

			// return if nothing found
			if($map === FALSE)
			{
				return 'No third-party addons were found.';
			}

			// get fieldtypes because the addons library doesn't give all the info
			$this->EE->api->instantiate('channel_fields');
			$fieldtypes = $this->EE->api_channel_fields->fetch_all_fieldtypes();

			// set third-party addons
			$third_party = array_intersect_key($this->EE->addons->_packages, $map);

			// get all installed addons
			$installed = array(
				'modules' => $this->EE->addons->get_installed('modules'),
				'plugins' => $this->EE->addons_model->get_plugins(),
				'extensions' => $this->EE->addons->get_installed('extensions'),
				'fieldtypes' => $this->EE->addons->get_installed('fieldtypes'),
				'accessories' => $this->EE->addons->get_installed('accessories')
			);

			foreach($third_party as $package => $types)
			{
				if(array_key_exists($package, $this->_addons))
				{
					continue;
				}

				// check for module
				if(array_key_exists($package, $installed['modules']))
				{
					$addon = $installed['modules'][$package];
					
					// fix weird name issue
					$this->EE->lang->loadfile(( ! isset($this->lang_overrides[$package])) ? $package : $this->lang_overrides[$package]);
					$name = (lang(strtolower($package).'_module_name') != FALSE) ? lang(strtolower($package).'_module_name') : $addon['name'];
					
					$this->_set_addon_info($package, $name, $addon['module_version'], $types);
				}
				// check for plugin
				elseif(array_key_exists($package, $installed['plugins']))
				{
					$addon = $installed['plugins'][$package];
					$this->_set_addon_info($package, $addon['pi_name'], $addon['pi_version'], $types);
				}
				// check for extension
				elseif(array_key_exists($package, $installed['extensions']))
				{
					$addon = $installed['extensions'][$package];
					$this->_set_addon_info($package, $addon['name'], $addon['version'], $types);
				}
				// check for fieldtype
				elseif(array_key_exists($package, $installed['fieldtypes']))
				{
					$addon = $fieldtypes[$package];
					$this->_set_addon_info($package, $addon['name'], $addon['version'], $types);
				}
				// check for accessory
				elseif(array_key_exists($package, $installed['accessories']))
				{
					$addon = $installed['accessories'][$package];

					// we need to load the class if not devotee to get more info
					if($package != 'devotee')
					{
						if( ! class_exists($addon['class']))
						{
							require_once PATH_THIRD."{$package}/acc.{$package}.php";
						}

						$acc = new $addon['class']();
					}
					// if devotee accessory, we already have the info!
					else
					{
						$acc = array(
							'name' => $this->name,
							'version' => $this->version
						);
						$acc = (object) $acc;
					}

					if(isset($acc))
					{
						$this->_set_addon_info($package, $acc->name, $acc->version, $types);

						unset($acc);
					}
				}
			}

			$updates = $this->_get_updates();

			write_file($cache_file, $updates);
		}

		// return view
		return $this->EE->load->view('accessory', array(
			'updates' => json_decode($updates),
			'last_check' => filemtime($cache_file)
		), TRUE);
	}

	/**
	 * Set addon info
	 *
	 * @param	string
	 * @param	string
	 * @param	string
	 * @param	array
	 * @access	protected
	 */
	protected function _set_addon_info($package, $name, $version, $types)
	{
		$this->_addons[$package] = array(
			'name' => $name,
			'version' => $version,
			'types' => $this->_abbreviate_types(array_keys($types))
		);
	}

	/**
	 * Get update info from API
	 *
	 * @access	protected
	 * @return	string
	 */
	protected function _get_updates()
	{
		$data = array(
			'data' => $this->_addons,
			'site_ip' => $this->EE->input->ip_address(),
		);

		$ch = curl_init('http://devotee.expressionmonitor.com/updates');
		curl_setopt_array($ch, array(
			CURLOPT_POST => TRUE,
			CURLOPT_RETURNTRANSFER => TRUE,
			CURLOPT_POSTFIELDS => $this->EE->javascript->generate_json($data, TRUE),
			CURLOPT_HTTPHEADER => array(
				'Content-type: application/json'
			)
		));
		$response = curl_exec($ch);
		curl_close($ch);

		if( ! $response)
		{
			$response = $this->EE->javascript->generate_json(array(
				'error' => 'The API could not be reached. Try again later.'
			), TRUE);
		}

		return $response;
	}

	/**
	 * Create an abbreviated list of add-on types, and designates whether the current add-on
	 * is of a particular type
	 *
	 * @param   array
	 * @return  array
	 * @access  protected
	 */
	protected function _abbreviate_types($types = array())
	{
		$available_types = array(
			'module' => 'MOD',
			'extension' => 'EXT',
			'plugin' => 'PLG',
			'fieldtype' => 'FLD',
			'accessory' => 'ACC'
		);

		$abbrevs = array();

		foreach($available_types as $key => $abbrev)
		{
			$abbrevs[$abbrev] = (in_array($key, $types)) ? TRUE : FALSE;
		}

		return $abbrevs;
	}

	/**
	 * AJAX method for clearing cache and reloading the addons list
	 */
	public function process_refresh()
	{
		if(AJAX_REQUEST)
		{
			// delete cache
			$this->EE->functions->delete_directory(APPPATH.'cache/devotee');

			// output html from view
			echo $this->_get_addons();
			exit;
		}
	}

}

/* End of file acc.ee_monitor.php */