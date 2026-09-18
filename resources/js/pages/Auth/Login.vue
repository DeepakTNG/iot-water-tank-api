<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import { privacyPolicy, termsOfService } from '@/routes';
import { redirect as googleRedirect } from '@/routes/auth/google';

const page = usePage<{ errors: Record<string, string> }>();
</script>

<template>
    <Head title="Sign in" />

    <main class="login-shell">
        <section class="login-panel">
            <div class="brand-mark">WT</div>
            <p class="eyebrow">Water tank operations</p>
            <h1>Sign in to your control room.</h1>
            <p class="lede">Manage connected devices and keep a clear eye on the latest readings.</p>

            <div class="login-form">
                <a :href="googleRedirect.url()" class="google-button">
                    <span class="google-mark">G</span>
                    Continue with Google
                </a>
                <small v-if="page.props.errors.email">{{ page.props.errors.email }}</small>
                <p class="login-note">Only approved Google accounts can access this control room.</p>
                <div class="login-legal">
                    <Link :href="privacyPolicy.url()">Privacy Policy</Link>
                    <span>&bull;</span>
                    <Link :href="termsOfService.url()">Terms of Service</Link>
                </div>
            </div>
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
