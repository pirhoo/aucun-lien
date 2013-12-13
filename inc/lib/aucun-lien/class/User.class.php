<?php
/**
 * User class extends Record class
 * 
 * This class represents user.
 * 
 * @author Pirhoo <hello@pirhoo.com>
 * @version 1.0
 * @package Record
 * @subpackage User
 */
class User extends Record{

    /**
     * Name of the record in the database
     *
     * @static
     * @var string
     * @access protected
     */
    protected static $dbTable = "extra_users";

    
        
    /**
     * @var string
     * @access protected
     */
    protected $name;

    /**
     * @var string
     * @access protected
     */
    protected $password;
        
    /**
     * @var string
     * @access protected
     */
    protected $email;
        
    /**
     * @var string
     * @access protected
     */
    protected $activation_key;

    /**
     * @var string
     * @access protected
     */
    protected $status;



    /**
     * Construct the class with the element's ID
     *
     * @access public
     * @static
     * @param int $id
     * @param mixin $db
     * @return mixin
     */
    public static function findByEmail($email, $db) {                        
            
            // find the called class (children of Record)
            $class = get_called_class();
            
            try {                         
                                   
                // find the user id
                $user_id = $db->get_var( $db->prepare("SELECT id FROM {$class::$dbTable} WHERE email = %s", $email) );
    
                // we didn't find any user 
                if(!is_numeric($user_id)) throw new Exception();

                // find the record
                $record = $class::getDbRecord($user_id, $db);

                // return the instance
                return new $class($record, $db);
                  
            } catch(Exception $e) {                
                // record no found
                return NULL;
            }
    }


    /**    
     *
     * @access public
     * @static
     * @param int $id
     * @param mixin $db
     * @return mixin
     */
    public static function findByActivationKey($key, $db) {                        
            
            // find the called class (children of Record)
            $class = get_called_class();
            
            try {                         
                                                                      
                // find the user id
                $user_id = $db->get_var( $db->prepare("SELECT id FROM {$class::$dbTable} WHERE activation_key = %s", $key) );
    
                // we didn't find any user 
                if(!is_numeric($user_id)) throw new Exception();

                // find the record
                $record = $class::getDbRecord($user_id, $db);

                // return the instance
                return new $class($record, $db);
                  
            } catch(Exception $e) {               
                // record no found
                return NULL;
            }
    }





    /**
     * Define the database schema for this table 
     * 
     * @static
     * @access public
     * @return array
     */
    public static function getDbSchema() {
    
        return array(
            "description" => "User representation",
            "fields" => array(
                "id" => array("type" => "int", "not null" => true, "description" => "Unique, identifier"),                
                "name"  => array("type" => "varchar", "length" => 120, "not null" => true, "description" => "User's name"),
                "password"  => array("type" => "varchar", "length" => 64, "not null" => true, "description" => "User's password"),
                "email"     => array("type" => "varchar", "length" => 60, "not null" => true, "description" => "User's email"),
                "activation_key"     => array("type" => "varchar", "length" => 60, "not null" => true, "description" => ""),
                "status"    => array("type" => "status", "not null" => true, "description" => "Status of the record")
            ),
            "primary keys" => array("id")
        );
            
    }

    /**
     * @function
     * 
     */
    public static function crypt_password($password) {
        return crypt($password, CRYPT_BLOWFISH);
    }

    /**
     * Active l'utilisateur
     *
     * @access public
     */
    public function activate() {        
        return $this->db->query("UPDATE {$this::$dbTable} SET status=0, activation_key='' WHERE id = {$this->id}");
    }

    /**
     * Créé la session utilisateur
     *
     * @access public
     */
    public function createSession() {
        
        // création de la session
        if( session_id() == "" ) session_start();

        $_SESSION["user_email"]    = $this->getEmail();
        $_SESSION["user_password"] = $this->getPassword();

    }

