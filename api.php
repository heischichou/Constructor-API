<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// change the timezone as necessary
date_default_timezone_set("Asia/Manila");

/*
 * PHP Constructor API by
 * @author Jan Michael Garot/Heischichou (https://github.com/heischichou)
*/
class API {
    private $server = "localhost";
    private $user = "";
    private $password = "";
    private $db = "";
    private $port = 3306;
    private $conn = null;

    /***** CREATE CONNECTION *****/

    // api class constructor
    public function __construct($user, $password, $db){
        $this->user = $user;
        $this->password = $password;
        $this->db = $db;

        $this->conn = new mysqli($this->server, $this->user, $this->password, $this->db, $this->port);
        if($this->conn->connect_error){
            die("Failed to establish connection. Error code " . $this->conn->connect_errno . " - " . $this->conn->connect_error );
        } else {
            $this->conn->set_charset('utf8mb4');
        }
    }

    // connect to database
    public function db_connect($user, $password, $db){
        $this->conn = new mysqli($this->server, $this->user, $this->password, $this->db, $this->port);
        if($this->conn != null){
            die("Failed to establish connection. Error code " . $this->conn->connect_errno . " - " . $this->conn->connect_error );
        } else {
            $this->user = $user;
            $this->password = $password;
            $this->db = $db;

            $this->conn = new mysqli($this->server, $this->user, $this->password, $this->db, $this->port);
            if($this->conn->connect_error){
                die("Failed to establish connection. Error code " . $this->conn->connect_errno . " - " . $this->conn->connect_error );
            } else {
                $this->conn->set_charset('utf8mb4');
            }
        }
    }

    // return connection to database
    public function db_return(){
        return $this->conn;
    }

    /***** CLOSE CONNECTION *****/

    // disconnect from database
    public function db_disconnect(){
        $this->conn->close();
        $this->conn = null;
    }

    // close connection to database
    public function db_close(&$conn){
        return $conn->close();
    }

    /***** HELPER FUNCTIONS *****/

