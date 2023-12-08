down:
	@vendor/bin/sail down

install:
	@vendor/bin/sail artisan migrate:fresh
	@vendor/bin/sail artisan app:install

migrate:
	@vendor/bin/sail artisan migrate

reset:
	@vendor/bin/sail artisan app:reinstall
	@npm run prod

route:
	@vendor/bin/sail artisan route:list

run:
	@vendor/bin/sail artisan "${cmd}"

test:
	@vendor/bin/sail artisan test

up:
	@vendor/bin/sail up -d