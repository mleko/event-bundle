# narrator/narrator-bundle

[![Travis CI](https://travis-ci.org/mleko/narrator-bundle.svg?branch=master)](https://travis-ci.org/mleko/narrator-bundle)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/mleko/narrator-bundle/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/mleko/narrator-bundle/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/mleko/narrator-bundle/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/mleko/narrator-bundle/?branch=master)

## Installation

Add project dependency using [Composer](http://getcomposer.org/):

```sh
$ composer require narrator/narrator-bundle
```

Then register bundle by adding it to `app/AppKernel.php` file

```
<?php
// app/AppKernel.php

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // ...

            new Mleko\Narrator\Bundle\NarratorBundle(),
        );

        // ...
    }

    // ...
}
```

## Configure listeners

NarratorBundle loads listeners from dependency container.
To use service as a listener you only need to add tag marking service as listener.

If you had service
```
<service class="Foo\BarBundle\UserInvitationSender">
    // .. arguments, configuration
</service>
```

```
class UserInvitationSender {
    ...
    public function handle(\Foo\BarBundle\UserRegistered $event){
        ...
    }
}
```

To use it as a listener add tag `narrator.listener`.

```
<service class="Foo\BarBundle\UserInvitationSender">
    // .. arguments, configuration
    <tag name="narrator.listener"/>
</service>
```

Listener don't have to define parameter type in handle method.
```
class UserInvitationSender {
    ...
    public function handle($event){
        ...
    }
}
```

Event type can be passed as tag attribute  `event` which should be FQCN of event.
```
<service class="Foo\BarBundle\UserInvitationSender">
    // .. arguments, configuration
    <tag name="narrator.listener" event="Foo\BarBundle\UserRegistered"/>
</service>
```

By default event is passed to method `handle` of registered listener. Method name can be changed using `method` parameter.

```
<service class="Foo\BarBundle\UserInvitationSender">
    // .. arguments, configuration
    <tag name="narrator.listener" event="Foo\BarBundle\UserRegistered" method="handleRegistration"/>
</service>
```

Using `method` parameter it is possible to use single service to handle different events.

```
<service class="Foo\BarBundle\NotificationSender">
    // .. arguments, configuration
    <tag name="narrator.listener" event="Foo\BarBundle\UserRegistered" method="handleRegistration"/>
    <tag name="narrator.listener" event="Foo\BarBundle\UserLoggedIn" method="handleLogin"/>
</service>
```

## Configure event buses

This bundle comes pre-configured with event bus called "default" aliased to `narrator.event_bus`.
You might want to use more buses or reconfigure "default" bus. You can do this via configuration
```
narrator:
  event_bus:
    default:
      resolver:
        type: instanceof
      public: true
    named: ~
```
This configuration defines two buses: "default" and "named". These buses will be registered as `narrator.event_bus.default` and `narrator.event_bus.named`.
`narrator.event_bus.default` will use `InstanceOf` resolver, therefore it will support event inheritance;
`narrator.event_bus.named` will use default configuration based on strict event name comparison.
By default all buses are registered as public services, it is possible to change that on per-bus basis using `public` parameter.
