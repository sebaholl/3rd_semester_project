<?php
/* Setup */
add_action('after_setup_theme', function () {
  add_theme_support('title-tag');
  add_theme_support('post-thumbnails');
  add_theme_support('html5', ['search-form','comment-form','comment-list','gallery','caption']);
  add_theme_support('custom-logo', ['height'=>48,'width'=>160,'flex-width'=>true,'flex-height'=>true]);
  register_nav_menus(['primary'=>__('Primary Menu','omniora'),'footer'=>__('Footer Menu','omniora')]);
  add_image_size('avatar-96', 96, 96, true);
});

/* Assets */
add_action('wp_enqueue_scripts', function () {
  $style = get_stylesheet_directory().'/style.css';
  wp_enqueue_style('omniora', get_stylesheet_uri(), [], file_exists($style)?filemtime($style):'1.0.0');

  $shop = get_template_directory().'/Shopstyle.css';
  if (file_exists($shop)) {
    wp_enqueue_style('shop-style', get_template_directory_uri().'/Shopstyle.css', [], filemtime($shop));
  }

  $js = get_template_directory().'/assets/main.js';
  if (file_exists($js)) {
    wp_enqueue_script('omniora', get_template_directory_uri().'/assets/main.js', [], filemtime($js), true);
  }
});

/* ACF Options */
if (function_exists('acf_add_options_page')) {
  acf_add_options_page([
    'page_title'=>'Theme Settings',
    'menu_title'=>'Theme Settings',
    'menu_slug'=>'omniora-theme-settings',
    'capability'=>'edit_posts',
    'redirect'=>false
  ]);
}
function omni_opt($key, $fallback=''){
  return function_exists('get_field') ? (get_field($key,'option') ?: $fallback) : $fallback;
}

/* Polylang strings */
if (function_exists('pll_register_string')) {
  foreach ([
    // UI & CTAs
    'Shop Now','BUY NOW','Read','Read Blog','Search','Language',

    // Blog & archives
    'Blog','From our Blog','From the Blog','All Blogs','Posts pagination',
    'No posts yet.','No blog posts found.','No posts in this category yet.','No posts with this tag yet.',
    '« Prev','Next »','No image','Post taxonomy','Pages:',

    // Single post
    'Related posts','← Back to Blog',

    // Testimonials
    'What customers say','No testimonials yet.','No testimonials for this product yet.',
    'Rating: %d out of 5',
    'Rating %1$.1f / 5 from %2$d testimonials'
  ] as $s) {
    pll_register_string('omniora',$s,'theme');
  }
}

/* Localized price */
function omniora_format_price($amount){
  $amount = (float)$amount;
  $lang = function_exists('pll_current_language') ? pll_current_language('slug') : '';
  $symbol = ($lang === 'cs') ? 'Kč' : 'kr.';
  return number_format_i18n($amount, 0).' '.$symbol;
}

/* Testimonials: avg rating (supports post_object/relationship fields) */
function omniora_get_product_testimonial_rating($product_id){
  $product_id = (int)$product_id; if(!$product_id) return ['avg'=>0,'count'=>0,'stars'=>''];
  $lang = function_exists('pll_current_language') ? pll_current_language('slug') : '';
  $cache_key = 'omniora_t_avg_'.$product_id.($lang ? "_$lang" : '');
  if(($cached=get_transient($cache_key))!==false) return $cached;

  $keys=['product_ref','related_product','product','related_shoe'];
  $meta=['relation'=>'OR'];
  foreach($keys as $k){
    $meta[] = ['key'=>$k,'value'=>$product_id,'compare'=>'='];          // Post Object
    $meta[] = ['key'=>$k,'value'=>'"'.$product_id.'"','compare'=>'LIKE']; // Relationship (serialized)
  }

  $args=[
    'post_type'=>'testimonial',
    'posts_per_page'=>-1,
    'post_status'=>'publish',
    'fields'=>'ids',
    'meta_query'=>$meta
  ];
  if($lang) $args['lang']=$lang;

  $q=new WP_Query($args);
  $sum=0; $count=0;
  if($q->have_posts() && function_exists('get_field')){
    foreach($q->posts as $tid){
      $r=(int)get_field('rating',$tid);
      if($r>0){ $sum+=$r; $count++; }
    }
  }
  wp_reset_postdata();

  $avg = $count ? round($sum/$count, 1) : 0.0;
  $stars = str_repeat('★',(int)round($avg)).str_repeat('☆',5-(int)round($avg));
  $data=['avg'=>$avg,'count'=>$count,'stars'=>$stars];
  set_transient($cache_key,$data,12*HOUR_IN_SECONDS);
  return $data;
}

/* Clear rating cache on testimonial save */
add_action('save_post_testimonial', function($post_id){
  if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
  if(wp_is_post_revision($post_id)) return;
  if(!function_exists('get_field')) return;

  $keys=['product_ref','related_product','product','related_shoe'];
  $ids=[];
  foreach($keys as $k){
    $v=get_field($k,$post_id);
    if(is_array($v)){
      foreach($v as $x){
        $ids[] = is_object($x)&&isset($x->ID) ? (int)$x->ID : (is_numeric($x)?(int)$x:0);
      }
    } else {
      $ids[] = is_object($v)&&isset($v->ID) ? (int)$v->ID : (is_numeric($v)?(int)$v:0);
    }
  }
  $ids=array_values(array_unique(array_filter($ids)));
  if(!$ids) return;

  global $wpdb;
  foreach($ids as $pid){
    $like=$wpdb->esc_like('omniora_t_avg_'.$pid).'%';
    $wpdb->query($wpdb->prepare("DELETE FROM $wpdb->options WHERE option_name LIKE %s",'_transient_'.$like));
    $wpdb->query($wpdb->prepare("DELETE FROM $wpdb->options WHERE option_name LIKE %s",'_transient_timeout_'.$like));
  }
});

