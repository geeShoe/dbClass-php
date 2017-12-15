# dbClass-php
Class for connecting to mysql databases that extends the PHP PDO extension. The goal is to automate many common mySql methods for projects that rely heavly on mysql.

Please note that this project is in initial development and as such, some documentation may be incomplete.

## Getting Started

These instructions will get you a copy of the project up and running on your local machine for development and testing purposes. See deployment for notes on how to deploy the project on a live system.

### Prerequisites

Current development of the project is being done with PHP 7.1, however earlier versions of PHP that support the mysql pdo extension will work. To check if the PDO mysql driver is enabled, run the following command in the CLI or add it to a page in your webroot

```
phpinfo();
```
and ensure PDO drivers lists mysql. If it doesn't or you cannot find any mention of PDO in phpinfo(). You may need to recompile PHP using:
```
./configure --with-pdo-mysql
```

### Installing

To add dbClass-php to your project, I recommend using composer. To do so create a composer.json file in your web root and add the following:
```
{
  "require": {
    "geeshoe/dbClass": "dev-master"
  },
  "autoload": {
    "psr-4": {
      "dBase\\": "vendor/geeshoe/dbclass/"
    }
  }
}
```
If you prefer to use the development branch of dbClass-php, change the following line of code in the composer.json file.

```
"geeshoe/dbClass": "dev-develop"
```

Using the console, navigate to the directory where your composer.json file is located and run:

```
composer install
```
Next, you will need to add the autoload.php to your index.php or to which ever file you wish to create a mysql query.
 To do so, you can use the code snippet below.
```
if(file_exists("vendor/autoload.php")){
    require "vendor/autoload.php";
} else {
    echo "Dam.. Something went wrong!";
}
```
That's it! You're all set to start using dbClass-php. If you prefer not to use composers autoload feature, just 
remove the "autoload" section from your composer.json file.

@TODO - Provide examples on how to use the dam thing.

## Authors

* **Jesse Rushlow** - *Lead developer* - [geeShoe Development](http://geeshoe.com)

Source available at (https://github.com/geeshoe)

For questions, comments, or rant's, drop me a line at 
```
jr (at) geeshoe (dot) com
```
