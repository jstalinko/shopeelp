<script setup lang="ts">
import { ref, computed, watch } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Switch } from '@/components/ui/switch';
import InputError from '@/components/InputError.vue';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import {
    Dialog,
    DialogContent,
    DialogHeader,
    DialogTitle,
    DialogDescription,
    DialogFooter,
} from '@/components/ui/dialog';
import {
    Plus,
    Pencil,
    Trash2,
    BarChart3,
    Globe,
    Laptop,
    Chrome,
    Link2,
    CheckCircle2,
    AlertCircle,
    Eye,
    Check,
    Copy,
} from 'lucide-vue-next';

// Define Type Interfaces
interface LinkItem {
    id: number;
    campaign_name: string;
    slug: string;
    target_urls: string;
    campaign_method: 'redirect' | 'landingpage';
    template: string;
    lock_country: string | null;
    lock_device: string | null;
    lock_browser: string | null;
    clicks: number;
    active: boolean;
    created_at: string;
}

interface TemplateItem {
    id: number;
    name: string;
    description: string | null;
    image: string | null;
    active: boolean;
}

const props = defineProps<{
    links: LinkItem[];
    templates: TemplateItem[];
    countries: Record<string, string>;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Links', href: '/dashboard/links' },
];

// Stats computed
const totalLinks = computed(() => props.links.length);
const activeLinks = computed(() => props.links.filter(l => l.active).length);
const totalClicks = computed(() => props.links.reduce((acc, l) => acc + l.clicks, 0));

// Modal states
const isModalOpen = ref(false);
const modalMode = ref<'create' | 'edit'>('create');
const currentEditId = ref<number | null>(null);

// Selection arrays for lock configs
const selectedDevices = ref<string[]>([]);
const selectedBrowsers = ref<string[]>([]);
const selectedCountries = ref<string[]>([]);
const countrySearchQuery = ref('');

// Form Setup
const form = useForm({
    campaign_name: '',
    slug: '',
    target_urls: '',
    campaign_method: 'redirect' as 'redirect' | 'landingpage',
    template: 'blank-page',
    lock_country: '',
    lock_device: '',
    lock_browser: '',
    active: true,
});

// Sync selection lists to comma-separated lock strings
watch(selectedDevices, (newVal) => {
    form.lock_device = newVal.join(',');
});
watch(selectedBrowsers, (newVal) => {
    form.lock_browser = newVal.join(',');
});
watch(selectedCountries, (newVal) => {
    form.lock_country = newVal.join(',');
});

// Auto-slugify campaign name when in create mode
watch(() => form.campaign_name, (newName) => {
    if (modalMode.value === 'create') {
        form.slug = newName
            .toLowerCase()
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/(^-|-$)+/g, '');
    }
});

// Watch method to toggle template default values
watch(() => form.campaign_method, (newMethod) => {
    if (newMethod === 'redirect') {
        form.template = 'blank-page';
    } else {
        const firstDbTemplate = props.templates[0]?.template_path.replace('templates/', '') || 'blank-page';
        form.template = firstDbTemplate;
    }
});

// Filtered country list based on search query
const filteredCountries = computed(() => {
    const query = countrySearchQuery.value.toLowerCase().trim();
    if (!query) return props.countries;
    
    return Object.entries(props.countries).reduce((acc, [code, name]) => {
        if (code.toLowerCase().includes(query) || name.toLowerCase().includes(query)) {
            acc[code] = name;
        }
        return acc;
    }, {} as Record<string, string>);
});

// Country helpers
const selectAllCountries = () => {
    selectedCountries.value = Object.keys(props.countries);
};

const clearAllCountries = () => {
    selectedCountries.value = [];
};

// Campaign name randomizer suffix helper
const randomizeCampaignName = () => {
    const randomSuffix = Math.random().toString(36).substring(2, 8).toUpperCase();
    form.campaign_name = `Campaign_${randomSuffix}`;
};

// Slug randomizer helper
const randomizeSlug = () => {
    const randomSuffix = Math.random().toString(36).substring(2, 8).toLowerCase();
    form.slug = `link-${randomSuffix}`;
};

// Modal control
const openCreateModal = () => {
    modalMode.value = 'create';
    currentEditId.value = null;
    form.reset();
    form.clearErrors();
    selectedDevices.value = [];
    selectedBrowsers.value = [];
    selectedCountries.value = [];
    countrySearchQuery.value = '';
    isModalOpen.value = true;
};

