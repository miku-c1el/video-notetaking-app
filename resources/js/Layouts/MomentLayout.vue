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

        <main class="flex-1 overflow-hidden">
            <slot />
        </main>
    </div>
</template>
