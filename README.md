# A simple PDO database handler

This is a simple PDO database handler class.

Init the database class. Place this somewhere in an index.php or config file after requiring the DbHandler.php file.
``` php
$db = new DbHandler(
    'localhost', // Hostname
    'db_name',   // Database
    'root',      // Username
    'toor',      // Password
    true,        // Return as object
    false        // Debug mode
);
```

Insert a new record into the users table
``` php
// inserts also return the last inserted id so we know the user id
$user_id = $db->query('insert into users set username = ?, password = ?, email = ?', ['Nathan', 'notMyPassword', 'nathan@email.com']);
``` 

Fetch the newly inserted user
``` php 
// Fetch 1 row
$user = $db->fetch('select * from users where id = ?', [$user_id]);
```

Select all users
``` php
// Fetch multiple rows
$users = $db->fetchAll('select * from users');
```

Delete the inserted user
``` php
// query can also be used for deletes or updates
$db->query('delete from users where id = ?', [$user_id]);
``` 

If it's still unclear check out the example.php file.