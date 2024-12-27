<?php
class DB
{
    private static $mysqli;

    public static function connect()
    {
        $db_user_name = "root";
        $db_user_password = "";
        $db_name = "wati_project";
        self::$mysqli = new mysqli("localhost", $db_user_name, $db_user_password, $db_name);

        if (self::$mysqli->connect_errno) {
            die("Failed to connect to MySQL: " . self::$mysqli->connect_error);
        }
    }
    public static function insert($table = "", $data = [], $console = false)
    {
        self::connect();
        if (empty($table) || empty($data)) {
            echo "<b>ARGUMENT ERROR</b> -> Table Name or Data Missing";
            exit();
        }

        try {
            $columns = implode(', ', array_keys($data));
            $placeholders = implode(', ', array_fill(0, count($data), '?'));

            $query = "INSERT INTO $table ($columns) VALUES ($placeholders)";

            $stmt = self::$mysqli->prepare($query);
            if ($stmt === false) {
                throw new Exception("Failed to prepare statement: " . self::$mysqli->error);
            }

            $types = '';
            $params = [];
            foreach ($data as $value) {
                if (is_int($value)) {
                    $types .= 'i'; // integer
                } elseif (is_float($value)) {
                    $types .= 'd'; // double
                } else {
                    $types .= 's'; // string
                }
                $params[] = $value;
            }

            $stmt->bind_param($types, ...$params);

            if (!$stmt->execute()) {
                throw new Exception("Failed to execute statement: " . $stmt->error);
            }

            $stmt->close();
            return true;
        } catch (Exception $e) {
            if ($console) {
                return "ERROR : " . $e->getMessage() . " FOR QUERY : " . $query;
            } else {
                return false;
                exit;
            }
        }
    }


