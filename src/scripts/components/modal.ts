export default function () {
  return {
    isOpen: false,

    open() {
      this.isOpen = true;
      (this.$refs.dialog as HTMLDialogElement).showModal();
    },

    close() {
      this.isOpen = false;
      (this.$refs.dialog as HTMLDialogElement).close();
    },
  };
}