    /**
     * Créé des cookies utilisateur
     *
     * @access public
     */
    public function createCookie() {
                
        setcookie("user_email", $this->getEmail(), time()+60*60*24*15, "/");  // expire dans 15 jours
        setcookie("user_password", $this->getPassword(), time()+60*60*24*15, "/");  // expire dans 15 jours
    }

    
    /**
     * Génère un nouveau password, l'enregistre chiffré et le retourne en claire
     *
     * @access public
     */
    public function generateNewPassword() {

        // nouveau password (12 premiers caractères d'un nombre aléatoire hashé avec md5)
        $password = substr( md5(rand(0,9999999999)), rand(0,10), rand(10,14) );

        // chriffre et alloue le nouveau password
        $this->setPassword( $this->crypt_password($password) );

        // met à jour la bdd
        $this->db->query("UPDATE {$this::$dbTable} SET password = '".$this->getPassword()."' WHERE id =".$this->getId()." ");

        // Detourne le nouveau password
        return $password;
    }

    /**
     * Retourne tous les bookmarks de l'utilisateur
     *
     * @access public
     * @return Array
     */
    public function getBookmarks() {                        

        $bookmarks = array();
        // Sélectionne tous les bookmarks de l'utilisateurs courant        
        $rows = $this->db->get_results("SELECT post FROM extra_bookmarks WHERE user = {$this->id}");        

        foreach($rows as $key => $b) {
            $bookmarks[] = $b->post;
        }

        return $bookmarks;
    }

    /**
     * Ajoute un bookmark à l'utilisateur
     *
     * @access public
     * @param  Integer $postID
     */
    public function addBookmark($postID) {                        
        
        return is_numeric($postID) 
        && $this->db->query("INSERT INTO extra_bookmarks (user, post) VALUES({$this->id}, {$postID})");

    }

    /**
     * Supprime un bookmark de l'utilisateur
     *
     * @access public
     * @param  Integer $postID
     */
    public function removeBookmark($postID) {                        
        
        return is_numeric($postID) 
        && $this->db->query("DELETE FROM extra_bookmarks WHERE user = {$this->id} AND post = {$postID}");

    }


    /**
     * 
     *
     * @access public
     */
    public function getFilters() {                                
        return json_decode(
            $this->db->get_var( 
                $this->db->prepare(
                    "SELECT filters FROM extra_filters WHERE user = %d", 
                    $this->getId()
                ) 
            ),
            true
        );
    }


    /**
     * 
     *
     * @access public
     */
    public function saveFilters(array $filters = array()) {                        
        
        // filtres au format json
        $filters_json = json_encode($filters);

        // insertion
        if( !$this->getFilters() ) {
                    
            return $this->db->query(
                $this->db->prepare("INSERT INTO extra_filters (user,filters) VALUES(%d,%s)", $this->id, $filters_json)
            );
        // mise à jour
        } else {  
            
            return $this->db->query(
                $this->db->prepare("UPDATE extra_filters SET filters = %s WHERE user = %d", $filters_json, $this->id)
            );        
        }

    }


    /**
     *  Status's setter 
     *
     * @access public
     * @param $value
     */
    public function setStatus($value) {
        $this->status = $value;
    }

    /**
     * Status's getter
     *
     * @access public
     * @param $value
     */
    public function getStatus() {
        
        return $this->status;
    }


    /**
     *  Activation_key's setter 
     *
     * @access public
     * @param $value
     */
    public function setActivationKey($value) {
        $this->activation_key = $value;
    }

    /**
     * Activation_key's getter
     *
     * @access public
     * @param $value
     */
    public function getActivationKey() {
        
        return $this->activation_key;
    }



    /**
     * Password's setter
     *
     * @access public
     * @param $value
     */
    public function setPassword($value) {
        $this->password = $value;
    }

    /**
     * Password's getter
     *
     * @access public
     * @param $value
     */
    public function getPassword() {
        
        return $this->password;
    }


    /**
     * email's setter
     *
     * @access public
     * @param $value
     */
    public function setEmail($value) {
        $this->email = $value;
    }

    /**
     * email's getter
     *
     * @access public
     * @param $value
     */
    public function getEmail() {
        
        return $this->email;
    }


    /**
     * Name's getter
     *
     * @access public
     * @param $value
     */
    public function setName($value) {
        $this->name = $value;
    }

    /**
     * Name's getter
     *
     * @access public
     * @param $value
     */
    public function getName() {
        
        return $this->name;
    }


}

?>
