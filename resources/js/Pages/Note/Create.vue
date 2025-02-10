<script setup>
import Layout from '@/Layouts/MomentLayout.vue';
import VideoMomentCapture from '@/Components/VideoMomentCapture.vue';
import NoteEditModal from '@/Components/NoteEditModal.vue';
import { ref, onMounted, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import axios from 'axios';


const props = defineProps({
    note: {
        type: Object,
        required: true
    },
    moments: Array,
    tags: Array
});

const player = ref(null);
const playerReady = ref(false);
const lastTimestamp = ref(null);
const isModalOpen = ref(false);
const tags = ref(props.tags || []);

const getMoments = () => {
    router.get(route('moments.index'), { 
        note_id: props.note.id 
    }, {
        preserveState: true,
        preserveScroll: true,
        only: ['moments']
    });
};

const getTags = () => {
    axios.get(route('tags.index'), { 
        params: {
            note_id: props.note.id 
        }
    })
    .then(response => {
        tags.value = response.data.tags;
    });
};

const loadYouTubeAPI = () => {
    if (!window.YT) {
        const script = document.createElement('script');
        script.src = 'https://www.youtube.com/iframe_api';
        script.async = true;
        document.body.appendChild(script);
    }
};

const onYouTubeIframeAPIReady = () => {
    if (!props.note.youtubeVideo_id) return;
    
    player.value = new YT.Player(`youtube-player-${props.note.youtubeVideo_id}`, {
        height: '100%',
        width: '100%',
        videoId: props.note.youtubeVideo_id,
        events: {
            'onReady': () => {
                playerReady.value = true;
                if (lastTimestamp.value !== null) {
                    seekToLastTimestamp();
                }
            }
        }
    });
};
    
const getCurrentTime = () => {
    if (playerReady.value && player.value) {
        return player.value.getCurrentTime();
    }
    return 0;
};

defineExpose({ getCurrentTime });

// 保存された時間位置にジャンプする関数
const seekToLastTimestamp = () => {
    if (playerReady.value && player.value && lastTimestamp.value !== null) {
        player.value.seekTo(lastTimestamp.value);
        lastTimestamp.value = null; // リセット
    }
};

// URLからタイムスタンプを取得する関数
const getTimestampFromUrl = () => {
    const urlParams = new URLSearchParams(window.location.search);
    const timestamp = urlParams.get('timestamp');
    return timestamp ? parseFloat(timestamp) : null;
};

onMounted(() => {
    // URLからタイムスタンプを取得して保存
    lastTimestamp.value = getTimestampFromUrl();

    if (props.note.id) {
        getMoments();
        getTags();
    }

    loadYouTubeAPI();
    if (window.YT && window.YT.Player) {
        onYouTubeIframeAPIReady();
    } else {
        window.onYouTubeIframeAPIReady = onYouTubeIframeAPIReady;
    }
});

watch(() => props.note.youtubeVideo_id, () => {
    if (window.YT && props.note.youtubeVideo_id) {
        onYouTubeIframeAPIReady();
    }
});

watch(() => props.note.id, () => {
    if (props.note.id) {
        getTags();
    }
});

const refreshTags = () => {
    if (props.note.id) {
        getTags();
    }
};
</script>

<template>
    <Layout>
        <div class="h-screen overflow-hidden"> <!-- 全体を画面の高さに固定 -->
            <div class="flex h-full"> <!-- flexコンテナを高さいっぱいに -->
                <!-- メインコンテンツ（固定） -->
                <div class="w-[60%]"> <!-- 幅を60%に増やし、paddingを削除 -->
                    <div class="h-full flex flex-col">
                        <!-- 固定コンテンツ用のコンテナ -->
                        <div class="p-8 flex-shrink-0">
                            <!-- ビデオプレイヤー -->
                            <div class="aspect-video bg-gray-900 rounded-lg overflow-hidden">
                                <div 
                                    :id="`youtube-player-${props.note.youtubeVideo_id}`"
                                    class="w-full h-full"
                                ></div>
                            </div>
                            
                            <!-- タイトルセクション -->
                            <div class="bg-white shadow rounded-lg p-6 mt-6">
                                <div class="flex justify-between items-center mb-4">
                                    <h2 class="text-lg font-medium text-gray-900">{{ props.note?.title }}</h2>
                                    <button 
                                        @click="isModalOpen = true"
                                        class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-md transition-colors"
                                    >
                                        詳細編集
                                    </button>

                                    <NoteEditModal
                                        v-model="isModalOpen"
                                        :note="note"
                                        :tags="tags"
                                        @tag-updated="refreshTags"
                                    />
                                </div>

                                <!-- タグセクション -->
                                <div class="flex gap-2 mt-4">
                                    <span 
                                        v-for="tag in tags" 
                                        :key="tag.id"
                                        class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm"
                                    >
                                        # {{ tag.name }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ノートセクション -->
                <div class="w-[40%]"> <!-- 幅を40%に調整 -->
                    <VideoMomentCapture 
                        v-if="note.id"
                        :noteId="note.id"
                        :player="player"
                        :getCurrentTime="getCurrentTime"
                        :moments="props.moments"
                    />
                </div>
            </div>
        </div>
    </Layout>
</template>