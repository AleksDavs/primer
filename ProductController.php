<?php

namespace app\controllers;

use app\models\Breadcrumbs;
use app\models\Product;

class ProductController extends AppController {

    public function viewAction(){
        $alias = $this->route['alias'];
        $product = \R::findOne('product', "alias = ? AND status = '1'", [$alias]);
        if(!$product){
            throw new \Exception('Страница не найдена', 404);
        }

        // хлебные крошки
        $breadcrumbs = Breadcrumbs::getBreadcrumbs($product->category_id, $product->title);


        // связанные товары
        $related = \R::getAll("SELECT * FROM related_product JOIN product ON product.id = related_product.related_id WHERE related_product.product_id = ?", [$product->id]);

        // запись в куки запрошенного товара
        $p_model = new Product();
        $p_model->setRecentlyViewed($product->id);

        // просмотренные товары
         $r_viewed = $p_model->getRecentlyViewed();
         $recentlyViewed = null;
        if($r_viewed) {
              $recentlyViewed = \R::find('product', 'id IN ('. \R::genSlots($r_viewed) .') LIMIT 5', $r_viewed);
        }



        // галерея
        $gallery = \R::findAll('gallery','product_id = ?', [$product->id] );
        $bigImg = \R::findOne('gallery', 'product_id = ?', [$product->id]);

        // модификации
         $mods = \R::findAll('modification');
      //  $mods = \R::findAll('modification' , 'product_id = ?', [$product->id]);

       $sizes = \R::findAll('ring_size');

        //$seo = $product->title." -  интернет магазин Золотой Дар - артикул : ".$product->articul;
        $seo = $product->title." заказать, купить";

        $this->setMeta($seo, $product->description, $product->keywords);
        $this->set(compact('product', 'related', 'gallery', 'bigImg', 'recentlyViewed', 'breadcrumbs', 'mods', 'sizes' ));
    }

}