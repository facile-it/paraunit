# Contributing to Paraunit

## Dev environment
Paraunit comes with a containerized environment which makes contributing really easy.
The project adopted Docker as container technology, you can find an useful guide for the installation process 
[here](https://docs.docker.com/engine/installation/).

The container already has Composer installed globally, and [OhMyZsh](https://github.com/robbyrussell/oh-my-zsh) installed 
as shell for some nice autocompletion features. 

To start contributing:

 * Clone the repo (or your fork):
```
git clone git@github.com:facile-it/paraunit.git
```
 * Move inside the repo folder:
```
cd paraunit
```
* Build and launch the container:
```
docker/setup.sh
```
 * Now you are inside the container! You should install all the dependencies:
```
composer install
```
 * If you want, you can launch the testsuite to check if it's all ready! (the shell has `./bin` in the `$PATH` env variable)
```
phpunit
```
 
And now your're ready! You should always use git commands (pull/commit/push) outside the container, since it does not 
have your GitHub credentials available. 

## Coding Standard

We follow STRICTLY PSR-1 and PSR-2:

 * https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-1-basic-coding-standard.md
 * https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md

When you run `composer install`, a pre-commit Git Hook is moved in place: it checks before every commit that the code is
PSR-2 compliant, and asks you to fix it if it doesn't. **Don't worry!!** You don't have to fix it manually, the script
will suggest to you the command line to fix it automatically using `phpcbf`.

## Travis and other integrations
Paraunit has a Travis build integration in place: every time you open a PR (or each time you commit something, if you 
enable Travis on your account/fork) it will run all the tests to check if it's all ok!

We also have other integrations in place that runs on every PR and every commit on the master branch; namely:

 * [Scrutinizer](https://scrutinizer-ci.com/g/facile-it/paraunit/)
 * [Coveralls](https://coveralls.io/github/facile-it/paraunit?branch=master)
 * [Codeclimate](https://codeclimate.com/github/facile-it/paraunit)
 * [SensioLabs Insights](https://insight.sensiolabs.com/projects/6571b482-6e1d-4e0c-b215-94d757909b20)

## Composer.lock file
We provide the composer.lock file with the project. This is due to the fact that a contributor shouldn't stumble on build
failures that aren't caused by his contribution itself, and have a stable and defined set of dependencies that he can use
to start with; the lock file is never considered when installing Paraunit as a dependency, so it's useful only in the 
Travis build and in the local dev environment.

To better understand this approach, see:
 * Same approach is being applied to [ZendFramework components](https://github.com/zendframework/zendframework/issues/7660)
 * @rdohms [talk about Composer](https://youtu.be/zt2eL4pbVXQ), especially at minute [34:56](https://youtu.be/zt2eL4pbVXQ?t=34m56s)
 * [Twitter discussion](https://twitter.com/rdohms/status/818351828840620032)
