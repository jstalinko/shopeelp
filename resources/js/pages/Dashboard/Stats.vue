<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { computed } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Analytics Stats', href: '/dashboard/stats' },
];

// Read query parameter if present
const urlParams = new URLSearchParams(window.location.search);
const linkId = computed(() => urlParams.get('link_id'));
</script>

<template>
    <Head title="Analytics Stats" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6 space-y-6">
            <div>
                <h1 class="text-2xl font-bold">Analytics & Statistics</h1>
                <p class="text-muted-foreground mt-1">Detailed click analytics and request logging stats.</p>
            </div>
            <div v-if="linkId" class="p-4 bg-muted border rounded-lg">
                Showing statistics for Campaign ID: <strong>{{ linkId }}</strong>
            </div>
            <div v-else class="text-muted-foreground italic">
                Select a campaign from the Links page to view detailed statistics.
            </div>
        </div>
    </AppLayout>
</template>
