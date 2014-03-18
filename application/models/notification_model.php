 <?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Notification_model extends CI_Model
{

	function __construct()
	{
		// parent construct
		parent::__construct();
	}

	public function get_notifications()
	{
		return array();
	}

	public function get_admin_notifications()
	{
		if(!$this->login->is_admin())
			return false;

		$this->db->select('SUM(users.new = 1) as new_users');
		$query = $this->db->get('users');
		$res = $query->result();

		return $res[0];
	}

}