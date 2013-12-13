<?php

abstract class upgrade_abstract_v3
{
	protected $has_remote_data = false;
	
	public function loadRemoteData()
	{
		if (!$this->has_remote_data) {
			return array();
		}

		if (defined('DESKPRO_DEBUG_DEVELOPERMODE')) {
			$file = ROOT . '/dev/upgrade_data/' . $this->version_number . '.php';
			return include($file);
		}

		global $db;

		$result = $db->query_return("SELECT data FROM data WHERE name = 'remote_upgrade_data'");

		if (!$result) {
			return $this->_fetchRemoteUpgradeData();
		}

		$data = unserialize($result['data']);
		if ($data['version'] != $this->version_number) {
			return $this->_fetchRemoteUpgradeData();
		}

		return $data['data'];
	}

	protected function _fetchRemoteUpgradeData()
	{
		global $db;
		
		$data = get_deskpro_software_data('upgrade_data', array('version' => $this->version_number));

		if (!$data) {
			throw new Exception('Error fetching upgrade data', 9001);
		}

		$db->query_delete('data', "name='remote_upgrade_data'");
		$db->query_insert('data', array(
			'name' => 'remote_upgrade_data',
			'data' => serialize(array('data' => $data, 'version' => $this->version_number))
		));

		return $data;
	}
}