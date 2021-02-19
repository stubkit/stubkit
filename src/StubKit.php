<?php

namespace StubKit;

use Closure;
use Illuminate\Support\Arr;
use PHPUnit\Framework\Assert;
use StubKit\Support\Fields;
use StubKit\Support\Item;
use StubKit\Support\Syntax;
use Symfony\Component\Finder\Finder;

class StubKit
{
    /**
     * The modified time.
     *
     * @var mixed
     */
    public $modified;

    /**
     * The concept being scaffolded.
     *
     * @var string
     */
    public $scaffolding;

    /**
     * The fields being scaffolded.
     *
     * @var string
     */
    public $fields;

    /**
     * The syntax used to render.
     *
     * @var Syntax
     */
    public $syntax;

    /**
     * The rendered files.
     *
     * @var array
     */
    public $rendered = [];

    /**
     * The created files.
     *
     * @var array
     */
    public $created = [];

    /**
     * The updated files.
     *
     * @var array
     */
    public $updated = [];

    /**
     * The ignored files.
     *
     * @var array
     */
    public $ignored = [];

    /**
     * The active field.
     *
     * @var string
     */
    public $activeField;

    /**
     * Perform syntax render.
     *
     * @return array
     */
    public function render()
    {
        $syntax = $this->syntax();

        $files = $this->getTracked();

        foreach ($files as $path) {
            $content = file_get_contents($path);
            $content = $syntax->parse($content);
            file_put_contents($path, $content);
            $this->rendered[] = $path;
        }

        return $files;
    }

    /**
     * Ignore paths during tracking.
     *
     * @param $path
     */
    public function ignore($path)
    {
        $this->ignored[] = $path;
    }

    /**
     * Get the syntax.
     *
     * @param array $entities
     *
     * @return Syntax
     */
    public function syntax(array $entities = [])
    {
        if (! is_null($this->syntax)) {
            return $this->syntax;
        }

        $this->syntax = new Syntax;

        $this->syntax->make(
            $entities,
            $this->defaultVariables(),
            $this->definedVariables()
        );

        return $this->syntax;
    }

    /**
     * Get the files.
     *
     * @return array
     */
    public function getTracked()
    {
        $files = $this->created;

        if (count($this->created) == 0) {
            $files = $this->getModifiedFiles();
        }

        $files = array_diff($files, $this->ignored);
        $files = array_diff($files, $this->rendered);
        $files = array_values($files);
        $files = array_merge($files, $this->updated);

        return $files;
    }

    /**
     * Get the modified files.
     *
     * @return array
     */
    public function getModifiedFiles()
    {
        $files = [];

        $finder = (new Finder)
            ->files()
            ->in(base_path())
            ->notPath(config('stubkit.excludes'));

        if ($this->modified) {
            $finder = $finder->date('>='.$this->modified);
        }

        foreach ($finder as $file) {
            $files[] = $file->getPathname();
        }

        return $files;
    }

    /**
     * Get the rendered files.
     *
     * @return array
     */
    public function rendered()
    {
        return $this->rendered;
    }

    /**
     * Get the variables used.
     *
     * @return array
     */
    public function variables()
    {
        return $this->syntax->all();
    }

    /**
     * Find stubs to publish.
     *
     * @param string $path
     * @param string $destination
     *
     * @return array
     */
    public function discover(string $path, string $destination = 'stubs')
    {
        $stubs = [];

        foreach ((new Finder)->in($path) as $file) {
            $stubs[$file->getPathname()] = base_path("${destination}/{$file->getBasename()}");
        }

        return $stubs;
    }

    /**
     * Start tracking files.
     *
     * @return $this
     */
    public function track()
    {
        $this->created = [];
        $this->updated = [];
        $this->modified = now();

        return $this;
    }

    /**
     * Add a variable.
     *
     * @param string $key
     * @param mixed $value
     *
     * @return void
     */
    public function variable(string $key, $value)
    {
        config()->set("stubkit.variables.${key}", $value);
    }

