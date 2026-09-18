<script setup lang="ts">
import { Head, Form, Link, usePage } from '@inertiajs/vue3';
import { destroy as logout } from '@/actions/App/Http/Controllers/Auth/AuthenticatedSessionController';
import { destroy, index, store } from '@/routes/devices';
import { type DevicePageProps } from '@/types/devices';

const page = usePage<DevicePageProps>();
const props = defineProps<DevicePageProps>();
const createForm = store.form();

function copyToken(token: string): void {
    void navigator.clipboard.writeText(token);
}

function formatDate(value: string | null): string {
    if (!value) return 'No readings yet';
    return new Intl.DateTimeFormat(undefined, { dateStyle: 'medium', timeStyle: 'short' }).format(new Date(value));
}

function payloadPreview(payload: Record<string, unknown>): string {
    return Object.entries(payload).map(([key, value]) => `${key}: ${String(value)}`).join('  /  ');
}
</script>

<template>
    <Head title="Devices" />

    <main class="dashboard-shell">
        <header class="topbar">
            <Link :href="index.url()" class="wordmark"><span>WT</span> Waterline</Link>
            <div class="operator-menu">
                <span>{{ page.props.auth.user.name }}</span>
                <Form v-bind="logout.form()">
                    <button type="submit" class="text-button">Sign out</button>
                </Form>
            </div>
        </header>

        <section class="dashboard-content">
            <div class="heading-row">
                <div>
                    <p class="eyebrow">Operations / devices</p>
                    <h1>Connected devices</h1>
                    <p class="lede">Register hardware, provision its private token, and watch the latest signal.</p>
                </div>
                <div class="device-count"><strong>{{ props.devices.length }}</strong><span>registered</span></div>
            </div>

            <div v-if="props.deviceToken" class="token-reveal" role="alert">
                <div>
                    <p class="eyebrow">Token created once</p>
                    <h2>Copy this token to the device now.</h2>
                    <p>It will not be shown again. Treat it like a password.</p>
                </div>
                <div class="token-row">
                    <code>{{ props.deviceToken }}</code>
                    <button type="button" @click="copyToken(props.deviceToken)">Copy token</button>
                </div>
            </div>

            <section class="workspace-grid">
                <div class="device-list">
                    <div v-if="props.devices.length === 0" class="empty-state">
                        <span class="empty-number">01</span>
                        <h2>Your network starts here.</h2>
                        <p>Register the first device and its private write token will be ready for installation.</p>
                    </div>

                    <article v-for="device in props.devices" :key="device.id" class="device-card">
                        <div class="device-card-top">
                            <div class="device-icon">{{ device.name.slice(0, 1).toUpperCase() }}</div>
                            <div>
                                <h2>{{ device.name }}</h2>
                                <span :class="['status', device.is_active ? 'status-live' : 'status-off']">
                                    <i /> {{ device.is_active ? 'Active' : 'Revoked' }}
                                </span>
                            </div>
                            <Form :action="destroy.url(device.id)" method="delete" class="revoke-form">
                                <button v-if="device.is_active" type="submit" class="text-button danger">Revoke</button>
                            </Form>
                        </div>
                        <div class="reading-line">
                            <span>Latest signal</span>
                            <time>{{ formatDate(device.latest_message?.received_at ?? null) }}</time>
                        </div>
                        <p v-if="device.latest_message" class="reading-payload">
                            {{ payloadPreview(device.latest_message.payload) }}
                        </p>
                        <p v-else class="reading-payload muted">Waiting for the first reading</p>
                    </article>
                </div>

                <aside class="setup-panel">
                    <p class="eyebrow">Provision hardware</p>
                    <h2>Add a device</h2>
                    <p class="panel-copy">Give your sensor a stable name. The private token will be generated after submission.</p>
                    <Form v-bind="createForm" class="device-form" reset-on-success v-slot="{ errors, processing }">
                        <label>
                            <span>Device name</span>
                            <input name="name" type="text" placeholder="tank-1" maxlength="100" required />
                            <small v-if="errors.name">{{ errors.name }}</small>
                        </label>
                        <button type="submit" :disabled="processing">
                            {{ processing ? 'Provisioning...' : 'Create private token' }}
                        </button>
                    </Form>
                    <div class="security-note"><strong>Private by default.</strong> Tokens are shown only once and are never recoverable from this dashboard.</div>
                </aside>
            </section>
        </section>
    </main>
</template>
