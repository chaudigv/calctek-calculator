export type CalculationError = {
    id: number;
    message: string;
    errorable_type: string;
    errorable_id: number;
    created_at: string;
    updated_at: string;
};

export type Calculation = {
    id: number;
    expression: string;
    result: string | null;
    error: CalculationError | null;
    created_at: string;
    updated_at: string;
};

export type CalculatorButton = {
    label: string;
    value: string;
    type: 'number' | 'operator' | 'function' | 'action' | 'clear';
    span?: number;
};
