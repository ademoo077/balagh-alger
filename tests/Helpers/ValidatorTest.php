<?php
namespace Tests\Helpers;

use App\Helpers\Validator;
use PHPUnit\Framework\TestCase;

class ValidatorTest extends TestCase
{
    public function testRequiredPassesWhenFieldIsPresent(): void
    {
        $v = new Validator(['name' => 'John']);
        $v->required('name', 'Nom');
        $this->assertFalse($v->fails());
        $this->assertEmpty($v->errors());
    }

    public function testRequiredFailsWhenFieldIsEmpty(): void
    {
        $v = new Validator(['name' => '']);
        $v->required('name', 'Nom');
        $this->assertTrue($v->fails());
        $this->assertArrayHasKey('name', $v->errors());
        $this->assertStringContainsString('obligatoire', $v->firstError());
    }

    public function testRequiredFailsWhenFieldIsMissing(): void
    {
        $v = new Validator([]);
        $v->required('name', 'Nom');
        $this->assertTrue($v->fails());
    }

    public function testRequiredFailsWithWhitespaceOnly(): void
    {
        $v = new Validator(['name' => '   ']);
        $v->required('name');
        $this->assertTrue($v->fails());
    }

    public function testEmailPassesWhenValid(): void
    {
        $v = new Validator(['email' => 'user@example.com']);
        $v->email('email');
        $this->assertFalse($v->fails());
    }

    public function testEmailFailsWhenInvalid(): void
    {
        $v = new Validator(['email' => 'not-an-email']);
        $v->email('email', 'Email');
        $this->assertTrue($v->fails());
        $this->assertStringContainsString('email valide', $v->firstError());
    }

    public function testEmailPassesWhenEmpty(): void
    {
        $v = new Validator(['email' => '']);
        $v->email('email');
        $this->assertFalse($v->fails());
    }

    public function testMinLengthPasses(): void
    {
        $v = new Validator(['pass' => '123456']);
        $v->minLength('pass', 6);
        $this->assertFalse($v->fails());
    }

    public function testMinLengthFails(): void
    {
        $v = new Validator(['pass' => '123']);
        $v->minLength('pass', 6, 'Mot de passe');
        $this->assertTrue($v->fails());
        $this->assertStringContainsString('6 caractères', $v->firstError());
    }

    public function testMaxLengthPasses(): void
    {
        $v = new Validator(['text' => 'abc']);
        $v->maxLength('text', 5);
        $this->assertFalse($v->fails());
    }

    public function testMaxLengthFails(): void
    {
        $v = new Validator(['text' => 'abcdef']);
        $v->maxLength('text', 5, 'Texte');
        $this->assertTrue($v->fails());
        $this->assertStringContainsString('5 caractères', $v->firstError());
    }

    public function testNumericPasses(): void
    {
        $v = new Validator(['age' => '25']);
        $v->numeric('age');
        $this->assertFalse($v->fails());
    }

    public function testNumericPassesWithInteger(): void
    {
        $v = new Validator(['age' => 30]);
        $v->numeric('age');
        $this->assertFalse($v->fails());
    }

    public function testNumericPassesWithFloat(): void
    {
        $v = new Validator(['price' => '12.5']);
        $v->numeric('price');
        $this->assertFalse($v->fails());
    }

    public function testNumericFails(): void
    {
        $v = new Validator(['age' => 'abc']);
        $v->numeric('age', 'Âge');
        $this->assertTrue($v->fails());
        $this->assertStringContainsString('numérique', $v->firstError());
    }

    public function testChainedValidation(): void
    {
        $v = new Validator([
            'name' => '',
            'email' => 'bad',
            'age' => 'not-a-number',
        ]);
        $v->required('name', 'Nom')
          ->email('email', 'Email')
          ->numeric('age', 'Âge');

        $this->assertTrue($v->fails());
        $this->assertCount(3, $v->errors());
    }

    public function testFirstErrorReturnsFirstError(): void
    {
        $v = new Validator(['a' => '', 'b' => '']);
        $v->required('a')->required('b');
        $this->assertNotNull($v->firstError());
    }

    public function testFirstErrorReturnsNullWhenNoErrors(): void
    {
        $v = new Validator(['a' => 'x']);
        $v->required('a');
        $this->assertNull($v->firstError());
    }

    public function testFailsReturnsFalseOnSuccess(): void
    {
        $v = new Validator(['x' => 'y']);
        $v->required('x');
        $this->assertFalse($v->fails());
    }

    public function testErrorsReturnsEmptyArrayOnSuccess(): void
    {
        $v = new Validator(['x' => 'y']);
        $v->required('x');
        $this->assertEmpty($v->errors());
    }
}
