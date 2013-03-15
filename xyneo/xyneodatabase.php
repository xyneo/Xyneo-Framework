<?php if ( ! defined('XYNEO') ) die("Direct access denied!");

class XyneoDataBase extends PDO

{
    
// PDO database connection
    
    function __construct()
    
    {
        try
        {
        parent::__construct(DB_DRIVER.':host='.DB_HOST.';dbname='.DB_USE, DB_USER, DB_PASSWORD,
                array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
        }
        catch(PDOException $ex){
            die("Database connection failed. Check your config file!");
        }

        $this -> query("SET NAMES 'utf8'");
        
    }

// Built in select method
    
    public function xSelect($table,$r_fields,$array = false,$orders = false)
                
    {
        
        if(empty($table))
            
           die('The table name is empty');
        
        
        if(empty($r_fields))
            
            die('No fields to select');
        
        $fields = array_values($r_fields);
        
        $sql = "SELECT ";
        
        foreach ($fields as $key => $field)
            
        {
                        
            $sql .= $field.", ";
            
        }
        
         $sql = trim($sql,', ').' FROM '.$table.'  ';
        
        if ($array)
            
        {
            
            $sql  .= " WHERE ";
            
            $conds = array_keys($array);

            foreach ($conds as $key => $cond)

            {

                $sql .= $cond." = ? AND ";

            }
            
            $sql = rtrim($sql,'AND ');
            
        }
        
        if ($orders)
            
        {
            
            $sql  .= " ORDER BY ";

            foreach ($orders as $key => $order)

            {

                $sql .= $key." ".$order.", ";

            }
            
            $sql = rtrim($sql,', ');
            
        }
        
        
        
        $select_query = $this -> prepare($sql);
        
        if($array)
            
            $select_query -> execute(array_values($array));
        
        else
            
            $select_query -> execute();
        
        return $select_query;
        
    }

 // Built in insert method
    
    public function xInsert($table,$array)
                
    {
        
        if(empty($table))
            
           die('The table name is empty');
        
        if(empty($array))
            
            die('No fields to insert');
        
        $fields = implode(',',array_keys($array));
        
        $values = "";
        
        foreach($array as $key => $value)
        
        {
            
            $values.= ",?";
            
        }
        
        $values = trim($values, ',');
        
        $sql = "INSERT INTO ".$table." (".$fields.") VALUES (".$values.")";      
        
        $insert_query = $this -> prepare($sql);
        
        $insert_query -> execute(array_values($array));
        
    }
    
// Built in update method
    
    public function xUpdate($table,$array,$conds=false){
        
        if(empty($table))
            
           die('The table name is empty');
        
        if(empty($array))
            
            die('No fields to update');
        
        
        $fields = array_keys($array);
        
        $sql = "UPDATE ".$table." SET ";
        
        foreach ($fields as $key => $field)
            
        {
            
            if ($key > 0)
                
                $sql .= ",";
            
            $sql .= $field."=?";
            
        }
        
        if ($conds)
        
        {
            $sql .= ' WHERE ';
            
            $cond = array_keys($conds);

            foreach ($cond as $key => $c)

            {

                $sql .= $c." = ? AND ";

            }
        
        $sql = trim($sql,'AND ');
        
        $update = array_merge(array_values($array),array_values($conds));
        
        }
        
        else
        
        {
            
            $update = array_values($array);
            
        }
        $update_query = $this -> prepare($sql);
        
        $update_query -> execute($update);
        
    }
    
// Built in delete method
    
    public function xDelete($table,$conds=false){
        
        if (empty($table))

            die('The table name is empty');
        
        $sql = "DELETE FROM ".$table." ";
        
        if($conds)
            
        {
            
            $sql.= "WHERE ";
        
            $cond = array_keys($conds);
            
            foreach ($cond as $key => $c)

            {

                $sql .= $c." = ? AND ";

            }
        
        $sql = rtrim($sql,'AND ');
        
        }       
        
        $delete_query = $this ->prepare($sql);
        
        if ($conds)
            
            $delete_query ->execute(array_values($conds));
        
        else 
            
            $delete_query ->execute();
        
    }
    
// Built in record check
    
    public function xIsInDb($search,$table,$column){
        
        if (empty($table))
            
            die('The table name is empty');
        
        if (empty($table))
            
            die('No column specified');

        $sql = "SELECT ".$column." FROM ".$table." WHERE ".$column."=?";

        $search_query = $this -> prepare($sql);
        
        $search_query -> execute(array($search));
        
        if($search_query -> rowCount() > 0)
            
            return true;
        
        else
            
            return false;
    }
    
}