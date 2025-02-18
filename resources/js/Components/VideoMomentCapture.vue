<script setup>
import { router } from '@inertiajs/vue3';
import { ref, onMounted, onBeforeUnmount } from 'vue';
import { Trash2 } from 'lucide-vue-next';
import { Card } from '@/Components/card';
import { Dialog, DialogPanel, DialogTitle } from '@headlessui/vue';
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
const showDuplicateAlert = ref(false);
const showDeleteConfirm = ref(false);
const momentToDelete = ref(null);

const createMoment = async () => {
  const currentTime = props.getCurrentTime();
  props.player.pauseVideo();

  if (existsMoment(currentTime)) {
      showDuplicateWarning();
      return;
    }


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

// 取得したモーメントがすでに存在するか確認
const existsMoment = (currentTime) => {
  return moments.value.some(moment => moment.timestamp === Math.round(currentTime));
};

// 重複アラート表示の処理
const showDuplicateWarning = () => {
  showDuplicateAlert.value = true;
  setTimeout(() => {
    showDuplicateAlert.value = false;
  }, 3000);
};

// モーメントのデバウンス処理
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

// 削除関連の処理
const confirmDelete = (moment) => {
  momentToDelete.value = moment;
  showDeleteConfirm.value = true;
};

const cancelDelete = () => {
  showDeleteConfirm.value = false;
  momentToDelete.value = null;
};

const handleDelete = async () => {
  try {
    await axios.delete(route('moments.destroy', momentToDelete.value.id));
    moments.value = moments.value.filter(moment => moment.id !== momentToDelete.value.id);
    showDeleteConfirm.value = false;
    momentToDelete.value = null;
  } catch (error) {
    console.error('Failed to delete moment:', error);
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
  <div class="border-l-0 lg:border-l border-gray-200 h-full">
    <div class="h-full bg-gray-50 flex flex-col">
      <!-- 重複アラート -->
      <div
        v-if="showDuplicateAlert"
        class="fixed top-4 right-4 z-50 animate-in fade-in slide-in-from-top duration-300"
      >
        <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4 shadow-lg">
          <p class="text-yellow-800">
            このタイムスタンプにはすでにモーメントが存在します
          </p>
        </div>
      </div>

      <!-- 削除確認モーダル -->
      <Dialog
        :open="showDeleteConfirm"
        @close="cancelDelete"
        class="relative z-[200]"
      >
        <div class="fixed inset-0 z-[190]">
          <div class="fixed inset-0 bg-black/30" aria-hidden="true" />
          
          <div class="fixed inset-0 flex items-center justify-center p-4 z-[200]">
            <DialogPanel class="w-full max-w-sm rounded bg-white p-6 shadow-xl">
              <DialogTitle class="text-lg font-medium mb-4">
                モーメントの削除
              </DialogTitle>
              
              <p class="mb-4">
                このモーメントを削除してもよろしいですか？
              </p>
              
              <div class="flex justify-end gap-3">
                <button
                  @click="cancelDelete"
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

      <div class="p-4 lg:p-6 bg-gray-50">
        <div class="flex justify-between items-center">
          <h2 class="text-lg font-semibold">モーメント ({{ moments.length }})</h2>
          <button
              @click="createMoment"
              class="px-4 py-2 bg-coral-500 text-black rounded-md hover:bg-coral-600"
          >
              モーメントをキャプチャ
          </button>
        </div>
      </div>

      <!-- モーメント一覧 -->
      <div class="lg:flex-1 lg:overflow-y-auto px-6">
        <template v-if="moments.length > 0">
          <Card 
              v-for="moment in moments" 
              :key="moment.id" 
              :id="'moment-' + moment.id"
              class="p-4 mb-4 bg-white"
          >
            <div class="flex justify-between items-start mb-2">
              <button
                @click="jumpToTimestamp(moment.timestamp)"
                class="p-1 hover:bg-gray-100 rounded"
              >
                {{ formatTimestamp(moment.timestamp) }}
              </button>
              <button
                @click="confirmDelete(moment)"
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
          <div class="mb-6"></div>
        </template>
        <div v-else class="flex items-center justify-center h-32">
          <p class="text-gray-500">まだモーメントがありません。</p>
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

.animate-in {
  animation: fadeIn 0.3s ease-out;
}

.slide-in-from-top {
  animation: slideInFromTop 0.3s ease-out;
}

.overflow-y-auto {
    overflow-y: auto;
    -webkit-overflow-scrolling: touch;
}

@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

@keyframes slideInFromTop {
  from { transform: translateY(-1rem); }
  to { transform: translateY(0); }
}
</style>





