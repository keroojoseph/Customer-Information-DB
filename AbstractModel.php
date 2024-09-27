<?php

class AbstractModel
{
    const DATA_TYPE_STR = PDO::PARAM_STR;
    const DATA_TYPE_INT = PDO::PARAM_INT;
    const DATA_TYPE_DECIMAL = 4;
    const DATA_TYPE_BOOL = PDO::PARAM_BOOL;

    public static function viewTableSchema()
    {
        return static:: $tableSchema;
    }

    public function create()
    {
        global $connection;
        $sql = 'INSERT INTO ' . static::$tableName . ' SET ' . self::buildNameParameterSQL();
        $stmt = $connection->prepare($sql);
        $this->prepareValues($stmt);
        return $stmt->execute();
    }

    private static function buildNameParameterSQL()
    {
        $params = '';
        foreach (static::$tableSchema as $column => $type) {
            $params .= $column . ' = ' . ':' . $column . ", ";
        }
        $params = trim($params, ', ');
        return $params;
    }

    private function prepareValues(PDOStatement &$stmt)
    {
        foreach (static::$tableSchema as $column => $type) {
            $value = $this->__get($column);

            if ($type == self::DATA_TYPE_DECIMAL) {
                $sanitizeValue = filter_var($value, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
                $stmt->bindValue(':' . $column, $sanitizeValue);
            } else {
                $stmt->bindValue(':' . $column, $value, $type);
            }
        }
    }

    public function update()
    {
        global $connection;
        $sql = 'UPDATE ' . static::$tableName . ' SET ' . self::buildNameParameterSQL() . ' WHERE ' . static::$primaryKey . " = " . $this->{static::$primaryKey};
        $stmt = $connection->prepare($sql);
        $this->prepareValues($stmt);
        return $stmt->execute();
    }

    public function delete()
    {
        global $connection;
        $sql = 'DELETE FROM ' . static::$tableName . ' WHERE ' . static::$primaryKey . " = " . $this->{static::$primaryKey};
        $stmt = $connection->prepare($sql);
        return $stmt->execute();
    }

    public static function getAll()
    {
        global $connection;
        $sql = 'SELECT * FROM ' . static::$tableName;
        $stmt = $connection->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, get_called_class(), array_keys(static::$tableSchema));
        return  (is_array($result) && !empty($result)) ? $result : false;
    }

    public static function getByPk ($pk) {
        global $connection;
        $sql = 'SELECT * FROM ' . static::$tableName . ' WHERE ' . static::$primaryKey . " = " . $pk;
        $stmt = $connection->prepare($sql);
        if ($stmt->execute() === true ) {
            $obj = $stmt->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, get_called_class(), array_keys(static::$tableSchema));
            return array_shift($obj);
        }
        return false;
    }

    public function save () {
        return $this->{static::$primaryKey} === null ? $this->create() : $this->update();
    }

    public static function get ($sql , $option = array()) {
        global $connection;
        $stmt = $connection->prepare($sql);

        if (!empty($option)) {
            foreach ($option as $column => $type) {
                if ($type[0] === 4) {
                    $sanitizeValue = filter_var($type[1], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
                    $stmt->bindValue(':' . $column, $sanitizeValue);
                } else {
                    $stmt->bindValue(':' . $column, $type[1], $type[0]);
                }
            }
        }

        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, get_called_class(), array_keys(static::$tableSchema));
        return  (is_array($result) && !empty($result)) ? $result : false;
    }
}