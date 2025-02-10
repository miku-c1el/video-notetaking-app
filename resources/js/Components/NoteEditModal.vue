<script setup>
import { ref, watch, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import debounce from 'lodash/debounce';
import TagEditModal from '@/Components/TagEditModal.vue';
import { 
    TransitionRoot, 
    TransitionChild, 
    Dialog, 
    DialogPanel 
} from '@headlessui/vue';
import { PlusIcon, PencilIcon, TrashIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    modelValue: Boolean,
    note: {
        type: Object,
        required: true
    },
    tags: Array
});

const emit = defineEmits(['update:modelValue', 'tag-updated']);

const title = ref(props.note?.title || '');
const tagInput = ref('');
const searchResults = ref([]);
const selectedTags = ref(props.note?.tags || []);
const isSearching = ref(false);
const showTagEditModal = ref(false);
const selectedTagForEdit = ref(null);

// 選択済みタグを除外した検索結果
const filteredSearchResults = computed(() => {
    if (!searchResults.value) return [];

    const selectedTagIds = selectedTags.value.map(tag => tag.id);
    return searchResults.value.filter(tag => !selectedTagIds.includes(tag.id));
});

// 入力されたタグが既存のタグと重複しているかチェック
const isDuplicateTag = computed(() => {
    if (!tagInput.value) return false;
    
    const normalizedInput = tagInput.value.toLowerCase().trim();
    
    // 検索結果内の重複をチェック
    const isDuplicateInSearch = searchResults.value.some(tag => 
        tag.name.toLowerCase() === normalizedInput
    );
    
    // 選択済みタグ内の重複をチェック
    const isDuplicateInSelected = selectedTags.value.some(tag =>
        tag.name.toLowerCase() === normalizedInput
    );
    
    return isDuplicateInSearch || isDuplicateInSelected;
});

// 検索結果の表示制御
const showDropdown = computed(() => {
    return (filteredSearchResults.value.length > 0 || (tagInput.value && !isDuplicateTag.value));
});

const debouncedSaveTitle = debounce(async (newTitle) => {
    router.patch(route('notes.update', props.note.id), 
        { title: newTitle },
        { 
            preserveState: true,
            preserveScroll: true,
            only: ['note']
        }
    );
}, 500);

const searchTags = debounce(async (query) => {
    if (!query) {
        searchResults.value = [];
        return;
    }

    try {
        const response = await fetch(route('api.tags.search', { query }));
        const data = await response.json();
        searchResults.value = data.tags;
    } catch (error) {
        console.error('Error searching tags:', error);
        searchResults.value = [];
    }
}, 300);

const createTag = async () => {
    if (!tagInput.value || isDuplicateTag.value) return;
    
    try {
        router.post(route('tags.store'), {
            name: tagInput.value,
            note_id: props.note.id,
        }, {
            preserveState: true,
            preserveScroll: true,
            onSuccess: () => {
                tagInput.value = '';
                isSearching.value = false;
                emit('tag-updated');
            }
        });
    } catch (error) {
        console.error('Error creating tag:', error);
    }
};

const detachTag = async (tagId) => {
    try {
        router.delete(route('notes.tags.destroy', [props.note.id, tagId]), {
            preserveState: true,
            preserveScroll: true,
            onSuccess: () => {
                selectedTags.value = selectedTags.value.filter(tag => tag.id !== tagId);
                emit('tag-updated');
            }
        });
    } catch (error) {
        console.error('Error detaching tag:', error);
    }
};

const handleTagSelect = async (tag) => {
    router.post(route('notes.tags.store', props.note.id), {
        tag_id: tag.id
    }, {
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => {
            tagInput.value = '';
            searchResults.value = [];
            isSearching.value = false;
            emit('tag-updated');
        }
    });
};

const editTag = async (tag) => {
    selectedTagForEdit.value = tag;
    showTagEditModal.value = true;
};

const handleTagUpdate = async (newName) => {
    if (!selectedTagForEdit.value) return;
    
    router.patch(route('tags.update', selectedTagForEdit.value.id), {
        name: newName
    }, {
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => {
            showTagEditModal.value = false;
            selectedTagForEdit.value = null;
            emit('tag-updated');
        }
    });
};

const closeTagEditModal = () => {
    showTagEditModal.value = false;
    selectedTagForEdit.value = null;
};

watch(tagInput, (newValue) => {
    searchTags(newValue);
});

