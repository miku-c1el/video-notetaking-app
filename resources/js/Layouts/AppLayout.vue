<template>
    <div>
        <nav class="bg-white text-black shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <div class="flex items-center">
                        <img src="/path-to-your-logo.png" alt="Logo" class="h-8 w-auto" />
                        <a href="/notes/index" class="ml-3 text-xl font-semibold">MyApp</a>
                    </div>
                    <a href="/videos" class="px-4 py-2 bg-orange-300 hover:bg-orange-400 rounded-full">
                      <span class="mr-2">+</span>
                      動画を検索する
                    </a>
                </div>
            </div>
        </nav>
        <main class="container mx-auto mt-6">
            <slot />
        </main>
    </div>
</template>
