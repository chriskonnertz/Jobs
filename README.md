# Jobs

Simple cron job manager

## Installation

Add `chriskonnertz/jobs` to `composer.json`:

    "chriskonnertz/jobs": "dev-master"

Run `composer update` to get the latest version of Jobs.

### Framework Support

In Laravel 4 you may add aliases to `app/config/app.php`:
```php
    'aliases' => array(
        // ...
        'Jobs' => 'ChrisKonnertz\Jobs\Jobs',
        'Job'  => 'ChrisKonnertz\Jobs\Job',
    ),
```

> In Laravel 5 the path to this file is `config/app.php`.

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

        public function run($executed)
        {
            echo 'Hello World!';
        }

    }
```

Instantiate the jobs manager:
```php
    $cache = new ExampleCacheClass;
    $jobs = new ChrisKonnertz\Jobs\Jobs($cache);
```

> If you use Laravel and the service provider you do not have to care about this. The service provider will inject the cache dependency.

Register the job:
```php
    $jobs->addLazy('updateStreams', 'ExampleJob');
```

Execute the registered jobs:
```php
    $jobs->run();
```

If your application is built on top of Laravel, you have also access to the Artisan command: `php artisan jobs`

## Methods Of The Jobs manager

> NOTE: Some of these methods are able to throw a JobException.

### Set The Cache Key Namespace
```php
     $jobs->cacheKey('jobs.');
```

### Set The Cool Down Time Span
```php
     $jobs->timeSpan(60); // Seconds
```

### Determine If A Job Exists In The Pool
```php
    $hasJob = $jobs->has('exampleJob');
```

### Add A Job To The Pool (Without Lazy Loading)
```php
    $job = new ExampleJob;
    $jobs->add($job);
```

### Add A Job To The Pool (With Lazy Loading)
```php
    // Pass the class name:
    $jobs->addLazy('My\Example\Job');

    // Pass a closure:
    $jobs->addLazy(function()
    {
        return new ExampleJob;
    });
```

### Remove A Job From The Pool
```php
    $jobs->remove('exampleJob');
```

### Remove All Jobs From The Pool
```php
    $jobs->clear();
```

### Count The Jobs
```php
    $howMany = $jobs->count();
```

## The Job Class

A job class implements the job interface. Therefore it has to implement these methods:

```php
    interface JobInterface {

        public function getName(); // The name (identifier) of the job

        public function getActive(); // Active or paused (=not executed)?

        public function getTimeSpan(); // The cool down time span

        public function run($executed); // The run method

    }
```

The Job class implements these methods. It provides the attributes `name`, `active` and `timeSpan` that can be overwritten by inheriting classes.