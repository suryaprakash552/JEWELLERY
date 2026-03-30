<?php
namespace Opencart\Admin\Model\Employee1;
/**
 * Class User Group
 *
 * Can be loaded using $this->load->model('user/user_group');
 *
 * @package Opencart\Admin\Model\User
 */
class DictionaryGroup extends \Opencart\System\Engine\Model {
	/**
	 * Add User Group
	 *
	 * Create a new user group record in the database.
	 *
	 * @param array<string, mixed> $data array of data
	 *
	 * @return int
	 *
	 * @example
	 *
	 * $user_group_data = [
	 *     'name'       => 'User Group Name',
	 *     'permission' => ''
	 * ];
	 *
	 * $this->load->model('user/user_group');
	 *
	 * $user_group_id = $this->model_user_user_group->addUserGroup($user_group_data);
	 */
	public function addDictionaryGroup(array $data): int {
		$this->db->query("INSERT INTO `" . DB_PREFIX . "dictionary_group` SET `name` = '" . $this->db->escape((string)$data['name']) . "', `permission` = '" . (isset($data['permission']) ? $this->db->escape(json_encode($data['permission'])) : '') . "'");

		return $this->db->getLastId();
	}

	/**
	 * Edit User Group
	 *
	 * Edit user group record in the database.
	 *
	 * @param int                  $user_group_id primary key of the user group record
	 * @param array<string, mixed> $data          array of data
	 *
	 * @return void
	 *
	 * @example
	 *
	 * $user_group_data = [
	 *     'name'       => 'User Group Name',
	 *     'permission' => ''
	 * ];
	 *
	 * $this->load->model('user/user_group');
	 *
	 * $this->model_user_user_group->editUserGroup($user_group_id, $user_group_data);
	 */
	public function editDictionaryGroup(int $dictionary_group_id, array $data): void {
		$this->db->query("UPDATE `" . DB_PREFIX . "dictionary_group` SET `name` = '" . $this->db->escape((string)$data['name']) . "', `permission` = '" . (isset($data['permission']) ? $this->db->escape(json_encode($data['permission'])) : '') . "' WHERE `dictionary_group_id` = '" . (int)$dicrionary_group_id . "'");
	}

	/**
	 * Delete User Group
	 *
	 * Delete user group record in the database.
	 *
	 * @param int $user_group_id primary key of the user group record
	 *
	 * @return void
	 *
	 * @example
	 *
	 * $this->load->model('user/user_group');
	 *
	 * $this->model_user_user_group->deleteUserGroup($user_group_id);
	 */
	public function deleteDictionaryGroup(int $dictionary_group_id): void {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "Dictionary_group` WHERE `dictionary_group_id` = '" . (int)$dictionary_group_id . "'");
	}

	/**
	 * Get User Group
	 *
	 * Get the record of the user group record in the database.
	 *
	 * @param int $user_group_id primary key of the user group record
	 *
	 * @return array<string, mixed> user group record that has user group ID
	 *
	 * @example
	 *
	 * $this->load->model('user/user_group');
	 *
	 * $user_group_info = $this->model_user_user_group->getUserGroup($user_group_id);
	 */
	public function getDictionaryGroup(int $dictionary_group_id): array {
		$query = $this->db->query("SELECT DISTINCT * FROM `" . DB_PREFIX . "dictionary_group` WHERE `dictionary_group_id` = '" . (int)$dictionary_group_id . "'");

		return ['permission' => $query->row['permission'] ? json_decode($query->row['permission'], true) : []] + $query->row;
	}

	/**
	 * Get User Groups
	 *
	 * Get the record of the user group records in the database.
	 *
	 * @param array<string, mixed> $data array of filters
	 *
	 * @return array<int, array<string, mixed>> user group records
	 *
	 * @example
	 *
	 * $filter_data = [
	 *     'sort'  => 'name',
	 *     'order' => 'DESC',
	 *     'start' => 0,
	 *     'limit' => 10
	 * ];
	 *
	 * $this->load->model('user/user_group');
	 *
	 * $results = $this->model_user_user_group->getUserGroups($filter_data);
	 */
	public function getDictionaryGroups(array $data = []): array {
		$sql = "SELECT * FROM `" . DB_PREFIX . "dictionary_group` ORDER BY `name`";

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$query = $this->db->query($sql);

		return $query->rows;
	}

	/**
	 * Get Total User Groups
	 *
	 * Get the total number of total user group records in the database.
	 *
	 * @return int total number of user group records
	 *
	 * @example
	 *
	 * $this->load->model('user/user_group');
	 *
	 * $user_group_total = $this->model_user_user_group->getTotalUserGroups();
	 */
	public function getTotalDictionaryGroups(): int {
		$query = $this->db->query("SELECT COUNT(*) AS `total` FROM `" . DB_PREFIX . "dictionary_group`");

		return (int)$query->row['total'];
	}

	/**
	 * Add Permission
	 *
	 * Create and edit new user permission record in the database.
	 *
	 * @param int    $user_group_id primary key of the user group record
	 * @param string $type
	 * @param string $route
	 *
	 * @return void
	 *
	 * @example
	 *
	 * $this->load->model('user/user_group');
	 *
	 * $this->model_user_user_group->addPermission($user_group_id, $type, $route);
	 */
	public function addPermission(int $dictionary_group_id, string $type, string $route): void {
		$dictionary_group_query = $this->db->query("SELECT DISTINCT * FROM `" . DB_PREFIX . "dictionary_group` WHERE `dictionary_group_id` = '" . (int)$dictionary_group_id . "'");

		if ($dictionary_group_query->num_rows) {
			$data = $dictionary_group_query->row['permission'] ? json_decode($dictionary_group_query->row['permission'], true) : [];

			$data[$type][] = $route;

			$this->db->query("UPDATE `" . DB_PREFIX . "dictionary_group` SET `permission` = '" . $this->db->escape(json_encode($data)) . "' WHERE `dicionary_group_id` = '" . (int)$dictionary_group_id . "'");
		}
	}

	/**
	 * Remove Permission
	 *
	 * Delete user permission record in the database.
	 *
	 * @param int    $user_group_id primary key of the user group record
	 * @param string $type
	 * @param string $route
	 *
	 * @return void
	 *
	 * @example
	 *
	 * $this->load->model('user/user_group');
	 *
	 * $this->model_user_user_group->removePermission($user_group_id, $type, $route);
	 */
	public function removePermission(int $dictionary_group_id, string $type, string $route): void {
		$user_group_query = $this->db->query("SELECT DISTINCT * FROM `" . DB_PREFIX . "dictionary_group` WHERE `dictionary_group_id` = '" . (int)$dictionary_group_id . "'");

		if ($dictionary_group_query->num_rows) {
			$data = $dictionary_group_query->row['permission'] ? json_decode($dictionary_group_query->row['permission'], true) : [];

			if (isset($data[$type])) {
				$data[$type] = array_diff($data[$type], [$route]);
			}

			$this->db->query("UPDATE `" . DB_PREFIX . "dictionary_group` SET `permission` = '" . $this->db->escape(json_encode($data)) . "' WHERE `dictionary_group_id` = '" . (int)$dictionary_group_id . "'");
		}
	}
}
