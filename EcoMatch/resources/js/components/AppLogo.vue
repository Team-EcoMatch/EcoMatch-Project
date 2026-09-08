<script setup lang="ts">
import { Leaf } from 'lucide-vue-next';
</script>

<template>
    <div class="flex aspect-square size-8 items-center justify-center rounded-md bg-primary/10 text-primary">
        <Leaf class="size-5" />
    </div>
    <div class="grid flex-1 text-left text-sm leading-tight">
        <span class="truncate font-semibold">EcoMatch</span>
        <span class="truncate text-xs text-muted-foreground">Economía Circular</span>
    </div>
</template>