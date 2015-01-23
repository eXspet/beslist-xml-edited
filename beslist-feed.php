<?php
/**
 * Function to get xml file content of  categories Products feeds 
 */
function beslist_getBeslistFeed($category_id) {
header ("Content-Type:text/xml");  
    global $wpdb; 
    $posts_table = $wpdb->prefix . 'posts';
//Thanks Devon from http://stackoverflow.com/questions/28113566/sql-query-by-ifempty-get-for-wordpress
$sql = "
        SELECT $wpdb->posts.ID, $wpdb->posts.post_title, $wpdb->posts.post_content, $wpdb->posts.post_excerpt, $wpdb->posts.post_name
        FROM $wpdb->posts
        LEFT JOIN $wpdb->term_relationships ON
        ($wpdb->posts.ID = $wpdb->term_relationships.object_id)
        LEFT JOIN $wpdb->term_taxonomy ON
        ($wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id)
        WHERE $wpdb->posts.post_status = 'publish'
        AND $wpdb->posts.post_type = 'product'
        AND $wpdb->term_taxonomy.taxonomy = 'product_cat'";

        if(!empty($_GET["category"])) {
        $sql .= " AND $wpdb->term_taxonomy.term_id = '".htmlspecialchars($_GET["category"], ENT_QUOTES)."'";
        }
        $sql .= " ORDER BY post_date DESC";
    $products = $wpdb->get_results($sql);
    $xml = '';
    $xml .= '<?xml version="1.0" encoding="utf-8" ?>' . PHP_EOL;
    $xml .= '<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
	xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9
	http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd"
	xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;
//echo '<sitemap>'.PHP_EOL;
    foreach ($products as $prod) {   
        $product = get_product($prod->ID);

        $size = sizeof( get_the_terms( $prod->ID, 'product_cat' ) );

        $search = array('@<script[^>]*?>.*?</script>@si',  // Strip out javascript 
                       '@<style[^>]*?>.*?</style>@siU',    // Strip style tags properly 
                       '@<![\s\S]*?--[ \t\n\r]*>@'         // Strip multi-line comments including CDATA 
        ); 

        $omschrijving = preg_replace($search, '', $prod->post_content);
 		$excerpt = preg_replace($search, '', $prod->post_excerpt);
        $xml .= '<urun>'. PHP_EOL;
		
		$term_list = wp_get_post_terms($prod->ID,'product_cat',array('fields'=>'ids'));
		$cat_id = (int)$term_list[0];
		$category = get_term ($cat_id, 'product_cat');
		$xml .= '<kategori>' .$category->name. '</kategori>' . PHP_EOL;
		$xml .= '<baslik>' .get_the_title($prod->ID). '</baslik>' . PHP_EOL;
        $xml .= '<fiyat>' .$product->price. '</fiyat>' . PHP_EOL;
		$xml .= '<kur>USD</kur>' . PHP_EOL;
        $xml .= '<stok>' . $product->stock . '</stok>' . PHP_EOL;
        $xml .= '<skod>' . $product->sku.'</skod>' . PHP_EOL;
	 $args = array(
	   'post_type' => 'attachment',
	   'numberposts' => -1,
	   'post_status' => null,
	   'post_parent' => $prod->ID
	  );
	
	  $attachments = get_posts( $args );
		 if ( $attachments ) {
		 $i = 0;
			foreach ( $attachments as $attachment ) {
			$i++;
		$image_attributes = wp_get_attachment_image_src( $attachment->ID, 'full' );	
		$xml .= '<resim'.$i.'>'.$image_attributes[0].'</resim'.$i.'>'. PHP_EOL;
			  }
		 }
        $xml .= '<tanim><![CDATA[' .htmlspecialchars($omschrijving). ']]></tanim>'. PHP_EOL;
	$xml .= '<kisatanim><![CDATA[' .htmlspecialchars($excerpt). ']]></kisatanim>'. PHP_EOL;


        $xml .= '</urun>'. PHP_EOL;
    }
// echo '</sitemap>'.PHP_EOL;
//echo '</sitemap>'.PHP_EOL;
    $xml .= '</urlset>';




    echo $xml;
}
?>
