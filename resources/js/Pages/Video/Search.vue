<script setup>
import { ref, watchEffect } from 'vue';
import Layout from '@/Layouts/AppLayout.vue';
import { router, Head } from '@inertiajs/vue3';
import Modal from '@/Components/Modal.vue';

const props = defineProps({
    videos: Array,
    query: String
});

const showModal = ref(false);
const selectedVideo = ref(null);
const searchQuery = ref(props.query || '');
const confirmedInput = ref('');

// 検索関連
const executeSearch = () => {
    if (searchQuery.value.trim()) {
        router.get(route('videos.index', { query: searchQuery.value }));
    }
};

const handleKeydown = (event) => {
    if (event.key === 'Enter') {
        if (confirmedInput.value === searchQuery.value.trim()) {
            executeSearch();
            confirmedInput.value = '';
        } else {
            confirmedInput.value = searchQuery.value.trim();
        }
    }
};

// modal関連
const openModal = (video) => {
    selectedVideo.value = video;
    showModal.value = true;
    document.body.classList.add('overflow-hidden');
};

const closeModal = () => {
    showModal.value = false;
    selectedVideo.value = null;
    document.body.classList.remove('overflow-hidden');
};

watchEffect((onCleanup) => {
    onCleanup(() => {
        document.body.classList.remove('overflow-hidden');
    });
});

// ノート関連
const createNote = () => {
    router.post(route('notes.store'), 
        {video: selectedVideo.value}
    );
};
</script>

<template>
    <Layout>
        <Head title="動画を検索" />
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- 検索フォーム -->
            <div class="mb-8 max-w-md mx-auto">
                <div class="flex">
                    <input 
                        v-model="searchQuery" 
                        type="text" 
                        placeholder="動画を検索..."
                        @keydown="handleKeydown"
                        class="flex-1 pl-4 pr-10 py-3 rounded-l-lg border border-r-0 border-gray-200 focus:outline-none focus:ring-0 focus:border-gray-200"
                    >
                    <button 
                        @click="executeSearch"
                        class="px-6 bg-primary text-white rounded-r-lg hover:bg-primary-light hover:text-primary-dark focus:outline-none"
                    >
                        <span class="hidden sm:inline">検索</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 sm:hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- 動画グリッド -->
            <div class="transition-opacity duration-300">
                <div v-if="videos.length" 
                    class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    <div 
                        v-for="video in videos" 
                        :key="video.videoId" 
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
                            <h3 class="font-medium text-primary-dark line-clamp-2 mb-2 group-hover:text-primary transition-colors">
                                {{ video.title }}
                            </h3>
                            <div class="flex items-center text-sm text-primary-dark space-x-2">
                                <span class="truncate">{{ video.channelTitle }}</span>
                                <span class="text-gray-400">•</span>
                                <span class="flex-shrink-0">{{ new Date(video.publishedAt).toLocaleDateString() }}</span>
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
                    <p class="text-gray-600 text-lg">お気に入りの動画を検索してください</p>
                </div>
            </div>

            <!-- モーダル -->
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
                                class="inline-flex items-center px-4 py-2 hover:bg-primary-light hover:text-primary-dark bg-primary text-white text-sm font-medium rounded-md transition-colors"
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
        </div>
    </Layout>
</template>

<style scoped>
.aspect-video {
    aspect-ratio: 16 / 9;
}
</style>


