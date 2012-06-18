<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
=====================================================
URL Helper Extension for ExpressionEngine 2.0
-----------------------------------------------------
http://www.boldminded.com/
-----------------------------------------------------

This is a combination of Bjorn Borresen's last_segment
extension (although last_segment is in EE 2.3+ core),
and Low's seg2cat extension. One hook call,
less to maintain, and less parsing to handle.

http://gotolow.com/addons/low-seg2cat

{last_segment} - returns the very last segment in the URI, even if it's a pagination segment
{last_segment_absolute} - returns the very last segment in the URI, but 2nd to last if the last is a pagination segment
{last_segment_id} - returns the ID of the last segment, in the case of /seg1/seg2/seg3/ it will return "3"
{last_segment_absolute_id} - return the ID of the last segment, or 2nd to last if the last is a pagination segment. In the case of /seg1/seg2/seg3/P5 it will return "3"
{parent_segment} - Will return the 2nd to last segment in the URI. In the case of /seg1/seg2/seg3/, it will return "seg2"
{all_segments} - /seg1/seg2/seg3/
{current_url} - http://www.mysite.com + segments + query string
{current_uri} - segments + query string
{current_url_encoded} - {current_url} base64encoded
{current_uri_encoded} - {current_uri} base64encoded
{query_string} - current query string including ?, returns blank if no query string exists
{referrer} - full referring/previous url visited
{referrer:segment_N} - fetch any segment from the referring url
{segment_N_category_id}
{segment_N_category_name}
{segment_N_category_description}
{segment_N_category_image}
{last_segment_category_id}
{last_segment_category_name}
{last_segment_category_description}
{last_segment_category_image}
{segment_category_ids} - 2&6&9 - useful for doing an all inclusive search of the segments
{segment_category_ids_any} - 2|6|9 - useful for doing an all inclusive search of the segments

Provided by PHP's parse_url() method

{query} - current query string without ?
{scheme} - http, https, ftp etc
{host} - your domain name, e.g. localhost, site.com
{port} - any port number present in the URL, e.g. :80 or :8888
{path} - full folder/virtural folder path or all segments if your site is located at the root of the domain/vhost
{fragment} - anything after # in the URI
{user}
{pass}

=====================================================
*/

class Url_helper_ext {

    var $settings = array();
    var $name = 'URL Helper';
    var $version = '1.0';
    var $description = 'Add various URL and segment variables to the Global variables.';
    var $settings_exist = 'n';
    var $docs_url = '';

    var $format = TRUE;

    function Url_helper_ext($settings='')
    {
        $this->settings = $settings;
        $this->EE =& get_instance();

        $this->config = $this->EE->config->item('current_url') ? $this->EE->config->item('current_url') : array();
        $this->prefix = isset($this->config['prefix']) ? $this->config['prefix'] : '';
    }

    /**
     * Do the magic.
     */
    function set_url_helper()
    {
        // Save a copy of the array so we don't reverse the global array, oops!
        $segs = $this->EE->uri->segments;

        $qry = (isset($_SERVER['QUERY_STRING']) AND $_SERVER['QUERY_STRING'] != '') ? '?'. $_SERVER['QUERY_STRING'] : '';

        $data[$this->prefix.'current_url'] = $this->EE->functions->remove_double_slashes($this->EE->config->item('site_url') . $this->EE->uri->uri_string .'/'. $qry);
        $data[$this->prefix.'current_uri'] = $this->EE->functions->remove_double_slashes('/'. $this->EE->uri->uri_string .'/'. $qry);
        $data[$this->prefix.'current_url_encoded'] = base64_encode($this->EE->functions->remove_double_slashes($data[$this->prefix.'current_url']));
        $data[$this->prefix.'current_uri_encoded'] = base64_encode($this->EE->functions->remove_double_slashes('/'. $this->EE->uri->uri_string .'/'. $qry));

        $data[$this->prefix.'query_string'] = $qry;

        $data[$this->prefix.'all_segments'] = '/'. implode('/', $segs) . '/';
        $data[$this->prefix.'print_segments'] = implode(' ', $segs);

        // Get the full referring URL
        $data[$this->prefix.'referrer'] = ( ! isset($_SERVER['HTTP_REFERER'])) ? '' : $this->EE->security->xss_clean($_SERVER['HTTP_REFERER']);

        // Now for something fun. Get the referring URL's segments! {referrer:segment_1}, {referrer:segment_2} etc
        $referrer_segments = explode('/', str_replace($this->EE->config->item('site_url'), '', $data[$this->prefix.'referrer']));
        for($i = 1; $i <= 9; $i++)
        {
            $data[$this->prefix.'referrer:segment_'. $i] = (isset($referrer_segments[$i-1])) ? $referrer_segments[$i-1] : '';
        }

        // Get all the URL parts.
        // http://php.net/manual/en/function.parse-url.php
        $url = parse_url($data[$this->prefix.'current_url']);

        foreach($url as $k => $v)
        {
            $data[$this->prefix.$k] = $v;
        }

        // Do a few things to get the parent segment, and only the parent segment
        // This could be helpful if we're 5 levels deep in the URL, and just need
        // the immediate parent, but don't know how deep we are.

        // Get rid of the last segment, which is our current page.
        array_pop($segs);

        // If this is true, then we're 2 segments deep.
        if(count($segs) == 1)
        {
            $data[$this->prefix.'parent_segment'] = $segs[1];
        }
        else
        {
            // Reverse the array, b/c we don't know how deep we are, and return
            // the first, which is the current page's parent. And re-index them
            // so the first is always 0. Schweet!
            $segs = array_merge(array_reverse($segs, TRUE));
            $data[$this->prefix.'parent_segment'] = isset($segs[0]) ? $segs[0] : '';
        }

        // Figure out the last_segment. Taken from Bjorn Borresen's last_segment add-on
        $segment_count = $this->EE->uri->total_segments();
        $last_segment_absolute = $this->EE->uri->segment($segment_count);
        $last_segment = $last_segment_absolute;
        $last_segment_id = $segment_count;

        if(substr($last_segment,0,1) == 'P') // might be a pagination page indicator
        {
            $end = substr($last_segment, 1, strlen($last_segment));
            if ((preg_match( '/^\d*$/', $end) == 1))
            {
                $last_segment_id = $segment_count-1;
                $last_segment = $this->EE->uri->segment($last_segment_id);
            }
        }

        $data[$this->prefix.'last_segment'] = $last_segment;
        $data[$this->prefix.'last_segment_absolute'] = $last_segment_absolute;
        $data[$this->prefix.'last_segment_id'] = $last_segment_id;
        $data[$this->prefix.'last_segment_absolute_id'] = $segment_count;

        // Put everything into global_vars
        $this->EE->config->_global_vars = array_merge($this->EE->config->_global_vars, $data);

        // This is basically the LowSeg2Cat extension.
        $this->set_category_segments();
    }

