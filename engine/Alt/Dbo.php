<?php defined('ALT_PATH') or die('No direct script access.');

class Alt_Dbo {
    // database instance for this class
    public $db;
    // database instance to use
    public $db_instance;
    // autoincrement flag
    public $autoinc = true;
    // primary key for the table
    public $pkey;
    // table name in database
    protected $table_name;
    // table fields
    protected $table_fields = array();
    // table dynamic column name
    protected $table_dyncolumn;
    // table dynamic fields data
    protected $table_dynfields = array();
    // view name in database
    protected $view_name;
    // view fields
    protected $view_fields = array();
    // view dynamic column name
    protected $view_dyncolumn;
    // view dynamic fields data
    protected $view_dynfields = array();

    public static function instance($db_instance = null){
        $classname = get_called_class();
        $self = new $classname();
        if($db_instance) $self->reinstance($db_instance);
        return $self;
    }

    /**
     * Constructing class
     * @return void
     */
    public function __construct() {
        $this->table_name   = $this->table_name ? $this->table_name : get_class($this);
        $this->pkey         = $this->pkey ? $this->pkey : $this->table_name ."id";
        $this->db           = Alt_Db::instance($this->db_instance);
    }

    /**
     * Creating column_create query dynamic column
     * @param $field
     * @param $value
     * @return array
     */
    protected function column_create($field, $value){
        $field = $this->quote($field);

        switch(gettype($value)){
            case "array":
            case "object":
                $dyncol = array();
                foreach($value as $key => $val){
                    list($key, $val) = $this->column_create($key, $val);
                    $dyncol[] = $key;
                    $dyncol[] = $val;
                }
                $value = count($dyncol) > 0 ? "COLUMN_CREATE (".implode(",",$dyncol).")" : "''";
                break;
            default:
                $value = $this->quote($value);
                break;
        }
        return array($field, $value);
    }

    /**
     * Creating column_get query for dynamic column
     * @param $column
     * @param array $array
     * @return string
     */
    protected function column_get($column, $array = array()){
        $str = "COLUMN_GET(";
        if(count($array) == 0) {
            $str .= $this->get_dyncolumn() . ", " . $this->quote($column) . ' AS CHAR';
        }else{
            $str .= $this->column_get($column, array_slice($array, 0, count($array)-1)) . ", " . $this->quote($array[count($array)-1]) . ' AS CHAR';
        }
        $str .= ")";
        return $str;
    }

    /**
     * Support dynamic field selection using dot
     * @param $field
     * @return string
     */
    protected function field($field){
        $column = explode(".", $field);
        $str = "";

        $dyncolumn = $this->get_dyncolumn();
        $dynfields = $this->get_dynfields();

        if($dyncolumn != null && array_key_exists($column[0], $dynfields)){
            $tmpcolumn = count($column) == 0 ? array() : array_slice($column, 1);
            $isall = $tmpcolumn[count($tmpcolumn)-1] == '*';
            $tmpcolumn = $isall ? array_slice($tmpcolumn, 0, count($tmpcolumn)-1) : $tmpcolumn;
            $format = $this->column_get($column[0], $tmpcolumn);
            if($isall) $format = "CAST(COLUMN_JSON(" . $format . ") AS CHAR)";
            $str =  $format;
        }else{
            $str = $field;
        }
        return $str;
    }

    /**
     * Support dynamic field selection using dot in any string, e.g. select field(x.y)
     * @param $field
     * @return mixed
     */
    protected function fieldstring($field){
        if($this->get_dyncolumn()){
            $regex = '/field\(([a-zA-z.\*]*)\)/i';
            preg_match_all($regex, $field, $match, PREG_PATTERN_ORDER);
            if(count($match) > 0) foreach($match[1] as $i => $item){
                $field = str_replace($match[0][$i], $this->field($item), $field);
            }
        }

        return $field;
    }

