<?php

class TestApp extends PHPUnit_Framework_TestCase
{

    private $test_files = array('1.csv', '2.csv');
    private $tmp_files = array();


    function test_csv()
    {
        $desired_outputs = array(
            0,
            5
        );

        $log = array();
        $options = array();
        $csvParser = new Niteoweb\RebuildPages\CSVParser($log, $options);

        foreach ($this->tmp_files as $key => $file) {
            $_FILES['ebn_import']['tmp_name'] = $file;
            $posts = $csvParser->parseUploadedMajesticFile();
            $this->assertEquals($desired_outputs[$key], count($posts));
        }

    }

}