<script setup>
import { ref } from 'vue';
import Layout from '@/Layouts/AppLayout.vue';
import { router, usePage } from '@inertiajs/vue3';

const props = defineProps({
    videos: Array,
    query: String
})
const searchQuery = ref(props.query);
const searchVideos = () => {
    router.get('videos', { query: searchQuery.value });
};
</script>

<template>
    <Layout>
        <div class="container mx-auto p-6">
            <!-- 検索フォーム -->
            <div class="mb-6 flex justify-center">
                <input v-model="searchQuery" type="text" placeholder="動画を検索..."
                    class="border border-gray-300 p-2 rounded-l-md w-80">
                <button @click="searchVideos"
                    class="bg-blue-500 text-white px-4 py-2 rounded-r-md">検索</button>
            </div>

            <!-- 検索結果 -->
            <div v-if="videos.length" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
                <div v-for="video in videos" :key="video.videoId" class="bg-white shadow-lg rounded-lg overflow-hidden">
                    <a :href="'https://www.youtube.com/watch?v=' + video.videoId" target="_blank" class="block">
                        <div class="aspect-w-16 aspect-h-9">
                            <img :src="video.thumbnail" :alt="video.title" class="thumbnail w-full h-full object-cover">
                        </div>
                        <div class="details px-4 pb-4">
                            <h3 class="mt-2 font-bold text-lg">{{ video.title }}</h3>
                            <p class="text-gray-700 text-sm">チャンネル: {{ video.channelTitle }}</p>
                            <p class="text-gray-500 text-sm">{{ new Date(video.publishedAt).toLocaleDateString() }}</p>
                        </div>
                    </a>
                </div>
            </div>
            <p v-else class="text-center text-gray-500">検索結果がありません</p>
        </div>
    </Layout>
</template>
