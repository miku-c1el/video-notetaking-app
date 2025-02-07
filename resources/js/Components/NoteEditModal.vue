<!-- components/NoteEditModal.vue -->
<script setup>
import { ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import debounce from 'lodash/debounce';
import { 
    TransitionRoot, 
    TransitionChild, 
    Dialog, 
    DialogPanel 
} from '@headlessui/vue';
import { PlusIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    modelValue: Boolean,
    note: {
        type: Object,
        required: true
    }
});

const emit = defineEmits(['update:modelValue']);

const title = ref(props.note?.title || '');
const tagInput = ref('');
const searchResults = ref([]);
const selectedTags = ref(props.note?.tags || []);
const showCreateTag = ref(false);

// タイトル自動保存のdebounce処理
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

// タグ検索
const searchTags = debounce(async (query) => {
    if (!query) {
        searchResults.value = [];
        showCreateTag.value = false;
        return;
    }

    try {
        const response = await fetch(route('api.tags.search', { query }));
        const data = await response.json();
        searchResults.value = data.tags;
        showCreateTag.value = !data.tags.length && query.length > 0;
    } catch (error) {
        console.error('Error searching tags:', error);
    }
}, 300);

// タグ作成
const createTag = async () => {
    try {
        router.post(route('tags.store'), {
            name: tagInput.value,
            note_id: props.note.id,
        }, {
            preserveState: true,
            preserveScroll: true,
            onSuccess: () => {
                tagInput.value = '';
                showCreateTag.value = false;
            }
        });
    } catch (error) {
        console.error('Error creating tag:', error);
    }
};

// タグ選択
const handleTagSelect = async (tag) => {
    router.post(route('notes.tags.store', props.note.id), {
        tag_id: tag.id
    }, {
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => {
            tagInput.value = '';
            searchResults.value = [];
        }
    });
};

// タグ削除
const removeTag = async (tagId) => {
    router.delete(route('notes.tags.destroy', [props.note.id, tagId]), {
        preserveState: true,
        preserveScroll: true
    });
};

watch(tagInput, (newValue) => {
    searchTags(newValue);
});

// モーダルを閉じる
const closeModal = () => {
    emit('update:modelValue', false);
};
</script>

<template>
    <TransitionRoot appear :show="modelValue" as="template">
        <Dialog as="div" @close="closeModal" class="relative z-50"> <!-- z-indexを上げる -->
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
                        <DialogPanel class="w-full max-w-md transform bg-white p-6 shadow-xl transition-all rounded-2xl relative"> <!-- relativeを追加 -->
                            <div class="space-y-4">
                                <!-- タイトル編集 -->
                                <div>
                                    <h4 class="text-sm font-medium mb-2">ノートの名前を変更</h4>
                                    <input
                                        type="text"
                                        v-model="title"
                                        @input="debouncedSaveTitle(title)"
                                        placeholder="現在の名前"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                    >
                                </div>

                                <!-- タグ編集 -->
                                <div>
                                    <h4 class="text-sm font-medium mb-2">タグ</h4>
                                    <div class="flex flex-wrap gap-2 mb-2">
                                        <span
                                            v-for="tag in selectedTags"
                                            :key="tag.id"
                                            class="bg-gray-100 px-2 py-1 rounded-md text-sm flex items-center gap-1"
                                        >
                                            #{{ tag.name }}
                                            <button
                                                @click="removeTag(tag.id)"
                                                class="text-gray-500 hover:text-gray-700"
                                            >
                                                ×
                                            </button>
                                        </span>
                                    </div>

                                    <div class="relative">
                                        <input
                                            type="text"
                                            v-model="tagInput"
                                            placeholder="検索、もしくは追加"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                        >

                                        <!-- ドロップダウンのスタイリングを修正 -->
                                        <div
                                            v-if="searchResults.length > 0 || showCreateTag"
                                            class="absolute left-0 right-0 mt-1 bg-white border rounded-md shadow-lg z-50 max-h-60 overflow-y-auto"
                                            style="min-width: 100%"
                                        >
                                            <button
                                                v-for="tag in searchResults"
                                                :key="tag.id"
                                                @click="handleTagSelect(tag)"
                                                class="w-full px-4 py-2 text-left hover:bg-gray-50 flex items-center"
                                            >
                                                #{{ tag.name }}
                                            </button>
                                            <button
                                                v-if="showCreateTag"
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

<style>
/* オーバーレイのz-indexを確実に最前面にする */
.fixed {
    z-index: 50;
}

/* ドロップダウンメニューが他の要素の下に隠れないようにする */
.relative {
    z-index: 51;
}
</style>
