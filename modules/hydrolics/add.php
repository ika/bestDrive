<?php

// hydrolics|add

if (!defined('MULTIDB_MODULE') || !USER_AUTHENTICATED) {
	die("UNAUTHORIZED ACCESS");
} else {

	$status = 'error';
	$msg = 'error';

	if (isset($_POST)) {

		$data = array();

		$data['partid'] = strtoupper(trim($_POST['record']['partid']));
		$data['partname'] = filter_var(trim($_POST['record']['partname']), FILTER_SANITIZE_STRING); 
		$data['size'] = filter_var(trim($_POST['record']['size']), FILTER_SANITIZE_STRING);
		$data['descr'] = filter_var(trim($_POST['record']['descr']), FILTER_SANITIZE_STRING);
		$data['date'] = trim($_POST['record']['date']);
		$data['cost'] = trim($_POST['record']['cost']);
		$data['onhand'] = trim($_POST['record']['onhand']);


		$data['cost'] = number_format($data['cost'], 2, '.', '');

		$class = new Hydrolics();

		if ($class->addHydrolics($data)) {
			$status = 'success';
		} else {
			$msg = 'ERROR: not added';
		}
	}

	$response = json_encode(array("status" => "$status", "message" => "$msg"));

	exit($response);
}
?>