const openEditModal = (link: LinkItem) => {
    modalMode.value = 'edit';
    currentEditId.value = link.id;
    form.clearErrors();
    
    form.campaign_name = link.campaign_name;
    form.slug = link.slug || '';
    form.target_urls = link.target_urls;
    form.campaign_method = link.campaign_method;
    form.template = link.template;
    form.lock_country = link.lock_country || '';
    form.lock_device = link.lock_device || '';
    form.lock_browser = link.lock_browser || '';
    form.active = link.active === 1 || link.active === true;

    selectedDevices.value = link.lock_device ? link.lock_device.split(',').filter(Boolean) : [];
    selectedBrowsers.value = link.lock_browser ? link.lock_browser.split(',').filter(Boolean) : [];
    selectedCountries.value = link.lock_country ? link.lock_country.split(',').filter(Boolean) : [];
    countrySearchQuery.value = '';
    
    isModalOpen.value = true;
};

// Form submit action
const submitForm = () => {
    if (modalMode.value === 'create') {
        form.post(route('dashboard.links.store'), {
            onSuccess: () => {
                isModalOpen.value = false;
                form.reset();
            },
        });
    } else if (modalMode.value === 'edit' && currentEditId.value !== null) {
        form.put(route('dashboard.links.update', currentEditId.value), {
            onSuccess: () => {
                isModalOpen.value = false;
                form.reset();
            },
        });
    }
};

// Interactive actions
const toggleActiveState = (link: LinkItem) => {
    router.patch(route('dashboard.links.toggle-active', link.id), {
        active: !link.active
    }, {
        preserveScroll: true,
    });
};

const deleteLink = (link: LinkItem) => {
    if (confirm(`Are you sure you want to delete this link campaign: "${link.campaign_name}"?`)) {
        router.delete(route('dashboard.links.destroy', link.id), {
            preserveScroll: true,
        });
    }
};

const viewStats = (link: LinkItem) => {
    router.visit(route('dashboard.stats', { link_id: link.id }));
};

const copiedId = ref<number | null>(null);

const copyToClipboard = (linkId: number, slug: string) => {
    const url = `${window.location.origin}/${slug}`;
    navigator.clipboard.writeText(url).then(() => {
        copiedId.value = linkId;
        setTimeout(() => {
            if (copiedId.value === linkId) {
                copiedId.value = null;
            }
        }, 2000);
    });
};

// Formatting helpers
const formatBrowserNames = (val: string | null) => {
    if (!val) return '';
    return val.split(',')
        .map(b => b === 'fbbrowser' ? 'FB Browser' : b.charAt(0).toUpperCase() + b.slice(1))
        .join(', ');
};

const getTemplateName = (path: string) => {
    if (!path) return 'None';
    const filename = path.replace('templates/', '');
    const found = props.templates.find(t => t.template_path.replace('templates/', '') === filename);
    return found ? found.name : filename;
};
</script>

