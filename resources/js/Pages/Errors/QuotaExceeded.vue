<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { TriangleAlert } from 'lucide-vue-next';

const page = usePage();
const message = page.props.flash && page.props.flash.message 
  ? page.props.flash.message 
  : '検索リクエストの上限に達しました。しばらくしてからお試しください。';
</script>

<template>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <Head title="検索制限" />

        <div class="flex flex-col items-center justify-center min-h-screen py-12 px-4 sm:px-6 lg:px-8">
            <div class="max-w-md w-full space-y-6 text-center">
                <TriangleAlert class="w-16 h-16 text-primary-dark mx-auto" />
                <div>
                    <h1 class="text-3xl font-extrabold text-primary">検索制限に達しました</h1>
                    <p class="mt-4 text-lg text-gray-600">{{ message }}</p>
                </div>
                <div class="mt-8">
                    <p class="text-gray-600 text-sm">
                        エクスプロアタブの動画は引き続きご覧いただけます。
                    </p>

                </div>
                <div class="mt-6">
                    <Link class="px-4 py-2 bg-primary rounded-md text-sm text-primary-light hover:text-primary-dark hover:bg-primary-light" :href="route('notes.index')">
                        ノート一覧に戻る
                    </Link>
                </div>
            </div>
        </div>
    </div>
</template>