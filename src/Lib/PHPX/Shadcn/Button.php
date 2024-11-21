<?php

namespace Lib\PHPX\Shadcn;

use Lib\PHPX\Utils;

class Button
{
    /**
     * @var array<string, mixed> The properties or attributes passed to the component.
     */
    private array $props;

    /**
     * @var mixed The children elements or content to be rendered within the component.
     */
    private mixed $children;

    /**
     * @var string The CSS class for custom styling.
     */
    private string $class;

    /**
     * @var string Stores the precomputed CSS classes for the component.
     */
    private string $computedClass;

    /**
     * Constructor to initialize the component with the given properties.
     * 
     * @param array<string, mixed> $props Optional properties to customize the component.
     */
    public function __construct(array $props = [])
    {
        $this->props = $props;
        $this->children = $props['children'] ?? '';
        $this->class = $props['class'] ?? '';

        // Precompute the static classes once during construction
        $this->computedClass = $this->getComputedClasses();

        // Optionally, execute an initialization function if passed
        $this->executeFunction('onInit', [$this->props]);
    }

    /**
     * Registers or initializes any necessary components or settings. (Placeholder method).
     */
    public static function init(): void
    {
        // Register the component or any necessary initialization
    }

    /**
     * Combines and returns the CSS classes for the component.
     * 
     * @return string The merged CSS class string.
     */
    private function getComputedClasses(): string
    {
        $defaultClasses = 'inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50';

        $variantClasses = [
            'default' => 'bg-primary text-primary-foreground hover:bg-primary/90',
            'destructive' => 'bg-destructive text-destructive-foreground hover:bg-destructive/90',
            'outline' => 'border border-input bg-background hover:bg-accent hover:text-accent-foreground',
            'secondary' => 'bg-secondary text-secondary-foreground hover:bg-secondary/80',
            'ghost' => 'hover:bg-accent hover:text-accent-foreground',
            'link' => 'text-primary underline-offset-4 hover:underline',
        ];

        $sizeClasses = [
            'default' => 'h-10 px-4 py-2',
            'sm' => 'h-9 px-3',
            'lg' => 'h-11 px-8',
            'icon' => 'h-10 w-10',
        ];

        $variant = $this->props['variant'] ?? 'default';
        $size = $this->props['size'] ?? 'default';
        $customClass = $this->props['class'] ?? '';

        $variantClass = $variantClasses[$variant] ?? $variantClasses['default'];
        $sizeClass = $sizeClasses[$size] ?? $sizeClasses['default'];

        return Utils::mergeClasses($defaultClasses, $variantClass, $sizeClass, $customClass);
    }

    /**
     * Generates and returns a string of HTML attributes from the provided props.
     * Excludes 'class' and 'children' props from being added as attributes.
     * 
     * @return string The generated HTML attributes.
     */
    private function getAttributes(): string
    {
        // Filter out 'class' and 'children' props
        $filteredProps = array_filter($this->props, function ($key) {
            return !in_array($key, ['class', 'children']);
        }, ARRAY_FILTER_USE_KEY);

        // Build attributes string by escaping keys and values
        $attributes = [];
        foreach ($filteredProps as $key => $value) {
            $escapedKey = htmlspecialchars($key, ENT_QUOTES, 'UTF-8');
            $escapedValue = htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
            $attributes[] = "$escapedKey='$escapedValue'";
        }

        return implode(' ', $attributes);
    }

    /**
     * Executes a callable function passed in the props.
     * 
     * @param string $propKey The key of the prop to check for a callable.
     * @param array $params Parameters to pass to the function.
     * @return mixed|null The result of the function execution, or null if not callable.
     */
    private function executeFunction(string $propKey, array $params = [])
    {
        if (isset($this->props[$propKey]) && is_callable($this->props[$propKey])) {
            // Execute the function and pass the parameters
            return call_user_func_array($this->props[$propKey], $params);
        }

        return null;
    }

    /**
     * Renders the component as an HTML string with the appropriate classes and attributes.
     * Also, allows for dynamic children rendering if a callable is passed.
     * 
     * @return string The final rendered HTML of the component.
     */
    public function render(): string
    {
        $attributes = $this->getAttributes();
        $class = $this->computedClass;
        $type = $this->props['type'] ?? 'button';

        // Handle children being a callable (e.g., for dynamic content)
        $childrenContent = is_callable($this->children)
            ? call_user_func($this->children, $this->props) // Pass props to children if callable
            : $this->children;

        // Execute a render hook function if defined
        $this->executeFunction('onRender', [$this, $this->props]);

        return "<button class='$class' type='$type' $attributes >" . $this->children . "</button>";
    }

    /**
     * Converts the object to its string representation by rendering it.
     *
     * @return string The rendered HTML output of the component.
     */
    public function __toString(): string
    {
        return $this->render();
    }
}
