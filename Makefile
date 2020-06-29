work-start:	d-up install m-up

d-up:
	docker-compose -f ../docker-compose.yml up -d

d-down:
	docker-compose -f ../docker-compose.yml down

m-up:
	docker exec php-bk2 php artisan migrate

m-down:
	docker exec php-bk2 php artisan migrate:rollback

m-status:
	docker exec php-bk2 php artisan migrate:status

m-reset:
	docker exec php-bk2 php artisan migrate:reset

migrate-up:
	docker exec php-bk2 php artisan migrate

m-roll:
	docker exec php-bk2 php artisan migrate:rollback

migrate-reset:
	docker exec php-bk2 php artisan migrate:reset

tinker:
	docker exec -ti php-bk2 php artisan tinker

clear-cache:
	docker exec -ti php-bk2 php artisan cache:clear

artisan:
	docker exec -ti php-bk2 php artisan $(filter-out $@,$(MAKECMDGOALS))

cmd:
	docker exec -ti php-bk2 php artisan $(cmd)

autoload:
	docker exec -ti php-bk2 composer dump-autoload

install:
	docker exec -ti php-bk2 composer install --ignore-platform-reqs

yarn-install:
	yarn install

test:
	docker exec php-bk2 /usr/local/bin/php /var/www/kassatka/vendor/phpunit/phpunit/phpunit

test-one:
	docker exec php-bk2 /usr/local/bin/php /var/www/kassatka/vendor/phpunit/phpunit/phpunit $(file)

test-one-filter:
	docker exec php-bk2 /usr/local/bin/php /var/www/kassatka/vendor/phpunit/phpunit/phpunit --filter $(method) $(file)

seed:
	docker exec php-bk2 php artisan db:seed

queue-worker:
	docker-compose -f ../docker-compose.yml exec lvll php artisan queue:work

a2-workers-stop:
	docker-compose -f ../docker-compose.yml stop bk2-worker

reduce_generate_watcher:
	docker-compose -f ../docker-compose.yml exec lvll php artisan ReduceByProducesCreatorDaemon start

acc_price:
	docker-compose -f ../docker-compose.yml exec lvll php artisan AccountingPriceRecountDaemon $(filter-out $@,$(MAKECMDGOALS))

a2-workers-build:
	docker-compose -f ../docker-compose.yml build bk2-worker

restart-diff:
	docker stop a2-worker-WAREHOUSE_INVENTORY_DIFF
	docker rm a2-worker-WAREHOUSE_INVENTORY_DIFF
	docker-compose -f ../docker-compose.yml run -d --name="a2-worker-WAREHOUSE_INVENTORY_DIFF" -e LISTEN_QUEUE_NAME="WAREHOUSE_INVENTORY_DIFF" bk2-worker
# 	docker stop "$cont"
# 	docker rm "$cont"