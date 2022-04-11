# therapy-scheme-generator

### Configuration
#### Database 
In the file `.env` value of constant DATABASE_URL replace with correct values:
- `"mysql://<username>:<password>@<address>:<port>/<db_name>?serverVersion=<8.0>&charset=utf8mb4"`

#### Install composer & symfony dependencies 
[Composer Download & Installation](https://getcomposer.org/download/)

After installation go to project root directory and run:

- `composer install`

This will install all needed symfony and third-party libraries & 'bin/console' app so next:

- `php bin/console doctrine:database:create` (if database isn't exists)
- `php bin/console doctrine:migrations:migrate`
- `php bin/console doctrine:fixtures:load` (if you want to load test data)
- `yarn install` or `npm install` to install and compile assets
- `yarn encore dev` or `npm encore dev` this will build and compile assets
