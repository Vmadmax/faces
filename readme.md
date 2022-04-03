## Start project

### 1. Get submodules
```shell script
git submodule update --init --recursive
```

### 2. copy .env file
```shell script
cp laravel/.env.example laravel/.env
```

### 3. create symlink to .env file
```shell script
ln -s laravel/.env .env
```

### 4. make docker exec file executable
```shell script
chmod +x bin/docker-exec
```

### 5. start docker-container
```shell script
docker compose up
```

### 6. Composer
```shell script
bin/docker-exec composer install
```

### 7. NPM
```shell script
bin/docker-exec npm install
```

### 8. Compile assets
```shell script
bin/docker-exec watch
```

### 9. run migrations
```shell script
bin/docker-exec php artisan migrate
```

## Visit the Page
You can visit the Site via: http://127.0.0.1:8080

## Deployment behind webserver auth
Add this config to file: `laravel/config/opcache.php`<br>
Replace "xxx" with your credentials
```text
"options" => [
    'username' => 'xxx',
    'password' => 'xxx'
]
```

## Update bin dependencies

If you want to update your bin dependencies you need to checkout a specific commit and pull it from git.

**!Attention!:** Never checkout the master and push!

```shell script
cd bin/
git fetch
git checkout -q <new commit id>
git pull origin <new commit id>
cd ..
git add bin
git commit -m "Update docker exec submodule"
git push origin <current branch name>
```

## References

See also: https://github.com/Duplexmedia/docker-exec
