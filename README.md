
# A Dockerised Symfony 4 & PHP7 User API

This is an HTTP API to manage Users (create, read, update and delete)

### Tech

The technologies i used in this application are:

* [Docker] - with 4 containers: apache, PHP, MySQL and phpmyadmin
* [Symfony] - Version 4.2.7
* [PHP] - Version 7.2.10
* [Apache] - Web server running with Debian 9(Stretch)
* [MySQL] - Server version: 8.0.16


### Installation

Make sure you have:

 - Cloned the repository
 - Have docker working properly in your system


You need only to run 2 commands in order to have the application working in your local system


```
$ cd /Path/To/where/you/clone/folder
```

Then
```
$ docker-compose build
```

Then
```
$ docker-compose up
```


To try the application via postman you can use the provided collection [here](https://github.com/Belgacem-TLILI/UserApi/tree/master/contracts/postman)

Note: API is secured by an X-API-KEY

You can access to phpMyAdmin via the address http://localhost:8080/ username and password are belga

### Swagger contract

Use online [Swagger editor](https://editor.swagger.io/) the current contract is located [here](https://github.com/Belgacem-TLILI/UserApi/blob/master/contracts/v1.0.yml)

License
----

[MIT](https://choosealicense.com/licenses/mit/)


**Free Software, Hell Yeah!**
