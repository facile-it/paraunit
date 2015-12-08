#include "hphp/runtime/ext/extension.h"
#include <signal.h>

namespace HPHP {

void HHVM_FUNCTION(sigsegv) {
    raise(SIGSEGV);
}

static class SigSegvExtension : public Extension {
 public:
  SigSegvExtension() : Extension("sigsegv") {}
  virtual void moduleInit() {
    HHVM_FE(sigsegv);
    loadSystemlib();
  }
} s_sigsegv_extension;

HHVM_GET_MODULE(sigsegv)

} // namespace HPHP