    /**
     * Support array filter, reformat to ".", e.g. post data from client x[y] will be formatted to x.y;
     * @param $key
     * @param $value
     * @param string $prev
     * @return array
     */
    protected function filter($key, $value, $prev = ""){
        $res = array();
        if(is_array($value)) {
            foreach($value as $k => $v){
                $res = array_merge($res, $this->filter($k, $v, ($prev != "" ? $prev . "." : "") . $key));
            }
        }else{
            $res[($prev != "" ? $prev . "." : "") . $key] = $value;
        }
        return $res;
    }

    /**
     * Get tablename
     * @param bool $returnview
     */
    protected function get_tablename($returnview = true){
        return $returnview && isset($this->view_name) ? $this->view_name : $this->table_name;
    }

    /**
     * Get table field
     * @param bool $returnview
     * @return array
     */
    protected function get_fields($returnview = true){
        return $returnview && isset($this->view_name) ? $this->view_fields : $this->table_fields;
    }

    /**
     * Get dynamic column name
     * @param bool $returnview
     * @return mixed
     */
    protected function get_dyncolumn($returnview = true){
        return $returnview && isset($this->view_dyncolumn) ? $this->view_dyncolumn : $this->table_dyncolumn;
    }

    /**
     * Get dynamic fields
     * @param bool $returnview
     * @return mixed
     */
    protected function get_dynfields($returnview = true){
        return $returnview && isset($this->view_dynfields) ? $this->view_dynfields : $this->table_dynfields;
    }

    /**
     * Get the where clause
     * @return string SQL group clause
     */
    protected function get_select($data = array()){
        $select = array();

        if($data['select'] != null && $data['select'] != ''){
            $data['select'] = $this->fieldstring($data['select']);
            $select[] = $data['select'];
        }

        return count($select) > 0 ? implode(", ", $select) : "*";
    }

    /**
     * Get the where clause
     * @return string SQL group clause
     */
    protected function get_where($data = array()){
        $where = array();

        if($data['where'] != null && $data['where'] != ''){
            if(gettype($data['where']) == "array"){
                $where = $data['where'];
            }else{
                $data['where'] = $this->field($data['where']);
                $where[] = $data['where'];
            }
        }

        foreach($data as $key => $value){
            $values = array();
            if(gettype($value) == "string" && $value != ""){
                $tmp = explode(" ", $value);
                if(in_array(trim($tmp[0]), array("like", "=", "<", ">", "<=", ">=", "in", "not"))){
                    $values = array($value);
                }else{
                    $values = array("like", "%" . $value . "%");
                }
            }

            if($this->table_fields[$key] !== null || $this->view_fields[$key] !== null){
                $where[] = $this->field($key) . " " . $values[0] . (count($values) > 1 ? " " . $this->quote($values[1]) : "");
            }else if($this->table_dynfields[$key] !== null){
                $tmp = $this->filter($key, $value);
                foreach($tmp as $k=>$v) {
                    $where[] = $this->field($k) . " like " . $this->quote("%" . $v . "%");
                }
            }
        }

        $fields = $this->get_fields();
        if($fields['isdeleted'] !== null && ($data['isdeleted'] == null || $data['isdeleted'] == '')){
            $where[] = 'isdeleted = 0';
        }

        return count($where) > 0 ? " where " . implode(" and ", $where) : "";
    }

    /**
     * Get the group clause
     * @return string SQL group clause
     */
    protected function get_group($data = array()) {
        if($data['group'] != null && $data['group'] != ''){
            return " GROUP BY " . $data['group'];
        }
        return "";
    }

    /**
     * Get the order clause
     * @return string SQL order clause
     */
    protected function get_order($data = array()) {
        if($data['order'] != null && $data['order'] != ''){
            return " ORDER BY " . $data['order'];
        }
        return "";
    }

    /**
     * Get the limit clause
     * @return string SQL limit clause
     */
    protected function get_limit($data = array()) {
        if($data['limit'] != null && $data['limit'] != ''){
            return " limit " . $data['limit'] . " offset " . ($data['offset'] ? $data['offset'] : 0);
        }
        return "";
    }

