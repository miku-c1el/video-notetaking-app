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
    <div>
        <Head title="Register" />
        <GuestLayout>
            <form @submit.prevent="submit" class="space-y-6 mt-8">
                <div class="space-y-4">
                    <div>
                        <InputLabel for="username" value="ユーザーネーム" class="text-sm font-medium text-[#2d2c38]" />
                        <TextInput
                            id="username"
                            type="text"
                            v-model="form.username"
                            required
                            autofocus
                            autocomplete="name"
                            class="mt-1 block w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-primary focus:border-primary"
                        />
                        <InputError class="mt-2" :message="form.errors.username" />
                    </div>

                    <div>
                        <InputLabel for="email" value="メールアドレス" class="text-sm font-medium text-[#2d2c38]" :required="true" />
                        <TextInput
                            id="email"
                            type="email"
                            v-model="form.email"
                            required
                            autocomplete="username"
                            class="mt-1 block w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-primary focus:border-primary"
                        />
                        <InputError class="mt-2" :message="form.errors.email" />
                    </div>

                    <div>
                        <InputLabel for="password" value="パスワード" class="text-sm font-medium text-primary-dark" :required="true" />
                        <TextInput
                            id="password"
                            type="password"
                            v-model="form.password"
                            required
                            autocomplete="new-password"
                            class="mt-1 block w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-primary focus:border-primary"
                        />
                        <InputError class="mt-2" :message="form.errors.password" />
                    </div>

                    <div>
                        <InputLabel for="password_confirmation" value="パスワード(確認用)" class="text-sm font-medium text-primary-dark" :required="true" />
                        <TextInput
                            id="password_confirmation"
                            type="password"
                            v-model="form.password_confirmation"
                            required
                            autocomplete="new-password"
                            class="mt-1 block w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-primary focus:border-primary"
                        />
                        <InputError class="mt-2" :message="form.errors.password_confirmation" />
                    </div>
                    <!-- パスワード条件の注意書き -->
                    <div class="password-conditions mt-2">
                        <p class="text-gray-500 text-xs">パスワードは以下の条件を満たすように設定してください</p>
                        <ul class="list-disc ml-5 text-gray-500 text-xs mt-1">
                            <li>最低8文字以上であること</li>
                            <li>数字、大文字、小文字、特殊文字（例: @, $, !, %, *, ?, &）をそれぞれ1つ以上含むこと</li>
                        </ul>
                    </div>
                </div>

                <PrimaryButton
                    class="w-full py-3 px-4 border border-transparent rounded-lg text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-0 focus:border-gray-200 focus:shadow-none transition-colors flex justify-center items-center"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    登録する
                </PrimaryButton>

                <div class="text-center mt-4">
                    <Link :href="route('login')" class="text-primary hover:text-primary-dark">
                        アカウントをお持ちの方はこちら
                    </Link>
                </div>
            </form>
        </GuestLayout>
    </div>
</template>

<style scoped>
.password-conditions p, .password-conditions li {
  color: #6b7280;
  font-size: 12px;
}
</style>