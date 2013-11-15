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
		header($url);
	}

	public abstract function defaultAction();
}

?>
