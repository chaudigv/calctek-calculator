<script setup lang="ts">
import { cn } from '@/lib/utils';
import type { CalculatorButton } from '@/types';

const emit = defineEmits<{
    keyPress: [value: string];
}>();

const buttons: CalculatorButton[] = [
    { label: 'sqrt', value: 'sqrt(', type: 'function' },
    { label: '^', value: '^', type: 'operator' },
    { label: '(', value: '(', type: 'operator' },
    { label: ')', value: ')', type: 'operator' },

    { label: 'C', value: 'C', type: 'clear' },
    { label: 'CE', value: 'CE', type: 'action' },
    { label: '%', value: '%', type: 'operator' },
    { label: '/', value: '/', type: 'operator' },

    { label: '7', value: '7', type: 'number' },
    { label: '8', value: '8', type: 'number' },
    { label: '9', value: '9', type: 'number' },
    { label: '*', value: '*', type: 'operator' },

    { label: '4', value: '4', type: 'number' },
    { label: '5', value: '5', type: 'number' },
    { label: '6', value: '6', type: 'number' },
    { label: '-', value: '-', type: 'operator' },

    { label: '1', value: '1', type: 'number' },
    { label: '2', value: '2', type: 'number' },
    { label: '3', value: '3', type: 'number' },
    { label: '+', value: '+', type: 'operator' },

    { label: '0', value: '0', type: 'number', span: 2 },
    { label: '.', value: '.', type: 'number' },
    { label: '=', value: '=', type: 'action' },
];

function getButtonClasses(button: CalculatorButton): string {
    const base = 'h-14 rounded-lg font-semibold text-lg transition-all active:scale-95 cursor-pointer';

    switch (button.type) {
        case 'number':
            return cn(base, 'bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-900 dark:text-white');
        case 'operator':
            return cn(base, 'bg-blue-500 text-white hover:bg-blue-600');
        case 'function':
            return cn(base, 'bg-purple-500 text-white hover:bg-purple-600 text-sm');
        case 'action':
            return cn(base, button.value === '=' ? 'bg-green-500 text-white hover:bg-green-600' : 'bg-gray-300 dark:bg-gray-600 text-gray-900 dark:text-white hover:bg-gray-400 dark:hover:bg-gray-500');
        case 'clear':
            return cn(base, 'bg-red-500 text-white hover:bg-red-600');
        default:
            return base;
    }
}
</script>

<template>
    <div class="grid grid-cols-4 gap-2">
        <button
            v-for="button in buttons"
            :key="button.label"
            :class="[getButtonClasses(button), button.span === 2 ? 'col-span-2' : '']"
            @click="emit('keyPress', button.value)"
        >
            {{ button.label }}
        </button>
    </div>
</template>
