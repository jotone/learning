# Installation

1. ```bash
   apt-get install make node docker docker-compose php8.2
   ```
2. ```bash
   sudo groupadd docker
   ```
3. ```bash
   sudo usermod -aG docker $USER && sudo systemctl enable docker.service && sudo systemctl enable containerd.service
   ```
4. ```bash
   php artisan sail:install
   ```
5. Choose sail package for mysql (option 0)
6. ```bash
   make up && make reset
   ```
   
## Makefile Commands

- make cmd run=`<your artisan command>` - will run custom artisan command
- up - start up sail docker containers
- down - stop docker containers
- migrate - run artisan migrate
- reset - reset and seed environment
- test - run tests

### TODO

- ~~Role list page~~
- ~~User list page~~
- ~~Main Settings page~~
- ~~Login Settings page~~
- Remove Auth\RegisteredUserController
- Login page
- Logout
- Sanctum middleware
- Separate dashboard middleware
- Create User
- Edit User
- Create role
- Edit role
- Remove role
- Remove User
- Coaches list page
- Create Coach
- Edit Coach
- Remove Coach
- Admin Profile
- Login page settings -> preview

### Technical debt

- Dashboard
  - Main settings
    - Site custom URL