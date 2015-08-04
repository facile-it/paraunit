dnl config.m4 for extension sigsegv

PHP_ARG_WITH(sigsegv, whether to enable sigsegv support,
[  --enable-sigsegv       Enable sigsegv])

if test "$PHP_SIGSEGV" != "no"; then
  PHP_NEW_EXTENSION(sigsegv, sigsegv.c, $ext_shared)
fi
