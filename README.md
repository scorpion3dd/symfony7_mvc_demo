# PROJECT - SIMPLE WEB DEMO "FREE LOTTERY" MANAGEMENT APPLICATION

## STATUS AND CONTACTS

<table>
    <tr>
        <th>
            <p>This project is no longer maintained.</p>
            <p>At this time, the repository has been archived, and is read-only.</p>
            <h3>(c) Denis Puzik <b>scorpion3dd@gmail.com</b></h3>
        </th>
        <th>
            <img src="/www/back/public/images/readme/3ds.jpg" alt="Logo 3ds">
        </th>
        <th>
            <img src="/www/back/public/images/symfony_black_02.svg" alt="Logo symfony">
        </th>
  </tr>
</table>

---
---

## INTRODUCTION

### RELEASE INFORMATION

**The simple web demo "free lottery" management application** project on symfony framework is a demo.
This application is designed to manage the process of lottery drawings, allowing users to
participate in the lottery, create and edit lottery drawings, and administer and monitor the drawings.

User feedback is monitored: for each user with active access to a valuable online resource,
other users can leave their comments, which, before being published in the public domain,
are automatically checked for spam and, if necessary, further manually moderated by the administrator.

Thus, administrators collect feedback from users, which is subsequently analyzed and used
to improve the product.

### BEST PRACTICES

- examples of `design and implementation of basic architectural solutions` that `improve performance and security`:

    - built on the use of `triggers, functions, procedures and events in the MySql` database - using these
      MySQL tools can significantly improve the performance and security of application, simplify its
      maintenance, and improve data integrity;
    - to `improve performance work with caching` at all levels: standard cache in Symfony Framework, cache in Doctrine,
      cache customers objects and data to lists in `Redis`, which stores `data in RAM`;
    - `asynchronous thread processing potentially lengthy processes` tied to interaction with third-party services,
      using `Rabbit MQ` programmatic message broker and using a scheme to exchange information between the sender
      and by the recipient, when data sources send information flows, and recipients process them as needed;
    - logging in the NoSql `MongoDB` database and the ability to view logs through the web interface;

- this application is designed on a `layered architecture` following `Domain Driven Design` (`DDD`)
  principles to provide a clean and maintainable code base;
- this project architecture makes it easy to `add new functionality` and `scale to accommodate increased load`;
- `program code` written according to the `"clean code"` principle, according to the concepts of `SOLID`, `DRY`, `DDD`,
  with the implementation of the current recommendations adopted by the `PSR`, written in Symfony Framework
  using the most common `Design Patterns`;
- using most of the `standard components` of the Symfony framework and third-party most effective
  and common components offering proven solutions:

    - for databases (relational MySql and NoSql types, MongoDB type) - a set of `Doctrine` components:
      DBAL, ORM, doctrine-bundle, ODM, mongodb-odm-bundle, doctrine-migrations-bundle, doctrine-fixtures-bundle;
    - `automatic` executing `complex PHP Unit, Integration and Functional tests`, providing the most
      `full code coverage` with tests and checking various combinations `all possible test cases`;
      which can be executed in the console after each change to the code and be sure that all checks
      are successfully passed - `PHPUnit` component set:  doctrine-test-bundle, php-code-coverage;
    - `automatic check` of `style` and `"purity"`, `static analysis` of the written `code`;
      which can be executed in the console after each change to the code and be sure that all checks
      are successfully passed - component set: phpcs, phpstan, psalm, phpmd;

- `automatic installation/updating/checking` in the project, by executing simple `composer` commands in the
  console from the `composer.json` file from the `scripts` section:

    - a set of all dependent components and libraries;
    - deleting the entire existing structure of the main database in MySql;
    - creating an empty structure of the main database in MySql,
    - generation of any volume, amount of data and filling them with tables of the main database in MySql;

- `Clean as Code` is an approach to code quality that eliminates many of the challenges that come with
  traditional approaches. As a developer, focus on maintaining high standards and taking responsibility
  specifically in the new code working on. `SonarQube` gives the tools to set high standards and take
  pride in knowing that code meets those standards.
- `optimal automated process for releasing software releases` use the principle of `CI / CD` in `Bitbucket
  Pipelines` use an integrated CI/CD service built into Bitbucket, which allows to `automatically build, test,
  and even deploy code` based on a configuration file in repository, in `Docker containers in the Cloud`.
- the application uses `advanced measures and solutions to ensure the security` of the application itself,
  as well as user data:

    - `Authentication and Authorization`: Use secure authentication methods (e.g. `OAuth`, `JWT`)
      and authorization to control access to resources;
    - `Role-based access controls` - allow to set rules that allow or deny access to certain
      resources on a website;
    - `Web form validators` - allow to be sure that malicious data entered by the user will
      not pass through the web form. Validators are used to verify that data sent via the web
      form, comply with certain rules;
    - `Cross-Site Request Forgery` (`CSRF`) form elements - used to prevent hacker attacks;
    - `Cryptography support` - allows to store sensitive data such as passwords encrypted
      with strong cryptographic algorithms that are difficult to crack;
    - `Injection Protection`: Using Prepared Statements or ORM (Object-Relational Mapping) to
      work with the database to prevent SQL injections;

- which `can guarantee` a `high speed of release of new versions` of the software
  product, `high quality of the code and the functionality` of the released
  software product, the `absence of bugs` and, as a result, `customer
  satisfaction` and `increase in sales of the manufactured software product` and,
  accordingly, an `increase in profits`.


---
---

## GETTING STARTED

### SYSTEM REQUIREMENTS


1. `Web HTTP Server` (example: `Apache` 2.4 HTTP server with mod_rewrite
   module or `Nginx` HTTP server)
2. `PHP` 8.2 with extensions gd, mbstring, xdebug, intl, pdo,
   mongodb, redis
3. `Symfony Framework`, Doctrine ORM, ODM, DataFixtures, Migrations,
   PHPUnit and other components
4. `DB MySql` 8.0, or later (active use triggers in tables, functions
   and procedures, events)
5. `DB Redis` 5.0 or later
6. `MongoDB` 5.0 or later
7. `Rabbit MQ` use asynchronous thread processing potentially lengthy processes
8. `Bitbucket Pipelines` use the principle of CI / CD remote
9. `Jenkins` uses the principle of CI / CD local
10. `SonarQube` is a self-managed, automatic code review tool that systematically
    helps deliver clean code

### INSTALLATION


0. In MySql, execute the actions of the items 1.-5. - `automatically` by executing next
~~~~~~bush
mysql -u root -p
~~~~~~
~~~~~~mysql
CREATE DATABASE learn CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE DATABASE learn_test CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'learn'@'%' IDENTIFIED BY 'learn123';
CREATE USER 'learn_test'@'%' IDENTIFIED BY 'learn_test123';
GRANT USAGE ON *.* TO 'learn'@'%';
GRANT USAGE ON *.* TO 'learn_test'@'%';
GRANT ALL PRIVILEGES ON learn.* TO 'learn'@'%';
GRANT ALL PRIVILEGES ON learn_test.* TO 'learn'@'%';
GRANT ALL PRIVILEGES ON learn_test.* TO 'learn_test'@'%';
FLUSH PRIVILEGES;
SET GLOBAL log_bin_trust_function_creators = 1;
SHOW GRANTS FOR 'learn'@'%';
SHOW GRANTS FOR 'learn_test'@'%';
~~~~~~

1. In MySql create DB `learn`
~~~~~~mysql
CREATE DATABASE learn CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
~~~~~~

2. In MySql create DB `learn_test`
~~~~~~mysql
CREATE DATABASE learn_test CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
~~~~~~

3. In MySql create user `learn` for DB learn
~~~~~~mysql
CREATE USER 'learn'@'%' IDENTIFIED WITH mysql_native_password BY 'learn123';
GRANT USAGE ON *.* TO 'learn'@'%';
GRANT ALL PRIVILEGES ON learn.* TO 'learn'@'%';
GRANT ALL PRIVILEGES ON learn_test.* TO 'learn'@'%';
FLUSH PRIVILEGES;
SHOW GRANTS FOR 'learn'@'%';
~~~~~~

4. In MySql create user `learn_test` for DB learn_test
~~~~~~mysql
CREATE USER 'learn_test'@'%' IDENTIFIED WITH mysql_native_password BY 'learn_test123';
GRANT USAGE ON *.* TO 'learn_test'@'%';
GRANT ALL PRIVILEGES ON learn_test.* TO 'learn_test'@'%';
FLUSH PRIVILEGES;
SHOW GRANTS FOR 'learn_test'@'%';
~~~~~~

5. In MySql, if necessary, set a parameter to relax the checking of non-deterministic functions
~~~~~~bush
mysql -u root -p
~~~~~~
and
~~~~~~mysql
SET GLOBAL log_bin_trust_function_creators = 1;
~~~~~~

6. Clone a project code from the GIT repository
~~~~~~bash
git clone https://scorpion3dd@bitbucket.org/3dscorpion7/docker-symfony7-learn.git
~~~~~~

7. Composer install the dependencies (Symfony Framework components and Doctrine)
~~~~~~bash
composer install
~~~~~~
or
~~~~~~bash
composer install --ignore-platform-reqs
~~~~~~

8. Generate keypair for JWT
~~~~~~bash
composer jwt-generate-keypair
~~~~~~

9. Perform NPM initialization for the project
~~~~~~bash
composer npm-install
composer symfony-npm-ci
~~~~~~

10. Collect Webpack resources CSS and JS files for the project
~~~~~~bash
composer symfony-npm-run-dev
~~~~~~

11. In files in /configs, if necessary, change the parameters

Execute the actions of the items 12.-14. - `automatically` by executing the
executable file `project_init.sh`, after giving it permission to execute:

~~~~~~bash
sudo chmod +x ./project_init.sh
./project_init.sh
sudo chmod -R 777 ./data/logs
~~~~~~

Or execute the actions of the items 12.-14. individually manually.

12. Copy config files from distribute version to worked, if necessary, change the parameters
    (example set database password parameter)
```bash
cp .env.dist .env
```
and
```bash
cp .env.test.dist .env.test
```
and
```bash
cp phpcs.xml.dist phpcs.xml
```
and
```bash
cp phpmd_ruleset.xml.dist phpmd_ruleset.xml
```
and
```bash
cp phpstan.neon.dist phpstan.neon
```
and
```bash
cp phpunit.xml.dist phpunit.xml
```
and
```bash
cp psalm.xml.dist psalm.xml
```
and
```bash
cp sonar-project.properties.dist sonar-project.properties
```
and
```bash
cp bitbucket-pipelines.yml.dist bitbucket-pipelines.yml
```

13. Give write permissions to directories

- /var/cache
- /var/log

Adjust permissions for `data` directory:
~~~~~~bash
sudo chown -R www-data:www-data ./var/cache
sudo chown -R www-data:www-data ./var/log
~~~~~~

14. Adjust permissions for next directories
~~~~~~bash
sudo chmod -R 777 ./var/cache
sudo chmod -R 777 ./var/log
sudo chmod -R 777 ./config/jwt
~~~~~~

15. In MySql create or refresh empty structure for database `learn` (tables,
    triggers, functions, procedures, events) and generate fixtures data by
    executing the SQL script, run next command
~~~~~~bash
composer project-create-dev
~~~~~~
or
~~~~~~bash
composer project-refresh-dev
~~~~~~

16. In MySql create or refresh empty structure for database `learn_test` (tables,
    triggers, functions, procedures) and generate fixtures data by
    executing the SQL script, run next command
~~~~~~bash
composer project-create-test
~~~~~~
or
~~~~~~bash
composer project-refresh-test
~~~~~~

17. Create virtual host in web server
    1. Web server setup Apache

To setup apache, setup a virtual host to point to the public/ directory of the
project and should be ready to go! It should look something like below:

```apache
<VirtualHost *:80>
    ServerName symfony-mvc.learn.vms
    ServerAlias symfony-mvc.learn.vms
	DocumentRoot /path/to/symfony-mvc/public
	<Directory /path/to/symfony-mvc/public/>
          DirectoryIndex index.php
          AllowOverride All
          Order allow,deny
          Allow from all
          <IfModule mod_authz_core.c>
              Require all granted
          </IfModule>
    </Directory>
	ErrorLog ${APACHE_LOG_DIR}/error_learn.log
	CustomLog ${APACHE_LOG_DIR}/access_learn.log combined
</VirtualHost>
```

    2. Web server setup Nginx setup

