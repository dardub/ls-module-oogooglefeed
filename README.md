ls-module-oogooglefeed
======================

Google Feed module for Lemonstand


Create a new cms page with the following code


<?

  echo '<?xml version="1.0" encoding="UTF-8"?>';
  
  $products = Db_DbHelper::objectArray('select p.name, p.url_name, p.description as product_description, p.short_description as product_short_description, MAX(c.name) as category,m.name as manufacturer_name, p.x_mpn, p.sku, p.price, p.weight, p.price_rules_compiled, p.grouped, p.grouped_option_desc, GROUP_CONCAT(distinct CONCAT(\'uploaded/public/\',f.disk_name)) as images, max(g.flat_path) as google_category  from shop_products p
                                        left join shop_manufacturers m
                                        on p.manufacturer_id = m.id
                                        inner join db_files f
                                        on f.master_object_id = p.id
                                        left join shop_products_categories r
                                        on r.shop_product_id = p.id
                                        left join shop_categories c
                                        on c.id = r.shop_category_id
                                        left join gd_google_taxonomy g
                                        on g.id = c.gd_google_taxonomy_id
                                        where p.enabled=1
                                        and master_object_class=\'Shop_Product\'
                                        and field=\'images\'
                                        and g.name is not null
                                        group by p.id'
                                      );
  
?>
<rss xmlns:g="http://base.google.com/ns/1.0" version="2.0">
  <channel>
     <title><?= $this->page->title ?></title> 
    <link><?= site_url('/') ?></link> 
    <description><?= $this->page->description ?></description> 
    <xhtml:meta xmlns:xhtml="http://www.w3.org/1999/xhtml" name="robots" content="noindex" />
    
    <? foreach($products as $product): ?>
      <item>
        <title><![CDATA[<?= h($product->name) ?> <? if($product->grouped==1&&$product->grouped_option_desc) echo ' - '.h($product->grouped_option_desc) ?> ]]></title>
        <link><?= site_url('/product/'.$product->url_name) ?></link>
        <? if($product->product_description): ?>
          <description><![CDATA[<?= h(strip_tags($product->product_description)) ?>]]></description>
        <? else: ?>
          <description><![CDATA[<?= h($product->product_short_description) ?>]]></description>
        <? endif ?>
        <g:id><![CDATA[<?= $product->sku ?>]]></g:id>
        <? if($product->manufacturer_name): ?>
          <g:brand><![CDATA[<?= $product->manufacturer_name ?>]]></g:brand>
        <? endif ?>
        <? if($product->x_mpn): ?>
          <g:mpn><![CDATA[<?= $product->x_mpn ?>]]></g:mpn>
        <? else: ?>
          <g:mpn><![CDATA[<?= $product->sku ?>]]></g:mpn>
        <? endif ?>
        <g:condition>new</g:condition>
        <g:price><?= $product->price ?> USD</g:price>
        <g:availability>available for order</g:availability>
        <? $images = explode(',',$product->images) ?>
        <g:image_link><?= site_url($images[0]) ?></g:image_link>
          <? 
            $num_images = count($images) <= 10 ? count($images) : 10;
            if($num_images > 1):
              for($i=1;$i<$num_images;$i++): 
         ?>
              <g:additional_image_link><?= site_url($images[$i]) ?></g:additional_image_link>
            <? endfor ?>
          <? endif ?>
        <g:product_type><![CDATA[<?= h($product->google_category)  ?>]]></g:product_type>
        <g:category><![CDATA[<?= h($product->category)  ?>]]></g:category>
          <g:shipping_weight><?= $product->weight ?> lb</g:shipping_weight>
      </item>
    <? endforeach ?>
  </channel>
</rss>




Add the following to the page post-action:

header("Content-Type: application/xml");



TODO: 
1. Create custom page action
2. Add password requirement for page
