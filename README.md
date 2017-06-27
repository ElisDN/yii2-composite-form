# Composite Form for Yii2 Framework

The extension allows to create nested form models.

## Installation

Install with composer:

```bash
composer require elisdn/yii2-composite-form
```

## Usage samples

Create any simple form model for SEO information:

```php
class MetaForm extends Model
{
    public $title;
    public $description;
    public $keywords;

    public function rules()
    {
        return [
            [['title'], 'string', 'max' => 255],
            [['description', 'keywords'], 'string'],
        ];
    }
}
```

and for characteristics:

```php
class ValueForm extends Model
{
    public $value;

    private $_characteristic;

    public function __construct(Characteristic $characteristic, $config = [])
    {
        $this->_characteristic = $characteristic;
        parent::__construct($config);
    }

    public function rules()
    {
        return [
            ['value', 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'value' => $this->_characteristic->name,
        ];
    }

    public function getCharacteristicId()
    {
        return $this->_characteristic->id;
    }
}
```

And create a composite form model which uses both as an internal forms:

```php
use elisdn\compositeForm\CompositeForm;

/**
 * @property MetaForm $meta
 * @property ValueForm[] $values
 */
class ProductCreateForm extends CompositeForm
{
    public $code;
    public $name;

    public function __construct($config = [])
    {
        $this->meta = new MetaForm();
        $this->values = array_map(function (Characteristic $characteristic) {
            return new ValueForm($characteristic);
        }, Characteristic::find()->orderBy('sort')->all());
        parent::__construct($config);
    }

    public function rules()
    {
        return [
            [['code', 'name'], 'required'],
            [['code', 'name'], 'string', 'max' => 255],
            [['code'], 'unique', 'targetClass' => Product::className()],
        ];
    }

    protected function internalForms()
    {
        return ['meta', 'values'];
    }
}
```

That is all. After all just use external `$form` and internal `$form->meta` and `$form->values` models for `ActiveForm`:

```php
<?php $form = ActiveForm::begin(); ?>

    <h2>Common</h2>
    
    <?= $form->field($model, 'code')->textInput() ?>
    <?= $form->field($model, 'name')->textInput() ?>
    
    <h2>Characteristics</h2>
    
    <?php foreach ($model->values as $i => $valueForm): ?>
        <?= $form->field($valueForm, '[' . $i . ']value')->textInput() ?>
    <?php endforeach; ?>
    
    <h2>SEO</h2>

    <?= $form->field($model->meta, 'title')->textInput() ?>
    <?= $form->field($model->meta, 'description')->textarea(['rows' => 2]) ?>
    
    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

<?php ActiveForm::end(); ?>
```

and for your application's services:

```php
class ProductManageService
{
    private $products;
    
    public function __construct(ProductRepository $products)
    {
        $this->products = $products;
    }

    public function create(ProductCreateForm $form)
    {
        $product = Product::create(
            $form->code,
            $form->name,
            new Meta(
                $form->meta->title,
                $form->meta->description,
                $form->meta->keywords
            )
        );

        foreach ($form->values as $valueForm) {
            $product->changeValue($valueForm->getCharacteristicId(), $valueForm->value);
        }

        $this->products->save($product);

        return $product->id;
    }

    ...
}
```

with simple controller for web:

```php
class ProductController extends \yii\web\Controller
{
    ...

    public function actionCreate()
    {
        $form = new ProductCreateForm();

        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            $id = $this->service->create($form);
            return $this->redirect(['view', 'id' => $id]);
        }

        return $this->render('create', [
            'model' => $form,
        ]);
    }
}
```

or for API:

```php
class ProductController extends \yii\rest\Controller
{
    ...

    public function actionCreate()
    {
        $form = new ProductCreateForm();
        $form->load(Yii::$app->request->getBodyParams());

        if ($form->validate()) {
            $id = $this->service->create($form);
            $response = Yii::$app->getResponse();
            $response->setStatusCode(201);
            $response->getHeaders()->set('Location', Url::to(['view', 'id' => $id], true));
            return [];
        }

        return $form;
    }
}
```