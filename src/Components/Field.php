<?php

namespace JayAitch\GiraffeUI\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use JayAitch\GiraffeUI\Support\Form;

/**
 * Wraps a control with a label, hint and error message.
 *
 * This is a class (not an anonymous component) so it can hand its controls a single, uniquely named
 * value. Controls read it with @aware(['guiField']), which can't collide with other parents' attributes.
 */
class Field extends Component
{
    /**
     * What the control inside this field needs to know.
     *
     * @var array{id: ?string, name: ?string, hint: bool, error: ?string, required: bool}
     */
    public array $guiField;

    public function __construct(
        public ?string $label = null,
        public ?string $name = null,
        public ?string $id = null,
        public ?string $hint = null,
        public ?string $error = null,
        public bool $required = false,
    ) {
        $this->id ??= Form::id($name);

        $this->guiField = [
            'id' => $this->id,
            'name' => $name,
            'hint' => $hint !== null && $hint !== '',
            'error' => $error,
            'required' => $required,
        ];
    }

    public function render(): View
    {
        return view('giraffeui::field');
    }
}
