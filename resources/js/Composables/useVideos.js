import { ref } from 'vue';
import axios from 'axios';
import { router } from '@inertiajs/vue3';

export function useVideos() {
    const videos = ref([]);
    const selectedVideo = ref(null);
    const showModal = ref(false);
    const selectedCategory = ref('Career');
    const categories = ['Career', 'Programming', 'English', 'Piano'];

    const loadVideosByCategory = async (category) => {
        selectedCategory.value = category;
        try {
            const response = await axios.get(route('exploreVideos.index'), { params: { category } });
            videos.value = response.data.videos;
        } catch (error) {
            console.error('Failed to load videos:', error);
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
        router.post(route('notes.store'), { video: selectedVideo.value });
    };

    return { videos, selectedVideo, showModal, selectedCategory, categories, loadVideosByCategory, openModal, closeModal, createNote };
}
