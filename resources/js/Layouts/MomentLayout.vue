<template>
    <div>
        <nav class="bg-gray-800 text-white p-4">
            <div class="container mx-auto flex justify-between">
                <a href="/" class="text-lg font-bold">MyApp</a>
                <div>
                    <a href="/videos" class="px-4">動画検索</a>
                </div>
            </div>
        </nav>

        <main class="container mt-6">
            <slot />
        </main>
    </div>
</template>