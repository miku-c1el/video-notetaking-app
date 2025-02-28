<template>
    <div class="error-page">
        <h1>404 Not Found</h1>
        <p>ページが見つかりませんでした。</p>
        <a @click.prevent="goBack" href="#">ホームへ戻る</a>
    </div>
</template>

<script>
export default {
    methods: {
        goBack() {
            this.$inertia.visit('/');
        }
    }
};
</script>
