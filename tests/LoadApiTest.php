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

class LoadApiTest extends TestCase
{
    public function testWholeForm()
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

        $this->assertEquals($data['code'], $form->code);
        $this->assertEquals($data['name'], $form->name);

        $this->assertEquals($data['meta']['title'], $form->meta->title);
        $this->assertEquals($data['meta']['description'], $form->meta->description);

        $this->assertCount(3, $values = $form->values);

        $this->assertEquals($data['values'][0]['value'], $values[0]->value);
        $this->assertEquals($data['values'][1]['value'], $values[1]->value);
        $this->assertEquals($data['values'][2]['value'], $values[2]->value);
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

        $this->assertEquals($data['meta']['title'], $form->meta->title);
        $this->assertEquals($data['meta']['description'], $form->meta->description);
    }
}
