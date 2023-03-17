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
- ~~Login page~~
- ~~Sanctum middleware~~
- Language settings
  - Language form
  - Add language
  - Select main language
  - Translation files
  - View translations form
  - Save translations
  - Apply translations to page
  - app:reset command
- Email
  - Smtp settings
    - send test email
  - Need help modal
  - Email Global Settings
  - Social Media Links
    - Superuser
    - Admin
  - Templates
    - Create
      - Form
      - Variables
      - Preview
      - Store
    - Edit
      - Form
      - Preview
      - Update
    - Remove (only superuser)
  - Welcome email (registration)
  - Course Invitation
  - Reset Password
- Create User
  - User form
  - User info
  - Store
  - Registration email
  - Thumbs
- Edit User
  - User form
  - Update user
- Remove User
- Forgot password
  - Reset password
  - Confirm password
- Create role
- Edit role
- Remove role
- Separate dashboard middleware
- Coaches list page
- Create Coach
- Edit Coach
- Remove Coach
- Admin Profile
  - Menu Item
- Login page settings -> preview

### Technical debt

- Dashboard
  - Main settings
    - Site custom URL
  - LoginRequest on AuthController
    - translations
  - Send Course Invitation email when user finished his account activation
  - Send Course Invitation email after student update
  - /dashboard/users/create - add courses if role is "student"