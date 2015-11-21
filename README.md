# Jobs

Simple Cron job manager. Register jobs and the job manager will execute them depending on their cool down time.

> NOTE: This is not a queue manager and therefore this has nothing to do with Laravel's queue component. Also note that Laravel 5 has an integrated [job component](http://laravel.com/docs/5.1/scheduling) that works similar to this one.

## Installation

Add `chriskonnertz/jobs` to `composer.json`:

    "chriskonnertz/jobs": "dev-master"

Run `composer update` to get the latest version of Jobs.

### Framework support

In Laravel 5 you may add aliases to `config/app.php`:
```php
    'aliases' => array(
        // ...
        'Jobs' => 'ChrisKonnertz\Jobs\Jobs',
        'Job'  => 'ChrisKonnertz\Jobs\Job',
    ),
```

> In Laravel 4 the path to this file is `app/config/app.php`.

There is also a service provider and a facade. Add the service provider to the config file:

```php
    'providers' => array(
        // ...
        'ChrisKonnertz\Jobs\JobsServiceProvider',
    ),
```

To create an alias for the facade, add a new entry (or replace the one created before):

```php
    'aliases' => array(
        // ...
        'Jobs' => 'ChrisKonnertz\Jobs\JobsFacade',
    ),
```

## Introduction

Create a job:
```php
    class ExampleJob extends ChrisKonnertz\Jobs\Job {

        protected $name = 'exampleJob';

        protected $timeSpan = 5; // Run every five minutes

        public function run($executed)
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

> If you use Laravel with the service provider you do not have to worry about this. The service provider will inject the cache dependency. In any other case the cache class has to implement the cache interface (`CacheInterface`). Take a look at the `CacheWrapper` class (that is meant for Laravel integration) for an example implementation.

Register the job:
```php
    $jobs->addLazy('updateStreams', 'ExampleJob');
```

Execute the registered jobs:
```php
    $jobs->run();
```

> If your application is built on top of Laravel, you will have access to an Artisan command: `php artisan jobs` This command will call `Jobs::run()` to execute the jobs. Therefore you can add a Cron job to start the command, for example `10 * * * * php /var/www/laravel/artisan jobs`. This will execute the Artisan command every ten minutes. Laravel recommends to run the Cron job every minute.

## Methods of the jobs manager

> NOTE: Some of these methods are able to throw a `JobException`.

### Set the cache key namespace
```php
    $jobs->cacheKey('jobs.');
```

### Set the minimum cool down time span for all jobs
```php
    $jobs->timeSpan(1); // Minute
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

    // Pass a closure:
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

## The job class

A job class implements the job interface. Therefore it has to implement these methods:

```php
    interface JobInterface {

        public function getName(); // The name (identifier) of the job

        public function getActive(); // Active or paused (=not executed)?

        public function getTimeSpan(); // The cool down time span

        public function run($executed); // The run method

    }
```

The `Job` class actually implements these methods. It provides the attributes `name`, `active` and `timeSpan` that inheriting classes can overwrite.

### The cool down time span

Per default (as long as the inheriting job class does do not overwrite it) the `getTimeSpan()` is a simple getter 
for the `timeSpan` attribute. The `timeSpan` attribute defines the duration of the job's cool down in minutes. For example if it is `60` minutes (= `1` hour) the job is executed once per hour (max).

> NOTE: The unit has been _seconds_ in older versions!