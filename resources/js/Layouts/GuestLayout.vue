<script setup>
import { computed } from 'vue';

const props = defineProps({
  status: String,
});

// 必要に応じて追加のロジック
</script>

<template>
  <div class="min-h-screen flex bg-[#f6f5f4]">
    <!-- 左側の装飾部分 -->
    <div class="hidden lg:flex lg:w-1/2 relative decorative-bg">
      <div class="relative z-10 flex items-center justify-center w-full">
        <h1 class="text-6xl font-bold text-primary-light">Create Account</h1>
      </div>
    </div>
    <!-- 右側のフォーム部分 -->
    <div class="w-full lg:w-1/2 flex items-center justify-center p-8">
      <div class="w-full max-w-md space-y-8">
        <div class="text-center">
          <h2 class="text-3xl font-bold text-primary-dark">Sign Up</h2>
        </div>

        <!-- ステータスメッセージ -->
        <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
          {{ status }}
        </div>

        <!-- スロットでフォームを受け取る -->
        <slot />
      </div>
    </div>
  </div>
</template>

<style scoped>
.decorative-bg {
  background-image: url('/images/authentication_bg.svg');
  background-size: cover;
  background-position: center;
  background-repeat: no-repeat;
}
</style>

