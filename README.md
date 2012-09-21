# Twig Shell Bundle

Provides a simple Twig REPL

## Example

    $ ./app/console twig:shell
    twig > 512*2
    1024
    twig > 512*2|number_format()
    1024
    twig > (512*2)|number_format()
    1,024
    twig >

## Install

```
$ composer require alb/twig-shell-bundle
```

When asked for a version constraint, type `*` and hit enter.

## Enable the bundle

Enable the bundle in the kernel:

``` php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new Alb\TwigShellBundle\AlbTwigShellBundle(),
    );
}
```
