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

To connect to a database, paste the following lines of code in your PHP document.
```php
$api->db_connect($user, $password, $db);
```
Example:
```php
$api = new api("root", "", "sample_db");
```

To close the connection to the database, paste the following lines of code in your PHP document.
```php
$api->db_close();
```

## API Documentation

<details><summary>General Use Functions</summary>
<p>

## $api->sanitize_data($data, $type)
Santizes the given data.
Example of integer sanitization, do
```php
$data = "1Lorem2ipsum3dolor4sit5amet";
$data = $api->sanitize_data($data, "int");
// $data = 12345;
```

Example of float sanitization, do
```php
$data = "1Lorem2ipsum3dolor4sit5amet";
$data = $api->sanitize_data($data, "float");
// $data = 12345.67;
```

Example of email sanitization, do
```php
$data = "john.doe@test.com";
$data = $api->sanitize_data($data, "email");
// $data = "john.doe@test.com";
```

Example of string (default case) sanitization, do
```php
$data = " <script>console.log('This is an attack.')</script> "
$data = $api->sanitize_data($data, "string");
// $data = "console.log('This is an attack');";
```


## $api->validate_data($data, $type)
Validate the given data. Returns true if the data is valid, false otherwise.
Integer validation valid case
```php
$data = $api->validate_data(12345, "int");
// $data = true;
```

Integer validation invalid case
```php
$data = $api->validate_data("1Lorem2ipsum3dolor4sit5amet", "int");
// $data = false;
```

Float validation valid case
```php
$data = $api->validate_data(12345.67, "float");
// $data = true;
```

Float validation invalid case
```php
$data = $api->validate_data("1Lorem2ipsum3dolor4sit5amet.67", "float");
// $data = false;
```

Email validation valid case
```php
$data = $api->sanitize_data("john.doe@test.com", "email");
// $data = true;
```

Email validation invalid case
```php
$data = $api->sanitize_data("invalid-email#test+com", "email");
// $data = 'false';
```

Date validation valid case
```php
$data = $api->validate_data("10-17-2000", "date");
// $data = true;
```

Date validation invalid case - Invalid date
```php
$data = $api->validate_data("02-31-2000", "date");
// $data = false;
```

Date validation invalid case - Past date
```php
// today = '05-05-2001'
$data = $api->validate_data("05-03-2000", "date");
// $data = false;
```

Time validation valid case
```php
$data = $api->validate_data("05:30:00", "time");
// $data = true;
```

Time validation invalid case
```php
$data = $api->validate_data("13:72:00", "time");
// $data = false;
```

Birthdate validation valid case
```php
$data = $api->validate_data("12-1-2000", "birthdate");
// $data = true;
```

Birthdate validation invalid case - Invalid format
```php
$data = $api->validate_data("13-32-1900", "birthdate");
// $data = false;
```

Birthdate validation invalid case - Less than legal age
```php
$data = $api->validate_data("04-17-2020", "birthdate");
// $data = false;
```

Birthdate validation invalid case - Exceeds age limit
```php
$data = $api->validate_data("08-23-1810", "birthdate");
// $data = false;
```

String (default case) valid case
```php
$data = $api->sanitize_data("Hello world!", "string");
// $data = 'true';
```


## $api->validate_image($data, $type)
Validate the given file. Returns true if the file is a valid image, false otherwise.
Example of image validation, do
```php
$data = $api->validate_image($image, $file_size);
// $data = true;
```

Image validation invalid case - Invalid file extension
```php
// $file_ext = "image/webp";
$data = $api->validate_image($image, 5000);
// $data = false;
```

Image validation invalid case - Invalid file type
```php
// $file_type = "webp";
$data = $api->validate_image($image, 5000);
// $data = false;
```

Image validation invalid case - Exceeds file size limit
```php
// $file_size = 7000;
$data = $api->validate_image($image, 5000);
// $data = false;
```


## $api->sanitize_data($data, $type)
Randomly generate a hexadecimal color code.
```php
$data = $api->generate_color();
// $data = #FDC651;
```

</p>
</details>

<details><summary>MySQLi Functions</summary>
<p>

<details><summary>Clause Helpers</summary>
<p>

## $api->table($string, $params)
Returns the given query string with the specified tables.
To specify a single table, do
```php
$query = $api->table($query, $table);

Example:
$query = $api->select();
$api->params($query, '*');
$api->from($query);
$api->table($query, 'table');
// $query = 'SELECT * FROM table';
```

To specify multiple tables, do
```php
$query = $api->table($query, array($arg1, $arg2, ..., $argN));

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
Returns the given query string with the specified SQL WHERE clause.
To specify a single condition, do
```php
$query = $api->where($query, $column, $param);

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
$query = $api->order($query, array($arg1, $arg2, ..., $argN), array($arg1, $arg2, ..., $argN));

Example:
$query = $api->select();
$api->params($query, '*');
$api->from($query);
$api->table($query, 'table');
$api->order($query, array('column1', 'column2'), array('value1', 'value2'));
// $query = 'SELECT * FROM table WHERE column1=value1 AND column2=value2;
```


## $api->limit($string, $limit)
Returns the given query string with the specified limit.
```php
$query = $api->limit($query, $int);

Example:
$query = $api->select();
$api->params($query, '*');
$api->from($query);
$api->table($query, 'table');
$api->limit($query, 2);
// $query = 'SELECT * FROM table LIMIT 2';
```

## $api->order($string, $params)
Returns the given query string with the specified order.
To specify ordering by a single column, do
```php
$query = $api->order($query, $column, $type);

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
$query = $api->order($query, array($arg1, $arg2, ..., $argN), array($arg1, $arg2, ..., $argN));

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
Returns the given query string with its defined parameters.
To specify a single parameter, do
```php
$query = $api->params($query, '*');
// $query = 'SELECT * ';
```

To specify multiple parameters, do
```php
$query = $api->params($query, array($arg1, $arg2, ..., $argN));

Example:
$query = $api->select();
$api->params($query, array('column1', 'column2', 'column3'));
// $query = 'SELECT column1, column2, column3 ';
```


## $api->from($string)
Returns the given query string with SQL FROM.
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
Returns the given query string with the specified columns to insert values at.
```php
$query = $api->columns($query, array($arg1, $arg2, ..., $argN));

Example:
$query = $api->insert();
$api->table($query, 'table');
$api->columns($query, array('column1', 'column2', 'column3'));
// $query = 'INSERT INTO table (column1, column2, column3) ';
```


## $api->values($string)
Returns the given query string with SQL VALUES.
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
Returns the given query string with the specified column-value pairs.
To specify a single column-value pair, do
```php
$query = $api->set($query, $column, $value);
```

To specify multiple column-value pairs, do
```php
$query = $api->set($query, array($col1, $col2, ..., $colN), array($value1, $value2, ..., $valueN));

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
Frees the memory associated with a result.
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