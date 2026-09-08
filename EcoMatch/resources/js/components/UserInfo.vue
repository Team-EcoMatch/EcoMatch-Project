<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const user = page.props.auth.user;

const nombreEmpresa = computed(() => page.props.auth.user?.nombreEmpresa || 'Mi Empresa');
</script>

<template>
    <div class="flex items-center gap-2 overflow-hidden">
        <img v-if="user?.profile_photo_url" :src="user.profile_photo_url as any" :alt="user?.name"
            class="rounded-full size-8 object-cover">
        <div v-else
            class="flex items-center justify-center size-8 rounded-full bg-primary/10 text-primary font-semibold">
            {{ user?.name?.charAt(0).toUpperCase() }}
        </div>
        <div class="grid flex-1 text-left text-sm leading-tight">
            <span class="truncate font-semibold">{{ user?.name }}</span>
            <span class="truncate text-xs text-muted-foreground">{{ nombreEmpresa }}</span>
        </div>
    </div>
</template>