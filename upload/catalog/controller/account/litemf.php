<?php
class ControllerAccountLitemf extends Controller {
    public function index() {
        if (!$this->customer->isLogged()) {
            $this->session->data['redirect'] = $this->url->link('account/address', '', true);

            $this->response->redirect($this->url->link('account/login', '', true));
        }
        $this->load->model('account/litemf');
        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {

            $this->model_account_litemf->addAddress($this->customer->getId(), $this->request->post);
        }
        $this->load->language('account/litemf');
        $data['action'] = $this->url->link('account/litemf', '', true);

        $this->document->setTitle($this->language->get('heading_title'));

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home')
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_account'),
            'href' => $this->url->link('account/account', '', true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('account/litemf', '', true)
        );

        $data['heading_title'] = $this->language->get('heading_title');

        $data['text_edit_address'] = $this->language->get('text_edit_address');
        $data['text_yes'] = $this->language->get('text_yes');
        $data['text_no'] = $this->language->get('text_no');
        $data['text_select'] = $this->language->get('text_select');
        $data['text_none'] = $this->language->get('text_none');
        $data['text_loading'] = $this->language->get('text_loading');

        $data['entry_first_name'] = $this->language->get('entry_first_name');
        $data['entry_last_name'] = $this->language->get('entry_last_name');
        $data['entry_middle_name'] = $this->language->get('entry_middle_name');
        $data['entry_phone'] = $this->language->get('entry_phone');
        $data['entry_zip_code'] = $this->language->get('entry_zip_code');
        $data['entry_region'] = $this->language->get('entry_region');
        $data['entry_city'] = $this->language->get('entry_city');
        $data['entry_street'] = $this->language->get('entry_street');
        $data['entry_house'] = $this->language->get('entry_house');
        $data['entry_series'] = $this->language->get('entry_series');
        $data['entry_number'] = $this->language->get('entry_number');
        $data['entry_issue_date'] = $this->language->get('entry_issue_date');
        $data['entry_issued_by'] = $this->language->get('entry_issued_by');

        $data['button_continue'] = $this->language->get('button_continue');
        $data['button_back'] = $this->language->get('button_back');
        $data['button_upload'] = $this->language->get('button_upload');
        $address_info = $this->model_account_litemf->getAddress($this->customer->getId());
        if (isset($this->request->post['first_name'])) {
            $data['first_name'] = $this->request->post['first_name'];
        } elseif (!empty($address_info)) {
            $data['first_name'] = $address_info['first_name'];
        } else {
            $data['first_name'] = '';
        }

        if (isset($this->request->post['last_name'])) {
            $data['last_name'] = $this->request->post['last_name'];
        } elseif (!empty($address_info)) {
            $data['last_name'] = $address_info['last_name'];
        } else {
            $data['last_name'] = '';
        }

        if (isset($this->request->post['middle_name'])) {
            $data['middle_name'] = $this->request->post['middle_name'];
        } elseif (!empty($address_info)) {
            $data['middle_name'] = $address_info['middle_name'];
        } else {
            $data['middle_name'] = '';
        }

        if (isset($this->request->post['phone'])) {
            $data['phone'] = $this->request->post['phone'];
        } elseif (!empty($address_info)) {
            $data['phone'] = $address_info['phone'];
        } else {
            $data['phone'] = '';
        }

        if (isset($this->request->post['zip_code'])) {
            $data['zip_code'] = $this->request->post['zip_code'];
        } elseif (!empty($address_info)) {
            $data['zip_code'] = $address_info['zip_code'];
        } else {
            $data['zip_code'] = '';
        }

        if (isset($this->request->post['region'])) {
            $data['region'] = $this->request->post['region'];
        } elseif (!empty($address_info)) {
            $data['region'] = $address_info['region'];
        } else {
            $data['region'] = '';
        }

        if (isset($this->request->post['city'])) {
            $data['city'] = $this->request->post['city'];
        } elseif (!empty($address_info)) {
            $data['city'] = $address_info['city'];
        } else {
            $data['city'] = '';
        }

        if (isset($this->request->post['street'])) {
            $data['street'] = $this->request->post['street'];
        } elseif (!empty($address_info)) {
            $data['street'] = $address_info['street'];
        } else {
            $data['street'] = '';
        }

        if (isset($this->request->post['house'])) {
            $data['house'] = $this->request->post['house'];
        } elseif (!empty($address_info)) {
            $data['house'] = $address_info['house'];
        } else {
            $data['house'] = '';
        }

        if (isset($this->request->post['series'])) {
            $data['series'] = $this->request->post['series'];
        } elseif (!empty($address_info)) {
            $data['series'] = $address_info['series'];
        } else {
            $data['series'] = '';
        }

        if (isset($this->request->post['number'])) {
            $data['number'] = $this->request->post['number'];
        } elseif (!empty($address_info)) {
            $data['number'] = $address_info['number'];
        } else {
            $data['number'] = '';
        }

        if (isset($this->request->post['issue_date'])) {
            $data['issue_date'] = $this->request->post['issue_date'];
        } elseif (!empty($address_info)) {
            $data['issue_date'] = $address_info['issue_date'];
        } else {
            $data['issue_date'] = '';
        }

        if (isset($this->request->post['issued_by'])) {
            $data['issued_by'] = $this->request->post['issued_by'];
        } elseif (!empty($address_info)) {
            $data['issued_by'] = $address_info['issued_by'];
        } else {
            $data['issued_by'] = '';
        }

        $data['column_left'] = $this->load->controller('common/column_left');
        $data['column_right'] = $this->load->controller('common/column_right');
        $data['content_top'] = $this->load->controller('common/content_top');
        $data['content_bottom'] = $this->load->controller('common/content_bottom');
        $data['footer'] = $this->load->controller('common/footer');
        $data['header'] = $this->load->controller('common/header');


        $this->response->setOutput($this->load->view('account/litemf', $data));
    }
}