<script setup>
import { Dialog, DialogPanel, DialogTitle } from '@headlessui/vue';

const props = defineProps({
  isOpen: {
    type: Boolean,
    required: true
  },
  note: {
    type: Object,
    required: true
  }
});

const emit = defineEmits(['close', 'delete']);

const handleDelete = () => {
  emit('delete', props.note.id);
  emit('close');
};
</script>

<template>
  <Dialog
    :open="isOpen"
    @close="emit('close')"
    class="relative z-[200]"
  >
    <div class="fixed inset-0 z-[190]">
      <div class="fixed inset-0 bg-black/30" aria-hidden="true" />
      
      <div class="fixed inset-0 flex items-center justify-center p-4 z-[200]">
        <DialogPanel class="w-full max-w-sm rounded bg-white p-6 shadow-xl">
          <DialogTitle class="text-lg font-medium mb-4">
            ノートの削除
          </DialogTitle>
          
          <p class="mb-4">
            このノートを削除してもよろしいですか？
          </p>
          
          <div class="flex justify-end gap-3">
            <button
              @click="emit('close')"
              class="px-4 py-2 text-gray-600 hover:text-gray-800"
            >
              キャンセル
            </button>
            <button
              @click="handleDelete"
              class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700"
            >
              削除
            </button>
          </div>
        </DialogPanel>
      </div>
    </div>
  </Dialog>
</template>