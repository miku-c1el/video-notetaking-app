<template>
  <div class="flex h-screen bg-gray-100">
    <!-- Video Player Section -->
    <div class="w-3/5 h-full bg-black relative">
      <div class="aspect-w-16 aspect-h-9">
        <iframe 
          :src="video.url" 
          class="w-full h-full" 
          frameborder="0" 
          allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
          allowfullscreen
        ></iframe>
      </div>
    </div>

    <!-- Notes Section -->
    <div class="w-2/5 h-full flex flex-col bg-white">
      <div class="p-4 border-b">
        <h1 class="text-xl font-bold">{{ video.title }}</h1>
      </div>

      <!-- Notes List -->
      <div class="flex-1 overflow-y-auto">
        <div class="p-4">
          <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold">モーメント ({{ moments.length }})</h2>
            <button
              @click="startCapture"
              class="bg-coral-500 text-white px-4 py-2 rounded-lg hover:bg-coral-600"
            >
              モーメントをキャプチャ
            </button>
          </div>

          <!-- Moments List -->
          <div class="space-y-4">
            <div
              v-for="moment in moments"
              :key="moment.id"
              class="bg-gray-50 p-4 rounded-lg"
            >
              <div class="flex justify-between items-start mb-2">
                <h3 class="font-medium">{{ moment.title }}</h3>
                <button
                  @click="seekTo(moment.timestamp)"
                  class="text-sm text-gray-600 hover:text-gray-900"
                >
                  {{ formatTime(moment.timestamp) }}
                </button>
              </div>
              <p class="text-gray-700">{{ moment.content }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- New Moment Form -->
      <MomentForm
        v-if="isCapturing"
        :currentTime="currentTime"
        @submit="saveMoment"
        @cancel="cancelCapture"
      />
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useForm } from '@inertiajs/vue3'
import MomentForm from '@/Components/MomentForm.vue'

const props = defineProps({
  video: {
    type: Object,
    required: true
  },
  moments: {
    type: Array,
    required: true
  }
})

const isCapturing = ref(false)
const currentTime = ref(0)
let player = null

onMounted(() => {
  // YouTube Player API integration would go here
  // This is a simplified version
})

const startCapture = () => {
  isCapturing.value = true
  currentTime.value = Math.floor(player?.getCurrentTime() || 0)
}

const cancelCapture = () => {
  isCapturing.value = false
}

const saveMoment = (formData) => {
  useForm({
    video_id: props.video.id,
    timestamp: formData.timestamp,
    title: formData.title,
    content: formData.content
  }).post(route('moments.store'), {
    onSuccess: () => {
      isCapturing.value = false
    }
  })
}

const seekTo = (timestamp) => {
  if (player) {
    player.seekTo(timestamp)
    player.playVideo()
  }
}

const formatTime = (seconds) => {
  const minutes = Math.floor(seconds / 60)
  const remainingSeconds = seconds % 60
  return `${minutes}:${remainingSeconds.toString().padStart(2, '0')}`
}
</script>