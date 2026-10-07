{{-- A code block with a copy button. Pass $code, or $model for Alpine-driven code. --}}
<x-gui::code-block :code="$code ?? null" :live="$model ?? null" />