    /**
     * Get the join clause
     * @return string SQL join clause
     */
    protected function get_join($data = array()) {
        if($data['join'] != null && $data['join'] != ''){
            return $data['join'];
        }
        return "";
    }

    /**
     * @param  $instance_name
     * @return Alt_Dbo
     */
    public function reinstance($instance_name = null) {
        $this->db = Alt_Db::instance($instance_name ? $instance_name : $this->db_instance);
        return $this;
    }

    /**
     * Quote value
     * @param $string
     * @return mixed
     */
    public function quote($string){
        return $this->db->quote($string);
    }

    /**
     * Execute a query
     * @param $string
     * @return mixed
     */
    public function query($sql, $type = "array"){
        return $this->db->query($this->fieldstring($sql), $type);
    }

    /**
     * count designated row
     * @param array $data
     * @param boolean $returnsql, is returning sql
     * @return int num of row
     */
    public function count($data = array(), $returnsql = false) {
        // sql query
        $sql = "select count(*) as numofrow from " . ($this->view_name ? $this->view_name : $this->table_name) . $this->get_where($data);
        if($returnsql) return $sql;

        $res = $this->query($sql);
        return !empty($res) ? $res[0]['numofrow'] : 0;
    }

    /**
     * insert into database
     * @param usedefault bool set true if you want to use default value for empty table_fields set by DBO
     * @return int inserted row
     */
    public function insert($data, $returnsql = false) {
        // constructing sql
        $sql = "insert into " . $this->table_name . " (";

        // imploding field names
        if ($this->pkey != "" && $this->autoinc)
            unset($data[$this->pkey]);

        // set field values
        $fields = $this->get_fields(false);

        // add entry time and entry user if exist
        $userdata = System_Auth::get_user_data();
        if($fields['entrytime'] !== null)   $data['entrytime'] = $data['entrytime'] != '' ? $data['entrytime'] : time();
        if($fields['entryuser'] !== null)   $data['entryuser'] = $data['entryuser'] != '' ? $data['entryuser'] : $userdata['username'];

        // set fields and values to insert
        $fnames = array();
        $values = array();
        foreach ($data as $field => $value) if(isset($fields[$field])) {
            $fnames[] = $field;
            $values[] = $this->quote($value);
        }

        // dynamic columns
        $dyncolumn = $this->get_dyncolumn(false);
        $dynfields = $this->get_dynfields(false);
        if ($dyncolumn != null && count($dynfields) > 0) {
            $fnames[] = $dyncolumn;
            $dyncol = array();
            foreach ($dynfields as $field => $value) {
                list($field, $value) = $this->column_create($field, $value, 'COLUMN_CREATE');
                $dyncol[] = $field;
                $dyncol[] = $value;
            }
            $values[] = "COLUMN_CREATE(".implode(",",$dyncol).")";
        }
        // forge sql
        $sql .= implode(",",$fnames) .") values (". implode(",",$values) .")";
        if($returnsql) return $sql;

        // execute or return query
        $res = $this->query($sql);
        return $res;
    }

    /**
     * Gets data from database
     * @return array of data
     */
    public function get($data = array(), $returnsql = false) {
        if(isset($data[$this->pkey])){
            $data['where'] = $this->pkey ." = ". $this->quote($data[$this->pkey]);
            unset($data[$this->pkey]);
        }

        $sql = "select ".$this->get_select($data)." from ".$this->get_tablename() . $this->get_where($data).$this->get_group($data).$this->get_order($data).$this->get_join($data).$this->get_limit($data);
        if($returnsql) return $sql;

        // returning data
        $res = $this->query($sql, "array");
        if($this->table_dyncolumn) {
            for ($i = 0; $i < count($data); $i++) {
                unset($res[$i]->{$this->table_dyncolumn});
                foreach ($res[$i] as $key => $value) {
                    $key = strtolower($key);
                    $decoded = json_decode($value);
                    $res[$i]->$key = $decoded !== NULL && (gettype($decoded) == 'array' || gettype($decoded) == 'object') ? json_decode($value) : $value;
                }
            }
        }

        return $res;
    }

