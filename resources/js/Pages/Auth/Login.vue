<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import LoginContainer from '@/Components/LoginContainer.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <div>
        <Head title="Log in" />
        <LoginContainer :status="status">
            <form @submit.prevent="submit" class="space-y-6">
                <div class="space-y-4">
                    <div>
                        <InputLabel for="email" value="メールアドレス" class="text-sm font-medium text-[#2d2c38]" />
                        <TextInput
                            id="email"
                            type="email"
                            v-model="form.email"
                            required
                            autofocus
                            autocomplete="username"
                            class="mt-1 block w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-primary focus:border-primary"
                        />
                        <InputError class="mt-2" :message="form.errors.email" />
                    </div>

                    <div>
                        <InputLabel for="password" value="パスワード" class="text-sm font-medium text-primary-dark" />
                        <TextInput
                            id="password"
                            type="password"
                            v-model="form.password"
                            required
                            autocomplete="current-password"
                            class="mt-1 block w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-primary focus:border-primary"
                        />
                        <InputError class="mt-2" :message="form.errors.password" />
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center">
                        <Checkbox v-model="form.remember" class="rounded border-gray-300 text-primary focus:ring-primary" />
                        <span class="ml-2 text-sm text-gray-600">Remember Me</span>
                    </label>

                    <Link
                        v-if="canResetPassword"
                        :href="route('password.request')"
                        class="text-sm text-primary hover:text-[#2d2c38]"
                    >
                        パスワードを忘れた方
                    </Link>
                </div>

                <PrimaryButton
                    class="w-full py-3 px-4 border border-transparent rounded-lg text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-0 focus:border-gray-200 focus:shadow-none transition-colors flex justify-center items-center"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    ログイン
                </PrimaryButton>

                <div class="text-center mt-4">
                    <Link :href="route('register')" class="text-primary hover:text-primary-dark">
                        新規会員登録の方はこちら
                    </Link>
                </div>
            </form>
        </LoginContainer>
    </div>
</template>
