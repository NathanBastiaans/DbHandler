<?php

// Load the db handler
require_once ('DbHandler.php');

// Init the db handler
$db = new DbHandler(
    'localhost',
    'db_name',
    'root',
    'toor'
);

// inserts also return the last inserted id so we know the user id
$user_id = $db->query('insert into users set username = ?, password = ?, email = ?', ['Nathan', 'notMyPassword', 'nathan@email.com']);

// Fetch 1 row
$user = $db->fetch('select * from users where id = ?', [$user_id]);

// Fetch multiple rows
$users = $db->fetchAll('select * from users');