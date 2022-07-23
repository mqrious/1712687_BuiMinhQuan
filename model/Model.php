<?php

abstract class Model
{
    public string $tableName = '';

    abstract public function attributes(): array;

    // Private attributes
    private array $conditions = [];
    private array $orderOptions = [];
    private string $limitOffset = '';

    // Private functions
    private function map($row)
    {
        $item = new $this();
        foreach ($this->attributes() as $attribute => $columnName) {
            if (is_string($item->{$attribute})) {
                $item->{$attribute} = (string)$row[$columnName];
            } elseif (is_numeric($item->{$attribute})) {
                $item->{$attribute} = (int)$row[$columnName];
            } else {
                $item->{$attribute} = $row[$columnName];
            }
        }
        return $item;
    }

    private function rowCount(): int
    {
        $mysqli = connect();
        $sql = $this->prepareQueryStatement(true);
        $count = 0;
        $result = $mysqli->query($sql);
        if ($result) {
            $count = $result->fetch_row()[0];
        }
        return (int)$count;
    }

    private function prepareQueryStatement($count = false): string
    {
        $query = "SELECT * FROM $this->tableName";
        $whereStatement = '';
        $orderStatement = '';
        $limitOffsetStatement = '';
        if (!empty($this->conditions)) {
            $whereStatement = "WHERE " . implode(' AND ', $this->conditions);
        }
        if ($count) {
            $query = "SELECT COUNT(*) FROM $this->tableName";
        } else {
            $limitOffsetStatement = $this->limitOffset;
            if (!empty($this->orderOptions)) {
                $orderStatement = implode(' ', $this->orderOptions);
            }
        }
        return trim(implode(' ', [$query, $whereStatement, $orderStatement, $limitOffsetStatement]));
    }

    // Public functions
    public function toObject(): array
    {
        return array_map(function ($attr) {
            return [$attr => $this->{$attr}];
        }, array_keys($this->attributes()));
    }

    public function query(string $sql): array
    {
        $mysqli = connect();
        $data = array();
        if ($result = $mysqli->query($sql)) {
            $fields = $result->fetch_fields();
            foreach ($result as $row) {
                $item = new stdClass();
                foreach ($fields as $field) {
                    $item->{$field->name} = $row[$field->name];
                }
                $data[] = $item;
            }
        }
        $mysqli->close();
        return $data;
    }

    public function all(): array
    {
        $mysqli = connect();
        $query = "SELECT * FROM $this->tableName";
        $result = $mysqli->query($query);
        $data = array();
        if ($result) {
            foreach ($result as $row) {
                $item = $this->map($row);
                $data[] = $item;
            }
        }
        $mysqli->close();
        return $data;
    }

    public function get(): array
    {
        $mysqli = connect();
        $sql = $this->prepareQueryStatement();
        $data = array();
        $result = $mysqli->query($sql);
        if ($result) {
            foreach ($result as $row) {
                $dataItem = $this->map($row);
                $data[] = $dataItem;
            }
        }
        $mysqli->close();
        return $data;
    }

    public function first(): ?Model
    {
        $this->limitOffset = sprintf('LIMIT %s', 1);
        $data = $this->get();
        return $data[0] ?? null;
    }

    public function paginate(int $limit): array
    {
        $rowCount = $this->rowCount();
        $pageCount = (int)ceil($rowCount / $limit);
        $currentPage = 1;
        if (strtolower($_SERVER['REQUEST_METHOD']) === 'get') {
            $currentPage = $_GET['page'] ?? 1;
        } else if (strtolower($_SERVER['REQUEST_METHOD']) === 'post') {
            $currentPage = $_POST['page'] ?? 1;
        }
        $offset = $limit * ($currentPage - 1);
        $this->limitOffset = sprintf('LIMIT %s OFFSET %s', $limit, $offset);
        $data = $this->get();
        return [
            'currentPage' => $currentPage,
            'pageCount' => $pageCount,
            'dataCount' => $rowCount,
            'data' => $data,
        ];
    }

    public function create()
    {
        $mysqli = connect();
        mysqli_report(MYSQLI_REPORT_ALL);
        try {
            $attributes = array_keys($this->attributes());
            $columnNames = array_values($this->attributes());
            $values = array_map(function ($attr) {
                $value = $this->{$attr};
                return $value == null
                    ? 'NULL'
                    : (is_numeric($value) ? $value : "'$value'");
            }, $attributes);
            $values[0] = 'default';
            $sql = sprintf("
                INSERT INTO $this->tableName (%s)
                VALUES (%s)
            ",
                implode(', ', $columnNames),
                implode(', ', $values)
            );
            $result = $mysqli->query($sql);
            if ($result) {
                $mysqli->close();
                http_response_code(201);
                return $this->toObject();
            }
        } catch (Exception $e) {
            http_response_code(403);
            echo $e->getMessage();
        }
        $mysqli->close();
        return null;
    }

    public function update($params = [])
    {
        $mysqli = connect();
        mysqli_report(MYSQLI_REPORT_ALL);
        try {
            $updateColumns = array_map(function ($attr, $value) {
                $field = isset($this->attributes()[$attr]) ? $this->attributes()[$attr] : $attr;
                if (is_numeric($value)) {
                    return "$field = $value";
                }
                return "$field = '$value'";
            }, array_keys($params), array_values($params));
            $idColumn = $this->attributes()[array_key_first($this->attributes())];
            $idValue = $this->{array_key_first($this->attributes())};
            $sql = sprintf("
                UPDATE $this->tableName
                SET %s
                WHERE %s = %s
            ",
                implode(', ', $updateColumns),
                $idColumn,
                $idValue
            );
            $result = $mysqli->query($sql);
            if ($result) {
                $mysqli->close();
                http_response_code(200);
                return $this->toObject();
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo $e->getMessage();
        }
        $mysqli->close();
        return null;
    }

    // Query functions
    public function where(string $field, string $operator, $value): Model
    {
        $condition = sprintf('%s %s %s',
            $this->attributes()[$field],
            $operator,
            is_string($value) ? ($operator === 'LIKE' ? "'%$value%'" : "'$value'") : $value);
        $this->conditions[] = $condition;
        return $this;
    }

    public function orderBy(string $field, string $option = 'ASC'): Model
    {
        if (in_array($field, array_keys($this->attributes()))) {
            $orderOption = sprintf('ORDER BY %s %s', $this->attributes()[$field], $option);
            $this->orderOptions[] = $orderOption;
        }
        return $this;
    }

    /**
     *  Relationships
     */

    /**
     * @param string $refModelName
     * @param string $localKey
     * @param string $foreignKey
     * @return Model
     * @var Model $refModel
     */
    public function hasOne(string $refModelName, string $localKey, string $foreignKey): ?Model
    {
        $refModel = new $refModelName();
        $refTableName = $refModel->tableName;
        $localColumn = $this->attributes()[$localKey];
        $foreignColumn = $refModel->attributes()[$foreignKey];

        if ($this->$localKey == null) {
            return null;
        }
        $mysqli = connect();
        $query = "
            SELECT table_1.* FROM 
            $this->tableName table_0
            JOIN $refTableName table_1 ON table_0.$localColumn = table_1.$foreignColumn
            WHERE table_1.{$foreignColumn} = '{$this->{$localKey}}'
            LIMIT 1
            ";
        $result = $mysqli->query($query);
        if ($result) {
            foreach ($result as $row) {
                $data = $refModel->map($row);
                break;
            }
        }
        $mysqli->close();
        return $data ?? null;
    }
}