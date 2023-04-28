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
- ~~Email~~
  - ~~SMTP~~
  - ~~Email Settings~~
  - ~~Templates~~
- ~~Create User~~
- User API store test
- Edit User
  - User form
  - Update user
- Remove User
  - ~~Remove~~
  - Check if self remove & role level
- Forgot password
  - Reset password
    - Reset Password request
    - Reset Password email
    - Preview on email page
  - Confirm password
- Create **role**
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
- Pages
  - How to help
  - terms of service
  - Privacy policy
  - Corp. address
- Language settings
  - ~~Add language~~
  - ~~Select main language~~
  - ~~Remove language package~~
  - Translation files
  - View translations form
  - Save translations
  - Apply translations to pages
    - Notifications
    - User list
    - Role list
    - Main settings
    - Login page
    - Language settings
    - Forms
      - Button settings
      - Image upload
    - Admin Menu
  - app:reset command

### Technical debt

- Dashboard
  - Main settings
    - Site custom URL
  - LoginRequest on AuthController
    - translations
  - Send Course Invitation email when user finished his account activation
  - Send Course Invitation email after student update
  - /dashboard/users/create - add courses if role is "student"
  - /dashboard/settings/emails - check superuser role for CKEDITOR "Source" button
    - remove, create email template can only superuser
  - Registration email
    - Apply variables (Course, Date)
    - Welcome email (registration)
    - Preview on email page
    - Activation URL
  - Course Invitation email & Preview
  - Check coaches and admins has avatars when the communities are allowed