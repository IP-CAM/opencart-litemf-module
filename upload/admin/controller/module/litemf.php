<?php

class ControllerModuleLitemf extends Controller
{
	private $error = array();

	public function index()
	{
		$this->load->language('module/litemf');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');
		$this->load->model('module/litemf');
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('litemf', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('extension/module', 'token=' . $this->session->data['token'], true));
		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_status'] = $this->language->get('text_status');

		$data['entry_description'] = $this->language->get('entry_description');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_set_order'] = $this->language->get('entry_set_order');
		$data['entry_get_order'] = $this->language->get('entry_get_order');
		$data['entry_delivery_order'] = $this->language->get('entry_delivery_order');
		$data['entry_api'] = $this->language->get('entry_api');
		$data['entry_time'] = $this->language->get('entry_time');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['litemf_get_order'])) {
			$data['error_litemf_get_order'] = $this->error['litemf_get_order'];
		} else {
			$data['error_litemf_get_order'] = '';
		}

		if (isset($this->error['litemf_set_order'])) {
			$data['error_litemf_set_order'] = $this->error['litemf_set_order'];
		} else {
			$data['error_litemf_set_order'] = '';
		}

		if (isset($this->error['litemf_delivery_order'])) {
			$data['error_litemf_delivery_order'] = $this->error['litemf_delivery_order'];
		} else {
			$data['error_litemf_delivery_order'] = '';
		}

		if (isset($this->error['litemf_api_key'])) {
			$data['error_litemf_api_key'] = $this->error['litemf_api_key'];
		} else {
			$data['error_litemf_api_key'] = '';
		}

		if (isset($this->error['litemf_timer'])) {
			$data['error_litemf_timer'] = $this->error['litemf_timer'];
		} else {
			$data['error_litemf_timer'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_module'),
			'href' => $this->url->link('extension/module', 'token=' . $this->session->data['token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('module/litemf', 'token=' . $this->session->data['token'], true)
		);

		$data['action'] = $this->url->link('module/litemf', 'token=' . $this->session->data['token'], true);

		$data['cancel'] = $this->url->link('extension/module', 'token=' . $this->session->data['token'], true);

		if (isset($this->request->post['litemf_get_order'])) {
			$data['litemf_get_order'] = $this->request->post['litemf_get_order'];
		} else {
			$data['litemf_get_order'] = $this->config->get('litemf_get_order');
		}

		if (isset($this->request->post['litemf_set_order'])) {
			$data['litemf_set_order'] = $this->request->post['litemf_set_order'];
		} else {
			$data['litemf_set_order'] = $this->config->get('litemf_set_order');
		}

		if (isset($this->request->post['litemf_delivery_order'])) {
			$data['litemf_delivery_order'] = $this->request->post['litemf_delivery_order'];
		} else {
			$data['litemf_delivery_order'] = $this->config->get('litemf_delivery_order');
		}

		if (isset($this->request->post['litemf_api_key'])) {
			$data['litemf_api_key'] = $this->request->post['litemf_api_key'];
			$this->load->controller('module/litemf_api');
		} else {
			$data['litemf_api_key'] = $this->config->get('litemf_api_key');
		}

		if (isset($this->request->post['litemf_timer'])) {
			$data['litemf_timer'] = $this->request->post['litemf_timer'];
		} else {
			$data['litemf_timer'] = $this->config->get('litemf_timer');
		}

		$data['token'] = $this->session->data['token'];
		$data['order_list'] = $this->model_module_litemf->getOrderStatusList();
		$data['litemf_order_list'] = $this->model_module_litemf->getLitemfPackage();
		$data['litemf_order_list_send'] = $this->model_module_litemf->getLitemfPackageSend();
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('module/litemf', $data));
	}

	protected function validate()
	{
		if (!$this->user->hasPermission('modify', 'module/litemf')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	public function getOrderDetails()
	{
		$this->load->model('module/litemf');
		$package = $this->model_module_litemf->getLitemfPackageById($this->request->get['order_id']);
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($package));
	}

	public function saveOrderDetails()
	{
		$this->load->model('module/litemf');
		$this->model_module_litemf->updateLitemfPackage($this->request->get['litemf']);
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode('Success'));
	}

	public function sendPackage()
	{
		$response = [];
		$this->load->model('module/litemf');
		$apiKey = $this->config->get('litemf_api_key');
		$incomingPackages = $this->model_module_litemf->createIncomingPackages($this->request->get['order_id']);
		foreach ($incomingPackages as $package) {
			$respons = $this->model_module_litemf->sendRequest($package, $apiKey);
			$response[] = $respons->result->incoming_package;
		}
		$address = $this->model_module_litemf->createAddress($this->request->get['order_id']);
		$addressResponse = $this->model_module_litemf->sendRequest($address, $apiKey);
		$this->model_module_litemf->updateLitemfOrder($this->request->get['order_id'], $addressResponse->result->address, implode(',',$response));

		$createOutgoingPackage = $this->model_module_litemf->createOutgoingPackage($this->request->get['order_id']);
        $createOutgoingPackageId = $this->model_module_litemf->sendRequest($createOutgoingPackage, $apiKey);
        $this->model_module_litemf->updateLitemfOrderStatus($this->request->get['order_id']);
		$this->model_module_litemf->updateLitemfOrderOutgoingPackageId($this->request->get['order_id'], $createOutgoingPackageId->result->outgoing_package);
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode('success'));
	}
}