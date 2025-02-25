<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { Link } from '@inertiajs/vue3'

// ドロップダウンメニューの表示状態を管理
const isDropdownOpen = ref(false)

// ドロップダウンメニューの表示・非表示を切り替える
const toggleDropdown = () => {
  isDropdownOpen.value = !isDropdownOpen.value
}

// ドロップダウンメニュー以外の場所をクリックした時にメニューを閉じる
const closeDropdown = (e) => {
  if (!e.target.closest('.dropdown-container')) {
    isDropdownOpen.value = false
  }
}

// コンポーネントがマウントされたときにイベントリスナーを設定
// コンポーネントがアンマウントされたときにイベントリスナーを削除
onMounted(() => {
  document.addEventListener('click', closeDropdown)
})

onUnmounted(() => {
  document.removeEventListener('click', closeDropdown)
})
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
                        <div class="relative dropdown-container">
                            <button
                                @click.stop="toggleDropdown"
                                class="p-2 rounded-full bg-primary-light hover:bg-primary transition-colors duration-200 hover:text-white flex items-center"
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
                            </button>

                            <!-- ドロップダウンメニュー -->
                            <div
                                v-if="isDropdownOpen"
                                class="absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 z-10"
                            >
                                <Link
                                :href="route('profile.edit')"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                >
                                Profile
                                </Link>
                                <Link
                                :href="route('logout')"
                                method="post"
                                as="button"
                                class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                >
                                Log Out
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>       
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <slot />
        </main>
    </div>
</template>
