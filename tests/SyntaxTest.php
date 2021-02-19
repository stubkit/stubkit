<?php

namespace StubKit\Tests;

use PHPUnit\Framework\TestCase;
use StubKit\Support\Syntax;

class SyntaxTest extends TestCase
{
    public function test_syntax_globals()
    {
        $config = require __DIR__.'/../config/stubkit.php';
        $syntax = (new Syntax)->make(
            ['resource' => 'UserAccount'],
            $config['variables']['*']
        );

        $this->assertCount(13, $syntax->all());
        $this->assertCount(12, $config['variables']['*']); // minus 1 for original
        $this->assertEquals('UserAccount', $syntax->parse('{{ resource }}'));
        $this->assertEquals('user account', $syntax->parse('{{ resource.lower }}'));
        $this->assertEquals('User Account', $syntax->parse('{{resource.title }}'));
        $this->assertEquals('UserAccount', $syntax->parse('{{ resource.studly }}'));
        $this->assertEquals('userAccount', $syntax->parse('{{ resource.camel}}'));
        $this->assertEquals('user-account', $syntax->parse('{{ resource.slug }}'));
        $this->assertEquals('user_account', $syntax->parse('{{ resource.snake }}'));
        $this->assertEquals('user accounts', $syntax->parse('{{ resource.plural }}'));
        $this->assertEquals('User Accounts', $syntax->parse('{{ resource.titlePlural }}'));
        $this->assertEquals('UserAccounts', $syntax->parse('{{ resource.studlyPlural }}'));
        $this->assertEquals('userAccounts', $syntax->parse('{{ resource.camelPlural }}'));
        $this->assertEquals('user-accounts', $syntax->parse('{{ resource.slugPlural }}'));
        $this->assertEquals('user_accounts', $syntax->parse('{{ resource.snakePlural }}'));
    }

    public function test_variables_get_priority_over_globals()
    {
        $global = [
            'lower' => function ($value) {
                return "This ${value} gets overridden by option";
            },
        ];

        $variables = [
            'user' => [
                'lower' => function ($value) {
                    return strtolower($value);
                },
            ],
        ];

        $output = (new Syntax)
            ->make(['user' => 'SARA'], $global, $variables)
            ->parse('{{ user.lower }}');

        $this->assertEquals('sara', $output);
    }

    public function test_variables_can_be_top_level()
    {
        $variables = [
            'url' => function () {
                return 'google.com';
            },
        ];

        $value = (new Syntax)
            ->make([], [], $variables)
            ->parse('{{ url }}');

        $this->assertEquals('google.com', $value);
    }

    public function test_top_level_variables_get_option_values()
    {
        $variables = [
            'user' => function ($value) {
                return ucfirst($value);
            },
        ];

        $output = (new Syntax)
            ->make(['user' => 'sara'], [], $variables)
            ->parse('{{ user }}');

        $this->assertEquals('Sara', $output);
    }

    public function test_syntax_skips_boolean_variables()
    {
        $syntax = (new Syntax)->make([
            'help' => true,
            'no-interaction' => false,
            'empty' => '',
        ]);

        $this->assertCount(0, $syntax->all());
        $this->assertEquals('{{ help }}', $syntax->parse('{{ help }}'));
        $this->assertEquals('{{ no-interaction }}', $syntax->parse('{{ no-interaction }}'));
        $this->assertEquals('{{ empty }}', $syntax->parse('{{ empty }}'));
    }

    public function test_syntax_output_same_indentation_of_variable_content()
    {
        $stub = file_get_contents(__DIR__.'/Fixtures/indentation-1/stub.txt');
        $expected = file_get_contents(__DIR__.'/Fixtures/indentation-1/expected.txt');
        $variable = file_get_contents(__DIR__.'/Fixtures/indentation-1/variable.txt');

        $syntax = new Syntax;
        $syntax->setVariables(['fields.rules' => $variable]);
        $output = $syntax->parse($stub);
        $this->assertEquals($expected, $output);
    }

    public function test_syntax_output_same_indentation_of_variable_content_with_indents()
    {
        $stub = file_get_contents(__DIR__.'/Fixtures/indentation-2/stub.txt');
        $expected = file_get_contents(__DIR__.'/Fixtures/indentation-2/expected.txt');
        $variable = file_get_contents(__DIR__.'/Fixtures/indentation-2/variable.txt');

        $syntax = new Syntax;
        $syntax->setVariables(['fields.inputs' => $variable]);
        $output = $syntax->parse($stub);
        $this->assertEquals($expected, $output);
    }

    public function test_syntax_output_variable_content_with_single_space_before()
    {
        $syntax = new Syntax;
        $syntax->setVariables(['model.studly' => 'User']);
        $output = $syntax->parse(' {{ model.studly }}');
        $this->assertEquals(' User', $output);
    }

    public function test_syntax_output_variable_content_with_single_space_after()
    {
        $syntax = new Syntax;
        $syntax->setVariables(['model.studly' => 'User']);
        $output = $syntax->parse('{{ model.studly }} ');
        $this->assertEquals('User ', $output);
    }

    public function test_syntax_output_variable_content_with_single_space_before_after()
    {
        $syntax = new Syntax;
        $syntax->setVariables(['model.studly' => 'User']);
        $output = $syntax->parse(' {{ model.studly }} ');
        $this->assertEquals(' User ', $output);
    }
}
