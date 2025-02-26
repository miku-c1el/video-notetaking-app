<script setup>
import { Link } from '@inertiajs/vue3'

</script>

<template>
    <div class="min-h-screen bg-background">
        <!-- ヘッダー -->
        <header class="bg-primary-dark shadow-sm">
            <div class="max-w-8xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <!-- ロゴ -->
                    <Link href="/" class="flex-shrink-0 text-xl font-semibold font-montserrat text-primary">
                        SnapScribe
                    </Link>

                    <!-- 右側のナビゲーション -->
                    <div class="flex items-center space-x-4">
                        
                        <!-- ログイン -->
                        <Link
                            :href="route('login')"
                            class="inline-flex items-center px-4 py-2 rounded-full text-primary-light hover:bg-primary transition-colors duration-200"
                        >
                            <span class="text-m font-semibold">ログイン</span>
                        </Link>

                        <!-- 会員登録 -->
                        <Link
                        :href="route('register')"
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
                            <span class="ml-2 text-m font-semibold">無料会員登録</span>
                        </Link>
                    </div>
                </div>
            </div>
        </header>       
        <main class="">
            <slot />
        </main>
    </div>
</template>