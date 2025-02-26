<script setup>
import { defineComponent } from 'vue';
import { Head } from '@inertiajs/vue3';
import WelcomeLayout from '@/Layouts/WelcomeLayout.vue';
</script>

<template>
    <div>
        <WelcomeLayout>
            <Head title="Welcome" />
            <div class="min-h-screen bg-background">
                <!-- Hero Section -->
                <section class="bg-primary-light py-16">
                <div class="container mx-auto px-4 flex flex-col md:flex-row items-center justify-between">
                    <div class="md:w-1/2 mb-8 md:mb-0">
                    <h1 class="text-3xl font-bold mb-2">動画の学びを、あなたのものに。</h1>
                    <p class="text-gray-600 mb-8">
                        ワンクリックで大事なシーンを逃さず記録して、いつでも振り返り。
                    </p>
                    <button class="bg-gray-800 text-white px-6 py-2 rounded">無料会員登録 →</button>
                    </div>
                    <div class="md:w-1/2">
                    <img src="/images/hero-image.jpg" alt="Two people working together" class="rounded-md shadow-lg" />
                    </div>
                </div>
                </section>

                <!-- Japanese Section Title -->
                <section class="py-12 text-center">
                <h2 class="text-xl">SnapScribeで、動画の学びと感動をもっと自由に</h2>
                </section>

                <!-- Feature Section 1 -->
                <section class="py-12 bg-white">
                <div class="container mx-auto px-4 flex flex-col md:flex-row items-center justify-between">
                    <div class="md:w-1/2 mb-8 md:mb-0">
                    <h2 class="text-2xl font-bold mb-4">大事な瞬間を即キャプチャ</h2>
                    <p class="text-gray-600 mb-4">
                        動画を見ながら、気になったシーンをスナップ＆メモ。<br>
                        重要な学びや感動の瞬間をその場で記録できます。
                    </p>
                    <p class="text-gray-600">
                        趣味にも勉強にも使える(下の箇所に移動)<br>
                        好きな映画の名シーンや、大事な講義のポイントをすぐに保存。使い方はあなた次第！
                    </p>
                    </div>
                    <div class="md:w-1/2 flex justify-center">
                    <img src="/images/app-screens.png" alt="App screens" class="max-w-xs" />
                    </div>
                </div>
                </section>

                <!-- Feature Section 2 -->
                <section class="py-12 bg-white">
                <div class="container mx-auto px-4 flex flex-col md:flex-row-reverse items-center justify-between">
                    <div class="md:w-1/2 mb-8 md:mb-0">
                    <h2 class="text-2xl font-bold mb-4">ワンクリックで特定のシーンへ</h2>
                    <p class="text-gray-600 mb-4">
                        保存したスナップをクリックするだけで、動画の該当シーンに即ジャンプ。<br>
                        見直したい場面を探す手間がゼロに。
                    </p>
                    </div>
                    <div class="md:w-1/2">
                    <img src="/images/person-illustration.png" alt="Person illustration" class="max-w-xs mx-auto" />
                    </div>
                </div>
                </section>

                <!-- Feature Section 3 -->
                <section class="py-12 bg-white">
                <div class="container mx-auto px-4 flex flex-col md:flex-row items-center justify-between">
                    <div class="md:w-1/2 mb-8 md:mb-0">
                    <h2 class="text-2xl font-bold mb-4">自由にタグ付けしてあなた専用の動画ライブラリを構築</h2>
                    <p class="text-gray-600 mb-4">
                        学習用メモ、好きな名言集、推しのシーンコレクション…<br>
                        タグを活用して、自分だけのデータベースを作ろう！
                    </p>
                    </div>
                    <div class="md:w-1/2 flex justify-center">
                    <img src="/images/mobile-app.png" alt="Mobile app" class="max-w-xs" />
                    </div>
                </div>
                </section>

                <!-- "Inspod" Section -->
                <section class="py-16 bg-orange-100">
                    <div class="container mx-auto px-4">
                        <h2 class="text-center mb-12 text-xl">趣味にも自己学習にも使えるSnapScribe<br>使い方はあなた次第！</h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="bg-white p-6 rounded-md shadow-sm">
                                <h3 class="font-semibold mb-4">ビジネススキル</h3>
                                <p class="text-sm text-gray-600">
                                業界の専門家やトップリーダーによる講演、スタートアップの成功事例、効率的な仕事術などを学べるビジネス系チャンネルの内容を素早くノートにまとめられます。重要なポイントをメモして、実践に活かしましょう。
                                </p>
                            </div>
                            <div class="bg-white p-6 rounded-md shadow-sm">
                                <h3 class="font-semibold mb-4">言語学習</h3>
                                <p class="text-sm text-gray-600">
                                英語やその他の言語を学ぶためのYouTubeチャンネルから、発音や文法のポイントをスナップ。リスニングのコツやネイティブ表現を記録し、いつでも復習できるように整理しましょう。
                                </p>
                            </div>
                            <div class="bg-white p-6 rounded-md shadow-sm">
                                <h3 class="font-semibold mb-4">旅行</h3>
                                <p class="text-sm text-gray-600">
                                世界中の旅行系YouTubeチャンネルから、現地のおすすめスポットや役立つ情報をスナップ。行きたい場所や旅の計画をメモして、次の旅行をよりスムーズに楽しめます。
                                </p>
                            </div>
                            <div class="bg-white p-6 rounded-md shadow-sm">
                                <h3 class="font-semibold mb-4">音楽</h3>
                                <p class="text-sm text-gray-600">
                                好きなアーティストのライブ映像や、楽器の演奏レッスン動画をスナップ。コード進行やテクニックを記録し、練習に役立てることができます。好きな音楽の瞬間を保存して、何度でも楽しみましょう。
                                </p>
                            </div>
                        </div>
                    </div>
                </section>
                <section>
                    <div>
                        <h2>SnapScribeを活用してあなたの動画体験をアップグレードしませんか？</h2>
                        
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
                </section>

                <!-- Footer -->
                <footer class="bg-indigo-50 pt-16 pb-6">
                <div class="container mx-auto px-4">
                    <div class="flex justify-between items-center border-b border-gray-200 pb-6 mb-6">
                    <div class="flex-shrink-0 text-sm font-semibold font-montserrat text-primary">
                        SnapScribe
                    </div>
                    </div>
                </div>
                </footer>
            </div>
        </WelcomeLayout>
    </div>
</template>


