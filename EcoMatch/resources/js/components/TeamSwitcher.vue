<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { Building2 } from 'lucide-vue-next';
import { SidebarMenu, SidebarMenuItem, SidebarMenuButton } from '@/components/ui/sidebar';

const page = usePage();

const nombreEmpresa = computed(() => page.props.auth.user?.nombreEmpresa || 'Mi Empresa');
</script>

<template>
    <SidebarMenu>
        <SidebarMenuItem>
            <SidebarMenuButton
                size="lg"
                class="data-[state=open]:bg-sidebar-accent data-[state=open]:text-sidebar-accent-foreground"
            >
                <div class="flex aspect-square size-8 items-center justify-center rounded-lg bg-primary/10 text-primary">
                    <Building2 class="size-4" />
                </div>
                <div class="grid flex-1 text-left text-sm leading-tight">
                    <span class="truncate font-semibold">{{ nombreEmpresa }}</span>
                    <span class="truncate text-xs">Empresa Activa</span>
                </div>
            </SidebarMenuButton>
        </SidebarMenuItem>
    </SidebarMenu>
</template>