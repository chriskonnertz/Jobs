# Jobs

Simple Cron job manager. Register jobs and the job manager will execute them automatically depending on their interval.

> NOTE: This is not a queue manager and therefore this has nothing to do with Laravel's queue component. Also note that Laravel 5 has an integrated [task scheduler](https://laravel.com/docs/5.5/scheduling) that works similar to this library.

## Installation

Add `chriskonnertz/jobs` to `composer.json`:

    "chriskonnertz/jobs": "3.*"
    
Or via a console:

```
composer require chriskonnertz/jobs
```

Run `composer update` to get the latest version of this library.

### Framework support

In Laravel 5 there is a service provider. Add the service provider to the config file `config/app.php`:

```php
    'providers' => array(
        // ...
        'ChrisKonnertz\Jobs\Integration\JobsServiceProvider',
    ),
```

To create an alias for the facade, add this new entry in this file:

```php
    'aliases' => array(
        // ...
        'Jobs' => 'ChrisKonnertz\Jobs\Integration\JobsFacade',
        'AbstractJob' => 'ChrisKonnertz\Jobs\AbstractJob',
    ),
```

## Introduction

Create a job:
```php
    class ExampleJob extends ChrisKonnertz\Jobs\AbstractJob 
    {

        protected $name = 'exampleJob';

        protected $interval = 5; // Run every five minutes

        public function run($executedAt)
        {
            echo 'Hello World!';
        }

    }
```

Instantiate the job manager:
```php
    $cache = new ExampleCacheClass;
    $jobs = new ChrisKonnertz\Jobs\Jobs($cache);
```

> If you use Laravel with the service provider you do not have to worry about this. The service provider will inject the cache dependency. In any other case the cache class has to implement the cache interface (`CacheInterface`). Take a look at the `LaravelCache` class (that is meant for Laravel integration) for an example implementation.

Register the job:
```php
    $jobs->addLazy('updateStreams', 'ExampleJob');
```

Execute the registered jobs:
```php
    $jobs->run();
```

> If your application is built on top of Laravel, you will have access to an Artisan command: `php artisan jobs` This command will call `Jobs::run()` to execute the jobs. Therefore you can add a Cron job to start the command, for example `10 * * * * php /var/www/laravel/artisan jobs`. This will execute the Artisan command every ten minutes. We recommend to run the Cron job every minute.

## Methods of the jobs manager

> NOTE: Some of these methods are able to throw a `JobException`.

### Set the cache key namespace
```php
    $jobs->cacheKey('jobs.');
```

### Set the minimum cool down time for all jobs
```php
    $jobs->coolDown(1); // Minute
```

The default value is one minute. Most likely there is no reason to change this value ever.

### Determine if a job exists in the pool
```php
    $hasJob = $jobs->has('exampleJob');
```

### Add a job to the pool (without lazy loading)
```php
    $job = new ExampleJob;
    $jobs->add($job);
```

### Add a job to the pool (with lazy loading)
```php
    // Pass the class name:
    $jobs->addLazy('My\Example\Job');

    // Or pass a closure:
    $jobs->addLazy(function()
    {
        return new ExampleJob;
    });
```

### Remove a job from the pool
```php
    $jobs->remove('exampleJob');
```

### Remove all jobs from the pool
```php
    $jobs->clear();
```

### Count the jobs
```php
    $howMany = $jobs->count();
```

### Get the remaining cool down
```php
$minutes = $jobs->remainingCoolDown();
```
    
### Get the timestamp of the last iteration
```php
$timestamp =  $jobs->lastRunAt();
```

## The job class

A job class implements the job interface. Therefore it has to implement these methods:

```php
    interface JobInterface 
    {

        public function getName(); // The name (identifier) of the job

        public function getActive(); // Active or paused (=not executed)?

        public function getInterval(); // The cool down time

        public function run($executed); // The run method

    }
```

The `Job` class actually implements these methods. It provides the attributes `name`, `active` and `interval` that inheriting classes can overwrite.

### The interval

Per default (as long as the inheriting job class does not overwrite it) the `getInterval()` is a simple getter 
for the `interval` attribute. The `interval` attribute defines the duration of the job's cool down in minutes. For example if it is `60` minutes (= `1` hour) the job is executed once per hour (max).

## Status

Status of this repository: **Maintained**. Create an issue and you will get a response, usually within 48 hours.
