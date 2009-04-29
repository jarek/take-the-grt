<?php

include_once 'headers.php';

include_once '../db_passwd.php';

class Database
{
	private static $user;
	private static $pass;

	private static $conn = false;

	private $query_count;

	function Open()
	{
		global $db_user; // defined in db_passwd.php
		global $db_pass; // defined in db_passwd.php
		self::$user = $db_user;
		self::$pass = $db_pass;

		self::$conn = new mysqli('db.takethegrt.ca', self::$user, self::$pass, 'grt_info');

		if (mysqli_connect_errno()) {
			throw new Exception('Connect failed: ' . mysqli_connect_error());
		}
	}

	function Select($query_string)
	{
		if (self::$conn === false || self::$conn === null) {
			self::Open();
		}

		$stmt = mysqli_stmt_init(self::$conn);

		if ($stmt->prepare($query_string))
		{
			$num_params = func_num_args();
			$params = array();
			$types = array();

			if ($num_params > 1)
			{
				for ($i = 1; $i < $num_params; ++$i)
				{
					$this_param = func_get_arg($i);
					self::AddParameter($params, $types, $this_param);
				}

				$types_flat = implode('', $types);
				array_unshift($params, $types_flat); // add $types_flat at the beginning of $params - required format

				call_user_func_array(array($stmt, 'bind_param'), $params); // bind parameters. must bind all at the same time
			}

			$stmt->execute();
			$stmt->store_result();

			if ($stmt->num_rows > 0)
			{
				$res_meta = $stmt->result_metadata();

				while ($field = $res_meta->fetch_field())
				{
					$fieldnames[] = &$array[$field->name];
				}

				call_user_func_array(array($stmt, 'bind_result'), $fieldnames);
			}

			while ($stmt->fetch())
			{
				foreach($array as $key => $val) 
				{ 
					$c[$key] = $val; 
				}
				
				$result_array[] = $c;
			}

			$stmt->free_result();

			$this->query_count++;

			if (empty($result_array) == true)
			{
				$result_array = array();
			}

			return $result_array;
		}
		else
		{
			$this->query_count++;
			throw new Exception('Unable to prepare SQL command ' . $this->Error());
		}
	}

	private function AddParameter(&$params, &$types, $this_param)
	{
		if (is_array($this_param) === true && count($this_param) > 0)
		{
			// recurse into any number of nested arrays as necessary
			foreach ($this_param as $this_parameter)
			{
				self::AddParameter($params, $types, $this_parameter);
			}
		}
		else
		{
			if (is_string($this_param))
			{
				$types[] = 's';
			}
			else if (is_int($this_param))
			{
				$types[] = 'i';
			}
			else if (is_double($this_param))
			{
				$types[] = 'd';
			}
			else
			{
				$types[] = 's'; // string as default
			}

			$params[] = $this_param;
		}
	}

	function Alter($query_string)
	{
		if (self::$conn === false || self::$conn === null) {
			self::Open();
		}

		$stmt = mysqli_stmt_init(self::$conn);

		if ($stmt->prepare($query_string))
		{
			$num_params = func_num_args();
			$params = array();
			$types = array();

			if (func_num_args() > 1)
			{
				for ($i = 1; $i < $num_params; ++$i)
				{
					$this_param = func_get_arg($i);
					self::AddParameter($params, $types, $this_param);
				}

				$types_flat = implode('', $types);
				array_unshift($params, $types_flat); // add $types_flat at the beginning of $params - required format

				call_user_func_array(array($stmt, 'bind_param'), $params);
			}

			$this->query_count++;
			return $stmt->execute();
		}
		else
		{
			return false;
		}
	}

	function GetQueryCount()
	{
		return $this->query_count;
	}
	
	function GetLastInsertID()
	{
		$result = $this->Select('SELECT LAST_INSERT_ID() AS id');
	
		return $result[0]['id'];
	}
	
	function Error()
	{
		if (self::$conn !== false)
		{
			if (empty(self::$conn->error) === false)
			{
				return self::$conn->error;
			}
			else
			{
				return mysqli_error(self::$conn);
			}
		}
		else
		{
			return 'unknown error';
		}
	}
}

?>
