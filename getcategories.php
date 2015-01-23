  <?php
  $taxonomy     = 'product_cat';
  $orderby      = 'name';  
  $show_count   = 0;      // 1 for yes, 0 for no
  $pad_counts   = 0;      // 1 for yes, 0 for no
  $hierarchical = 1;      // 1 for yes, 0 for no  
  $title        = '';  
  $empty        = 0;
$args = array(
  'taxonomy'     => $taxonomy,
  'orderby'      => $orderby,
  'show_count'   => $show_count,
  'pad_counts'   => $pad_counts,
  'hierarchical' => $hierarchical,
  'title_li'     => $title,
  'hide_empty'   => $empty
);
?>
<?php $all_categories = get_categories( $args );
//print_r($all_categories);
foreach ($all_categories as $cat) {
    //print_r($cat);
    if($cat->category_parent == 0) {
        $category_id = $cat->term_id;

        echo "<br /><ul><li><a href='".get_site_url()."/?beslistxml=feed&category=".$cat->term_id."' target='_blank'>XML feed for ". $cat->name ."</a>";  ?>


        <?php
        $args2 = array(
          'taxonomy'     => $taxonomy,
          'child_of'     => 0,
          'parent'       => $category_id,
          'orderby'      => $orderby,
          'show_count'   => $show_count,
          'pad_counts'   => $pad_counts,
          'hierarchical' => $hierarchical,
          'title_li'     => $title,
          'hide_empty'   => $empty
        );
        $sub_cats = get_categories( $args2 );
        if($sub_cats) {
            foreach($sub_cats as $sub_category) {
                echo  "<li><a href='".get_site_url()."/?beslistxml=feed&category=".$sub_category->term_id."' target='_blank'>".$sub_category->name."</a></li>" ;
            }
				echo "</li></ul>";
        } ?>


    <?php }     
}
?>
