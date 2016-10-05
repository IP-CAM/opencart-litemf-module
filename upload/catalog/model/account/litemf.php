<?php
class ModelAccountLitemf extends Model {
	public function addAddress($user_id, $data) {
        $date = date_create($data['issue_date']);
        $date = date_format($date, 'Y-m-d H:i:s');
		$this->db->query("DELETE FROM " . DB_PREFIX . "litemf_address WHERE user_id = '" . (int)$user_id . "'");

		$this->db->query("INSERT INTO " . DB_PREFIX . "litemf_address SET customer_id = '" . (int)$this->customer->getId() . "', firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', company = '" . $this->db->escape($data['company']) . "', address_1 = '" . $this->db->escape($data['address_1']) . "', address_2 = '" . $this->db->escape($data['address_2']) . "', postcode = '" . $this->db->escape($data['postcode']) . "', city = '" . $this->db->escape($data['city']) . "', zone_id = '" . (int)$data['zone_id'] . "', country_id = '" . (int)$data['country_id'] . "', custom_field = '" . $this->db->escape(isset($data['custom_field']) ? json_encode($data['custom_field']) : '') . "'");
        $this->db->query("INSERT INTO `" . DB_PREFIX . "litemf_address` SET user_id = '" . (int)$user_id . "', first_name = '" . $data['first_name'] . "', last_name = '" . $data['last_name'] . "', middle_name = '" . $data['middle_name'] . "', street = '" . $data['street'] . "', house = '" . $data['house'] . "', city = '" . $data['city'] . "', region = '" . $data['region'] . "', zip_code = '" . $data['zip_code'] . "', phone = '" . $data['phone'] . "', series = '" . $data['series'] . "', number = '" . $data['number'] . "', issue_date = '" . $date . "', issued_by = '" . $data['issued_by'] . "'");

		$address_id = $this->db->getLastId();

		return $address_id;
	}

	public function getAddress($user_id) {
		$address_query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "litemf_address WHERE user_id = '" . (int)$user_id . "' AND customer_id = '" . (int)$this->customer->getId() . "'");

		if ($address_query->num_rows) {
			return $address_query->rows;
		} else {
			return false;
		}
	}
}