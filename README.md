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
