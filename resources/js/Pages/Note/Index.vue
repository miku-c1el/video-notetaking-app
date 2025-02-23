<script setup>
import { ref, computed, onMounted, onUnmounted, watch, nextTick } from 'vue';
import Layout from '@/Layouts/AppLayout.vue';
import { useForm, router } from '@inertiajs/vue3';
import TagFilterModal from '@/Components/TagFilterModal.vue';
import SortButton from '@/Components/SortButton.vue';
import NoteMenuDropdown from '@/Components/NoteMenuDropdown.vue';
import NoteEditModal from '@/Components/NoteEditModal.vue';
import Modal from '@/Components/Modal.vue';
import axios from 'axios';
import { useNotes } from '@/Composables/useNotes';

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
    default: () => ({tags: []})
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

// Notes Logic
const { notes, isLoading, hasMore, selectedTags, page, loadMoreNotes, handleNoteUpdate, handleDeleteNote } = useNotes(props.initialNotes, props.pagination, props.filters);

// 共通の状態
const activeTab = ref('my-notes');
const loadingElement = ref(null);

// ノート関連の状態
const showFilterModal = ref(false);
const showEditModal = ref(false);
const showDeleteModal = ref(false);
const selectedNote = ref(null);
const cleanup = ref(null);
const tags = ref(props.tags || []);

// 動画関連の状態
const videos = ref([]);
const selectedVideo = ref(null);
const showModal = ref(false);
const selectedCategory = ref('Career');
const categories = ref([
  'Career',
  'Programming',
  'English',
  'Piano',
]);

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
  // notes.value = [];  // ノートをリセット
  // page.value = 1;    // ページをリセット
  // selectedTags.value = [];

  if (newTab === 'explore') {
    // エクスプロアタブの初期化
    videos.value = [];
    selectedCategory.value = 'Career';
    await loadVideosByCategory(selectedCategory.value);
  } else {
    selectedTags.value = [];
    router.get(
    route('notes.index'),
    { tags: selectedTags.value },
    );
    await loadMoreNotes();
  }
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


// Reset data when filters change
// const resetData = () => {
//   notes.value = [];
//   page.value = 1;
//   hasMore.value = true;
//   loadMoreNotes();
// };

// Watch for filter changes
// watch(selectedTags, resetData);
// watch(activeTab, resetData);

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

const showNote = (note) => {
  router.get(route('notes.show', note.id), 
        {noteId: note.id}
    );
};

const handleEditClick = (note) => {
  selectedNote.value = note;
  showEditModal.value = true;
};

const closeEditModal = () => {
  showEditModal.value = false;
  selectedNote.value = null;
  editMode.value = null;
};

// const handleNoteUpdate = (updatedNote) => {

//     if (!updatedNote || !updatedNote.id) {
//         console.warn('Invalid updated note received:', updatedNote);
//         return;
//     }
    
//     // 現在の表示中のノートリストを更新
//     notes.value = notes.value.map(note => 
//       note.id === updatedNote.id 
//         ? { 
//             ...note, 
//             ...updatedNote,

//           } 
//         : note
//     );
// };

