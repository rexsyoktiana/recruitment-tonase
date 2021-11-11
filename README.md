# Installation
1.  Clone your project.
2.  Go to the folder application using cd command on your cmd or terminal.
3.  Run composer install on your cmd or terminal.
4.  Copy .env.example file to .env on the root folder.
5.  Create a database with the name recruitment-tonase.
6.  edit file .env:
    * MAIL_USERNAME=your_email
    * MAIL_PASSWORD=your_password
    * MAIL_FROM_NAME=TONASE
7.  Set up e-mail: Allow less secure apps: ON
8.  Run php artisan key:generate.
9.  Run php artisan migrate.
10.  Run php artisan serve.
