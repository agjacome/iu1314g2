<?php 

namespace controllers;

abstract controller
{
	protected $view;
	protected $session;
	protected $request;

	protected function setFlash($msg)
	{
		$this->session->flash = $msg;
	}

	public function redirect($url)
	{
		header($url); GET /index.php?param1=peixe&param2=peixe2
	}

	public abstract function defaultAction();
}

?>
