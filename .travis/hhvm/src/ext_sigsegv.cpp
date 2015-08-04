namespace HPHP {
void HHVM_FUNCTION(sigsegv) {
  char *damn_pointer = "I am a damn pointer";
  *damn_pointer = "So bad!";
}

class SigsegvExtension : public Extension {
  public:
    SigSegvExtension(): Extension("sigsegv", "0.0.1") {}
    void moduleInit() override {
      HHVM_FE(sigsegv);
      loadSystemLib();
    }
} s_sigsegv_extension;

HHVM_GET_MODULE(sigsegv)
} // namespace HPHP
