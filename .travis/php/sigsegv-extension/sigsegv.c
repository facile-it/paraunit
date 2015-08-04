#include "php_sigsegv.h"

PHP_FUNCTION (sigsegv) {
    char *damn_pointer = "I am a damn pointer";
    *damn_pointer = "So bad!";
}

PHP_MINIT_FUNCTION (sigsegv) {
    return SUCCESS;
}

const zend_function_entry sigsegv_functions[] = {
PHP_FE(sigsegv, NULL)
PHP_FE_END};

zend_module_entry sigsegv_module_entry = {
STANDARD_MODULE_HEADER,
PHP_SIGSEGV_NAME,
sigsegv_functions,
PHP_MINIT(sigsegv),
NULL,
NULL,
NULL,
NULL,
PHP_SIGSEGV_VERSION,
STANDARD_MODULE_PROPERTIES };

#ifdef COMPILE_DL_SIGSEGV
ZEND_GET_MODULE(sigsegv)
#endif