    public function retrieve($data = array(), $returnsql = false){
        $res = $this->get($data, $returnsql);

        if($returnsql) return $res;
        if(count($res) < 0) throw new Alt_Exception("Data tidak ditemukan!");
        return $res[0];
    }

    /**
     * update the data
     * @return int affected row
     */
    public function update($data, $returnsql = false) {
        // constructing sql
        $sql = "update " . $this->table_name . " set ";

        $pkey = $data[$this->pkey];
        unset($data[$this->pkey]);

        // set field values
        $table_fields = $this->get_fields(false);

        // add modified time and modified user if exist
        $userdata = System_Auth::get_user_data();
        if($table_fields['modifiedtime'] !== null)   $data['modifiedtime'] = $data['modifiedtime'] != '' ? $data['modifiedtime'] : time();
        if($table_fields['modifieduser'] !== null)   $data['modifieduser'] = $data['modifieduser'] != '' ? $data['modifieduser'] : $userdata['username'];

        // set fields and values to update
        $fields = array();
        foreach ($data as $field => $value) if(isset($table_fields[$field])) {
            $fields[] = $field." = ".$this->quote($value);
        }

        // dynamic columns
        $dyncolumn = $this->get_dyncolumn(false);
        $dynfields = $this->get_dynfields(false);
        if ($dyncolumn != null && count($dynfields) > 0) {
            $dyncol = array();
            foreach ($dynfields as $field => $value) if(isset($data[$field])) {
                list($field, $value) = $this->column_create($field, $value, 'COLUMN_CREATE');
                $dyncol[] = $field;
                $dyncol[] = $value;
            }
            if (count($dyncol) > 0)
                $fields[] = "$this->table_dyncolumn = COLUMN_CREATE(".implode(",",$dyncol).")";
        }

        // forge sql
        if(count($fields) <= 0)
            throw new Alt_Exception("No field to update");

        $sql .= implode(",",$fields) . ($data['where'] ? " where " . $data['where'] : (isset($pkey) ? " where " . $this->pkey . " = ". $this->quote($pkey) : ""));

        // return sql
        if($returnsql) return $sql;

        // execute
        $res = $this->query($sql);
        return $res;
    }

    /**
     * delete the data
     * @return int num of deleted data
     */
    public function delete($data, $returnsql = false) {
        if(isset($data[$this->pkey])){
            $data['where'] = $this->pkey ." = ". $this->quote($data[$this->pkey]);
            unset($data[$this->pkey]);
        }else if($this->get_where($data) == ' where ' && !isset($data['where'])){
            return -1;
        }

        // add modified time and modified user if exist
        $fields = $this->get_fields(false);
        if($fields['isdeleted'] !== null){
            $userdata = System_Auth::get_user_data();
            if($fields['deletedtime'] !== null)    $data['deletedtime'] = $data['deletedtime'] != '' ? $data['deletedtime'] : time();
            if($fields['deleteduser'] !== null)    $data['deleteduser'] = $data['deleteduser'] != '' ? $data['deleteduser'] : $userdata['username'];
            if($fields['isdeleted'] !== null)       $data['isdeleted'] = 1;

            return $this->update($data, $returnsql);
        }

        // return sql
        $sql = "delete from " . $this->table_name . $this->get_where($data);
        if($returnsql) return $sql;

        // execute
        $res = $this->query($sql);
        return $res;
    }

    public function keyvalues($data, $returnsql = false){
        $key = $data['key'] ? $data['key'] : $this->pkey;
        if(isset($data['value'])) $data['select'] = $key . ", " . $data['values'];
        $tmp = $this->get($data, $returnsql);

        if($returnsql) return $tmp;

        $ref = array();
        foreach($tmp as $item){
            $setvalue = $data['values'] ? $item[$data['values']] : $item;

            if($data['ismulti']){
                $ref[$item[$key]][] = $setvalue;
            }else{
                $ref[$item[$key]] = $setvalue;
            }
        }
        return $ref;
    }
}