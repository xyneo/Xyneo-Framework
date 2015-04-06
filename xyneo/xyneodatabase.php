<?php
if (! defined('XYNEO')) {
    throw new XyneoError("Direct access denied!");
}

/**
 *
 * @author AnarchyChampion
 *
 */
class XyneoDatabase extends PDO
{

    /**
     * PDO database connection
     *
     * @return void
     */
    public function __construct()
    {
        try {
            parent::__construct(DB_DRIVER . ":host=" . DB_HOST . ";dbname=" . DB_USE, DB_USER, DB_PASSWORD, array(
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ));
        } catch (PDOException $ex) {
            die("Database connection failed. Check your config file!");
        }
        $this->query("SET NAMES 'utf8'");
    }

    /**
     * Prepare SQL parts in private properties
     */
    private $type = "", $columns = array(), $from = "", $join = array(), $where = array(), $keys = array(), $values = array(), $groupby = array(), $having = array(), $h_keys = array(), $h_values = array(), $orderby = array(), $limit = false, $offset = false;

    /**
     * Built in select columns method
     *
     * @param array $columns
     * @return XyneoDataBase
     */
    public function xSelect($columns = array("*"))
    {
        if (count($columns) == 0)
            die("There are no columns selected.");
        foreach ($columns as $k => $v) {
            $v = filter_var($v, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
            if ($v == "")
                unset($columns[$k]);
        }
        $columns = array_values($columns);
        if (count($columns) == 0)
            die("The given columns are invalid.");
        $this->columns = $columns;
        return $this;
    }

    /**
     * Built in select table method - xSelect method is required
     *
     * @param string $table
     * @return XyneoDataBase
     */
    public function xFrom($table)
    {
        if (! $table || empty($table))
            die("The table name is empty.");
        $table = filter_var($table, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
        if (empty($table))
            die("The given table name is invalid.");
        $this->from = $table;
        return $this;
    }

    /**
     * Built in select join method - xFrom method is required
     *
     * @param string $table
     * @param string $link1
     * @param string $link2
     * @param string $type
     * @return XyneoDataBase
     */
    public function xJoin($table, $link1, $link2, $type = "")
    {
        $types = array(
            "LEFT",
            "RIGHT",
            "OUTER",
            "INNER",
            "LEFT OUTER",
            "RIGHT OUTER"
        );
        if (! $table || empty($table))
            die("The joinable table name is empty.");
        $table = filter_var($table, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
        if (empty($table))
            die("The given joinable table name is invalid.");
        $type = strtoupper(filter_var($type, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES));
        if ($type != "") {
            if (! in_array($type, $types))
                die("The given join type is invalid.");
            $type .= " ";
        } else
            $type = " ";
        if (empty($link1))
            die("The given first link is empty.");
        if (empty($link2))
            die("The given second link is empty.");
        $link1 = filter_var($link1, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
        if (empty($link1))
            die("The given first link is invalid.");
        $link2 = filter_var($link2, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
        if (empty($link2))
            die("The given second link is invalid.");
        $this->join[] = $type . "JOIN " . $table . " ON " . $link1 . " = " . $link2;
        return $this;
    }

    /**
     * Built in select condition method - xFrom method is required
     *
     * @param string $column
     * @param mixed $value
     * @param string $operator
     * @param string $logic
     * @return XyneoDataBase
     */
    public function xWhere($column, $value, $operator, $logic = "AND")
    {
        $logics = array(
            "AND",
            "OR",
            "NAND",
            "NOR",
            "XOR",
            "XNOR"
        );
        $logic = strtoupper(filter_var($logic, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES));
        if (! in_array($logic, $logics))
            die("The given logic gate is invalid.");
        $operators = array(
            "=",
            "<>",
            "!=",
            "<",
            ">",
            ">=",
            "<=",
            "REGEXP",
            "LIKE",
            "NOT LIKE",
            "IN",
            "NOT IN"
        );
        $operator = strtoupper($operator);
        if (! in_array($operator, $operators))
            die("The given operator is invalid.");
        if (empty($column))
            die("The given column is empty.");
        $column = filter_var($column, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
        if ($column == "")
            die("The given column is invalid.");
        $for_alias = $this->xGenerateAlias($column);
        $this->keys[] = $for_alias;
        if (count($this->where) == 0) {
            if ($operator == "IN" || $operator == "NOT IN")
                $cond = $column . " " . $operator . " (:" . $for_alias . ")";
            else
                $cond = $column . " " . $operator . " :" . $for_alias;
        } else {
            if ($operator == "IN" || $operator == "NOT IN")
                $cond = " " . $logic . " " . $column . " " . $operator . " (:" . $for_alias . ")";
            else
                $cond = " " . $logic . " " . $column . " " . $operator . " :" . $for_alias;
        }
        if (is_array($value)) {
            $cond = str_ireplace(":" . $for_alias, implode(", ", $value), $cond);
        }
        $this->where[] = $cond;
        $this->values[] = $value;
        return $this;
    }

    /**
     * Built in group by method
     *
     * @param string $column
     * @return XyneoDataBase
     */
    public function xGroup($column)
    {
        if (empty($column))
            die("The given column is empty.");
        $column = filter_var($column, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
        if ($column == "")
            die("The given column is invalid.");
        $this->groupby[] = $column;
        return $this;
    }

    /**
     * Built in having method - xGroup method is required
     *
     * @param string $column
     * @param mixed $value
     * @param string $operator
     * @param string $logic
     * @return XyneoDataBase
     */
    public function xHaving($column, $value, $operator, $logic = "AND")
    {
        $logics = array(
            "AND",
            "OR",
            "NAND",
            "NOR",
            "XOR",
            "XNOR"
        );
        $logic = strtoupper(filter_var($logic, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES));
        if (! in_array($logic, $logics))
            die("The given logic gate is invalid.");
        $operators = array(
            "=",
            "<>",
            "!=",
            "<",
            ">",
            ">=",
            "<=",
            "LIKE",
            "NOT LIKE",
            "IN",
            "NOT IN"
        );
        $operator = strtoupper($operator);
        if (! in_array($operator, $operators))
            die("The given operator is invalid.");
        if (empty($column))
            die("The given column is empty.");
        $column = filter_var($column, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
        if ($column == "")
            die("The given column is invalid.");
        $for_alias = $this->xGenerateAlias($column);
        $this->h_keys[] = $for_alias;
        if (count($this->having) == 0) {
            if ($operator == "IN" || $operator == "NOT IN")
                $cond = $column . " " . $operator . " (:having_" . $for_alias . ")";
            else
                $cond = $column . " " . $operator . " :having_" . $for_alias;
        } else {
            if ($operator == "IN" || $operator == "NOT IN")
                $cond = " " . $logic . " " . $column . " " . $operator . " (:having_" . $for_alias . ")";
            else
                $cond = " " . $logic . " " . $column . " " . $operator . " :having_" . $for_alias;
        }
        if (is_array($value)) {
            $cond = str_ireplace(":having_" . $for_alias, current($value), $cond);
        }
        $this->having[] = $cond;
        $this->h_values[] = $value;
        return $this;
    }

    /**
     * Built in order by method - xFrom method is required
     *
     * @param string $orderby
     * @param string $order
     * @return XyneoDataBase
     */
    public function xOrder($orderby, $order = "ASC")
    {
        if (empty($orderby))
            die("The given column is empty.");
        $orderby = filter_var($orderby, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
        if ($orderby == "")
            die("The given column is invalid.");
        $orders = array(
            "ASC",
            "DESC"
        );
        $order = strtoupper(filter_var($order, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES));
        if (! in_array($order, $orders))
            die("The given order is invalid.");
        $this->orderby[] = $orderby . " " . $order;
        return $this;
    }

    /**
     * Built in select limit method - xFrom method is required
     *
     * @param integer $limit
     * @param integer $offset
     * @return XyneoDataBase
     */
    public function xLimit($limit, $offset = false)
    {
        if (empty($limit) && $limit != 0)
            die("The given limit number is empty.");
        $limit = filter_var($limit, FILTER_SANITIZE_NUMBER_INT);
        if (preg_match("/\D/", $limit) || ($limit <= 0 || $limit > 4294967295))
            die("The given limit number is invalid.");
        $this->limit = $limit;
        if ($offset !== false) {
            if (empty($offset) && $offset != 0)
                die("The given offset number is empty.");
            $offset = filter_var($offset, FILTER_SANITIZE_NUMBER_INT);
            if (preg_match("/\D/", $offset) || ($offset < 0 || $offset > 4294967295))
                die("The given offset number is invalid.");
            $this->offset = $offset;
        }
        return $this;
    }

    /**
     * Built in select method - if there are no parameters, then xFrom method is required
     *
     * @param strging $table
     * @param array $columns
     * @param array $cond
     * @return PDOStatement
     */
    public function xGet($table = null, $columns = array("*"), $cond = false)
    {
        if ($table != "") {
            $this->xSelect($columns)->xFrom($table);
            if ($cond) {
                foreach ($cond as $column => $value) {
                    $this->xWhere($column, $value, "=");
                }
            }
            return $this->xGet();
        } else {
            if (empty($this->from))
                die("The given table name is empty.");
            $query_string = "SELECT " . implode(", ", $this->columns) . " FROM " . $this->from;
            if (count($this->join) > 0)
                $query_string .= " " . implode(" ", $this->join);
            if (count($this->where) > 0)
                $query_string .= " WHERE " . implode($this->where);
            if (count($this->groupby) > 0)
                $query_string .= " GROUP BY " . implode(", ", $this->groupby);
            if (count($this->having) > 0)
                $query_string .= " HAVING " . implode($this->having);
            if (count($this->orderby) > 0)
                $query_string .= " ORDER BY " . implode(", ", $this->orderby);
            if ($this->limit !== false)
                $query_string .= " LIMIT " . ($this->offset !== false ? $this->offset . ", " : "") . $this->limit;
            $sql = $this->prepare($query_string);
            if (count($this->where) > 0) {
                foreach ($this->values as $k => $v) {
                    if (! is_array($v)) {
                        $sql->bindValue(":" . $this->keys[$k], $v);
                    }
                }
            }
            if (count($this->having) > 0) {
                foreach ($this->h_values as $k => $v) {
                    if (! is_array($v)) {
                        $sql->bindValue(":having_" . $this->h_keys[$k], $v);
                    }
                }
            }
        }
        $sql->execute();
        $this->xReset();
        return $sql;
    }

    /**
     * Built in insert prepare method
     *
     * @param string $table
     * @param array $fields
     * @param string $select
     * @param string $link1
     * @param string $link2
     * @param string $column
     * @param mixed $value
     * @return XyneoDataBase
     */
    public function xInsert($table, $fields, $select = false, $link1 = false, $link2 = false, $column = false, $value = false)
    {
        $this->type = "insert";
        if (! $table || empty($table))
            die("The given table name is empty.");
        $table = filter_var($table, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
        if (empty($table))
            die("The given table name is invalid.");
        $this->from = $table;
        $columns = array_keys($fields);
        $values = array_values($fields);
        foreach ($columns as $k => $v) {
            $v = filter_var($v, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
            if ($v == "")
                die("The given " . ($k + 1) . ". column for update is invalid.");
            $this->columns[] = $v;
            if (! is_array($values[$k])) {
                $this->h_keys[] = ":" . $this->xGenerateAlias($v);
            } else {
                $this->h_keys[] = current($values[$k]);
            }
        }
        $this->h_values = $values;
        if ($select !== false) {
            if (empty($select))
                die("The given joinable table name is empty.");
            $select = filter_var($select, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
            if (empty($select))
                die("The given joinable table name is invalid.");
            if (empty($link1))
                die("The given first link is empty.");
            $link1 = filter_var($link1, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
            if ($link1 == "")
                die("The given first link is invalid.");
            if (empty($link2))
                die("The given second link is empty.");
            $link2 = filter_var($link2, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
            if (! in_array($link2, $this->columns))
                die("The given second link is invalid.");
            if (empty($column))
                die("The given column in the where clause is empty.");
            $column = filter_var($column, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
            if ($column == "")
                die("The given column in the where clause is invalid.");
            $this->having = $link2;
            $this->keys = $column;
            $this->values = $value;
            $for_alias = $this->xGenerateAlias($column);
            $this->join = "SELECT " . $link1 . " FROM " . $select . " WHERE " . $column . " = :where_" . $for_alias . " LIMIT 1";
            if (is_array($value)) {
                $this->join = str_ireplace(":where_" . $for_alias, current($value), $this->join);
            }
        }
        return $this;
    }

    /**
     * Built in update prepare method
     *
     * @param string $table
     * @param array $fields
     * @return XyneoDataBase
     */
    public function xUpdate($table, $fields)
    {
        $this->type = "update";
        if (! $table || empty($table))
            die("The given table name is empty.");
        $table = filter_var($table, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
        if (empty($table))
            die("The given table name is invalid.");
        $this->from = $table;
        if (! $fields || ! is_array($fields))
            die("The given fields are empty or added as invalid parameter.");
        $columns = array_keys($fields);
        $values = array_values($fields);
        $for_alias = array();
        foreach ($columns as $k => $v) {
            $v = filter_var($v, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
            if ($v == "")
                die("The given " . ($k + 1) . ". column for update is invalid.");
            $for_alias[] = $this->xGenerateAlias($v);
            if (! is_array($values[$k])) {
                $this->columns[] = $v . " = :set_" . $for_alias[$k];
            } else {
                $this->columns[] = $v . " = " . current($values[$k]);
            }
        }
        $this->h_keys = $for_alias;
        $this->h_values = $values;
        return $this;
    }

    /**
     * Built in delete prepare method
     *
     * @param string $table
     * @return XyneoDatabase
     */
    public function xDelete($table)
    {
        $this->type = "delete";
        if (! $table || empty($table))
            die("The given table name is empty.");
        $table = filter_var($table, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
        if (empty($table))
            die("The given table name is invalid.");
        $this->from = $table;
        return $this;
    }

    /**
     * Built in multifunctional method - the first parameter must be
     * INSERT, UPDATE or DELETE;
     * If the second parameter isn't given, then the xInsert, xUpdate or
     * xSelect+xFrom+xWhere method is required (depending on the first parameter);
     * If the second parameter is given, then it must be an array like
     * as the example below
     *
     * $options = array(
     * "table" => [string] table_name,
     * "fields" => array(
     * "column1" => [string|int|float] value1,
     * "column2" => [string|int|float] value2,
     * "column3" => [string|int|float] value3
     * ),
     * "condition" => array(
     * "column1" => [string|int|float] value1,
     * "column2" => [string|int|float] value2
     * )
     * );
     *
     * @param string $type
     * @param array $options
     * @return PDOStatement
     */
    public function xSet($type = null, $options = false)
    {
        if ($this->type && ! $type) {
            $type = $this->type;
        }
        if (empty($type))
            die("The sql command type is empty.");
        $types = array(
            "INSERT",
            "UPDATE",
            "DELETE"
        );
        $type = strtoupper(filter_var($type, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES));
        if (! in_array($type, $types))
            die("The sql command type is invalid.");
        if ($options !== false) {
            switch ($type) {
                case "INSERT":
                    return $this->xInsert($options["table"], $options["fields"])->xSet();
                    break;
                case "UPDATE":
                    $this->xUpdate($options["table"], $options["fields"]);
                    foreach ($options["condition"] as $column => $value) {
                        $this->xWhere($column, $value, "=");
                    }
                    return $this->xSet();
                    break;
                case "DELETE":
                    $this->xDelete($options["table"]);
                    foreach ($options["condition"] as $column => $value) {
                        $this->xWhere($column, $value, "=");
                    }
                    return $this->xSet();
                    break;
            }
        } else {
            switch ($type) {
                case "INSERT":
                    if (empty($this->from))
                        die("The given table name is empty.");
                    $query_string = "INSERT INTO " . $this->from . " (" . implode(", ", $this->columns) . ") VALUES(" . implode(", ", $this->h_keys) . ")";
                    if ($this->having !== array())
                        $query_string = str_replace(":" . $this->having, "(" . $this->join . ")", $query_string);
                    $sql = $this->prepare($query_string);
                    if ($this->having !== array())
                        $sql->bindValue(":where_" . $this->keys, $this->values);
                    foreach ($this->h_values as $k => $v)
                        if ($this->h_keys[$k] != ":" . $this->having) {
                            if (! is_array($v)) {
                                $sql->bindValue($this->h_keys[$k], $v);
                            }
                        }
                    break;
                case "UPDATE":
                    if (empty($this->from))
                        die("The given table name is empty.");
                    $query_string = "UPDATE " . $this->from;
                    if (count($this->join) > 0)
                        $query_string .= " " . implode(" ", $this->join);
                    $query_string .= " SET " . implode(", ", $this->columns);
                    if (count($this->where) > 0)
                        $query_string .= " WHERE " . implode($this->where);
                    $sql = $this->prepare($query_string);
                    foreach ($this->h_values as $k => $v) {
                        if (! is_array($v)) {
                            $sql->bindValue(":set_" . $this->h_keys[$k], $v);
                        }
                    }
                    if (count($this->where) > 0) {
                        foreach ($this->values as $k => $v) {
                            if (! is_array($v)) {
                                $sql->bindValue(":" . $this->keys[$k], $v);
                            }
                        }
                    }
                    break;
                case "DELETE":
                    $query_string = "DELETE ";
                    if (count($this->columns) > 0)
                        $query_string .= implode(", ", $this->columns);
                    if (empty($this->from))
                        die("The given table name is empty.");
                    $query_string .= " FROM " . $this->from;
                    if (count($this->join) > 0)
                        $query_string .= " " . implode(" ", $this->join);
                    if (count($this->where) > 0)
                        $query_string .= " WHERE " . implode($this->where);
                    $sql = $this->prepare($query_string);
                    if (count($this->where) > 0) {
                        foreach ($this->values as $k => $v) {
                            if (! is_array($v)) {
                                $sql->bindValue(":" . $this->keys[$k], $v);
                            }
                        }
                    }
                    break;
            }
        }
        $sql->execute();
        $this->xReset();
        return $sql;
    }

    /**
     * Reseter method for private properties
     *
     * @return void
     */
    private function xReset()
    {
        $this->type = "";
        $this->columns = array();
        $this->from = "";
        $this->join = array();
        $this->where = array();
        $this->keys = array();
        $this->values = array();
        $this->groupby = array();
        $this->having = array();
        $this->h_keys = array();
        $this->h_values = array();
        $this->orderby = array();
        $this->limit = false;
        $this->offset = false;
    }

    /**
     * Built in record check
     *
     * @param mixed $search
     * @param string $table
     * @param string $column
     * @return boolean
     */
    public function xIsInDb($search, $table, $column)
    {
        if (empty($column)) {
            die("No column specified");
        }
        $search_query = $this->xSelect(array(
            $column
        ))
            ->xFrom($table)
            ->xWhere($column, $search, "=")
            ->xGet();
        return (boolean) $search_query->rowCount();
    }

    /**
     * Generate alias for PDO bindValue
     *
     * @param string $column
     * @return string
     */
    private function xGenerateAlias($column)
    {
        $delimiter = "_";
        setlocale(LC_ALL, "hu_HU.UTF-8");
        $clean = iconv("UTF-8", "ASCII//TRANSLIT", $column);
        $clean = preg_replace("/[^a-zA-Z0-9\.\/_|+ -]/", "", $clean);
        $clean = strtolower(trim($clean, $delimiter));
        $clean = preg_replace("/[\.\/_|+ -]+/", $delimiter, $clean);

        return trim($clean, $delimiter);
    }
}