<template>
    <Head title="Link Management" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <!-- Header Section -->
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight">Campaign Links</h1>
                    <p class="text-muted-foreground mt-1">Create, manage, monitor and reactive redirect target campaigns.</p>
                </div>
                <Button @click="openCreateModal" class="shadow-sm">
                    <Plus class="mr-2 h-4 w-4" />
                    Create new link
                </Button>
            </div>

            <!-- Stats Grid -->
            <div class="grid gap-4 md:grid-cols-3">
                <div class="bg-card text-card-foreground rounded-xl border p-6 shadow-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-muted-foreground">Total Campaigns</span>
                        <Link2 class="text-muted-foreground h-4 w-4" />
                    </div>
                    <div class="mt-2 text-3xl font-bold">{{ totalLinks }}</div>
                </div>
                <div class="bg-card text-card-foreground rounded-xl border p-6 shadow-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-muted-foreground">Active Links</span>
                        <CheckCircle2 class="text-emerald-500 h-4 w-4" />
                    </div>
                    <div class="mt-2 text-3xl font-bold text-emerald-600 dark:text-emerald-400">{{ activeLinks }}</div>
                </div>
                <div class="bg-card text-card-foreground rounded-xl border p-6 shadow-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-muted-foreground">Aggregated Clicks</span>
                        <BarChart3 class="text-muted-foreground h-4 w-4" />
                    </div>
                    <div class="mt-2 text-3xl font-bold">{{ totalClicks }}</div>
                </div>
            </div>

            <!-- Table Container -->
            <div class="bg-card text-card-foreground rounded-xl border shadow-sm">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead class="w-[280px]">Campaign Name & Slug</TableHead>
                            <TableHead class="w-[120px]">Method</TableHead>
                            <TableHead class="w-[185px]">Template</TableHead>
                            <TableHead>Lock Configurations</TableHead>
                            <TableHead class="w-[100px] text-right">Clicks</TableHead>
                            <TableHead class="w-[100px] text-center">Status</TableHead>
                            <TableHead class="w-[150px] text-right">Actions</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="link in props.links" :key="link.id" class="hover:bg-muted/50 transition-colors">
                            <TableCell class="font-medium">
                                <div class="flex flex-col gap-1">
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        <span class="text-foreground font-semibold text-sm">{{ link.campaign_name }}</span>
                                        <a
                                            :href="'/' + link.slug"
                                            target="_blank"
                                            class="text-[10px] bg-muted px-1.5 py-0.5 rounded font-mono text-muted-foreground border hover:bg-accent hover:text-accent-foreground transition-colors"
                                            title="Open Campaign URL"
                                        >
                                            /{{ link.slug }}
                                        </a>
                                        <button
                                            type="button"
                                            @click="copyToClipboard(link.id, link.slug)"
                                            class="text-muted-foreground hover:text-foreground transition-colors p-0.5"
                                            title="Copy Campaign URL"
                                        >
                                            <Check v-if="copiedId === link.id" class="h-3 w-3 text-emerald-500 animate-in fade-in" />
                                            <Copy v-else class="h-3 w-3" />
                                        </button>
                                    </div>
                                    <span class="text-xs text-muted-foreground line-clamp-1 truncate max-w-[260px]" :title="link.target_urls">
                                        {{ link.target_urls }}
                                    </span>
                                </div>
                            </TableCell>
                            <TableCell>
                                <span
                                    class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold shadow-sm"
                                    :class="link.campaign_method === 'redirect'
                                        ? 'bg-blue-50 text-blue-700 border border-blue-200 dark:bg-blue-900/30 dark:text-blue-300 dark:border-blue-800'
                                        : 'bg-emerald-50 text-emerald-700 border border-emerald-200 dark:bg-emerald-900/30 dark:text-emerald-300 dark:border-emerald-800'"
                                >
                                    {{ link.campaign_method }}
                                </span>
                            </TableCell>
                            <TableCell>
                                <span class="text-xs bg-muted px-2 py-1 rounded text-muted-foreground border border-border/50">
                                    {{ getTemplateName(link.template) }}
                                </span>
                            </TableCell>
                            <TableCell>
                                <div class="flex flex-wrap gap-2 items-center text-xs">
                                    <span v-if="!link.lock_country && !link.lock_device && !link.lock_browser" class="text-muted-foreground italic">
                                        None
                                    </span>
                                    <span v-if="link.lock_country" class="inline-flex items-center gap-1 bg-amber-50 text-amber-700 border border-amber-200 dark:bg-amber-900/20 dark:text-amber-300 dark:border-amber-800 px-1.5 py-0.5 rounded" :title="link.lock_country">
                                        <Globe class="h-3 w-3" />
                                        {{ link.lock_country }}
                                    </span>
                                    <span v-if="link.lock_device" class="inline-flex items-center gap-1 bg-purple-50 text-purple-700 border border-purple-200 dark:bg-purple-900/20 dark:text-purple-300 dark:border-purple-800 px-1.5 py-0.5 rounded">
                                        <Laptop class="h-3 w-3" />
                                        {{ link.lock_device }}
                                    </span>
                                    <span v-if="link.lock_browser" class="inline-flex items-center gap-1 bg-indigo-50 text-indigo-700 border border-indigo-200 dark:bg-indigo-900/20 dark:text-indigo-300 dark:border-indigo-800 px-1.5 py-0.5 rounded">
                                        <Chrome class="h-3 w-3" />
                                        {{ formatBrowserNames(link.lock_browser) }}
                                    </span>
                                </div>
                            </TableCell>
                            <TableCell class="text-right font-mono font-medium">{{ link.clicks }}</TableCell>
                            <TableCell class="text-center">
                                <div class="flex items-center justify-center">
                                    <Switch
                                        :checked="link.active === 1 || link.active === true"
                                        @update:checked="toggleActiveState(link)"
                                    />
                                </div>
                            </TableCell>
                            <TableCell class="text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <Button size="icon" variant="ghost" @click="openEditModal(link)" title="Edit Campaign">
                                        <Pencil class="h-4 w-4" />
                                    </Button>
                                    <Button size="icon" variant="ghost" @click="viewStats(link)" class="text-blue-500 hover:text-blue-600 dark:text-blue-400" title="Analytics Stats">
                                        <BarChart3 class="h-4 w-4" />
                                    </Button>
                                    <Button size="icon" variant="ghost" @click="deleteLink(link)" class="text-destructive hover:text-destructive/90" title="Delete Campaign">
                                        <Trash2 class="h-4 w-4" />
                                    </Button>
                                </div>
                            </TableCell>
                        </TableRow>
                        <TableRow v-if="props.links.length === 0">
                            <TableCell colspan="7" class="h-32 text-center text-muted-foreground">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <AlertCircle class="h-8 w-8 text-muted-foreground/60" />
                                    <p>No campaign links configured. Click "Create new link" to get started.</p>
                                </div>
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>

            <!-- Create / Edit Modal -->
            <Dialog v-model:open="isModalOpen">
                <DialogContent class="sm:max-w-4xl max-h-[90vh] overflow-y-auto">
                    <DialogHeader>
                        <DialogTitle class="text-2xl font-bold">
                            {{ modalMode === 'create' ? 'Create New Link' : 'Edit Campaign Link' }}
                        </DialogTitle>
                        <DialogDescription>
                            Configure details, methods, and lock conditions for your redirects and campaigns.
                        </DialogDescription>
                    </DialogHeader>

                    <form @submit.prevent="submitForm" class="space-y-6 py-4">
                        <div class="grid gap-6 md:grid-cols-2">
                            <!-- Left Column: Core Fields -->
                            <div class="space-y-4">
                                <div class="space-y-2">
                                    <Label for="campaign_name" class="text-sm font-semibold">Campaign Name</Label>
                                    <div class="flex gap-2">
                                        <Input
                                            id="campaign_name"
                                            type="text"
                                            placeholder="e.g. Shopee Mega Promo July"
                                            v-model="form.campaign_name"
                                            required
                                            class="flex-1"
                                        />
                                        <Button
                                            type="button"
                                            variant="outline"
                                            @click="randomizeCampaignName"
                                            class="px-3"
                                            title="Randomize or Append Suffix"
                                        >
                                            Random
                                        </Button>
                                    </div>
                                    <InputError :message="form.errors.campaign_name" />
                                </div>

                                <div class="space-y-2">
                                    <Label for="slug" class="text-sm font-semibold">Slug (for URL access)</Label>
                                    <div class="flex gap-2">
                                        <Input
                                            id="slug"
                                            type="text"
                                            placeholder="e.g. shopee-promo"
                                            v-model="form.slug"
                                            required
                                            class="flex-1 font-mono"
                                        />
                                        <Button
                                            type="button"
                                            variant="outline"
                                            @click="randomizeSlug"
                                            class="px-3"
                                            title="Randomize Slug"
                                        >
                                            Random
                                        </Button>
                                    </div>
                                    <span class="text-xs text-muted-foreground block">
                                        This slug will be used to access the campaign, e.g. http://domain.com/slug
                                    </span>
                                    <InputError :message="form.errors.slug" />
                                </div>

                                <div class="space-y-2">
                                    <Label for="target_urls" class="text-sm font-semibold">Target URLs</Label>
                                    <textarea
                                        id="target_urls"
                                        rows="5"
                                        placeholder="Paste target destination URLs. Use separate lines for multiple URLs (split clicks random weight)"
                                        class="flex w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                        v-model="form.target_urls"
                                        required
                                    ></textarea>
                                    <span class="text-xs text-muted-foreground block">
                                        For multiple target destinations, enter one URL per line.
                                    </span>
                                    <InputError :message="form.errors.target_urls" />
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div class="space-y-2">
                                        <Label for="campaign_method" class="text-sm font-semibold">Method</Label>
                                        <select
                                            id="campaign_method"
                                            v-model="form.campaign_method"
                                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                        >
                                            <option value="redirect">Direct Redirect</option>
                                            <option value="landingpage">Landing Page</option>
                                        </select>
                                        <InputError :message="form.errors.campaign_method" />
                                    </div>

                                    <div class="space-y-2">
                                        <Label for="template" class="text-sm font-semibold">Template</Label>
                                        <select
                                            id="template"
                                            v-model="form.template"
                                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                        >
                                            <option v-for="t in props.templates" :key="t.id" :value="t.template_path.replace('templates/', '')">
                                                {{ t.name }}
                                            </option>
                                            <option v-if="props.templates.length === 0" value="blank-page">Default Blank Page</option>
                                        </select>
                                        <InputError :message="form.errors.template" />
                                    </div>
                                </div>
                            </div>

                            <!-- Right Column: Restriction / Lock Configs -->
                            <div class="space-y-6 bg-muted/40 p-4 rounded-xl border border-border/60">
                                <h3 class="text-sm font-bold text-foreground border-b pb-2">Filter & Restriction Controls</h3>
                                
                                <!-- Country filter (searchable multiple checkboxes) -->
                                <div class="space-y-2">
                                    <Label class="text-sm font-semibold flex items-center gap-1.5">
                                        <Globe class="h-4 w-4 text-muted-foreground" />
                                        Target Countries
                                    </Label>
                                    <div class="space-y-2 border border-input bg-background p-3 rounded-lg">
                                        <!-- Search input -->
                                        <Input
                                            type="text"
                                            placeholder="Search countries..."
                                            v-model="countrySearchQuery"
                                            class="h-8 text-xs bg-muted/30"
                                        />
                                        
                                        <!-- Checkbox tools -->
                                        <div class="flex gap-2 justify-end">
                                            <button
                                                type="button"
                                                @click="selectAllCountries"
                                                class="text-[10px] text-primary hover:underline font-medium"
                                            >
                                                Select All
                                            </button>
                                            <span class="text-[10px] text-muted-foreground">|</span>
                                            <button
                                                type="button"
                                                @click="clearAllCountries"
                                                class="text-[10px] text-primary hover:underline font-medium"
                                            >
                                                Clear All
                                            </button>
                                        </div>

                                        <!-- Country scroll list -->
                                        <div class="h-32 overflow-y-auto space-y-1 pr-1 border-t pt-2 scrollbar-thin">
                                            <div
                                                v-for="[code, name] in Object.entries(filteredCountries)"
                                                :key="code"
                                                class="flex items-center gap-2 hover:bg-muted/40 p-1 rounded transition-colors"
                                            >
                                                <input
                                                    type="checkbox"
                                                    :id="'country-' + code"
                                                    :value="code"
                                                    v-model="selectedCountries"
                                                    class="rounded border-input text-primary focus:ring-ring size-3.5 cursor-pointer"
                                                />
                                                <label :for="'country-' + code" class="text-xs cursor-pointer select-none flex justify-between w-full">
                                                    <span class="truncate max-w-[200px]" :title="name">{{ name }}</span>
                                                    <span class="text-muted-foreground font-mono text-[10px]">{{ code }}</span>
                                                </label>
                                            </div>
                                            <div v-if="Object.keys(filteredCountries).length === 0" class="text-xs text-muted-foreground text-center py-4">
                                                No countries found.
                                            </div>
                                        </div>

                                        <!-- Selected summary -->
                                        <div class="text-[10px] text-muted-foreground border-t pt-2 flex justify-between items-center">
                                            <span>Selected Countries: <strong>{{ selectedCountries.length }}</strong></span>
                                            <span v-if="selectedCountries.length > 0" class="truncate max-w-[150px] font-mono text-primary font-semibold" :title="selectedCountries.join(', ')">
                                                {{ selectedCountries.join(', ') }}
                                            </span>
                                        </div>
                                    </div>
                                    <InputError :message="form.errors.lock_country" />
                                </div>

                                <!-- Device filter checkboxes -->
                                <div class="space-y-2">
                                    <Label class="text-sm font-semibold flex items-center gap-1.5">
                                        <Laptop class="h-4 w-4 text-muted-foreground" />
                                        Target Devices
                                    </Label>
                                    <div class="flex flex-wrap gap-4 mt-2">
                                        <label class="flex items-center gap-2 text-sm cursor-pointer select-none">
                                            <input
                                                type="checkbox"
                                                value="mobile"
                                                v-model="selectedDevices"
                                                class="rounded border-input text-primary focus:ring-ring"
                                            />
                                            <span>Mobile</span>
                                        </label>
                                        <label class="flex items-center gap-2 text-sm cursor-pointer select-none">
                                            <input
                                                type="checkbox"
                                                value="desktop"
                                                v-model="selectedDevices"
                                                class="rounded border-input text-primary focus:ring-ring"
                                            />
                                            <span>Desktop</span>
                                        </label>
                                        <label class="flex items-center gap-2 text-sm cursor-pointer select-none">
                                            <input
                                                type="checkbox"
                                                value="tablet"
                                                v-model="selectedDevices"
                                                class="rounded border-input text-primary focus:ring-ring"
                                            />
                                            <span>Tablet</span>
                                        </label>
                                    </div>
                                    <span class="text-xs text-muted-foreground block">
                                        Checked devices are allowed. If none are selected, all devices are permitted.
                                    </span>
                                    <InputError :message="form.errors.lock_device" />
                                </div>

                                <!-- Browser filter checkboxes -->
                                <div class="space-y-2">
                                    <Label class="text-sm font-semibold flex items-center gap-1.5">
                                        <Chrome class="h-4 w-4 text-muted-foreground" />
                                        Target Browsers
                                    </Label>
                                    <div class="grid grid-cols-3 gap-2 mt-2">
                                        <label class="flex items-center gap-2 text-xs cursor-pointer select-none">
                                            <input
                                                type="checkbox"
                                                value="chrome"
                                                v-model="selectedBrowsers"
                                                class="rounded border-input text-primary focus:ring-ring"
                                            />
                                            <span>Chrome</span>
                                        </label>
                                        <label class="flex items-center gap-2 text-xs cursor-pointer select-none">
                                            <input
                                                type="checkbox"
                                                value="firefox"
                                                v-model="selectedBrowsers"
                                                class="rounded border-input text-primary focus:ring-ring"
                                            />
                                            <span>Firefox</span>
                                        </label>
                                        <label class="flex items-center gap-2 text-xs cursor-pointer select-none">
                                            <input
                                                type="checkbox"
                                                value="safari"
                                                v-model="selectedBrowsers"
                                                class="rounded border-input text-primary focus:ring-ring"
                                            />
                                            <span>Safari</span>
                                        </label>
                                        <label class="flex items-center gap-2 text-xs cursor-pointer select-none">
                                            <input
                                                type="checkbox"
                                                value="opera"
                                                v-model="selectedBrowsers"
                                                class="rounded border-input text-primary focus:ring-ring"
                                            />
                                            <span>Opera</span>
                                        </label>
                                        <label class="flex items-center gap-2 text-xs cursor-pointer select-none">
                                            <input
                                                type="checkbox"
                                                value="edge"
                                                v-model="selectedBrowsers"
                                                class="rounded border-input text-primary focus:ring-ring"
                                            />
                                            <span>Edge</span>
                                        </label>
                                        <label class="flex items-center gap-2 text-xs cursor-pointer select-none">
                                            <input
                                                type="checkbox"
                                                value="fbbrowser"
                                                v-model="selectedBrowsers"
                                                class="rounded border-input text-primary focus:ring-ring"
                                            />
                                            <span>FB Browser</span>
                                        </label>
                                    </div>
                                    <span class="text-xs text-muted-foreground block mt-1">
                                        Leave empty to allow all browsers.
                                    </span>
                                    <InputError :message="form.errors.lock_browser" />
                                </div>

                                <!-- Campaign active switch -->
                                <div class="flex items-center justify-between border-t pt-4">
                                    <div class="space-y-0.5">
                                        <Label for="active" class="text-sm font-semibold">Campaign Status</Label>
                                        <p class="text-xs text-muted-foreground">Define if this campaign is immediately active and routing.</p>
                                    </div>
                                    <Switch
                                        id="active"
                                        :checked="form.active"
                                        @update:checked="(val) => form.active = val"
                                    />
                                    <InputError :message="form.errors.active" />
                                </div>
                            </div>
                        </div>

                        <!-- Footer actions -->
                        <DialogFooter class="flex items-center gap-2 justify-end pt-4 border-t">
                            <Button type="button" variant="outline" @click="isModalOpen = false">
                                Cancel
                            </Button>
                            <Button type="submit" :disabled="form.processing">
                                {{ form.processing ? 'Saving...' : 'Save Campaign' }}
                            </Button>
                        </DialogFooter>
                    </form>
                </DialogContent>
            </Dialog>
        </div>
    </AppLayout>
</template>