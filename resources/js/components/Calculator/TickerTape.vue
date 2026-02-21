<script setup lang="ts">
import { cn } from '@/lib/utils';
import type { Calculation } from '@/types';

defineProps<{
    calculations: Calculation[];
    isLoading: boolean;
}>();

const emit = defineEmits<{
    delete: [id: number];
    clearAll: [];
}>();

function formatResult(calc: Calculation): string {
    if (calc.error) {
        return `Error: ${calc.error.message}`;
    }

    if (calc.result === null) {
        return '';
    }

    const num = parseFloat(calc.result);

    if (Number.isInteger(num)) {
        return num.toString();
    }

    return parseFloat(num.toFixed(10)).toString();
}
</script>

<template>
    <div class="flex flex-col h-full">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">History</h2>
            <button
                v-if="calculations.length > 0"
                class="text-red-500 hover:text-red-600 text-sm font-medium cursor-pointer"
                @click="emit('clearAll')"
            >
                Clear All
            </button>
        </div>

        <div v-if="isLoading" class="flex-1 flex items-center justify-center">
            <div class="animate-spin h-8 w-8 border-4 border-blue-500 border-t-transparent rounded-full" />
        </div>

        <div v-else-if="calculations.length === 0" class="flex-1 flex items-center justify-center text-gray-500">
            No calculations yet
        </div>

        <div v-else class="flex-1 overflow-y-auto space-y-2">
            <div
                v-for="calc in calculations"
                :key="calc.id"
                :class="cn('group p-3 rounded-lg wrap-anywhere', calc.error ? 'bg-red-50 dark:bg-red-900/20' : 'bg-gray-50 dark:bg-gray-700/50')"
            >
                <div class="flex items-start justify-between">
                    <div class="flex-1 font-mono">
                        <div class="text-gray-600 dark:text-gray-400 text-sm">
                            {{ calc.expression }}
                        </div>
                        <div
                            :class="cn('text-lg font-semibold', calc.error ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-white')"
                        >
                            = {{ formatResult(calc) }}
                        </div>
                    </div>
                    <button
                        class="opacity-0 group-hover:opacity-100 text-gray-400 hover:text-red-500 transition-opacity cursor-pointer"
                        @click="emit('delete', calc.id)"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
