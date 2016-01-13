<?php

class RebuildPagesMajesticPostInsertionTest extends WP_UnitTestCase {

	private $test_files = array('1.csv', '2.csv');
	private $tmp_files = array();

	/**
	 * 
	 * Tests if posts returned by CSV Parser that will be inserted are correct, checks if we have
	 * an inpartial file and that the posts we will add equal 0.
	 * 
	 * @return [void]
	 * 
	 */
	function test_csv_parser() {

		echo 'Testing CSVParser class ...';
		
		$desired_outputs = array(
			0,
			5
		);
		
		$log = array();
		$options = array(
			'opt_min_backlinks' => '0'
		);

		$csvParser = new rebuildPagesMajestic_CSVParser($log, $options);

		$this->prepare_test_data();

		foreach($this->tmp_files as $key => $file) {
			$_FILES['ebn_import']['tmp_name'] = $file;
			$posts = $csvParser->parseUploadedMajesticFile();
			print 'Testing file ' . $this->test_files[$key];
			$this->assertEquals($desired_outputs[$key], count($posts));
		}

	}

	/**
	 * 
	 * Test if posts returned by CSV Parser that will be inserted are correct. This test checks
	 * if the minimal backlink criteria works.
	 * 
	 * @return [void]
	 * 
	 */
	function test_external_backlinks() {

		echo 'Testing external backlinks ...';

		$desired_outputs = array(
			0,
			4
		);

		$log = array();
		$options = array(
			'opt_min_backlinks' => '5'
		);

		$csvParser = new rebuildPagesMajestic_CSVParser($log, $options);

		$this->prepare_test_data();

		foreach($this->tmp_files as $key => $file) {
			$_FILES['ebn_import']['tmp_name'] = $file;
			$posts = $csvParser->parseUploadedMajesticFile();
			print 'Testing file ' . $this->test_files[$key];
			$this->assertEquals($desired_outputs[$key], count($posts));
		}

	}

	/**
	 * 
	 * Prepares test data, copies all files from testcases directory to tmp directory.
	 * This is needed, because the CSV parser unlinks the files afterwards.
	 * 
	 * @return [void]
	 * 
	 */
	function prepare_test_data() {
		$this->tmp_files = array();
		foreach($this->test_files as $key => $file) {
			$f = getcwd() . '/tests/testcases/' . $file;
			$tmp_f = '/tmp/tmp_' . $file;
			copy($f, $tmp_f);
			array_push($this->tmp_files, $tmp_f);
		}
	}

}

?>
