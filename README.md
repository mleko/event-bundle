#narrator\narrator-bundle

[![Travis CI](https://travis-ci.org/mleko/narrator-bundle.svg?branch=master)](https://travis-ci.org/mleko/narrator-bundle)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/mleko/narrator-bundle/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/mleko/narrator-bundle/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/mleko/narrator-bundle/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/mleko/narrator-bundle/?branch=master)

##Installation

Add project depdendency using [Composer](http://getcomposer.org/):

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

##Configure listeners

NarratorBundle loads listeners from dependency container.
To use service as a listener you only need to add tag marking service as listener.

If you had service
```
<service class="Foo\BarBundle\UserInvitationSender">
    // .. arguments, configuration
</service>
```

To use it as a listener add tag. Listener tag always have to define parameter event which should be FQCN of event it will listen to.

```
<service class="Foo\BarBundle\UserInvitationSender">
    // .. arguments, configuration
    <tag name="narrator.listener" event="Foo\BarBundle\UserRegistered"/>
</service>
```
