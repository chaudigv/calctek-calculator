<script setup lang="ts">
import { cn } from '@/lib/utils';

defineProps<{
    expression: string;
}>();
</script>

<template>
    <div
        :class="
            cn(
                'w-full h-20 bg-gray-900 dark:bg-gray-950 rounded-lg mb-4',
                'flex items-center justify-end px-4',
                'font-mono text-3xl text-white',
                'overflow-x-auto whitespace-nowrap'
            )
        "
    >
        {{ expression || '0' }}
    </div>
</template>