    // input sanitization
    public function sanitize_data(&$data, $type){
        switch($type){
            // int sanitization
            case 'int':
                $data = filter_var($data, FILTER_SANITIZE_NUMBER_INT);
                $data = intval($data);
            break;

            // float sanitization
            case 'float':
                $data = filter_var($data, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
                $data = doubleval($data);
            break;

            // email sanitization
            case 'email':
                $data = trim($data);
                $data = filter_var($data, FILTER_SANITIZE_EMAIL);
            break;

            // default case
            case 'string':
            default:
                $data = trim($data);
                $data = stripslashes($data);
                $data = filter_var($data, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
                $data = htmlspecialchars($data);
            break;
        }
    }

    // string validation
    public function is_string($data) {
        return (is_object($data) && method_exists($data, '__toString'));
    }

    // image validation
    public function validate_image($image, $size_limit){
        $checks = boolval(!empty($image['name']) && !empty($image['type']) && empty($image['error']));
        if($checks){
            // change whitelist as necessary
            $ext_whitelist = array('jpg','jpeg','png','gif');
            $type_whitelist = array('image/jpg', 'image/jpeg', 'image/png', 'image/gif');

            $file_ext = strtolower(pathinfo($image['name'], PATHINFO_EXTENSION));
            $file_type = strtolower($image['type']);

            // check file extension
            if(!in_array($file_ext, $ext_whitelist)){
                $_SESSION['image_err'] = "Uploaded file has invalid extension.";
                $checks = false;
            }

            // check if file is a valid image
            if(!getimagesize($image['tmp_name'])){
                $checks = false;
            }

            // check file type
            if(!in_array($file_type, $type_whitelist)){
                $checks = false;
            }

            // check if file exceeds image size limit
            if($image['size'] > $size_limit){
                $checks = false;
            }
        }
    }

    // input validation
    public function validate_data($data, $type){
        $checks = false;

        switch($type){
            // int validation
            case 'int':
                $checks = filter_var($data, FILTER_VALIDATE_INT);
                $checks = is_int($data);
            break;

            // float validation
            case 'float':
                $checks = filter_var($data, FILTER_VALIDATE_FLOAT);
                $checks = is_double($data);
            break;

            // email validation
            case 'email':
                $checks = filter_var($data, FILTER_VALIDATE_EMAIL);
            break;

            // date validation
            case 'date':
                $checks = (bool) strtotime($data);
                if($checks){
                    $ymd = explode('-', $data);
                    $checks = checkdate($ymd[1], $ymd[2], $ymd[0]);
                    if($checks){
                        $d = DateTime::createFromFormat("Y-m-d", $data);

                        $checks = ($d && $d->format("Y-m-d") === $data) ? true : false;
                        if($checks){
                            $date = new DateTime($data);
                            
                            $today = new DateTime();

                            $checks = ($date >= $today) ? true : false;
                        }
                    }
                }
            break;

            // time validation
            case 'time':
                $time = strtotime($data);
                $checks = (bool) $time;
                if($checks){
                    $time = date("G:i:s", $time);

                    $hms = explode(':', $time);
                    $checks = ($hms[0] >= 0 && $hms[0] <= 24) ? true : false;
                    if($checks){
                        $checks = ($hms[1] >= 0 && $hms[1] <= 60) ? true : false;
                        if($checks){
                            $checks = ($hms[2] >= 0 && $hms[2] <= 60) ? true : false;
                        }
                    }
                }
            break;

            // birthdate validation
            case 'birthdate':
                $checks = (bool) strtotime($birthdate);

                if($checks){
                    $ymd = explode('-', $birthdate);
                    $checks = checkdate($ymd[1], $ymd[2], $ymd[0]);
                    if($checks){
                        $tz = new DateTimeZone('Asia/Manila');
                        $d = DateTime::createFromFormat("Y-m-d", $birthdate);

                        $checks = ($d && $d->format("Y-m-d") === $birthdate) ? true : false;

                        $age = DateTime::createFromFormat('Y-m-d', $birthdate, $tz)->diff(new DateTime('now', $tz))->y;
                        
                        if($age < 18 || $age > 90){
                            $checks = false;
                        }
                    }
                }
            break;

            // default case
            default:
                $checks = filter_var($data, FILTER_SANITIZE_STRING);
                $checks = $this->is_string($data);
            break;
        }

        return $checks;
    }

    // generate random hex color code
    public function generate_color() {
        return '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT);
    }

    // report error
    public function error(){
        return $this->conn->error;
    }

    // report error code
    public function errno(){
        return $this->conn->errno;
    }
    
    public function within_service_hours($time){
        $checks = false;
        if($this->is_valid_time($time)){
            $hour = date("H", strtotime($time));

            // change starting and ending hours to match your business hours
            $checks = ($hour >= 8 && $hour <= 18);
        }

        return $checks;
    }

    // user logout
    public function logout(){
        setcookie(session_id(), "", time() - 3600);
        session_destroy();
        session_write_close();
    }

    /***** MYSQL HELPERS *****/

    // mysql table clause
    public function table(&$string, $params){
        if(!empty($string) && !empty($params)){
            if(!is_array($params)){
                $this->sanitize_data($params, "string");
                $string = $string . $params . " ";
            } else {
                if(!empty($params)){
                    foreach($params as $value){
                        $this->sanitize_data($value, "string");
                        $string = $string . $value . ", ";
                    }
        
                    $string = substr($string, 0, -2);
                    $string = $string . " ";
                }
            }
        }
    }

    // mysql join clause
    public function join($type, $left, $right, $left_kv, $right_kv){
        $this->sanitize_data($left, "string");
        $this->sanitize_data($left_kv, "string");
        $this->sanitize_data($right, "string");
        $this->sanitize_data($right_kv, "string");

        $join = ($this->is_string($type) && !empty($type)) ? "(" . $left . " " . strtoupper($type) . " JOIN " : "(" . $left . " JOIN ";
        $join = $join . $right . " ON " . $left_kv . "=" . $right_kv . ")";
        return $join;
    }

    // mysql where clause
    public function where(&$string, $cols, $params){
        if(!empty($params) && !empty($params)){
            $string = $string . "WHERE ";

            if(!is_array($cols) && !is_array($params)){
                if($this->is_string($cols)){
                    $this->sanitize_data($cols, "string");
                }

                if($this->is_string($params)){
                    $this->sanitize_data($params, "string");
                }

                $string = $string . $cols . "=" . $params . " ";
            } else {
                if(count($cols) == count($params)){
                    $kv = array_combine($cols, $params);

                    foreach($kv as $key => $value){
                        if($this->is_string($key)){
                            $this->sanitize_data($key, "string");
                        }

                        if($this->is_string($value)){
                            $this->sanitize_data($value, "string");
                        }
                        
                        $string = $string . $key . "=" . $value . " AND ";                        
                    }
        
                    $string = substr($string, 0, -4);
                    $string = $string . " ";
                }
            }
        }
    }

    // mysql limit clause
    public function limit(&$string, $limit){
        if(is_int($limit)){
            $string = $string . "LIMIT " . $limit;
        }
    }

    // mysql order by clause
    public function order(&$string, $params, $order){
        if(!empty($params) && !empty($order)){
            if(!is_array($params)){
                $this->sanitize_data($params, "string");
                $this->sanitize_data($order, "string");

                $string = $string . "ORDER BY " . $params . " " . $order . " ";
            } else {
                if(count($params) == count($order)){
                    $string = $string . "ORDER BY ";
                    $kv = array_combine($params, $order);

                    foreach($kv as $key => $value){
                        if($this->is_string($key)){
                            $this->sanitize_data($key, "string");
                        }

                        if($this->is_string($value)){
                            $this->sanitize_data($value, "string");
                        }

                        $string = $string . $key . " " . $value . ", ";
                    }
        
                    $string = substr($string, 0, -2);
                    $string = $string . " ";
                }
            }
        }
    }

    // php mysqli change_user()
    public function change_user($user, $password){
        $user = $this->sanitize_data($user, "string");
        $password = trim($password);

        $this->conn->change_user($user, $password, $this->db);
    }

    /***** SELECT *****/

    // mysql select
    public function select(){
        return "SELECT ";
    }

    // mysql select statement parameters
    public function params(&$string, $params){
        if(!is_array($params)){
            $this->sanitize_data($params, "string");
            $string = $string . $params . " ";
        } else {
            if(!empty($params)){
                foreach($params as $value){
                    $this->sanitize_data($value, "string");
                    $string = $string . $value . ", ";
                }
    
                $string = substr($string, 0, -2);
                $string = $string . " ";
            }
        }
    }

    // mysql from clause
    public function from(&$string){
        $string = $string . "FROM ";
    }

    /***** INSERT *****/

    // mysql insert clause
    public function insert(){
        return "INSERT INTO ";
    }

    // mysql insert statement parameters
    public function columns(&$string, $params = array()){
        if(!empty($params)){
            $string = $string . "(";

            foreach($params as $value){
                $this->sanitize_data($value, "string");
                $string = $string . $value . ", ";
            }

            $string = substr($string, 0, -2);
            $string = trim($string) . ") ";
        }  
    }

    // mysql values clause
    public function values(&$string){
        $string = $string . "VALUES ";
    }

    /***** UPDATE *****/

    // mysql update clause
    public function update(){
        return "UPDATE ";
    }

    // mysql update set parameters
    public function set(&$string, $cols, $params){
        if(!empty($cols) && !empty($params)){
            if(!is_array($cols) && !is_array($params)){
                $this->sanitize_data($cols, "string");
                $this->sanitize_data($params, "string");

                $string = $string . "SET " . $cols . "=" . $params . " ";
            } else {
                if(count($cols) == count($params)){
                    $kv = array_combine($cols, $params);

                    foreach($kv as $key => $value){
                        if($this->is_string($key)){
                            $this->sanitize_data($key, "string");
                        }

                        if($this->is_string($value)){
                            $this->sanitize_data($value, "string");
                        }

                        $string = $string . $key . "=" . $value . ", ";                        
                    }
        
                    $string = substr($string, 0, -2);
                    $string = $string . " ";
                }
            }
        }
    }

    /***** DELETING *****/

    // mysql delete clause
    public function delete(){
        return "DELETE ";
    }

    /***** QUERYING *****/

    // php mysqli prepare()
    public function prepare($query){
        return $this->conn->prepare($query);
    }
    
    // php mysqli execute()
    public function execute(&$statement){
        return $statement->execute();
    }

    // php mysqli store_result()
    public function store_result(&$statement){
        return $statement->store_result();
    }

    // php mysqli num_rows()
    public function num_rows($res){
        return $res->num_rows;
    }

    // php mysqli bind_params()
    public function bind_params(&$statement, $types, $params){
        if(!is_array($params)){
            try {
                $param_ref[] = &$types;
                if(is_string($params)){
                    $params = $this->sanitize_data($params, "string");
                }
                $param_ref[] = &$params;
                return call_user_func_array(array($statement, 'bind_param'), $param_ref);
            } catch (Exception $e){
                echo $e->getMessage();
            }
        } else {
            try {
                $param_ref[] = &$types;
                for($i = 0; $i < count($params); $i++){
                    if(is_string($params[$i])){
                        $params[$i] = $this->sanitize_data($params[$i], "string");
                    }
                    $param_ref[] = &$params[$i];
                }
                return call_user_func_array(array($statement, 'bind_param'), $param_ref);
            } catch (Exception $e){
                echo $e->getMessage();
            }
        }
    }

    // php mysqli bind_result()
    public function bind_result(&$statement, $params = array()){
        if(!empty($params)){
            try {
                for($i = 0; $i < count($params); $i++){
                    $param_ref[] = &$params[$i];
                }
                call_user_func_array(array($statement, 'bind_result'), $param_ref);
                $statement->fetch();
                return $param_ref;
            }
            catch (Exception $e){
                echo $e->getMessage();
            }
        }
    }

    // php mysqli get_result()
    public function get_result(&$statement){
        return $statement->get_result();
    }

    // php mysqli fetch_assoc()
    public function fetch_assoc(&$result){
        return $result->fetch_assoc();
    }

    // php mysqli free_result()
    public function free_result(&$statement){
        $statement->free_result();
    }

    // php mysqli close()
    public function close(&$statement){
        return $statement->close();
    }
}
?>