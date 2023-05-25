# Installation Ubuntu/Debian

1. **Install necessary programs**
```shell
sudo apt-get install make node docker docker-compose
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
DB_DATABASE=learning
DB_USERNAME=sail
DB_PASSWORD=password
```
7. **Install sail**
```shell
php artisan sail:install
```
8. **Choose sail package for mysql (option 0)**
9. **Set environment key**
```shell
php artisan key:generate
```
10. **Enable containers and run migrations with installation data**
```shell
make up && make install
```
11. **Install npm dependencies and run prod script**
```shell
npm i && npm run prod
```
## Makefile Commands

- `make cmd run=<your artisan command>`** - will run custom artisan command
- `make up` - start up sail docker containers
- `make down` - stop docker containers
- `make migrate` - run artisan migrate
- `make reset` - reset and seed environment
- `make test` - run tests

### TODO

- ~~Role list page~~
- ~~User list page~~
- ~~Main Settings page~~
- ~~Login Settings page~~
- ~~Login page~~
- ~~Sanctum middleware~~
- ~~Email~~
  - ~~SMTP~~
  - ~~Email Settings~~
  - ~~Templates~~
- ~~Create User~~
- ~~Edit User~~
- ~~Remove User~~
- ~~Forgot password~~
  - ~~Reset password~~
  - ~~Confirm password~~
- ~~Create role~~
- ~~Edit role~~
- ~~Remove role~~
- ~~Separate dashboard middleware~~
- ~~Coaches list page~~
- ~~Create Coach~~
- ~~Edit Coach~~
- ~~Remove Coach~~
- ~~Admin Profile~~
- ~~Login page settings -> preview~~
- ~~Language settings~~
- ~~Apply translations to pages~~
- ~~app:reset command~~
- ~~Course Models~~
- Courses List
  - ~~Api~~
  - Page
- Categories
  - Create
  - Edit
  - Remove
- Courses
  - Create
    - digistore24 list
    - copecart list
  - Edit
  - Clone
  - Remove

### Technical debt

- Dashboard
  - Main settings
    - Site custom URL
  - Send Course Invitation email when user finished his account activation
  - Send Course Invitation email after student update
  - /dashboard/users/create - add courses if role is "student"
  - Registration email
    - Apply variables (Course, Date)
    - Welcome email (registration)
    - Preview on email page
    - Activation URL
  - Course Invitation email & Preview
  - Check coaches and admins has avatars when the communities are allowed
    formatOption

[//]: # (dashboard    manifest    vendor)
[//]: # (   736.9k        1.5k    120.9k <--- 16.05.2022)
[//]: # (   742.8k        1.5     120.9k <--- 24.05.2022)
