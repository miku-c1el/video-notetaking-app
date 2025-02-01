<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

//※フォームに入力されたデータが格納される
const form = useForm({
    username: '',
    email: '',
    password: '',
    password_confirmation: '',
});

//※フォームが送信されるときに実行される関数
const submit = () => {
    form.post(route('register'), {
        //※リクエスト処理の終了時に実行される(リクエストが成功しても失敗しても)
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Register" />

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="username" value="ユーザーネーム" />
                <TextInput
                    id="username"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.username"
                    autofocus
                    autocomplete="name"
                />
            </div>
            <div class="mt-4">
                <InputLabel for="email" value="メールアドレス" :required="true" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
                    autocomplete="username"
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mt-4">
                <InputLabel for="password" value="パスワード" :required="true" />

                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password"
                    required
                    autocomplete="new-password"
                />
                    <!-- パスワード条件の注意書き -->
                <div class="password-conditions mt-2">
                    <p>パスワードは以下の条件を満たすように設定してください</p>
                    <ul>
                        <li>最低8文字以上であること</li>
                        <li>数字、大文字、小文字、特殊文字（例: @, $, !, %, *, ?, &）をそれぞれ1つ以上含むこと</li>
                    </ul>
                </div>

                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="mt-4">
                <InputLabel for="password_confirmation" value="パスワード(確認用)" :required="true"/>

                <TextInput
                    id="password_confirmation"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password_confirmation"
                    required
                    autocomplete="new-password"
                />

                <InputError class="mt-2" :message="form.errors.password_confirmation" />
            </div>

            <div class="flex items-center justify-end mt-4">
                <Link
                    :href="route('login')"
                    class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                    ログインする
                </Link>

                <PrimaryButton class="ms-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    登録する
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>

<style scoped>
.password-conditions p, .password-conditions li {
  color: #6b7280; 
  font-size: 12px;
}

.password-conditions ul {
  list-style-type: disc; 
  padding-left: 20px;
}
</style>