<?php
class ModelShippingLitemfDelivery extends Model {
	function getQuote($address) {
		$this->load->language('shipping/litemf_delivery');

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$this->config->get('pickup_geo_zone_id') . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");

		if (!$this->config->get('pickup_geo_zone_id')) {
			$status = true;
		} elseif ($query->num_rows) {
			$status = true;
		} else {
			$status = false;
		}

		$method_data = array();

		if ($status) {
			$quote_data = array();

			$quote_data['litemf_delivery'] = array(
				'code'         => 'litemf_delivery.litemf_delivery',
				'title'        => $this->language->get('text_description'),
				'cost'         => 0.00,
				'tax_class_id' => 0,
				'text'         => $this->currency->format(0.00, $this->session->data['currency'])
			);

			$method_data = array(
				'code'       => 'litemf_delivery',
				'title'      => $this->language->get('text_title'),
				'quote'      => $quote_data,
				'sort_order' => $this->config->get('litemf_delivery_sort_order'),
				'error'      => false
			);
		}

		return $method_data;
	}

	public function addOrderLitemf($data) {
		$poinId = $data['passport']['delivery_point_id'] == 'courier' ? null : $data['passport']['delivery_point_id'];
		$this->db->query("INSERT INTO `" . DB_PREFIX . "litemf_orders` SET status = 'unsend', user_id = '" . (int)$data['user_id'] . "', order_id = '" . $this->db->escape($data['order_id']) . "', delivery_method_id = '" . $data['passport']['delivery_method_id'] . "', delivery_point_id = '" . $poinId . "'");
		$litemf_order_id = $this->db->getLastId();
		$this->db->query("INSERT INTO `" . DB_PREFIX . "litemf_address` SET litemf_orders = '" . (int)$litemf_order_id . "', first_name = '" . $data['passport']['first_name'] . "', last_name = '" . $data['passport']['last_name'] . "', middle_name = '" . $data['passport']['middle_name'] . "', street = '" . $data['passport']['street'] . "', house = '" . $data['passport']['house'] . "', city = '" . $data['passport']['city'] . "', region = '" . $data['passport']['region'] . "', zip_code = '" . $data['passport']['zip_code'] . "', phone = '" . $data['passport']['phone'] . "', series = '" . $data['passport']['series'] . "', number = '" . $data['passport']['number'] . "', issue_date = '" . $data['passport']['issue_date'] . "', issued_by = '" . $data['passport']['issued_by'] . "'");
		if (isset($data['courier']) && is_null($poinId)) {
			$this->db->query("INSERT INTO `" . DB_PREFIX . "litemf_courier_address` SET litemf_orders = '" . (int)$litemf_order_id . "', street = '" . $data['courier']['street'] . "', house = '" . $data['courier']['house'] . "', email = '" . $data['courier']['email'] . "', phone = '" . $data['courier']['phone'] . "', number = '" . $data['courier']['number'] . "'");
		}
	}
}