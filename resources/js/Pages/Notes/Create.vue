<script setup>
import Layout from '@/Layouts/AppLayout.vue';
// import { Button } from '@/Components/ui/button';
// import { Card, CardContent } from '@/Components/ui/card';
import { ref } from 'vue';

const props = defineProps({
    note: {
        type: Object,
        required: true
    }
});

// 状態管理
const showNoteForm = ref(false);

const toggleNoteForm = () => {
    showNoteForm.value = !showNoteForm.value;
};
</script>


<template>
    <Layout>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="grid grid-cols-12 gap-6">
                <!-- メインコンテンツ -->
                <div class="col-span-12 lg:col-span-8">
                    <!-- ビデオプレイヤー -->
                    <div class="aspect-video bg-gray-900 rounded-lg mb-6 overflow-hidden">
                        <iframe 
                            v-if="note.youtubeVideo_id"
                            :src="`https://www.youtube.com/embed/${note.youtubeVideo_id}`"
                            class="w-full h-full"
                            allow="accelerometer; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                            allowfullscreen
                        ></iframe>
                    </div>
                    
                    <!-- タイトルセクション -->
                    <div class="bg-white shadow rounded-lg p-6 mb-6">
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="text-lg font-medium text-gray-900">タイトル</h2>
                            <button class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-md transition-colors">
                                詳細編集
                            </button>
                        </div>
                        <h3 class="text-xl text-gray-900">{{ note?.title }}</h3>
                        <div class="flex gap-2 mt-4">
                            <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm"># Youtube</span>
                        </div>
                    </div>
                </div>

                <!-- ノートセクション -->
                <div class="col-span-12 lg:col-span-4">
                    <Card class="bg-white shadow">
                        <CardContent class="p-6">
                            <div class="flex justify-between items-center mb-6">
                                <button 
                                    @click="toggleNoteForm"
                                    class="inline-flex items-center px-4 py-2 w-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md transition-colors"
                                >
                                    <svg 
                                        xmlns="http://www.w3.org/2000/svg" 
                                        class="h-5 w-5 mr-2" 
                                        viewBox="0 0 20 20" 
                                        fill="currentColor"
                                    >
                                        <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                                    </svg>
                                    モーメントを記録する
                                </button>
                            </div>
                            <div v-if="showNoteForm">
                                <!-- ノート作成フォーム -->
                            </div>
                            <div v-else class="text-center text-gray-500">
                                <p class="text-sm mb-1">ノートがまだありません。</p>
                                <p class="text-sm">「モーメントを記録する」をクリックしてノートを追加します。</p>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>
    </Layout>
</template>

<style scoped>
.aspect-video {
    aspect-ratio: 16 / 9;
}
</style>