<?php
class Uri_routes extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->library('user');
		$this->load->model('Design_model');	    
	}

	public function index() {
			
		if (!$this->user->islogged()) {  
  			redirect('admin/login');
		}

    	if (!$this->user->hasPermissions('access', 'admin/uri_routes')) {
  			redirect('admin/permission');
		}
			
		if ($this->session->flashdata('alert')) {
			$data['alert'] = $this->session->flashdata('alert');
		} else { 
			$data['alert'] = '';
		}		
				
		$data['heading'] 			= 'URI Routes';
		$data['sub_menu_save'] 	= 'Save';

		if ($this->input->post('routes')) {
			$routes = $this->input->post('routes');
		} else {
			$routes = $this->Design_model->getRoutes();
		}
		
		$data['routes'] = array();
		foreach ($routes as $route) {
	
			$data['routes'][] = array(
				'route_id'		=> $route['uri_route_id'],
				'route'			=> $route['route'],
				'controller' 	=> $route['controller'],
				'priority' 		=> $route['priority'],
				'status' 		=> $route['status']
			);
		}

		if ($this->input->post() && $this->_updateRoute() === TRUE){
						
			redirect('admin/uri_routes');
		}
		
		$regions = array(
			'admin/header',
			'admin/footer'
		);
		
		$this->template->regions($regions);
		$this->template->load('admin/uri_routes', $data);
	}

	public function _updateRoute() {
						
    	if (!$this->user->hasPermissions('modify', 'admin/uri_routes')) {
		
			$this->session->set_flashdata('alert', '<p class="warning">Warning: You do not have permission to modify!</p>');
			return TRUE;
    	
    	} else if ($this->input->post('routes')) { 
			
			$update = array();
			
			$update = $this->input->post('routes');

			if ($this->Design_model->updateRoutes($update)) {
		
				$this->session->set_flashdata('alert', '<p class="success">URI Routes Updated Sucessfully!</p>');
			} else {
		
				$this->session->set_flashdata('alert', '<p class="warning">Nothing Updated!</p>');				
			}
		
			return TRUE;
		}
	}
}