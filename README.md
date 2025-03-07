# Laravel Migration Actions

![the dragon code migration actions](https://preview.dragon-code.pro/the-dragon-code/migration-actions.svg?brand=laravel)

[![Stable Version][badge_stable]][link_packagist]
[![Unstable Version][badge_unstable]][link_packagist]
[![Total Downloads][badge_downloads]][link_packagist]
[![Github Workflow Status][badge_build]][link_build]
[![License][badge_license]][link_license]

> Actions are like version control for your migration process, allowing your team to modify and share the application's actionable schema. If you have ever had to tell a teammate to manually perform any action on a producton server, you've come across an issue that actions solves.

## Installation

To get the latest version of Laravel Actions, simply require the project using [Composer](https://getcomposer.org):

```bash
$ composer require dragon-code/laravel-migration-actions
```

Or manually update `require` block of `composer.json` and run `composer update`.

```json
{
    "require": {
        "dragon-code/laravel-migration-actions": "^2.6"
    }
}
```

### Upgrade From `dragon-code/laravel-actions`

1. In your `composer.json` file, replace `dragon-code/laravel-actions` with `dragon-code/laravel-migration-actions`.
3. Run the `command composer` update.
4. Profit!

### Upgrade From `andrey-helldar/laravel-actions`

1. In your `composer.json` file, replace `"andrey-helldar/laravel-actions": "^1.0"` with `"dragon-code/laravel-migration-actions": "^2.0"`.
2. Replace the `Helldar\LaravelActions` namespace prefix with `DragonCode\LaravelActions` in your app;
3. Run the `command composer` update.
4. Profit!

#### Laravel Framework

Nothing else needs to be done. All is ready 😊

#### Lumen Framework

This package is focused on Laravel development, but it can also be used in Lumen with some workarounds. Because Lumen works a little different, as it is like a barebone version of
Laravel and the main configuration parameters are instead located in `bootstrap/app.php`, some alterations must be made.

You can install `Laravel Actions` in `app/Providers/AppServiceProvider.php`, and uncommenting this line that registers the App Service Providers so it can properly load.

```
// $app->register(App\Providers\AppServiceProvider::class);
```

If you are not using that line, that is usually handy to manage gracefully multiple Lumen installations, you will have to add this line of code under
the `Register Service Providers` section of your `bootstrap/app.php`.

```php
$app->register(\DragonCode\LaravelActions\ServiceProvider::class);
```

## How To Use

### Creating Actions

To create a migration, use the `make:migration:action` Artisan command:

```
php artisan make:migration:action my_action
```

The new action will be placed in your `database/actions` directory. Each action file name contains a timestamp, which allows Laravel to determine the order of the actions.

> At the first start, you need to create a table by running the `migrate:actions:install` command.
>
> If you execute `migrate:actions` with the first command, the `migrate:actions:install` command will be called automatically.

#### Automatically Generate A File Name

If you are not worried about the names of your files, then in version [2.6](https://github.com/TheDragonCode/laravel-migration-actions/releases/tag/v2.6.0) we added the ability to
automatically generate file names.

Just don't include the name attribute when creating the migration.

If a git repository is found in the main folder, then the name of the current active branch will be taken as a prefix:

```bash
php artisan make:migration:action

### Before Laravel 8.37
# 2022_01_28_184116_main_1643384476.php
# 2022_01_28_184117_main_1643384477.php
# 2022_01_28_184118_crm_2345_1643384478.php
# 2022_01_28_184119_crm_2345_1643384479.php

### Laravel 8.37 or higher
# 2022_01_28_184116_main.php
# 2022_01_28_184117_main.php
# 2022_01_28_184118_crm_2345.php
# 2022_01_28_184119_crm_2345.php
```

If the git repository is not found, then the default prefix will be used:

```bash
php artisan make:migration:action

### Before Laravel 8.37
# 2022_01_28_184116_auto_1643384476.php
# 2022_01_28_184117_auto_1643384477.php
# 2022_01_28_184118_auto_1643384478.php

### Laravel 8.37 or higher
# 2022_01_28_184116_auto.php
# 2022_01_28_184117_auto.php
# 2022_01_28_184118_auto.php
```

If you are using Laravel prior to version [8.37](https://github.com/laravel/framework/releases/tag/v8.37.0), then to ensure backward compatibility, if the current git repository
branch name starts with a number, the `branch` prefix will be automatically added to it:

```bash
php artisan make:migration:action
```

```php
/* 2022_01_28_184116_branch_2x_1643384476.php */
class Branch2x1643384476 extends Actionable { }
```

### Running actions

To run all of your outstanding actions, execute the `migrate:actions` Artisan command:

```
php artisan migrate:actions
```

#### Forcing Actions To Run In Production

Some action operations are destructive, which means they may cause you to lose data. In order to protect you from running these commands against your production database, you will
be prompted for confirmation before the commands are executed. To force the commands to run without a prompt, use the `--force` flag:

```
php artisan migrate:actions --force
```

#### Execution Every Time

In some cases, you need to call the code every time you deploy the application. For example, to call reindexing.

To do this, override the `$once` variable in the action file:

```php
use DragonCode\LaravelActions\Support\Actionable;

return new class extends Actionable
{
    protected $once = false;

    public function up(): void
    {
        // your code
    }
};
```

If the value is `$once = false`, the `up` method will be called every time the `migrate:actions` command called.

In this case, information about it will not be written to the `migration_actions` table and, therefore, the `down` method will not be called when the rollback command is called.

#### Execution In A Specific Environment

In some cases, it becomes necessary to execute an action in a specific environment. For example `production`.

For this you can use the `$environment` parameter:

```php
use DragonCode\LaravelActions\Support\Actionable;

return new class extends Actionable
{
    /** @var string|array|null */
    protected $environment = 'production';

    public function up(): void
    {
        // your code
    }
};
```

You can also specify multiple environment names:

```php
use DragonCode\LaravelActions\Support\Actionable;

return new class extends Actionable
{
    /** @var string|array|null */
    protected $environment = ['testing', 'staging'];

    public function up(): void
    {
        // your code
    }
};
```

By default, the action will run in all environments. The same will happen if you specify `null` or `[]` as the value.

#### Execution Excluding Certain Environments

In some cases, it becomes necessary to execute an action excluding certain environments. For example `production`.

For this you can use the `$except_environment` parameter:

```php
use DragonCode\LaravelActions\Support\Actionable;

return new class extends Actionable
{
    /** @var string|array|null */
    protected $except_environment = 'production';

    public function up(): void
    {
        // your code
    }
};
```

You can also specify multiple environment names:

```php
use DragonCode\LaravelActions\Support\Actionable;

return new class extends Actionable
{
    /** @var string|array|null */
    protected $except_environment = ['testing', 'staging'];

    public function up(): void
    {
        // your code
    }
};
```

By default, no actions will be excluded. The same happens if you specify `null` or `[]` value.

#### Database Transactions

In some cases, it becomes necessary to undo previously performed actions in the database. For example, when code execution throws an error. To do this, the code must be wrapped in
a transaction.

By setting the `$transactions = true` parameter, you will ensure that your code is wrapped in a transaction without having to manually call the `DB::transaction()` method. This
will reduce the time it takes to create the action.

```php
use DragonCode\LaravelActions\Support\Actionable;

return new class extends Actionable
{
    protected $transactions = true;

    protected $transaction_attempts = 3;

    public function up(): void
    {
        // ...

        $post = Post::create([
            'title' => 'Random Title'
        ]);

        $post->tags()->sync($ids);
    }
};
```

### Rolling Back Actions

To roll back the latest action operation, you may use the `rollback` command. This command rolls back the last "batch" of actions, which may include multiple action files:

```
php artisan migrate:actions:rollback
```

You may roll back a limited number of actions by providing the `step` option to the rollback command. For example, the following command will roll back the last five actions:

```
php artisan migrate:actions:rollback --step=5
```

The `migrate:actions:reset` command will roll back all of your application's migrations:

```
php artisan migrate:actions:reset
```

### Roll Back & Action Using A Single Command

The `migrate:actions:refresh` command will roll back all of your migrations and then execute the `migrate:actions` command. This command effectively re-creates your entire
database:

```
php artisan migrate:actions:refresh
```

You may roll back & re-migrate a limited number of migrations by providing the `step` option to the `refresh` command. For example, the following command will roll back &
re-migrate the last five migrations:

```
php artisan migrate:actions:refresh --step=5
```

### Drop All Actions & Migrate Actions

The `migrate:actions:fresh` command will drop all actions records from the actions table and then execute the migrate command:

```
php artisan migrate:actions:fresh
```

### Actions Status

The `migrate:actions:status` command displays the execution status of actions. In it you can see which actions were executed and which were not:

```
php artisan migrate:actions:status
```

### Execution Status

You can also override the `success` and `failed` methods, which are called on success or failure processing.

#### If Success

```php
use DragonCode\LaravelActions\Support\Actionable;
use Illuminate\Support\Facade\Log;

return new class extends Actionable
{
    public function up(): void
    {
       //
    }

    public function down(): void
    {
       //
    }

    public function success(): void
    {
       Log::info('success');
    }

    public function failed(): void
    {
       Log::info('failed');
    }
};
```

Call the `php artisan migrate:actions` command.

The log file will contain two `success` entries.

#### If Failed

```php
use DragonCode\LaravelActions\Support\Actionable;
use Exeption;
use Illuminate\Support\Facade\Log;

return new class extends Actionable
{
    public function up(): void
    {
       throw new Exeption();
    }

    public function down(): void
    {
       throw new Exeption();
    }

    public function success(): void
    {
       Log::info('success');
    }

    public function failed(): void
    {
       Log::info('failed');
    }
};
```

Call the `php artisan migrate:actions` command.

The log file will contain two `failed` entries.

### Artisan Command

Quite often, when working with actions, it becomes necessary to run one or another console command, and each time you have to write the following code:

```php
use DragonCode\LaravelActions\Support\Actionable;
use Illuminate\Support\Facades\Artisan;

return new class extends Actionable
{
    public function up()
    {
        Artisan::call('command-name', [
            // parameters
        ]);
    }
};
```

Since version [`2.3`](https://github.com/TheDragonCode/laravel-migration-actions/releases/tag/v2.3.0) we have added a method call. Now calling commands has become much easier:

```php
use DragonCode\LaravelActions\Support\Actionable;

return new class extends Actionable
{
    public function up()
    {
        $this->artisan('command-name', [
            // parameters
        ]);
    }
};
```

## License

This package is licensed under the [MIT License](LICENSE).


[badge_build]:          https://img.shields.io/github/workflow/status/TheDragonCode/laravel-migration-actions/phpunit?style=flat-square

[badge_downloads]:      https://img.shields.io/packagist/dt/dragon-code/laravel-migration-actions.svg?style=flat-square

[badge_license]:        https://img.shields.io/packagist/l/dragon-code/laravel-migration-actions.svg?style=flat-square

[badge_stable]:         https://img.shields.io/github/v/release/TheDragonCode/laravel-migration-actions?label=stable&style=flat-square

[badge_unstable]:       https://img.shields.io/badge/unstable-dev--main-orange?style=flat-square

[link_build]:           https://github.com/TheDragonCode/laravel-migration-actions/actions

[link_license]:         LICENSE

[link_packagist]:       https://packagist.org/packages/dragon-code/laravel-migration-actions
