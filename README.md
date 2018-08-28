API with Cache
========

Instructions
------
This is a simple challenge to test your skills on Symfony, PHPUnit and Cache.

As you can see Controller\CustomerController gives you the following capabilities:

* Create multiple customers by sending a JSON payload to the /customers/ endpoint
* Delete all customers
* Get all customers

What do you have to do?

* Improve the GET method to obtain customers from a cache server and unload the database.
* If customers are available on cache, you should not use the database.
* Ensure cache gets invalidated properly. E.g., database gets updated.
* Implement a failover. If there's a connectivity issue connecting with the cache server the app should fail gracefully by getting data from the database.
* Make Unit and Integration Tests to ensure everything is working fine.
* Feel free to improve existing code or add bug fixes if needed
* Good Commit Messages and SOLID knowledge are a plus.

Minimum Requirements
---------
* PHP 7.1
* [MongoDB driver](http://php.net/manual/en/mongo.installation.php#mongo.installation.nix)
* Mongo 4.1
* Redis 3

Installation
------
* Fire up a local MongoDB ```docker run -it -d -p 27017:27017 mongo:4.1```
* ```pecl install mongodb```
* ```cd <interview-folder>/```
* ```composer install```
* Run tests ```./bin/simple-phpunit```
* Run server ```php app/console server:run```
* Open http://127.0.0.1:8000/customers in your browser (check if everything is fine)
* You can test your database operation by doing a POST into /customers/
* $ curl http://127.0.0.1:8000/customers/ -X POST -d '[{"name":"leandro", "age":26}, {"name":"marcio", "age":30}]'
* Then check your MongoDB collection to see if customers were created or just call the action
* $ curl http://127.0.0.1:8000/customers/
