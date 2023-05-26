cmd:
	@vendor/bin/sail artisan "${run}"

down:
	@vendor/bin/sail down

install:
	@vendor/bin/sail artisan migrate:fresh
	@vendor/bin/sail artisan app:install

migrate:
	@vendor/bin/sail artisan migrate

reset:
	@vendor/bin/sail artisan app:reset
	@npm run prod

route:
	@vendor/bin/sail artisan route:list

test:
	@vendor/bin/sail artisan test

up:
	@vendor/bin/sail up -d