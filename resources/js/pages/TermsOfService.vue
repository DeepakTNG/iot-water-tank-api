<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { home, privacyPolicy } from '@/routes';
</script>

<template>
    <main class="legal-shell">
        <Head title="Terms of Service" />

        <header class="topbar">
            <Link :href="home.url()" class="wordmark">
                <span>NGI</span> Next Gen IOT
            </Link>
            <nav class="operator-menu">
                <Link :href="privacyPolicy.url()" class="text-button">
                    Privacy Policy
                </Link>
                <Link :href="home.url()" class="text-button">
                    Back to app
                </Link>
            </nav>
        </header>

        <div class="legal-content">
            <Link :href="home.url()" class="legal-nav-link">
                &larr; Return to Next Gen IoT
            </Link>

            <article class="legal-card">
                <p class="eyebrow">Legal & Compliance</p>
                <h1>Terms of Service</h1>
                <p class="legal-meta">Last updated: September 18, 2026</p>

                <section class="legal-section">
                    <h2>1. Acceptance of Terms</h2>
                    <p>
                        By accessing or using the `Next Gen IoT` IoT water tank monitoring platform ("Service"), you agree to be bound by these
                        Terms of Service. If you do not agree to these terms, you must not access or use the Service.
                    </p>
                </section>

                <section class="legal-section">
                    <h2>2. Authorized Operators & Authentication</h2>
                    <p>
                        Access to the `Next Gen IoT` dashboard is restricted to designated operators. Authentication is performed via Google OAuth.
                        You agree to:
                    </p>
                    <ul>
                        <li>Use only verified Google accounts explicitly authorized by system administrators.</li>
                        <li>Maintain the confidentiality of your account credentials.</li>
                        <li>Immediately notify system administrators of any unauthorized access or security breach.</li>
                    </ul>
                </section>

                <section class="legal-section">
                    <h2>3. Hardware Provisioning & Token Security</h2>
                    <p>
                        `Next Gen IoT` allows operators to provision hardware devices and issue API access tokens for transmitting sensor data:
                    </p>
                    <ul>
                        <li>Hardware tokens are generated once and must be stored securely within device firmware or environment variables.</li>
                        <li>You are responsible for all sensor readings and API activities conducted using tokens issued to your account.</li>
                        <li>If a device or token is compromised, you must immediately revoke the device from the `Next Gen IoT` dashboard.</li>
                    </ul>
                </section>

                <section class="legal-section">
                    <h2>4. Acceptable Use of Telemetry APIs</h2>
                    <p>
                        IoT devices sending readings to our API endpoints must adhere to rate limits and send data formatted in according
                        with the API specification. You agree not to:
                    </p>
                    <ul>
                        <li>Transmit corrupted, forged, or malicious payloads intended to disrupt the Service.</li>
                        <li>Attempt to access telemetry or hardware belonging to other operators without authorization.</li>
                        <li>Reverse engineer, decompile, or tamper with the underlying platform or infrastructure.</li>
                    </ul>
                </section>

                <section class="legal-section">
                    <h2>5. Disclaimer of Warranties</h2>
                    <p>
                        The Service is provided on an "AS IS" and "AS AVAILABLE" basis. While `Next Gen IoT` is built for reliability,
                        we do not warrant that tank telemetry, network connectivity, or sensor signals will be uninterrupted, error-free,
                        or suitable as a sole safety fail-safe for critical physical systems without proper mechanical backups.
                    </p>
                </section>

                <section class="legal-section">
                    <h2>6. Limitation of Liability</h2>
                    <p>
                        To the maximum extent permitted by applicable law, `Next Gen IoT` and its operators shall not be liable for any indirect,
                        incidental, special, consequential, or punitive damages arising from the use or inability to use the Service or
                        connected IoT hardware.
                    </p>
                </section>

                <section class="legal-section">
                    <h2>7. Modifications & Inquiries</h2>
                    <p>
                        We reserve the right to modify these terms at any time. Continued use of the platform constitutes agreement to the updated terms.
                        For questions regarding these Terms of Service, contact your organization's system administrator.
                    </p>
                </section>
            </article>
        </div>
    </main>
</template>
