<script setup>
import { ref, computed, onMounted } from 'vue';
import { useForm } from '@inertiajs/vue3';
import Modal from '@/Components/Modal.vue';

const props = defineProps({
  initialNotes: Array,
  filters: Object,
  tags: Array,
});

const notes = ref(props.initialNotes || []);
const activeTab = ref('my-notes');
const page = ref(1);
const isLoading = ref(false);
const showCreateModal = ref(false);
const loadingElement = ref(null);

const displayedNotes = computed(() => {
  return notes.value.filter(note => {
    if (activeTab.value === 'my-notes') {
      return note.user_id === props.auth?.user?.id;
    }
    return true;
  });
});

const formatTimeAgo = (date) => {
  const now = new Date();
  const past = new Date(date);
  const diffTime = Math.abs(now - past);
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

  if (diffDays === 0) return '今日';
  if (diffDays === 1) return '昨日';
  if (diffDays < 7) return `${diffDays}日前`;
  if (diffDays < 30) return `${Math.floor(diffDays / 7)}週間前`;
  if (diffDays < 365) return `${Math.floor(diffDays / 30)}ヶ月前`;
  return `${Math.floor(diffDays / 365)}年前`;
};

// Infinite Scroll
onMounted(() => {
  const observer = new IntersectionObserver(async (entries) => {
    const target = entries[0];
    if (target.isIntersecting && !isLoading.value) {
      await loadMoreNotes();
    }
  }, {
    root: null,
    rootMargin: '100px',
    threshold: 0.1
  });

  if (loadingElement.value) {
    observer.observe(loadingElement.value);
  }
});

const loadMoreNotes = async () => {
  if (isLoading.value) return;

  isLoading.value = true;
  const nextPage = page.value + 1;

  try {
    const response = await fetch(`/api/notes?page=${nextPage}&tab=${activeTab.value}`);
    const data = await response.json();

    if (data.data.length > 0) {
      notes.value = [...notes.value, ...data.data];
      page.value = nextPage;
    }
  } catch (error) {
    console.error('Failed to load more notes:', error);
  } finally {
    isLoading.value = false;
  }
};

const newNote = useForm({
  title: '',
  content: '',
  tags: [],
});

async function filterByTag(tag) {
  selectedTag.value=tag===selectedTag.value? null:tag;
  notes.value=[];
  page.value=1;
  noMoreData.value=false;
  await async();
}

const updateSort = async () => {
  notes.value = [];
  page.value = 1;
  noMoreData.value = false;
  await async();
};

const createNote = () => {
  newNote.post('/notes', {
    onSuccess: () => {
      showCreateModal.value = false;
      newNote.reset();
    },
  });
};

const deleteNote = (id) => {
  if (confirm('このノートを削除してもよろしいですか？')) {
    router.delete(`/notes/${id}`);
  }
};

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('ja-JP');
};
</script>

<template>
    <div class="min-h-screen bg-gray-50">
      <!-- ヘッダー -->
        <header class="bg-white shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <div class="flex items-center">
                        <img src="/path-to-your-logo.png" alt="Logo" class="h-8 w-auto" />
                        <h1 class="ml-3 text-xl font-semibold">Inspod</h1>
                    </div>
                    <button @click="showCreateModal = true" class="bg-orange-300 hover:bg-orange-400 text-white px-4 py-2 rounded-full flex items-center">
                    <span class="mr-2">+</span>
                    ノートの新規追加
                    </button>
                </div>
            </div>
        </header>
  
        <!-- メインコンテンツ -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- ナビゲーションタブ -->
            <div class="border-b border-gray-200 mb-6">
                <nav class="-mb-px flex space-x-8">
                    <button
                    @click="activeTab = 'explore'"
                    :class="[
                        activeTab === 'explore'
                        ? 'border-orange-400 text-orange-400'
                        : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
                        'whitespace-nowrap pb-4 px-1 border-b-2 font-medium'
                    ]"
                    >
                    エクスプロア
                    </button>
                    <button
                    @click="activeTab = 'my-notes'"
                    :class="[
                        activeTab === 'my-notes'
                        ? 'border-orange-400 text-orange-400'
                        : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
                        'whitespace-nowrap pb-4 px-1 border-b-2 font-medium'
                    ]"
                    >
                    自分のノート
                    </button>
                </nav>
            </div>
    
            <!-- ノート一覧 -->
            <div>
                <div class="flex justify-between items-center mb-4">
                    <div class="text-sm text-gray-500">{{ displayedNotes.length }} 項目</div>
                        <div class="flex items-center space-x-4">
                            <button class="text-gray-600 flex items-center">
                                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12" />
                                </svg>
                                フィルタ
                            </button>
                            <button class="text-gray-600 flex items-center">
                                最後に開いた
                            </button>
                        </div>
                    </div>
    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div
                        v-for="note in displayedNotes"
                        :key="note.id"
                        class="bg-white rounded-lg shadow overflow-hidden hover:shadow-md transition-shadow"
                        >
                        <!-- サムネイル画像 -->
                        <div class="relative pb-[56.25%] bg-gray-100">
                            <img
                            :src="note.thumbnail || '/path-to-default-thumbnail.jpg'"
                            :alt="note.title"
                            class="absolute inset-0 w-full h-full object-cover"
                            />
                    </div>
    
                        <!-- ノート情報 -->
                        <div class="p-4">
                            <div class="flex justify-between items-start">
                                <h3 class="text-lg font-medium text-gray-900 mb-1">{{ note.title }}</h3>
                                <button class="text-gray-400 hover:text-gray-600">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z" />
                                    </svg>
                                </button>
                            </div>
    
                            <div class="text-sm text-gray-500 mb-2">
                            {{ formatTimeAgo(note.created_at) }}
                            </div>
    
                            <!-- タイムスタンプタグ -->
                            <div class="flex flex-wrap gap-2 mb-2">
                                <span
                                    v-for="timestamp in note.timestamps"
                                    :key="timestamp"
                                    class="inline-flex items-center px-2 py-1 rounded-md text-xs bg-gray-100 text-gray-600"
                                >
                                    {{ timestamp }}
                                </span>
                            </div>
    
                            <!-- いいねボタン -->
                            <div class="flex items-center justify-between mt-2">
                                <button
                                    class="inline-flex items-center text-orange-400 hover:text-orange-500"
                                >
                                    <span class="mr-1">+</span>
                                    <span class="text-sm">{{ note.likes || 0 }}</span>
                                </button>
                            <div class="text-sm text-gray-500">
                                {{ note.comments ? '評価' : '' }}
                            </div>
                        </div>
                    </div>
                </div>
                </div>
    
            <!-- ローディングインジケーター -->
                <div
                    v-if="isLoading"
                    class="flex justify-center items-center py-4"
                    ref="loadingElement"
                >
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-orange-400"></div>
                </div>
            </div>
        </div>
  
        <!-- 新規ノート作成モーダル -->
        <Modal v-if="showCreateModal" @close="showCreateModal = false">
        <!-- モーダルの内容は前回と同じ -->
        </Modal>
    </div>
</template>