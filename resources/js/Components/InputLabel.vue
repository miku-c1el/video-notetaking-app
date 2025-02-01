<script setup>
defineProps({
    value: {
        type: String,
    },
    required: {
        type: Boolean,
        default: false,
    },
});
</script>

<template>
    <label class="block font-medium text-sm text-gray-700 flex items-center justify-between">
        <span v-if="value">{{ value }}</span>
        <span v-else><slot /></span>

        <span v-if="required" class="bg-red-500 text-white text-xs py-1 px-2 rounded-[5px] ml-auto">必須</span>
    </label>
</template>
