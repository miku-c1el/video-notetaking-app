<script setup>
import Layout from '@/Layouts/MomentLayout.vue';
import VideoMomentCapture from '@/Components/VideoMomentCapture.vue';
import NoteEditModal from '@/Components/NoteEditModal.vue';
import { ref, onMounted, watch, onUnmounted } from 'vue';
import { router } from '@inertiajs/vue3';
import axios from 'axios';
import NoteHead from '@/Components/NoteHead.vue';

const props = defineProps({
    note: {
        type: Object,
        required: true
    },
    moments: Array,
    tags: Array,
});

const isMobile = ref(window.innerWidth < 1024);
const player = ref(null);
const playerReady = ref(false);
const lastTimestamp = ref(null);
const isModalOpen = ref(false);
const tags = ref(props.tags || []);
const moments = ref(props.moments || []);
const updateScreenSize = () => {
    isMobile.value = window.innerWidth < 1024;
};

onMounted(() => {
    window.addEventListener('resize', updateScreenSize);
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

onUnmounted(() => {
    window.removeEventListener('resize', updateScreenSize);
});

const getMoments = () => {
    axios.get(route('moments.index'), { 
        params: { note_id: props.note.id }
    })
    .then(response => {
        moments.value = response.data.moments;
    });
};

const getTags = () => {
    axios.get(route('tags.index'), { 
        params: { note_id: props.note.id }
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
        return Math.floor(player.value.getCurrentTime());
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

watch(() => props.note.youtubeVideo_id, () => {
    if (window.YT && props.note.youtubeVideo_id) {
        onYouTubeIframeAPIReady();
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
        <NoteHead :title="props.note.title" />
        <div class="h-full">
            <div class="sticky top-0 z-10 bg-white flex h-full flex-col lg:flex-row"> 
                <!-- メインコンテンツ（動画＋タイトル） -->
                <div class="w-full lg:w-[60%] h-full flex flex-col"> 
                    
                    <!-- 動画プレイヤーセクション - 固定 or スティッキー -->
                    <div 
                        class="w-full bg-red transition-all"
                        :class="{
                            'fixed top-0 left-0 w-full z-50': isMobile,
                            'sticky top-0 z-100': !isMobile
                        }"
                    >
                        <div class="w-full h-[60vh] lg:h-[50vh] xl:h-[60vh]">
                            <div
                                :id="`youtube-player-${props.note.youtubeVideo_id}`"
                                class="w-full h-full"
                            ></div>
                        </div>
                    </div>

                    <!-- md以下の時は余白を追加 -->
                    <div v-if="isMobile" class="h-[53vh]"></div>

                    <!-- lg以上: 独立スクロール, md以下: メインスクロールの一部 -->
                    <div class="lg:flex-1 lg:overflow-y-auto">
                        <div class="bg-white">
                            <div class="p-4 lg:p-6">
                                <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4">
                                    <h2 class="text-lg font-semibold text-primary-dark">{{ props.note?.title }}</h2>
                                    <button
                                        @click="isModalOpen = true"
                                        class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-primary-dark text-sm font-medium rounded-md transition-colors whitespace-nowrap"
                                    >
                                        詳細編集
                                    </button>
                                </div>

                                <NoteEditModal
                                    v-model="isModalOpen"
                                    :note="note"
                                    :tags="tags"
                                    @tag-updated="refreshTags"
                                />

                                <!-- タグセクション -->
                                <div class="flex flex-wrap gap-2 mt-4">
                                    <span
                                        v-for="tag in tags" 
                                        :key="tag.id"
                                        class="px-3 py-1 bg-gray-100 text-primary-dark rounded-md text-sm"
                                    >
                                        # {{ tag.name }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ノートセクション -->
                <div class="w-full lg:w-[40%] lg:h-full lg:overflow-hidden">
                    <VideoMomentCapture 
                        v-if="note.id"
                        :noteId="note.id"
                        :player="player"
                        :getCurrentTime="getCurrentTime"
                        :moments="moments"
                        class="lg:h-full"
                    />
                </div>
            </div>
        </div>
    </Layout>
</template>

<style>
.overflow-y-auto {
    overflow-y: auto;
    -webkit-overflow-scrolling: touch;
}
</style>




