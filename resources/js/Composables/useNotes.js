import { ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';

export function useNotes(initialNotes, pagination, filters) {
    const notes = ref(initialNotes?.data ?? []);
    const selectedTags = ref(filters?.tags || []);
    const page = ref(pagination.current_page || 0);
    const hasMore = ref(pagination.has_more || false);
    const isLoading = ref(false);

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
          
          if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
          }
      
          const result = await response.json();
          if (result.notes && Array.isArray(result.notes) && result.notes.length > 0) {
            notes.value = Array.isArray(notes.value) 
              ? [...notes.value, ...result.notes]
              : result.notes;
            hasMore.value = result.current_page < result.last_page;
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

    const showNote = (note) => {
        router.get(route('notes.show', note.id), 
              {noteId: note.id}
          );
    };

    const handleDeleteNote = (note_id) => {
        router.delete(`/notes/${note_id}`, {
          preserveScroll: true,
          onSuccess: () => {
            // ノート一覧を更新
            notes.value = notes.value.filter(note => note.id !== note_id);
          }
        });
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

    return { notes, isLoading, hasMore, selectedTags, page, loadMoreNotes, handleNoteUpdate, handleDeleteNote, showNote };
}