To setup nginx, open `/path/to/nginx/nginx.conf` and add an
[include directive](http://nginx.org/en/docs/ngx_core_module.html#include) below
into `http` block if it does not already exist:

```nginx
http {
    # ...
    include sites-enabled/*.conf;
}
```

Create a virtual host configuration file for project under `/path/to/nginx/sites-enabled/zfapp.localhost.conf`
it should look something like below:

```nginx
server {
    listen       80;
    server_name  symfony-mvc.learn.vms;
    root         /path/to/symfony-mvc-learn/public;

    location / {
        index index.php;
        try_files $uri $uri/ @php;
    }

    location @php {
        # Pass the PHP requests to FastCGI server (php-fpm) on 127.0.0.1:9000
        fastcgi_pass   127.0.0.1:9000;
        fastcgi_param  SCRIPT_FILENAME /path/to/symfony-mvc-learn/public/index.php;
        include fastcgi_params;
    }
}
```

Restart the Apache or Nginx, now should be ready to go!

18. Reload Web Server (example Apache)
~~~~~~bash
sudo systemctl restart apache2
~~~~~~

19. Reload Web Server (example Nginx)
~~~~~~bash
sudo systemctl restart nginx
~~~~~~

20. Run all `PHP automatic checks` (code style checker, code static checker, Unit and Integration tests)
~~~~~~bash
composer check-project
composer check-project-coverage
~~~~~~

21. Now should be able to see the `Simple Web Demo Free Lottery Management Application`
    website by visiting the link "[symfony-mvc.learn.vms](http://symfony-mvc.learn.vms/)".

22. If want to test the site performance on any amount of data, can:

- set value to parameter `COUNT_RESIDENT_USERS` or `COUNT_NOT_RESIDENT_USERS`
  the required number of entries for new users - in the file `.env` for the website
  and in the file `.env.test` for tests, and save;

- automatically completely recreate all data sets for the website and for integration tests, i.e. delete all data
  and structures of MySql databases, create a new empty database structure, generate dummy datasets,
  perform all automatic checks - to do this, execute just one command:
~~~~~~bash
composer project-refresh-dev
composer project-refresh-test
~~~~~~

## DESCRIPTION

### GENERAL

This application is designed to manage the process of lottery drawings,
allowing users to participate in the lottery, create and edit lottery
drawings, and administer and monitor the drawings.

The `main logic` of this application `lottery drawing` is performed by **random** setting
and resetting `user access` to a valuable online resource through `automatic calling`
of the corresponding `stored procedures` **setUsersAccesses** and
**setUsersNotAccesses** based on `events` **setAccesses** every minute and **setNotAccesses**
every hour in the database MySql.

`Logs` of these executions can be viewed in the table `logs` in the database MySql:

![MySql Logs](/www/back/public/images/readme/mysql_logs.jpg)


### ACCESS INTERFACES

An `access interface` is a way of interacting with a system or application,
allowing the user to control it or obtain information from it.
That is, it is the way in which the user can interact with the program, perform
operations, or access its functionality.

In `this project` the `access interface` can be: a `website` through which the user
interacts with the web application, or a `command line` through which the admin
can be controlled using commands, or `REST API` through which the other services
interact with the application.

Most `lottery comments` will be sent from `smartphones`, not all users carry laptops
with them. So why not create a `mobile application` (`SPA`) where can `quickly view
lottery comments`?

### WEB SITE

#### ADVANTAGES

An `access interface` via website:

- `Advantages`: Allows to conveniently interact with the application
  through a browser, which is convenient for users not familiar with the
  command line or programming.
- `Why`: Used to provide an interactive interface, display information,
  perform actions through a graphical user interface (GUI).
- `Examples`: public consoles, administrative panels, online platforms.

The web part of this project consists of a `public part` and an `admin panel`,
which can only be entered after passing authentication, having entered
the login and password correctly.

#### ADMINISTRATIVE PANEL

The `administrative panel` is a secure section of the site where
project administrators can change data, moderate comments,
and much more.

Can quickly generate an administration panel based on the project
model using the `EasyAdmin bundles`.

`EasyAdmin Bundle` is a Symfony package that allows to quickly
create admin interfaces for managing Doctrine entities.

`EasyAdmin` automatically generates an admin panel from specific
controllers in the application.

Users with the "Administrator" role in `manual mode`, after
authentication in the application, with next credentials:

- login: **admin1**
- password: **admin1**

![Login](/www/back/public/images/readme/login.jpg)

Administrator through the admin panel can:

- view `Dashboard` with different analytics information;

![Dashboard](/www/back/public/images/readme/dashboard.jpg)

- view, create, edit, delete records with `User` data;

![Users](/www/back/public/images/readme/users.jpg)

![User view](/www/back/public/images/readme/user_004.jpg)

![User view comments](/www/back/public/images/readme/user_005.jpg)

![User create](/www/back/public/images/readme/user_006.jpg)

![User create](/www/back/public/images/readme/user_007.jpg)

![User delete](/www/back/public/images/readme/user_008.jpg)

![User delete](/www/back/public/images/readme/user_009.jpg)

- Search and Filter `users`;

![Users search](/www/back/public/images/readme/user_010.jpg)

![Users filter](/www/back/public/images/readme/user_011.jpg)

![Users filter](/www/back/public/images/readme/user_012.jpg)

- Paginate `users`;

![Users paginate](/www/back/public/images/readme/user_013.jpg)

- Sort `users`;

![Users sort](/www/back/public/images/readme/user_014.jpg)

- change the `status of users` from active to inactive;

![User change the status](/www/back/public/images/readme/user_001.jpg)

- give other `users access` to a very valuable resource.

![User give access](/www/back/public/images/readme/user_002.jpg)

- view `comments to user`:

![User view comments](/www/back/public/images/readme/user_003.jpg)


When the Administrator changes the user's data, for example, changes the
Status or the User's Access - after saving the changes in the database,
the user is `sent a notification letter to his email`.

Since the sending of emails affects `external systems`, this process may
take some time. In order to quickly release (not delay the process of
sending letters) the web interface to the Administrator for its further
work, `Rabbit MQ` is used, which is a message broker and provides
uninterrupted and convenient data exchange in messages during `asynchronous
communication between the Producer and the Consumer`.

Thus, the `Producer` script writes the necessary information in a specific
data structure, which is serialized into a string and transmitted as a message
to Rabbit MQ and returns control back to the Administrator, `without delay`.

And after some short period of time in the `Consumer` script, all messages
from Rabbit MQ are read in a stream and the text of each message is parsed
in a certain way and deserialized into a certain data structure, followed
by `specific processing` (`sending a letter` from the Administrator to the
user with a specific message text).

And also in `automatic mode` for random users, the application can perform
a "Free Lottery", that is, change the status of users from active to
inactive and can give other active users the opportunity to access a
very valuable resource. This is possible through active use in DB MySql
`triggers in tables, functions and procedures, events`.


Users with the "Administrator" role in manual mode, after
authentication in the application, through the admin panel can:

- view, create, edit, remove user access `permissions`;

![Permissions](/www/back/public/images/readme/permissions.jpg)

![Permission view](/www/back/public/images/readme/permission_001.jpg)

![Permission edit](/www/back/public/images/readme/permission_002.jpg)

- Search and Filter, Paginate and Sort `permissions`;

![Permissions search](/www/back/public/images/readme/permission_003.jpg)

![Permissions filter](/www/back/public/images/readme/permission_004.jpg)

![Permissions filter](/www/back/public/images/readme/permission_005.jpg)

![Permissions paginate](/www/back/public/images/readme/permission_006.jpg)

![Permissions sort](/www/back/public/images/readme/permission_007.jpg)

- view, create, edit, delete user `roles`;

![Roles](/www/back/public/images/readme/roles.jpg)

![Role view](/www/back/public/images/readme/role_002.jpg)

- Search and Filter, Paginate and Sort `roles`;

![Roles search](/www/back/public/images/readme/role_003.jpg)

![Roles filter](/www/back/public/images/readme/role_004.jpg)

![Roles filter](/www/back/public/images/readme/role_005.jpg)

![Roles paginate](/www/back/public/images/readme/role_006.jpg)

![Roles sort](/www/back/public/images/readme/role_007.jpg)

- make `user roles` hierarchical;

![Role edit](/www/back/public/images/readme/role_001.jpg)

- assign access permissions to `user roles`;

- change `user roles`;

- view, create, edit, remove user `logs`;

![Logs](/www/back/public/images/readme/logs.jpg)

![Log view](/www/back/public/images/readme/log_001.jpg)

![Log edit](/www/back/public/images/readme/log_002.jpg)

- Filter, Paginate and Sort `logs`;

![Logs filter](/www/back/public/images/readme/log_005.jpg)

![Logs paginate](/www/back/public/images/readme/log_003.jpg)

![Logs sort](/www/back/public/images/readme/log_004.jpg)

It shows how to:

* Implement roles and permissions
* Organize roles in database into a hierarchy
* Use dynamic assertions to implement complex access control rules

#### PUBLIC PART


The "Free Lottery" application on the main page, `freely available`,
without user authentication  can:

- allows to view a `list of active users` with open access to a very
  valuable resource at a given time;

![Lottery main](/www/back/public/images/readme/lottery_001.jpg)

- Filter, Paginate and Sort `Lottery`;

![Lottery filter](/www/back/public/images/readme/lottery_007.jpg)

![Lottery paginate](/www/back/public/images/readme/lottery_008.jpg)

![Lottery sort](/www/back/public/images/readme/lottery_009.jpg)

- view a `detail info about of active user with comments` with open access to a very
  valuable resource at a given time;

![Lottery user detail info](/www/back/public/images/readme/lottery_010.jpg)
![Lottery user detail info comment](/www/back/public/images/readme/lottery_011.jpg)

- write and add new comment to `user`;

![Lottery user add comment](/www/back/public/images/readme/lottery_012.jpg)

Problems may arise while `checking the form for spam`. If the response from
the external `Akismet API` takes a long time, our form page will also become
slower. The worst-case scenario is that we may lose comments completely due
to a timeout or Akismet API being unavailable.

Ideally, we should immediately return a response to a submitted form, and
the application should save the data without attempting to publish it
immediately. Can check for spam later.

Therefore, after sending a comment, it is not immediately displayed to all
users, notify users directly in the browser that their comments are reviewed
after they are submitted:

![Lottery user add comment message](/www/back/public/images/readme/lottery_013.jpg)

And as an addition, a beautiful notification will appear at the top of the site
if an error occurs when filling out the form.

The `Messenger component` manages asynchronous code in Symfony.
To perform `asynchronous tasks`, send a message to the `messaging bus`.
The bus stores the message in a queue and returns the response immediately
to eliminate any delays in applications.

The recipient works continuously in the background, reading new messages
from the queue and performing related tasks.
The recipient can work either on the same server where the application
itself is located, or on a separate server.

Symfony has a `console command` to receive messages (written in file
composer.json):
~~~~~~bash
symfony console messenger:consume async -vv --ansi
~~~~~~
or
~~~~~~bash
composer messenger-consume-async
~~~~~~

Instead of starting the recipient every time a comment is posted and
stopping it immediately, I'd like it to run continuously.

Symfony CLI can manage such background commands or workers by adding
a flag ( -d ) to the run command.

Launch the message recipient again, but now in the background:
~~~~~~bash
symfony run -d --watch=config,src,templates,vendor symfony console messenger:consume async -vv --ansi
~~~~~~
or
~~~~~~bash
composer messenger-consume-async-d
~~~~~~

Requires checking for spam, `automatically background in console`:

![Lottery messenger-consume-async](/www/back/public/images/readme/lottery_015.jpg)

Users probably don't realize that the comment is being reviewed and is therefore
not published immediately. For this reason, they may resend it thinking there
was a technical error. It would be great to notify them after posting a comment.

It's also a good idea to let them know when the comment is published.
We ask users to provide an email.

There are many ways to notify users. Email is the first thing that comes to mind,
although we can also do this on the site itself. Send SMS messages or notifications
to `Slack` or Telegram - can choose any of the options.

The Symfony `Notifier component` offers many notification strategies.

The notifier sends a notification to recipients over a channel.

A notification consists of a subject, optional content, and severity.

The notification is sent through one or more channels depending on its importance.
For example, can send urgent notifications via SMS and regular notifications
via email.

We all expect positive or at least constructive feedback. If someone writes a
comment with the words `great` or `awesome`, we'll likely approve it faster.

You want to receive such messages not only by email, but also in chats, for
example in `Slack`.

If post a comment containing the word `great` and see that comment
in `Slack chat`:

![Lottery user comment slack](/www/back/public/images/readme/lottery_016.jpg)

![Lottery user comment email](/www/back/public/images/readme/lottery_021.jpg)

By analogy with an email, we also changed the standard message design in Slack.
Wouldn't it be great to approve or reject a comment directly in Slack.
Change the notification to accept a comment check URL and add two buttons
to the Slack message.
Once messages are sent to all channels asynchronously, the messages themselves
cease to depend on each other.
In Slack message click to button Accept - will be open new page with message:

![Lottery user comment page](/www/back/public/images/readme/lottery_017.jpg)

And, if necessary, manual moderation by the Administrator in the Admin panel:

![Lottery user comment moderation](/www/back/public/images/readme/lottery_014.jpg)

![Lottery user comment moderation published](/www/back/public/images/readme/lottery_018.jpg)

![Lottery user comment moderation published](/www/back/public/images/readme/lottery_019.jpg)

And after all this, the comment will appear in the public domain for all users:

![Lottery user comment public](/www/back/public/images/readme/lottery_020.jpg)

The web part of this project consists of a public part with page `About`.

![Lottery about](/www/back/public/images/readme/lottery_002.jpg)

Select `different languages` in the user interface and all services texts
in pages `translator to selected language`.

![Lottery language](/www/back/public/images/readme/lottery_003.jpg)

![Lottery language French about](/www/back/public/images/readme/lottery_004.jpg)

![Lottery language French main](/www/back/public/images/readme/lottery_005.jpg)

![Lottery language English main](/www/back/public/images/readme/lottery_006.jpg)


For great performance application use DB `Redis`, which save data to RAM and
operations write and read from Redis running very quickly.

The "Free Lottery" application during operation `writes logs` to a `text file`,
in the `MongoDB database` and in the `MySql database`.
In order to view the logs from the mongo database, need to go to the "Logs"
page, on which can view a list of logs, can click on a specific log
and view detailed data about the log, can edit or delete a specific log,
can manually add a new entry for a new log.



---
---

### COMMAND LINE

#### ADVANTAGES

An `access interface` via command line:

- `Advantages`: Provides faster and more flexible access to functionality,
  allows to automate tasks using scripts.
- `For what`: Used to execute commands, scripts and control the application
  without using a graphical interface.
- `Examples`: Console utilities, data processing scripts, server management.

#### COMMAND LINE USAGES

The `project administrator`, having connected to the server, for example via SSH,
can log into the project console and execute the necessary project console
commands, for example:

- view `console about` commands:
~~~~~~bush
composer console-about
~~~~~~
or
~~~~~~bush
php bin/console about --ansi
~~~~~~

![Console about](/www/back/public/images/readme/console_002.jpg)

- view all `list console` commands:
~~~~~~bush
composer list-console
~~~~~~
or
~~~~~~bush
php bin/console list --ansi
~~~~~~

![Console list](/www/back/public/images/readme/console_001.jpg)

- view all `list routers` commands:
~~~~~~bush
composer list-routers
~~~~~~
or
~~~~~~bush
php bin/console debug:router --ansi
~~~~~~

![Console routers](/www/back/public/images/readme/console_003.jpg)

- run `messenger consume async` commands:
~~~~~~bush
composer messenger-consume-async
~~~~~~
or
~~~~~~bush
symfony console messenger:consume async -vv --ansi
~~~~~~

![Console messenger consume async](/www/back/public/images/readme/console_004.jpg)

- view all `messenger list handlers` commands:
~~~~~~bush
composer messenger-list-handlers
~~~~~~
or
~~~~~~bush
php bin/console debug:messenger --ansi
~~~~~~

![Console messenger list handlers](/www/back/public/images/readme/console_005.jpg)

- run `messenger users create faker user` commands:
~~~~~~bush
composer messenger-console-users-create-faker-user
~~~~~~
or
~~~~~~bush
php bin/console app:users:create-faker-user --ansi
~~~~~~

![Console messenger users create faker user](/www/back/public/images/readme/console_006.jpg)

- run `messenger comment message` commands:
~~~~~~bush
composer messenger-console-comment-message
~~~~~~
or
~~~~~~bush
php bin/console app:comment:message --ansi
~~~~~~

![Console messenger comment message](/www/back/public/images/readme/console_007.jpg)

![Console messenger comment message](/www/back/public/images/readme/console_008.jpg)

- run `users find user by email async` commands:
~~~~~~bush
composer symfony-users-find-user-by-email-async
~~~~~~
or
~~~~~~bush
symfony console app:users:find-user-by-email-async --ansi
~~~~~~

![Console users find user by email async](/www/back/public/images/readme/console_009.jpg)

- run `users find user by email sync` commands:
~~~~~~bush
composer symfony-users-find-user-by-email-sync
~~~~~~
or
~~~~~~bush
symfony console app:users:find-user-by-email-sync --ansi
~~~~~~

![Console users find user by email sync](/www/back/public/images/readme/console_010.jpg)

- run `comment cleanup` commands:
~~~~~~bush
composer symfony-comment-cleanup
~~~~~~
or
~~~~~~bush
symfony console app:comment:cleanup --ansi
~~~~~~

![Console comment cleanup](/www/back/public/images/readme/console_011.jpg)

### REST API

#### ADVANTAGES

An `access interface` via REST API:

- `Advantages`: Allows to interact with the application from anywhere
  on the Internet, provides ease of integration with other applications.
- `Why`: Used to access data and application functionality over the network,
  often used in microservice architecture.
- `Examples`: Mobile applications, web services, integration with other
  systems via API.

#### REST API USAGES

`Mobile applications`, `web services`, integration with `other systems`
via REST API, having connected to the server, for example via HTTP or HTTPS,
can execute the necessary project REST API.

`Api-platform` automatically generates `API documentation` based on API's
schema, making it easy for developers to understand and use API.

For automatically generates `API documentation` run `openapi export` console commands:
~~~~~~bush
composer openapi-export
~~~~~~
or
~~~~~~bush
php bin/console api:openapi:export --output=./var/api/swagger_docs.json --ansi
~~~~~~

![Console openapi export](/www/back/public/images/readme/console_012.jpg)

View automatically generates `API documentation` in web:

![API platform](/www/back/public/images/readme/api_platform_001.jpg)

![API platform](/www/back/public/images/readme/api_platform_002.jpg)

![API platform](/www/back/public/images/readme/api_platform_003.jpg)

![API platform](/www/back/public/images/readme/api_platform_004.jpg)

![API platform](/www/back/public/images/readme/api_platform_005.jpg)

For example `API documentation` for route `GET /api/admins`:

![API platform admins](/www/back/public/images/readme/api_platform_006.jpg)

![API platform admins](/www/back/public/images/readme/api_platform_007.jpg)

For example execute project `REST API` routes:

- run `login` API route:
~~~~~~bush
POST /api/login
~~~~~~

Request:

![REST API login](/www/back/public/images/readme/api_001.jpg)

Result get response:

![REST API login](/www/back/public/images/readme/api_002.jpg)

![REST API login](/www/back/public/images/readme/api_003.jpg)

- view `admins` API route:
~~~~~~bush
GET /api/admins
~~~~~~

Request:

![REST API admins](/www/back/public/images/readme/api_004.jpg)

Result get response:

![REST API admins](/www/back/public/images/readme/api_005.jpg)

![REST API admins](/www/back/public/images/readme/api_006.jpg)

![REST API admins](/www/back/public/images/readme/api_007.jpg)

- view `users lottery` API route:
~~~~~~bush
GET /api/users/lottery
~~~~~~

Request:

![REST API users lottery](/www/back/public/images/readme/api_008.jpg)

Result get response:

![REST API users lottery](/www/back/public/images/readme/api_009.jpg)

![REST API users lottery](/www/back/public/images/readme/api_010.jpg)

![REST API users lottery](/www/back/public/images/readme/api_011.jpg)

![REST API users lottery](/www/back/public/images/readme/api_012.jpg)

- create `user` API route:
~~~~~~bush
POST /api/users/lottery
~~~~~~

Request:

![REST API users create](/www/back/public/images/readme/api_013.jpg)

Result get response:

![REST API users create](/www/back/public/images/readme/api_014.jpg)

![REST API users create](/www/back/public/images/readme/api_015.jpg)

- add `user comment with upload file picture` API route:
~~~~~~bush
POST /api/comments/upload
~~~~~~

Request:

![REST API users comment upload](/www/back/public/images/readme/api_016.jpg)

Result get response:

![REST API users comment upload](/www/back/public/images/readme/api_017.jpg)

![REST API users comment upload](/www/back/public/images/readme/api_018.jpg)

### SPA

#### ADVANTAGES

Building a JavaScript `Single Page Application` (`SPA`) is one way to create such a
`mobile application`. SPA runs locally, can use local storage, make HTTP requests
to third-party APIs, and also supports service workers that provide the benefits
of an almost real (native) application.

To create a `mobile application` we will use `Preact` and `Symfony Encore`.

`Preact` is a small and efficient library that is well suited for this project
`SPA application`.

To make the site and SPA clear and predictable, we will use the same Sass style
sheets for the mobile application as for the site.

#### SPA USAGES

`Preact` is a JavaScript library for creating `user interfaces` (`UIs`) that enables
fast and efficient web application development. `Preact` is an alternative to `React`,
but is more compact in size and easier to implement in projects with limited resources.

Main features of `Preact`:

* `Lightweight`: Preact is about 3 KB in size (compressed and minified), making it one
  of the most compact frameworks for creating interfaces.
* `React Compatible`: Preact is compatible with the React API, allowing to use most
  of the features and functionality of React without having to rewrite code.
* `Fast loading and rendering`: Being lightweight, Preact ensures fast loading and
  rendering of web pages, which improves the user experience.
* `Small ecosystem`: Unlike React, which has a huge community and many plugins, Preact
  has fewer plugins and tools, but enough to build most web applications.
* `Use in SPA and SSR`: Preact can be used for both Single Page Application (SPA) and
  Server Rendering (SSR) development.

Using `Preact` with `Symfony Encore` will allow to create modern and powerful `mobile
applications` with ease and efficiency of development.

Since this application works independently of the main site, need to run another web server.

## ARCHITECTURE

### GENERAL DESCRIPTION

`Simple web demo free lottery management application` is a web application
developed in PHP 8.2 using the Symfony framework version 7.

This application is designed to manage the process of lottery drawings,
providing users with the opportunity to participate in the lottery,
creating and editing lottery draws, as well as administering and monitoring draws.

The `application architecture` is built on the principles of modularity and layering.
Key application components providing separation of responsibilities and making it
easier to maintain and extend the functionality of the application.

It contains the following components:

- `View`: Responsible for displaying data to the user.
- Business Logic (`Model`): Contains application logic and interacts with the database.
- `Controllers`: Process user requests and interact with business logic and presentation.

To interact with the database, `Doctrine ORM` is used, which provides an
object-oriented approach to working with data. The application also
maintains scalability and performance using caching and database query
optimization mechanisms.

Application `security` is ensured through attack protection and data encryption.

`Logging and monitoring` are carried out using appropriate tools, providing the
ability to track application performance and identify problems.

Application is designed with `DDD` to be modular and extensible, providing
an `API` to add new functionality and integrate with other systems.

Designing an application with `DDD` in mind helps create a more flexible,
understandable and scalable architecture, making the application easier
to maintain and extend.

### DOMAIN-DRIVEN DESIGN

An application developed using `Domain-Driven Design` (`DDD`) typically has
the following features and principles:

- `Ubiquitous Language`: A core principle of DDD that uses a common language to
  describe the business rules and concepts of an application. This language
  should be understandable to both business experts and developers.
- `Domain Model`: The central part of the application, which is an object
  model of business processes and rules. The domain model contains entities,
  aggregates, services and other elements that reflect the structure and
  logic of the application.
- `Entities`: Objects that have an identity and a life cycle, and which are
  the key objects in the domain model. For example, in a lottery management
  application, the entity might be a lottery ticket.
- `Aggregates`: Groupings of related entities and values that are treated as a whole.
  Aggregates define the boundaries of data integrity in an application and
  provide consistency.
- `Repositories`: Objects responsible for storing and retrieving entities from
  a data store. Repositories provide an abstraction for working with a database
  while hiding the implementation details.
- `Domain Services`: Contains the logic that which does not relate directly to
  any specific entity, but represents operations or processes associated with
  business rules.
- `Factories`: Used to create complex objects or units with specified parameters.
  Factories help manage the process of creating objects according to business rules.
- `Application Services`: Provides an interface for interacting with external systems
  or the user interface. Application services are used to perform specific tasks
  using business logic from the domain model.

### ARCHITECTURAL PRINCIPLES

When developing an application using `Domain-Driven Design` (`DDD`),
the following architectural principles apply:

- `Separating business logic from infrastructure code`: The application's
  business logic should be isolated from technical implementation details
  such as the database or frameworks. This provides a more flexible architecture
  and simplifies testing and support.
- `Use of domain language in code`: The common language used by business experts
  and developers should be reflected in the application code. This helps reduce
  misunderstandings and improve communication between project participants.
  Layering: The application is divided into layers based on functionality, such
  as the presentation layer, the business logic layer, and the data access layer.
  This improves code readability and maintainability.
- `Using Aggregates`: Entities and their associated objects are grouped into aggregates,
  allowing to ensure data integrity and manage it through a single point of entry.
- `Invariant support`: Business rules and invariants must be explicitly expressed
  in code and supported at all levels of the application. This ensures data
  consistency and prevents violations of business rules.
  Using the event model: Events are used to control asynchronous processes and
  notifications in an application. This helps reduce dependencies and improve scalability.
- `Careful design of aggregates and entities`: Aggregates and entities should be
  designed taking into account their purpose and the relationships between them,
  to ensure efficient operation and data management.
- `Use of transactions`: To ensure data integrity in an application, should
  use transactions where necessary to avoid data loss or violation of business rules.

These principles help create a flexible, scalable and easily maintainable
application architecture, consistent with DDD principles.

### EXPLICIT ARCHITECTURE

`Explicit architecture` is an approach to software architecture design in
which major architectural decisions are made based on explicit and well-defined
principles and rules. The goal of explicit architecture is to make the
application structure understandable, easily maintainable, and scalable.

Basic principles of explicit architecture include:

- `Clear separation of responsibilities`: Each application component or layer
  should be responsible for only its part of the functionality. For example,
  the presentation layer is responsible only for displaying data to the user,
  and the business logic layer is responsible for processing business rules.
  Explicitly Defining Interfaces: All interactions between application components
  must be done through explicitly defined interfaces. This makes it easy to
  replace or modify components without having to change other parts of the application.
- `Minimize dependencies`: Application components should be loosely coupled to
  each other, so that changes in one component do not lead to unexpected
  consequences in other components.
- `Explicit life cycle management`: The life cycle of objects and components
  must be clearly defined and managed. For example, objects must be created
  and destroyed at the right time to avoid memory leaks or other problems.
  Explicit state management: Application state must be explicitly managed and
  predictable. State changes should only occur through specific mechanisms
  and interfaces.

Explicit architecture helps create a clearer and more maintainable application
because developers can easily understand which components are responsible for
which functions and how they interact with each other.

![Lottery main](/www/back/public/images/readme/ExplicitArchitecture.png)

`Concept`:

- The `application core` is the most important thing to think about.
  This code allows to perform real actions in the system, that is,
  this IS our application. Several user interfaces (progressive web
  application, mobile application, CLI, API, etc.) can work with it,
  everything runs on a single core.
- Away from the most important core code are the `tools` that the
  application uses. For example, the database engine, search engine,
  web server and CLI console.
- The blocks of code that connect tools to the application core are
  called `adapters` (Ports & Adapters architecture). They allow business
  logic to interact with a specific tool, and vice versa.
- Adapters that tell an application to do something are called
  `primary or managing adapters`, while adapters that tell an application
  to do something are called `secondary or managed adapters`.
- However, these adapters are not created randomly, but to match a
  specific entry point into the application core, a `port`. A `port` is
  nothing more than a specification of how a tool can use the core of
  an application or vice versa. In most languages and in its simplest
  form this port would be an interface, but in fact it can be composed
  of several interfaces and DTOs.
- It is important to note that ports (interfaces) are
  `inside the business logic`, and `adapters are outside`.
- `Core` or `control adapters` wrap around a port and use it to tell the
  application core what to do. They transform all data from the delivery
  engine into method calls in the application core. In other words, our
  `control adapters` are `controllers` or `console commands`, they are injected
  into their constructor with some object, the class of which implements
  the interface (port) required by the controller or console command.
- `Managed adapters` implement a port, interface, and then are introduced
  into the application core where the port is required (specifying the type).

### INTERACTION BETWEEN COMPONENTS

#### MIDDLEWARE

In Symfony, middleware is often used to perform tasks such as authentication,
authorization, exception handling, etc. `Middleware` is usually classes or
functions that accept a request, perform the necessary actions, and pass
control to the next middleware or handler.

#### EVENT MODEL

The `event model` allows application components to interact with each other
through events. Components can emit events to which other components can
subscribe and react. This allows to create more flexible and extensible
applications. The `event model` allows to create loosely coupled components,
making application easier to maintain and extend.

### STANDARD DESIGN PATTERNS

- The `Model-View-Controller` pattern is used in all modern PHP frameworks.
  In an MVC application, separate code into three categories:
  `models` (business logic), `views` (presentation), and `controllers`
  (code responsible for user interaction). With MVC, can reuse the
  components of this triad in other projects. It is also easy to replace any
  part of the triad. For example, can easily replace a view with another
  view without changing the business logic.

- `Domain Driven Design` (DDD) in Symfony Framework, will be dividing the
  model layer even further into: `entities` - classes that work with database
  tables, `repositories` - classes that allow to get entities from the
  database, `value objects` - model classes, without an identifier, and
  `services` - that is, classes responsible for business logic.
  Additionally, will have web `forms` - model classes responsible for user
  input, `form helpers` in the form of `validators` and `filters`. You will have
  a view rendering strategy that determines what how the page will be rendered.
  By default, to get an HTML page, the `.phtml view template` is rendered using
  the `PhpRenderer` class, which lives in the Symfony\View\Renderer namespace.
  This strategy works well 99% of the time. But sometimes may need to render
  something other than the HTML page, for example, response in JSON format.
  View helpers, reusable plugins designed to display different content on a web
  page, and probably other types of models.

- `Aspect Oriented Design template` - everything in Symfony Framework is based
  on events. When a user requests a web page, an event is fired. An observer can
  respond to an event. `Observers` can be divided into listeners (listener) and
  `subscribers` (subscriber). This allows to expand the capabilities of the
  framework.

- The `CQRS (Command Query Responsibility Segregation)` template - offers separation
  of `write operations` (`commands`) and `read operations` (`queries`) in an application,
  which can improve performance, scalability, and simplify the data model. This approach
  helps separate the logic for changing application state from the logic for reading data,
  making the system more flexible and maintainable. `Message brokers`, such as `Apache Kafka`
  or `RabbitMQ` are used to provide asynchronous messaging between different system components.
  They can be used in architecture to implement asynchrony, decouple system components, and
  ensure reliable message delivery.

- The `Strategy` template - is just a class that encapsulates an algorithm. And
  can use different algorithms if certain conditions are triggered. For example,
  a renderer has several strategies for rendering a web page (for example, it can
  generate an HTML page, a JSON array, or an RSS feed based on the HTTP headers request).

- `Adapter` pattern - allows to tailor a general purpose class to a specific use
  case. Internally, it uses adapters for each supported DBMS (SQLite,
  MySQL, PostgreSQL, etc.).

- `Factory` pattern - can create an instance of a class using the new operator.
  Or can create it with a factory. A factory is just a class that creates other objects.
  Factories are useful because they make `dependency injection` easier. It also makes it
  `easier to test models and controllers`.

- The `Service Manager` template - is a centralized repository of all the services
  available in the application. Extract services from the service manager not anywhere
  in the code, but inside the factory (factory). When create an object, extract the
  services it depends on and pass those services (dependencies) to the object's constructor.
  This is also called `dependency injection`.

- `Singleton` pattern - each service in the centralized repository of all services
  available in the application exists in only one instance.

### SKELETON APPLICATION BY SYMFONY FRAMEWORK

#### INTRODUCTION

This is a skeleton application using the `Symfony Framework`:

* MVC layer and module systems.
* Security.
* Performance.
* Standard Design Patterns.
* Main Components.
* PHP Standards Recommendations (PSR).
* Principles of Clean Code (SOLID, DRY) and Clean Architecture in PHP.

#### ADVANTAGES

Using an application skeleton in `Symfony Framework` has several advantages:

- `Quick Start`: The application skeleton contains the minimum set of files
  and settings needed to run a Symfony application. This allows to start
  working with Symfony quickly and without unnecessary complexity.
- `Configuration Flexibility`: You can easily configure the application
  skeleton to suit needs by adding or removing Symfony components and bundles.
- `Development Standards`: The application skeleton follows Symfony's development
  standards, helping create applications that adhere to best development practices.
- `Convenient Updates`: When updating Symfony to a new version, the application
  skeleton is typically updated along with the framework, making the update process
  easier and safer.
- `Ease of Maintenance`: Using the application skeleton makes the code more
  understandable and easier to maintain for other developers, as it contains the
  minimum necessary components and settings.
- `Integration with Other Tools`: The application skeleton easily integrates
  with various development and build tools, such as Composer, Docker, CI/CD
  systems, and others.
- The `Model-View-Controller` (MVC) pattern used in Symfony Framework allows to
  implement `Domain Driven Design` (DDD) separate the business logic from
  the presentation layer, making `the code structure more consistent and manageable`.
- Instead of interacting directly with the database through SQL queries,
  using `Doctrine Object-Relational Mapping` (ORM, ODM) allows to `manage
  the structure and relationships of data` by accessing the database in
  an `object-oriented style`.
- Using an application skeleton helps speed up development, make the code more
  structured, and facilitate its maintenance and updates.

#### SECURITY

- The `input script` (index.php) - is the only PHP file available to
  web visitors. All other PHP scripts are outside the document root
  directory of the Apache web server. This is much safer than giving all
  visitors access to any of the PHP scripts.
- `Request Routing` (Routing) - allows to set `strict rules` for how an
  acceptable web page URL should look like. If the user enters an invalid
  URL into the browser's navigation bar, they are automatically directed
  to an error page.
- `Access control lists` (ACL) and `Role-Based Access Control` (RBAC) -
  allow to set `rules to allow` or `deny access` to specific resources on
  website.
- Web form `validators` - allow to be sure that harmful data entered
  by the user will not pass through the web form. `Validators` are used
  to make sure that the data submitted through a web form meet certain rules.
- `Cross-Site Request Forgery` (CSRF) form elements - used to prevent hacker attacks.
- Support for `cryptography` - allows to store important data, such
  as passwords, encrypted with strong cryptographic algorithms, which are
  difficult to crack.

#### PERFORMANCE

- `Lazy class autoloading` - classes are loaded only when needed.
- `Efficient loading of services and plugins` in Symfony Framework - business
  logic classes are instantiated only when really needed. This is achieved
  through the `service manager`, the central container for all the
  application's services.
- `Caching support` - PHP has several caching extensions (such as
  Redis) that can be used to speed up sites built with Symfony Framework.

#### MAIN BUNDLES

##### SYMFONY/FRAMEWORK BUNDLE

`Symfony Framework Bundle` is a core Symfony bundle that provides essential
features and configurations for Symfony applications.

Here are some of the key features and advantages of using FrameworkBundle:

- `Configuration`: FrameworkBundle provides a central configuration
  for Symfony application, including default configuration settings
  for routing, templating, and security.
- `Routing`: FrameworkBundle provides the routing system for Symfony
  applications, allowing to define URL patterns and map them to
  controller actions.
- `Templating`: The bundle integrates with the Twig templating engine,
  providing support for rendering templates in Symfony application.
- `Security`: FrameworkBundle provides a security system for Symfony
  applications, allowing to configure access control rules, authentication
  mechanisms, and more.
- `Error Handling`: The bundle provides default error pages and configuration
  options for handling errors in Symfony application.
- `Event Dispatcher`: FrameworkBundle includes the Symfony Event Dispatcher
  component, allowing to dispatch and listen for events in application.
- `Dependency Injection`: The bundle integrates with the Symfony Dependency
  Injection component, allowing to define and inject services into application.
- `Environment Configuration`: FrameworkBundle provides configuration options
  for defining different configurations based on the environment (e.g., development,
  production).
- `Console Commands`: The bundle provides support for defining and running
  console commands in Symfony application.
- `Community Support`: FrameworkBundle is a core Symfony bundle and is widely
  used in the Symfony community, ensuring that can find help and resources
  when using this bundle in application.

##### SENSIO/FRAMEWORK-EXTRA BUNDLE

`Sensio Framework Extra Bundle` is a Symfony bundle that provides additional
features and annotations to enhance the functionality of Symfony controllers.

Here are some of the key features and advantages of using Sensio Framework
Extra Bundle:

- `Annotations`: The bundle provides annotations that allow to define routing,
  security, templating, and other configurations directly in controller
  classes, making it easier to organize and manage application logic.
- `Routing`: You can use annotations to define routes for controller actions,
  eliminating the need for separate routing configuration files.
- `Template Configuration`: Annotations allow to specify the template to render
  for a controller action, simplifying the process of rendering views in application.
- `Security Configuration`: Annotations can be used to configure security settings
  for controller actions, such as access control rules and authentication requirements.
- `ParamConverter`: The bundle provides annotations for automatically converting
  request parameters into objects, making it easier to work with request data in
  controller actions.
- `Cache Configuration`: Annotations can be used to configure caching settings
  for controller actions, allowing to control how responses are cached by
  the Symfony cache system.
- `Form Configuration`: Annotations provide a convenient way to configure forms
  in Symfony controllers, making it easier to create and handle forms in application.
- `Community Support`: SensioFrameworkExtraBundle is widely used in the Symfony
  community and has a supportive community, ensuring that can find help and
  resources when using this bundle in application.

##### SYMFONY/MONOLOG BUNDLE

`Symfony/monolog bundle` is a Symfony bundle that integrates the
Monolog logging library into Symfony applications. `Monolog` is a
popular PHP logging library that provides powerful logging capabilities.

Here are some advantages of using Symfony/monolog-bundle:

- `Flexible Configuration`: The bundle allows to configure logging
  channels, handlers, and formatters using Symfony's configuration system,
  providing flexibility in how configure logging in application.
- `Integration with Symfony`: Symfony/monolog-bundle integrates seamlessly
  with Symfony, allowing to access and configure Monolog from
  Symfony application's configuration files.
- `Multiple Log Handlers`: Monolog supports multiple log handlers, such as
  stream handlers, rotating file handlers, syslog handlers, and more, allowing
  to log to different destinations based on requirements.
- `Logging Levels`: Monolog supports logging at different levels (e.g., DEBUG,
  INFO, WARNING, ERROR, CRITICAL), allowing to control the verbosity of logs.
- `Contextual Logging`: Monolog supports adding contextual information to log
  messages, such as the current user, request ID, or any other relevant information,
  making it easier to debug issues in application.
- `Log Filtering`: Monolog allows to filter log messages based on various
  criteria, such as the logging level or the log message content, allowing
  to control which messages are logged.
- `Community Support`: Symfony/monolog-bundle is widely used in the Symfony
  community and has a supportive community, ensuring that can find help
  and resources when using this bundle in application.

##### SYMFONY/SECURITY BUNDLE

`Symfony/Security Bundle` is a Symfony bundle that provides security
features for Symfony applications. It integrates with Symfony's
security component to provide authentication, authorization, and
other security-related features.

Here are some advantages of using Symfony/SecurityBundle:

- `Authentication`: The bundle provides authentication mechanisms,
  such as form-based authentication, HTTP basic authentication, and more,
  allowing to secure application's endpoints.
- `Authorization`: Symfony/SecurityBundle allows to define access
  control rules based on roles and attributes, giving fine-grained
  control over who can access specific parts of application.
- `Firewalls`: The bundle allows to configure firewalls to protect
  different parts of application, allowing to define different
  security policies based on the request path or other criteria.
- `User Providers`: Symfony/SecurityBundle provides user providers for
  loading user information from different sources, such as a database or
  LDAP server, allowing to authenticate users against different backends.
- `Security Voters`: The bundle provides security voters for implementing
  custom authorization logic, allowing to define complex access control
  rules based on application's requirements.
- `Encryption`: Symfony/SecurityBundle provides tools for encrypting and
  hashing passwords, ensuring that user passwords are stored securely in
  application.
- `Community Support`: Symfony/SecurityBundle is widely used in the Symfony
  community and has a supportive community, ensuring that can find help
  and resources when using this bundle in application.

##### SYMFONY/TWIG BUNDLE

`Symfony/Twig Bundle` is a Symfony bundle that provides integration with
the Twig templating engine. Twig is a flexible, fast, and secure template
engine for PHP.

Here are some advantages of using Symfony/Twig Bundle:

- `Template Inheritance`: Twig supports template inheritance, allowing
  to define a base template with common layout and structure, and then extend
  or override specific blocks in child templates.
- `Powerful Syntax`: Twig provides a powerful and expressive syntax for
  writing templates, including support for variables, filters, functions,
  loops, and conditionals, making it easy to generate dynamic content.
- `Automatic HTML Escaping`: Twig automatically escapes output by default,
  helping to prevent XSS (Cross-Site Scripting) attacks and ensuring that
  application is secure.
- `Extensibility`: Twig is highly extensible, allowing to define custom
  filters, functions, and tags to extend its functionality and tailor it to
  application's needs.
- `Integration with Symfony`: Symfony/Twig Bundle integrates seamlessly with
  Symfony, providing easy configuration and access to Symfony services and
  components from Twig templates.
- `Internationalization and Localization`: Twig provides built-in support
  for internationalization (i18n) and localization (l10n), making it easy
  to create multilingual templates.
- `Debugging Tools`: Twig provides helpful debugging tools, such as the dump()
  function, which allows to inspect variables and objects in templates.
- `Community Support`: Symfony/Twig Bundle is widely used in the Symfony community
  and has a supportive community, ensuring that can find help and
  resources when using this bundle in application.

##### SYMFONY/WEBPACK-ENCORE BUNDLE

`Symfony/WebpackEncore Bundle` is a Symfony bundle that provides integration
with Webpack Encore, a tool for managing JavaScript and CSS assets in Symfony
applications.

Here are some advantages of using `Symfony/WebpackEncore Bundle`:

- `Modern JavaScript and CSS Workflow`: Webpack Encore allows to use modern
  JavaScript (ES6+) and CSS (Sass, Less) syntax in Symfony applications,
  and provides tools for compiling and optimizing these assets for production.
- `Asset Versioning and Cache Busting`: Webpack Encore automatically generates
  unique file names for assets based on their content, allowing to
  easily implement cache busting to ensure that users always get the latest
  version of assets.
- `Code Splitting`: Webpack Encore supports code splitting, allowing to
  split JavaScript code into smaller chunks that can be loaded
  asynchronously, improving page load times.
- `Asset Optimization`: Webpack Encore provides tools for optimizing
  assets, such as minification, concatenation, and tree shaking, reducing
  the size of assets and improving page load times.
- `Hot Module Replacement (HMR)`: Webpack Encore supports HMR, allowing
  to see changes to JavaScript and CSS assets in real-time without
  refreshing the page during development.
- `Integration with Symfony`: Symfony/WebpackEncore Bundle integrates seamlessly
  with Symfony, providing easy configuration and access to Webpack Encore's
  features from within Symfony application.
- `Community Support`: Symfony/WebpackEncore Bundle is widely used in the Symfony
  community and has a supportive community, ensuring that can find help and
  resources when using this bundle in application.

##### SYMFONY/WEB-PROFILER BUNDLE

`Symfony/Web Profiler Bundle` is a Symfony bundle that provides the web
profiler toolbar and the profiler interface, which are powerful
debugging and profiling tools for Symfony applications.

Here are some advantages of using Symfony/WebProfilerBundle:

- `Debugging Toolbar`: The bundle provides a debugging toolbar that
  appears at the bottom of web pages when Symfony is in debug mode.
  The toolbar displays useful information about the current request,
  including routing information, controller details, database queries,
  and more.
- `Profiler Interface`: Symfony/WebProfilerBundle also provides a profiler
  interface that allows to inspect detailed information about each
  request, including timeline information, memory usage, and executed
  queries. This can be invaluable for debugging performance issues in
  application.
- `Twig Extensions`: The bundle provides Twig extensions that allow
  to easily debug and profile Twig templates, including displaying
  the rendering time and the template hierarchy.
- `Integration with Other Symfony Bundles`: Symfony/WebProfilerBundle
  integrates seamlessly with other Symfony bundles, such as DoctrineBundle
  and SwiftmailerBundle, providing additional debugging and profiling
  information for these bundles.
- `Security`: The bundle includes security features to prevent unauthorized
  access to the profiler interface, ensuring that sensitive information
  is protected.
- `Community Support`: Symfony/WebProfilerBundle is widely used in the
  Symfony community and has a supportive community, ensuring that
  can find help and resources when using this bundle in application.

##### TWIG/EXTRA-BUNDLE BUNDLE

`TwigExtra Bundle` is not a standard Symfony bundle, so it's possible
that might be referring to a different bundle.

However, if looking for a bundle that provides additional functionality for Twig
templates in Symfony applications, might be interested in the TwigExtra library.

Here are some potential features and advantages of using such a bundle:

- `Additional Twig Functions`: The bundle could provide additional Twig
  functions that extend the functionality of the core Twig library, allowing
  to perform more complex operations in templates.
- `Custom Twig Tags`: It could also provide custom Twig tags that enable
  to create custom syntax in templates, making them more expressive
  and easier to work with.
- `Template Includes`: The bundle might include pre-built templates or template
  parts that can easily include in own templates, saving time
  and effort in creating common elements.
- `Template Extensions`: It could provide Twig extensions that add new filters,
  functions, or tests to Twig, allowing to extend Twig's capabilities
  according to needs.
- `Integration with Symfony`: If the bundle is specifically designed for Symfony,
  it could provide integration with Symfony services and components, making
  it easier to use Symfony features in Twig templates.
- `Community Support`: If the bundle is widely used in the Symfony community,
  can expect to find support and resources from other Symfony developers,
  making it easier to learn and use the bundle in projects.

##### VICH/UPLOADER BUNDLE

`VichUploader Bundle` is a Symfony bundle that simplifies file uploads
and management in Symfony applications.

Here are some advantages of using VichUploaderBundle:

- `Easy File Uploads`: VichUploaderBundle provides an easy way to handle
  file uploads in Symfony, including handling file uploads in forms and
  persisting files to the filesystem or a cloud storage service.
- `Mapping Annotations`: The bundle allows to use mapping annotations
  to configure how files are stored and managed, providing flexibility
  in how files are handled in application.
- `Integration with Doctrine`: VichUploaderBundle integrates seamlessly
  with Doctrine ORM, allowing to associate uploaded files with
  Doctrine entities and manage them using Doctrine's ORM features.
- `File Naming Strategies`: The bundle provides various file naming
  strategies, allowing to customize how uploaded files are named
  and organized on the filesystem.
- `File Size and Type Validation`: VichUploaderBundle provides built-in
  support for validating file size and type, ensuring that only valid
  files are uploaded to application.
- `Integration with Symfony Forms`: The bundle integrates with Symfony
  forms, allowing to easily create file upload forms and handle
  file uploads in controllers.
- `Community Support`: VichUploaderBundle is widely used in the Symfony
  community and has a supportive community, ensuring that can find
  help and resources when using this bundle in application.

##### EASYADMIN BUNDLE

Here are some advantages of using EasyAdmin Bundle:

- `Easy Installation and Usage`: EasyAdmin Bundle integrates easily
  into Symfony applications and allows to quickly create admin
  interfaces without the need to write a lot of code.
- `Configurability`: You can easily configure the admin interfaces
  using configuration files, allowing to quickly adapt the
  interfaces to needs.
- `Flexibility and Extensibility`: EasyAdmin Bundle provides many
  options for customizing and extending the functionality of admin
  interfaces, such as filtering, sorting, pagination, and more.
- `Integration with Doctrine`: EasyAdmin Bundle integrates directly
  with Doctrine, making it easy to manage entities through the
  admin interfaces.
- `Automatic Interface Generation`: EasyAdmin Bundle can automatically
  generate admin interfaces based on Doctrine entities, greatly
  simplifying the process of creating admin interfaces.
- `Support for Various Field Types`: EasyAdmin Bundle supports various
  field types, such as text fields, dropdown lists, checkboxes, and others,
  allowing to create diverse admin interfaces.
- `Automatic Interface Updates`: EasyAdmin Bundle automatically updates
  admin interfaces when Doctrine entities change, avoiding the need for
  manual interface updates.

##### DOCTRINE BUNDLE

Using `Doctrine Bundle` in Symfony has several advantages:

- `ORM Integration`: Doctrine Bundle provides seamless integration with
  Doctrine ORM (Object-Relational Mapping), allowing to work with
  databases using PHP objects and abstracting away the complexities of
  SQL queries.
- `Database Abstraction`: You can work with multiple database systems
  (MySQL, PostgreSQL, SQLite, etc.) without changing code, thanks
  to Doctrine's database abstraction layer.
- `Entity Management`: Doctrine Bundle simplifies entity management,
  including CRUD operations (Create, Read, Update, Delete), relationships
  between entities, and data validation.
- `Query Language`: Doctrine Query Language (DQL) allows to write
  database queries using a familiar object-oriented syntax, making it
  easier to work with complex queries.
- `Schema Migration`: Doctrine Bundle provides tools for managing database
  schema changes, allowing to easily update database schema as
  application evolves.
- `Performance Optimization`: Doctrine includes features like lazy loading,
  caching, and query optimization, helping improve the performance of
  application.
- `Symfony Integration`: Doctrine Bundle is designed to work seamlessly
  with Symfony, providing easy configuration and integration with Symfony's
  services and components.
- `Community Support`: Doctrine is widely used in the Symfony community and
  has active development and support, ensuring that can find help and
  resources when needed.

##### DOCTRINE FIXTURES BUNDLE

`Doctrine Fixtures Bundle` is a Symfony bundle that provides tools for
loading test data into database for testing and development purposes.

Here are some advantages of using DoctrineFixturesBundle:

- `Data Fixtures`: The bundle allows to define "fixtures" which are PHP
  classes that contain the data want to load into database.
  Fixtures can be used to populate database with test data for
  testing and development.
- `Fixture Groups`: DoctrineFixturesBundle supports grouping fixtures,
  allowing to load only the fixtures need for a specific test
  case or scenario.
- `Ordering Fixtures`: You can define the order in which fixtures are
  loaded, ensuring that dependencies between fixtures are handled correctly.
- `Dependency Injection`: The bundle integrates with Symfony's dependency
  injection container, allowing to inject services into fixtures
  if needed.
- `ORM Support`: DoctrineFixturesBundle works with Doctrine ORM, allowing
  to easily load fixtures into database using Doctrine's ORM features.
- `Command Line Interface (CLI)`: The bundle provides a CLI command for
  loading fixtures, making it easy to load fixtures from the command line.
- `Community Support`: DoctrineFixturesBundle is widely used in the Symfony
  community and has a supportive community, ensuring that can find help
  and resources when using this bundle in application.

##### JWT-REFRESH-TOKEN BUNDLE

`JWT Refresh Token Bundle` is a Symfony bundle that provides functionality
for handling JWT (JSON Web Token) refresh tokens in Symfony applications.

Here are some advantages of using this bundle:

- `Secure Token Refreshing`: JWT refresh tokens allow users to obtain new
  access tokens without needing to re-enter their credentials, providing a
  more seamless and secure authentication experience.
- `Token Expiry Management`: The bundle manages the expiration of refresh
  tokens, ensuring that they are valid for a limited time and automatically
  issuing new refresh tokens when needed.
- `Reduced Server Load`: JWT refresh tokens can reduce the load on server
  by reducing the frequency of authentication requests, as users can obtain
  new access tokens without involving the server.
- `Improved User Experience`: With JWT refresh tokens, users can stay
  authenticated for longer periods without needing to log in again, improving
  the overall user experience of application.
- `Integration with Symfony Security`: The bundle integrates seamlessly with
  Symfony Security, allowing to easily configure token refresh behavior
  and integrate it into authentication workflow.
- `Customization Options`: JWT Refresh Token Bundle provides various customization
  options, allowing to tailor the token refresh behavior to fit
  application's specific requirements.
- `Community Support`: The bundle is actively maintained and has a supportive
  community, ensuring that can find help and resources when integrating JWT
  refresh tokens into Symfony application.

##### JWT-AUTHENTICATION BUNDLE

`JWT Authentication Bundle` is a Symfony bundle that provides JWT
(JSON Web Token) authentication functionality for Symfony applications.

Here are some advantages of using this bundle:

- `Stateless Authentication`: JWT authentication is stateless, meaning
  the server does not need to store session information for authenticated
  users, leading to better scalability and performance.
- `Secure Communication`: JWT uses digital signatures to verify the
  authenticity of the tokens, ensuring that the information exchanged
  between the client and server is secure and cannot be tampered with.
- `Single Sign-On (SSO)`: JWT tokens can be used for single sign-on across
  multiple applications, allowing users to authenticate once and access
  multiple services without needing to log in again.
- `Customizable Tokens`: JWT tokens can contain custom claims, allowing
  to include additional information about the user or session in the
  token payload.
- `Expiration and Refresh Tokens`: JWT tokens can have expiration times,
  after which they are no longer valid. Additionally, JWT Authentication
  Bundle can support refresh tokens, allowing users to obtain new tokens
  without needing to log in again.
- `Integration with Symfony Security`: The bundle integrates seamlessly
  with Symfony Security, allowing to configure JWT authentication as
  part of application's security configuration.
- `Community Support`: JWT Authentication Bundle is actively maintained
  and has a supportive community, ensuring that can find help and
  resources when integrating JWT authentication into Symfony application.

##### KNP-PAGINATOR BUNDLE

`KnpPaginator Bundle` is a Symfony bundle that provides pagination
functionality for Doctrine ORM queries in Symfony applications.

Here are some advantages of using this bundle:

- `Efficient Pagination`: KnpPaginatorBundle allows to paginate
  large sets of data efficiently, by fetching only the data needed
  for the current page.
- `Easy Integration`: The bundle integrates seamlessly with Symfony
  and Doctrine ORM, making it easy to add pagination to Symfony
  applications.
- `Customizable Pagination Templates`: You can customize the pagination
  templates to match the design of application, providing a consistent
  user experience.
- `Flexible Configuration`: KnpPaginatorBundle provides flexible configuration
  options, allowing to customize the pagination behavior to fit
  application's specific requirements.
- `Support for AJAX Pagination`: The bundle supports AJAX-based pagination,
  allowing to load paginated data asynchronously without reloading the
  entire page.
- `Community Support`: KnpPaginatorBundle is actively maintained and has a
  supportive community, ensuring that can find help and resources when
  integrating pagination into Symfony application.

##### CORS BUNDLE

`Cors Bundle` is a Symfony bundle that provides Cross-Origin Resource
Sharing (CORS) support for Symfony applications. CORS is a mechanism
that allows resources on a web page to be requested from another domain
outside the domain from which the resource originated.

Here are some advantages of using CorsBundle:

- `Cross-Domain Requests`: CorsBundle allows to configure CORS settings
  to control which domains are allowed to make cross-origin requests to
  Symfony application.
- `Security`: By configuring CORS settings, can prevent unauthorized
  cross-origin requests, improving the security of application.
- `Flexibility`: CorsBundle provides flexible configuration options, allowing
  to customize CORS settings based on application's specific requirements.
- `Integration with Symfony Security`: The bundle integrates seamlessly with
  Symfony Security, allowing to apply CORS settings based on the user's
  authentication status or role.
- `Community Support`: CorsBundle is actively maintained and has a supportive
  community, ensuring that can find help and resources when integrating
  CORS support into Symfony application.

##### PHPUNIT/PHPUNIT TESTING FRAMEWORK

`PHPUnit` is a standalone PHP testing framework.
Integrate PHPUnit into Symfony projects to write and run unit tests
for Symfony applications.

PHPUnit is a popular testing framework for PHP that provides a wide
range of features for writing and running unit tests, making it an
essential tool for PHP developers.

Here are some advantages of using PHPUnit:

- `Easy to Use`: PHPUnit is easy to learn and use, even for beginners. It
  provides a simple and intuitive syntax for writing tests, making it easy
  to write and maintain tests for PHP code.
- `Comprehensive Assertions`: PHPUnit provides a wide range of assertion
  methods for verifying the behavior of code. These assertions cover
  a variety of scenarios, including checking values, comparing arrays,
  and verifying exceptions.
- `Fixture Management`: PHPUnit provides features for managing test fixtures,
  allowing to set up and tear down the environment for tests.
  This makes it easy to create isolated and repeatable tests.
- `Test Suite Management`: PHPUnit allows to organize tests into
  test suites, making it easy to run groups of tests together. This can
  be useful for organizing tests by module or feature.
- `Mocking and Stubbing`: PHPUnit provides features for creating mock objects
  and stubs, allowing to simulate complex dependencies in tests.
  This can help isolate the code are testing and make tests
  more focused.
- `Code Coverage Analysis`: PHPUnit can generate code coverage reports,
  showing which parts of code are covered by tests.
  This can help identify areas of code that are not adequately
  tested.
- `Integration with Continuous Integration (CI)`: PHPUnit integrates
  seamlessly with CI tools like Jenkins, Travis CI, and GitHub Actions,
  allowing to automate the execution of tests and ensure that
  code remains stable.
- `Active Development`: PHPUnit is actively developed and maintained,
  with frequent updates and new features being added. This ensures
  that have access to the latest tools and techniques for writing tests.

##### API-PLATFORM/CORE FRAMEWORK

`Api-platform/core` is a powerful PHP framework for building APIs
using the JSON-LD, Hydra, and GraphQL standards.

Here are some advantages of using `api-platform/core`:

- `Rapid Development`: Api-platform/core provides a set of tools and
  conventions that allow to quickly build and deploy APIs. It comes
  with built-in support for common API features such as pagination,
  filtering, and sorting.
- `Flexibility`: Api-platform/core is highly flexible and customizable.
  You can easily extend and customize its functionality to meet
  specific requirements.
- `Standard Compliance`: Api-platform/core is built on top of standards
  such as JSON-LD, Hydra, and GraphQL, ensuring that APIs are
  compliant with industry standards and interoperable with other systems.
- `Automatic Documentation`: Api-platform/core automatically generates
  API documentation based on API's schema, making it easy for
  developers to understand and use API.
- `Data Validation`: Api-platform/core provides built-in support for data
  validation, ensuring that only valid data is accepted by API.
- `Security`: Api-platform/core provides built-in security features such
  as authentication and authorization, allowing to secure API
  with ease.
- `Performance`: Api-platform/core is designed to be highly performant,
  with features such as caching and lazy loading to optimize the
  performance of API.
- `Community Support`: Api-platform/core has a large and active community
  of developers who contribute to its development and provide support,
  documentation, and resources.

Overall, `api-platform/core` offers several advantages for building APIs,
including rapid development, flexibility, standard compliance, automatic
documentation, data validation, security, performance, and community
support. It is a powerful framework that can help build robust and
scalable APIs with ease.

##### WEBONYX/GRAPHQL-PHP

`webonyx/graphql-php` is a PHP implementation of the `GraphQL specification`,
which is a query language for APIs.

Here are some advantages of using webonyx/graphql-php:

- `GraphQL Support`: webonyx/graphql-php fully supports the GraphQL specification,
  allowing to easily create GraphQL APIs in PHP.
- `Flexibility`: GraphQL allows clients to request only the data they need, making
  it more flexible than traditional REST APIs. webonyx/graphql-php makes it easy
  to define and execute GraphQL queries.
- `Efficiency`: Because clients can request only the data they need, GraphQL APIs
  can be more efficient than REST APIs, reducing the amount of data transferred
  over the network.
- `Type Safety`: GraphQL APIs are strongly typed, meaning that the types of data
  returned by the API are explicitly defined. webonyx/graphql-php provides tools
  for defining these types and ensuring type safety in API.
- `Code Generation`: webonyx/graphql-php can generate PHP classes based on
  GraphQL schema, making it easier to work with API in PHP code.
- `Community Support`: webonyx/graphql-php is actively maintained and has a growing
  community of developers who contribute to its development and provide support,
  documentation, and resources.

Overall, `webonyx/graphql-php` offers several advantages for building GraphQL APIs
in PHP, including flexibility, efficiency, type safety, code generation, and
community support. It is a powerful tool for creating modern, flexible APIs that
can meet the needs of today's applications.

#### MAIN COMPONENTS

- `Symfony\amqp-messenger` and `Symfony\doctrine-messenger` - The `Messenger
  component` manages asynchronous code in Symfony. Are packages that provides
  integration between Symfony Messenger and `AMQP` (`Advanced Message Queuing Protocol`).
  Symfony Messenger is a Symfony component that provides asynchronous message
  processing within an application.
- `symfony/workflow` - is a Symfony component that provides tools for managing
  workflow within an application. A workflow is a sequence of steps that define
  the order in which a task or business process is performed. Makes it easier to
  create and manage complex business processes in Symfony applications, making
  them more flexible and easier to maintain.
- `imagine/imagine` - is a PHP library for working with images. It provides convenient
  methods for creating, modifying and manipulating images. Is a powerful tool for
  working with images in PHP and can be useful for a variety of applications,
  including processing images on websites, creating thumbnails, generating images
  for social networks and much more.
- `symfony/notifier` - is a Symfony component that provides a convenient way to
  send notifications through various channels such as email, SMS, Slack, Telegram and others.
- `symfony/slack-notifier` - this is an add-on package for Symfony that provides
  Slack integration for sending notifications. It is part of Symfony Notifier, which
  makes it easy to send notifications through various channels.

### PRINCIPLES OF CLEAN ARCHITECTURE

#### MEASURE OF DESIGN QUALITY

A `measure of design quality` can be a simple measure of the labor required
to satisfy the client's needs.

If labor costs are low and remain low over the life of the system, the
system is `well-designed`.

If the effort increases with each new version, the system is `poorly designed`.

#### SOLID

The `Clean Architecture Principles` proposed by Robert Martin (also known
as the `SOLID principles`) are a set of guidelines and practices aimed at
creating software systems that are flexible, extensible, and easily maintainable.

Here are the basic principles of clean architecture:

- `Single Responsibility Principle` (`SRP`): Each module or class should be responsible
  for only one piece of functionality. This makes the code easier to understand,
  test, and change.
- `Open/Closed Principle` (`OCP`): Software entities should be open for extension but
  closed for modification. This is achieved through the use of abstractions and interfaces.
- `Barbara Liskov Substitution Principle` (`LSP`): Objects in a program must be
  replaceable instances of their underlying types without affecting the correctness
  of the program. This allows to use polymorphism to simplify code.
- `Interface Segregation Principle` (`ISP`): Clients should not depend on interfaces they
  do not use. Interfaces should be small and client-specific.
- `Dependency Inversion Principle` (`DIP`): Upper level modules should not depend on
  lower level modules. Both types of modules must depend on abstractions. Details should
  depend on abstractions, not the other way around.

These principles help create a flexible and extensible architecture that is easy
to maintain and test.

#### DRY

The `DRY principle` (`Don't Repeat Yourself`) is a software design principle
that states that every piece of knowledge or logic in a system should have
a single, consistent representation within that system. This means that
repeated sections of code should be moved to separate components or functions,
to avoid code duplication and make it easier to maintain and change.

The `DRY principle` can be applied at different levels of an application's
architecture, including the code level, the module level, and the overall
system level. For example, at the code level, this could mean separating
common functionality into a separate function or class method,
to avoid repeating the same code in different parts of the application.

Applying the `DRY principle` helps reduce the likelihood of errors, makes
code easier to understand and maintain, and helps developers use their
time more efficiently.

#### KISS

`KISS` (`Keep It Simple, Stupid`): The principle states that design should
be as simple and clear as possible. Complexity should only arise from the
need to solve real problems, not from unnecessary design complexity.

#### YAGNI

`YAGNI` (`You Aren't Gonna Need It`): This principle states that functionality
that is not currently needed should not be included in a program. It's better
to add new functionality only when it's really needed.

#### LAW OF DEMETER

The `Law of Demeter` is also known as the `Principle of Least Knowledge` or
the `Principle of Least Surprise`, and is often considered one of the
principles of clean architecture. This principle states that an object
should only interact with objects that are directly related to it,
and not with objects who are "far" from him.

Applying the `Law of Demeter` helps reduce coupling between system
components, making code more flexible and maintainable. In addition,
this principle helps create clearer and more predictable program
behavior, since objects do not depend on the implementation details
of other objects.

### ARCHITECTURE TESTING

`Architecture testing` involves verifying that a system's architecture
matches its design and requirements. The purpose of such testing is to
ensure that the architecture supports the required performance, scalability,
reliability and security of the system.

The following approaches and methods can be used as part of architecture testing:

- `Static code analysis`: Using static analysis tools to verify that code adheres to
  clean architecture principles and to identify potential problems in the architecture.
- `Architectural Reviews`: Conduct regular system architecture reviews with architects
  and other stakeholders to identify and resolve design issues.
- `Modeling`: Using architectural modeling to visualize and analyze a system's architecture,
  allowing potential problems to be identified and design to be improved.
- `Performance Testing`: Conducting performance tests to evaluate system architecture
  compliance with performance requirements and identify bottlenecks.
- `Scalability Testing`: Conducting scalability tests to evaluate the ability of a system
  architecture to scale as load increases.
- `Reliability Testing`: Conducting reliability tests to evaluate the ability of a system
  architecture to handle errors and failures.
- `Security Testing`: Conducting security tests to evaluate system architecture compliance
  with security requirements and identifying vulnerabilities.
  `Architectural testing` helps ensure high quality and reliability of the system, as well
  as improve the development process and reduce development risks.

### SCALABILITY AND PERFORMANCE

#### SCALABILITY

`Scalability of an architecture` means its ability to efficiently handle
growth in workload or data volume.

To achieve scalability, the following approaches are used:

- `Vertical scaling` (`Scaling Up`): Increasing server power (for example, adding
  processors, memory) to improve performance. Symfony and PHP typically scale
  well vertically through optimizations to code and server configuration.
- `Horizontal scaling` (`Scaling Out`): Adding additional servers to distribute the load.
  For horizontal scalability, Symfony can be configured to work with load balancers and caches,
  such as Redis or Memcached.
- `Asynchronous processing`: Using asynchronous processes to process tasks that do not
  require an immediate response. For example, message queues (e.g. RabbitMQ, Kafka)
  and queue processors can be used to handle long-running operations such as generating
  reports or sending notifications.

#### PERFORMANCE

The `performance of the architecture` is determined by the speed and efficiency
of query processing.

To ensure high performance, the following approaches are used:

- `Optimizing database queries`: Using indexes, optimizing table structure, query
  caching to reduce the load on the database.
- `Caching`: Uses caching to store frequently accessed data and query results, thereby
  reducing query processing time.
- `Code optimization`: Improving algorithms, reducing the number of database queries,
  avoiding redundant calculations and operations.
- `Using CDN`: Using Content Delivery Network (CDN) to quickly deliver static resources
  such as images, CSS and JavaScript files.

### SAFETY

#### ATTACK PROTECTION

To `ensure protection` against attacks, the following measures are used:

- `Injection Protection`: Using `Prepared Statements` or ORM (Object-Relational Mapping)
  to work with the database to prevent `SQL injections`.
- `Protection against cross-site attacks` (`XSS`): Filtering and escaping data
  before output to the page, Using `Content Security Policy` (`CSP`) to limit where
  resources are downloaded.
- `Cross-site request forgery` (`CSRF`) protection: Use CSRF tokens to protect against
  request forgery.
- `Protection against information leaks`: Awareness of possible sources of information
  leaks and application of appropriate protective measures, such as the use of `HTTPS`,
  restricting access to confidential information.
- `Authentication and Authorization`: Use secure authentication methods (e.g. `OAuth`, `JWT`)
  and authorization to control access to resources.

#### DATA ENCRYPTION

The following measures are used to ensure data security:

- `Encrypt data at res`t: Use encryption protocols such as `TLS/SSL` to
  protect data during transmission between client and server.
- `Password Hashing`: Storing passwords as hashes using cryptographic hash
  functions such as `bcrypt` or `Argon2`. to protect against password leaks in
  the event of a database compromise.
- `Encrypt sensitive data`: Encrypt sensitive data on the server side before
  saving to the database and decrypt only when necessary.

### LOGGING AND MONITORING

#### GENERAL

`Logging and monitoring` play an important role in ensuring security,
monitoring performance, and identifying problems in the application
architecture.

#### LOGGING

The following approaches are used for `logging`:

- `Centralized logging`: Using special services or tools for centralized
  collection and analysis of application logs, such as `ELK Stack`
  (Elasticsearch, Logstash, Kibana) or `Splunk`.
- `Logging Levels`: Using different logging levels (e.g. DEBUG, INFO, WARNING,
  ERROR, CRITICAL) for various message types to facilitate log analysis.
- `Contextual logging`: Add contextual information to logging messages
  (eg request ID, user information) to make logs easier to track and analyze.
  Exception logging: Recording exceptions and errors in logs for their
  subsequent analysis and resolution.

#### MONITORING

To `monitor` this application, can use the following software:

- `Performance monitoring software`: For example, `New Relic` or `Datadog`,
  which provide tools to track application performance, analyze server
  response time, resource utilization and performance optimization.
- `Availability monitoring software`: For example, `Pingdom` or `UptimeRobot`,
  which can check the availability of web application, send alerts
  in case of failures, and provide availability statistics.
- `Security monitoring software`: For example, `OWASP ZAP` or `Snort`, which
  provide monitoring of network activity, detection of attacks and web
  application vulnerabilities.
- `Logging and log analysis systems`: For example, `ELK Stack` (Elasticsearch,
  Logstash, Kibana) or `Graylog`, which allow to centrally collect,
  store and analyze application logs to identify problems and analyze
  application operation.
- `PHP performance monitoring tools`: For example, `Blackfire.io` or `Xdebug`,
  which help profile and analyze the performance of PHP code to optimize
  and improve performance.

These software tools can help provide reliable `monitoring` and control
over the performance, availability and security of web application,
allowing to quickly respond to problems and provide a high-quality
user experience.

### LOCALIZATION

#### ADVANTAGES

`Localizing` an application in Symfony has a number of advantages that make
this process more efficient and convenient:

* `Integration with Symfony Translator`: Symfony provides powerful tools for
  working with translations through Symfony Translator. This component makes it
  easy to manage translations in the application and use them in code and templates.
* `Multilingual`: Symfony makes it easy to add support for multiple languages in
  an application. This makes the application accessible to audiences from different
  countries and cultures.
* `Ease of adding new translations`: Adding new translations or updating existing
  ones can be done without changing the application code. This simplifies the process
  of localizing and updating translations.
* `Separating translations from code`: Translations are separated into separate files,
  making them easy to manage and change without changing the main application code.
* `Formatting and Variable Support`: Symfony Translator supports translation formatting
  and variable insertion, which allows to create more dynamic and adaptive translations.
* `Flexible and Customizable`: Symfony allows to customize many aspects of localization,
  such as default language selection, translation file paths, and other settings, making it a
  flexible tool for app localization.

These advantages make Symfony an excellent choice for app localization, providing convenience,
flexibility, and efficiency when working with translations and multilingual content.

`Internationalization` (`i18n`) and `localization` (`l10n`) have been available out of the
box for a very long time. Localizing an application involves not only translating the
interface, but also handling plural forms, date and currency formatting, URLs, etc.

### API FOR EXPANDING FUNCTIONALITY

To provide the ability to expand the functionality to this application via the `API`,
can use the following approaches and examples:

- `RESTful API`: Creating a RESTful API to interact with the application using standard
  HTTP methods (GET, POST, PUT, DELETE) and data formats (JSON, XML). For example,
  API for managing lottery draws, creating and editing tickets, and obtaining statistics
  about the draws.
- `GraphQL API`: Use the GraphQL API to query data more flexibly and get only the information
  need. For example, an API for obtaining information about users, their tickets and
  participation in drawings.
- `Authorization and Authentication`: Ensure secure access to APIs using authorization and
  authentication mechanisms such as `OAuth 2.0` or `JWT`. For example, the API requires an
  access token to be passed in order to perform protected operations.
- `API Documentation`: Provides detailed API documentation for easy use by developers. For example,
  Using `Swagger` or `OpenAPI Specification` to describe and document API methods and parameters.
- `Integration with other services`: Providing the ability to integrate with other external
  services and applications via API. For example, integration with payment systems to accept
  payments for participation in sweepstakes.

These examples will help ensure that application's functionality is extensible through the `API`,
which will allow to easily integrate new features and improve its functionality.

## TECHNOLOGIES

### BACKEND TECHNOLOGY

#### DATABASES

##### DB MYSQL

###### ADVANTAGES

`DB MySql` - it is the main relational database that stores all application
data in the appropriate tables.

MySQL is a popular open-source relational database management system
(RDBMS) that is widely used for web applications.

Here are some advantages of using MySQL:

- `Ease of Use`: MySQL is known for its ease of use and simplicity.
  It has a straightforward setup process and a user-friendly interface,
  making it easy for developers to work with.
- `Speed`: MySQL is optimized for speed, making it a good choice for
  applications that require fast data retrieval and processing. It uses
  various techniques such as indexing and caching to improve performance.
- `Scalability`: MySQL can handle large amounts of data and can be easily
  scaled to accommodate growing workloads. It supports replication,
  clustering, and partitioning, allowing to scale database as
  application grows.
- `Reliability`: MySQL is known for its reliability and stability. It has a
  proven track record of being used in production environments and is
  backed by a large community of developers who contribute to its
  development and maintenance.
- `Security`: MySQL provides robust security features to protect data.
  It supports encryption, authentication, and access control mechanisms
  to ensure that data is secure.
- `Flexibility`: MySQL supports various data types and storage engines,
  giving the flexibility to choose the best storage option for
  application. It also supports a wide range of platforms, making it
  suitable for different environments.
- `Compatibility`: MySQL is compatible with many operating systems and
  programming languages, making it easy to integrate into existing
  infrastructure. It also supports standard SQL, making it easy to
  migrate from other database systems.
- `Community Support`: MySQL has a large and active community of developers
  and users who provide support, documentation, and resources. This can be
  helpful when encounter issues or need advice on how to use MySQL effectively.

Using triggers, functions, procedures and events in MySQL can improve the
performance and security of application:

- `Triggers`:

    - `Automate tasks`: Triggers allow to automate certain tasks when data in a table changes.
      For example, can use triggers to update related tables or log changes.
    - `Data Validation`: Triggers can be used to validate data before inserting, updating,
      or deleting it, which helps ensure data integrity and prevent errors.

- `Functions`:

    - `Code reuse`: Functions allow to write code once and use it in different parts of
      queries or triggers. This makes it easier to maintain and update the code.
    - `Server-side computing`: Using functions for server-side computing can improve performance.
      since the data does not need to be passed to the client side for processing.

- `Procedures`:

    - `Reduce network traffic`: Performing complex server-side operations using procedures
      can reduce the amount of data transferred over the network, which improves performance.
    - `Improved security`: Using procedures to access data can provide stronger access controls
      and protect data from unauthorized access.

- `Events`:

    - `Scheduling tasks`: Events allow to schedule specific tasks to run at specific
      times or at specific intervals. For example, can use events to archive data regularly.
    - `Improved performance`: Using events to run tasks at specific times can reduce server load
      and improve overall system performance.

Using these MySQL tools can significantly improve the performance and security of
application, simplify its maintenance, and improve data integrity.

###### MYSQL USAGES

The `Database diagram`, table structure, relationships between tables is
shown below:

![Database diagram](/www/back/public/images/readme/db_diagrams.png)

Procedures and functions in Database:

![Database functions](/www/back/public/images/readme/db_functions.jpg)

Events in Database:

![Database events](/www/back/public/images/readme/db_events.jpg)

Some tables have triggers that automatically perform auxiliary operations,
such as logging all performed data write/modify/delete operations.

Example table `user` has next triggers:

- `user_AFTER_INSERT` with SQL code:
```mysql
create trigger user_AFTER_INSERT after insert on user for each row
BEGIN
    DECLARE v_user_id INT DEFAULT 0;
    IF (@SESSION.user_id IS NOT NULL ) THEN
        SET v_user_id = @SESSION.user_id;
    END IF;
    INSERT INTO `user_log`
    (`user_id`, `action_user_id`, `action`, `changed`, `date_action`)
    VALUES (NEW.`id`, v_user_id, 1, '', NOW());
END;
```

- `user_AFTER_UPDATE` with SQL code:
```mysql
create trigger user_AFTER_UPDATE after update on user for each row
BEGIN
    DECLARE v_user_id INT DEFAULT 0;
    DECLARE v_changed LONGTEXT DEFAULT '';
    IF (NEW.`email` <> OLD.`email` or
        NEW.`full_name` <> OLD.`full_name` or
        NEW.`description` <> OLD.`description` or
        NEW.`password` <> OLD.`password` or
        NEW.`status` <> OLD.`status` or
        NEW.`access` <> OLD.`access` or
        NEW.`gender` <> OLD.`gender` or
        NEW.`date_birthday` <> OLD.`date_birthday` or
        NEW.`date_created` <> OLD.`date_created` or
        NEW.`pwd_reset_token` <> OLD.`pwd_reset_token` or
        NEW.`pwd_reset_token_creation_date` <> OLD.`pwd_reset_token_creation_date`)
    THEN
        IF (NEW.`email` <> OLD.`email`) THEN
            SET v_changed = CONCAT(v_changed, 'email = ', NEW.`email`, '; ');
        END IF;
        IF (NEW.`full_name` <> OLD.`full_name`) THEN
            SET v_changed = CONCAT(v_changed, 'full_name = ', NEW.`full_name`, '; ');
        END IF;
        IF (NEW.`description` <> OLD.`description`) THEN
            SET v_changed = CONCAT(v_changed, 'description = ', NEW.`description`, '; ');
        END IF;
        IF (NEW.`status` <> OLD.`status`) THEN
            SET v_changed = CONCAT(v_changed, 'status = ', NEW.`status`, '; ');
        END IF;
        IF (NEW.`access` <> OLD.`access`) THEN
            SET v_changed = CONCAT(v_changed, 'access = ', NEW.`access`, '; ');
        END IF;
        IF (NEW.`gender` <> OLD.`gender`) THEN
            SET v_changed = CONCAT(v_changed, 'gender = ', NEW.`gender`, '; ');
        END IF;
        IF (NEW.`date_birthday` <> OLD.`date_birthday`) THEN
            SET v_changed = CONCAT(v_changed, 'date_birthday = ', NEW.`date_birthday`, '; ');
        END IF;
        IF (NEW.`pwd_reset_token` <> OLD.`pwd_reset_token`) THEN
            SET v_changed = CONCAT(v_changed, 'pwd_reset_token = ', NEW.`pwd_reset_token`, '; ');
        END IF;
        IF (NEW.`pwd_reset_token_creation_date` <> OLD.`pwd_reset_token_creation_date`) THEN
            SET v_changed = CONCAT(v_changed, 'pwd_reset_token_creation_date = ', NEW.`pwd_reset_token_creation_date`, '; ');
        END IF;
        IF (@SESSION.user_id IS NOT NULL ) THEN
            SET v_user_id = @SESSION.user_id;
        END IF;
        INSERT INTO `user_log`
        (`user_id`, `action_user_id`, `action`, `changed`, `date_action`)
        VALUES (OLD.`id`, v_user_id, 2, v_changed, NOW());
    END IF;
END;
```

- `user_BEFORE_DELETE` with SQL code:
```mysql
create trigger user_BEFORE_DELETE before delete on user for each row
BEGIN
    DECLARE v_user_id INT DEFAULT 0;
    DECLARE v_archive INT DEFAULT 3;
    IF (@SESSION.user_id IS NOT NULL) THEN
        SET v_user_id = @SESSION.user_id;
    END IF;
    IF (@SESSION.archive IS NOT NULL) THEN
        SET v_archive = @SESSION.archive;
    END IF;
    INSERT INTO `user_log`
    (`user_id`, `action_user_id`, `action`, `changed`, `date_action`)
    VALUES (OLD.`id`, v_user_id, v_archive, '', NOW());

    DELETE FROM `user_role` WHERE `user_id` = OLD.`id`;
END;
```

To simulate business processes the Database also uses basic procedures:
- `moveUsersArchives` with SQL code:
```mysql
create procedure moveUsersArchives()
BEGIN
    DECLARE v_user_id_archived INT DEFAULT 0;
    DECLARE v_user_id INT DEFAULT 0;
    DECLARE v_done integer DEFAULT 0;
    DECLARE v_id decimal(20, 0) DEFAULT 0;
    DECLARE v_email varchar(128) DEFAULT '';
    DECLARE v_full_name varchar(256) DEFAULT '';
    DECLARE v_description varchar(1024) DEFAULT '';
    DECLARE v_password varchar(128) DEFAULT '';
    DECLARE v_status integer DEFAULT 0;
    DECLARE v_access integer DEFAULT 0;
    DECLARE v_gender integer DEFAULT 0;
    DECLARE v_date_birthday DATETIME;
    DECLARE v_date_created DATETIME;
    DECLARE v_date_updated DATETIME;
    DECLARE v_pwd_reset_token varchar(32) DEFAULT '';
    DECLARE v_pwd_reset_token_creation_date DATETIME;

    DECLARE v_users_cursor CURSOR FOR
        SELECT u.`id`, u.`email`, u.`full_name`, u.`description`, u.`password`, u.`status`,
            u.`access`, u.`gender`, u.`date_birthday`, u.`date_created`, u.`date_updated`,
            u.`pwd_reset_token`, u.`pwd_reset_token_creation_date`
        FROM `user` u
            INNER JOIN user_role ur on u.`id` = ur.`user_id`
            INNER JOIN role r on r.`id` = ur.`role_id`
        WHERE u.`status` = 2 AND r.`name` = 'Guest';

    DECLARE CONTINUE HANDLER FOR NOT FOUND SET v_done = 1;

    IF (@SESSION.user_id IS NOT NULL ) THEN
        SET v_user_id = @SESSION.user_id;
    END IF;

    OPEN v_users_cursor;

    users_loop:
    LOOP
        FETCH v_users_cursor INTO v_id, v_email, v_full_name, v_description, v_password,
            v_status, v_access, v_gender, v_date_birthday, v_date_created, v_date_updated,
            v_pwd_reset_token, v_pwd_reset_token_creation_date;

        IF v_done = 1 THEN
            LEAVE users_loop;
        END IF;

        INSERT INTO `user_archives`
        (`email`, `full_name`, `description`, `password`, `status`, `access`, `gender`,
         `date_birthday`, `date_created`, `date_updated`, `pwd_reset_token`,
         `pwd_reset_token_creation_date`, `date_archived`, `archived_user_id`)
        VALUES (v_email,  v_full_name, v_description, v_password, v_status, v_access, v_gender,
                v_date_birthday, v_date_created, v_date_updated, v_pwd_reset_token,
                v_pwd_reset_token_creation_date, NOW(), v_user_id);
        SET v_user_id_archived = LAST_INSERT_ID();

        UPDATE `user_role` SET `user_id` = 0, `user_archived_id` = v_user_id_archived WHERE `user_id` = v_id;

        SET @SESSION.archive = 4;
        DELETE FROM `user` WHERE `id` = v_id;
    END LOOP users_loop;
    CLOSE v_users_cursor;
END;
```

- `setUsersAccesses` with SQL code:
```mysql
create procedure setUsersAccesses()
BEGIN
    DECLARE v_max_id INT DEFAULT 0;
    SELECT MAX(id) FROM `user` INTO v_max_id;
    UPDATE `user` u
        INNER JOIN user_role ur on u.`id` = ur.`user_id`
        INNER JOIN role r on r.`id` = ur.`role_id`
    SET u.`access` = 1, u.`date_updated` = NOW()
    WHERE u.`status` = 1 AND r.`name` = 'Guest' AND u.`id` = randomInt(v_max_id);
END;
```

- `setUsersArchives` with SQL code:
```mysql
create procedure setUsersArchives()
BEGIN
    DECLARE v_max_id INT DEFAULT 0;
    SELECT MAX(id) FROM `user` INTO v_max_id;
    UPDATE `user` u
        INNER JOIN user_role ur on u.`id` = ur.`user_id`
        INNER JOIN role r on r.`id` = ur.`role_id`
    SET u.`status` = 2, u.`date_updated` = NOW()
    WHERE u.`status` = 1 AND r.`name` = 'Guest' AND u.`id` = randomInt(v_max_id);
END;
```

- `setUsersNotAccesses` with SQL code:
```mysql
create procedure setUsersNotAccesses()
BEGIN
    UPDATE `user` SET `access` = 2, `date_updated` = NOW() WHERE `access` = 1;
END;
```

The Database also uses events that run the main procedures on a given schedule.
- `moveArchives` with SQL code:
```mysql
create event moveArchives on schedule
    every '1' HOUR starts '2023-01-24 00:42:59' enable do
    BEGIN
    CALL moveUsersArchives();
END;
```

- `setAccesses`;

- `setArchives`;

- `setNotAccesses`.

The Database also uses helper function:
- `randomInt` with SQL code:
```mysql
create function randomInt(count int) returns tinyint
BEGIN
    DECLARE vResult INT DEFAULT 0;
    SELECT FLOOR((RAND() * 100)) INTO vResult;

    RETURN vResult;
END;
```

##### DB MONGO

###### ADVANTAGES

`MongoDB` - is an additional NoSqL documentary Database in which application
logs are written, it consists of one collection `logs`.

MongoDB is a popular NoSQL database that is known for its flexibility,
scalability, and performance.

Here are some advantages of using MongoDB:

- `Schema-less Design`: MongoDB is a document-oriented database, which means it
  stores data in flexible, JSON-like documents. This schema-less design allows
  to store data without a predefined schema, making it easy to evolve
  data model over time.
- `Highly Scalable`: MongoDB is designed to scale horizontally, allowing to
  distribute data across multiple servers to handle large amounts of data
  and high traffic loads. This makes it suitable for large-scale applications
  that require high availability and scalability.
- `Flexible Data Model`: MongoDB's document model allows to store data in a way
  that reflects the structure of application's objects. This makes it easy
  to work with complex data structures and nested objects without the need for
  complex joins or relationships.
- `High Performance`: MongoDB is optimized for performance, with support for indexes,
  queries, and aggregation operations that allow to retrieve and manipulate
  data quickly and efficiently. It also supports sharding and replication, which
  can further improve performance and availability.
- `Ad Hoc Queries`: MongoDB supports ad hoc queries, allowing to query
  data using a flexible query language that supports a wide range of operations,
  including filtering, sorting, and aggregation.
- `Automatic Failover and Replication`: MongoDB supports automatic failover and
  replication, allowing to ensure high availability and data durability.
  In the event of a server failure, MongoDB can automatically failover to a replica
  set member, minimizing downtime and data loss.
- `Rich Query Language`: MongoDB's query language is powerful and expressive,
  allowing to perform complex queries on data. It supports a wide
  range of operators and functions, making it easy to manipulate and analyze
  data.
- `Community Support`: MongoDB has a large and active community of developers
  and users who provide support, documentation, and resources. This can be
  helpful when encounter issues or need advice on how to use MongoDB effectively.

###### MONGO USAGES

The Database collection `logs` structure:

![Mongodb logs](/www/back/public/images/readme/mongodb_logs.jpg)

##### DB REDIS

###### ADVANTAGES

`Redis` is an auxiliary database that stores data in RAM in the form
of key-value records `role:`, lists `roles`, sorted lists `role:set`
and speeds up the application by replacing heavier references to
the main relational DB.

`Redis `is an open-source, in-memory data structure store that is used
as a database, cache, and message broker.

Here are some advantages of using Redis:

- `Speed`: Redis is incredibly fast, as it stores data in memory. This
  allows for quick read and write operations, making it ideal for use
  cases that require high performance, such as caching and real-time
  analytics.
- `Versatility`: Redis supports various data structures, including strings,
  hashes, lists, sets, and sorted sets. This versatility allows to
  use Redis for a wide range of use cases, from caching to real-time
  analytics to messaging.
- `Persistence`: Redis offers different persistence options, allowing
  to choose between storing data only in memory (which provides the highest
  performance but no durability) or persisting data to disk (which provides
  durability but at the cost of performance).
- `Pub/Sub Messaging`: Redis supports publish/subscribe messaging, allowing
  to build real-time applications that can send and receive messages.
  This feature is useful for building chat applications, real-time
  dashboards, and more.
- `Atomic Operations`: Redis supports atomic operations on its data structures,
  allowing to perform complex operations on data in a single,
  atomic operation. This ensures that data remains consistent even in
  a multi-threaded or distributed environment.
- `Lua Scripting`: Redis supports Lua scripting, allowing to write custom
  scripts that can be executed on the server side. This allows for complex
  operations to be performed on the server side, reducing the amount of
  data that needs to be sent over the network.
- `High Availability`: Redis supports replication and clustering, allowing
  to set up a highly available Redis cluster that can withstand node
  failures without losing data.
- `Community Support`: Redis has a large and active community of developers
  and users who provide support, documentation, and resources. This can be
  helpful when encounter issues or need advice on how to use Redis effectively.

###### REDIS USAGES

In console can be executed commands:
```bash
redis-cli
keys "*"
```

The Database `lists` structure:

![Redis lists](/www/back/public/images/readme/redis_list.jpg)

#### PROGRAMMATIC MESSAGE BROKER

##### RABBIT MQ

###### ADVANTAGES

`Asynchronous thread processing potentially lengthy processes` tied
to interaction with third-party services, using `Rabbit MQ`
programmatic message broker and using a scheme to exchange information
between the sender and by the recipient, when data sources send
information flows, and recipients process them as needed.

`RabbitMQ` is a popular open-source message broker that is widely used for
building scalable and reliable messaging applications.

Here are some advantages of using RabbitMQ:

- `Reliability`: RabbitMQ is known for its reliability and robustness. It
  ensures message delivery by using features such as message acknowledgments,
  durable queues, and message persistence.
- `Flexibility`: RabbitMQ supports multiple messaging protocols, including Advanced
  Message Queuing Protocol (AMQP), MQTT, and STOMP. This makes it suitable for a
  wide range of messaging use cases.
- `Scalability`: RabbitMQ is designed to be highly scalable. It supports clustering,
  which allows to distribute message queues across multiple nodes to handle high
  message volumes and ensure high availability.
- `Routing and Filtering`: RabbitMQ supports advanced routing and filtering capabilities,
  allowing to route messages based on message attributes and content. This makes it
  easy to implement complex messaging patterns.
- `Message Acknowledgment`: RabbitMQ supports message acknowledgment, which ensures that
  messages are only removed from the queue once they have been successfully processed.
  This helps prevent message loss and ensures reliable message delivery.
- `Management and Monitoring`: RabbitMQ provides a management interface that allows
  to monitor and manage message queues and exchanges. You can view metrics such
  as queue depth, message rates, and node status.
- `Community Support`: RabbitMQ has a large and active community of developers and users
  who provide support, documentation, and resources. This can be helpful when
  encounter issues or need advice on how to use RabbitMQ effectively.
- `Integration`: RabbitMQ integrates seamlessly with a wide range of programming languages
  and frameworks, making it easy to use RabbitMQ in existing applications.

###### RABBIT MQ USAGES

The handler for viewing the list of messages in topic `user_notification`
in `Rabbit MQ` can be executed by running:
```bash
rabbit
```

#### CRON

##### ADVANTAGES

`CRON` is a standard tool in Unix-like operating systems that allows
tasks to be executed at a specific time or frequency. CRON is especially
useful for automating regular tasks such as updating a database, sending
notifications, or cleaning up temporary files.

`Cron jobs` are useful for administrative tasks. Unlike workers,
they are launched for a short time according to a schedule.

The CRON system is based on configuration files of a cron daemon, which
runs in the background and executes tasks at specified times. Each task
in CRON is described using CRON records, which indicate the time points
or frequency of task execution.

##### CRON USAGES

In `Symfony` and other PHP applications, `CRON` is often used to run
`Symfony Console commands` or `PHP scripts` on a `schedule`. To do this,
need to configure the appropriate CRON entry to execute the
desired command or script at the right time.

`Comments` marked as spam or rejected by the `administrator` are stored in the
database so that the administrator can view them later. But they probably
should be removed after a certain time anyway.

Removing old comments is an ideal cron job. It should be performed regularly
and minor delays in execution do not play a significant role.

Create a `CLI command` called `app:comment:cleanup`.

All application commands are registered along with the built-in `Symfony commands`,
and they are all accessible through the symfony console. Since the number of
available commands can be large, it is necessary to group them by namespace.
By convention, application commands should be stored in the app space.

The command accepts `input` (arguments and parameters passed to the command) as
well as `output`, which can use to print data to the console.

Thus, `CRON` is a powerful tool for `automating tasks` on Unix-like systems
and is widely used to ensure regular execution of tasks in many applications.

#### LOGGING

##### ADVANTAGES

`Logging` is the process of recording events and messages that occur
during the execution of a software application.

Logging is an essential part of application development and operation,
providing several advantages:

- `Debugging`: Logging helps developers debug issues by providing a record of
  events and messages that occurred leading up to an error or unexpected
  behavior. This information can help identify the root cause of the problem
  and facilitate troubleshooting.
- `Monitoring`: Logging allows developers and system administrators to monitor
  the health and performance of an application in real-time. By analyzing log
  messages, they can identify performance bottlenecks, track usage patterns,
  and detect anomalies.
- `Auditing`: Logging can be used for auditing purposes to track user actions
  and system events. This can be helpful for compliance with regulations and
  standards, as well as for investigating security incidents.
- `Performance Analysis`: Logging can provide valuable insights into the
  performance of an application, including response times, resource usage,
  and throughput. This information can be used to optimize the performance
  of the application.
- `Error Reporting`: Logging allows applications to report errors and exceptions
  in a structured format, making it easier to identify and resolve issues.
  Error logs can include stack traces, error codes, and other relevant
  information to help diagnose the problem.
- `Security`: Logging can help improve the security of an application by recording
  security-related events, such as failed login attempts, access control changes,
  and security policy violations. This information can be used to detect and
  respond to security incidents.
- `Historical Analysis`: Logging provides a historical record of events and messages,
  which can be useful for trend analysis, forecasting, and planning future
  development efforts.
- `Compliance`: Logging is often required for compliance with regulations and
  standards, such as GDPR, HIPAA, and PCI DSS. Logging can help demonstrate
  compliance by providing a record of relevant events and actions.

##### LOGGING USAGES

1. in PHP read log file, example in console run next commands:
```bash
tail -n 100 -f /var/log/dev-2024-02-29.log
```

![Log php](/www/back/public/images/readme/log_php.jpg)

2. in MongoDB `learn` collection `logs`, example run next query:
```bash
db.getCollection('logs').find({priority:100, timestamp:{$gte:ISODate("2023-01-24"),$lt:ISODate("2023-02-24")}});
```

![Log MongoDB](/www/back/public/images/readme/log_mongo.jpg)

3. in MySql DB `learn` tables: `logs` - for all logs in application,
   `user_log` and `user_role_log` - only for logs from table `user` and `user_role`

![Log MySql logs](/www/back/public/images/readme/log_mysql_logs.jpg)

![Log MySql user_log](/www/back/public/images/readme/log_mysql_user_log.jpg)

4. in Redis DB read log file, example in console run next commands:
```bash
tail -n 100 -f /var/log/redis/redis.log
```

![Log redis](/www/back/public/images/readme/log_redis.jpg)

#### PHP STANDARDS RECOMMENDATIONS (PSR)

`Symfony` supports several `PHP Standard Recommendations` (`PSRs`), which are
standards for organizing PHP code.

Below are specific Symfony components that implement specific `PSRs`:

- `PSR-1: Basic Coding Standard` - Symfony supports PSR-1 in general, but does not provide
  special components or tools to implement it directly. Instead, Symfony recommends that
  developers follow PSR-1 standards when writing code.
- `PSR-3: Logger Interface` - `symfony/monolog-bundle` component provides integration
  with the Monolog library for logging and implements PSR-3 for logging.
- `PSR-4: Autoloading Standard` - Symfony fully complies with PSR-4 for class autoloading.
  This means that the directory structure in Symfony must follow the class namespace hierarchy.
  In Symfony Framework, the recommended `directory structure` follows this standard and used
  in `autoload` all components, libraries, packages in Symfony by `composer`.
- `PSR-6: Caching Interface` - used in `symfony/cache`, `doctrine/cache` components
  in Symfony Framework provides support for caching and implements PSR-6 for cache handling.
- `PSR-7: HTTP Message Interface` - used in `Symfony\Component\HttpFoundation\Request`,
  `Symfony\Component\HttpFoundation\Response` components in Symfony Framework.
- `PSR-11: Container Interface` - used in `Psr\Container\ContainerInterface`,
  `Symfony\Component\DependencyInjection\ContainerBuilder`,
  `Symfony\Component\DependencyInjection\ServiceLocator` components in Symfony Framework
  implements PSR-11 to work with a dependency container.
- `PSR-12: Extended Coding Style Guide` - used in all codes in Symfony Framework,
  Symfony does not explicitly support PSR-12, as it is a standard for organizing
  and formatting code that focuses on code readability and maintainability.
- `PSR-14: Event Dispatcher` - Symfony has its own mechanism for working with events
  through the `symfony/event-dispatcher` component, which is not an exact implementation
  of PSR-14, but provides similar functionality.
- `PSR-15: HTTP Middleware` - Symfony provides its own middleware implementation
  through the `symfony/http-kernel` component.
- `PSR-17: HTTP Factories` - `symfony/http-foundation` provides an implementation of
  PSR-17 for creating HTTP requests and responses.
- `PSR-18: HTTP Client` - Symfony does not have a native implementation of PSR-18,
  but the `symfony/http-client` component provides an HTTP client, which can be used
  to make HTTP requests.

These components allow to use `Symfony` in accordance with the `PHP Standard Recommendations`
for a more convenient and compliant `PHP` development experience.

### FRONTEND TECHNOLOGY

#### HTML, CSS, JAVASCRIPT

To develop the user interface of this application, `standard web technologies` are used:

- `HTML` (`HyperText Markup Language`): Used to structure the content of web pages,
  including markup of forms, tables, and other elements.
- `CSS` (`Cascading Style Sheets`): Used to style the appearance of web pages,
  including text styles, colors, fonts, padding, and other properties.
- `JavaScript`: Used to create interactivity in web pages, handle events, form
  validation, asynchronously load data, and other tasks.

#### FRAMEWORKS OR LIBRARIES USED

To simplify development and provide a more modern and responsive user interface,
the application uses the following `frameworks` and `libraries`:

- `Bootstrap`: Used to create responsive and stylish web page designs. Bootstrap
  provides ready-made components, grids, styles for buttons, forms and other
  interface elements.
- `jQuery`: jQuery is used to make working with JavaScript easier by facilitating
  DOM manipulation, event handling, animations, and other operations.
- `Symfony Webpack Encore`: Symfony Webpack Encore is used to build and manage
  static resources (e.g. CSS, JavaScript, images) in a Symfony project, making
  front-end development and optimization easier.

These frameworks and libraries help speed up development, provide a consistent
design and improve the user experience when working with the application.
`Symfony` can easily integrate with them to create a user interface.

To `integrate with Bootstrap and jQuery in Symfony`, can use the following
components and packages:

- `Webpack Encore`: Symfony offers Webpack Encore for managing front-end resources
  such as JavaScript and CSS. With Webpack Encore, can easily integrate and
  use Bootstrap and jQuery in Symfony projects.
- `Asset`: The Asset component allows to manage static assets (such as images,
  CSS and JavaScript files) and their paths. You can use Asset to load Bootstrap
  and jQuery from their respective libraries.
- `Twig Extensions`: With Twig extensions can easily embed links to static resources,
  such as CSS and JavaScript files into Twig templates. It allows to use
  Bootstrap and jQuery in Symfony templates.
- `Symfony Forms`: Symfony Forms allows to create forms in Symfony applications.
  You can use Bootstrap's styling tools to style forms and use jQuery to handle dynamic
  form behavior.
- `Twig and Assetic`: You can use Twig and Assetic to include and manage Bootstrap and
  jQuery styles and scripts in Symfony templates.

### DEPENDENCY MANAGEMENT

#### COMPOSER

##### ADVANTAGES

`Composer` is a `dependency management tool for PHP` that simplifies the
process of managing dependencies and libraries in `PHP` projects.

Here are some advantages of using Composer:

- `Dependency Management`: Composer allows to declare the dependencies
  of project in a composer.json file, including the specific versions
  of each dependency. Composer then manages these dependencies,
  ensuring that the correct versions are installed.
- `Autoloading`: Composer generates an autoloader for project based on
  the dependencies have declared. This autoloader allows to easily
  load classes from dependencies without having to manually include them.
- `Version Constraint Resolution`: Composer can resolve version constraints
  for dependencies, ensuring that the correct versions are installed
  based on the constraints have specified in composer.json file.
- `Package Repositories`: Composer can install packages from the Packagist
  repository, which is a central repository for PHP packages. You can also
  configure Composer to use other repositories if needed.
- `Update and Dependency Resolution`: Composer can update dependencies
  to the latest versions that match version constraints. It also
  resolves dependencies recursively, ensuring that all dependencies are
  compatible with each other.
- `Optimized Dependency Resolution`: Composer uses a dependency solver to
  find the best combination of dependencies that meet requirements,
  ensuring that project's dependencies are compatible and consistent.
- `Lock File`: Composer generates a composer.lock file that locks the versions
  of dependencies, ensuring that the same versions are installed when
  deploy project on different environments.
- `Plugins`: Composer supports plugins that extend its functionality. There
  are plugins available for tasks such as optimizing autoloader performance
  and managing package installation.
- `Community Support`: Composer is widely used in the PHP community and has a
  large and active community. This means that can find plenty of
  resources, documentation, and support when using Composer in projects.

##### COMPOSER COMMANDS

Composer is a `dependency manager` for the PHP programming language.

An additional feature of Composer is that it offers utilities for
hooking packages to PHP `autoload` by PSR-4.

`Custom scripts` that do not fit one of the predefined event name above,
can either run them with run-script or also run them as `native Composer commands`.

Command in Composer's dependency manager to install all dependencies specified
in the project's `composer.json` file:
```bash
composer install
```

The `composer install` command also checks for the presence of the `composer.lock`
file and, if present, installs the package versions specified there; otherwise,
Composer installs the latest available package versions according to the specified
version restrictions in composer.json.

Using `composer install` is recommended when installing a project for the first
time or after updating the composer.json file with new dependencies to ensure
that all dependencies are installed correctly and consistently.

Command in Composer's dependency manager to updating all project dependencies
to their latest versions, according to the version restrictions specified in
the `composer.json` file:
```bash
composer update
```

Using `composer update` is recommended to update project's dependencies
to the latest versions. However, should be careful when running this
command on a production server, as it may introduce changes that could break
the application due to upgrades to `incompatible package versions`.

Command in Composer's dependency manager which checks that the syntax of
the `composer.json` file in project is correct:
```bash
composer validate
```

Using `composer validate` is recommended when working with composer.json
file to ensure that it contains the correct structure and does not contain
errors that could cause Composer to not work correctly when installing
dependencies.

The handler for automatic code style checking with `phpcs` can be executed by running:
```bash
composer check-cs
```
or
```bash
composer check-cs-coverage
```

The handler for automatic code style fixing errors with `phpcs` can be executed by running:
```bash
composer check-cs-fix
```

The handler for automatic code static checking with `phpstan` can be executed by running:
```bash
composer check-stan
```
or
```bash
composer check-stan-coverage
```

The handler for automatic code static checking with `psalm` can be executed by running:
```bash
composer check-psalm
```
or
```bash
composer check-psalm-coverage
```

The handler for automatic code static checking with `phpmd` can be executed by running:
```bash
composer check-phpmd
```
or
```bash
composer check-phpmd-coverage
```

The handler for automatic checking `unit tests` in all code can be executed by running:
```bash
composer check-phpunit-unit-no-coverage
```
or
```bash
composer check-phpunit-unit-coverage-html
```
or
```bash
composer check-phpunit-unit-coverage-clover
```

The handler for automatic checking `integration tests` in all code can be executed by running:
```bash
composer check-phpunit-integration-no-coverage
```
or
```bash
composer check-phpunit-integration-coverage-html
```
or
```bash
composer check-phpunit-integration-coverage-clover
```

The handler for automatic checking `functional tests` in all code can be executed by running:
```bash
composer check-phpunit-functional-no-coverage
```
or
```bash
composer check-phpunit-functional-coverage-html
```
or
```bash
composer check-phpunit-functional-coverage-clover
```

The handler for automatic checking all, `unit`, `integration` and `functional tests`
in all code can be executed by running:
```bash
composer check-phpunit-all-no-coverage
```
or
```bash
composer check-phpunit-all-coverage-html
```
or
```bash
composer check-phpunit-all-coverage-clover
```

The handler for automatic create all Databases and loading fixtures
to all Databases can be executed by running:
```bash
composer project-refresh-dev
```

The handler for automatic create all Databases and loading fixtures
to all Databases for integration tests can be executed by running:
```bash
composer project-refresh-test
```

The handler for automatic checking code style, code static, unit,
integration and functional tests in all code can be executed by running:
```bash
composer check-project
```
or
```bash
composer check-project-clear-cache
```
or
```bash
composer check-project-coverage
```
or
```bash
composer check-project-coverage-clear-cache
```

#### NPM

##### ADVANTAGES

`NPM` (`Node Package Manager`) is a package manager for the `JavaScript` language
that allows to manage dependencies and publish packages for use by other
developers.

Here are some key features and benefits of NPM:

- `Dependency Management`: NPM makes it easy to manage project dependencies,
  installing and updating packages from the NPM repository.
- `Publishing packages`: Developers can publish their own packages to the NPM
  repository, making them available for use by other users.
- `Local installation`: NPM allows to install packages locally for a specific
  project or globally for the entire system.
- `Scripts`: In NPM, can define scripts that perform various tasks, such as
  building a project, running tests, and other operations.
- `Versioning`: NPM supports package versioning, which allows to specify specific
  versions of packages to install and update.
- `Large selection of packages`: The NPM repository contains a huge number of packages
  and libraries for various tasks and development scenarios.
- `Automatic installation of dependencies`: When install a package, NPM automatically
  installs all its dependencies, simplifying the development process.
- `Support for multiple operating systems`: NPM supports a variety of operating systems,
  including Windows, macOS, and Linux.

Using `NPM` allows developers to speed up the development process, use ready-made
solutions from a huge catalog of packages, and manage project dependencies with
minimal effort.

##### NPM COMMANDS

Command in NPM's dependency manager to install all dependencies specified
in the project's `package.json` file:
```bash
npm install
```

This command will read the `package.json` file in project's root
directory and install all dependencies listed in the dependencies and
devDependencies sections. The installed packages will be saved in the
`node_modules` directory in project.

By running `npm ci` using symfony run, leveraging Symfony's ability
to execute commands in a controlled environment, ensuring that the `npm ci`
command is executed correctly within the context of Symfony application:
```bash
composer symfony-npm-ci
```
or
```bash
symfony run npm ci --ansi
```

By running `npm run dev` using symfony run, executing the script in the
context of Symfony application, which can be useful for development
workflows where need to build assets alongside PHP code:
```bash
composer symfony-npm-run-dev
```
or
```bash
symfony run npm run dev --ansi
```

### CI/CD

#### ADVANTAGES

`Continuous Integration, Delivery, and Deployment (CI/CD)` - is at the heart
of the success of DevOps practices. The principle of CI / CD is focused on
creating an optimal automated process for releasing software releases.
Teams that put CI/CD into practice receive constant feedback, delivering
software to end users as quickly as possible, by studying user experience
and embodying their ideas in the next releases.

`Continuous Integration/Continuous Deployment (CI/CD) automation` refers to
the practice of automating the processes involved in building, testing,
and deploying software. Here are some advantages of CI/CD automation:

- `Faster Development Cycles`: CI/CD automation allows developers to quickly
  integrate their code changes into the main codebase, leading to faster
  development cycles and shorter release cycles.
- `Improved Code Quality`: By automating the testing process, CI/CD helps
  ensure that code changes meet quality standards before being deployed,
  reducing the likelihood of bugs and errors in production.
- `Early Detection of Issues`: Automated testing in CI/CD pipelines can detect
  issues such as bugs, regressions, and compatibility problems early in the
  development process, making them easier and cheaper to fix.
- `Consistent Builds`: CI/CD automation ensures that builds are performed
  consistently, using the same environment and configuration every time.
  This helps reduce the risk of build failures due to environment differences.
- `Scalability`: CI/CD automation can easily scale to handle large codebases
  and complex projects, allowing development teams to work on projects of
  any size with confidence.
- `Faster Time to Market`: By automating the deployment process, CI/CD helps
  reduce the time it takes to get new features and updates into the hands
  of users, allowing businesses to respond quickly to market demands.
- `Improved Collaboration`: CI/CD encourages collaboration between development,
  operations, and quality assurance teams, leading to better communication
  and shared responsibility for the software development process.
- `Cost Savings`: CI/CD automation can lead to cost savings by reducing the
  time and effort required for manual testing, deployment, and maintenance
  tasks.
- `Risk Reduction`: By automating repetitive tasks and ensuring consistent
  builds, CI/CD helps reduce the risk of human error and potential security
  vulnerabilities in the software development process.

#### JENKINS AUTOMATION

##### ADVANTAGES

`Jenkins` is a popular open-source automation server that is used to
automate various tasks involved in the software development process.

Here are some advantages of using Jenkins automation:

- `Continuous Integration (CI)`: Jenkins is well-suited for implementing
  CI pipelines, allowing developers to automatically build, test, and
  deploy their code whenever changes are made to the codebase. This helps
  catch integration issues early in the development cycle.
- `Flexibility`: Jenkins offers a wide range of plugins that extend its
  functionality, allowing to integrate Jenkins with other tools and
  services in development workflow. This flexibility makes Jenkins
  suitable for a variety of use cases and environments.
- `Easy Configuration`: Jenkins provides an intuitive web interface for
  configuring and managing jobs, making it easy to set up and customize
  CI/CD pipelines according to project's requirements.
- `Scalability`: Jenkins can scale to handle large and complex projects,
  allowing to run multiple builds and tests in parallel to improve
  efficiency and reduce build times.
- `Wide Adoption`: Jenkins is widely used in the industry and has a large
  community of users and contributors. This means that can find plenty
  of resources, documentation, and support when using Jenkins in projects.
- `Integration with Version Control Systems`: Jenkins integrates seamlessly
  with popular version control systems like Git, allowing to trigger
  builds automatically whenever changes are pushed to the repository.
- `Extensibility`: Jenkins can be extended using plugins to add new features
  and functionality. There are thousands of plugins available for Jenkins,
  allowing to customize and enhance its capabilities to suit needs.
- `Monitoring and Reporting`: Jenkins provides built-in monitoring and reporting
  tools that allow to track the status of builds and view detailed
  reports on test results and code coverage.
- `Cost-Effective`: Jenkins is open-source software, meaning that it is free
  to use and can be deployed on own infrastructure, reducing the cost
  of implementing CI/CD pipelines compared to proprietary solutions.

##### JENKINS USAGES

`Symfony` does not contain components that integrate directly with `Jenkins`.

However, Symfony applications can easily integrate with `Jenkins` to implement
`continuous integration and delivery` (`CI/CD`) using the following components
and practices:

- `Symfony Console`: The Symfony Console component allows to create
  own commands to interact with the application via the command line.
  You can use the Symfony Console to create commands that can be run in
  Jenkins to automate the build and test processes.
- `Symfony Process`: The Symfony Process component allows to launch external
  processes from a Symfony application. You can use Symfony Process to run
  Jenkins commands from application.
- `PHPUnit`: Symfony comes with PHPUnit for testing applications. You can
  configure Jenkins to run PHPUnit tests as part of the CI process.
- `Jenkins Pipeline`: Jenkins supports the concept of Pipeline, which allows
  to describe the CI/CD process in the form of code. You can create a
  Jenkins Pipeline that will run Symfony Console commands, PHPUnit and other
  necessary commands to build, test and deploy a `Symfony application`.
- `Jenkins Plugins`: There are many Jenkins plugins that can help with
  integration with Symfony and other technologies. For example, the
  Symfony Plugin for Jenkins can make it easier to configure Jenkins
  to work with Symfony applications.

Integrating Symfony with Jenkins is usually done through setting up a
Jenkins Job or Pipeline, which runs the necessary Symfony commands to
build, test, and deploy the application.

#### BITBUCKET PIPELINES AUTOMATION

##### ADVANTAGES

`Bitbucket Pipelines` runs all builds in Docker containers using an image
that specify at the beginning of configuration file. Can easily use PHP
with Bitbucket Pipelines by using one of the official PHP Docker images
on Docker Hub.

`Bitbucket Pipelines` is an integrated CI/CD service built into Bitbucket.
It allows to automatically build, test, and even deploy code based on a
configuration file in repository. Essentially, create Docker containers
in the cloud.

`Bitbucket Pipelines` is a continuous integration and deployment tool that
is integrated directly into Bitbucket Cloud, providing seamless automation
for software development workflows.

Here are some advantages of using Bitbucket Pipelines automation:

- `Native Integration`: Bitbucket Pipelines is tightly integrated with Bitbucket
  Cloud, allowing to define CI/CD pipelines directly in repository
  using a bitbucket-pipelines.yml file. This makes it easy to set up and manage
  pipelines alongside code.
- `YAML Configuration`: Pipelines configuration is done using a simple YAML syntax,
  making it easy to define build and deployment steps. This allows to
  automate tasks such as building, testing, and deploying code with just
  a few lines of code.
- `Docker Support`: Bitbucket Pipelines provides built-in support for Docker,
  allowing to use Docker containers to define build environment.
  This makes it easy to create reproducible build environments and ensures
  that builds are consistent across different environments.
- `Parallel Builds`: Pipelines supports parallel builds, allowing to split
  build process into multiple steps that can run concurrently. This can
  help reduce build times and improve overall pipeline efficiency.
- `Integrated Testing`: Pipelines can automatically run tests as part of
  build process, providing instant feedback on the quality of code.
  This helps catch bugs early in the development process and ensures that
  code is always in a deployable state.
- `Deployment Automation`: Pipelines can automatically deploy code to
  hosting environment, such as AWS, Azure, or Heroku, based on defined
  deployment strategy. This helps streamline the deployment process and reduces
  the risk of manual errors.
- `Visibility and Monitoring`: Pipelines provides visibility into the status of
  builds and deployments through the Bitbucket interface, allowing
  to monitor the progress of pipelines and quickly identify and fix any
  issues that arise.
- `Cost-Effective`: Bitbucket Pipelines offers a pay-as-you-go pricing model,
  meaning that only pay for the resources use. This can be
  cost-effective for small to medium-sized teams compared to maintaining and
  scaling own CI/CD infrastructure.

##### BITBUCKET PIPELINES USAGES

`Symfony` does not include components that directly integrate with
`Bitbucket Pipelines`.

However, `Symfony applications` can easily integrate with Bitbucket Pipelines
to implement `continuous integration and delivery` (`CI/CD`) using the following
components and practices:

- `Symfony Console`: The Symfony Console component allows to create
  own commands to interact with the application via the command line.
  You can use the Symfony Console to create commands that can be run in
  Bitbucket Pipelines to automate the build and test processes.
- `PHPUnit`: Symfony comes with PHPUnit for testing applications. You can
  configure Bitbucket Pipelines to run PHPUnit tests as part of CI process.
- `Docker`: Symfony applications can be packaged into Docker containers for
  easier deployment and dependency management. You can use Docker in Bitbucket
  Pipelines to build and run Symfony applications.
- `Bitbucket Pipelines Configuration`: You can configure the bitbucket-pipelines.
  yml in Bitbucket repository to describe the CI/CD process, including the
  steps to build, test, and deploy a Symfony application.
- `Bitbucket API`: You can also use the Bitbucket API to automate some of the
  tasks associated with managing repository and CI/CD process in Bitbucket Pipelines.

Integrating `Symfony` with `Bitbucket Pipelines` is typically accomplished by setting
up Bitbucket Pipelines configuration to run the necessary Symfony commands to
build, test, and deploy the application.

#### DOCKER AUTOMATION

##### ADVANTAGES

`Docker` is a popular platform for developing, shipping, and running
applications inside containers. Containers are lightweight, standalone,
and executable packages that contain everything needed to run a piece
of software, including the code, runtime, system tools, libraries, and
settings.

Here are some advantages of using Docker:

- `Consistency`: Docker containers provide a consistent environment for
  running applications, regardless of the underlying infrastructure.
  This helps eliminate the "it works on my machine" problem and ensures
  that applications behave the same way in different environments.
- `Isolation`: Docker containers provide process and filesystem isolation,
  allowing to run multiple containers on the same host without
  interference. This makes it easy to deploy and manage complex applications
  with multiple dependencies.
- `Efficiency`: Docker containers are lightweight and share the host
  system's kernel, making them more efficient than traditional virtual
  machines. Containers start up quickly and consume fewer resources,
  making them ideal for deploying and scaling applications.
- `Portability`: Docker containers can run on any system that supports
  Docker, regardless of the underlying operating system. This makes it
  easy to deploy applications across different environments, from
  development to production.
- `Version Control`: Docker images are version-controlled and can be
  easily shared and distributed using Docker registries, such as
  Docker Hub. This makes it easy to collaborate with others and
  deploy applications to different environments.
- `Dependency Management`: Docker containers encapsulate dependencies,
  making it easy to manage and update them. This helps ensure that
  applications always run with the correct dependencies, reducing
  the risk of compatibility issues.
- `DevOps Practices`: Docker promotes DevOps practices by enabling
  developers to build, test, and deploy applications more quickly
  and efficiently. Docker's tooling and ecosystem integrate seamlessly
  with CI/CD pipelines, making it easy to automate the software
  delivery process.
- `Scalability`: Docker containers can be easily scaled horizontally to
  handle increased traffic or workload. Docker Swarm and Kubernetes
  are popular tools for orchestrating and managing containerized
  applications in a clustered environment.

##### DOCKER USAGES

`Symfony` does not include specific components for working with `Docker` directly.

However, `Symfony applications` can easily integrate with `Docker` for packaging
and deployment in containers.

To implement `Continuous Delivery` (`CD`) using integrate `Symfony` with `Docker`,
can use the following components and tools:

- `Symfony Flex`: Symfony Flex provides convenient tools for managing dependencies
  and customizing a Symfony project. You can use Symfony Flex to install and configure
  Docker-specific dependencies, such as setting up Docker environment.
- `Symfony CLI`: The Symfony CLI provides many commands for managing a Symfony application,
  including commands for working with Docker. For example, can use the Symfony CLI
  to run a Symfony application in a Docker container in local development environment.
- `Dockerfile`: To package a Symfony application into a Docker container, need a
  Dockerfile, which defines how to build and run the container. Symfony does not
  contain Docker-specific components, but can use standard Dockerfile instructions
  to install PHP and configure a Symfony application in a container.
- `Docker Compose`: Docker Compose allows to define and run multi-container
  Docker applications using a simple `YAML configuration` file. You can use
  Docker Compose to define and run containers, necessary for running Symfony applications,
  including a container for PHP, a web server and a database.
- `Symfony Bundles`: There are third party Symfony bundles that make it easier to
  integrate Symfony with Docker. For example, docker-bundle allows to manage
  Docker containers from a Symfony application, which can be useful for development
  and testing.
- `Deployment Tools`: To automate the process of deploying a Symfony application
  to a production environment, can use various tools such as Ansible, Chef,
  Puppet or even specialized CI/CD platforms (for example, `Jenkins`, `GitLab CI/CD`,
  `CircleCI`).
- `Environment Variables`: Symfony supports working with environment variables,
  which allows to customize application for different environments (e.g.
  development, staging, production) without changing the source code. You can use
  environment variables to configure application in different Docker containers
  and deployment environments.

Integrating `Symfony` with `Docker` is usually done by creating a `Dockerfile` and a
`Docker Compose` file to package and run a `Symfony application` in a container.
Symfony provides convenient tools to manage this process and make working
with Docker easier in project.

Implementing Continuous Delivery using Symfony and Docker allows to automate
the process of deploying application and ensure its fast and reliable delivery
to the production environment.

This skeleton provides a `docker-compose.yml` for use with
[docker-compose](https://docs.docker.com/compose/); it uses the provided
`Dockerfile` to build a docker image for the `symfony` container created
with `docker-compose`.

Build and start the image and container using:
```bash
$ docker-compose up -d --build
```

At this point, can visit [localhost:8080](http://localhost:8080) to
see the site running.

You can also run commands such as `composer` in the container.  The container
environment is named "symfony" so will pass that value to `docker-compose run`:
```bash
$ docker-compose run symfony composer install
```

### QA TOOLS

#### ADVANTAGES

`Automation QA tools` in PHP, such as PHPUnit for unit testing and
Codeception for acceptance and functional testing, offer several
advantages:

- `Efficiency`: Automation tools can run tests much faster than manual
  testing, allowing for quicker feedback on the code changes.
- `Consistency`: Automated tests are consistent and repeatable, ensuring
  that the same tests are executed in the same way every time.
- `Coverage`: Automation tools can cover a wide range of test cases,
  including edge cases and error scenarios, ensuring thorough testing
  of the application.
- `Regression Testing`: Automated tests can be easily re-run to ensure
  that new code changes do not introduce regressions or break existing
  functionality.
- `Integration with CI/CD`: Automation tools can be integrated with
  Continuous Integration/Continuous Deployment (CI/CD) pipelines,
  allowing for automated testing of code changes before deployment.
- `Early Bug Detection`: Automated tests can detect bugs early in the
  development cycle, when they are easier and cheaper to fix.
- `Improved Code Quality`: By encouraging developers to write testable code,
  automation tools can lead to improved code quality and maintainability.
- `Documentation`: Automated tests serve as documentation for the expected
  behavior of the application, making it easier for developers to
  understand and maintain the code.

#### AUTOMATIC QA TOOLS USAGES

##### GENERAL

This project has a QA tooling, with configuration for each of:

- [phpcs](https://github.com/squizlabs/php_codesniffer)
- [phpstan](https://phpstan.org)
- [psalm](https://psalm.dev)
- [phpmd](https://phpmd.org)
- [phpunit tests](https://phpunit.de)
- [sonar qube](https://docs.sonarqube.org/latest/)

Provide aliases for each of these tools in the Composer configuration.

##### AUTOMATIC CODE STYLE CHECKER

The handler for automatic code style checking with `phpcs` can be executed by running:
```bash
composer check-cs
```
or
```bash
phpcs
```
or
```bash
composer check-cs-coverage
```

![phpcs](/www/back/public/images/readme/phpcs.jpg)

The handler for automatic code style fixing errors with `phpcs` can be executed by running:
```bash
composer check-cs-fix
```

##### AUTOMATIC CODE STATIC CHECKER PHPSTAN

The handler for automatic code static checking with `phpstan` can be executed by running:
```bash
composer check-stan
```
or
```bash
composer check-stan-coverage
```
or
```bash
phpstan analyse --level=7 --memory-limit=1024M --xdebug
```

![phpstan](/www/back/public/images/readme/phpstan.jpg)

##### AUTOMATIC CODE STATIC PSALM ANALYSIS

The handler for automatic code static checking with `psalm` can be executed by running:
```bash
composer check-psalm
```
or
```bash
composer check-psalm-coverage
```
or
```bash
vendor/bin/psalm
```

![psalm](/www/back/public/images/readme/psalm.jpg)

##### AUTOMATIC CODE STATIC PHPMD ANALYSIS

The handler for automatic code static checking with `phpmd` can be executed by running:
```bash
composer check-phpmd
```
or
```bash
composer check-phpmd-coverage
```
or
```bash
vendor/bin/phpmd src/ text phpmd_ruleset.xml --suffixes php,phtml.twig --strict --color
```

![phpmd](/www/back/public/images/readme/phpmd.jpg)

#### AUTOMATIC TESTS CHECKER

##### ADVANTAGES

For a successful, cyclical, smooth and error-free release of each version
of the software product testers must write a `test plan`, that is, make a
`very large number` (from several dozen, up to several hundred) `test cases`
to cover `absolutely all possible use cases users of the functionality` of
the software product (for example, each route), for `absolutely all
business processes`.

And then, before the release of each version, testers must `manually
execute all previously written test plan`, that is, they must check all
hundreds of test cases. Such a very large number of test cases for manual
verification, makes the work of the tester routine and introduces the
concept of `"human factor"`, which increases the likelihood of them making
an `error` during the check.

And with subsequent releases of versions of the software product, there
may be a need to perform `integration testing` or `functional testing`,
that is, checking not only the changes in the latest version, but also
the entire functionality, which means that there will already be
`several thousand test cases for manual verification`.

Therefore, once writing all the unit and integration or functional tests
for each test case, even if for each route there will be several dozen
of them - can almost completely `automate` all the routine, manual
work of testers and eliminate the human factor in the work of testers
almost completely.

Thus, it is possible to `automate up to 90% of all manual work` of testers.

The `remaining 10%`, something that cannot be automated, such as
intellectual, creative, research test cases, tests to identify new ways
to use existing functionality, to search for new ones business processes
and, as a result, new offers to users - can remain testers for manual
execution by them.

`Automation of verification of absolutely almost all test cases`, once
writing all the unit and integration or functional tests, allows to
check everything at any time, repeatedly in a fully automatic mode, for
example, at each code changes, with each merge to the base branch, with
each release of a new version, periodically the entire working system.

Which `can guarantee` a `high speed of release of new versions of the
software product`, `high quality of the code and the functionality of the
released software product`, the `absence of bugs` and, as a result, customer
satisfaction error-free and trouble-free operation of the system they
use, and as a result, an `increase in sales of the manufactured software
product` and, accordingly, an `increase in profits`.

`Symfony` typically uses `PHPUnit`, which is a standard tool for testing in PHP,
to write `unit`, `integration`, and `functional` tests.

Here's how these `types of tests` are typically implemented:

- `Unit tests`:

    - `Goal`: Testing individual methods or classes (units) without dependencies.
    - `Characteristics`:
        - Individual parts of the code are tested without dependencies on other modules.
        - Typically, written by programmers to test small sections of code.
        - Can be launched quickly and easily.
    - `Implementation`: In PHPUnit, unit tests are typically created for each
      method or class and use assertions to verify that they behave as expected.
    - `Example`: Testing a class method that performs a mathematical calculation.

- `Integration tests`:

    - `Goal`: Checking the interaction between components (for example, classes,
      services) within the application.
    - `Characteristics`:
        - Use cases involving multiple modules or components are tested.
        - Can identify problems in integration between components.
        - Usually they require setting up the environment to run.
    - `Implementation`: Integration tests instantiate classes and call their
      methods to test interactions. Symfony configuration loading can be used.
    - `Example`: Testing the interaction of a database class and a business logic class.

- `Functional tests`:

    - `Goal`: To test the behavior of an application using its API or interface.
    - `Characteristics`:
        - Complete application usage scenarios are tested.
        - May include interaction with the user interface or API.
        - Help ensure that the application performs as expected by the end user.
    - `Implementation`: Functional tests can use the Symfony client to interact
      with the application over HTTP and test responses.
    - `Example`: Testing a web application through a browser, including page
      navigation and data entry.

Together, these types of tests ensure the reliability and stability of the software.

##### PHP UNIT TESTS

###### ADVANTAGES

All Unit tests running in `total isolation`, without connecting
to any external services, such as databases, message brokers, etc.
Which provides very `high execution speed` and allows developers
to constantly run the entire unit test suite after each code
change and before each merge to the base branch.

All calls to any external services are `mute`.

PHP Unit tests writing with `full coverage for all test cases`.

Here are some advantages of using PHPUnit for unit testing in PHP:

- `Automated Testing`: PHPUnit allows to automate the process of
  running tests, making it easy to test code quickly and efficiently.
- `Isolation`: PHPUnit encourages to write tests that are isolated from
  each other, meaning that each test should only test a single piece of
  functionality. This makes it easier to identify and fix bugs.
- `Code Coverage`: PHPUnit can generate code coverage reports, showing
  which parts of code are covered by tests. This can help
  identify areas of code that are not adequately tested.
- `Assertion Library`: PHPUnit provides a wide range of assertion methods
  for verifying the behavior of code. These assertions cover a variety
  of scenarios, including checking values, comparing arrays, and verifying
  exceptions.
- `Integration with CI/CD`: PHPUnit integrates seamlessly with Continuous
  Integration/Continuous Deployment (CI/CD) pipelines, allowing to
  automate the execution of tests and ensure that code remains stable.
- `Mocking and Stubbing`: PHPUnit provides features for creating mock objects
  and stubs, allowing to simulate complex dependencies in tests.
  This can help isolate the code are testing and make tests
  more focused.
- `Community Support`: PHPUnit has a large and active community of developers
  and users who provide support, documentation, and resources. This can be
  helpful when encounter issues or need advice on how to use PHPUnit
  effectively.

###### PHP UNIT TESTS USAGES

This project has a complete PHP Unit tests with `full coverage code` for all methods in:

- CQRS;
- Command;
- Controller;
- DTO;
- DataFixtures;
- Document;
- Entity;
- EntityListener;
- EventSubscriber;
- Factory;
- Form;
- Helper;
- Message;
- MessageHandler;
- Notification;
- Repository;
- Security;
- Service;
- State;
- Util;
- Validator;

Running all PHP Unit Tests with coverage in HTML-format:
```bash
composer check-phpunit-unit-coverage-html
```
or
```bash
php bin/phpunit --testsuite=Unit --colors=always  --coverage-html ./var/check/tests/unit
```

![phpunit_unit](/www/back/public/images/readme/phpunit_unit.jpg)

Running all PHP Unit Admin Tests with coverage in HTML-format:
```bash
composer check-phpunit-unit-admin-coverage-html
```

![phpunit_unit admin](/www/back/public/images/readme/phpunit_unit_admin.jpg)

Running all PHP Unit Admin Form Tests with coverage in HTML-format:
```bash
composer check-phpunit-unit-admin-form-coverage-html
```

![phpunit_unit admin form](/www/back/public/images/readme/phpunit_unit_admin_form.jpg)

View Web Dashboard PHP Unit tests coverage code in this project:

![phpunit_unit coverage](/www/back/public/images/readme/phpunit_unit_coverage1.jpg)
![phpunit_unit coverage](/www/back/public/images/readme/phpunit_unit_coverage2.jpg)
![phpunit_unit coverage](/www/back/public/images/readme/phpunit_unit_coverage2_1.jpg)
![phpunit_unit coverage](/www/back/public/images/readme/phpunit_unit_coverage2_2.jpg)

![phpunit_unit coverage](/www/back/public/images/readme/phpunit_unit_coverage3.jpg)
![phpunit_unit coverage](/www/back/public/images/readme/phpunit_unit_coverage4.jpg)
![phpunit_unit coverage](/www/back/public/images/readme/phpunit_unit_coverage5.jpg)

##### PHP INTEGRATION TESTS

###### ADVANTAGES

All Integration tests running with `real connecting to all external
services`, such as databases, message brokers, etc.

PHP Integration tests writing with `full coverage for all test cases`.

###### PHP INTEGRATION TESTS USAGES

This project has a complete PHP Integration tests with `full coverage code` for all methods in:

- Api;
- Command;
- Controllers;

Running all PHP Integration Tests with coverage in HTML-format:
```bash
composer check-phpunit-integration-coverage-html
```
or
```bash
php bin/phpunit --testsuite=Integration --colors=always  --coverage-html ./var/check/tests/integration
```

![phpunit_integration](/www/back/public/images/readme/phpunit_integration.jpg)

View Web Dashboard PHP Integration tests coverage code in this project:

![phpunit_integrationt coverage](/www/back/public/images/readme/phpunit_integration_coverage1.jpg)
![phpunit_integrationt coverage](/www/back/public/images/readme/phpunit_integration_coverage2.jpg)
![phpunit_integrationt coverage](/www/back/public/images/readme/phpunit_integration_coverage3.jpg)
![phpunit_integrationt coverage](/www/back/public/images/readme/phpunit_integration_coverage4.jpg)
![phpunit_integrationt coverage](/www/back/public/images/readme/phpunit_integration_coverage5.jpg)

##### PHP FUNCTIONAL TESTS

###### ADVANTAGES

All Functional tests running with `real connecting to all external
services`, such as databases, message brokers, etc.

PHP Functional tests writing with `full coverage for all test cases`.

###### PHP FUNCTIONAL TESTS USAGES

This project has a complete PHP Functional tests with `full coverage code` for all methods in:

- Api;
- Controllers;

Running all PHP Functional Tests with coverage in HTML-format:
```bash
composer check-phpunit-functional-coverage-html
```
or
```bash
php bin/phpunit --testsuite=Functional --colors=always  --coverage-html ./var/check/tests/functional
```

![phpunit_functional](/www/back/public/images/readme/phpunit_functional.jpg)

View Web Dashboard PHP Functional tests coverage code in this project:

![phpunit_functional coverage](/www/back/public/images/readme/phpunit_functional_coverage1.jpg)
![phpunit_functional coverage](/www/back/public/images/readme/phpunit_functional_coverage2.jpg)
![phpunit_functional coverage](/www/back/public/images/readme/phpunit_functional_coverage3.jpg)

##### PHP UNIT, INTEGRATION AND FUNCTIONAL TESTS

Running all PHP Unit, Integration and Functional tests with coverage in HTML-format:
```bash
composer check-phpunit-all-coverage-html
```

#### AUTOMATIC SONARQUBE

##### ADVANTAGES

`SonarQube` is a self-managed, automatic code review tool that systematically
helps deliver clean code. As a core element of Sonar solution, SonarQube
integrates into existing workflow and detects issues in code to help perform
continuous code inspections of projects.

The tool analyses programming languages and integrates into CI pipeline and
DevOps platform to ensure that code meets high-quality standards.

Writing `clean code` is essential to maintaining a healthy codebase.
We define clean code as code that meets a certain defined standard,
i.e. code that is reliable, secure, maintainable, readable, and modular,
in addition to having other key attributes. This applies to all code:
source code, test code, infrastructure as code, glue code, scripts, etc.

Sonar's `Clean as Code` approach eliminates many of the pitfalls that arise from
reviewing code at a late stage in the development process.
The `Clean as Code` approach uses quality gate to alert/inform when thereвЂ™s
something to fix or review in new code (code that has been added or changed),
allowing to maintain high standards and focus on code quality.

![sonar_01](/www/back/public/images/readme/sonar_01.jpg)

The Sonar solution performs checks at every stage of the development process:

- SonarLint provides immediate feedback in IDE as write code so can find
  and fix issues before a commit.
- SonarQubeвЂ™s PR analysis fits into CI/CD workflows with SonarQubeвЂ™s PR analysis
  and use of quality gates.
- Quality gates keep code with issues from being released to production, a key tool
  in helping incorporate the Clean as Code methodology.
- The Clean as Code approach helps focus on submitting new, clean code for production,
  knowing that existing code will be improved over time.


With Clean as Code, focus is always on `New code` (code that has been added or
changed according to new code definition) and making sure the code write today is clean
and safe.

The New code definition can be set at different levels (global, project, and, starting
in Developer Edition, at the branch level).

Depending on the level at which new code definition is set, can change the
starting point to fit situation.


Organizations start off with a default set of rules and metrics called the Sonar way
`quality profile`. This can be customized per project to satisfy different technical
requirements. Issues raised in the analysis are compared against the conditions defined
in the quality profile to establish quality gate.

![sonar_02](/www/back/public/images/readme/sonar_02.jpg)


A `quality gate` is an indicator of code quality that can be configured to give a go/no-go
signal on the current release-worthiness of the code.

It indicates whether code is clean and can move forward:

- A passing (green) quality gate means the code meets standard and is ready to be merged.
- A failing (red) quality gate means there are issues to address.

With the Clean as You Code approach, Quality gate should:

- Focus on `New code metrics` вЂ“ When quality gate is set to focus on new code metrics
  (like the built-in Sonar way quality gate), new features will be delivered cleanly.
  As long as quality gate is green, releases will continue to improve.
- Set and enforce `high standards` вЂ“ When standards are set and enforced on new code,
  aren't worried about having to meet those standards in old code and having to
  clean up someone else's code. You can take pride in meeting high standards in code.
  If a project doesn't meet these high standards, it won't pass the quality gate,
  and is therefore not ready to be released.

![sonar_03](/www/back/public/images/readme/sonar_03.jpg)

You can use `pull request analysis` and `pull request decoration` to make sure that code meets
standards before merging. Pull request analysis lets see pull request's quality
gate in the SonarQube UI. You can then decorate pull requests with SonarQube issues
directly in DevOps platform's interface.

SonarQube provides `feedback` through its UI, email, and in decorations on pull or merge
requests to notify team that there are issues to address.
Feedback can also be obtained in SonarLint supported IDEs when running in connected mode.

SonarQube also provides in-depth guidance on the issues telling why each issue is a problem
and how to fix it, adding a valuable layer of education for developers of all experience
levels. Developers can then address issues effectively, so code is only promoted when the
code is clean and passes the quality gate.

##### SONARQUBE USAGES

```bash
# Run scanner SonarQube:
composer sonar-scanner
```

![sonar scanner](/www/back/public/images/readme/sonar_05.jpg)


---
---

## DESIGN

### DESIGN METHODS

There are many design methods and technologies available, and the specific
ones chosen depend on the type of project, its goals, and the context.

Some of the `best known` and widely used methods and technologies include:

- `IDEF` (`Integrated DEFinition`) is a set of methodologies and modeling languages
  developed for the analysis and design of systems. IDEF includes several different
  methods, each of which describes specific aspects of systems design. `IDEF` is usually
  used in the early stages of design to analyze and model the system as a whole. It helps
  define the structure, functions, and interactions between system components at a high
  level of abstraction. Thus, `IDEF` rather corresponds to the
  `top level of the system architecture`. This will help understand the overall
  architecture of the system and its interaction with external components.
- `DDD` (`Domain-Driven Design`): A design methodology that focuses on domain modeling
  and business rules to create more flexible and understandable systems. `DDD`, on the other
  hand, focuses on modeling the domain at a lower level of abstraction. It describes business
  rules, entities and their interactions within the system. DDD is primarily used during the
  software design phase to create `specific solutions` that meet business requirements. This
  will help create more accurate and flexible software that reflects real business needs.

Thus, we can say that `IDEF` is usually used at a `higher level of a system's architecture`
to generally describe its structure and functions, while `DDD` is used at a `lower level`
to `model business processes and objects` in more detail.

Thus, a combination of `IDEF` and `DDD` techniques can provide a more complete and efficient
system design, taking into account both `general structural aspects` and `details of
business logic and functionality`.

### IDEF

#### ADVANTAGES

The `main goal of IDEF` is to structure and document complex systems in order to
better understand their functionality and the interactions of components.

When designing using IDEF methods, the following steps typically follow:

- `Create a Conceptual Model` (`IDEF0`): In this step, create an IDEF0 diagram that
  shows the overall structure and function of the system at a high level. This diagram
  helps identify the main functional areas of the system and their relationships.
  Function Flow Analysis (IDEF0): Next, analyze the functional flows in the system,
  identifying the inputs, outputs, and control points for each function. This allows
  to understand how data and control move through the system.
- `Data Structure Modeling` (`IDEF1`): In this step, create an IDEF1 diagram,
  which shows the data structure of the system, including entities, attributes and
  relationships between them. This helps to understand how data is organized and how
  it is used in the system.
- `Modeling Decision Processes` (`IDEF3`): If system has decision processes,
  can use IDEF3 diagrams to model them. This will help identify various alternatives
  and selection criteria.
- `Additional methods and diagrams`: Depending on the specifics of project,
  may use other IDEF methods and diagrams, such as `IDEF4` for modeling organizational
  structure or `IDEF5` for modeling data processing processes.

**Thus, `IDEF methods and diagrams` help structure the system `design process`, from the
`overall concept` to the `details of its functionality` and `data structure`.**

Some of the more well-known `IDEF methods` include:

- `IDEF0`: Used for `functional modeling`, including the description of processes
  and their interactions in a system.
- `IDEF1`: Used to describe `information flows` and `data structures` in a system.
- `IDEF1X`: An extension of IDEF1, used to model data structures in `databases`.
- `IDEF3`: Used to describe the `decision-making processes` of a system.

`IDEF diagrams` are graphical models used to describe the structure and function of a system.

There are several types of `IDEF diagrams`, including:

- `IDEF0`: Diagrams used to `model the functional processes` of a system. They include blocks
  to represent functions, arrows to show data flow and control, as well as context diagrams
  to show the interactions between different functions.
- `IDEF1`: Diagrams used to `model the data structure` of a system. They may include data
  entities, attributes, and relationships between them, represented as a database schema.
- `IDEF3`: Diagrams, used to `model decision-making processes` in the system. They may include
  nodes to represent solutions and alternatives, as well as arrows to show the flow of decisions.

You can use specialized modeling tools such as `Diagramo` or other `CASE tools` to create
`IDEF diagrams`. They provide a set of symbols and functions for creating `IDEF diagrams` as
required by the standard. `Diagramo` provides a free online tool for creating various types of
diagrams, including IDEF.

#### IDEF0 - CREATE A CONCEPTUAL MODEL

For the **Simple Web Demo - Free Lottery Management Application** project,
creating a `conceptual model` can be a key step in the design.

A `conceptual model` can help describe the core functionality and structure
of application at a high level of abstraction.

Creating a `conceptual model` for the project:

- `Identification of main functions`:

    - `Lottery participant management`: User registration, account management,
      ability to view and change user information.
    - `Creating and editing draws`: Ability to create new lottery draws, edit
      existing draws, manage the number of draws.
    - `Conducting and monitoring drawings`: Automatic holding of drawings,
      tracking the results of drawings, notification of winners.

- `Description of entities and their relationships`:

    - `Users`: Information about users (ID, name, email, password, etc.).
      Relationships: One user can have one or more roles (Role). One user can
      have permissions through a Role (Permission), which determines his access
      to certain functions or resources.
    - `Comment`: Contains comments from other users on the winning user.
      Relationships: The comment is associated with the specific user to whom
      the comment is written.
    - `Role`: Defines the user's role in the system. Relationships: A role can
      be assigned to one or more users.
    - `Permission`: Defines a user's access rights to certain application features
      or resources. Relationships: A permission can be assigned to one or more roles.
    - `Admin`: A separate user with additional rights and access to the administrative
      functions of the application. Relationships: An administrator can have all or
      some of the permissions and roles defined in the system.
    - `Log`: A log of events or actions occurring in the system. Relationships: A log
      entry can be created by an administrator or another user and contains information
      about the action, the time, and the user who performed the action.

- `Creating an IDEF0 diagram`:

    - An `IDEF0 diagram` may include blocks of functions such as
      `User management`, `Administration of users and resources`,
      `Automatic lottery`, `Managing user roles and access rights`,
      `View a list of active users with access to a resource` and arrows indicating the flow
      of data and control between these functions.

- `Defining business rules`:

    - `Sweepstakes Conditions`: Age requirements, geographic restrictions
      and other rules governing who may participate in the Sweepstakes.
    - `Sweepstakes Rules`: How the winner will be selected, how the prize
      will be determined, etc.

#### IDEF0 - FUNCTION FLOW ANALYSIS

For the application **Simple Web Demo Free Lottery Management Application**,
the `IDEF0 diagram` for `functional flow analysis` will consist of the
following blocks:

- `User Management`: The main block representing the overall management of users
  and resources. Includes administration of users and resources, as well as
  automatic lottery holding. `Movement of functional flows`:

    - Data about users and their roles is transferred to the
      `User and resource administration` block.
    - Information about conducted lotteries and changes in user statuses
      is transferred to the `Manage user roles and access rights` block.

- `User and resource administration`: A function responsible for creating, editing
  and deleting user data, and managing access to resources.  `Movement of functional flows`:

    - User data can be transferred from the `User Management` block to
      update information.
    - Data on user status (active/inactive) is transferred to the
      `Manage user roles and access rights` block.

- `Automatic lottery`: A function that conducts a lottery for random users and
  changes their status and access to resources.  `Movement of functional flows`:

    - The results of the lottery (changes in user statuses) are transferred to
      the `User Management` block to update the data.

- `Manage user roles and access rights`: The function responsible for creating,
  editing and deleting user roles and access rights, as well as their hierarchy
  and purpose.  `Movement of functional flows`:

    - Information about user roles and access rights is transferred from
      the `User Management` block for their management and modification.
    - Data on roles and access rights can be transferred to the
      `User and resource administration` block to update the information.

- `View a list of active users with access to a resource`: A function that allows
  to view a list of users with public access to a resource without authentication.  
  `Movement of functional flows`:

    - The list of active users and their access to the resource is formed
      based on data from the `User Management` block and is transmitted for display.

This is a `general conceptual model` of application's block-level functionality.
Each block can be further detailed indicating input and output data, control elements, etc.

#### IDEF1 - DATA STRUCTURE MODELING

`IDEF1` - data structure modeling is a modeling methodology that allows to
describe the structure of data in an information system.

For the `Simple Web Demo Free Lottery Management Application`, can create
a data model as follows:

- **Entities**:

    - `Admin`:
        - ID (user identifier)
        - UserName
        - Roles
        - Password
        - Token

    - `User`:

        - ID (user identifier)
        - UID  (user identifier for view)
        - UserName
        - Full Name
        - Description
        - Email
        - Status (active/inactive)
        - Access (yes/no)
        - Gender (man/woman)
        - Date birthday
        - Created at
        - Updated at
        - Roles
        - Role (reference to Role entity)
        - Slug

    - `Comment`:

        - ID (comment identifier)
        - User (reference to the User entity)
        - Author
        - Text
        - Email
        - Created at
        - Photo filename
        - State

    - `Role`:

        - ID (role identifier)
        - Role name
        - Description
        - Date created

    - `Permission`:

        - ID (Access Rights Identifier)
        - Access right name
        - Description
        - Date created

- **Relationship**:

    - Each comment belongs to one user (`Comment -> User`)
    - A role can be assigned to one or more users (`Role -> User`)
    - An access right can be assigned to one or more roles (`Permission -> Role`)

It is a simplified data model that captures the core entities and their
relationships for an application.

#### IDEF3 - MODELING DECISION PROCESSES

`IDEF3` is a method for modeling decision-making processes that allows
to describe the processes associated with making and implementing decisions
in a system.

For the **Simple Web Demo Free Lottery Management Application**, can use `IDEF3`
to model the processes for managing users, roles, and permissions.

Processes may include:

- **User creation process**:

    - `Input data`: information about the new user (name, email, password, etc.).
    - `Decision making`: checking the availability of all necessary data, creating
      a user record in the database.
    - `Output`: created user.

- **The process of assigning a role to a user**:

    - `Input data`: user, to whom the role needs to be assigned, and the selected role.
    - `Decision making`: checking the correctness of the role selection, assigning
      the role to the user.
    - `Output`: user with assigned role.

- **Lottery process**:

    - `Input data`: list of lottery participants.
    - `Decision making`: choosing a random participant, changing its status and providing
      access to the resource.
    - `Output`: participant with changed status.

- **Process for changing access rights**:

    - `Input`: The selected role or permission for which want to change settings.
    - `Making a decision`: changing the settings of the selected role or access right.
    - `Output`: updated settings.

Modeling these and other processes using IDEF3 will allow to better understand
and optimize application management processes.

#### IDEF4 - ADDITIONAL METHODS AND DIAGRAMS

`IDEF4` is a methodology that provides additional methods and diagrams for
modeling aspects of a system that were not covered in previous IDEF methods.

For the **Simple Web Demo Free Lottery Management Application**, can use
`IDEF4` to further model the following aspects:

- **Interaction Diagram**:

    - Shows the interactions between objects in the system, such as between
      users, roles and access rights.
    - Allows to see which objects interact with each other and what
      operations they perform.

- **Sequence Diagram**:

    - Shows the sequence of messages between objects in the system in a specific context.
    - Allows to understand what actions occur in the system and in what sequence.

- **State Diagram**:

    - Shows the various states of an object in the system and the transitions between these states.
    - Allows to simulate the behavior of objects depending on their current state.

- **Component Diagram**:

    - Shows system components and their relationships.
    - Allows to see the structure of the system and the interaction between its components.

- **Deployment Diagram**:

    - Shows the physical deployment of system components on hardware.
    - Allows to see which components are located on which devices and how they interact.

Using these additional methods and diagrams will allow to describe
various aspects of the application and its operation in more detail
and completeness.

### DDD

#### ADVANTAGES

`Domain-Driven Design` (`DDD`) is a software development approach that
focuses on modeling a business domain and its rules.

For the **Simple Web Demo Free Lottery Management Application**, can
apply `DDD principles` to design and organize code more efficiently.

#### DDD PRINCIPLES

`DDD principles` for the **Simple Web Demo Free Lottery Management Application**:

- **Ubiquitous Language**:

    - Use a common language to describe business rules and concepts in the application.
    - Ensure understanding between developers and business experts.

- **Business Domain Modeling**:

    - Define the key entities and aggregates of application (for example, users, roles,
      access rights).
    - Highlight the business rules and invariants that must be followed in the application.

- **Using Repositories**:

    - Use repositories to access data while hiding database implementation details.
    - Repositories allow to abstract from a specific data storage technology.

- **Domain Services**:

    - Use domain services to implement business logic that does not belong to a specific entity.
    - Domain services can be used to perform complex operations that require the
      interaction of multiple entities.

- **Significant Events (Domain Events)**:

    - Use domain events to track significant changes in business domain.
    - Events allow different parts of an application to respond to changes in the system.

- **Conceptual Contours**:

    - Describe the structure and relationships between key elements of a business domain.
    - Helps visualize and understand architectural solutions at the level of concepts and
      abstractions.

- **Deep Refactoring**:

    - The process of changing the code structure and architecture of an application
      to improve its readability, understandability, and maintainability.
    - Allows to improve the quality of code and architecture without changing
      its external behavior.

- **Bounded Context**:

    - The principle of dividing large systems into limited contexts, each of which
      has its own rules and modeling.
    - Allows to reduce the complexity of the system and increase understanding
      of its parts.

- **Continuous Integration**:

    - The process of automatically building and testing code when it changes.
    - Allows to quickly identify and fix errors, improving code quality and
      speeding up the development process.

- **Context Map**:

    - Visualize connections and interactions between bounded contexts in a system.
    - Helps to understand the structure of the system and its architectural solutions.

- **Common Core**:

    - The part of the model that is common to all bounded contexts.
    - Allows to avoid duplication of code and data in the system.

- **Distillation**:

    - The process of identifying the most important and key elements from a model or system.
    - Helps to focus on the main aspects and simplify the architecture.

- **Core Domain**:

    - The part of a business domain that is core to the business and brings the most value.
    - This should be the focus of most modeling and development efforts.

- **Conformist Core**:

    - Part of a business domain that does not provide direct value to the business,
      but is required to maintain consistency with other systems or standards.
    - Typically represents adaptation or support for third-party systems.

- **Metaphorical image of the system (System Metaphor)**:

    - An image that illustrates the main aspects and principles of a system
      using analogies from the real world.
    - Helps understand and remember key ideas and concepts of the system.

Applying `DDD principles` will help create a more `flexible and scalable
application architecture` and improve business understanding logic in development team.

## MANAGEMENT

### ADVANTAGES

`Development management` includes the organization of work processes, communication
within the team, quality control and deadlines for completing work.

### DEVELOPMENT MANAGEMENT METHODOLOGIES

#### ADVANTAGES

Well-known development management methodologies:

- `Scrum`: An agile methodology based on iterative development. Suitable for projects
  requiring frequent changes and rapid response to customer requirements.
- `Kanban`: A methodology focused on workflow visualization and work flow management.
  Suitable for projects with a continuous flow of tasks.
- `Lean Development`: Based on the principles of Lean Manufacturing and aimed at eliminating
  redundant processes and maximizing value creation for the customer.
- `Extreme Programming` (`XP`): A methodology that emphasizes code quality and fast feedback.
  Suitable for projects with high quality requirements and frequent changes.
- `Feature Driven Development` (`FDD`): A methodology that focuses on feature development.
  Suitable for projects with clearly defined functional requirements.
- `Crystal`: A family of agile methodologies adapted to different types of projects.
  Suitable for teams of varying sizes and experience.
- `Dynamic Systems Development Method` (`DSDM`): A methodology focused on achieving rapid
  results and managing change during the development process. Suitable for projects with
  limited time and budget.

The choice of a specific methodology depends on the characteristics of the project,
the preferences of the team and the requirements of the customer.

#### CHOOSING A MANAGEMENT METHODOLOGY

To manage the development of the **Simple Web Demo Free Lottery Management Application**
project, the `Scrum methodology` was chosen. This choice is motivated by the need for a
flexible and iterative development approach that allows rapid response to changing
requirements and ensures transparency of the process for all participants.

#### BASIC PRINCIPLES AND PRACTICES OF SCRUM

`Scrum` is based on the principles of adaptability, transparency and collective
responsibility. Core Scrum practices include:

* Dividing work into small iterations called sprints.
* Plan sprints based on team priorities and capabilities.
* Daily status meetings to discuss progress and possible problems.
* Regular sprint reviews to demonstrate results and receive feedback.
* Regular retrospectives to analyze past sprints and find ways to improve the process.
* Scrum supports constant interaction between team members and the customer, which
  contributes to more effective development and achievement of set goals.

### ORGANIZATION OF WORK PROCESSES

#### PROCESSES OF WORKING ON TASKS

Developing task processes involves defining the stages of task completion,
organizing and managing the workflow for effective project development.

For project **Simple Web Demo Free Lottery Management Application** can suggest
the following approaches:

- **Task tracking**:

    - Use a task management system Jira to create and track tasks.
    - Create tasks for each required functionality or improvement.
    - Prioritizing tasks and grouping them into sprints or releases.

- **Code review**:

    - Definition of the code review process, including the frequency and obligation of reviews.
    - Participation of several developers in code reviews to ensure code quality and exchange of experience.
    - Using code review tools (e.g. GitHub Pull Requests, Bitbucket Code Insights).

- **Testing**:

    - Development of test scripts to check the functionality of the application in the `TestRail`
      online service. `TestRail` allows to effectively organize and manage the testing process,
      which will help develop a high-quality and reliable application.
    - Using automated tests for repeatable scenarios (for example, PHPUnit for testing PHP code).
    - Run tests regularly to ensure code stability and quality.

- **Integration with CI/CD**:

    - Set up a continuous integration and continuous delivery (CI/CD) process to automate the build,
      test and deployment of the application.
    - Using CI/CD tools Jenkins to perform tasks automatically.

- **Feedback and process improvement**:

    - Conduct regular retrospectives to discuss past iterations and identify improvements to the workflow.
    - Making adjustments to the process of working on tasks based on feedback from the team.

The development of task processes should be flexible and tailored to the specifics
of project to ensure effective collaboration among team members and achievement of
set goals.

#### ROLES AND RESPONSIBILITIES IN THE TEAM

The following roles and responsibilities should be defined within the project development team:

* `Project Manager`: Organization of team work and project management.
  Planning and control of task execution.
  Interaction with the customer and other stakeholders.
* `Product Owner`: Determining product requirements and prioritizing tasks.
  Ensuring that the product being developed meets the customer's needs.
  Working with the task backlog.
* `Scrum Master`: Ensure adherence to Scrum principles and practices.
  Removing obstacles that hinder the team's work.
  Maintaining communication within the team and promoting self-organization.
* `Team Lead`: Leading a development team, ensuring a shared vision and goals.
  Motivating the team to achieve better results.
  Planning the team's work and distributing tasks among participants.
  Organizing work processes and improving their efficiency.
  Helping developers solve technical problems and complex problems.
  Ensure adherence to architectural principles and coding standards.
* `Developers`: Direct code creation and implementation of product functionality.
  Participation in planning, code review and testing.
* `QA Engineer`: Plan and execute product testing. Defect detection and tracking.
  Collaborating with developers to improve product quality.
* `DevOps Engineers`: Organization and support of development and deployment infrastructure.
  Automate the processes of building, testing and deploying the application.
  Ensuring application security and monitoring.
* `UI/UX Designers`: User interface and experience development.
  Creation of prototypes and design layouts.
  Ensuring ease of use and design consistency with the brand.
* `Business Analysts`: Analyze business requirements and define functional requirements.
  Development of user scenarios and interaction with the customer to clarify requirements.

Each role has unique responsibilities and interacts with other team members to
successfully deliver the project.

### PLANNING AND TASK MANAGEMENT

#### CREATING AND ESTIMATING DEVELOPMENT TASKS

* `User Stories`: The development team, together with the customer, defines
  functional requirements in the form of user scenarios. User stories should be
  detailed enough to describe the required functionality, but specific enough
  to be completed in one sprint.
* `Tasks`: Based on user stories, specific tasks that must be completed to
  implement each user story are determined. Each task must be clearly formulated,
  have an estimate of complexity and completion time.
* `Task Estimation`: Task estimation is carried out with the participation of
  the entire development team, usually using the Planning Poker method or
  similar techniques. The purpose of an estimate is to get an idea of how much
  time and effort will be required to complete a task.

#### DRAWING UP AND MAINTAINING A WORK PLAN

* `Roadmap`: This is a high-level development plan that defines the key project
  milestones and their timelines. It helps the entire team and stakeholders understand
  the big picture of the project's development.
* `Sprint Planning`: Based on user stories and task assessments, the team plans work
  for the next sprint. During sprint planning, tasks are selected for completion, their
  priorities are determined and distributed among team members. The sprint plan must be
  realistic and take into account the capabilities and capabilities of the team.
* `Plan Maintenance`: During the sprint, the team tracks progress on tasks and updates
  the work plan as necessary. Regular meetings (such as Daily Stand-ups) help track
  progress and resolve emerging issues.

### COMMUNICATION WITHIN THE TEAM

#### DAILY STATUS MEETINGS (STAND-UPS)

Stand-up meetings (or daily meetings) are intended to exchange information
within the team, discuss current tasks and identify possible problems.

Frequency: Every day at a set time, usually in the morning before the
start of the work day.

Duration: 10 to 15 minutes.

Process - each team member answers three basic questions:

* What has been done since the last meeting?
* What are planning to do for the next meeting?
* Are there any obstacles or problems preventing from completing tasks?

#### USING COMMUNICATION TOOLS FOR RAPID EXCHANGE OF INFORMATION

`Slack`: A tool for instant messaging, creating channels to discuss a project,
managing files, and integrating with other services.

`Zoom`: A platform for video conferencing and online meetings, which allows
to hold virtual meetings to discuss tasks, problems, and show the results
of work.

Benefits of use:

* Instant communication: the ability to quickly get answers to questions and
  discuss important points.
* Virtual meetings: the convenience of holding meetings without the need for
  physical presence.
* Archiving: the ability to save the history of messages and files for later access.

Recommendations for use:

* Create separate channels for different types of discussions (for example,
  #general for general questions, #development for development discussions, etc.).
* Conducting regular video conferences for in-depth discussion of tasks and problems.

### CHANGE AND CONFIGURATION MANAGEMENT

#### CODE VERSION CONTROL USING GIT VERSION CONTROL SYSTEM

`Git` is used to track changes to a project's source code, control versions,
and collaborate on code.

Work principles:

* Each developer works in his own local copy of the repository.
* Changes are made in separate branches to isolate work on specific tasks.
* Once work on an issue is completed, changes are merged into the main branch
  (for example, master or main).

Benefits of using Git:

* Change history: the ability to track all changes in the code and return to
  previous versions.
* Collaboration: the ability for several developers to work together on one project.
* Task isolation: Each task is implemented in a separate branch, which simplifies
  change management and controls conflicts.

#### MANAGEMENT OF CHANGES IN CONFIGURATION AND CONTINUOUS INTEGRATION

`Continuous Integration` (`CI`) is a software development practice in which code
changes are regularly and automatically merged into a common repository after
passing a set of automated tests.

Work principles:

* Automation: The processes of building, testing and deploying code should
  be fully automated.
* Frequent merges: Code changes are regularly merged into a common repository
  to minimize conflicts and integrate new functionality faster.
* Automated tests: Before merging changes, must run a set of automated
  tests to ensure that code is working correctly.

Benefits of using CI:

* Fast Feedback: Developers get fast feedback on the status of their code.
* Improved code quality: Automated tests help identify errors and problems
  in code.
* Accelerated Delivery: CI speeds up the software delivery process and reduces
  the time between changes and implementation.

### DEVELOPMENT PERFORMANCE MONITORING AND ANALYSIS

#### DEVELOPMENT PERFORMANCE MONITORING

The implementation of a monitoring system allows to track development
performance and identify bottlenecks in the process.

Steps to implement monitoring:

* `Select Tools`: Identify monitoring tools such as code monitoring systems,
  performance analysis tools, etc.
* `Development Process Integration`: Integrate chosen tools into
  development workflow.
* `Custom Metrics`: Define the performance metrics want to track.
* `Monitoring Automation`: Automate the collection and analysis of performance
  metrics to ensure continuous monitoring.

#### ANALYZE MONITORING DATA TO OPTIMIZE AND IMPROVE DEVELOPMENT PRODUCTIVITY

Analysis of monitoring data allows to identify problem areas and take
measures to optimize them.

Monitoring data analysis process:

* `Data Collection`: Collect development performance data from monitoring tools.
* `Data Analysis`: Analyze collected data to identify bottlenecks and problems.
* `Optimization`: Based on the analysis results, take measures to optimize the
  development process.
* `Iterative Process`: Repeat the analysis and optimization process to continually
  improve development performance.

Benefits of analyzing monitoring data:

* `Identification of bottlenecks`: Analysis allows to identify problem areas
  and focus on improving them
* `Improved Productivity`: Data-driven optimization helps improve productivity
  and development efficiency.
* `Making informed decisions`: Data analysis provides information to make informed
  decisions to improve the development process.

### PROVIDING TRAINING FOR NEW TEAM MEMBERS AND MAINTAINING KNOWLEDGE WITHIN THE TEAM

#### CREATION OF PROJECT DOCUMENTATION

Creating documentation helps new team members quickly get up to speed on the
project and helps maintain current knowledge within the team.

Steps to create documentation:

* `Determine what information is needed`: Determine what information is needed
  for new team members and to maintain knowledge within the team.
* `Create documents`: Create documents describing the project architecture, code
  structure, deployment instructions, etc.
* `Updating documentation`: Keep documentation up to date, making changes as necessary.

#### PROVIDE TRAINING FOR NEW TEAM MEMBERS

Training new team members helps them quickly get up to speed and become
productive team members.

Training process for new team members:

* `Determine training materials`: Prepare materials that will help new team
  members get comfortable with the project (presentations, video tutorials, etc.).
* `Provide training`: Organize training sessions or one-on-one training for
  new team members.
* `Hands-on training`: Provide new team members with hands-on opportunities
  to apply what they learn in practice.

#### KNOWLEDGE SUPPORT WITHIN THE TEAM

Knowledge support within the team helps preserve and disseminate knowledge
about the project and its features.

Ways to support knowledge within the team:

* `Regular exchanges`: Organize meetings or sessions to share experiences
  and knowledge within the team.
* `Documenting Experience`: Encourage team members to document their
  experiences and best practices.
* `Training Materials`: Prepare training materials and resources for
  self-learning of team members.

### RISK AND RESOURCE MANAGEMENT

#### IDENTIFICATION AND ASSESSMENT OF DEVELOPMENT RISKS

Identifying and assessing risks helps prevent potential problems in
the development process and manage them effectively.

Steps to identify and assess risks:

* `Risk Identification`: Analyze potential threats to the project, such as
  technical problems, requirements changes, resource problems, etc.
* `Probability and impact assessment`: Assess the likelihood of each risk
  occurring and its impact on the project.
* `Develop risk management strategies`: Develop action plans to reduce the
  likelihood and impact of risks or their consequences.

#### PLANNING AND MANAGEMENT OF RESOURCES (PEOPLE, BUDGET, EQUIPMENT)

Resource planning and management helps to effectively use project resources
(people, budget, equipment) to achieve set goals.

Steps to plan and manage resources:

* `Determining Resource Needs`: Determine what resources are required to
  complete the project's tasks.
* `Resource Allocation`: Allocate resources among tasks and team members,
  taking into account their specialization and workload.
* `Budget Management`: Optimize the use of the project budget, monitor
  expenses and make adjustments as necessary.
* `Human Resource Management`: Provide support and motivation to team members,
  manage their workload and performance.
* `Equipment Management`: Ensure the availability and proper use of necessary
  equipment to complete project tasks.

### CONTINUOUS IMPROVEMENT

#### CONDUCTING RETROSPECTIVES TO ANALYZE AND IMPROVE DEVELOPMENT PROCESSES

A retrospective allows the development team to analyze past iterations of work,
identify problems and find ways to solve them to improve processes.

Steps to conduct a retrospective:

* `Preparation`: Prepare an agenda for the retrospective that includes a
  discussion of accomplishments, challenges, and suggestions for improvement.
* `Conducting an analysis`: Discuss past iterations of the work, identifying
  successful practices and problematic areas.
* `Develop an Improvement Plan`: Work with team to identify specific
  steps to improve development processes and create an action plan.
* `Track results`: Monitor the implementation of the improvement plan and
  evaluate its effectiveness in subsequent iterations of work.

#### IMPLEMENTATION OF CHANGES AND NEW PRACTICES TO IMPROVE EFFICIENCY AND QUALITY OF DEVELOPMENT

Implementing changes and new practices helps the development team improve work
processes, increase efficiency and quality of development.

Steps to implement changes and new practices:

* `Plan for Change`: Identify needed changes and new practices that can improve
  development processes.
* `Testing and Adapting`: Test new practices in small areas of the project and
  adapt them as necessary.
* `Implementation and Training`: Introduce new practices into the team's work
  and ensure that team members understand and support them.
* `Monitoring and evaluation`: Track the results of implementing new practices,
  evaluate their effectiveness and make adjustments if necessary.

## LICENSE

This code is provided under the [BSD-like license](https://en.wikipedia.org/wiki/BSD_licenses).

You are free to use, modify and distribute the content for non-commercial purposes.
Just mention the original author and provide a link to this repo.

---
---
