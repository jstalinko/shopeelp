<script setup lang="ts">
import { ref } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Switch } from '@/components/ui/switch';
import InputError from '@/components/InputError.vue';
import {
    Dialog,
    DialogContent,
    DialogHeader,
    DialogTitle,
    DialogDescription,
    DialogFooter,
} from '@/components/ui/dialog';
import {
    Pencil,
    Layout,
    User,
    Calendar,
    Settings,
    Layers,
    Sliders,
    CheckCircle2,
    XCircle,
    SlidersHorizontal,
} from 'lucide-vue-next';

interface ConfigField {
    name: string;
    type: string;
    required: boolean;
}

interface TemplateItem {
    id: number;
    name: string;
    description: string | null;
    image: string | null;
    template_path: string;
    active: boolean;
    db_config: Record<string, string>;
    config_fields: ConfigField[];
    author: string;
    version: string;
    created: string;
    updated: string;
}

const props = defineProps<{
    templates: TemplateItem[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Templates', href: '/dashboard/templates' },
];

const selectedTemplate = ref<TemplateItem | null>(null);
const isModalOpen = ref(false);

const form = useForm({
    active: true,
    config: {} as Record<string, string>,
});

const openEditModal = (template: TemplateItem) => {
    selectedTemplate.value = template;
    form.clearErrors();
    form.active = template.active;
    
    // Initialize config dynamically
    const initialConfig: Record<string, string> = {};
    template.config_fields.forEach((field) => {
        initialConfig[field.name] = template.db_config[field.name] || '';
    });
    form.config = initialConfig;
    
    isModalOpen.value = true;
};

const submitForm = () => {
    if (!selectedTemplate.value) return;
    
    form.put(route('dashboard.templates.update-config', selectedTemplate.value.id), {
        onSuccess: () => {
            isModalOpen.value = false;
        }
    });
};

const formatFieldName = (name: string) => {
    return name
        .replace(/_/g, ' ')
        .replace(/\b\w/g, (c) => c.toUpperCase());
};
</script>

<template>
    <Head title="Templates" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <!-- Header Section -->
            <div class="flex flex-col gap-2">
                <h1 class="text-3xl font-bold tracking-tight">Campaign Templates</h1>
                <p class="text-muted-foreground">Manage templates discovered from <code class="bg-muted px-1 py-0.5 rounded text-xs">resources/views/templates/</code> and configure variables.</p>
            </div>

            <!-- Templates Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div
                    v-for="template in props.templates"
                    :key="template.id"
                    class="bg-card text-card-foreground rounded-xl border shadow-sm flex flex-col justify-between overflow-hidden transition-all duration-200 hover:shadow-md hover:translate-y-[-2px]"
                >
                    <!-- Card Top Header Visual -->
                    <div class="bg-muted/40 h-32 flex items-center justify-center border-b relative">
                        <div class="bg-primary/5 text-primary p-4 rounded-full border border-primary/10">
                            <Layout class="h-8 w-8" />
                        </div>
                        <span
                            class="absolute top-3 right-3 text-[10px] font-bold px-2 py-0.5 rounded-full border"
                            :class="template.active
                                ? 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/30 dark:text-emerald-400 dark:border-emerald-800'
                                : 'bg-rose-50 text-rose-700 border-rose-200 dark:bg-rose-950/30 dark:text-rose-400 dark:border-rose-800'"
                        >
                            {{ template.active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>

                    <!-- Card Body -->
                    <div class="p-6 flex-1 flex flex-col gap-4">
                        <div class="space-y-1">
                            <h2 class="text-lg font-bold truncate" :title="template.name">{{ template.name }}</h2>
                            <div class="flex items-center gap-3 text-xs text-muted-foreground flex-wrap">
                                <span class="flex items-center gap-1">
                                    <User class="h-3 w-3" />
                                    {{ template.author }}
                                </span>
                                <span>v{{ template.version }}</span>
                            </div>
                        </div>

                        <p class="text-xs text-muted-foreground/80 line-clamp-3 leading-relaxed">
                            {{ template.description }}
                        </p>

                        <!-- Path and Configuration fields tags -->
                        <div class="space-y-2 mt-auto">
                            <div class="text-[10px] text-muted-foreground font-mono bg-muted/60 p-1.5 rounded border border-border/40 truncate" :title="template.template_path">
                                Path: {{ template.template_path }}
                            </div>
                            
                            <div class="space-y-1">
                                <span class="text-[10px] font-semibold text-muted-foreground block uppercase tracking-wider">Required Variables:</span>
                                <div class="flex flex-wrap gap-1.5">
                                    <span
                                        v-for="field in template.config_fields"
                                        :key="field.name"
                                        class="text-[10px] bg-secondary/80 text-secondary-foreground border border-border px-1.5 py-0.5 rounded font-mono"
                                    >
                                        ${{ field.name }}
                                    </span>
                                    <span v-if="template.config_fields.length === 0" class="text-[10px] text-muted-foreground italic">
                                        No variables required
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card Footer Actions -->
                    <div class="p-6 border-t bg-muted/20 flex gap-2">
                        <Button
                            variant="outline"
                            class="w-full text-xs font-semibold"
                            @click="openEditModal(template)"
                        >
                            <Settings class="mr-1.5 h-3.5 w-3.5" />
                            Configure Variables
                        </Button>
                    </div>
                </div>
            </div>

            <!-- Dynamic Variables Configuration Modal -->
            <Dialog v-model:open="isModalOpen">
                <DialogContent class="sm:max-w-lg max-h-[90vh] overflow-y-auto">
                    <DialogHeader>
                        <DialogTitle class="text-2xl font-bold flex items-center gap-2">
                            <Sliders class="h-5 w-5 text-primary" />
                            Configure Layout
                        </DialogTitle>
                        <DialogDescription>
                            Configure variables for the <code class="bg-muted px-1.5 py-0.5 rounded text-xs font-mono font-bold text-foreground">{{ selectedTemplate?.name }}</code> template.
                        </DialogDescription>
                    </DialogHeader>

                    <form @submit.prevent="submitForm" class="space-y-6 py-4" v-if="selectedTemplate">
                        <!-- Dynamic Variable inputs -->
                        <div class="space-y-4">
                            <div v-for="field in selectedTemplate.config_fields" :key="field.name" class="space-y-2">
                                <Label :for="field.name" class="text-sm font-semibold flex items-center gap-1">
                                    {{ formatFieldName(field.name) }}
                                    <span v-if="field.required" class="text-red-500" title="Required">*</span>
                                </Label>
                                <textarea
                                    v-if="field.name.includes('description') || field.name.includes('content') || field.name.includes('urls')"
                                    :id="field.name"
                                    rows="3"
                                    v-model="form.config[field.name]"
                                    :required="field.required"
                                    class="flex w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                    :placeholder="'Enter ' + formatFieldName(field.name)"
                                ></textarea>
                                <Input
                                    v-else
                                    :id="field.name"
                                    type="text"
                                    v-model="form.config[field.name]"
                                    :required="field.required"
                                    :placeholder="'Enter ' + formatFieldName(field.name)"
                                />
                                <span class="text-[10px] text-muted-foreground block">
                                    Variable code: <code class="font-mono text-primary font-bold">${{ field.name }}</code> (Type: {{ field.type }})
                                </span>
                            </div>
                            
                            <div v-if="selectedTemplate.config_fields.length === 0" class="p-4 bg-muted rounded-lg text-center text-xs text-muted-foreground">
                                This template has no configurable variables in its file header.
                            </div>
                        </div>

                        <!-- Active status switcher -->
                        <div class="flex items-center justify-between border-t pt-4">
                            <div class="space-y-0.5">
                                <Label for="template-active" class="text-sm font-semibold">Template Status</Label>
                                <p class="text-xs text-muted-foreground">Inactive templates cannot be selected for campaign redirect routing.</p>
                            </div>
                            <Switch
                                id="template-active"
                                :checked="form.active"
                                @update:checked="(val) => form.active = val"
                            />
                        </div>

                        <!-- Footer -->
                        <DialogFooter class="flex items-center gap-2 justify-end pt-4 border-t">
                            <Button type="button" variant="outline" @click="isModalOpen = false">
                                Cancel
                            </Button>
                            <Button type="submit" :disabled="form.processing">
                                {{ form.processing ? 'Saving...' : 'Save Configuration' }}
                            </Button>
                        </DialogFooter>
                    </form>
                </DialogContent>
            </Dialog>
        </div>
    </AppLayout>
</template>
