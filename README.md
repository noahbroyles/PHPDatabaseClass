# PHPDatabaseClass
A class to make using MySQL Databases easier in PHP. This class has built in SQL injection protection. 

## Use:
```php
// Create a new Database Object
$db = new Database('mysql.domain.com', 'username', 'password', 'databaseName');

// Query the Database
$results = $db->query("SELECT Email FROM tUser WHERE UserID = ?", [42]); // returns an array of arrays
$email = $results[0]['Email']; // Get the Email in the first row - There would only be one row with one email anyway

// Insert into the Database
$db->execute("INSERT INTO tUser (Username, Email, Password) VALUES (?, ?, ?);", ['username', 'email@hoster.com', 'some hashed password']);
// No need to commit, this is done automatically

// Finally, close your Database
$db->close();
```
### `$db->query()`:
The `query` function is used when you need data that would be returned from a query, such as `SELECT`, `SHOW`, `DESCRIBE`, or `EXPLAIN` statements.  
### `$db->execute()`:
The `execute` function is used when you do _not_ need any data to be returned from the SQL statement, like `INSERT`, `UPDATE`, `DELETE`, `CREATE`, `DROP`, _etc._
