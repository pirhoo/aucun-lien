<?php

/**
 * Record Class
 * 
 * This abstract class implements ArrayAccess class to create object
 * following the database schema.
 * 
 * @author Pirhoo <hello@pirhoo.com>
 * @version 1.0
 * @package Record
 */
abstract class Record implements ArrayAccess {

      /**
       * @var PDO
       * @access protected
       */
      protected $db;

      /**
       * @var integer
       * @access protected 
       */
      protected $id;

      /**
       * Name of the record in the database
       *
       * @static
       * @var string
       * @access protected
       */
      protected static $dbTable = NULL;

      /**
       * Default constructor which receives an associative array of every field. 
       * 
       * @access public
       * @param array $data
       * @param PDO $db
       */
      public function __construct($data = array(), $db) {            

            // save the PDO instance
            $this->db = $db;

            // if array is not empty
            if (!empty($data) && is_array($data)) {
                  // convert associative array to new private attribute (and there respective getters/setters)
                  $this->hydrate($data);                                                      
            }

      }


      /**
       * Define the database schema for this table. This method must be implemented.
       * 
       * @abstract
       * @static
       * @access public
       * @return array
       */
      public static abstract function getDbSchema();


      /**
       * Construct the class with the element's ID
       *
       * @access public
       * @static
       * @param int $id
       * @param mixin $db
       * @return mixin
       */
      public static function find($id, $db) {                        
            
            // find the called class (children of Record)
            $class = get_called_class();
            
            try {
                  // find the record
                  $record = $class::getDbRecord($id, $db);

                  // return the instance
                  return new $class($record, $db);
                  
            } catch(Exception $e) {
                  // record no found
                  return NULL;
            }
      }

      /**
       *
       * Insert the record in the database
       *
       * @access public
       *
       */
      public function insert() {
            
            // database schema
            $dbSchema = $this->getDbSchema();
            // throw an exception if the schema is unvailable
            if( ! is_array($dbSchema) ) throw new Exception("Record's schema unavailable.");
            
            // iterator
            $i = 0;
            // string to contain the columns and the values
            $columns =  $values = "";

            // foreach field, add an insert value
            foreach( $dbSchema["fields"] as $key => $field) {                               
                      
                  // find the getter's method of the field
                  $valueGetter = $this->getGetter($key);

                  // value of the field
                  $value = $this->$valueGetter();

                  // convert some object to an id
                  if( get_parent_class($value) == "Record" ) $value = $value->getId();                  

                  // if the is a value
                  if( $value != "" && $value != NULL) {
                              
                        // separator for the columns
                        $columns .= $i > 0 ? "," : "";
                        // separator for the values
                        $values  .= $i > 0 ? "," : "";

                        // add the column to the query
                        $columns .= " {$key} ";
                                                                                        
                        // add the value following this type
                        switch ($field["type"]) {

                              case "int":
                                    $value = (int) $value;
                                    $values .= " {$value} ";
                                    break;

                              case "float":
                                    $value = (float) $value;
                                    $values .= " {$value} ";
                                    break;

                              case "text":                              
                                    // escape some carracters
                                    $value = htmlentities($value, ENT_QUOTES);
                                    // remove spacing at the begining and the end
                                    $value = trim($value); 
                                    $values .= " '{$value}' ";
                                    break;

                              case "varchar":
                                    // escape some carracters
                                    $value = htmlentities($value, ENT_QUOTES);
                                    // remove spacing at the begining and the end
                                    $value = trim($value);
                                    // if the string has a maximum length
                                    if( isset($field["length"]) )  {
                                          // we truncate it
                                          $value = substr($value, 0, (int) $field["length"] );
                                    }
                                    $values .= " '{$value}' ";
                                    break;

                              default : 
                                    // escape some carracters
                                    $value = htmlentities($value, ENT_QUOTES);
                                    // remove spacing at the begining and the end
                                    $value = trim($value);
                                    // if the string has a maximum length
                                    if( isset($field["length"]) )  {
                                          // we truncate it
                                          $value = substr($value, 0, (int) $field["length"] );
                                    }
                                    $values .= " '{$value}' ";                                 
                                    break;
                        }
                        // iterate
                        $i++;
                  }            

            }      
            
            // build the query
            $query  = "INSERT INTO " . $this->dbTable() . "\n"; // table
            $query .= "({$columns})" . "\n"; // columns
            $query .= "VALUES ({$values})"; // values

            // execute the query
            return $this->db->query($query); 
                                
      }



      /**
       *
       * Synchronize the database record with the new data 
       *
       * @access public
       *
       */
      public function sync() {

            // find the called class (children of Record)
            $class = get_called_class();

            // find the database schema
            $dbSchema = $this->getDbSchema();
            // throw an exception if the schema is unvailable
            if( ! is_array($dbSchema) ) throw new Exception("Record's schema unavailable.");

            // values to change (all)
            $values = array();

            foreach($dbSchema["fields"] as $name => $value) {
                  // find the getter
                  $method = $this->getGetter($name);
                  // find the value of the field
                  $value = $this->$method();    
                  // add the field to change in the array
                  $values[] = "{$name}='{$value}'";
                  // check if the current field is an id
                  if($name == "id") $id = $this->$method();
            }

            if( !isset($id) ) throw new Exception("No record's id for this schema.");

            // build the query
            $query  = "UPDATE ".$this->dbTable() . "\n"; // table            
            $query .= "SET ".implode(", ", $values)."\n"; // values
            $query .= "WHERE {$class::idContraint($id)}";

            // execute the query
            return $this->db->query($query); 

      }
            