/* [testimonials] shortcode (language-aware) */
add_shortcode('testimonials', function($atts){
  $atts=shortcode_atts(['limit'=>6,'columns'=>3,'order'=>'DESC','product'=>''],$atts,'testimonials');
  $args=[
    'post_type'=>'testimonial',
    'posts_per_page'=>(int)$atts['limit'],
    'post_status'=>'publish',
    'orderby'=>'date',
    'order'=>strtoupper($atts['order'])==='ASC'?'ASC':'DESC'
  ];
  if(function_exists('pll_current_language')) $args['lang']=pll_current_language('slug');

  if(!empty($atts['product'])){
    $pid=is_numeric($atts['product'])?(int)$atts['product']:0;
    if(!$pid){
      $p=get_page_by_path(sanitize_title($atts['product']),OBJECT,['shoe']);
      if($p) $pid=(int)$p->ID;
    }
    if($pid){
      $keys=['product_ref','related_product','product','related_shoe'];
      $meta=['relation'=>'OR'];
      foreach($keys as $k){
        $meta[]=['key'=>$k,'value'=>$pid,'compare'=>'='];
        $meta[]=['key'=>$k,'value'=>'"'.$pid.'"','compare'=>'LIKE'];
      }
      $args['meta_query']=$meta;
    }
  }

  $q=new WP_Query($args);
  if(!$q->have_posts()){
    return '<p class="muted">'.(function_exists('pll__')?pll__('No testimonials yet.'):__('No testimonials yet.','omniora')).'</p>';
  }

  $cols=max(1,min(4,(int)$atts['columns']));
  ob_start();
  echo '<div class="testimonial-grid cols-'.esc_attr($cols).'">';
  while($q->have_posts()){
    $q->the_post();
    $id=get_the_ID();
    $quote=function_exists('get_field')?(string)get_field('quote',$id):get_the_excerpt($id);
    $name=get_the_title($id);
    $rating=function_exists('get_field')?(int)get_field('rating',$id):5;
    $avatar=has_post_thumbnail($id)
      ? get_the_post_thumbnail($id,'avatar-96',['class'=>'t-card__avatar','loading'=>'lazy','alt'=>esc_attr(($name?:__('Customer','omniora')).' portrait')])
      : '';
    $r=max(1,min(5,$rating));
    $stars=str_repeat('★',$r).str_repeat('☆',5-$r);

    echo '<article class="t-card">';
      if($avatar) echo '<div class="t-card__media">'.$avatar.'</div>';
      echo '<div class="t-card__body">';
        echo '<div class="t-card__rating" aria-label="'.esc_attr(sprintf(function_exists('pll__')?pll__('Rating: %d out of 5'):__('Rating: %d out of 5','omniora'),$r)).'">'.$stars.'</div>';
        if($quote) echo '<blockquote class="t-card__quote"><p>'.wp_kses_post($quote).'</p></blockquote>';
        if($name)  echo '<h3 class="t-card__name">'.esc_html($name).'</h3>';
      echo '</div>';
    echo '</article>';
  }
  echo '</div>';
  wp_reset_postdata();
  return ob_get_clean();
});


/* Survey */
add_action('init', function () {
  if (empty($_POST['omniora_survey_nonce'])) return;
  if (!wp_verify_nonce($_POST['omniora_survey_nonce'], 'omniora_survey')) return;
  if (!empty($_POST['website'])) return; 

  $lang = function_exists('pll_current_language') ? pll_current_language('slug') : '';
  $get  = function($k){ return isset($_POST[$k]) ? wp_unslash($_POST[$k]) : ''; };

  $sport    = sanitize_text_field($get('sport'));
  $level    = sanitize_text_field($get('level'));
  $terrain  = isset($_POST['terrain']) && is_array($_POST['terrain']) ? array_map('sanitize_text_field', $_POST['terrain']) : [];
  $width    = sanitize_text_field($get('width'));
  $budget   = sanitize_text_field($get('budget'));
  $features = isset($_POST['features']) && is_array($_POST['features']) ? array_map('sanitize_text_field', $_POST['features']) : [];
  $email    = sanitize_email($get('email'));
  $consent  = !empty($_POST['consent']) ? 1 : 0;

  $title = sprintf('%s %s', __('Survey','omniora'), date_i18n('Y-m-d H:i'));
  $post_id = wp_insert_post([
    'post_type'   => 'survey_entry',
    'post_title'  => $title,
    'post_status' => 'publish',
  ]);

  if ($post_id && !is_wp_error($post_id)) {
    update_post_meta($post_id, 'sport', $sport);
    update_post_meta($post_id, 'level', $level);
    update_post_meta($post_id, 'terrain', implode(', ', $terrain));
    update_post_meta($post_id, 'width', $width);
    update_post_meta($post_id, 'budget', $budget);
    update_post_meta($post_id, 'features', implode(', ', $features));
    update_post_meta($post_id, 'email', $consent ? $email : '');
    update_post_meta($post_id, 'consent', $consent);

    if ($lang && function_exists('pll_set_post_language')) {
      pll_set_post_language($post_id, $lang);
    }
  }

  $redirect = add_query_arg('survey', 'thanks', wp_get_referer() ?: home_url('/'));
  wp_safe_redirect($redirect);
  exit;
});

