import { ref, computed, watch } from 'vue';
import { router } from '@inertiajs/vue3';

export function useNotes(initialNotes, pagination, filters) {
    const notes = ref(initialNotes?.data ?? []);
    const selectedTags = ref(filters?.tags || []);
    const page = ref(pagination.current_page || 0);
    const hasMore = ref(pagination.has_more || false);
    const isLoading = ref(false);

    const displayedNotes = computed(() => {
        if (!Array.isArray(notes.value)) {
            console.warn('notes.value is not an array:', notes.value);
            return [];
        }
        return notes.value;
    });

    const loadMoreNotes = async () => {
        if (isLoading.value || !hasMore.value) return;

        isLoading.value = true;
        page.value += 1;
        try {
            const params = new URLSearchParams({
                page: page.value,
                tags: JSON.stringify(selectedTags.value),
            });

            const response = await fetch(`/api/notes?${params}`);
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);

            const data = await response.json();
            if (data.data.data?.length > 0) {
                notes.value = [...notes.value, ...data.data.data];
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

    const deleteNote = (id) => {
        if (confirm('このノートを削除してもよろしいですか？')) {
            router.delete(`/notes/${id}`);
        }
    };

    const handleNoteUpdate = (updatedNote) => {
        if (!updatedNote?.id) {
            console.warn('Invalid updated note received:', updatedNote);
            return;
        }
        notes.value = notes.value.map(note => note.id === updatedNote.id ? { ...note, ...updatedNote } : note);
    };

    // タグに変更があればデータをリセット
    watch(selectedTags, () => {
        notes.value = [];
        page.value = 1;
        hasMore.value = true;
        loadMoreNotes();
    });

    return { notes, displayedNotes, isLoading, hasMore, loadMoreNotes, deleteNote, handleNoteUpdate };
}
