Yii Recommendation Widget
=========================

Provides personal content recommendation for each visitor

Usage
-----

Integration is really simple.

Just copy IndirectaWidget.php to your widget directory.

And place the following code to display personal recommendations. 

```php
<?php $this->widget('IndirectaWidget', array(
    'siteid'=>'<YOUR SITEID>',
    'markup'=>'titles',
    'title'=>'Related articles',
    'id'=>'http://www.articles.com/best-article-ever.htm',
)); ?>
```

To get further details please send us your contact email at http://indirecta.ru
