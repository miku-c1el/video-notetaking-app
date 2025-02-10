<script setup>
import { ref, watch } from 'vue';
import { TransitionRoot, TransitionChild, Dialog, DialogPanel } from '@headlessui/vue';

const props = defineProps({
    modelValue: Boolean,
    tag: {
        type: Object,
        required: true
    }
});

const emit = defineEmits(['update:modelValue', 'save']);

const tagName = ref('');

// タグが変更されたときに tagName を更新
watch(() => props.tag, (newTag) => {
    if (newTag) {
        tagName.value = newTag.name;
    }
}, { immediate: true });

// モーダルが開閉されるときにもtagNameを更新
watch(() => props.modelValue, (isOpen) => {
    if (isOpen && props.tag) {
        tagName.value = props.tag.name;
    }
});

const handleSave = () => {
    emit('save', tagName.value);
    emit('update:modelValue', false);
};

const closeModal = () => {
    emit('update:modelValue', false);
};
</script>

<template>
    <TransitionRoot appear :show="modelValue" as="template">
        <Dialog as="div" @close="closeModal" class="relative z-[60]">
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
                            <h3 class="text-lg font-bold mb-4">タグの名前を変更</h3>
                            
                            <input
                                type="text"
                                v-model="tagName"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 mb-4"
                            >

                            <div class="flex justify-end gap-3">
                                <button
                                    @click="closeModal"
                                    class="px-4 py-2 text-gray-600 hover:text-gray-800"
                                >
                                    キャンセル
                                </button>
                                <button
                                    @click="handleSave"
                                    class="px-4 py-2 bg-coral-400 text-black rounded-md hover:bg-coral-500"
                                >
                                    確認
                                </button>
                            </div>
                        </DialogPanel>
                    </TransitionChild>
                </div>
            </div>
        </Dialog>
    </TransitionRoot>
</template>