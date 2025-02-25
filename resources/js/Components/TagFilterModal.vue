<script setup>
import { ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
  isOpen: Boolean,
  initialSelectedTags: {
    type: Array,
    default: () => []
  },
  availableTags: {
    type: Array,
    default: () => []
  }
});

const emit = defineEmits(['close', 'update:selectedTags']);
const selectedTags = ref([...props.initialSelectedTags]);

watch(() => props.initialSelectedTags, (newTags) => {
  selectedTags.value = [...newTags]; // Reset selectedTags when prop changes
}, { immediate: true });

const toggleTag = (tag) => {
  const index = selectedTags.value.indexOf(tag.name);
  if (index === -1) {
    selectedTags.value.push(tag.name);
  } else {
    selectedTags.value.splice(index, 1);
  }
};

const clearSelection = () => {
  selectedTags.value = [];
  applyFilters();
};

const applyFilters = () => {
  router.get(
    route('notes.index'),
    { tags: selectedTags.value },
  );
  emit('update:selectedTags', selectedTags.value);
  emit('close');
};
</script>

<template>
  <div v-if="isOpen" class="fixed inset-0 z-50 overflow-y-auto">
    <!-- バックドロップ -->
    <div class="fixed inset-0 bg-black bg-opacity-50" @click="emit('close')"></div>

    <!-- モーダル -->
    <div class="relative min-h-screen flex items-center justify-center p-4">
      <div class="relative bg-white rounded-lg w-full max-w-md p-6">
        <!-- ヘッダー -->
        <div class="flex justify-between items-center mb-6">
          <h3 class="text-lg text-primary-dark font-semibold">タグで絞り込む</h3>
          <button @click="emit('close')" class="text-gray-400 hover:text-gray-500">
            <span class="sr-only">Close</span>
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <!-- タグ選択 -->
        <div class="space-y-4">
          <div class="flex flex-wrap gap-2">
            <button
              v-for="tag in availableTags"
              :key="tag.id"
              @click="toggleTag(tag)"
              :class="[
                'px-3 py-1 rounded-full text-sm font-medium transition-colors',
                selectedTags.includes(tag.name)
                  ? 'bg-primary text-white'
                  : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
              ]"
            >
              # {{ tag.name }}
            </button>
          </div>
          <div v-if="availableTags.length === 0" class="flex justify-center">
            <p class="text-sm text-gray-500">まだタグがありません</p>
          </div>
        </div>

        <!-- フッター -->
        <div class="mt-6 flex justify-between">
          <button
            @click="clearSelection"
            class="text-gray-600 hover:text-gray-800 text-sm font-semibold"
          >
            選択をクリア
          </button>
          <div class="space-x-3">
            <button
              @click="emit('close')"
              class="px-4 py-2 text-sm font-semibold text-gray-700 hover:text-gray-800"
            >
              キャンセル
            </button>
            <button
              @click="applyFilters"
              class="px-4 py-2 text-sm font-semibold bg-primary text-white rounded-md hover:bg-primary-light hover:text-primary-dark"
            >
              確認
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>