<?php if ( ! defined("XYNEO") ) die("Direct access denied!");

class XyneoDataBase extends PDO
{
// PDO database connection
    function __construct()
    {
        try
        {
          parent::__construct(
                              DB_DRIVER.":host=".DB_HOST.";dbname=".DB_USE,
                              DB_USER, DB_PASSWORD,
                              array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
                              );
        }
        catch(PDOException $ex)
        {
          die("Database connection failed. Check your config file!");
        }
        $this->query("SET NAMES 'utf8'");
    }

// Prepare SQL parts in private properties
    private $columns = array(), $from = "", $join = array(), $where = array(),
            $keys = array(), $values = array(), $groupby = array(),
            $having = array(), $h_keys = array(), $h_values = array(),
            $orderby = array(), $limit = false, $offset = false;

// Built in select columns method
    public function xSelect($columns=array("*"))
    {
        if (count($columns) == 0)
          die("There are no columns selected.");
        foreach ($columns as $k => $v)
        {
          $v = filter_var($v, FILTER_SANITIZE_STRING,
                          FILTER_FLAG_NO_ENCODE_QUOTES);
          if ($v == "")
            unset($columns[$k]);
        }
        $columns = array_values($columns);
        if (count($columns) == 0)
          die("The given columns are invalid.");
        $this->columns = $columns;
        return $this;
    }

// Built in select table method - xSelect method is required
    public function xFrom($table)
    {
        if (!$table || empty($table))
          die("The table name is empty.");
        $table = filter_var($table, FILTER_SANITIZE_STRING,
                            FILTER_FLAG_NO_ENCODE_QUOTES);
        if (empty($table))
          die("The given table name is invalid.");
        $this->from = $table;
        return $this;
    }

// Built in select join method - xFrom method is required
    public function xJoin($table, $link1, $link2, $type="")
    {
        $types = array("LEFT", "RIGHT", "OUTER", "INNER", "LEFT OUTER",
                       "RIGHT OUTER");
        if (!$table || empty($table))
          die("The joinable table name is empty.");
        $table = filter_var($table, FILTER_SANITIZE_STRING,
                            FILTER_FLAG_NO_ENCODE_QUOTES);
        if (empty($table))
          die("The given joinable table name is invalid.");
        $type = strtoupper(filter_var($type, FILTER_SANITIZE_STRING,
                                      FILTER_FLAG_NO_ENCODE_QUOTES));
        if ($type != "")
        {
          if (!in_array($type, $types))
            die("The given join type is invalid.");
          $type .= " ";
        }
        else
          $type = " ";
        if (empty($link1))
          die("The given first link is empty.");
        if (empty($link2))
          die("The given second link is empty.");
        $link1 = filter_var($link1, FILTER_SANITIZE_STRING,
                            FILTER_FLAG_NO_ENCODE_QUOTES);
        if (empty($link1))
          die("The given first link is invalid.");
        $link2 = filter_var($link2, FILTER_SANITIZE_STRING,
                            FILTER_FLAG_NO_ENCODE_QUOTES);
        if (empty($link2))
          die("The given second link is invalid.");
        $this->join[] = $type ."JOIN ". $table ." ON ". $link1 ." = ". $link2;
        return $this;
    }

// Built in select condition method - xFrom method is required
    public function xWhere($column, $value, $operator, $logic="AND")
    {
        $logics = array("AND", "OR", "NAND", "NOR", "XOR", "XNOR");
        $logic = strtoupper(filter_var($logic, FILTER_SANITIZE_STRING,
                                       FILTER_FLAG_NO_ENCODE_QUOTES));
        if (!in_array($logic, $logics))
          die("The given logic gate is invalid.");
        $operators = array("=", "<>", "!=", "<", ">", ">=", "<=", "LIKE",
                           "NOT LIKE", "IN", "NOT IN");
        $operator = strtoupper($operator);
        if (!in_array($operator, $operators))
          die("The given operator is invalid.");
        if (empty($column))
          die("The given column is empty.");
        $column = filter_var($column, FILTER_SANITIZE_STRING,
                             FILTER_FLAG_NO_ENCODE_QUOTES);
        if ($column == "")
          die("The given column is invalid.");
        $for_alias = str_replace(".", "_", $column);
        $this->keys[] = $for_alias;
        if (count($this->where) == 0)
        {
          if ($operator == "IN" || $operator == "NOT IN")
            $this->where[] = $column ." ". $operator ." (:". $for_alias .")";
          else
            $this->where[] = $column ." ". $operator ." :". $for_alias;
        }
        else
        {
          if ($operator == "IN" || $operator == "NOT IN")
            $this->where[] = " ". $logic ." ". $column ." ". $operator ." (:".
                             $for_alias .")";
          else
            $this->where[] = " ". $logic ." ". $column ." ". $operator ." :".
                             $for_alias;
        }
        $this->values[] = $value;
        return $this;
    }

// Built in group by method
    public function xGroup($column)
    {
        if (empty($column))
          die("The given column is empty.");
        $column = filter_var($column, FILTER_SANITIZE_STRING,
                             FILTER_FLAG_NO_ENCODE_QUOTES);
        if ($column == "")
          die("The given column is invalid.");
        $this->groupby[] = $column;
        return $this;
    }

// Built in having method - xGroup method is required
    public function xHaving($column, $value, $operator, $logic="AND")
    {
        $logics = array("AND", "OR", "NAND", "NOR", "XOR", "XNOR");
        $logic = strtoupper(filter_var($logic, FILTER_SANITIZE_STRING,
                                       FILTER_FLAG_NO_ENCODE_QUOTES));
        if (!in_array($logic, $logics))
          die("The given logic gate is invalid.");
        $operators = array("=", "<>", "!=", "<", ">", ">=", "<=", "LIKE",
                           "NOT LIKE", "IN", "NOT IN");
        $operator = strtoupper($operator);
        if (!in_array($operator, $operators))
          die("The given operator is invalid.");
        if (empty($column))
          die("The given column is empty.");
        $column = filter_var($column, FILTER_SANITIZE_STRING,
                             FILTER_FLAG_NO_ENCODE_QUOTES);
        if ($column == "")
          die("The given column is invalid.");
        $for_alias = str_replace(".", "_", $column);
        $this->h_keys[] = $for_alias;
        if (count($this->having) == 0)
        {
          if ($operator == "IN" || $operator == "NOT IN")
            $this->having[] = $column ." ". $operator ." (:having_".
                              $for_alias .")";
          else
            $this->having[] = $column ." ". $operator ." :having_". $for_alias;
        }
        else
        {
          if ($operator == "IN" || $operator == "NOT IN")
            $this->having[] = " ". $logic ." ". $column ." ". $operator
                              ." (:having_". $for_alias .")";
          else
            $this->having[] = " ". $logic ." ". $column ." ". $operator
                              ." :having_". $for_alias;
        }
        $this->h_values[] = $value;
        return $this;
    }

// Built in order by method - xFrom method is required
    public function xOrder($orderby, $order="ASC")
    {
        if (empty($orderby))
          die("The given column is empty.");
        $orderby = filter_var($orderby, FILTER_SANITIZE_STRING,
                              FILTER_FLAG_NO_ENCODE_QUOTES);
        if ($orderby == "")
          die("The given column is invalid.");
        $orders = array("ASC", "DESC");
        $order = strtoupper(filter_var($order, FILTER_SANITIZE_STRING,
                                       FILTER_FLAG_NO_ENCODE_QUOTES));
        if (!in_array($order, $orders))
          die("The given order is invalid.");
        $this->orderby[] = $orderby ." ". $order;
        return $this;
    }

// Built in select limit method - xFrom method is required
    public function xLimit($limit, $offset=false)
    {
        if (empty($limit) && $limit != 0)
          die("The given limit number is empty.");
        $limit = filter_var($limit, FILTER_SANITIZE_NUMBER_INT);
        if (preg_match("/\D/", $limit) || ($limit <= 0 || $limit > 4294967295))
          die("The given limit number is invalid.");
        $this->limit = $limit;
        if ($offset !== false)
        {
          if (empty($offset) && $offset != 0)
            die("The given offset number is empty.");
          $offset = filter_var($offset, FILTER_SANITIZE_NUMBER_INT);
        if (preg_match("/\D/", $offset) ||
            ($offset < 0 || $offset > 4294967295))
            die("The given offset number is invalid.");
          $this->offset = $offset;
        }
    }

// Built in select method - if there are no parameters,
//                          then xFrom method is required
    public function xGet($table="", $columns=array("*"), $cond=false)
    {
        if ($table != "")
        {
          $table = filter_var($table, FILTER_SANITIZE_STRING,
                              FILTER_FLAG_NO_ENCODE_QUOTES);
          if (empty($table))
            die("The given table name is invalid.");
          if (count($columns) == 0)
            die("There are no columns selected.");
          foreach ($columns as $k => $v)
          {
            $v = filter_var($v, FILTER_SANITIZE_STRING,
                            FILTER_FLAG_NO_ENCODE_QUOTES);
            if ($v == "")
              unset($columns[$k]);
          }
          $columns = array_values($columns);
          if (count($columns) == 0)
            die("The given columns are invalid.");
          $query_string = "SELECT ". implode(", ", $columns) ." FROM ". $table;
          if ($cond)
          {
            $selected_columns = array_keys($cond);
            $protected_columns = array();
            foreach ($selected_columns as $k => $v)
            {
              $v = filter_var($v, FILTER_SANITIZE_STRING,
                              FILTER_FLAG_NO_ENCODE_QUOTES);
              if ($v == "")
                die("The given ". ($k + 1) .". column is invalid.");
              $selected_columns[$k] = $v;
              $protected_columns[] = $v ." = :". $v;
            }
            $query_string .= " WHERE ". implode(" AND ", $protected_columns);
            $sql = $this->prepare($query_string);
            $selected_values = array_values($cond);
            foreach ($selected_values as $k => $v)
              $sql->bindValue(":". $selected_columns[$k], $v);
          }
          else
            $sql = $this->prepare($query_string);
        }
        else
        {
          if (empty($this->from))
            die("The given table name is empty.");
          $query_string = "SELECT ". implode(", ", $this->columns) ." FROM ".
                          $this->from;
          if (count($this->join) > 0)
            $query_string .= " ". implode(" ", $this->join);
          if (count($this->where) > 0)
            $query_string .= " WHERE ". implode($this->where);
          if (count($this->groupby) > 0)
            $query_string .= " GROUP BY ". implode(", ", $this->groupby);
          if (count($this->having) > 0)
            $query_string .= " HAVING ". implode($this->having);
          if (count($this->orderby) > 0)
            $query_string .= " ORDER BY ". implode(", ", $this->orderby);
          if ($this->limit !== false)
            $query_string .= " LIMIT ". ($this->offset !== false ? $this->offset
                             .", " : "") . $this->limit;
          $sql = $this->prepare($query_string);
          if (count($this->where) > 0)
          {
            foreach ($this->values as $k => $v)
              $sql->bindValue(":". $this->keys[$k], $v);
          }
          if (count($this->having) > 0)
          {
            foreach ($this->h_values as $k => $v)
              $sql->bindValue(":having_". $this->h_keys[$k], $v);
          }
        }
        $sql->execute();
        $this->xReset();
        return $sql;
    }

// Built in insert prepare method
    public function xInsert($table, $fields, $select=false, $link1=false,
                            $link2=false, $column=false, $value=false)
    {
        if (!$table || empty($table))
          die("The given table name is empty.");
        $table = filter_var($table, FILTER_SANITIZE_STRING,
                            FILTER_FLAG_NO_ENCODE_QUOTES);
        if (empty($table))
          die("The given table name is invalid.");
        $this->from = $table;
        $columns = array_keys($fields);
        foreach ($columns as $k => $v)
        {
          $v = filter_var($v, FILTER_SANITIZE_STRING,
                          FILTER_FLAG_NO_ENCODE_QUOTES);
          if ($v == "")
            die("The given ". ($k + 1) .". column for update is invalid.");
          $this->columns[] = $v;
          $this->h_keys[] = ":". $v;
        }
        $this->h_values = array_values($fields);
        if ($select !== false)
        {
          if (empty($select))
            die("The given joinable table name is empty.");
          $select = filter_var($select, FILTER_SANITIZE_STRING,
                               FILTER_FLAG_NO_ENCODE_QUOTES);
          if (empty($select))
            die("The given joinable table name is invalid.");
          if (empty($link1))
            die("The given first link is empty.");
          $link1 = filter_var($link1, FILTER_SANITIZE_STRING,
                              FILTER_FLAG_NO_ENCODE_QUOTES);
          if ($link1 == "")
            die("The given first link is invalid.");
          if (empty($link2))
            die("The given second link is empty.");
          $link2 = filter_var($link2, FILTER_SANITIZE_STRING,
                              FILTER_FLAG_NO_ENCODE_QUOTES);
          if (!in_array($link2, $this->columns))
            die("The given second link is invalid.");
          if (empty($column))
            die("The given column in the where clause is empty.");
          $column = filter_var($column, FILTER_SANITIZE_STRING,
                               FILTER_FLAG_NO_ENCODE_QUOTES);
          if ($column == "")
            die("The given column in the where clause is invalid.");
          $this->having = $link2;
          $this->keys = $column;
          $this->values = $value;
          $this->join = "SELECT ". $link1 ." FROM ". $select ." WHERE ". $column
                        ." = :where_". $column ." LIMIT 1";
        }
        return $this;
    }

// Built in update prepare method
    public function xUpdate($table, $fields)
    {
        if (!$table || empty($table))
          die("The given table name is empty.");
        $table = filter_var($table, FILTER_SANITIZE_STRING,
                            FILTER_FLAG_NO_ENCODE_QUOTES);
        if (empty($table))
          die("The given table name is invalid.");
        $this->from = $table;
        if (!$fields || !is_array($fields))
          die("The given fields are empty or added as invalid parameter.");
        $columns = array_keys($fields);
        $for_alias = array();
        foreach ($columns as $k => $v)
        {
          $v = filter_var($v, FILTER_SANITIZE_STRING,
                          FILTER_FLAG_NO_ENCODE_QUOTES);
          if ($v == "")
            die("The given ". ($k + 1) .". column for update is invalid.");
          $for_alias[] = str_replace(".", "_", $v);
          $this->columns[] = $v ." = :set_". $for_alias[$k];
        }
        $this->h_keys = $for_alias;
        $this->h_values = array_values($fields);
        return $this;
    }

// Built in multifunctional method - the first parameter must be
//                                   INSERT, UPDATE or DELETE;
// If the second parameter isn't given, then the xInsert, xUpdate or
// xSelect+xFrom+xWhere method is required (depending on the first parameter);
// If the second parameter is given, then it must be an array like
// as the example below
/*
  $options = array(
    "table" => [string] table_name,
    "fields" => array(
      "column1" => [string|int|float] value1,
      "column2" => [string|int|float] value2,
      "column3" => [string|int|float] value3
    ),
    "condition" => array(
      "column1" => [string|int|float] value1,
      "column2" => [string|int|float] value2
    )
  );
*/
    public function xSet($type, $options=false)
    {
        if (empty($type))
          die("The sql command type is empty.");
        $types = array("INSERT", "UPDATE", "DELETE");
        $type = strtoupper(filter_var($type, FILTER_SANITIZE_STRING,
                                      FILTER_FLAG_NO_ENCODE_QUOTES));
        if (!in_array($type, $types))
          die("The sql command type is invalid.");
        if ($options !== false)
        {
          switch ($type)
          {
            case "INSERT":
              if (!isset($options["table"]) || empty($options["table"]))
                die("The given table name is empty.");
              $table = filter_var($options["table"], FILTER_SANITIZE_STRING,
                                  FILTER_FLAG_NO_ENCODE_QUOTES);
              if (empty($table))
                die("The given table name is invalid.");
              if (!isset($options["fields"]) || !is_array($options["fields"]))
                die("The given fields are empty or 
                     added as invalid parameter.");
              $columns = array_keys($options["fields"]);
              $for_alias = array();
              foreach ($columns as $k => $v)
              {
                $v = filter_var($v, FILTER_SANITIZE_STRING,
                                FILTER_FLAG_NO_ENCODE_QUOTES);
                if ($v == "")
                  die("The given ". ($k + 1) .". column is invalid.");
                $columns[$k] = $v;
                $for_alias[] = ":". $columns[$k];
              }
              $query_string = "INSERT INTO ". $table ." (". implode(", ",
                              $columns) .") VALUES (". implode(", ", $for_alias)
                               .")";
              $sql = $this->prepare($query_string);
              $values = array_values($options["fields"]);
              foreach ($values as $k => $v)
                $sql->bindValue($for_alias[$k], $v);
            break;
            case "UPDATE":
              if (!isset($options["table"]) || empty($options["table"]))
                die("The given table name is empty.");
              $table = filter_var($options["table"], FILTER_SANITIZE_STRING,
                                  FILTER_FLAG_NO_ENCODE_QUOTES);
              if (empty($table))
                die("The given table name is invalid.");
              if (!isset($options["fields"]) || !is_array($options["fields"]))
                die("The given fields are empty or 
                     added as invalid parameter.");
              $update_columns = array_keys($options["fields"]);
              $protected_update_columns = array();
              foreach ($update_columns as $k => $v)
              {
                $v = filter_var($v, FILTER_SANITIZE_STRING,
                                FILTER_FLAG_NO_ENCODE_QUOTES);
                if ($v == "")
                  die("The given ". ($k + 1) .". column 
                      for update is invalid.");
                $update_columns[$k] = str_replace(".", "_", $v);
                $protected_update_columns[] = $v ." = :". $update_columns[$k];
              }
              if (!isset($options["condition"]) ||
                  !is_array($options["condition"]))
                die("The given condition are empty or 
                    added as invalid parameter.");
              $columns = array_keys($options["condition"]);
              $protected_columns = array();
              foreach ($columns as $k => $v)
              {
                $v = filter_var($v, FILTER_SANITIZE_STRING,
                                FILTER_FLAG_NO_ENCODE_QUOTES);
                if ($v == "")
                  die("The given ". ($k + 1) .". column is invalid.");
                $columns[$k] = str_replace(".", "_", $v);
                $protected_columns[] = $v ." = :where_". $columns[$k];
              }
              $query_string = "UPDATE ". $table ." SET ". implode(", ",
                              $protected_update_columns) ." WHERE ".
                              implode(" AND ", $protected_columns);
              $sql = $this->prepare($query_string);
              $values = array_values($options["fields"]);
              foreach ($values as $k => $v)
                $sql->bindValue(":". $update_columns[$k], $v);
              $values = array_values($options["condition"]);
              foreach ($values as $k => $v)
                $sql->bindValue(":where_". $columns[$k], $v);
            break;
            case "DELETE":
              if (!isset($options["table"]) || empty($options["table"]))
                die("The given table name is empty.");
              $table = filter_var($options["table"], FILTER_SANITIZE_STRING,
                                  FILTER_FLAG_NO_ENCODE_QUOTES);
              if (empty($table))
                die("The given table name is invalid.");
              if (!isset($options["condition"]) ||
                  !is_array($options["condition"]))
                die("The given condition are empty or 
                    added as invalid parameter.");
              $columns = array_keys($options["condition"]);
              $protected_columns = array();
              foreach ($columns as $k => $v)
              {
                $v = filter_var($v, FILTER_SANITIZE_STRING,
                                FILTER_FLAG_NO_ENCODE_QUOTES);
                if ($v == "")
                  die("The given ". ($k + 1) .". column is invalid.");
                $columns[$k] = str_replace(".", "_", $v);
                $protected_columns[] = $v ." = :". $columns[$k];
              }
              $query_string = "DELETE FROM ". $table ." WHERE ".
                              implode(" AND ", $protected_columns);
              $sql = $this->prepare($query_string);
              $values = array_values($options["condition"]);
              foreach ($values as $k => $v)
                $sql->bindValue(":". $columns[$k], $v);
            break;
          }
        }
        else
        {
          switch ($type)
          {
            case "INSERT":
              if (empty($this->from))
                die("The given table name is empty.");
              $query_string = "INSERT INTO ". $this->from ." (".
                              implode(", ", $this->columns) .") VALUES(".
                              implode(", ", $this->h_keys) .")";
              if ($this->having !== array())
                $query_string = str_replace(":". $this->having, "(".
                                $this->join .")", $query_string);
              $sql = $this->prepare($query_string);
              if ($this->having !== array())
                $sql->bindValue(":where_". $this->keys, $this->values);
              foreach ($this->h_values as $k => $v)
                if ($this->h_keys[$k] != ":". $this->having)
                  $sql->bindValue($this->h_keys[$k], $v);
            break;
            case "UPDATE":
              if (empty($this->from))
                die("The given table name is empty.");
              $query_string = "UPDATE ". $this->from;
              if (count($this->join) > 0)
                $query_string .= " ". implode(" ", $this->join);
              $query_string .= " SET ". implode(", ", $this->columns);
              if (count($this->where) > 0)
                $query_string .= " WHERE ". implode($this->where);
              $sql = $this->prepare($query_string);
              foreach ($this->h_values as $k => $v)
                $sql->bindValue(":set_". $this->h_keys[$k], $v);
              if (count($this->where) > 0)
              {
                foreach ($this->values as $k => $v)
                  $sql->bindValue(":". $this->keys[$k], $v);
              }
            break;
            case "DELETE":
              $query_string = "DELETE ";
              if (count($this->columns) > 0)
                $query_string .= implode(", ", $this->columns);
              if (empty($this->from))
                die("The given table name is empty.");
              $query_string .= " FROM ". $this->from;
              if (count($this->join) > 0)
                $query_string .= " ". implode(" ", $this->join);
              if (count($this->where) > 0)
                $query_string .= " WHERE ". implode($this->where);
              $sql = $this->prepare($query_string);
              if (count($this->where) > 0)
              {
                foreach ($this->values as $k => $v)
                  $sql->bindValue(":". $this->keys[$k], $v);
              }
            break;
          }
        }
        $sql->execute();
        $this->xReset();
        return $sql;
    }

// Reseter method for private properties
    private function xReset()
    {
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

// Built in record check
    public function xIsInDb($search, $table, $column)
    {
        if (empty($table))
          die("The table name is empty");
        if (empty($table))
          die("No column specified");
        $sql = "SELECT ". $column ." FROM ". $table ." WHERE ". $column ." = ?";
        $search_query = $this->prepare($sql);
        $search_query->execute(array($search));
        if ($search_query->rowCount() > 0)
          return true;
        else
          return false;
    }
}