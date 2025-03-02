<script setup>
import { ref } from 'vue';
import { Menu, MenuButton, MenuItems, MenuItem } from '@headlessui/vue';
import { EllipsisHorizontalIcon } from '@heroicons/vue/24/outline';
import DeleteNoteModal from '@/Components/DeleteNoteModal.vue';

const props = defineProps({
  note: {
    type: Object,
    required: true
  },
  showDeleteModal: Boolean
});

const emit = defineEmits(['edit', 'delete']);
const showDeleteModal = ref(false);
</script>

<template>
  <div class="relative" @click.stop>
    <Menu as="div">
      <MenuButton class="flex items-center text-gray-400 hover:text-gray-600 focus:outline-none">
        <EllipsisHorizontalIcon class="w-5 h-5" />
      </MenuButton>

      <transition
        enter-active-class="transition duration-100 ease-out"
        enter-from-class="transform scale-95 opacity-0"
        enter-to-class="transform scale-100 opacity-100"
        leave-active-class="transition duration-75 ease-in"
        leave-from-class="transform scale-100 opacity-100"
        leave-to-class="transform scale-95 opacity-0"
      >
        <MenuItems class="absolute right-0 mt-2 w-48 origin-top-right divide-y divide-gray-100 rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none z-10">
          <div class="px-1 py-1">
            <MenuItem v-slot="{ active }">
              <button
                @click="emit('edit')"
                :class="[
                  active ? 'bg-gray-100' : '',
                  'group flex w-full items-center rounded-md px-2 py-2 text-sm text-gray-900'
                ]"
              >
                詳細編集
              </button>
            </MenuItem>
            <MenuItem v-slot="{ active }">
              <button
                @click="showDeleteModal = true"
                :class="[
                  active ? 'bg-gray-100 text-red-600' : 'text-red-500',
                  'group flex w-full items-center rounded-md px-2 py-2 text-sm'
                ]"
              >
                削除
              </button>
            </MenuItem>
          </div>
        </MenuItems>
      </transition>
    </Menu>
    <DeleteNoteModal
      :is-open="showDeleteModal"
      :note="note"
      @close="showDeleteModal = false"
      @delete="(note_id) => emit('delete', note_id)"
    />
  </div>
</template>