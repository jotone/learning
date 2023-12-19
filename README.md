# Installation Ubuntu/Debian

1. **Install necessary programs**
```shell
sudo apt-get install make docker docker-compose
```
2. **Install php and it's extensions**
```shell
sudo apt-get install php8.2 php8.2-{bcmath,bz2,cgi,cli,common,curl,http,gd,imagick,intl,mbstring,mcrypt,mysql,opcache,raphf,readline,snmp,soap,xml,xmlrpc,xsl,yaml,zip}
```
2.1. **Optional: Download and install latest composer**
```shell
curl -sS https://getcomposer.org/installer -o composer-setup.php
sudo php composer-setup.php --install-dir=/usr/local/bin --filename=composer
```
2.2. **Optional: Download and install latest Node version manager**
```shell
curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.39.3/install.sh | bash
```
close terminal
```shell
nvm install node
```
3. **Create docker group**
```shell
sudo groupadd docker
```
4. **Add your user to docker group and enable docker service**
```shell
sudo usermod -aG docker $USER && sudo systemctl enable docker.service && sudo systemctl enable containerd.service
```
5. **Copy .env file**
```shell
cp .env.example .env
```
6. **Set database variables. For example:**
```
APP_ENV=local
DB_DATABASE=learning
DB_USERNAME=sail
DB_PASSWORD=password
```
7. **Install composer dependecies**
```shell
composer install
```
8. **Install sail**
```shell
php artisan sail:install
php artisan sail:publish
```
9. **Choose sail package for mysql (option 0)**
10. **Set environment key**
```shell
php artisan key:generate
```
11. **Enable containers and run migrations with installation data**
```shell
make up && make install
```
12. **Install npm dependencies and run prod script**
```shell
npm i && npm run prod
```
## Makefile Commands

- `make run cmd=<your artisan command>`** - will run custom artisan command
- `make up` - start up sail docker containers
- `make down` - stop docker containers
- `make migrate` - run artisan migrate
- `make reset` - reset and seed environment
- `make test` - run tests

- Roles
  - ~~Api~~
  - Page
    - List
      - ~~DataTable~~
      - Pagination
      - PerPage
      - Directions
      - Search
      - Remove confirmation
    - Add
      - Notifications
      - Breadcrumbs
    - Edit
    - Remove
- Templates
  - Api
    - List
    - Create
    - Update
    - Remove
- Settings
  - Api
    - List
    - Update
  - Page
    - Main
    - Functionality
    - Email
      - Templates
        - Create
        - Edit
        - Remove
      - SMTP
    - Language
      - Api
      - Page
- Courses
  - Api
    - List
    - Create
    - Update
    - Delete
- Categories
  - Api
    - List
    - Create
    - Update
    - Delete
- Courses
  - Page
    - Category create
    - Category edit
    - Course list
    - Course columns
    - Course Create
    - Course edit
    - Course bulk actions
    - Course filters
- Users
  - Api
    - List
    - Create
    - Update
    - Delete
  - Page
    - List
    - Bulk actions
    - Filters
    - Create
    - Import
    - Courses
    - Personal information
    - Access and usages
- Registration progress
  - Set password
  - User profile
- Week
  - Api
    - Create
    - Update
    - Delete
- Lesson
  - Api
    - Create
    - Update
    - Delete
- Quiz
  - Api
    - Create
    - Update
    - Delete
- Feedback Form
  - Api
    - Create
    - Update
    - Delete
- Course
  - Curriculum
    - Api
      - Sort
    - Page
- Quiz Questions
  - Api
    - Create
    - Update
    - Delete
- Quiz Answers
  - Api
    - Create
    - Update
    - Delete
- Quiz Page
- Feedback Page
- Lesson Page