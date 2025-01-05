# GiraffeUI :: Button

## Introduction

The GiraffeUI (GUI) Button component is a flexible and customizable Laravel Blade component designed to create buttons with various styles and functionalities, leveraging the power of Tailwind CSS for seamless styling integration. This component empowers you to easily integrate visually appealing buttons into your Laravel application with extensive options for colors, sizes, variants, and more.

## Usage

### Button Component

```php
<x-gui::button 
    text="Learn More" 
    link="https://github.com/Jarryd-W-Hoffman/GiraffeUI"
    external
    color="primary"
    variant="contain"
    fullWidth
    customRight='
        <svg 
            xmlns="http://www.w3.org/2000/svg" 
            fill="none" 
            viewBox="0 0 24 24" 
            stroke-width="1.5" 
            stroke="currentColor" 
            class="w-6 h-6"
        >
            <path 
                stroke-linecap="round" 
                stroke-linejoin="round" 
                d="m5.25 4.5 7.5 7.5-7.5 7.5m6-15 7.5 7.5-7.5 7.5" 
            />
        </svg>'
/>
```

### Parameters

- `text` (string, required): The text content of the button.
- `link` (string, optional): The link to which the button should navigate.
- `color` (string, optional): The color of the button (primary, secondary, success, warning, danger, info, light, dark).
- `size` (string, optional): The size of the button (small, default, large).
- `variant` (string, optional): The variant of the button (contain, outline, text).
- `type` (string, optional): The type of the button (button, reset, submit).
- `fullWidth` (boolean, optional): Whether the button should take up the full width of the container.
- `disabled` (boolean, optional): Whether the button is disabled.
- `external` (boolean, optional): Whether the link is an external link.
- `customLeft` (string, optional): Custom content to be displayed on the left side of the button.
- `customRight` (string, optional): Custom content to be displayed on the right side of the button.

## Styling

The GiraffeUI Button component generates dynamic styles based on the configured colors and sizes. You can customize these styles by updating the configurations in the `config/giraffeui.php` file.

### Dynamic Styling with GiraffeUI Configurations

The GiraffeUI Button component offers dynamic styling capabilities, allowing you to easily configure the appearance of your buttons by updating the configurations in the `config/giraffeui.php` file. When `use_tailwind_config` is set to `false` in the configuration, GiraffeUI adopts its own color configurations, which are inspired by the Tailwind CSS color palette. Here's an example snippet from the configuration:

```php
'colors' => [
    'primary' => [
        'base' => [
            'color' => 'blue',
            'shade' => '500',
            'opacity' => null,
        ],
        'text_color' => 'white',
    ],
    'secondary' => [
        'base' => [
            'color' => 'gray',
            'shade' => '500',
            'opacity' => null,
        ],
        'text_color' => 'black',
    ],
    // ... additional color configurations
],
```

In this example, the `primary` color is configured with a base color of blue, shade 500, and no opacity. The `text_color` is set to white. Similarly, the `secondary` color is configured with a base color of gray, shade 500, and text color of black. These configurations are used to dynamically generate styles for the buttons.

### Adopting Tailwind CSS Configurations

If `use_tailwind_config` is set to `true` in the configuration, GiraffeUI allows users to leverage their custom Tailwind CSS configurations. Users can specify their preferred colors in the Tailwind CSS config, and the GiraffeUI Button component will automatically use these custom colors. Here's an example of a Tailwind CSS configuration:

```javascript
// tailwind.config.js
module.exports = {
    extend: {
        colors: {
            'branding-primary': '#89CFF0',
            // ... additional custom colors
        },
    },
    // ... additional Tailwind CSS configurations
};
```

In this example, the user has defined a custom color named `branding-primary` with a hex value. With `use_tailwind_config` set to `true`, GiraffeUI will recognize and use these custom color names (e.g., `branding-primary`) instead of the default GiraffeUI color names when rendering buttons.

### Configuration Switch

Users have the flexibility to choose between the default GiraffeUI color configurations or their custom Tailwind CSS configurations by adjusting the `use_tailwind_config` option in the `config/giraffeui.php` file.

```php
'use_tailwind_config' => false, // Use GiraffeUI color configurations
```

or

```php
'use_tailwind_config' => true, // Use Tailwind CSS color configurations
```

This seamless integration allows developers to tailor the styling of GiraffeUI buttons to their preferences, whether it's based on the default GiraffeUI color palette or their custom Tailwind CSS colors.

## Examples

- **Basic Usage:**
    ```php
    <x-gui::button text="Click me" />
    ```

- **Internal Link with Right Icon:**
    ```php
    <x-gui::button 
        text="Internal Page" 
        link="{{ route('page') }}" 
        customRight='<i class="fas fa-external-link-alt"></i>' 
    />
    ```

- **Disabled Button:**
    ```php
    <x-gui::button text="Disabled Button" disabled />
    ```

For more examples and customization options, refer to the provided documentation and the configuration files.
