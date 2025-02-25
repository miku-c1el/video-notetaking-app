<script setup>
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'
</script>

<template>
    <div class="min-h-screen bg-background">
        <!-- ヘッダー -->
        <header class="bg-primary-dark shadow-sm">
        <div class="max-w-8xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
            <!-- ロゴ -->
            <Link href="/notes/index" class="flex-shrink-0 text-xl font-semibold font-montserrat text-primary">
                SnapScribe
            </Link>

            <!-- 右側のナビゲーション -->
            <div class="flex items-center space-x-4">
                <!-- 動画検索ボタン -->
                <Link
                href="/videos"
                class="inline-flex items-center px-4 py-2 rounded-full bg-primary-light text-primary-dark hover:bg-primary hover:text-white transition-colors duration-200"
                >
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                >
                    <path
                    fill-rule="evenodd"
                    d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
                    clip-rule="evenodd"
                    />
                </svg>
                <span class="ml-2 text-m hidden md:inline font-semibold">動画を検索</span>
                </Link>

                <!-- ユーザーメニュー -->
                <Link
                href="/profile"
                class="p-2 rounded-full bg-primary-light hover:bg-primary transition-colors duration-200 hover:text-white"
                >
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                >
                    <path
                    fill-rule="evenodd"
                    d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                    clip-rule="evenodd"
                    />
                </svg>
                </Link>
            </div>
            </div>
        </div>
        </header>       
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <slot />
        </main>
    </div>
</template>
