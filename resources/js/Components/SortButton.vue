<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'
import { router } from '@inertiajs/vue3'

const props = defineProps({
    initialSort: {
        type: String,
        default: 'created_at'
    },
    initialSelectedTags: {
    type: Array,
    default: () => []
    },
});

const isOpen = ref(false);
const dropdownRef = ref(null);

const sortOptions = [
  { value: 'created_at', label: '作成時間' },
  { value: 'last_opened', label: '最後に開いた' }
];

const selectedOption = computed(() => 
  sortOptions.find(option => option.value === props.initialSort) || sortOptions[0]
);

const handleClickOutside = (event) => {
  if (dropdownRef.value && !dropdownRef.value.contains(event.target)) {
    isOpen.value = false
  }
};

const selectOption = (option) => {
  router.get(
    route('notes.index'),
    { 
        sort: option.value,
        tags: props.initialSelectedTags,
    },
  )
  isOpen.value = false
};

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
});

onBeforeUnmount(() => {
  document.removeEventListener('click', handleClickOutside)
});
</script>

<template>
  <div class="relative" ref="dropdownRef">
    <button
      @click="isOpen = !isOpen"
      class=" px-4 py-2 rounded-md bg-primary-light text-primary-dark focus:outline-none flex items-center font-semibold"
    >
      {{ selectedOption.label }}
      <svg 
        class="w-4 h-4 ml-1 transform transition-transform duration-200"
        :class="{ 'rotate-180': isOpen }"
        fill="none" 
        stroke="currentColor" 
        viewBox="0 0 24 24"
      >
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
      </svg>
    </button>

    <div
      v-if="isOpen"
      class="absolute right-0 mt-2 py-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 z-10"
    >
      <button
        v-for="option in sortOptions"
        :key="option.value"
        @click="selectOption(option)"
        class="w-full px-4 py-2 text-left hover:bg-gray-50"
        :class="{ 'text-primary font-semibold': selectedOption.value === option.value }"
      >
        {{ option.label }}
      </button>
    </div>
  </div>
</template>



