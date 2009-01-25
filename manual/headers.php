<?php

require_once 'Controls.php';

function __autoload($class_name)
{
        require_once $class_name . '.php';
}

$Database = new Database();

class Database
{
	private static $user = 'grt_user';
	private static $pass = 'grtgrt';

	private static $conn = false;

	private $query_count;

	function Open()
	{
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
			for ($i = 1; $i < func_num_args(); ++$i) {
				$param = func_get_arg($i);
				$params[] = $param;

				if (is_string($param))
				{
					$types .= 's';
				}
				else if (is_int($param))
				{
					$types .= 'i';
				}
				else if (is_double($param))
				{
					$types .= 'd';
				}
				else
				{
					$types .= 's'; // string as default
				}
			}

			if (func_num_args() > 1)
			{
				array_unshift($params, $types);

				call_user_func_array(array($stmt, 'bind_param'), $params);
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
			return $result_array;
		}
		else
		{
			$this->query_count++;
			throw new Exception('Unable to prepare SQL command' . $query_string);
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
			for ($i = 1; $i < func_num_args(); ++$i) {
				$param = func_get_arg($i);
				$params[] = $param;
				
				if (is_string($param))
				{
					$types .= 's';
				}
				else if (is_int($param))
				{
					$types .= 'i';
				}
				else if (is_double($param))
				{
					$types .= 'd';
				}
				else
				{
					$types .= 's'; // string as default
				}
			}

			if (func_num_args() > 1)
			{
				array_unshift($params, $types);

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
