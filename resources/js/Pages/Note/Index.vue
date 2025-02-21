<script setup>
import { ref, computed, onMounted, onUnmounted, watch, nextTick } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
// import Modal from '@/Components/Modal.vue';
import TagFilterModal from '@/Components/TagFilterModal.vue';
import SortButton from '@/Components/SortButton.vue';
import NoteMenuDropdown from '@/Components/NoteMenuDropdown.vue';
import NoteEditModal from '@/Components/NoteEditModal.vue';
import axios from 'axios';

const props = defineProps({
  initialNotes: {
    type: Object,
    required: true,
    default: () => ({
      data: [],
    })
  },
  noteCount: Number,
  tags: Array,
  filters: {
    type: Object,
    default: () => ({})
  },
  pagination: {
    type: Object,
    default: () => ({
      current_page: 0,
      per_page: 12,
      total: 0,
      last_page: 1,
      has_more: false
    })
  }
});

// データの初期化
const notes = ref(props.initialNotes?.data ?? []);
const activeTab = ref('my-notes');
const page = ref(props.pagination.current_page || 0);
const isLoading = ref(false);
const hasMore = ref(props.pagination.has_more || false);
const loadingElement = ref(null);
const showFilterModal = ref(false);
const selectedTags = ref(props.filters?.tags || []);
const showEditModal = ref(false);
const selectedNote = ref(null);
const cleanup = ref(null);
const tags = ref(props.tags || []);


const displayedNotes = computed(() => {
  if (!Array.isArray(notes.value)) {
    console.warn('notes.value is not an array:', notes.value);
    return [];
  }
  return notes.value;
});

const formatTimeAgo = (date) => {
  const now = new Date();
  const past = new Date(date);

  const today = new Date(now.getFullYear(), now.getMonth(), now.getDate());
  const pastDay = new Date(past.getFullYear(), past.getMonth(), past.getDate());

  const diffTime = today - pastDay;
  const diffDays = diffTime / (1000 * 60 * 60 * 24); // 日数差

  if (diffDays === 0) return '今日';
  if (diffDays === 1) return '昨日';
  if (diffDays < 7) return `${diffDays}日前`;
  if (diffDays < 30) return `${Math.floor(diffDays / 7)}週間前`;
  if (diffDays < 365) return `${Math.floor(diffDays / 30)}ヶ月前`;
  return `${Math.floor(diffDays / 365)}年前`;
};


// タブ切り替え用の関数を追加
const switchTab = async (newTab) => {
  activeTab.value = newTab;
  notes.value = [];  // ノートをリセット
  page.value = 0;    // ページをリセット
  await loadMoreNotes(); // 新しいデータを取得
};

const observer = ref(null);

onMounted(async () => {
  await nextTick(); 
  setupInfiniteScroll();
});

onUnmounted(() => {
  if (observer.value) {
    observer.value.disconnect();
  }
  if (cleanup.value) {
    cleanup.value();
  }
});

const setupInfiniteScroll = () => {
  if (observer.value) {
    observer.value.disconnect();
  }
  observer.value = new IntersectionObserver(
    async (entries) => {
      const target = entries[0];
      if (target.isIntersecting && !isLoading.value && hasMore.value) {
        await loadMoreNotes();
      }
    },
    {
      root: null,
      rootMargin: '100px',
      threshold: 0.1
    }
  );

  if (loadingElement.value) {
    observer.value.observe(loadingElement.value);
  }
};

const loadMoreNotes = async () => {
  if (isLoading.value || !hasMore.value) return;

  isLoading.value = true;
  page.value += 1;
  try {
    const response = await fetch(
      `/api/notes?page=${page.value}&tab=${activeTab.value}&tags=${encodeURIComponent(JSON.stringify(selectedTags.value))}`
    );
    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }

    const data = await response.json();
    if (data.data.data && Array.isArray(data.data.data) && data.data.data.length > 0) {
      notes.value = Array.isArray(notes.value) 
        ? [...notes.value, ...data.data.data]
        : data.data.data;

      hasMore.value = data.current_page < data.last_page;
    } else {
      hasMore.value = false;
    }
  } catch (error) {
    console.error('Failed to load more notes:', error);
    hasMore.value = false;
  } finally {
    isLoading.value = false;
  }
};

// Reset data when filters change
const resetData = () => {
  notes.value = [];
  page.value = 1;
  hasMore.value = true;
  loadMoreNotes();
};

// Watch for filter changes
watch(selectedTags, resetData);
watch(activeTab, resetData);

watch(loadingElement, (newVal) => {
  if (newVal && observer.value) {
    setupInfiniteScroll(); // 要素が変更されたら再設定
  }
});

const updateSort = async () => {
  notes.value = [];
  page.value = 1;
  noMoreData.value = false;
  await async();
};

const deleteNote = (id) => {
  if (confirm('このノートを削除してもよろしいですか？')) {
    router.delete(`/notes/${id}`);
  }
};

const showNote = (note) => {
  router.get(route('notes.show', note.id), 
        {noteId: note.id}
    );
};

const handleEditClick = (note) => {
  selectedNote.value = note;
  showEditModal.value = true;
};

