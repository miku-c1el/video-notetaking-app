<script setup>
import { defineComponent } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import WelcomeLayout from '@/Layouts/WelcomeLayout.vue';
</script>

<template>
    <div>
        <WelcomeLayout>
            <Head title="Welcome" />
            <div class="min-h-screen bg-background">
                <!-- Hero Section -->
                <section class="bg-primary-light py-16">
                    <div class="container mx-auto px-4 sm:px-6 lg:px-8 max-w-6xl">
                        <div class="flex flex-col md:flex-row items-center justify-between">
                            <div class="md:w-1/2 mb-8 md:mb-0">
                                <h1 class="text-4xl font-bold text-dark mb-6">動画の学びを、あなたのものに。</h1>
                                <p class="text-textBody text-lg mb-8">
                                    ワンクリックで大事なシーンを逃さず記録して、いつでも振り返り。
                                </p>
                                <Link
                                    :href="route('register')"
                                >
                                    <span class="bg-primary hover:bg-opacity-90 text-white font-medium px-8 py-3 rounded-md inline-block shadow-md transition">無料会員登録 →</span>
                                </Link>
                            </div>
                            <div class="md:w-1/2 flex justify-center">
                                <img src="/images/service.svg" alt="A person watching youtube" class="max-w-full" />
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Japanese Section Title -->
                <section class="py-16 text-center">
                    <h2 class="text-3xl font-bold text-primary-dark text-center mb-12 relative">
                        <span class="inline-block relative">
                            SnapScribeの特徴
                            <span class="absolute bottom-0 left-0 w-full h-1 bg-primary"></span>
                        </span>
                    </h2>

                    <!-- Feature Section 1 -->
                    <div class="container mx-auto px-4 mb-10 md:mb-5 lg:px-8 xl:px-12 lg:max-w-6xl xl:max-w-7xl">
                        <div class="flex flex-col md:flex-row items-center justify-center gap-6">
                            <div class="md:w-2/5 text-center md:text-left">
                                <div class="flex flex-col items-center md:items-center mb-4">
                                    <div class="bg-primary h-12 w-12 rounded-full flex items-center justify-center mb-3">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <h2 class="text-2xl font-bold text-primary-dark">大事な瞬間を即キャプチャ</h2>
                                </div>
                                <p class="text-textMuted text-lg leading-relaxed">
                                    動画を見ながら、気になったシーンをスナップ＆メモ。<br>
                                    重要な学びや感動の瞬間をその場で記録できます。
                                </p>
                            </div>
                            <div class="md:w-2/5 flex justify-center items-center">
                                <img src="/images/timestamp.svg" alt="App screens" class="max-w-xs hover:opacity-90 transition-opacity" />
                            </div>
                        </div>
                    </div>

                    <!-- Feature Section 2 -->
                    <div class="container mx-auto px-4 mb-16 md:mb-12 lg:px-8 xl:px-12 lg:max-w-6xl xl:max-w-7xl">
                        <div class="flex flex-col md:flex-row-reverse items-center justify-center gap-6">
                            <div class="md:w-2/5 text-center md:text-left">
                                <div class="flex flex-col items-center md:items-center mb-4">
                                    <div class="bg-primary h-12 w-12 rounded-full flex items-center justify-center mb-3">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h18" />
                                        </svg>
                                    </div>
                                    <h2 class="text-2xl font-bold text-primary-dark">ワンクリックで特定のシーンへ</h2>
                                </div>
                                <p class="text-textMuted text-lg leading-relaxed">
                                    保存したスナップをクリックするだけで、動画の該当シーンに即ジャンプ。<br>
                                    見直したい場面を探す手間がゼロに。
                                </p>
                            </div>
                            <div class="md:w-2/5 flex justify-center items-center">
                                <img src="/images/rewind.svg" alt="Person illustration" class="max-w-xs hover:opacity-90 transition-opacity" />
                            </div>
                        </div>
                    </div>

                    <!-- Feature Section 3 -->
                    <div class="container mx-auto px-4 mb-12 lg:px-8 xl:px-12 lg:max-w-6xl xl:max-w-7xl">
                        <div class="flex flex-col md:flex-row items-center justify-center gap-6">
                            <div class="md:w-2/5 text-center md:text-left">
                                <div class="flex flex-col items-center md:items-center mb-4">
                                    <!-- アイコンをh2の上に配置 -->
                                    <div class="bg-primary h-12 w-12 rounded-full flex items-center justify-center mb-3">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                        </svg>
                                    </div>
                                    <h2 class="text-2xl font-bold text-primary-dark">自由にタグ付けしてノートを整理</h2>
                                </div>
                                <p class="text-textMuted text-lg leading-relaxed">
                                    学習用メモ、好きな名言集、推しのシーンコレクション…<br>
                                    タグを活用して、自分だけのライブラリを作ろう！
                                </p>
                            </div>
                            <div class="md:w-2/5 flex justify-center items-center">
                                <img src="/images/tag.svg" alt="Mobile app" class="max-w-xs hover:opacity-90 transition-opacity" />
                            </div>
                        </div>
                    </div>
                </section>

                <!-- 使用例 Section -->
                <section class="py-16 bg-primary-light">
                    <div class="container mx-auto px-4 sm:px-6 lg:px-8 max-w-6xl">
                        <h2 class="text-center mb-12 text-3xl font-bold text-primary-dark">
                            <p class="mb-2">趣味にも自己学習にも使えるSnapScribe</p>
                            <p>使い方はあなた次第！</p>
                        </h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 sm:gap-8 lg:gap-10">
                            <div class="bg-white p-5 sm:p-6 lg:p-8 rounded-md shadow-sm">
                                <div class="flex items-start mb-4">
                                    <div class="bg-primary h-10 w-10 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                                            <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                                        </svg>
                                    </div>
                                    <h3 class="font-semibold text-lg">ビジネススキル</h3>
                                </div>
                                <p class="text-sm text-gray-600 ml-0 sm:ml-13">
                                業界の専門家やトップリーダーによる講演、スタートアップの成功事例、効率的な仕事術などを学べるビジネス系チャンネルの内容を素早くノートにまとめられます。重要なポイントをメモして、実践に活かしましょう。
                                </p>
                            </div>
                            <div class="bg-white p-5 sm:p-6 lg:p-8 rounded-md shadow-sm">
                                <div class="flex items-start mb-4">
                                    <div class="bg-primary h-10 w-10 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                                        </svg>
                                    </div>
                                    <h3 class="font-semibold text-lg">言語学習</h3>
                                </div>
                                <p class="text-sm text-gray-600 ml-0 sm:ml-13">
                                英語やその他の言語を学ぶためのYouTubeチャンネルから、発音や文法のポイントをスナップ。リスニングのコツやネイティブ表現を記録し、いつでも復習できるように整理しましょう。
                                </p>
                            </div>
                            <div class="bg-white p-5 sm:p-6 lg:p-8 rounded-md shadow-sm">
                                <div class="flex items-start mb-4">
                                    <div class="bg-primary h-10 w-10 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <circle cx="12" cy="12" r="10"></circle>
                                            <line x1="2" y1="12" x2="22" y2="12"></line>
                                            <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path>
                                        </svg>
                                    </div>
                                    <h3 class="font-semibold text-lg">旅行</h3>
                                </div>
                                <p class="text-sm text-gray-600 ml-0 sm:ml-13">
                                世界中の旅行系YouTubeチャンネルから、現地のおすすめスポットや役立つ情報をスナップ。行きたい場所や旅の計画をメモして、次の旅行をよりスムーズに楽しめます。
                                </p>
                            </div>
                            <div class="bg-white p-5 sm:p-6 lg:p-8 rounded-md shadow-sm">
                                <div class="flex items-start mb-4">
                                    <div class="bg-primary h-10 w-10 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M9 18V5l12-2v13"></path>
                                            <circle cx="6" cy="18" r="3"></circle>
                                            <circle cx="18" cy="16" r="3"></circle>
                                        </svg>
                                    </div>
                                    <h3 class="font-semibold text-lg">音楽</h3>
                                </div>
                                <p class="text-sm text-gray-600 ml-0 sm:ml-13">
                                好きなアーティストのライブ映像や、楽器の演奏レッスン動画をスナップ。コード進行やテクニックを記録し、練習に役立てることができます。好きな音楽の瞬間を保存して、何度でも楽しみましょう。
                                </p>
                            </div>
                        </div>
                    </div>
                </section>
                <section class="py-16">
                    <div class="container mx-auto px-6 text-center">
                    <h2 class="text-3xl font-bold text-dark mb-4">さあ、今すぐ始めましょう</h2>
                    <p class="text-textBody text-lg mb-8 max-w-2xl mx-auto">SnapScribeを使って、あなたの動画体験を向上させませんか。</p>
                    <div class="flex flex-col sm:flex-row justify-center gap-4">
                        <Link
                            :href="route('register')"
                        >
                            <span class="bg-primary hover:bg-opacity-90 text-white font-medium px-8 py-3 rounded-md inline-block shadow-md transition">無料で試す</span>
                        </Link>
                    </div>
                    </div>
                </section>

                <!-- Footer -->
                <footer class="bg-dark text-white py-12">
                    <div class="container mx-auto px-6">
                    <div class="grid md:grid-cols-4 gap-8 mb-8">
                        <div>
                        <h3 class="text-xl font-bold font-montserrat text-primary mb-4">SnapScribe</h3>
                        <p class="text-gray-400">YouTube動画での学びを即座に記録。大事なポイントを逃さず、あなたの学びをスムーズにノート化するためのツールです。</p>
                        </div>

                    </div>
                    <div class="border-t border-gray-700 pt-8 flex flex-col md:flex-row justify-between items-center">
                        <div class="text-gray-400 mb-4 md:mb-0">© 2025 SnapScribe. All rights reserved.</div>
                        </div>
                    </div>
                </footer>
            </div>
        </WelcomeLayout>
    </div>
</template>


