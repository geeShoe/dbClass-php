# dbClass-php

## dbClass-php has been depreciated. It has been replaced by [geeShoe/DbLib](https://github.com/geeShoe/DbLib)

Class for connecting to mysql databases that extends the PHP PDO extension. The goal is to automate many common mySql methods for projects that rely heavily on mysql.

Please note that this project is in initial development and as such, some documentation may be incomplete.

## Getting Started

These instructions will get you a copy of the project up and running on your local machine for development and testing purposes. See deployment for notes on how to deploy the project on a live system.

### Prerequisites

Current development of the project is being done with PHP 7.2, however earlier versions of PHP that support the mysql pdo extension will work. To check if the PDO mysql driver is enabled, run the following command in the CLI or add it to a page in your webroot

```
phpinfo();
```
and ensure PDO drivers lists mysql. If it doesn't or you cannot find any mention of PDO in phpinfo(). You may need to recompile PHP using:
```
./configure --with-pdo-mysql
```

### Installing

To add DbClass to your project, run:

```
composer require geeshoe/dbclass
```

If you prefer to use the development branch of dbClass-php, change the following line of code in the composer.json file.

```
composer require geeshoe/dbclass dev-develop
```

### Configure

Copy the included sample_config.ini to the parent directory of your vendor folder and rename it to DbConfig.ini. Change
the values in the mysql section to reflect your database configuration.

Setting AltPath to /some/other/path/config.ini 
will use the configuration directives in the specified config file rather than the ones in DbConfig.ini.

```
[config]
AltPath =

[mysql]
hostName = 127.0.0.1   //Points to the mysql server. Usually 127.0.0.1 or localhost 
port = 3306   //Typically the mysql port is 3306
dataBase = dbClassTest   //The name of the database which you will be using.
userName = testUser   //Both the username and password for the mysql account used to manipulate the mysql database
passWord = me6wp3Ha92n
```

### Documentation

The API is documented in docs/api/index.html


@TODO - Provide examples on how to use the dam thing.

### Authors

* **Jesse Rushlow** - *Lead developer* - [geeShoe Development](http://geeshoe.com)

Source available at (https://github.com/geeshoe)

For questions, comments, or rant's, drop me a line at 
```
jr (at) geeshoe (dot) com
```