    public static function select($select = "*", $table, $where = [], $console = false)
    {
        self::connect();
        if (empty($select) || empty($table)) {
            echo "<b>ARGUMENT ERROR</b> -> Attributes or Table Name Missing";
            exit();
        }

        $whereClause = '';
        $params = [];
        if (!empty($where)) {
            $conditions = [];
            foreach ($where as $key => $val) {
                $conditions[] = "$key = ?";
                $params[] = $val;
            }
            $whereClause = "WHERE " . implode(" AND ", $conditions);
        }

        $query = "SELECT $select FROM $table $whereClause";

        if ($console) {
            return $query . "<br>";
        }

        $stmt = self::$mysqli->prepare($query);
        if ($stmt === false) {
            return "Failed to prepare statement: " . self::$mysqli->error;
            exit();
        }

        if (!empty($params)) {
            $types = str_repeat('s', count($params));
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_all(MYSQLI_ASSOC);

        $stmt->close();
        return $data;
    }

    public static function update($table = "", $set = [], $where = [], $console = false)
    {
        self::connect();
        if (empty($set) || empty($table) || empty($where)) {
            echo "<b>ARGUMENT ERROR</b> -> Data, Table Name, or Where Condition Missing";
            exit();
        }

        $setClause = [];
        $params = [];
        foreach ($set as $key => $val) {
            $setClause[] = "$key = ?";
            $params[] = $val;
        }
        $setClause = implode(", ", $setClause);

        $whereClause = '';
        if (!empty($where)) {
            $whereConditions = [];
            foreach ($where as $key => $val) {
                $whereConditions[] = "$key = ?";
                $params[] = $val;
            }
            $whereClause = "WHERE " . implode(" AND ", $whereConditions);
        }

        $query = "UPDATE $table SET $setClause $whereClause";

        if ($console) {
            echo $query . "<br>";
        }

        $stmt = self::$mysqli->prepare($query);
        if ($stmt === false) {
            return "Failed to prepare statement: " . self::$mysqli->error;
            exit();
        }

        $types = str_repeat('s', count($params));
        $stmt->bind_param($types, ...$params);

        $stmt->execute();
        $stmt->close();

        if ($console) {
            echo "Data Update<br>";
        }

        return true;
    }

    public static function delete($table, $where = [], $console = false)
    {
        self::connect();
        if (empty($table)) {
            echo "<b>ARGUMENT ERROR</b> -> Table Name Missing";
            exit();
        }
    
        $whereClause = '';
        $params = [];
        if (!empty($where)) {
            $whereConditions = [];
            foreach ($where as $key => $val) {
                $whereConditions[] = "$key = ?";
                $params[] = $val;
            }
            $whereClause = "WHERE " . implode(" AND ", $whereConditions);
        }
    
        $query = "DELETE FROM $table $whereClause";
    
        if ($console) {
            echo $query . "<br>";
        }
    
        $stmt = self::$mysqli->prepare($query);
        if ($stmt === false) {
            return "Failed to prepare statement: " . self::$mysqli->error;
        }
    
        $types = str_repeat('s', count($params));
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
    
        $stmt->execute();
    
        $affectedRows = self::$mysqli->affected_rows; // Get the number of affected rows
    
        $stmt->close();
    
        if ($console) {
            if ($affectedRows > 0) {
                echo "Data Deleted<br>";
            } else {
                echo "No Data Found to Delete<br>";
            }
        }
    
        return $affectedRows > 0; // Return true if rows were deleted, false otherwise
    }

    public static function exists($table = "", $where = [], $console = false)
    {
        self::connect();
        if (empty($table) || empty($where)) {
            echo "<b>ARGUMENT ERROR</b> -> Table Name or Where Condition Missing";
            exit();
        }

        $result = self::select('*', $table, $where, $console);
        if ($result) {
            if ($console) {
                echo "Data Found<br>";
            }
            return true;
        } else {
            if ($console) {
                echo "Data Not Found<br>";
            }
            return false;
        }
    }
    public static function getFunderNameById($funderId)
    {
        self::connect();
        $result = self::select('`entity_name`', 'funder_master', ['funder_id' => $funderId]);
        if ($result) {
            return $result[0]['entity_name'];
        } else {
            return false;
        }
    }
    public static function getEmpolyeeNameById($empId, $table = "employee_table")
    {
        self::connect();
        $emp = self::select('*', $table, ['employee_id' => $empId]);
        $first = !empty($emp[0]['first_name']) ? ' ' . $emp[0]['first_name'] : '';
        $mid = !empty($emp[0]['middle_name']) ? ' ' . $emp[0]['middle_name'] . ' ' : '';
        $last = !empty($emp[0]['last_name']) ? ' ' . $emp[0]['last_name'] : '';
        $full = $first . $mid . $last;
        $cleanedString = preg_replace('/\s+/', ' ', $full);
        return $cleanedString;
    }

    public static function raw($query, $console = false)
    {
        self::connect();

        $stmt = self::$mysqli->prepare($query);
        if ($stmt === false) {
            return "Failed to prepare statement: " . self::$mysqli->error;
            exit();
        }

        // Execute the statement
        $executed = $stmt->execute();

        // Check if the query is an update or delete query
        $isUpdateOrDeleteQuery = stripos($query, 'update') === 0 || stripos($query, 'delete') === 0;

        if ($isUpdateOrDeleteQuery) {
            // If it's an update or delete query, return the execution result directly
            if ($console) {
                echo "Update/Delete Query Executed<br>";
            }
            return $executed;
        } else {
            // If it's not an update or delete query, fetch the result set
            $result = $stmt->get_result();
            if ($result === false) {
                return "Error getting result set: " . $stmt->error;
                exit();
            }

            // Fetch all rows from the result set
            $data = $result->fetch_all(MYSQLI_ASSOC);
            $stmt->close();

            if ($console) {
                return $query;
            }

            return $data;
        }
    }
    public static function uploadImages($img, $upload_dir)
    {
        $uploaded_paths = []; // Array to store paths of uploaded images
        if (empty($img)) {
            return null;
        }

        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true); // Create directory recursively with full permissions
        }

        if (isset($_FILES[$img]) && !empty($_FILES[$img]["name"][0])) {
            foreach ($_FILES[$img]["tmp_name"] as $key => $tmp_name) {
                $file_name = $_FILES[$img]["name"][$key];
                $file_tmp = $_FILES[$img]["tmp_name"][$key];

                $unique_name = uniqid() . "_" . $file_name;

                if (move_uploaded_file($file_tmp, $upload_dir . $unique_name)) {
                    $uploaded_paths[] = $upload_dir . $unique_name;
                }
            }
        }

        return implode(",", $uploaded_paths);
    }

    public static function log($data)
    {
        echo '<pre>';
        print_r($data);
        echo '</pre>';
        exit;
    }
    public static function xerror_log($filePath, $content)
    {
        // Append the content to the file with a new line
        $result = file_put_contents($filePath, $content . PHP_EOL, FILE_APPEND);
        $result = preg_replace('/\s+/', ' ', $result);
        return $result;
    }
}