const closeModal = () => {
    emit('update:modelValue', false);
};
</script>

<template>
    <TransitionRoot appear :show="modelValue" as="template">
        <Dialog as="div" @close="closeModal" class="relative z-50">
            <TransitionChild
                as="template"
                enter="duration-300 ease-out"
                enter-from="opacity-0"
                enter-to="opacity-100"
                leave="duration-200 ease-in"
                leave-from="opacity-100"
                leave-to="opacity-0"
            >
                <div class="fixed inset-0 bg-black bg-opacity-25" />
            </TransitionChild>

            <div class="fixed inset-0 overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4">
                    <TransitionChild
                        as="template"
                        enter="duration-300 ease-out"
                        enter-from="opacity-0 scale-95"
                        enter-to="opacity-100 scale-100"
                        leave="duration-200 ease-in"
                        leave-from="opacity-100 scale-100"
                        leave-to="opacity-0 scale-95"
                    >
                        <DialogPanel class="w-full max-w-md transform bg-white p-6 shadow-xl transition-all rounded-2xl">
                            <div class="space-y-4">
                                <!-- タイトル編集 -->
                                <div>
                                    <h4 class="text-sm font-medium mb-2">ノートの名前を変更</h4>
                                    <input
                                        type="text"
                                        v-model="title"
                                        @input="debouncedSaveTitle(title)"
                                        placeholder="ノート名を入力"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                    >
                                </div>

                                <!-- タグ編集 -->
                                <div>
                                    <h4 class="text-sm font-medium mb-2">タグ</h4>
                                    <!-- 既存のタグ表示 -->
                                    <div v-if="tags.length > 0" class="mb-3 flex flex-wrap gap-2">
                                        <div
                                            v-for="tag in tags"
                                            :key="tag.id"
                                            class="bg-gray-100 px-2 py-1 rounded-md text-sm flex items-center gap-1"
                                        >
                                            {{ tag.name }}
                                            <button
                                                @click="detachTag(tag.id)"
                                                class="text-gray-500 hover:text-gray-700 ml-1"
                                            >
                                                ×
                                            </button>
                                        </div>
                                    </div>
                                    <!-- タグ入力フィールド -->
                                    <div class="relative">
                                        <input
                                            type="text"
                                            v-model="tagInput"
                                            placeholder="タグを追加"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                        >
                                        <!-- 検索結果ドロップダウン -->
                                        <div
                                            v-if="tagInput && showDropdown"
                                            class="absolute left-0 right-0 mt-1 bg-white border rounded-md shadow-lg z-50 max-h-60 overflow-y-auto"
                                        >
                                            <div v-if="filteredSearchResults.length > 0">
                                                <div
                                                    v-for="tag in filteredSearchResults"
                                                    :key="tag.id"
                                                    class="px-4 py-2 hover:bg-gray-50 flex items-center justify-between group"
                                                >
                                                    <button
                                                        @click="handleTagSelect(tag)"
                                                        class="flex-1 text-left"
                                                    >
                                                        #{{ tag.name }}
                                                    </button>
                                                    <div class="hidden group-hover:flex items-center gap-2">
                                                        <button
                                                            @click="editTag(tag)"
                                                            class="text-gray-500 hover:text-gray-700"
                                                        >
                                                            <PencilIcon class="w-4 h-4" />
                                                        </button>
                                                            <!-- タグ編集モーダル - selectedTagForEditが存在する場合のみ表示 -->
                                                        <TagEditModal
                                                            v-if="selectedTagForEdit"
                                                            v-model="showTagEditModal"
                                                            :tag="selectedTagForEdit"
                                                            @save="handleTagUpdate"
                                                            @update:modelValue="closeTagEditModal"
                                                        />
                                                        <button
                                                            @click="detachTag(tag.id)"
                                                            class="text-gray-500 hover:text-gray-700"
                                                        >
                                                            <TrashIcon class="w-4 h-4" />
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            <button
                                                v-if="!isDuplicateTag"
                                                @click="createTag"
                                                class="w-full px-4 py-2 text-left hover:bg-gray-50 flex items-center gap-2"
                                            >
                                                <PlusIcon class="w-4 h-4" />
                                                <span>「{{ tagInput }}」を作成</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </DialogPanel>
                    </TransitionChild>
                </div>
            </div>
        </Dialog>
    </TransitionRoot>
</template>

<style scoped>
.fixed {
    z-index: 50;
}

.relative {
    z-index: 51;
}
</style>