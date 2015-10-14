<?php

namespace tdt4237\webapp;

use tdt4237\webapp\models\User;

class Sql
{
    static $pdo;

    function __construct()
    {
    }

    /**
     * Create tables.
     */
    static function up()
    {
        $q1 = "CREATE TABLE users (id INTEGER PRIMARY KEY, user VARCHAR(50), pass VARCHAR(50), email VARCHAR(50), fullname VARCHAR(50), address VARCHAR(50), postcode VARCHAR (4), age VARCHAR(50), bio VARCHAR(50), isadmin INTEGER, isdoctor INTEGER, bankaccount VARCHAR(11), issubscribed INTEGER);";
        $q6 = "CREATE TABLE posts (postId INTEGER PRIMARY KEY AUTOINCREMENT, author TEXT, title TEXT NOT NULL, content TEXT NOT NULL, date TEXT NOT NULL, FOREIGN KEY(author) REFERENCES users(user));";
        $q7 = "CREATE TABLE comments(commentId INTEGER PRIMARY KEY AUTOINCREMENT, date TEXT NOT NULL, author TEXT NOT NULL, text INTEGER NOT NULL, belongs_to_post INTEGER NOT NULL, FOREIGN KEY(belongs_to_post) REFERENCES posts(postId));";

        self::$pdo->exec($q1);
        self::$pdo->exec($q6);
        self::$pdo->exec($q7);

        print "[tdt4237] Done creating all SQL tables.".PHP_EOL;

        self::insertDummyUsers();
        self::insertPosts();
        self::insertComments();
    }

    static function insertDummyUsers()
    {
        $hash1 = Hash::make(bin2hex(openssl_random_pseudo_bytes(2)));
        $hash2 = Hash::make('bobdylan');
        $hash3 = Hash::make('liverpool');

        $q1 = "INSERT INTO users(user, pass, isadmin, fullname, address, postcode, isdoctor, bankaccount, issubscribed) VALUES ('admin', '$hash1', 1, 'admin', 'homebase', '9090', 1, '', 0)";
        $q2 = "INSERT INTO users(user, pass, isadmin, fullname, address, postcode, isdoctor, bankaccount, issubscribed) VALUES ('bob', '$hash2', 1, 'Robert Green', 'Greenland Grove 9', '2010', 1, '11115553333', 1)";
        $q3 = "INSERT INTO users(user, pass, isadmin, fullname, address, postcode, isdoctor, bankaccount, issubscribed) VALUES ('bjarni', '$hash3', 1, 'Bjarni Torgmund', 'Hummerdale 12', '4120', 1, '', 0)";
       
        self::$pdo->exec($q1);
        self::$pdo->exec($q2);
        self::$pdo->exec($q3);

        print "[tdt4237] Done inserting dummy users.".PHP_EOL;
    }

    static function insertPosts() {
        $q4 = "INSERT INTO posts(author, date, title, content) VALUES ('bob', '26082015', 'I have a problem', 'I have a generic problem I think its embarrasing to talk about. Someone help?')";
        $q5 = "INSERT INTO posts(author, date, title, content) VALUES ('bjarni', '26082015', 'I also have a problem', 'I generally fear very much for my health')";

        self::$pdo->exec($q4);
        self::$pdo->exec($q5);
        print "[tdt4237] Done inserting posts.".PHP_EOL;
    }

    static function insertComments() {
        $q1 = "INSERT INTO comments(author, date, text, belongs_to_post) VALUES ('bjarni', '26082015', 'Don''t be shy! No reason to be afraid here',0)";
        $q2 = "INSERT INTO comments(author, date, text, belongs_to_post) VALUES ('bob', '26082015', 'I wouldn''t worry too much, really. Just relax!',1)";
        self::$pdo->exec($q1);
        self::$pdo->exec($q2);
        print "[tdt4237] Done inserting comments.".PHP_EOL;
    }

    static function down()
    {
        $q1 = "DROP TABLE users";
        $q4 = "DROP TABLE posts";
        $q5 = "DROP TABLE comments";

        self::$pdo->exec($q1);
        self::$pdo->exec($q4);
        self::$pdo->exec($q5);

        print "[tdt4237] Done deleting all SQL tables.".PHP_EOL;
    }
}
try {
    // Create (connect to) SQLite database in file
    Sql::$pdo = new \PDO('sqlite:app.db');
    // Set errormode to exceptions
    Sql::$pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
} catch (\PDOException $e) {
    echo $e->getMessage();
    exit();
}
