<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { onMounted, onUnmounted, ref } from 'vue';
import { useCalculator } from '@/composables/useCalculator';
import CalculatorDisplay from '@/components/Calculator/CalculatorDisplay.vue';
import CalculatorKeypad from '@/components/Calculator/CalculatorKeypad.vue';
import TickerTape from '@/components/Calculator/TickerTape.vue';

const {
    appendToExpression,
    backspace,
    calculations,
    clearAllCalculations,
    clearExpression,
    currentExpression,
    deleteCalculation,
    fetchCalculations,
    isLoading,
    submitCalculation,
} = useCalculator();

const calculatorHeight = ref<number | null>(null);
const calculatorRef = ref<HTMLElement | null>(null);

function handleKeyboard(event: KeyboardEvent) {
    const key = event.key;

    if (key === 'Enter') {
        event.preventDefault();
        submitCalculation();
    } else if (key === 'Escape') {
        clearExpression();
    } else if (key === 'Backspace') {
        backspace();
    } else if (/^[\d\+\-\*\/\.\(\)\^%]$/.test(key)) {
        appendToExpression(key);
    }
}

function handleKeyPress(value: string) {
    if (value === '=') {
        submitCalculation();
    } else if (value === 'C') {
        clearExpression();
    } else if (value === 'CE') {
        backspace();
    } else {
        appendToExpression(value);
    }
}

function updateHeight() {
    if (calculatorRef.value) {
        calculatorHeight.value = calculatorRef.value.offsetHeight;
        console.log(calculatorHeight.value);
        
    }
}

onMounted(() => {
    fetchCalculations();
    updateHeight();
    window.addEventListener('resize', updateHeight);
});

onUnmounted(() => {
    window.removeEventListener('resize', updateHeight);
});
</script>

<template>
    <Head title="Calculator" />

    <div class="min-h-screen bg-gray-100 dark:bg-gray-900 py-8" @keydown="handleKeyboard" tabindex="0">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-8">CalcTek Calculator</h1>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-start">
                <div ref="calculatorRef" class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6">
                    <CalculatorDisplay :expression="currentExpression" />
                    <CalculatorKeypad @key-press="handleKeyPress" />
                </div>

                <div
                    class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6 overflow-auto"
                    :style="{
                        height: calculatorHeight ? `${calculatorHeight}px` : '100%',
                        maxHeight: calculatorHeight ? `${calculatorHeight}px` : undefined
                    }"
                >
                    <TickerTape
                        :calculations="calculations"
                        :is-loading="isLoading"
                        @delete="deleteCalculation"
                        @clear-all="clearAllCalculations"
                    />
                </div>
            </div>
        </div>
    </div>
</template>
