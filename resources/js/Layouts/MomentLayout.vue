<script setup>
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import Layout from '@/Layouts/AppLayout.vue';
</script>

<template>

    <div class="h-screen flex flex-col overflow-hidden">
        <nav class="bg-gray-800 text-white p-4 w-full flex-shrink-0">
            <div class="flex justify-between items-center">
                <Link href="/notes/index" class="flex-shrink-0 text-xl font-semibold font-montserrat text-primary">
                    SnapScribe
                </Link>
                <div>
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
                </div>
            </div>
        </nav>

        <main class="flex-1 overflow-y-auto">
            <slot />
        </main>
    </div>
</template>
