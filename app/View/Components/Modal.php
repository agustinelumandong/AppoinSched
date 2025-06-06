<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Modal extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public readonly string $id,
        public readonly string $title,
        public readonly string $size = 'max-w-xl',
        public readonly bool $closable = true,
        public readonly bool $backdrop = true,
        public readonly string $footerClass = 'justify-end'
    ) {
        // Constructor logic can be added here if needed
    }

    /**
     * Get the classes for the modal size.
     *
     * @return string
     */
    public function sizeClasses()
    {
        return match ($this->size) {
            'sm' => 'max-w-sm',
            'md' => 'max-w-md',
            'lg' => 'max-w-lg',
            'xl' => 'max-w-xl',
            default => 'max-w-2xl',
        };
    }

    /**
     * Get the classes for the modal footer.
     *
     * @return string
     */
    public function render(): View|Closure|string
    {
        return view('components.modal');
    }
}
