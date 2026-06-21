<?php

namespace App\Helpers;

class CurrencyManager
{
    private string $symbol = '₦';
    private string $position = 'before';
    private string $decimalSeparator = '.';
    private string $thousandSeparator = ',';
    private int $decimals = 2;

    /**
     * Format a number into a currency string
     */
    public function format(float $amount): string
    {
        $amount = (float)$amount; // auto-cast null → 0.0
        $formatted = number_format(
            $amount,
            $this->decimals,
            $this->decimalSeparator,
            $this->thousandSeparator
        );

        if ($this->position === 'before') {
            return "{$this->symbol}{$formatted}";
        }

        return "{$formatted}{$this->symbol}";
    }


    /**
     * Set a new currency symbol
     */
    public function setSymbol(string $symbol): void
    {
        $this->symbol = $symbol;
    }

    /**
     * Set the position of the currency symbol
     */
    public function setPosition(string $position): void
    {
        if (!in_array($position, ['before', 'after'])) {
            throw new \InvalidArgumentException("Position must be 'before' or 'after'.");
        }

        $this->position = $position;
    }

    /**
     * Set decimal and thousand separators
     */
    public function setSeparators(string $decimal, string $thousand): void
    {
        $this->decimalSeparator = $decimal;
        $this->thousandSeparator = $thousand;
    }

    /**
     * Set number of decimal places
     */
    public function setDecimals(int $decimals): void
    {
        $this->decimals = max(0, $decimals);
    }
}
