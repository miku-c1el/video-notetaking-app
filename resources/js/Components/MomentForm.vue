<template>
  <div class="border-t p-4 bg-white">
    <form @submit.prevent="handleSubmit" class="space-y-4">
      <div>
        <label for="title" class="block text-sm font-medium text-gray-700">タイトル</label>
        <input
          type="text"
          id="title"
          v-model="form.title"
          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-coral-500 focus:ring-coral-500"
          required
        />
      </div>

      <div>
        <label for="content" class="block text-sm font-medium text-gray-700">内容</label>
        <textarea
          id="content"
          v-model="form.content"
          rows="3"
          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-coral-500 focus:ring-coral-500"
          required
        ></textarea>
      </div>

      <div class="flex justify-between">
        <span class="text-sm text-gray-500">
          タイムスタンプ: {{ formatTime(props.currentTime) }}
        </span>
        <div class="space-x-2">
          <button
            type="button"
            @click="$emit('cancel')"
            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50"
          >
            キャンセル
          </button>
          <button
            type="submit"
            class="px-4 py-2 text-sm font-medium text-white bg-coral-500 rounded-md hover:bg-coral-600"
          >
            保存
          </button>
        </div>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref } from 'vue'

const props = defineProps({
  currentTime: {
    type: Number,
    required: true
  }
})

const emit = defineEmits(['submit', 'cancel'])

const form = ref({
  title: '',
  content: '',
  timestamp: props.currentTime
})

const handleSubmit = () => {
  emit('submit', {
    ...form.value,
    timestamp: props.currentTime
  })
}

const formatTime = (seconds) => {
  const minutes = Math.floor(seconds / 60)
  const remainingSeconds = seconds % 60
  return `${minutes}:${remainingSeconds.toString().padStart(2, '0')}`
}
</script>