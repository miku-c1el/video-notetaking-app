<script setup>
import { router } from '@inertiajs/vue3';
import { ref, onMounted, onBeforeUnmount } from 'vue';
import { Trash2 } from 'lucide-vue-next';
import { Card } from '@/Components/card';
import axios from 'axios';

const props = defineProps({
  noteId: Number,
  player: Object,
  getCurrentTime: Function,
  moments: Array,
});

const moments = ref(props.moments.map(moment => ({
  ...moment,
  isEditing: false
})));

const title = ref('');

const createMoment = async () => {
  const currentTime = props.getCurrentTime();
  props.player.pauseVideo();

  try {
    const response = await axios.post(route('moments.store'), {
      noteId: props.noteId,
      timestamp: currentTime,
      title: title.value,
      content: ''
    });

    const newMoment = {
      ...response.data.moment,
      isEditing: true
    };

    moments.value = [...moments.value, newMoment].sort((a, b) => a.timestamp - b.timestamp);
    title.value = '';

    setTimeout(() => {
      const element = document.getElementById(`moment-${newMoment.id}`);
      if (element) {
        element.scrollIntoView({ behavior: 'smooth', block: 'center' });
      }
    }, 100);

  } catch (error) {
    console.error('Failed to create moment:', error);
  }
};

let updateTimeout;
const updateMoment = async (moment) => {
  if (updateTimeout) {
    clearTimeout(updateTimeout);
  }

  updateTimeout = setTimeout(async () => {
    try {
      await axios.put(route('moments.update', moment.id), {
        title: moment.title,
        content: moment.content
      });
    } catch (error) {
      console.error('Failed to update moment:', error);
    }
  }, 1000);
};

const deleteMoment = async (momentId) => {
  if (confirm('このモーメントを削除してもよろしいですか？')) {
    try {
      await axios.delete(route('moments.destroy', momentId));
      moments.value = moments.value.filter(moment => moment.id !== momentId);
    } catch (error) {
      console.error('Failed to delete moment:', error);
    }
  }
};

const formatTimestamp = (seconds) => {
  const minutes = Math.floor(seconds / 60);
  const remainingSeconds = Math.floor(seconds % 60);
  return `${minutes}:${remainingSeconds.toString().padStart(2, '0')}`;
};

const jumpToTimestamp = (timestamp) => {
  if (props.player) {
    props.player.seekTo(timestamp);
  }
};
</script>

<template>
  <div class="h-screen border-l border-gray-200">
    <div class="h-full bg-gray-50 p-6">
      <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-semibold">モーメント ({{ moments.length }})</h2>
        <button
          @click="createMoment"
          class="px-4 py-2 bg-coral-500 text-black rounded-md hover:bg-coral-600"
        >
          モーメントをキャプチャ
        </button>
      </div>
      
      <div class="space-y-4 overflow-y-auto h-[calc(100vh-100px)]">
        <div v-if="moments">
          <Card 
            v-for="moment in moments" 
            :key="moment.id" 
            :id="'moment-' + moment.id"
            class="p-4 bg-white"
          >
            <div class="flex justify-between items-start mb-2">
              <button
                @click="jumpToTimestamp(moment.timestamp)"
                class="p-1 hover:bg-gray-100 rounded"
              >
                {{ formatTimestamp(moment.timestamp) }}
              </button>
              <button
                @click="deleteMoment(moment.id)"
                class="p-1 hover:text-red-500 transition-colors"
              >
                <Trash2 class="w-4 h-4" />
              </button>
            </div>
            
            <div class="border-t border-b border-gray-100 py-2">
              <input 
                type="text" 
                class="w-full border-none focus:ring-0 text-lg font-medium"
                placeholder="見出し" 
                v-model="moment.title"
                @input="() => updateMoment(moment)"
              >
              <textarea
                v-model="moment.content"
                @input="() => updateMoment(moment)"
                class="w-full min-h-[100px] border-none focus:ring-0 resize-y p-2"
                placeholder="メモを入力してください..."
              ></textarea>
            </div>
          </Card>
        </div>
        <div v-else class="text-center py-12">
          <!-- モーメントがないことを伝えるコメントを挿入する -->
          <p></p>
        </div>
      </div>
    </div>
  </div>
</template>

<style>
textarea {
  font-family: inherit;
  line-height: 1.5;
}
</style>





