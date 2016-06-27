<?php
namespace Niteoweb\RebuildPages;


function convert_chars( $s ) {
	return $s;
}

function unlink( $s ) {
	return $s;
}

class TestApp extends \PHPUnit_Framework_TestCase {

	private $test_files = array( 'fixtures/1.csv', 'fixtures/2.csv' );

	function test_csv() {
		$desired_outputs = array(
			6,
			5
		);

		$log       = array();
		$options   = array( "opt_min_backlinks" => 1 );
		$csvParser = new CSVParser( $log, $options );

		foreach ( $this->test_files as $key => $file ) {
			$_FILES['ebn_import']['tmp_name'] = $file;
			$posts                            = $csvParser->parseUploadedMajesticFile();
			$this->assertEquals( $desired_outputs[ $key ], count( $posts ) );
		}

	}

}