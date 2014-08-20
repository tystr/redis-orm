Redis ORM
=========
[![Build Status](https://travis-ci.org/tystr/redis-orm.svg?branch=master)](https://travis-ci.org/tystr/redis-orm)

This is a small object mapper library designed to assist in storing objects into a [Redis][1] database
while maintaining indexes for the fields of the object for efficient querying and filtering.

This project is under development. Use at your own risk.

TODO: 

 - Associated objects
 - Use redis transactions

Installation
============
Add to your project via composer:

    $ composer.phar require tystr/redis-orm:1.0.*

Setting up the Development Environment
======================================
You'll need [Vagrant][2] installed and configured correctly.

Simply run the following command to get your VM up and running:

    $ vagrant up

To run the [Behat][3] test suite:

    $ vagrant ssh
    $ cd /vagrant
    $ vendor/bin/behat

Read The Documentation
======================
* **[Intro](doc/00-intro.md)**
* **[Installation](doc/01-installation.md)**
* **[Usage](doc/02-usage.md)**
* **[Annotations](doc/03-annotations.md)**

[1]: http://redis.io/
[2]: http://vagrantup.com/
[3]: http://docs.behat.org/en/v3.0/