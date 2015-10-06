## What is this?

This is an extension written for testing purposes.


## Provided functionalities

| Function  | Expected result                  |
|-----------|----------------------------------|
| sigsegv() | segmentation fault (core dumped) |


## Build

```
hphpize
cmake .
make -j
```

## Usage

```
hhvm -vDynamicExtension.o=sigsegv.so 
```
