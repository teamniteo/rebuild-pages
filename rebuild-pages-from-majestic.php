<?php

    /*
        Plugin Name: Rebuild Pages from Majestic
        Description: Rebuild pages with backlinks by importing Majestic CSV export.
        Author: NiteoWeb Ltd.
        Author URI:  www.niteoweb.com
     */
   
    include 'appdata/model/class.rebuildPagesMajestic_Post.php';

    class Rebuild_Pages_From_Majestic {

        public $defaults = array(
            'Title'                 => null,
            'URL'                   => null,
            'ReferringExtBackLinks' => 0
        );

        public $log = array();

        /**
         * 
         * Determine value of option $name from database, $default value or $params,
         * save it to the db if needed and return it.
         *
         * @param string $name
         * @param mixed  $default
         * @param array  $params
         * 
         * @return string
         * 
         */
        private function process_option($name, $default, $params) {
            if (array_key_exists($name, $params)) {
                $value = stripslashes($params[$name]);
            } else {
                $value = $default;
            }
            return $value;
        }

        /**
         * 
         * Plugin's interface
         *
         * @return void
         * 
         */
        function form() {
            
    		$opt_min_backlinks = $this->process_option('ebn_min_backlinks', 0, $_POST);
            
            if ('POST' == $_SERVER['REQUEST_METHOD']) {
                $this->post(compact('opt_min_backlinks'));
            }

            include 'appdata/view/main_settings_view.php';

        }

        function print_messages() {

            if (!empty($this->log)) {

                $show_error = (!empty($this->log['error'])) ? true : false;
                $show_notice = (!empty($this->log['notice'])) ? true : false;

                include 'appdata/view/main_settings_view_import_messages.php';

                $this->log = array();

            }

        }

        /**
         * 
         * Handle POST submission
         *
         * @param array $options
         * 
         * @return void
         * 
         */
        private function post($options) {

            if (empty($_FILES['ebn_import']['tmp_name'])) {
                $this->log['error'][] = 'No file uploaded, aborting.';
                $this->print_messages();
                return;
            }

            if (!current_user_can('publish_pages')) {
                $this->log['error'][] = 'You don\'t have the permissions to publish pages. Please contact the blog\'s administrator.';
                $this->print_messages();
                return;
            }

            //require_once 'File_CSV_DataSource/DataSource.php';

            $time_start = microtime(true);
    		
            //$csv = new File_CSV_DataSource;
            $file = $_FILES['ebn_import']['tmp_name'];
            $this->stripBOM($file);
    		$row = 1;
    		$skipped = 0;
            $imported = 0;
            $mappings = array();
            $home_url = get_home_url();
    		
    		if (($handle = fopen($file, "r")) !== FALSE) {
    			$data = fgetcsv($handle, filesize($file));
    			if(!$this->mappings) 
    				$this->mappings = $data; 
    			while (($data = fgetcsv($handle, filesize($file))) !== FALSE) {
    				//print_r($data);
    				if($data[0]) { 
    					foreach($data as $key => $value) 
    					   $converted_data[$this->mappings[$key]] = addslashes($value); 
    					//print_r($converted_data);
    					if (convert_chars($converted_data['ReferringExtBackLinks']) >= $options['opt_min_backlinks']) {
    						if ($post_id = $this->create_post($converted_data, $options, $home_url)) {
    							$imported++;
    						} else {
    							$skipped++;
    						}
    					}
    				}                       
    				
    			}
    			fclose($handle);
    		}
    		else{
    			$this->log['error'][] = 'Failed to load file, aborting.';
                $this->print_messages();
                return;
    		}
    		if (file_exists($file)) {
                @unlink($file);
            }
            $exec_time = microtime(true) - $time_start;

            if ($skipped) {
                $this->log['notice'][] = "<b>Skipped {$skipped} pages .</b>";
            }
            $this->log['notice'][] = sprintf("<b>Imported {$imported} pages in %.2f seconds.</b>", $exec_time);
            $this->print_messages();
        }

        private function create_post($data, $options, $home_url) {

            $opt_min_backlinks = isset($options['opt_min_backlinks']) ? $options['opt_min_backlinks'] : 0;
            $guid = parse_url($data['URL'], PHP_URL_PATH);
    		$guid = ltrim($guid, '/');
            $data = array_merge($this->defaults, $data);

            $post_content = sprintf('Oops. Page not found. Go to <a href="%s">homepage</a>', $home_url);

            $new_post = array(
                'post_title'    => convert_chars($data['Title']),
    			'post_content'  => $post_content,
                'post_status'   => 'publish',
                'post_type'     => 'page',
    			'guid'          => $guid,
                'post_name'     => convert_chars($data['Title'])
            );

            // create!
            $id = wp_insert_post($new_post);
    		if ($id) {
    			 if ( ! add_post_meta( $id, 'custom_permalink', $guid, true ) ) { 
    				update_post_meta ( $id, 'custom_permalink', $guid);
    			}
    		}
            return $id;
        }

        /**
         * 
         * Delete BOM from UTF-8 file.
         *
         * @param string $fname
         * 
         * @return void
         * 
         */
        private function stripBOM($fname) {

            $res = fopen($fname, 'rb');
            if (false !== $res) {
                $bytes = fread($res, 3);
                if ($bytes == pack('CCC', 0xef, 0xbb, 0xbf)) {
                    $this->log['notice'][] = 'Getting rid of byte order mark...';
                    fclose($res);

                    $contents = file_get_contents($fname);
                    if (false === $contents) {
                        trigger_error('Failed to get file contents.', E_USER_WARNING);
                    }
                    $contents = substr($contents, 3);
                    $success = file_put_contents($fname, $contents);
                    if (false === $success) {
                        trigger_error('Failed to put file contents.', E_USER_WARNING);
                    }
                } else {
                    fclose($res);
                }
            } else {
                $this->log['error'][] = 'Failed to open file, aborting.';
            }
        }
    }


    function ebn_admin_menu() {
        $plugin = new Rebuild_Pages_From_Majestic;
        add_management_page('edit.php', 'Rebuild Pages', 'manage_options', __FILE__,
            array($plugin, 'form'));
    }

    add_action('admin_menu', 'ebn_admin_menu');

?>