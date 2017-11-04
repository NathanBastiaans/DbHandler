<?php

class DbHandler
{

    /**
     * Holds the database username
     * @var string
     */
    private $username;

    /**
     * Holds the database password
     * @var string
     */
    private $password;

    /**
     * Holds the database name
     * @var string
     */
    private $database;

    /**
     * Holds the database hostname
     * @var string
     */
    private $hostname;

    /**
     * Holds debug
     * @var boolean
     */
    private $debug;

    /**
     * Should the return values be array or object
     * @var boolean
     */
    private $returnAsObject;
    /**
     * Holds the database connection
     * @var PDO
     */
    public $connection;

    /**
     * DbHandler constructor.
     * @param $hostname
     * @param $database
     * @param $username
     * @param $password
     * @param $returnAsObject
     * @param $debug
     */
    public function __construct (
        $hostname,
        $database,
        $username,
        $password,
        $returnAsObject = false,
        $debug = false
    )
    {

        // Set database vars
        $this->hostname = $hostname;
        $this->database = $database;
        $this->username = $username;
        $this->password = $password;

        // Set returnAsObject
        $this->returnAsObject = $returnAsObject;

        // Set debug mode
        $this->debug = $debug;

        // Initialize the connection
        $this->initialize ();

    }

    /**
     * By defining a private final __clone ()
     * we restrict the usage of the cloning statement
     * on all instances of our database object.
     */
    private final function __clone () {}

    /**
     * Establishes a database connection using the PDO object.
     */
    private function initialize ()
    {

        $this->connection = new PDO
        (
            "mysql:host=" . $this->hostname . ";dbname=" . $this->database . ";",
            $this->username,
            $this->password
        );

    }

    /**
     * Function to query the database, used for inserts, deletes and updates
     *
     * @param $query
     * @param null $params
     * @return string
     */
    public function query ($query, $params = NULL)
    {

        if ($this->debug)
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $this->connection->prepare ($query);
        $stmt->execute ($params);
        $error = $this->connection->errorInfo ();

        if ($this->debug)
            if ($error[0] != "00000")
                die ($this->connection->errorInfo ());


        return $this->connection->lastInsertId();
    }

    /**
     * Returns one single result or false if result is not found
     *
     * @param $query
     * @param null $params
     * @return bool|mixed
     */
    public function fetch ($query, $params = NULL)
    {

        if ($this->debug)
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $this->connection->prepare ($query);
        $stmt->execute ($params);
        $result = $stmt->fetch (PDO::FETCH_ASSOC);
        $stmt->closeCursor ();
        $error = $this->connection->errorInfo ();

        if ($this->debug)
            if ($error[0] != "00000")
                die (print_r ($this->connection->errorInfo ()));

        //Send single data object
        return $result
            ? $this->getResultSet($result)
            : false;

    }

    /**
     * Returns an array of objects/arrays with the results
     *
     * @param $query
     * @param null $params
     * @return array
     */
    public function fetchAll ($query, $params = NULL)
    {

        if ($this->debug)
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $this->connection->prepare ($query);
        $stmt->execute ($params);
        $results = $stmt->fetchAll (PDO::FETCH_ASSOC);
        $stmt->closeCursor ();

        $error = $this->connection->errorInfo ();
        if ($this->debug)
            if ($error[0] != "00000")
                die (print_r ($this->connection->errorInfo ()));

        $return = [];
        foreach ($results as $result)
            array_push ($return, $this->getResultSet($result));

        // Send all data objects in an array
        return $return;

    }

    /**
     * @param $result
     * @return mixed array|object
     */
    private function getResultSet($result)
    {
        if ( $this->returnAsObject )
        {
            $object = new stdClass();
            foreach ($result as $key => $value)
                $object->$key = $value;

            return $object;

        }

        return $result;
    }

}