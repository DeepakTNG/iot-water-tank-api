<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { home, termsOfService } from '@/routes';
</script>

<template>
    <main class="legal-shell">
        <Head title="Privacy Policy" />

        <header class="topbar">
            <Link :href="home.url()" class="wordmark">
                <span>WT</span> Waterline
            </Link>
            <nav class="operator-menu">
                <Link :href="termsOfService.url()" class="text-button">
                    Terms of Service
                </Link>
                <Link :href="home.url()" class="text-button">
                    Back to app
                </Link>
            </nav>
        </header>

        <div class="legal-content">
            <Link :href="home.url()" class="legal-nav-link">
                &larr; Return to Waterline
            </Link>

            <article class="legal-card">
                <p class="eyebrow">Legal & Compliance</p>
                <h1>Privacy Policy</h1>
                <p class="legal-meta">Last updated: September 18, 2026</p>

                <section class="legal-section">
                    <h2>1. Introduction</h2>
                    <p>
                        Waterline ("we", "our", or "us") provides private IoT water tank monitoring and management software.
                        This Privacy Policy explains how we collect, use, and safeguard information when you use our web application,
                        device APIs, and related services.
                    </p>
                </section>

                <section class="legal-section">
                    <h2>2. Information We Collect</h2>
                    <p>We only collect information necessary to provide hardware telemetry and monitoring services:</p>
                    <ul>
                        <li>
                            <strong>Account Information:</strong> When you sign in using Google OAuth, we receive your email address,
                            display name, Google user ID, and email verification status to authenticate you against our authorized operator list.
                        </li>
                        <li>
                            <strong>Device & Telemetry Data:</strong> We collect sensor readings submitted by your provisioned IoT devices,
                            such as water levels, temperatures, flow metrics, timestamps, and device identifiers.
                        </li>
                        <li>
                            <strong>Access Tokens & Credentials:</strong> Hardware access tokens created for your devices are cryptographically
                            hashed. Raw tokens are displayed to you once upon creation and are never stored in plaintext on our servers.
                        </li>
                    </ul>
                </section>

                <section class="legal-section">
                    <h2>3. How We Use Information</h2>
                    <p>We use the collected information solely for:</p>
                    <ul>
                        <li>Authenticating authorized operators and securing access to the control room.</li>
                        <li>Associating telemetry and sensor data with the correct provisioned hardware.</li>
                        <li>Displaying real-time tank metrics, readings, and operational status on your dashboard.</li>
                        <li>Maintaining system stability, troubleshooting communication errors, and preventing abuse.</li>
                    </ul>
                </section>

                <section class="legal-section">
                    <h2>4. Google API Services Compliance</h2>
                    <p>
                        Our application uses Google OAuth for operator authentication. Our use and transfer of information received from Google APIs
                        to any other app adheres to the
                        <a
                            href="https://developers.google.com/terms/api-services-user-data-policy"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="underline"
                        >Google API Services User Data Policy</a>,
                        including the Limited Use requirements.
                    </p>
                </section>

                <section class="legal-section">
                    <h2>5. Data Retention & Security</h2>
                    <p>
                        We implement industry-standard technical measures to protect your information and device network:
                    </p>
                    <ul>
                        <li>All web and API communication is encrypted using Transport Layer Security (TLS/HTTPS).</li>
                        <li>Access to the control room is restricted to pre-approved administrator accounts.</li>
                        <li>Device tokens can be revoked immediately at any time by the operator through the dashboard.</li>
                    </ul>
                </section>

                <section class="legal-section">
                    <h2>6. Contact Us</h2>
                    <p>
                        If you have questions about this Privacy Policy or your data, please contact the system administrator
                        or submit an inquiry through your organization's support channel.
                    </p>
                </section>
            </article>
        </div>
    </main>
</template>