    /**
     * Add types for fields.
     *
     * @param array $types
     *
     * @return void
     */
    public function addTypes(array $types = [])
    {
        $fields = new Fields(
            config('stubkit-fields'),
            config('stubkit-mappings'),
        );

        config()->set(
            'stubkit-fields',
            $fields->addTypes($types)
        );
    }

    /**
     * Check if command is allowed.
     *
     * @param string $command
     *
     * @return bool
     */
    public function allows($command = null)
    {
        return ! is_null($command) && in_array($command, config('stubkit.commands', []));
    }

    /**
     * Get or set the scaffold name.
     * @param null $name
     * @return void|string
     */
    public function scaffold($name = null)
    {
        if (is_null($name)) {
            return $this->scaffolding;
        }

        $this->scaffolding = $name;
    }

    /**
     * Get or set the scaffold fields string.
     * @param null $fields
     * @return string|void
     */
    public function fields($fields = null)
    {
        if (is_null($fields)) {
            return $this->fields;
        }

        $this->fields = $fields;
    }

    /**
     * Check if in the process of scaffolding.
     * @return bool
     */
    public function scaffolding()
    {
        return ! is_null($this->scaffolding);
    }

    /**
     * Manually add a file that was created for rendering.
     * @param $path
     */
    public function created($path)
    {
        $this->created[] = $path;
    }

    /**
     * Manually add a file that was updated for rendering.
     * @param $path
     */
    public function updated($path)
    {
        $this->updated[] = $path;
    }

    /**
     * Handle the use of the @stubkit directive.
     * @param $expression
     * @return string
     */
    public function directive($expression)
    {
        $expression = trim($expression, '"');
        $expression = trim($expression, "'");
        $expression = trim($expression, '{{');
        $expression = trim($expression, '}}');
        $expression = str_replace('{{', '<::', $expression);
        $expression = str_replace('}}', '::>', $expression);
        $expression = '@{{ '.trim($expression).' }}';

        return "<?php echo \StubKit\Facades\StubKit::helper('${expression}', get_defined_vars()); ?>";
    }

    /**
     * Handle usage of the stubkit() helper.
     * @param $expression
     * @param array $variables
     * @return string
     */
    public function helper($expression, array $variables = [])
    {
        $expression = str_replace('<::', '{{', $expression);
        $expression = str_replace('::>', '}}', $expression);

        foreach ($variables as $key => $value) {
            if (in_array($key, ['app', 'helper', 'loop', 'raw', '__currentLoopData', '__path', '__data', '__env', 'field_type'])) {
                unset($variables[$key]);
            } elseif (is_a($value, Item::class)) {
                $variables[$key] = $value->field;
            } elseif (! is_string($value)) {
                unset($variables[$key]);
            }
        }

        if (count($variables)) {
            $syntax = new Syntax;
            $syntax->setVariables($this->syntax()->all());
            $syntax->mergeMake(
                $variables,
                $this->defaultVariables(),
                $this->definedVariables(),
            );
        } else {
            $syntax = $this->syntax();
        }

        return $syntax->parse($expression);
    }

    /**
     * Test assertion for callback / snapshot comparison.
     * @param Closure $actual
     * @param $expected
     */
    public function assertRender(Closure $actual, $expected)
    {
        $actual = call_user_func($actual);
        $path = pathinfo($expected)['dirname'].'/'.time().'.txt';
        $expected = file_get_contents($expected);
        file_put_contents($path, $actual);
        $this->created($path);
        $this->render();
        $actual = file_get_contents($path);
        unlink($path);
        Assert::assertEquals($expected, $actual);
    }

    /**
     * Get the default variables.
     * @return array
     */
    public function defaultVariables()
    {
        $variables = config('stubkit.variables', []);

        return Arr::get($variables, '*', []);
    }

    /**
     * Get the defined variables.
     * @return array
     */
    public function definedVariables()
    {
        $variables = config('stubkit.variables', []);

        unset($variables['*']);

        return $variables;
    }

    /**
     * Set or get the active field.
     * @param null $field
     * @return string|void
     */
    public function activeField($field = null)
    {
        if (is_null($field)) {
            return $this->activeField;
        }

        $this->activeField = $field;
    }
}
