<template>
    <div class="h-screen flex flex-col overflow-hidden">
        <nav class="bg-gray-800 text-white p-4 w-full flex-shrink-0">
            <div class="flex justify-between items-center">
                <a href="/notes/index" class="text-lg font-bold">MyApp</a>
                <div>
                    <a href="/videos" class="px-4">動画検索</a>
                </div>
            </div>
        </nav>

        <!-- 修正: overflow-y-auto を追加 -->
        <main class="flex-1 overflow-y-auto">
            <slot />
        </main>
    </div>
</template>
