<script setup>
import { ref, watch, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import debounce from 'lodash/debounce';
import TagEditModal from '@/Components/TagEditModal.vue';
import {
    TransitionRoot, 
    TransitionChild, 
    Dialog, 
    DialogPanel,
    DialogTitle
} from '@headlessui/vue';
import { PlusIcon, PencilIcon, TrashIcon } from '@heroicons/vue/24/outline';
import { XMarkIcon } from '@heroicons/vue/24/outline';

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
const showDeleteConfirm = ref(false);
const tagToDelete = ref(null);

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
            tagInput.value = '';
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

const confirmDelete = (tag) => {
    tagToDelete.value = tag;
    showDeleteConfirm.value = true;
};

const handleDelete = async () => {
    if (!tagToDelete.value) return;
    try {
        router.delete(route('tags.destroy', tagToDelete.value.id), {
            preserveState: true,
            preserveScroll: true,
            onSuccess: () => {
                showDeleteConfirm.value = false;
                tagToDelete.value = null;
                emit('tag-updated');
                tagInput.value = '';
            },
            onError: (errors) => {
                console.error('タグの削除に失敗しました:', errors);
                showDeleteConfirm.value = false;
            }
        });
    } catch (error) {
        console.error('予期せぬエラーが発生しました:', error);
        showDeleteConfirm.value = false;
    }
};

const cancelDelete = () => {
    showDeleteConfirm.value = false;
    tagToDelete.value = null;
    tagInput.value = '';
};
</script>

<template>
    <TransitionRoot appear :show="modelValue" as="div">
        <Dialog as="div" @close="closeModal" class="relative z-[100]">
            <!-- モーダルの背景 -->
            <TransitionChild
                as="div"
                enter="duration-300 ease-out"
                enter-from="opacity-0"
                enter-to="opacity-100"
                leave="duration-200 ease-in"
                leave-from="opacity-100"
                leave-to="opacity-0"
            >
                <div class="fixed inset-0 bg-black bg-opacity-25" />
            </TransitionChild>

            <!-- モーダルコンテンツ -->
            <div class="fixed inset-0 overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4">
                    <TransitionChild
                        as="div"
                        enter="duration-300 ease-out"
                        enter-from="opacity-0 scale-95"
                        enter-to="opacity-100 scale-100"
                        leave="duration-200 ease-in"
                        leave-from="opacity-100 scale-100"
                        leave-to="opacity-0 scale-95"
                        class="w-screen max-w-[70vw] md:max-w-[50vw] lg:max-w-[40vw]"
                    >
                        <DialogPanel class="relative w-full bg-white p-6 sm:p-8 shadow-xl transition-all rounded-2xl mx-auto">
                            <button 
                                @click="closeModal" 
                                class="absolute top-4 right-4 text-gray-400 hover:text-gray-500 focus:outline-none"
                            >
                                <XMarkIcon class="h-6 w-6" />
                            </button>

                            <div class="space-y-6">
                                <!-- ノート名編集 -->
                                <div>
                                    <h4 class="text-base font-medium mb-3">ノートの名前を変更</h4>
                                    <input
                                        type="text"
                                        v-model="title"
                                        @input="debouncedSaveTitle(title)"
                                        placeholder="ノート名を入力"
                                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-base p-3"
                                    >
                                </div>

                                <!-- タグ編集 -->
                                <div>
                                    <h4 class="text-base font-medium mb-3">タグ</h4>
                                    <!-- 既存のタグ表示 -->
                                    <div v-if="tags.length > 0" class="mb-4 flex flex-wrap gap-2">
                                        <div
                                            v-for="tag in tags"
                                            :key="tag.id"
                                            class="bg-gray-100 px-3 py-2 rounded-lg text-base flex items-center gap-2"
                                        >
                                            {{ tag.name }}
                                            <button
                                                @click="detachTag(tag.id)"
                                                class="text-gray-500 hover:text-gray-700"
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
                                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-base p-3"
                                        >
                                        <!-- 検索結果ドロップダウン -->
                                        <div
                                            v-if="tagInput && showDropdown"
                                            class="absolute left-0 right-0 mt-2 bg-white border rounded-lg shadow-lg z-50 max-h-72 overflow-y-auto"
                                        >
                                            <div v-if="filteredSearchResults.length > 0">
                                                <div
                                                    v-for="tag in filteredSearchResults"
                                                    :key="tag.id"
                                                    class="px-4 py-3 hover:bg-gray-50 flex items-center justify-between group"
                                                >
                                                    <button
                                                        @click="handleTagSelect(tag)"
                                                        class="flex-1 text-left text-base"
                                                    >
                                                        #{{ tag.name }}
                                                    </button>
                                                    <div class="hidden group-hover:flex items-center gap-3">
                                                        <button
                                                            @click="editTag(tag)"
                                                            class="text-gray-500 hover:text-gray-700"
                                                        >
                                                            <PencilIcon class="w-5 h-5" />
                                                        </button>
                                                        <button
                                                            @click="confirmDelete(tag)"
                                                            class="text-gray-500 hover:text-gray-700"
                                                        >
                                                            <TrashIcon class="w-5 h-5" />
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            <button
                                                v-if="!isDuplicateTag"
                                                @click="createTag"
                                                class="w-full px-4 py-3 text-left hover:bg-gray-50 flex items-center gap-3 text-base"
                                            >
                                                <PlusIcon class="w-5 h-5" />
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

            <!-- タグ編集モーダル -->
            <TagEditModal
                v-if="selectedTagForEdit"
                v-model="showTagEditModal"
                :tag="selectedTagForEdit"
                @save="handleTagUpdate"
                @update:modelValue="closeTagEditModal"
            />

            <!-- 削除確認モーダル -->
            <div
            v-if="showDeleteConfirm"
            class="relative z-60"
            >
                <div class="fixed inset-0 bg-black/30" aria-hidden="true" />
                
                <div class="fixed inset-0 flex items-center justify-center p-4">
                    <DialogPanel class="w-full max-w-sm rounded bg-white p-6 shadow-xl">
                        <DialogTitle class="text-lg font-medium mb-4">
                            タグの削除
                        </DialogTitle>
                        
                        <p class="mb-4">
                            「{{ tagToDelete?.name }}」を削除してもよろしいですか？
                        </p>
                        
                        <div class="flex justify-end gap-3">
                            <button
                                @click="cancelDelete"
                                class="px-4 py-2 text-gray-600 hover:text-gray-800"
                            >
                                キャンセル
                            </button>
                            <button
                                @click="handleDelete"
                                class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700"
                            >
                                削除
                            </button>
                        </div>
                    </DialogPanel>
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