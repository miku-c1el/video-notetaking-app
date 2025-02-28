<template>
    <div class="error-page">
        <h1>403 Forbidden</h1>
        <p>アクセスが許可されていません。</p>
        <a @click.prevent="goBack" href="#">戻る</a>
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