    private function set_category_segments()
    {
        // Only continue if request is a page and we have segments to check
        if (REQ != 'PAGE' || empty($this->EE->uri->segments)) return;

        // Suggestion by Leevi Graham: check for pattern before continuing
        // if ( !empty($this->settings['uri_pattern']) && !preg_match($this->settings['uri_pattern'], $this->EE->uri->uri_string) ) return;

        // initiate some vars
        $site = $this->EE->config->item('site_id');
        $data = $cats = $segs = array();
        $data[$this->prefix.'segment_category_ids'] = '';

        // loop through segments and set data array thus: segment_1_category_id etc
        foreach ($this->EE->uri->segments AS $nr => $seg)
        {
            $data[$this->prefix.'segment_'.$nr.'_category_id']            = '';
            $data[$this->prefix.'segment_'.$nr.'_category_name']          = '';
            $data[$this->prefix.'segment_'.$nr.'_category_description']   = '';
            $data[$this->prefix.'segment_'.$nr.'_category_image']         = '';
            $data[$this->prefix.'segment_'.$nr.'_category_parent_id']     = '';
            $segs[] = $seg;
        }

        // Compose query, get results
        $this->EE->db->select('cat_id, cat_url_title, cat_name, cat_description, cat_image, parent_id');
        $this->EE->db->from('exp_categories');
        $this->EE->db->where('site_id', $site);
        $this->EE->db->where_in('cat_url_title', $segs);
        $query = $this->EE->db->get();

        // if we have matching categories, continue...
        if ($query->num_rows())
        {
            // Load typography
            $this->EE->load->library('typography');

            // flip segment array to get 'segment_1' => '1'
            $ids = array_flip($this->EE->uri->segments);

            // loop through categories
            foreach ($query->result_array() as $row)
            {
                // overwrite values in data array
                $data[$this->prefix.'segment_'.$ids[$row['cat_url_title']].'_category_id']            = $row['cat_id'];
                $data[$this->prefix.'segment_'.$ids[$row['cat_url_title']].'_category_name']          = $this->format ? $this->EE->typography->format_characters($row['cat_name']) : $row['cat_name'];
                $data[$this->prefix.'segment_'.$ids[$row['cat_url_title']].'_category_description']   = $row['cat_description'];
                $data[$this->prefix.'segment_'.$ids[$row['cat_url_title']].'_category_image']         = $row['cat_image'];
                $data[$this->prefix.'segment_'.$ids[$row['cat_url_title']].'_category_parent_id']     = $row['parent_id'];
                $cats[] = $row['cat_id'];

                if($ids[$row['cat_url_title']] == count($ids))
                {
                    $data[$this->prefix.'last_segment_category_id']           = $row['cat_id'];
                    $data[$this->prefix.'last_segment_category_name']         = $this->EE->typography->format_characters($row['cat_name']);
                    $data[$this->prefix.'last_segment_category_description']  = $row['cat_description'];
                    $data[$this->prefix.'last_segment_category_image']        = $row['cat_image'];
                }
            }

            // create inclusive stack of all category ids present in segments
            $data[$this->prefix.'segment_category_ids'] = implode('&',$cats);
            $data[$this->prefix.'segment_category_ids_any'] = implode('|',$cats);
        }

        // Add data to global vars
        $this->EE->config->_global_vars = array_merge($this->EE->config->_global_vars, $data);
    }


    /**
     * Install the extension
     */
    function activate_extension()
    {
        // Delete old hooks
        $this->EE->db->query("DELETE FROM exp_extensions WHERE class = '". __CLASS__ ."'");

        // Add new hooks
        $ext_template = array(
            'class'    => __CLASS__,
            'settings' => '',
            'priority' => 8,
            'version'  => $this->version,
            'enabled'  => 'y'
        );

        $extensions = array(
            array('hook'=>'sessions_start', 'method'=>'set_url_helper')
        );

        foreach($extensions as $extension)
        {
            $ext = array_merge($ext_template, $extension);
            $this->EE->db->insert('exp_extensions', $ext);
        }
    }


    /**
     * No updates yet.
     * Manual says this function is required.
     * @param string $current currently installed version
     */
    function update_extension($current = '') {}

    /**
     * Uninstalls extension
     */
    function disable_extension()
    {
        // Delete records
        $this->EE->db->where('class', __CLASS__);
        $this->EE->db->delete('exp_extensions');
    }
}