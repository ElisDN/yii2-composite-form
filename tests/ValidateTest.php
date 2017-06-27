<?php
/**
 * This file is part of the elisdn/yii2-composite-form library
 *
 * @copyright Copyright (c) Dmitry Eliseev <mail@elisdn.ru>
 * @license https://github.com/ElisDN/yii2-composite-form/blob/master/LICENSE.md
 * @link https://github.com/ElisDN/yii2-composite-form
 */

namespace elisdn\compositeForm\tests;

use elisdn\compositeForm\tests\_forms\OnlyNestedProductForm;
use elisdn\compositeForm\tests\_forms\ProductForm;

class ValidateTest extends TestCase
{
    public function testValidWholeForm()
    {
        $data = [
            'code' => 'P100',
            'name' => 'Product Name',
            'meta' => [
                'title' => 'Meta Title',
                'description' => 'Meta Description',
            ],
            'values' => [
                ['value' => '101'],
                ['value' => '102'],
                ['value' => '103'],
            ],
        ];

        $form = new ProductForm(3);

        $form->load($data, '');

        $this->assertTrue($form->validate());
        $this->assertFalse($form->hasErrors());
        $this->assertEmpty($form->getErrors());
    }

    public function testValidWithoutValues()
    {
        $data = [
            'code' => 'P100',
            'name' => 'Product Name',
            'meta' => [
                'title' => 'Meta Title',
                'description' => 'Meta Description',
            ],
            'values' => [],
        ];

        $form = new ProductForm(0);

        $form->load($data, '');

        $this->assertTrue($form->validate());
        $this->assertFalse($form->hasErrors());
        $this->assertEmpty($form->getErrors());
    }

    public function testNotValidWholeForm()
    {
        $data = [
            'code' => null,
            'name' => 'Product Name',
            'meta' => [
                'title' => null,
                'description' => 'Meta Description',
            ],
            'values' => [
                ['value' => '101'],
                ['value' => ''],
                ['value' => '103'],
            ],
        ];

        $form = new ProductForm(3);

        $form->load($data, '');

        $this->assertFalse($form->validate());
        $this->assertTrue($form->hasErrors());

        $this->assertEquals([
            'code' => ['Code cannot be blank.'],
            'meta.title' => ['Title cannot be blank.'],
            'values.1.value' => ['Value cannot be blank.'],
        ], $form->getErrors());

        $this->assertEquals(['Code cannot be blank.'], $form->getErrors('code'));
        $this->assertEquals(['Title cannot be blank.'], $form->getErrors('meta.title'));
        $this->assertEquals(['Value cannot be blank.'], $form->getErrors('values.1.value'));

        $this->assertEquals([], $form->getErrors('name'));
        $this->assertEquals([], $form->getErrors('meta.description'));
        $this->assertEquals([], $form->getErrors('values.2.value'));

        $this->assertTrue($form->hasErrors('code'));
        $this->assertFalse($form->hasErrors('name'));
        $this->assertTrue($form->hasErrors('meta.title'));
        $this->assertFalse($form->hasErrors('meta.description'));
        $this->assertTrue($form->hasErrors('values.1.value'));
        $this->assertFalse($form->hasErrors('values.2.value'));

        $this->assertEquals([
            'code' => 'Code cannot be blank.',
            'meta.title' => 'Title cannot be blank.',
            'values.1.value' => 'Value cannot be blank.',
        ], $form->getFirstErrors());
    }

    public function testNotValidInternalForms()
    {
        $data = [
            'code' => 'P100',
            'name' => 'Product Name',
            'meta' => [
                'title' => null,
                'description' => 'Meta Description',
            ],
            'values' => [
                ['value' => '101'],
                ['value' => ''],
                ['value' => '103'],
            ],
        ];

        $form = new ProductForm(3);

        $form->load($data, '');

        $this->assertFalse($form->validate());
        $this->assertTrue($form->hasErrors());

        $this->assertEquals([
            'meta.title' => ['Title cannot be blank.'],
            'values.1.value' => ['Value cannot be blank.'],
        ], $form->getErrors());

        $this->assertFalse($form->hasErrors('code'));
        $this->assertTrue($form->hasErrors('meta.title'));
        $this->assertTrue($form->hasErrors('values.1.value'));

        $this->assertEquals([
            'meta.title' => 'Title cannot be blank.',
            'values.1.value' => 'Value cannot be blank.',
        ], $form->getFirstErrors());
    }

    public function testValidAttributeNames()
    {
        $data = [
            'code' => 'P100',
            'name' => 'Product Name',
            'meta' => [
                'title' => 'Meta Title',
                'description' => 'Meta Description',
            ],
            'values' => [
                ['value' => '101'],
                ['value' => '103'],
            ],
        ];

        $form = new ProductForm(0);

        $form->load($data, '');

        $this->assertTrue($form->validate(['code']));
        $this->assertTrue($form->validate(['name']));
        $this->assertTrue($form->validate(['meta']));
        $this->assertTrue($form->validate(['meta' => ['title']]));
        $this->assertTrue($form->validate(['meta' => ['description']]));
        $this->assertTrue($form->validate(['meta' => ['title', 'description']]));
        $this->assertTrue($form->validate(['values']));
        $this->assertTrue($form->validate(['values' => ['value']]));
    }

    public function testNotValidAttributeNames()
    {
        $data = [
            'code' => null,
            'name' => 'Product Name',
            'meta' => [
                'title' => null,
                'description' => 'Meta Description',
            ],
            'values' => [
                ['value' => '101'],
                ['value' => ''],
            ],
        ];

        $form = new ProductForm(2);

        $form->load($data, '');

        $this->assertFalse($form->validate(['code']));
        $this->assertTrue($form->validate(['name']));
        $this->assertFalse($form->validate(['meta']));
        $this->assertFalse($form->validate(['meta' => ['title']]));
        $this->assertTrue($form->validate(['meta' => ['description']]));
        $this->assertFalse($form->validate(['meta' => ['title', 'description']]));
        $this->assertFalse($form->validate(['values']));
        $this->assertFalse($form->validate(['values' => ['value']]));
    }

    public function testValidOnlyNestedForms()
    {
        $data = [
            'meta' => [
                'title' => 'Meta Title',
                'description' => 'Meta Description',
            ],
        ];

        $form = new OnlyNestedProductForm();

        $form->load($data, '');

        $this->assertTrue($form->validate());
        $this->assertFalse($form->hasErrors());
        $this->assertEmpty($form->getErrors());
    }

    public function testNotValidOnlyNestedForms()
    {
        $data = [
            'meta' => [
                'title' => null,
                'description' => 'Meta Description',
            ],
        ];

        $form = new OnlyNestedProductForm();

        $form->load($data, '');

        $this->assertFalse($form->validate());
        $this->assertTrue($form->hasErrors());

        $this->assertEquals([
            'meta.title' => ['Title cannot be blank.'],
        ], $form->getErrors());

        $this->assertEquals([
            'meta.title' => 'Title cannot be blank.',
        ], $form->getFirstErrors());
    }
}