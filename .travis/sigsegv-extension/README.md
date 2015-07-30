## What is this?

This is an extension written for testing purposes.


## Provided functionalities

| Function  | Expected result                  |
|-----------|----------------------------------|
| sigsegv() | segmentation fault (core dumped) |


## Build

```
phpize
./configure --with-sigsegv
make -j
```

## Usage

```
php -d extension=modules/sigsegv.so -r "sigsegv();"
```
