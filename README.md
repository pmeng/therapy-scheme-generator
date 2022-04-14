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
- `yarn install` to install and compile node libraries
- `yarn encore dev` this will build and compile needed assets

### Docker version setup steps
Constant DATABASE_URL in the file `.env` should have a value:
- `"mysql://root:secret@database:3306/therapy_scheme_generator?serverVersion=8.0&charset=utf8mb4"`

Folder '.docker' in the root of the project must be available for writing

- `docker-compose up -d --build --remove-orphans`
- `docker-compose exec php composer install`
- `docker-compose exec php symfony console doctrine:migrations:migrate`
- `docker-compose exec php symfony console doctrine:fixtures:load` (if you want to load test data)

Next steps are on host machine:

- `yarn install`
- `yarn encore dev`

Now you can open in browser http://127.0.0.1:8000/
