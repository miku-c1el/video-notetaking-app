<template>
  <p
    class="text-sm text-gray-500"
    :class="className"
  >
    <slot></slot>
  </p>
</template>

<script setup>
defineProps({
  className: {
    type: String,
    default: ''
  }
})
</script>
