<?php

class IndexController extends \Base\WeixinController {

	public function indexAction() {
		$data = $this->getSource();
		
		echo '<pre>';
		print_r($data);
		exit();
	}
}