      /**
       * 
       * 
       * @static
       * @return array
       * @access public
       */
      public static function getDbRecord($id, $db) {
            
            // find the called class (children of Record)
            $class = get_called_class();

            // if any table name is available
            if($class::$dbTable == NULL) throw new Exception("Any table name available.");            

            try {
                  return $db->get_row("SELECT * FROM {$class::$dbTable} WHERE {$class::idContraint($id)}", ARRAY_A);
            } catch(Exception $err) {
                  return NULL;
            }
      }

      /**
       * How to select a record (by default, with a single one key) ?
       * 
       * @static
       * @param mixin $id
       * @return string
       * @access public
       */
      public static function idContraint($id) {                        
            return " id = '$id' ";
      }


      /**
       * Checks if the object has an id 
       * @return boolean
       * @access public
       */
      public function isNew() {
            return empty($this->id);
      }


      /**
       * Converts an associative array to new private attribute (and there respective getters/setters)
       * @param array $data
       * @access public
       */
      public function hydrate(array $data) {

            // for each value in parameter
            foreach ($data as $attribut => $value) {
                  
                  // Attribut isn't an index
                  if (!is_int($attribut)) {
                              
                        // Converts associative name to normalized nomenclature and calls the right setter method:
                        $method = $this->getSetter($attribut);

                        // If the method is callable...
                        if (is_callable(array($this, $method)))
                        // ...we call it (to set value) !
                              $this->$method($value);
                  }
            }
      }


      /**
       * Determine the setter following the name of the attribute
       * 
       * @param string $attribut
       * @return string
       */
      public function getSetter($attribut) {

            /* Apply the following steps to determinate the method name:
             *    1- convert all underscores to spaces (ex: "relation_id" begins "relation id")
             *    2- transform all word first letter to uppercase (ex: "relation id" begins "Relation Id"
             *    3- remove all spaces (ex "Relation Id" begins "RelationId")
             *    4- add a "set" prefix (ex "RelationId" begins "setRelationId")
             */                         
            return 'set' . str_replace(' ', '', ucwords(str_replace('_', ' ', $attribut)));
      }


      /**
       * Determine the getter following the name of the attribute
       * 
       * @param string $attribut
       * @return string
       */
      public function getGetter($attribut) {

            /* Apply the following steps to determinate the method name:
             *    1- convert all underscores to spaces (ex: "relation_id" begins "relation id")
             *    2- transform all word first letter to uppercase (ex: "relation id" begins "Relation Id"
             *    3- remove all spaces (ex "Relation Id" begins "RelationId")
             *    4- add a "get" prefix (ex "RelationId" begins "setRelationId")
             */                         
            return 'get' . str_replace(' ', '', ucwords(str_replace('_', ' ', $attribut)));
      }


      /**
       * $dbTable getter 
       *
       * @access public
       * @return string
       */
      public static function dbTable() {

            // find the called class (children of Record)
            $class = get_called_class();

            return $class::$dbTable;
      }


      /**
       * $erreur attrbiute getter
       * @return array
       * @access public
       */
      public function getErrors() {
            return $this->errors;
      }

      /**
       * $id attrbiute getter
       * @return array
       * @access public
       */
      public function getId() {
            return $this->id;
      }

      /**
       * $id attrbiute setter
       * @param  integer $id
       * @access public
       */
      public function setId($id) {
            // cast value
            $this->id = (int) $id;
      }

      /**
       * Checks if we can call the named method and call it
       * @param  string $var
       * @access public
       */
      public function offsetGet($var) {

            if (isset($this->$var) && is_callable(array($this, $var)))
                  return $this->$var();
      }

      /**
       * Checks if we can set the named value and sets it
       * @param string $var
       * @param mixed $value
       * @access public
       */
      public function offsetSet($var, $value) {

            $method = 'set' . ucfirst($var);

            if (isset($this->$var) && is_callable(array($this, $method)))
                  $this->$method($value);
      }

      /**
       * Checks if we can call the named method
       * @param string $var
       * @access public
       */
      public function offsetExists($var) {

            return isset($this->$var) && is_callable(array($this, $var));
      }

      /**
       * Implements an abstract method from ArrayAccess 
       * (and trow an exception, we can't unset a value)
       * @param string $var 
       */
      public function offsetUnset($var) {

            throw new Exception(_('Remove value failled.'));
      }

      /**
       * Dirty method to show the class content
       * @access public
       */
      public function quickDisplay() {

            print_r($this);
      }


      /**
       * Method to convert an Unicode to UTF8 (Freebas returns unicode)
       * @access public
       * @param string
       * @return string
       */
      public function unicodeToHtml($string) {
            
            if (preg_match_all('!\\\u([0-9A-Fa-f]{4})!i', $string, $matches)) {
                  
                  foreach ($matches[0] as $v) {                        
                        $unicode_hexacode = $v[2] . $v[3] . $v[4] . $v[5];
                        $string = str_replace($v, '&#' . base_convert($unicode_hexacode, 16, 10) . ';', $string);
                  }
            }
            
            return $string;
      }

}

?>
