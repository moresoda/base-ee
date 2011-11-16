<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');



/**
 * One calorie SEO module, no sugar added!
 *
 * @package		Seo_lite
 * @subpackage	ThirdParty
 * @category	Modules
 * @author		bjorn
 * @link		http://ee.bybjorn.com/
 */
class Seo_lite_upd {
		
	var $version        = '1.2.4';
	var $module_name = "Seo_lite";

    /**
     * @var Devkit_code_completion
     */
    public $EE;

    function Seo_lite_upd( $switch = TRUE ) 
    { 
		// Make a local reference to the ExpressionEngine super object
		$this->EE =& get_instance();
    } 

    /**
     * Installer for the Seo_lite module
     */
    function install() 
	{				
		$site_id = $this->EE->config->item('site_id');
		if($site_id == 0)	// if SEO Lite is installed with a theme site_id will be 0, so set it to 1
		{
			$site_id = 1;
		}
		
		$data = array(
			'module_name' 	 => $this->module_name,
			'module_version' => $this->version,
			'has_cp_backend' => 'y',
            'has_publish_fields' => 'y'            
		);

		$this->EE->db->insert('modules', $data);		

        $this->EE->load->dbforge();

        $seolite_content_fields = array(
            'seolite_content_id' => array(
                'type' => 'int',
                'constraint' => '10',
                'unsigned' => TRUE,
                'auto_increment' => TRUE,),
            'site_id' => array(
                'type' => 'int',
                'constraint' => '10',
                'null' => FALSE,),
            'entry_id' => array(
                'type' => 'int',
                'constraint' => '10',
                'null' => FALSE,),

            'title' => array(
                'type' => 'varchar',
                'constraint' => '1024',
            ),            
            'keywords' => array(
                'type' => 'varchar',
                'constraint' => '1024',
                'null' => FALSE),
            'description' => array(
                'type' => 'text',),
        );

        $this->EE->dbforge->add_field($seolite_content_fields);
        $this->EE->dbforge->add_key('seolite_content_id', TRUE);
        $this->EE->dbforge->create_table('seolite_content');

        $seolite_config_fields = array(
            'seolite_config_id' => array(
                'type' => 'int',
                'constraint' => '10',
                'unsigned' => TRUE,
                'auto_increment' => TRUE,),
            'site_id' => array(
                'type' => 'int',
                'constraint' => '10',
                'unsigned' => TRUE,
            ),
            'template' => array(
                'type' => 'text',),
            'default_keywords' => array(
                'type' => 'varchar',
                'constraint' => '255',
                'null' => FALSE,),
            'default_description' => array(
                'type' => 'varchar',
                'constraint' => '255',
                'null' => FALSE),

            'default_title_postfix' => array(
                'type' => 'char',
                'constraint' => '60',
                'null' => FALSE),

        );

        $this->EE->dbforge->add_field($seolite_config_fields);
        $this->EE->dbforge->add_key('seolite_config_id', TRUE);
        $this->EE->dbforge->create_table('seolite_config');

        // insert default config
        $this->EE->db->insert('seolite_config', array(
            'template' => "<title>{title}{site_name}</title>\n	<meta name='description' content='{meta_description}' />\n	<link rel='canonical' href='{canonical_url}' />\n	<!-- generated by seo_lite -->",
            'site_id' => $site_id,
            'default_keywords' => 'your, default, keywords, here',
            'default_description' => 'Your default description here',
            'default_title_postfix' => ' |&nbsp;',
        ));

        $this->EE->load->library('layout');
        $this->EE->layout->add_layout_tabs($this->tabs(), 'seo_lite');

		return TRUE;
	}

    function tabs()
    {
        $tabs['seo_lite'] = array(
            'seo_lite_title'=> array(
                'visible'	=> 'true',
                'collapse'	=> 'false',
                'htmlbuttons'	=> 'false',
                'width'		=> '100%'
                ),
            'seo_lite_keywords'=> array(
                'visible'	=> 'true',
                'collapse'	=> 'false',
                'htmlbuttons'	=> 'false',
                'width'		=> '100%'
                ),
            'seo_lite_description' => array(
                'visible'	=> 'true',
                'collapse'	=> 'false',
                'htmlbuttons'	=> 'false',
                'width'		=> '100%',
                ),            
            );

        return $tabs;
    }

	
	/**
	 * Uninstall the Seo_lite module
	 */
	function uninstall() 
	{ 				
        $this->EE->load->dbforge();
        
		$this->EE->db->select('module_id');
		$query = $this->EE->db->get_where('modules', array('module_name' => $this->module_name));
		
		$this->EE->db->where('module_id', $query->row('module_id'));
		$this->EE->db->delete('module_member_groups');
		
		$this->EE->db->where('module_name', $this->module_name);
		$this->EE->db->delete('modules');
		
		$this->EE->db->where('class', $this->module_name);
		$this->EE->db->delete('actions');
		
		$this->EE->db->where('class', $this->module_name.'_mcp');
		$this->EE->db->delete('actions');

        $this->EE->dbforge->drop_table('seolite_content');
        $this->EE->dbforge->drop_table('seolite_config');

        $this->EE->load->library('layout');
        $this->EE->layout->delete_layout_tabs($this->tabs(), 'seo_lite');

		return TRUE;
	}
	
	/**
	 * Update the Seo_lite module
	 * 
	 * @param $current current version number
	 * @return boolean indicating whether or not the module was updated 
	 */
    function update($current = '')
    {
        if ($current == $this->version)
        {
            return FALSE;
        }

        if ($current < '1.2')
        {
            $this->EE->load->dbforge();

            $fields = array('default_title_postfix' => array(
                'type' => 'char',
                'constraint' => '60',
                'null' => FALSE));

            $this->EE->dbforge->add_column('seolite_config', $fields);
        }

        if($current < '1.2.4')
        {

            // change the coltype of default_title_postifx, char(60) would strip trailing space
            $this->EE->db->query("ALTER TABLE ".$this->EE->db->dbprefix('seolite_config')." CHANGE `default_title_postfix` `default_title_postfix` VARCHAR( 60 )");

            // increase size of keywords/desc field
            $this->EE->db->query("ALTER TABLE ".$this->EE->db->dbprefix('seolite_config')." CHANGE `default_keywords` `default_keywords` VARCHAR( 1024 ) ");
            $this->EE->db->query("ALTER TABLE ".$this->EE->db->dbprefix('seolite_config')." CHANGE `default_description` `default_description` VARCHAR( 1024 )");

            $configs = $this->EE->db->get_where('seolite_config');
            foreach($configs->result() as $config)
            {
                $upd_arr = array(
                    'template' => str_replace('&nbsp;', ' ', htmlspecialchars_decode($config->template, ENT_QUOTES)),
                    'default_description' => str_replace('&nbsp;', ' ', htmlspecialchars_decode($config->default_description, ENT_QUOTES)),
                    'default_keywords' => str_replace('&nbsp;', ' ', htmlspecialchars_decode($config->default_keywords, ENT_QUOTES)),
                    'default_title_postfix' => str_replace('&nbsp;', ' ', htmlspecialchars_decode($config->default_title_postfix, ENT_QUOTES)),
                );
                
                $this->EE->db->update('seolite_config', $upd_arr, array('seolite_config_id'=>$config->seolite_config_id));
            }
        }

        return TRUE;
    }
    
}

/* End of file upd.seo_lite.php */ 
/* Location: ./system/expressionengine/third_party/seo_lite/upd.seo_lite.php */ 