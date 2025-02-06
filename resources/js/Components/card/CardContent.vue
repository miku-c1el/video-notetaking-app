<template>
  <div
    class="p-6 pt-0"
    :class="className"
  >
    <slot></slot>
  </div>
</template>

<script setup>
defineProps({
  className: {
    type: String,
    default: ''
  }
})
</script>