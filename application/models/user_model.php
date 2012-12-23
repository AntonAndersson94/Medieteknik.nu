<?php
class User_model extends CI_Model
{

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }

	/**
	 * Validates if the user credentials is correct
	 *
	 * @param  string	$lukasid	The lukas-id of the user, for example abcde123
	 * @param  string	$password	The password in clear text
	 * @return bool
	 */
	function validate($lukasid = '', $password = '')
	{
		$lid = preg_replace("/(@.*)/", "", $lukasid);

		$this->db->where('lukasid', $lid);
		$this->db->where('password_hash', encrypt_password($password));
		$query = $this->db->get('users');
		if($query->num_rows == 1)
		{
			return $query;
		}
		return false;

	}

	/**
	 * Checks if the user has any of the specified privileges
	 *
	 * @param  integer	$user_id	The user id
	 * @param  string	$privilege	The privilege name, ex forum_moderator or array('forum_moderator', 'admin')
	 * @return bool
	 */
	function has_privilege($user_id, $privilege)
	{
		if(!is_array($privilege))
		{
			$thePrivileges = array($privilege);
		} else {
			$thePrivileges = $privilege;
		}
		$first = true;
		array_push($thePrivileges, 'superadmin');

		$this->db->select("*");
		$this->db->from("privileges");
		$this->db->join("users_privileges", "users_privileges.privilege_id = privileges.id", "");
		$this->db->where("users_privileges.user_id", $user_id);
		$this->db->where_in("privileges.privilege_name ", $thePrivileges);

		$query = $this->db->get();
		if($query->num_rows > 0)
		{
			return true;
		}
		return false;
	}

	/**
	 * Fetches all the users
	 *
	 * @param  integer	$pagination	Pagination or not. If so, how many results per page?
	 * @param  integer	$page	The current page.
	 * @return array
	 */
    function get_all_users($pagination = 0, $page = 0, $option = 'all')
    {
    	if($pagination !== 0)
    		$this->db->limit($pagination, ($page * $pagination));

    	// are only disabled users asked for?
    	if ($option == 'disabled')
    		$this->db->where('disabled !=', '0');
    	elseif ($option == 'active')
    		$this->db->where('disabled', '0');

        $query = $this->db->get('users');
        return $query->result();
    }

	/**
	 * Counts all the users
	 *
	 * @return integer
	 */
    function count_all_users()
    {
        $query = $this->db->get('users');
        return $query->num_rows();
    }

	/**
	 * Fetches the user profile of specified user
	 *
	 * @param  integer	$id	The id or the lukasid of the user
	 * @return array 	if no user is found, user_id returns 0
	 */
    function get_user_profile($id)
    {
    	// check if the id is numeric, ie if it is a user id or lukasid
		if(is_numeric($id))
			$where = 'id';
		else
			$where = 'lukasid';

		$this->db->select("*");
		$this->db->from("users");
		$this->db->join("users_data", "users.id = users_data.users_id", "left");
		$this->db->where("users.".$where, $id);
		$this->db->limit(1);
		$query = $this->db->get();
		$res = $query->result();
		if(!$res)
			$res['user_id'] = 0;
		else
			$res = $res[0];
		return $res;
	}

	/**
	 * Fetches the privileges of specied user
	 *
	 * @param  integer	$id		The id of the user
	 * @return array
	 */
	function get_user_privileges($id)
	{
		$this->db->select("*");
		$this->db->from("users_privileges");
		$this->db->join("privileges", "privileges.id = users_privileges.privilege_id", "");
		$this->db->where("users_privileges.user_id", $id);
		$query = $this->db->get();
		return $query->result();
	}

	/**
	 * Checks if specified lukasid exists
	 *
	 * @param  string	$lukasid	The lukasid to check
	 * @return bool
	 */
	function lukasid_exists($lid = '')
	{
		$this->db->where('lukasid', $lid);
		$query = $this->db->get('users');
		if($query->num_rows == 1)
		{
			return true;
		}
		return false;
	}

	/**
	 * Checks if specified userid exists
	 *
	 * @param  int	$uid	The user id to check
	 * @return bool
	 */
	function userid_exists($uid = '')
	{
		if(!is_numeric($uid))
			return false;
		$this->db->where('id', $uid);
		$query = $this->db->get('users');
		if($query->num_rows == 1)
		{
			return true;
		}
		return false;
	}

	/**
	 * Adds a user to the database
	 *
	 * @param  string	$fname		The first name of the user
	 * @param  string	$lname		The last name of the user
	 * @param  string	$lukasid	The lukasid of the user, ex abcde123
	 * @param  string	$password	The password in clear text
	 * @return bool
	 */
	function add_user($fname = '', $lname = '', $lukasid ='', $password = '')
	{
		// fixing and trimming
		$fn = trim(preg_replace("/[^A-Za-z]/", "", $fname ));
		$ln = trim(preg_replace("/[^A-Za-z]/", "", $lname ));
		$lid = trim(preg_replace("/[^A-Za-z0-9]/", "", $lukasid ));
		$pwd = trim($password);

		// check lengths
		if(strlen($fn) > 0 && strlen($ln) > 0 && strlen($lid) == 8 && strlen($pwd) > 5)
		{
			// if lukas_id not exists insert user
			if(!$this->lukasid_exists($lid))
			{
				$data = array(
				   'first_name' => $fn ,
				   'last_name' => $ln,
				   'lukasid' => $lid,
				   'password_hash' => encrypt_password($pwd)
				);
				$q = $this->db->insert('users', $data);
				return $q;
			}
			else
				return false;
		} else {
			// something was not correct
			return false;
		}
	}

	/**
	 * Edit user data
	 *
	 * @param  integer	$id			The user id
	 * @param  integer	$img		The chosen img-id for the user.
	 * @param  string	$web		The user web adress
	 * @param  string	$linkedin	The user LinkedIn-profile
	 * @param  string	$twitter	The users Twitter-id
	 * @param  string	$presentation 	The user presentation text
	 * @return mixed 				true if success, array of bool if fail
	 */
	function edit_user_data($id, $web = '', $linkedin = '', $twitter = '', $presentation = '', $img = '')
	{
		//check if the user exists, quit function if not
		if(!userid_exists($id))
			return false;

		// fixing and trimming
		$twitter = preg_replace("/[^0-9A-Za-z_]/", "", $twitter );
		$web = valid_url($web);
		$linkedin = valid_url($linkedin);
		$presentation = trim($presentation);

		// validate
		if(strlen($web) <= 300 && (preg_match("(?i)\b(?:http[s]?://)?(?(?=www.)www.)(?:[-a-z\d]+\.)+[a-z]{2,4}", $web)
			|| strlen($web) == 0) && strlen($twitter) <= 300 &&
			strlen($linkedin) <= 300 && strlen($presentation) <= 1000)
		{
			//set data to be updated/inserted
			$data = array(
					'users_id' => $id,
					'image_id' => $img,
					'web' => $web,
					'linkedin' => $linkedin,
					'presentation' => $presentation,
					'twitter' => $twitter
					);

			// search for user data
			$this->db->where('users_id', $id);
			$find = $this->db->get('users_data');

			// update or insert?
			if($find->num_rows == 1) // update
				$q = $this->db->update('users_data', $data);
			else // insert
				$q = $this->db->insert('users_data', $data);

			// return result from query
			return $q;
		}
		else
			return false;
	}
}

