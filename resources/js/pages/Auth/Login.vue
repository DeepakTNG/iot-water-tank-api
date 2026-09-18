<script setup lang="ts">
import { Head, Form } from '@inertiajs/vue3';
import { store } from '@/routes/login';

const form = store.form();
</script>

<template>
    <Head title="Sign in" />

    <main class="login-shell">
        <section class="login-panel">
            <div class="brand-mark">WT</div>
            <p class="eyebrow">Water tank operations</p>
            <h1>Sign in to your control room.</h1>
            <p class="lede">Manage connected devices and keep a clear eye on the latest readings.</p>

            <Form v-bind="form" :reset-on-error="['password']" class="login-form" v-slot="{ errors, processing }">
                <label>
                    <span>Email address</span>
                    <input name="email" type="email" autocomplete="email" placeholder="operator@example.com" required />
                    <small v-if="errors.email">{{ errors.email }}</small>
                </label>

                <label>
                    <span>Password</span>
                    <input name="password" type="password" autocomplete="current-password" placeholder="Enter your password" required />
                    <small v-if="errors.password">{{ errors.password }}</small>
                </label>

                <label class="remember-row">
                    <input name="remember" type="checkbox" value="1" />
                    <span>Keep me signed in</span>
                </label>

                <button type="submit" :disabled="processing">
                    {{ processing ? 'Checking credentials...' : 'Enter dashboard' }}
                </button>
            </Form>
        </section>

        <aside class="login-aside">
            <div class="aside-grid" />
            <div class="aside-content">
                <span class="signal-dot" />
                <p>Private device network</p>
                <strong>Every reading has a source.</strong>
                <span>Tokens stay with the hardware. Your dashboard stays with you.</span>
            </div>
        </aside>
    </main>
</template>
