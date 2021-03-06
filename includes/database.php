<?php
/** AdServer 
 * 2013 Andrew Drane
 * 
 * Database connection class
 *  Includes simple login and query functions
 *  Connects to database on initialization
 *  Closes connection when done
 */

require_once 'config.php'; 

class DB {
    
    public $mysqli = null; 

    /** Login to the database on object instantiation
     * Use the global database config
     */
	function __construct() {
        global $database;

        $this->mysqli = new mysqli(
                $database['location'], 
                $database['user'],
                $database['password'], 
                $database['database']
                );
        
        //throw an exception if we get an error
        if ( $this->mysqli->connect_error ) {
            throw new Exception( 'db connection error' );
        }
    }
    
    /** Be nice to all the processes, and close the mysql connection when done.
     * 
     */
    function __destruct() {
        try {
            $this->mysqli->close();
        } catch ( Exception $e ) { 
            ; //don't do anything if there is an exception... just don't want error stuff
        }
    }
	
    /** Very basic query function.
     * Tests that we are actually connected
     *
     * @param type $sql
     * @return type 
     */
    function query( $sql ) { 
        
        //make sure we are connected, or thow an exception
        if ( $this->mysqli->connect_error ) {
            throw new Exception( 'db connection error' );
        }
        
        //return the mysql result set
        return $this->mysqli->query( $sql );
        
        //results will be a boolean or an object
//        if( is_object( $results ) ) {
//            return $results->fetch_array();
//        } else {
//            return $results; 
//        }
    }
}
