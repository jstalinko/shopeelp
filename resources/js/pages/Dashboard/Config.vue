<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Configuration', href: '/dashboard/config' },
];
</script>

<template>
    <Head title="Configuration" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6">
            <h1 class="text-2xl font-bold">Configuration Settings</h1>
            <p class="text-muted-foreground mt-1">Manage global redirection and blocking configuration parameters here.</p>
        </div>
    </AppLayout>
</template>
