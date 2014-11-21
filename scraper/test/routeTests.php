<?php
require_once('simpletest/autorun.php');
require_once('../routes.php');

class RouteTests extends UnitTestCase {
	function testLogCreatesNewFileOnFirstMessage() {
        	/*@unlink('/temp/test.log');
	        $log = new Log('/temp/test.log');
		$this->assertFalse(file_exists('/temp/test.log'));
		$log->message('Should write this to a file');
		$this->assertTrue(file_exists('/temp/test.log'));*/
		RouteScraper::ListRoutes();
		$this->assertTrue(false);
	}
}
?>
