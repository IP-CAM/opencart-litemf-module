<?php
class ControllerShippingLitemfDelivery extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('shipping/litemf_delivery');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('litemf_delivery', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('extension/shipping', 'token=' . $this->session->data['token'], true));
		}

		$data['heading_title'] = $this->language->get('heading_title');
		
		$data['text_edit'] = $this->language->get('text_edit');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_all_zones'] = $this->language->get('text_all_zones');
		$data['text_none'] = $this->language->get('text_none');

		$data['entry_geo_zone'] = $this->language->get('entry_geo_zone');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_sort_order'] = $this->language->get('entry_sort_order');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_shipping'),
			'href' => $this->url->link('extension/shipping', 'token=' . $this->session->data['token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('shipping/pickup', 'token=' . $this->session->data['token'], true)
		);

		$data['action'] = $this->url->link('shipping/litemf_delivery', 'token=' . $this->session->data['token'], true);

		$data['cancel'] = $this->url->link('extension/shipping', 'token=' . $this->session->data['token'], true);

		if (isset($this->request->post['litemf_delivery_geo_zone_id'])) {
			$data['litemf_geo_zone_id'] = $this->request->post['litemf_delivery_geo_zone_id'];
		} else {
			$data['litemf_delivery_geo_zone_id'] = $this->config->get('litemf_delivery_geo_zone_id');
		}

		$this->load->model('localisation/geo_zone');

		$data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

		if (isset($this->request->post['litemf_delivery_status'])) {
			$data['litemf_delivery_status'] = $this->request->post['litemf_delivery_status'];
		} else {
			$data['litemf_delivery_status'] = $this->config->get('litemf_delivery_status');
		}

		if (isset($this->request->post['litemf_delivery_sort_order'])) {
			$data['litemf_delivery_sort_order'] = $this->request->post['litemf_delivery_sort_order'];
		} else {
			$data['litemf_delivery_sort_order'] = $this->config->get('litemf_delivery_sort_order');
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('shipping/litemf_delivery', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'shipping/litemf_delivery')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}