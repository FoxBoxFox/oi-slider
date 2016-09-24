<?php

/**
 * Функция, реализующая шорткод
 *
 * @param $atts string - входные параметры, указанные в виде атрибутов шорткода
 *
 * @return string
 */
function insert_slider($atts)
{
    $atts = shortcode_atts(array(
        'id' => 0,

    ), $atts);
    $slider_json = get_post($atts['id'], ARRAY_A);
    $slider = $slider_json['post_content'];
    $slider = json_decode($slider, true);
    $count_slides = count($slider['slide']);
    ob_start();
    ?>
    <div id="myCarousel<?php echo $atts['id']; ?>" class="carousel slide" data-ride="carousel">
        <ol class="carousel-indicators">
            <?php for ($i = 0; $i < $count_slides; $i++) { ?>
                <li data-target="#myCarousel<?php echo $atts['id']; ?>"
                    data-slide-to="<?php echo $i; ?>" <?php if ($i == 0) {
                    echo 'class="active"';
                } ?>></li>
            <?php } ?>

        </ol>

        <div class="carousel-inner" role="listbox">
            <?php for ($i = 0; $i < $count_slides; $i++) { ?>
                <div class="item <?php echo $i; ?> <?php if ($i == 0) {
                    echo 'active';
                } ?>">
                    <img src=<?php echo $slider['slide'][$i]; ?> alt=<?php echo $slider['title'][$i]; ?>>
                    <h3 class="slide_title"><?php echo $slider['title'][$i]; ?></h3>
                    <p class="desc"><?php echo $slider['caption'][$i]; ?></p>
                </div>
            <?php } ?>

        </div>


        <a class="left carousel-control" href="#myCarousel<?php echo $atts['id']; ?>" role="button" data-slide="prev">
            <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
            <span class="sr-only">Previous</span>
        </a>
        <a class="right carousel-control" href="#myCarousel<?php echo $atts['id']; ?>" role="button" data-slide="next">
            <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
            <span class="sr-only">Next</span>
        </a>
    </div>

    <?php
    $out = ob_get_contents();
    ob_end_clean();
    return $out;

}

add_shortcode('oislider', 'insert_slider');