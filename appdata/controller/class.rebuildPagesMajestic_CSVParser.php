<?php

	class rebuildPagesMajestic_CSVParser {
		
		private $log;
		private $options;

		public function __construct(&$log, &$options) {
			$this->log = &$log;
			$this->options = &$options;
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

		public function parseUploadedMajesticFile() {

            $file = $_FILES['ebn_import']['tmp_name'];
            $this->stripBOM($file);
    		$row = 1;
    		$skipped = 0;
            $imported = 0;
            $home_url = get_home_url();

            $imported_posts = array();

    		if (($handle = fopen($file, "r")) !== FALSE) {
    			$data = fgetcsv($handle, filesize($file));
    			$mappings = $data;
    			if (in_array('ReferringExtBackLinks', $mappings) && in_array('Title', $mappings) && in_array('URL', $mappings)) {
	    			while (($data = fgetcsv($handle, filesize($file))) !== FALSE) {
	    				if($data[0]) {
	    					$line = array();
	    					foreach($data as $key => $value) {
	    						$line[$mappings[$key]] = addslashes($value);
	    					}
	    					if ($line['Title'] && $line['URL'] && $line['ReferringExtBackLinks']) {
		    					$externalBacklinks = convert_chars($line['ReferringExtBackLinks']);
		    					if ($externalBacklinks >= $this->options['opt_min_backlinks']) {
			    					$post = new rebuildPagesMajestic_Post(
			    						addslashes($line['Title']),
			    						addslashes($line['URL'])
			    					);
			    					array_push($imported_posts, $post);    						
		    					}
		    					else {
		    						$skipped++;
		    					}	    						
	    					}
	    				}
	    			}
	    		}
	    		else {
	    			fclose($handle);
	    			return $imported_posts;
	    		}
    			fclose($handle);
    		}
    		else {
    			$this->log['error'][] = 'Failed to load file, aborting.';
    			return $imported_posts;
    		}

    		if (file_exists($file)) {
                @unlink($file);
            }

            if ($skipped) {
                $this->log['notice'][] = "<b>Skipped {$skipped} pages .</b>";
            }

            return $imported_posts;

		}

	}

?>