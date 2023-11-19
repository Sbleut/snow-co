# Badge Codacy

[![Codacy Badge](https://app.codacy.com/project/badge/Grade/df2f1b91278644dfac6c482cc9f96571)](https://app.codacy.com/gh/Sbleut/snow-co/dashboard?utm_source=gh&utm_medium=referral&utm_content=&utm_campaign=Badge_grade)


## Requirements 

```
PHP 8.1
Composer 2.2.12
npm 9.5.1
Symfony 6.3
symfony cli (optional)
mysql 5.7.36
mailhog 1.0.1 (optional)
```

## Git Clone

In the repository you want to start your projec/app clone the git repository.

```
git clone https://github.com/Sbleut/snow-co.git
```

## Database configuration

In the .env file, change the line corresponding to the corresponding Database, in this case the mysql line.

```
DATABASE_URL=mysql://db_user:db_password@127.0.0.1:3306/db_name?serverVersion=5.7
```

## Database Creation

In Terminal launch following commands

```symfony console doctrine:database:create```

It will specify if DB has bean created

```symfony console doctrine:migrations:migrate```

```symfony console doctrine:fixtures:load```

## Email

For dev purpose we are using Mailhog. 
Switch to your real mail server when going to prod.

###> symfony/mailer ###
##MailHog
MAILER_DSN=smtp://localhost:1025
###< symfony/mailer ###
