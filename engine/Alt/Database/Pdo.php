<?php defined('ALT_PATH') OR die('No direct script access.');
/**
 * PDO Alt_Database connection.
 *
 * @package    Alt/Alt_Database
 * @category   Drivers
 * @author     Alt Team
 * @copyright  (c) 2008-2009 Alt Team
 * @license    http://Altphp.com/license
 */
class Alt_Database_Pdo extends Alt_Database {

	// PDO uses no quoting for identifiers
	protected $_identifier = '';

	public function __construct($name, array $config)
	{
		parent::__construct($name, $config);

		if (isset($this->_config['identifier']))
		{
			// Allow the identifier to be overloaded per-connection
			$this->_identifier = (string) $this->_config['identifier'];
		}
	}

	public function connect()
	{
		if ($this->_connection)
			return;

		// Extract the connection parameters, adding required variabels
		extract(array_union($this->_config['connection'], array(
			'dsn'        => '',
			'username'   => NULL,
			'password'   => NULL,
			'persistent' => FALSE,
		)));

		// Clear the connection parameters for security
		unset($this->_config['connection']);

		// Force PDO to use exceptions for all errors
		$options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;

		if ( ! empty($persistent))
		{
			// Make the connection persistent
			$options[PDO::ATTR_PERSISTENT] = TRUE;
		}

		try
		{
			// Create a new PDO connection
			$this->_connection = new PDO($dsn, $username, $password, $options);
		}
		catch (PDOException $e)
		{
			throw new Alt_Exception($e->getMessage());
		}
	}

	/**
	 * Create or redefine a SQL aggregate function.
	 *
	 * [!!] Works only with SQLite
	 *
	 * @link http://php.net/manual/function.pdo-sqlitecreateaggregate
	 *
	 * @param   string      $name       Name of the SQL function to be created or redefined
	 * @param   callback    $step       Called for each row of a result set
	 * @param   callback    $final      Called after all rows of a result set have been processed
	 * @param   integer     $arguments  Number of arguments that the SQL function takes
	 *
	 * @return  boolean
	 */
	public function create_aggregate($name, $step, $final, $arguments = -1)
	{
		$this->_connection or $this->connect();

		return $this->_connection->sqliteCreateAggregate(
			$name, $step, $final, $arguments
		);
	}

	/**
	 * Create or redefine a SQL function.
	 *
	 * [!!] Works only with SQLite
	 *
	 * @link http://php.net/manual/function.pdo-sqlitecreatefunction
	 *
	 * @param   string      $name       Name of the SQL function to be created or redefined
	 * @param   callback    $callback   Callback which implements the SQL function
	 * @param   integer     $arguments  Number of arguments that the SQL function takes
	 *
	 * @return  boolean
	 */
	public function create_function($name, $callback, $arguments = -1)
	{
		$this->_connection or $this->connect();

		return $this->_connection->sqliteCreateFunction(
			$name, $callback, $arguments
		);
	}

	public function disconnect()
	{
		// Destroy the PDO object
		$this->_connection = NULL;

		return parent::disconnect();
	}

	public function set_charset($charset)
	{
		// Make sure the Alt_Database is connected
		$this->_connection OR $this->connect();

		// This SQL-92 syntax is not supported by all drivers
		$this->_connection->exec('SET NAMES '.$this->quote($charset));
	}

	public function query($type, $sql, $as_object = FALSE, array $params = NULL)
	{
		// Make sure the Alt_Database is connected
		$this->_connection or $this->connect();

		try
		{
			$result = $this->_connection->query($sql);
		}
		catch (Exception $e)
		{
			// Convert the exception in a Alt_Database exception
			throw new Alt_Exception($e->getMessage().' [ '.$sql.' ]');
		}

		// Set the last query
		$this->last_query = $sql;

		if ($type === Alt_Database::SELECT)
		{
			// Convert the result into an array, as PDOStatement::rowCount is not reliable
			if ($as_object === FALSE)
			{
				$result->setFetchMode(PDO::FETCH_ASSOC);
			}
			elseif (is_string($as_object))
			{
				$result->setFetchMode(PDO::FETCH_CLASS, $as_object, $params);
			}
			else
			{
				$result->setFetchMode(PDO::FETCH_CLASS, 'stdClass');
			}

			$result = $result->fetchAll();

			// Return an iterator of results
			return new Alt_Database_Result_Cached($result, $sql, $as_object, $params);
		}
		elseif ($type === Alt_Database::INSERT)
		{
			// Return a list of insert id and rows created
			return array(
				$this->_connection->lastInsertId(),
				$result->rowCount(),
			);
		}
		else
		{
			// Return the number of rows affected
			return $result->rowCount();
		}
	}

	public function begin($mode = NULL)
	{
		// Make sure the Alt_Database is connected
		$this->_connection or $this->connect();

		return $this->_connection->beginTransaction();
	}

	public function commit()
	{
		// Make sure the Alt_Database is connected
		$this->_connection or $this->connect();

		return $this->_connection->commit();
	}

	public function rollback()
	{
		// Make sure the Alt_Database is connected
		$this->_connection or $this->connect();

		return $this->_connection->rollBack();
	}

	public function list_tables($like = NULL)
	{
		throw new Alt_Exception('Alt_Database method '.__FUNCTION__.' is not supported by '.__CLASS__);
	}

	public function list_columns($table, $like = NULL, $add_prefix = TRUE)
	{
        throw new Alt_Exception('Alt_Database method '.__FUNCTION__.' is not supported by '.__CLASS__);
	}

	public function escape($value)
	{
		// Make sure the Alt_Database is connected
		$this->_connection or $this->connect();

		return $this->_connection->quote($value);
	}

} // End Alt_Database_PDO
