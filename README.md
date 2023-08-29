# Streamlabs
![img](/resources/img/Dashboard.png)
## Prerequisite
Assume that Laravel has been configurated correctly on your environment.

### Install dependecies
On Unbuntu
```
sudo apt install sqlite3 && sudo apt install redis-server.
```
In `Streamlabs` root directory, run
```
npm install
```

### Seeding database tables
```
php artisan migrate:refresh && php artisan db:seed
```

### Populate cache
```
php artisan make:command PopulateEventCache
```

## How to run
1. Make sure **redis-server** is running.
2. Run `php artisan serve` in one terminal and run `npm run dev` in another terminal.
3. If you haven't registered an account, please click on **Register** button on the top right corner.
4. Once your account has been created, just log in.
5. You should see a message list on dashboard page.

## Explain the design
### DisplayMessage
In this assignment, `DisplayMessage` data is generating during database seeding. In production, we need a backend service that listens to table (e.g. followers and subscribers) update events and process these events. Databases like DynamoDB provide table event stream so just leverage it. Processed data can either be persisted or cached, depending on requirements.

The backend service will also be responsible for summarizing data which can be stored in cache as the data doesn't require persistence and will expire in X days.