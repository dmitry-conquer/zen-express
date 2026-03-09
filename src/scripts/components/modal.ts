type ModalContext = {
  isOpen: boolean;
  $refs: { dialog: HTMLDialogElement };
};

export default function () {
  return {
    isOpen: false,

    open(this: ModalContext) {
      this.isOpen = true;
      this.$refs.dialog.showModal();
    },

    close(this: ModalContext) {
      this.isOpen = false;
      this.$refs.dialog.close();
    },
  };
}
