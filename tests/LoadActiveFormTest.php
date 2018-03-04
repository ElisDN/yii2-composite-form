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

class LoadActiveFormTest extends TestCase
{
    public function testWholeForm()
    {
        $data = [
            'ProductForm' => [
                'code' => 'P100',
                'name' => 'Product Name',
            ],
            'MetaForm' => [
                'title' => 'Meta Title',
                'description' => 'Meta Description',
            ],
            'ValueForm' => [
                ['value' => '101'],
                ['value' => '102'],
                ['value' => '103'],
            ],
        ];

        $form = new ProductForm(3);

        $this->assertTrue($form->load($data));

        $this->assertEquals($data['ProductForm']['code'], $form->code);
        $this->assertEquals($data['ProductForm']['name'], $form->name);

        $this->assertEquals($data['MetaForm']['title'], $form->meta->title);
        $this->assertEquals($data['MetaForm']['description'], $form->meta->description);

        $this->assertCount(3, $values = $form->values);

        $this->assertEquals($data['ValueForm'][0]['value'], $values[0]->value);
        $this->assertEquals($data['ValueForm'][1]['value'], $values[1]->value);
        $this->assertEquals($data['ValueForm'][2]['value'], $values[2]->value);
    }

    public function testPartialForm()
    {
        $data = [
            'ProductForm' => [
                'code' => 'P100',
                'name' => 'Product Name',
            ],
            'MetaForm' => [
                'title' => 'Meta Title',
                'description' => 'Meta Description',
            ],
        ];

        $form = new ProductForm(3);

        $this->assertTrue($form->load($data));

        $this->assertEquals($data['ProductForm']['code'], $form->code);
        $this->assertEquals($data['ProductForm']['name'], $form->name);

        $this->assertEquals($data['MetaForm']['title'], $form->meta->title);
        $this->assertEquals($data['MetaForm']['description'], $form->meta->description);

        $this->assertCount(3, $values = $form->values);

        $this->assertNull($values[0]->value);
        $this->assertNull($values[1]->value);
        $this->assertNull($values[2]->value);
    }

    public function testOnlyInternalForms()
    {
        $data = [
            'MetaForm' => [
                'title' => 'Meta Title',
                'description' => 'Meta Description',
            ],
        ];

        $form = new OnlyNestedProductForm();

        $this->assertTrue($form->load($data));

        $this->assertEquals($data['MetaForm']['title'], $form->meta->title);
        $this->assertEquals($data['MetaForm']['description'], $form->meta->description);
    }
}
