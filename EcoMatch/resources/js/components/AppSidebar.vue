<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { BookOpen, FolderGit2, LayoutGrid, PackageSearch, Search, Tags } from '@lucide/vue';
import { computed, ref, watch } from 'vue';
import AppLogo from '@/components/AppLogo.vue';
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import TeamSwitcher from '@/components/TeamSwitcher.vue';
import {
    Sidebar, SidebarContent, SidebarFooter,
    SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem,
} from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import type { NavItem } from '@/types';
import { Boxes, Building, CheckCircle2, Inbox, MapPinned, Users, X } from 'lucide-vue-next';

const page = usePage();

const dashboardUrl = computed(() =>
    page.props.currentTeam ? dashboard(page.props.currentTeam.slug).url : '/',
);

const idEmpresa = computed(() => page.props.auth.user.idempresa);

const mainNavItems = computed<NavItem[]>(() => {
    const items = [
        { title: 'Dashboard',        href: dashboardUrl.value,                    icon: LayoutGrid },
        { title: 'Categorías',       href: '/categorias',                          icon: Tags       },
        { title: 'Publicaciones',    href: '/publicaciones',                       icon: Boxes      },
        { title: 'Solicitudes',      href: '/solicitudes',                         icon: Inbox      },
        { title: 'Mapa Interactivo', href: '/mapa',                                icon: MapPinned  },
    ];

    if (page.props.auth.user.rol === 'Jefe') {
        items.push({ title: 'Perfil de Empresa', href: `/empresas/${idEmpresa.value}/edit`, icon: Building });
        items.push({ title: 'Empleados',         href: '/empleados',                        icon: Users    });
    }

    return items;
});

const footerNavItems: NavItem[] = [
    { title: 'Repository',     href: 'https://github.com/laravel/vue-starter-kit', icon: FolderGit2 },
    { title: 'Documentation',  href: 'https://laravel.com/docs/starter-kits#vue',  icon: BookOpen   },
];

const showNotification = ref(false);
const notificationMessage = ref('');

watch(
    () => page.props.message,
    (msg) => {
        if (msg) {
            notificationMessage.value = msg as string;
            showNotification.value = true;
            setTimeout(() => { showNotification.value = false; }, 3000);
        }
    },
    { immediate: true }
);
</script>

<template>
    <Transition
        enter-active-class="transition-all duration-300 ease-out"
        enter-from-class="opacity-0 translate-y-[-10px] scale-95"
        enter-to-class="opacity-100 translate-y-0 scale-100"
        leave-active-class="transition-all duration-300 ease-in"
        leave-from-class="opacity-100 translate-y-0 scale-100"
        leave-to-class="opacity-0 translate-y-[-10px] scale-95">
        <div v-if="showNotification"
            class="fixed top-6 right-6 z-[9999] w-[360px] rounded-xl border border-border bg-card shadow-2xl overflow-hidden">
            <div class="flex items-start gap-3 p-4">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-green-500/10">
                    <CheckCircle2 class="h-5 w-5 text-green-500" />
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-foreground">Acción completada</p>
                    <p class="mt-1 text-sm text-muted-foreground">{{ notificationMessage }}</p>
                </div>
                <button type="button" @click="showNotification = false"
                    class="text-muted-foreground hover:text-foreground transition-colors">
                    <X class="h-4 w-4" />
                </button>
            </div>
            <div class="h-1 bg-muted">
                <div class="h-full bg-green-500 animate-[toast-progress_3s_linear_forwards]"></div>
            </div>
        </div>
    </Transition>

    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboardUrl">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
            <SidebarMenu>
                <SidebarMenuItem>
                    <TeamSwitcher />
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
</template>