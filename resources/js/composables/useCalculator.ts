import { ref } from 'vue';
import type { Calculation } from '@/types';

export function useCalculator() {
    const calculations = ref<Calculation[]>([]);
    const currentExpression = ref('');
    const isLoading = ref(false);

    function appendToExpression(value: string) {
        currentExpression.value += value;
    }

    function backspace() {
        currentExpression.value = currentExpression.value.slice(0, -1);
    }

    async function clearAllCalculations() {
        await fetch('/api/calculations', { method: 'DELETE' });
        calculations.value = [];
    }

    function clearExpression() {
        currentExpression.value = '';
    }

    async function deleteCalculation(id: number) {
        await fetch(`/api/calculations/${id}`, { method: 'DELETE' });
        calculations.value = calculations.value.filter((c) => c.id !== id);
    }

    async function fetchCalculations() {
        isLoading.value = true;

        try {
            const response = await fetch('/api/calculations');
            calculations.value = await response.json();
        } finally {
            isLoading.value = false;
        }
    }

    async function submitCalculation() {
        if (!currentExpression.value.trim()) {
            return;
        }

        isLoading.value = true;

        try {
            const response = await fetch('/api/calculations', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ expression: currentExpression.value }),
            });

            const calculation = await response.json();
            calculations.value.unshift(calculation);
            currentExpression.value = '';
        } finally {
            isLoading.value = false;
        }
    }

    return {
        calculations,
        currentExpression,
        isLoading,
        appendToExpression,
        backspace,
        clearAllCalculations,
        clearExpression,
        deleteCalculation,
        fetchCalculations,
        submitCalculation,
    };
}
