<?php

namespace Lib\PHPX\GoogleIcons;

use Lib\PHPX\Utils;

/**
 * ComponentTemplate class represents a base component for rendering HTML elements with customizable properties.
 * 
 * It provides a structured way to define reusable UI components such as cards, with support for dynamic class 
 * and attribute merging.
 */
class ArrowBackiOS
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
        $baseClass = ''; // Define any base classes if necessary
        return Utils::mergeClasses($baseClass, $this->class);
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

        // Handle children being a callable (e.g., for dynamic content)
        $childrenContent = is_callable($this->children)
            ? call_user_func($this->children, $this->props) // Pass props to children if callable
            : $this->children;

        // Execute a render hook function if defined
        $this->executeFunction('onRender', [$this, $this->props]);

        return "<svg xmlns='http://www.w3.org/2000/svg' height='24px' viewBox='0 -960 960 960' width='24px' fill='currentColor'>
                <path d='M400-80 0-480l400-400 71 71-329 329 329 329-71 71Z' />
            </svg>";
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
