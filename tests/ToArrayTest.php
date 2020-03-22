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

class ToArrayTest extends TestCase
{

    public function testSuccess()
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

        $this->assertTrue($form->load($data, ''));

        $array = $form->toArray();

        $this->assertEquals($data['code'], $array['code']);
        $this->assertEquals($data['name'], $array['name']);

        $this->assertArrayHasKey('meta', $array);
        $this->assertEquals($data['meta'], $array['meta']);
        
        $this->assertArrayHasKey('values', $array);
        $this->assertEquals($data['values'], $array['values']);
    }

    public function testOnlyInternalForms()
    {
        $data = [
            'meta' => [
                'title' => 'Meta Title',
                'description' => 'Meta Description',
            ],
        ];

        $form = new OnlyNestedProductForm();

        $this->assertTrue($form->load($data, ''));

        $array = $form->toArray();

        $this->assertArrayHasKey('meta', $array);
        $this->assertEquals($data['meta'], $array['meta']);
    }
}