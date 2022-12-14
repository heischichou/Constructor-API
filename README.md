# Constructor API
Constructor API is a port over of a custom PHP-based API I made from scratch during [my school project](https://github.com/heischichou/NJC-Tattoo/). This API comprises primarily a MySQL Query Builder from which this repository derives its name. It also contains helper functions for general utility.

## Setup
To use the API, simply paste the following lines of code in your PHP document. You may change the constructor arguments as necessary.
```php
require_once 'api.php';
$api = new api($user, $password, $db);
```
Example:
```php
require_once 'api.php';
$api = new api("root", "", "sample_db");
```

To set the API's database connection, paste the following lines of code in your PHP document.
```php
$api->db_connect($user, $password, $db);
```
Example:
```php
$api->db_connect("root", "", "sample_db");
```

## API Documentation

<details><summary>General Use Functions</summary>
<p>

## $api->sanitize_data($data, $type)
Santizes the given data.
Example of integer sanitization, do
```php
$data = "1Lorem2ipsum3dolor4sit5amet";
$api->sanitize_data($data, "int");
// $data = 12345;
```

Example of float sanitization, do
```php
$data = "1Lorem2ipsum3dolor4sit5amet";
$api->sanitize_data($data, "float");
// $data = 12345.67;
```

Example of email sanitization, do
```php
$data = "john.doe@test.com";
$api->sanitize_data($data, "email");
// $data = "john.doe@test.com";
```

Example of string (default case) sanitization, do
```php
$data = " <script>console.log('This is an attack.')</script> "
$api->sanitize_data($data, "string");
// $data = "console.log('This is an attack');";
```


## $api->validate_data($data, $type)
Validate the given data. Returns true if the data is valid, false otherwise.
Integer validation valid case
```php
$boolean = $api->validate_data(12345, "int");
// $boolean = true;
```

Integer validation invalid case
```php
$boolean = $api->validate_data("1Lorem2ipsum3dolor4sit5amet", "int");
// $boolean = false;
```

Float validation valid case
```php
$boolean = $api->validate_data(12345.67, "float");
// $boolean = true;
```

Float validation invalid case
```php
$boolean = $api->validate_data("1Lorem2ipsum3dolor4sit5amet.67", "float");
// $boolean = false;
```

Email validation valid case
```php
$boolean = $api->sanitize_data("john.doe@test.com", "email");
// $boolean = true;
```

Email validation invalid case
```php
$boolean = $api->sanitize_data("invalid-email#test+com", "email");
// $boolean = 'false';
```

Date validation valid case
```php
$boolean = $api->validate_data("10-17-2000", "date");
// $boolean = true;
```

Date validation invalid case - Invalid date
```php
$boolean = $api->validate_data("02-31-2000", "date");
// $boolean = false;
```

Date validation invalid case - Past date
```php
// today = '05-05-2001'
$boolean = $api->validate_data("05-03-2000", "date");
// $boolean = false;
```

Time validation valid case
```php
$boolean = $api->validate_data("05:30:00", "time");
// $boolean = true;
```

Time validation invalid case
```php
$boolean = $api->validate_data("13:72:00", "time");
// $boolean = false;
```

Birthdate validation valid case
```php
$boolean = $api->validate_data("12-1-2000", "birthdate");
// $boolean = true;
```

Birthdate validation invalid case - Invalid format
```php
$boolean = $api->validate_data("13-32-1900", "birthdate");
// $boolean = false;
```

Birthdate validation invalid case - Less than legal age
```php
$boolean = $api->validate_data("04-17-2020", "birthdate");
// $boolean = false;
```

Birthdate validation invalid case - Exceeds age limit
```php
$boolean = $api->validate_data("08-23-1810", "birthdate");
// $boolean = false;
```

String (default case) valid case
```php
$boolean = $api->sanitize_data("Hello world!", "string");
// $boolean = 'true';
```


## $api->validate_image($image, $file_size)
Validate the given file. Returns true if the file is a valid image, false otherwise.
Example of image validation, do
```php
$boolean = $api->validate_image($image, $file_size);
// $boolean = true;
```

Image validation invalid case - Invalid file extension
```php
// $file_ext = "image/webp";
$boolean = $api->validate_image($image, 5000);
// $boolean = false;
```

Image validation invalid case - Invalid file type
```php
// $file_type = "webp";
$boolean = $api->validate_image($image, 5000);
// $boolean = false;
```

Image validation invalid case - Exceeds file size limit
```php
// $file_size = 7000;
$boolean = $api->validate_image($image, 5000);
// $boolean = false;
```


## $api->generate-color()
Randomly generate a hexadecimal color code.
```php
$data = $api->generate_color();
// $data = #FDC651;
```

</p>
</details>

<details><summary>MySQLi Functions</summary>
<p>

<details><summary>Database Functions</summary>
<p>

## $api->db_return()
Returns the API's database connection.
```php
$api->db_connect("root", "", "sample_db");
$conn = $api->db_return();
```


## $api->db_disconnect()
Close the API's database connection.
```php
$api->db_disconnect();
$conn = $api->db_return();
// $conn = null;
```


## $api->db_close()
Close the given database connection. Returns true on success, false on failure.
```php
$conn = $api->db_return();
...
$api->db_return($conn);
```

</p>
</details>

<details><summary>Clause Helpers</summary>
<p>

## $api->table($string, $params)
Appends the specified tables to the given query string.
To specify a single table, do
```php
$api->table($query, $table);

Example:
$query = $api->select();
$api->params($query, '*');
$api->from($query);
$api->table($query, 'table');
// $query = 'SELECT * FROM table';
```

To specify multiple tables, do
```php
$api->table($query, array($arg1, $arg2, ..., $argN));

Example:
$query = $api->select();
$api->params($query, '*');
$api->from($query);
$api->table($query, array('table1', 'table2'));
// $query = 'SELECT * FROM table1, table2';
```


## $api->join($type, $left, $right, $left_kv, $right_kv)
Returns a join clause string with the specified join type.

To construct a default JOIN (INNER), do
```php
$join_clause = $api->join('', 'tableLeft', 'tableRight', 'tableLeft.column', 'tableRight.column');
// $join_clause = '(tableLeft JOIN tableRight ON tableLeft.column=tableRight.column)';
```

To construct a LEFT JOIN, do
```php
$join_clause = $api->join('left', 'tableLeft', 'tableRight', 'tableLeft.column', 'tableRight.column');
// $join_clause = '(tableLeft LEFT JOIN tableRight ON tableLeft.column=tableRight.column)';
```

To construct a RIGHT JOIN, do
```php
$join_clause = $api->join('right', 'tableLeft', 'tableRight', 'tableLeft.column', 'tableRight.column');
// $join_clause = '(tableLeft RIGHT JOIN tableRight ON tableLeft.column=tableRight.column)';
```

To construct a nested JOIN, do
```php
$nested_join= $api->join('', 'table1', 'table2', 'table1.column', 'table2.column');

$join_clause = $api->join('', $nested_join, 'table3', 'table2.column', 'table3.column');
// $join_clause = '((table1 JOIN table2 ON table1.column=table2.column) JOIN table3 ON table2.column=table3.column)';
```


## $api->where($string, $cols, $params)
Appends the specified SQL WHERE clause to the given query string.
To specify a single condition, do
```php
$api->where($query, $column, $param);

Example:
$query = $api->select();
$api->params($query, '*');
$api->from($query);
$api->table($query, 'table');
$api->where($query, 'column', 1);
// $query = 'SELECT * FROM table WHERE column=1';

Another example:
$query = $api->select();
$api->params($query, '*');
$api->from($query);
$api->table($query, array('table1', 'table2'));
$api->where($query, 'table1.column', 'table2.column');
// $query = 'SELECT * FROM table1, table2 WHERE table1.column=table2.column';
```


To specify multiple conditions, do
```php
$api->where($query, array($arg1, $arg2, ..., $argN), array($arg1, $arg2, ..., $argN));

Example:
$query = $api->select();
$api->params($query, '*');
$api->from($query);
$api->table($query, 'table');
$api->where($query, array('column1', 'column2'), array('value1', 'value2'));
// $query = 'SELECT * FROM table WHERE column1=value1 AND column2=value2;
```


## $api->limit($string, $limit)
Appends the specified SQL LIMIT clause to the given query string.
```php
$api->limit($query, $int);

Example:
$query = $api->select();
$api->params($query, '*');
$api->from($query);
$api->table($query, 'table');
$api->limit($query, 2);
// $query = 'SELECT * FROM table LIMIT 2';
```

## $api->order($string, $params)
Appends the specified SQL ORDER clause to the given query string.
To specify ordering by a single column, do
```php
$api->order($query, $column, $type);

Example:
$query = $api->select();
$api->params($query, '*');
$api->from($query);
$api->table($query, 'table');
$api->order($query, 'column', 'ASC');
// $query = 'SELECT * FROM table ORDER BY column ASC';
```
To specify ordering by multiple columns, do
```php
$api->order($query, array($arg1, $arg2, ..., $argN), array($arg1, $arg2, ..., $argN));

Example:
$query = $api->select();
$api->params($query, '*');
$api->from($query);
$api->table($query, 'table');
$api->order($query, array('column1', 'column2'), array('ASC', 'DESC'));
// $query = 'SELECT * FROM table ORDER BY column1 ASC, column2 DESC';
```

</p>
</details>

<details><summary>Select Functions</summary>
<p>

## $api->select()
Returns SQL SELECT to the calling string.
```php
$query = $api->select();
// $query = 'SELECT ';
```


## $api->params($string, $params)
Appends the defined parameters to given query string.
To specify a single parameter, do
```php
$api->params($query, '*');
// $query = 'SELECT * ';
```

To specify multiple parameters, do
```php
$api->params($query, array($arg1, $arg2, ..., $argN));

Example:
$query = $api->select();
$api->params($query, array('column1', 'column2', 'column3'));
// $query = 'SELECT column1, column2, column3 ';
```


## $api->from($string)
Appends SQL FROM to the given query string.
```php
$query = $api->select();
$api->params($query, '*');
$api->from($query);
// $query = 'SELECT * FROM ';
```

To construct a select query, do
```php
$query = $api->select();
$api->params($query, '*');
$api->from($query);
$api->table($query, 'table');
// $query = 'SELECT * FROM table';
```

</p>
</details>

<details><summary>Insert Functions</summary>
<p>

## $api->insert()
Returns SQL INSERT to the calling string.
```php
$query = $api->insert();
// $query = 'INSERT INTO ';
```


## $api->columns($string, $params = array())
Appends the specified SQL table columns to the given query string
```php
$api->columns($query, array($arg1, $arg2, ..., $argN));

Example:
$query = $api->insert();
$api->table($query, 'table');
$api->columns($query, array('column1', 'column2', 'column3'));
// $query = 'INSERT INTO table (column1, column2, column3) ';
```


## $api->values($string)
Appends SQL VALUES to the given query string.
```php
$query = $api->insert();
$api->table($query, 'table');
$api->columns($query, array('column1', 'column2'));
$api->values($query);
// $query = 'INSERT INTO table (column1, column2) VALUES ';
```

To construct an insert query, do
```php
$query = $api->insert();
$api->table($query, 'table');
$api->columns($query, array('column1', 'column2', 'column3'));
$api->values($query);
$api->columns($query, array('value1', 'value2', 'value3'));
// $query = 'INSERT INTO table (column1, column2, column3) VALUES (value1, value2, value3)';
```

</p>
</details>

<details><summary>Update Functions</summary>
<p>

## $api->update()
Returns SQL UPDATE to the calling string.
```php
$query = $api->update();
// $query = 'UPDATE ';
```


## $api->set($string, $cols, $params)
Appends the specified column-value pairs to the given query string.
To specify a single column-value pair, do
```php
$api->set($query, $column, $value);
```

To specify multiple column-value pairs, do
```php
$api->set($query, array($col1, $col2, ..., $colN), array($value1, $value2, ..., $valueN));

Example:
$query = $api->update();
$api->table($query, 'table');
$api->set($query, array('column1', 'column2', 'column3'), array('value1', 'value2', 'value3'));
// $query = 'UPDATE table SET column1=value1, column2=value2, column3=value3 ';
```

To construct an update query, do
```php
$query = $api->update();
$api->table($query, 'table');
$api->set($query, array('column1', 'column2', 'column3'), array('value1', 'value2', 'value3'));
$api->where($query, 'column', 'value');
// $query = 'UPDATE table SET column1=value1, column2=value2, column3=value3 WHERE column=value';
```

</p>
</details>

<details><summary>Delete Function</summary>
<p>

## $api->delete()
Returns SQL DELETE to the calling string.
```php
$query = $api->delete();
// $query = 'DELETE ';
```

To construct a delete query, do
```php
$query = $api->delete();
$api->from($query);
$api->table($query, 'table');
$api->where($query, 'column', 'value');
// $query = 'DELETE FROM table WHERE column=value';
```

</p>
</details>

<details><summary>Prepared Statement Functions</summary>
<p>

## $api->prepare($query)
Prepares the given SQL query string for execution. Returns a statement object on success, false on failure.
```php
$query = $api->select();
$api->params($query, '*');
$api->from($query);
$api->table($query, 'table');

$statement = $api->prepare($query);
```


## $api->bind_params(&$statement, $types, $params)
Binds variables to the given prepared statement. Returns true on success, false on failure.
To bind a single variable, do
```php
$query = $api->select();
$api->params($query, '*');
$api->from($query);
$api->table($query, 'table');
$api->where($query, 'column', '?');

$statement = $api->prepare($query);
$boolean = $api->bind_params($statement, "i", 1);
```

To bind multiple variables, do
```php
$query = $api->select();
$api->params($query, '*');
$api->from($query);
$api->table($query, 'table');
$api->where($query, array('column1', 'column2', 'column3'), array('?', '?', '?'));

$statement = $api->prepare($query);
$boolean = $api->bind_params($statement, "sis", array('param1', 2, 'param3'));
```


## $api->bind_result(&$statement, $types, $params)
Binds variables to the given prepared statement. Returns an array of all the bound variables on success, false on failure.
```php
$query = $api->select();
$api->params($query, array('column1', 'column2', 'column3'));
$api->from($query);
$api->table($query, 'table');
$api->where($query, 'column', '?');

$statement = $api->prepare($query);
$api->bind_params($statement, "s", $value);
$api->execute($statement);
$api->store_result($statement);
$boolean = $api->bind_result($statement, array($key1, $key2, $key3));
```


## $api->execute(&$statement)
Executes the given prepared statement. Returns true on success, false on failure.
```php
$query = $api->select();
...
$statement = $api->prepare($query);
$boolean = $api->execute($statement);
```


## $api->store_result(&$statement)
Stores the result set of a successfully executed statement in an internal buffer. Returns true on success, false on failure.
```php
$query = $api->select();
...
$api->execute($statement);
$boolean = $api->store_result($statement);
```


## $api->get_result(&$statement)
Gets the result set of a prepared statement. If the prepared statement was successfully executed, $api->get_result() returns its result set, else, it returns false.
```php
$query = $api->select();
...
$api->execute($statement);
$res = $api->get_result($statement);
```


## $api->num_rows($res)
Returns the number of rows in a given result set. If no rows are found, $api->num_rows() returns 0.
```php
$query = $api->select();
...
$res = $api->get_result($statement);
$count = $api->num_rows($res);
```


## $api->fetch_assoc(&$result)
Fetches a single row from a given result set. $api->fetch_assoc() returns an associative array representing the fetched row, null if there are no more rows in the result set, or false on failure.
```php
$query = $api->select();
...
$res = $api->get_result($statement);
$row = $api->fetch_assoc($res);
```


## $api->free_result(&$statement)
Frees the memory associated with a result set.
```php
$query = $api->select();
...
$statement = $api->prepare($query);
...
$api->free_result($statement);
```


## $api->close(&$statement)
Closes the given prepared statement. Returns true on success, false on failure.
```php
$query = $api->select();
...
$statement = $api->prepare($query);
...
$api->free($statement);
$boolean = $api->close($statement);
```

</p>
</details>

</p>
</details>

</p>
</details>