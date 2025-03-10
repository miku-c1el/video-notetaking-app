<script setup>
import { ref, watch, computed, watchEffect, nextTick} from 'vue';
import { router} from '@inertiajs/vue3';
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
import axios from 'axios';
import { filter } from 'lodash';

const props = defineProps({
    modelValue: Boolean,
    note: {
        type: [Object, null],
        default: null
    },
    tags : Array
});

const emit = defineEmits(['update:modelValue', 'tag-updated', 'updated', 'close']);
const title = ref(props.note?.title || '');
const tagInput = ref('');
const confirmedInput = ref('');
const searchResults = ref([]);
const selectedTags = ref(props.note?.tags || []);
const isSearching = ref(false);
const showTagEditModal = ref(false);
const selectedTagForEdit = ref(null);
const showDeleteConfirm = ref(false);
const tagToDelete = ref(null);
const selectedIndex = ref(-1);
const isCheckingDuplicate = ref(true);

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

watch([tagInput, searchResults, selectedTags], async () => {
    isCheckingDuplicate.value = true;
    
    await nextTick(); // Wait for Vue to process updates
    
    isCheckingDuplicate.value = false;
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
            // only: ['note'],
            onSuccess: (response) => {
                emit('updated', { id: props.note.id, title: newTitle });
            }
        }
    );
}, 500);

const searchTags = debounce(async (query) => {
    if (!query) {
        searchResults.value = [];
        return;
    }

    try {
        const response = await axios.get(route('api.tags.search', { query }));
        const data = await response.data;
        searchResults.value = data.tags;
    } catch (error) {
        console.error('Error searching tags:', error);
        searchResults.value = [];
    }
}, 300);

const createTag = async () => {
    const tagName = tagInput.value.trim();
    if (!tagName || isDuplicateTag.value) return;
    try {
        router.post(route('tags.store'), {
            name: tagName,
            note_id: props.note.id,
        }, {
            preserveState: true,
            preserveScroll: true,
            onSuccess: () => {
                tagInput.value = '';
                isSearching.value = false;
                confirmedInput.value = '';
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
        onSuccess: (response) => {
            showTagEditModal.value = false;
            selectedTagForEdit.value = null;
            confirmedInput.value = '';
            emit('tag-updated');
            emit('updated', props.note);
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
    tagInput.value = '';
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

// モーダルが開かれるたびにタイトルを更新
watch(() => props.modelValue, (newValue) => {
    if (newValue && props.note) {
        title.value = props.note.title || '';
    }
});

// モーダルが開かれるたびにタイトルを更新
watch(() => props.modelValue, (newValue) => {
    if (newValue && props.note) {
        title.value = props.note.title || '';
    }
});

// noteプロパティの変更も監視
watch(() => props.note, (newNote) => {
    if (newNote) {
        title.value = newNote.title || '';
    }
});

const handleKeyDown = async (event) => {
    if (!showDropdown.value) return;
    
    switch (event.key) {
        case 'ArrowDown':
            event.preventDefault();
            selectedIndex.value = Math.min(
                selectedIndex.value + 1, 
                filteredSearchResults.value.length + (isDuplicateTag.value ? 0 : 1) - 1
            );
            break;
        case 'ArrowUp':
            event.preventDefault();
            selectedIndex.value = Math.max(selectedIndex.value - 1, -1);
            break;
        case 'Enter':
            event.preventDefault();

            if (selectedIndex.value >= 0 && selectedIndex.value < filteredSearchResults.value.length) {
                handleTagSelect(filteredSearchResults.value[selectedIndex.value]);
                return;
            }

            if (confirmedInput.value === tagInput.value.trim()) {
                createTag();
            } else {
                confirmedInput.value = tagInput.value.trim();
            }
            break;
        case 'Escape':
            event.preventDefault();
            selectedIndex.value = -1;
            break;
    }
};
</script>

<template>
    <TransitionRoot appear :show="modelValue" as="div">
        <Dialog as="div" @close="closeModal" class="relative z-[200]">
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
                                    <h4 class="text-base font-semibold mb-3">ノートの名前を変更</h4>
                                    <input
                                        type="text"
                                        v-model="title"
                                        @input="debouncedSaveTitle(title)"
                                        placeholder="ノート名を入力"
                                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 border-gray-200 focus:outline-none focus:ring-0 focus:border-gray-200 focus:shadow-none text-base p-3"
                                    >
                                </div>

                                <!-- タグ編集 -->
                                <div>
                                    <h4 class="text-base font-semibold mb-3">タグ</h4>
                                    <!-- 既存のタグ表示 -->
                                    <div v-if="tags.length > 0" class="mb-4 flex flex-wrap gap-2">
                                        <div
                                            v-for="tag in tags"
                                            :key="tag.id"
                                            class="bg-gray-100 px-3 py-2 rounded-lg text-base flex items-center gap-2"
                                        >
                                            # {{ tag.name }}
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
                                            @keydown="handleKeyDown"
                                            placeholder="タグを追加"
                                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 border-gray-200 focus:outline-none focus:ring-0 focus:border-gray-200 focus:shadow-none text-base p-3"
                                        >
                                        <!-- 検索結果ドロップダウン -->
                                        <div
                                            v-if="tagInput && showDropdown"
                                            class="absolute left-0 right-0 mt-2 bg-white border rounded-lg shadow-lg z-50 max-h-72 overflow-y-auto"
                                        >
                                            <div v-if="filteredSearchResults.length > 0" class="overflow-y-auto">
                                                <div
                                                    v-for="(tag, index) in filteredSearchResults"
                                                    :key="tag.id"
                                                    :class="[
                                                        'px-4 py-3 hover:bg-gray-50 flex items-center justify-between group',
                                                        selectedIndex === index ? 'bg-gray-100' : ''
                                                    ]"
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
                                                v-if="!isCheckingDuplicate && !isDuplicateTag"
                                                @click="createTag"
                                                v-on:keyup.enter="createTag"
                                                :class="[
                                                    'w-full px-4 py-3 text-left hover:bg-gray-50 flex items-center gap-3 text-base',
                                                    selectedIndex === filteredSearchResults.length ? 'bg-gray-100' : ''
                                                ]"
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
                        <DialogTitle class="text-lg font-semibold mb-4">
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
                                class="px-4 py-2 bg-primary text-white rounded-md hover:bg-primary-light hover:text-primary-dark"
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