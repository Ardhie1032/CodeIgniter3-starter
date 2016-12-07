<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Sample extends CI_Controller {

	public function index()
	{
        $this->view->useTemplate();
        $this->view->render();
    }

	public function green()
	{
        $data = [];
        $this->view->useTemplate($data, 'green');
        $this->view->render('home/index');
    }

	public function codeigniter()
	{
        $data = [];
        $this->view->useTemplate('codeigniter', $data);
        $this->view->render('welcome_message');
    }

	public function sbadmin()
	{
        $this->view->useTemplate('sbadmin');
        $this->view->render('welcome_message');
    }

	public function signin()
	{
        $this->view->useTemplate('bootstrap', [], 'signin');
        $this->view->render();
    }

	public function starter()
	{
        $this->view
            ->cache(60)
            ->useTemplate('bootstrap', [], 'starter')
            ->render('sample/index');
    }

}