const handleDeleteNote = (noteId) => {
  router.delete(`/notes/${noteId}`, {
    preserveScroll: true,
    onSuccess: () => {
      // ノート一覧を更新
      notes.value = notes.value.filter(note => note.id !== noteId);
    }
  });
};

const closeEditModal = () => {
  showEditModal.value = false;
  selectedNote.value = null;
  editMode.value = null;
};

const handleNoteUpdate = (updatedNote) => {

    if (!updatedNote || !updatedNote.id) {
        console.warn('Invalid updated note received:', updatedNote);
        return;
    }
    
    // 現在の表示中のノートリストを更新
    notes.value = notes.value.map(note => 
      note.id === updatedNote.id 
        ? { 
            ...note, 
            ...updatedNote,

          } 
        : note
    );
};

// タグ更新用の関数
const refreshTags = () => {
    if (selectedNote.value.id) {
        getTags();
    }
    getTags().then(() => {
        Inertia.reload({ only: ['notes'] });
    });

    // const noteIndex = notes.value.findIndex(n => n.id === selectedNote.value.id);
    // if (noteIndex !== -1) {
    //   notes.value[noteIndex].tags = selectedNote.value.tags;
    // }
    // Inertia.reload({ only: ['notes'] });
};

const getTags = async () => {
    try {
        const response = await axios.get(route('tags.index'), {
            params: { note_id: selectedNote.value.id }
        });
        selectedNote.value.tags = response.data.tags; // Ensure the modal updates
    } catch (error) {
        console.error("Failed to fetch tags:", error);
    }
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
                    <a href="/videos" class="px-4 py-2 bg-orange-300 hover:bg-orange-400 rounded-full">
                      <span class="mr-2">+</span>
                      動画を検索する
                    </a>
                </div>
            </div>
        </header>
  
        <!-- メインコンテンツ -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- ナビゲーションタブ -->
            <div class="border-b border-gray-200 mb-6">
                <nav class="-mb-px flex space-x-8">
                    <button
                    @click="switchTab('explore')"
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
                    @click="switchTab('my-notes')"
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
                  <div class="text-sm text-gray-500">{{ props.noteCount }} 項目</div>
                  <div class="flex items-center space-x-4">
                      <button 
                        @click="showFilterModal = true" 
                        class="text-gray-600 flex items-center">
                          <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12" />
                          </svg>
                          フィルタ
                      </button>
                      <TagFilterModal
                        :is-open="showFilterModal"
                        :initial-selected-tags="selectedTags"
                        :available-tags="props.tags"
                        @close="showFilterModal = false"
                        @update:selected-tags="selectedTags = $event"
                      />

                      <SortButton 
                      :initial-sort="props.filters.sort"
                      :initial-selected-tags="selectedTags"
                      />
                    </div>
                </div>
  
                <div class="transition-opacity duration-300">
                    <div v-if="displayedNotes.length"
                        class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3 gap-6">
                        <div 
                            v-for="note in displayedNotes"
                            :key="note.id"
                            @click="showNote(note)"
                            class="group bg-white rounded-xl shadow-sm hover:shadow-md transition-all duration-200 cursor-pointer overflow-hidden"
                        >
                            <!-- サムネイル -->
                            <div class="relative aspect-video overflow-hidden">
                                <img 
                                    :src="note.thumbnail" 
                                    :alt="note.title"
                                    class="w-full h-full object-cover"
                                >
                                <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-10 transition-all duration-200"></div>
                            </div>
                            <div class="p-4">
                                <!-- ノート情報 -->
                                <div class="flex justify-between items-start">
                                    <h3 class="text-md font-medium text-gray-900 mb-1">{{ note.title }}</h3>
                                    <NoteMenuDropdown
                                      :note="note"
                                      @edit="() => handleEditClick(note)"
                                      @delete="handleDeleteNote"
                                    />
                                </div>
                                <div class="text-sm text-gray-500 mb-2">
                                {{ formatTimeAgo(note.created_at) }}
                                </div>
        
                                <!-- 関連タグ一覧 -->
                                <div v-if="note.tags.length > 0" class="mb-3 flex flex-wrap gap-2">
                                    <div
                                        v-for="tag in note.tags"
                                        :key="tag.id"
                                        class="bg-gray-100 px-2 py-1 rounded-md text-sm flex items-center gap-1"
                                    >
                                      # {{ tag.name }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                
                    <div v-else class="text-center py-12">
                        <div class="text-gray-400 mb-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z" />
                            </svg>
                        </div>
                        <p class="text-gray-600 text-lg">まだノートがありません</p>
                        <p class="text-gray-400 mt-1">右上のノート作成ボタンからノートを作成してください</p>
                    </div>
                </div>
            </div>
                
            <!-- ローディングインジケーター -->
            <div
                class="flex justify-center items-center py-4 min-h-[100px]"
                ref="loadingElement"
            >
                <div v-if="isLoading" class="animate-spin rounded-full h-8 w-8 border-b-2 border-orange-400"></div>
            </div>
        </div>
        <!-- 編集モーダル -->
        <NoteEditModal
          v-model="showEditModal"
          :note="selectedNote"
          :tags="selectedNote?.tags"
          @updated="handleNoteUpdate"
          @tag-updated="refreshTags"
          @close="closeEditModal"
        />
    </div>
</template>

<style scoped>
.aspect-video {
    aspect-ratio: 16 / 9;
}
</style>