// タグ更新用の関数
const refreshTags = () => {
    if (selectedNote.value.id) {
        getTags();
    }
    getTags().then(() => {
        router.reload({ only: ['notes'] });
    });
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

// 動画関連の関数
const loadVideosByCategory = async (category) => {
  isLoading.value = true;
  selectedCategory.value = category;
  try {
    const response = await axios.get(route('exploreVideos.index'), {
      params: { category: category }
    });
    videos.value = response.data.videos;
    console.log(videos);
  } catch (error) {
    console.error('Failed to load videos:', error);
  } finally {
    isLoading.value = false;
  }
};

const openModal = (video) => {
  selectedVideo.value = video;
  showModal.value = true;
};

const closeModal = () => {
  showModal.value = false;
  selectedVideo.value = null;
};

const createNote = () => {
    router.post(route('notes.store'), 
        {video: selectedVideo.value}
    );
};
</script>

<template>
  <Layout>
    <div class="min-h-screen bg-gray-50">
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

        <!-- コンテンツ表示領域 -->
        <div>
          <!-- エクスプロアタブのコンテンツ -->
          <template v-if="activeTab === 'explore'">
            <!-- カテゴリー選択 -->
            <div class="mb-6">
              <h2 class="text-xl font-bold text-gray-900 mb-4">おすすめ動画</h2>
              <div class="flex flex-wrap gap-3">
                <button
                  v-for="category in categories"
                  :key="category"
                  @click="loadVideosByCategory(category)"
                  :class="[
                    'px-4 py-2 rounded-full text-sm font-medium transition-colors duration-200',
                    selectedCategory === category
                      ? 'bg-blue-900 text-white'
                      : 'bg-gray-100 text-gray-600 hover:bg-gray-200'
                  ]"
                >
                  {{ category }}
                </button>
              </div>
            </div>
            
            <!-- 動画グリッド -->
            <div class="transition-opacity duration-300">
              <div v-if="videos.length" 
                class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <div 
                  v-for="video in videos" 
                  :key="video.id" 
                  @click="openModal(video)"
                  class="group bg-white rounded-xl shadow-sm hover:shadow-md transition-all duration-200 cursor-pointer overflow-hidden"
                >
                  <!-- サムネイル -->
                  <div class="relative aspect-video overflow-hidden">
                    <img 
                      :src="video.thumbnail" 
                      :alt="video.title"
                      class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-300"
                    >
                    <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-10 transition-all duration-200"></div>
                  </div>
                  
                  <!-- 動画情報 -->
                  <div class="p-4">
                    <h3 class="font-semibold text-gray-900 line-clamp-2 mb-2 group-hover:text-blue-600 transition-colors">
                      {{ video.title }}
                    </h3>
                    <div class="flex items-center text-sm text-gray-600 space-x-2">
                      <span class="truncate">{{ video.channelTitle }}</span>
                      <span class="text-gray-400">•</span>
                      <span class="flex-shrink-0">{{ new Date(video.publishedAt).toLocaleDateString() }}</span>
                    </div>
                  </div>
                </div>
              </div>

              <!-- 動画が見つからない場合 -->
              <div v-else-if="!isLoading" class="text-center py-12">
                <div class="text-gray-400 mb-2">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z" />
                  </svg>
                </div>
                <p class="text-gray-600 text-lg">動画が見つかりませんでした</p>
                <p class="text-gray-400 mt-1">別のカテゴリーを選択してください</p>
              </div>
            </div>

            <!-- 動画モーダル -->
            <Modal :show="showModal" @close="closeModal">
              <div class="p-6">
                <!-- ビデオプレイヤー -->
                <div class="aspect-video bg-gray-100 rounded-lg overflow-hidden mb-4">
                    <iframe 
                        v-if="selectedVideo" 
                        :src="`https://www.youtube.com/embed/${selectedVideo.videoId}`"
                        class="w-full h-full"
                        allow="accelerometer; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                        allowfullscreen
                    ></iframe>
                </div>

                <!-- ビデオ情報 -->
                <div class="text-left mb-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-2">
                        {{ selectedVideo?.title }}
                    </h2>
                    <div class="flex items-center space-x-4 text-sm text-gray-600">
                        <span class="font-medium">{{ selectedVideo?.channelTitle }}</span>
                        <span class="text-gray-400">•</span>
                        <span>{{ selectedVideo ? new Date(selectedVideo.publishedAt).toLocaleDateString() : '' }}</span>
                    </div>
                </div>

                <!-- ノートセクション -->
                <div class="border-t pt-4">
                  <div class="flex justify-center items-center mb-4">
                    <button 
                      @click="createNote"
                      class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md transition-colors"
                    >
                      <svg 
                        xmlns="http://www.w3.org/2000/svg" 
                        class="h-5 w-5 mr-2" 
                        viewBox="0 0 20 20" 
                        fill="currentColor"
                      >
                      <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                      </svg>
                      ノートを作成
                    </button>
                  </div>
                </div>
              </div>
            </Modal>
          </template>
        

          <!-- マイノートタブのコンテンツ -->
          <template v-else>
            <!-- 既存のノート一覧コード -->
            <div class="flex justify-between items-center mb-4">
              <!-- <div 
              v-if="activeTab === 'my-notes'"
              class="flex justify-between items-center mb-4"
              > -->
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
                  :initial-sort="String(props.filters.sort || 'created_at')"
                  :initial-selected-tags="selectedTags"
                  />
                </div>
            </div>

            <div class="transition-opacity duration-300">
              <div v-if="notes.length"
                class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3 gap-6">
                <div 
                  v-for="note in notes"
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
                          :showDeleteodal="showDeleteModal"
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
          </template>
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
        :note="selectedNote || {}"
        :tags="selectedNote?.tags"
        @updated="handleNoteUpdate"
        @tag-updated="refreshTags"
        @close="closeEditModal"
      />
    </div>
  </Layout>
</template>

<style scoped>
.aspect-video {
    aspect-ratio: 16 / 9;
}
</style>