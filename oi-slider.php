<?php
/*
Plugin Name: Oi-bootstrap-slider
Plugin URI: 
Description: Plugin allow to create pages from the site's frontend 
Version: 1.0
Author: Alexander Kiryushin
Author URI: 
License: GPL2
*/

include 'inc/shortcodes.php';
include 'templates/slider.php';


//подключение скрипта в админке
function oi_slide_scripts() {
	wp_register_script( 'oi_slide_scripts', plugin_dir_url( __FILE__ ) . 'js/script.js' );
	wp_enqueue_script( 'oi_slide_scripts' );

}

add_action( 'admin_enqueue_scripts', 'oi_slide_scripts' );

/**
 * Функция подключения стилей и скриптов
 */
function bootstrap_styles_scripts() {
	// подключение стилей
	wp_enqueue_style( 'bootstrap', plugin_dir_url( __FILE__ ) . 'inc/bootstrap/css/bootstrap.min.css' );

	// подключение скриптов
	wp_enqueue_script( 'bootstrap_js', plugin_dir_url( __FILE__ ) . 'inc/bootstrap/js/bootstrap.min.js', array( 'jquery' ) );

}

add_action( 'wp_enqueue_scripts', 'bootstrap_styles_scripts' );

add_action( 'init', 'frontend_slider' );

/**
 * Создаем тип поста "Слайдер"
 */
function frontend_slider() {
	$labels = array(
		'name'               => 'Слайдер',
		'singular_name'      => 'Слайдер',
		'add_new'            => 'Добавить Слайдер',
		'add_new_item'       => 'Добавить Слайдер',
		'edit_item'          => 'Редактировать Слайдер',
		'new_item'           => 'Новый слайдер',
		'all_items'          => 'Все слайдеры',
		'view_item'          => 'Просмотреть Слайдер',
		'search_items'       => 'Поиск Слайдеров',
		'not_found'          => 'Не найдено Слайдеров',
		'not_found_in_trash' => 'Не найдено в корзине',
		'menu_name'          => 'Слайдер',
	);


	$args = array(
		'labels'             => $labels,
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => true,
		'rewrite'            => true,
		'capability_type'    => 'post',
		'has_archive'        => true,
		'menu_icon'          => 'dashicons-images-alt',
		'hierarchical'       => false,
		'menu_position'      => null,
		'supports'           => array( 'title', 'thumbnail', 'excerpt', 'custom-fields' )
	);

	register_post_type( 'slider', $args );


}

add_action( 'add_meta_boxes', 'slider_boxes', 1 );
//Добавляем метабоксы
function slider_boxes() {
	add_meta_box( 'slider_extra_fields', 'Добавить слайд: ', 'slider_fields_box_func', 'slider', 'normal', 'high' );
}

/**
 * @param $post
 */
function slider_fields_box_func( $post ) {
	$slider = array();
	$slider = json_decode( $post->post_content, true );

	?>
	<div id="slides_container">
	<?php
	echo 'Шорткод этого слайдера: [oislider id="' . $post->ID . '"]<br>';
	$counter = 1;
	for ( $i = 0; $i < count( $slider['slide'] ); $i ++ ) {
		if ( strlen( $slider['slide'][ $i ] ) > 0 ) {

			echo '<div class="slide_place">

                    Изображение: <input id="default_featured_image_' . $counter . '" class="img_url" type="text"
                                        size="60" name="slider[slide][]"
                                        value="' . esc_attr( $slider['slide'][ $i ] ) . '"/>
                    <button type="button" class="button insert-media add_media"
                            data-editor="default_featured_image_' . $counter . '"><span
                            class="wp-media-buttons-icon"></span> Загрузить фотографию
                    </button>
                    <button class="remove_slide" onclick="remove_slide(this); return false;">Удалить</button>
                    <div>Заголовок слайда: <input class="img_caption" type="text" size="60" name="slider[title][]"
                                                 value="' . esc_attr( $slider['title'][ $i ] ) . '"/></div>
                   <div>' . __( 'Подпись', 'xxx' ) . ': <input class="img_caption" type="text" size="60" name="slider[caption][]"
                                        value="' . esc_attr( $slider['caption'][ $i ] ) . '"/></div>
                    <hr>
                    <hr>
                </div>';

			$counter ++;
		}
	}
	echo '<a class = "add_slide" title = "Добавить слайд" onclick = "add_slide();return false;" href = "#" ><img
                class="add_slide_img" src = "' . plugin_dir_url( __FILE__ ) . '/img/plus_add_blue.png"
                width = "35px;" alt = "Добавить слайд" ></a >
        <input type = "hidden" name = "extra_fields_nonce" value = "' . wp_create_nonce( __FILE__ ) . '" />
    </div >';
	?>
	<script>
		var counter = 1;
		if (jQuery('.img_url').last().attr('id')) {
			counter = jQuery('.img_url').last().attr('id').split('_');
		}


		if (Number(counter[3])) {
			counter = Number(counter[3]) + 1;
		}

		function add_slide() {
			jQuery('.add_slide').before('<div class="slide_place">Изображение:<input id="default_featured_image_' + counter + '" type="text" size="60" name="slider[slide][]" /> <button type="button" class="button insert-media add_media" data-editor="default_featured_image_' + counter + '"><span class="wp-media-buttons-icon"></span>  Загрузить фотографию</button> <button class="remove_slide" >Удалить</button><br>Заголовок слайда: <input class="img_caption" type="text" size="60" name="slider[title][]" value="" /><br>Подпись: <input class="img_caption" type="text" size="60" name="slider[caption][]" /><br><hr><hr></div>');
			counter++;
			jQuery('.remove_slide').last().attr('onclick', 'remove_slide(this); return false;');
		}
		function remove_slide(obj) {
			jQuery(obj).parent().remove();
			return false;
		}
		jQuery('#slides_container').on('click', '.insert-media', function () {
			jQuery(this).parent().children('.img_url').val('');
		});
	</script>
	<?php
}

/**
 * @param $post_id
 */
function save_slides( $post_id ) {
	global $wpdb;

	if ( ! wp_verify_nonce( $_POST['extra_fields_nonce'], __FILE__ ) ) {
		return false;
	} // проверка
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return false;
	} // выходим если это автосохранение
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return false;
	} // выходим если юзер не имеет право редактировать запись


	if ( ! wp_is_post_revision( $post_id ) ) {
		// удаляем этот хук, чтобы он не создавал бесконечного цикла
		remove_action( 'save_post', 'save_slides' );
	}

	$my_post       = array();
	$my_post['ID'] = $post_id;
	$slides_count  = count( $_POST['slider']['title'] );
	for ( $i = 0; $i < $slides_count; $i ++ ) {
		$doc = new DOMDocument();
		@$doc->loadHTML( $_POST['slider']['slide'][ $i ] );
		$tags = $doc->getElementsByTagName( 'img' );

		foreach ( $tags as $tag ) {
			$_POST['slider']['slide'][ $i ] = $tag->getAttribute( 'src' );
		}

	}

	$my_post['post_content'] = wp_json_encode( $_POST['slider'], JSON_UNESCAPED_UNICODE );
	wp_update_post( $my_post );
	add_action( 'save_post', 'save_slides' );

}


add_action( 'save_post', 'save_slides' );


