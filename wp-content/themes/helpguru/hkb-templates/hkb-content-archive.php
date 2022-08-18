<?php global $hkb_current_term_id; ?>

    <style>
		@media screen and (min-width: 768px){
		#site-footer{
            position: absolute !important;
            bottom: -135px;
        }

		}
		
		@media screen and (max-width: 768px){
			div#primary {
				margin-bottom: 35vh;
			}
		}

    </style>

<?php $tax_terms = hkb_get_archive_tax_terms(); ?>
<?php $ht_kb_category_count = count($tax_terms); ?>
<?php $columns = hkb_archive_columns_string(); ?>
<?php $cat_counter = 0; ?>
    <!-- .hkb-archive -->
    <ul class="hkb-archive hkb-archive--<?php echo esc_attr($columns); ?>-cols clearfix">
        <?php foreach ($tax_terms as $key => $tax_term): ?>
            <?php
            //set hkb_current_term_id
            $hkb_current_term_id = $tax_term->term_id;
            ?>

            <li>
                <div class="hkb-category card"
                     data-hkb-cat-icon="<?php echo hkb_has_category_custom_icon($hkb_current_term_id); ?>">
                    <?php
                    $category_thumb_att_id = hkb_get_category_thumb_att_id($hkb_current_term_id);
                    $category_thumb_src = false;

                    if (!empty($category_thumb_att_id) && $category_thumb_att_id != 0) {
                        $category_thumb_obj = wp_get_attachment_image_src($category_thumb_att_id, 'hkb-thumb');
                        $category_thumb_src = $category_thumb_obj[0];
                    }
                    ?>

                    <div class="<?php echo ($category_thumb_src) ? 'd-flex' : '' ?>">

                        <?php if ($category_thumb_src): ?>
                            <div style="margin-right: 1rem;">
                                <img src="<?php echo $category_thumb_src ?>" alt="hkb-category__icon" width="50">
                            </div>

                        <?php endif; ?>
                        <div>
                            <h2 class="hkb-category__title"><a href="<?php echo get_category_link(get_category_by_slug($tax_term->slug))?>"
                                        title="<?php echo sprintf(__('View all posts in %s', 'helpguru'), $tax_term->name) ?>"><?php echo esc_html($tax_term->name); ?></a>
                                <?php if (hkb_archive_display_subcategory_count()) : ?><span
                                        class="hkb-category__count"><?php echo sprintf(_n('1 Article', '%s Articles', $tax_term->count, 'helpguru'), $tax_term->count); ?></span><?php endif; ?>
                            </h2>
                            <?php $ht_kb_tax_desc = $tax_term->description; ?>
                            <?php if (!empty($ht_kb_tax_desc)): ?>
                                <p class="hkb-category__description"><?php echo esc_html($ht_kb_tax_desc); ?></p>
<!--                                <a class="hkb-cat-link" href="--><?php //hkb_term_link($tax_term);?><!--">More info</a>-->
                                <a class="hkb-cat-link" href="<?php echo get_category_link(get_category_by_slug($tax_term->slug))?>">More info</a>
                            <?php endif; ?>
                        </div>




                    </div>

                    <?php
                    //display sub categories
                    hkb_get_template_part('hkb-subcategories', 'archive');
                    ?>

                    <?php $cat_posts = hkb_get_archive_articles($tax_term, null, null, 'kb_home'); ?>

                    <?php if (!empty($cat_posts) && !is_a($cat_posts, 'WP_Error')): ?>

                        <div class="popup">
                            <div class="popupContainer">
                                <a class="popupCloseButton" draggable="false">Close</a>
                                <div class="mobileProducts">
                                    <div class="mobileProductsList">
                                        <ul class="hkb-article-list">
                                            <?php foreach ($cat_posts as $post) : ?>
                                                <li class="hkb-article-list__<?php hkb_post_format_class($post->ID); ?>">
                                                    <a href="<?php echo get_permalink($post->ID); ?>"><?php echo custom_str(get_the_title($post->ID),50); ?></a>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                        <div class="d-flex justify-content-end pt-1">
                                        <a class="hkb-category__view-all " href="<?php echo get_category_link(get_category_by_slug($tax_term->slug))?>">View all</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

<!--                        <ul class="hkb-article-list d-none">-->
<!---->
<!--                        </ul>-->

                    <?php endif; ?>

                </div>
            </li>
        <?php endforeach; ?>
    </ul>
    <!-- /.hkb-archive -->

<?php //$uncat_posts = hkb_get_uncategorized_articles(); ?>
<?php $uncat_posts = array() ?>
<?php if (!empty($uncat_posts) && !is_a($uncat_posts, 'WP_Error')): ?>
    <div class="hkb-category">
        <div class="hkb-category__header">
            <h2 class="hkb-category__title">
                <?php _e('Uncategorized', 'helpguru'); ?>
            </h2>
        </div>
        <ul class="hkb-article-list">
            <?php foreach ($uncat_posts as $post) : ?>
                <li class="hkb-article-list__<?php hkb_post_format_class($post->ID); ?>">
                    <a href="<?php echo get_permalink($post->ID); ?>"><?php echo get_the_title($post->ID); ?></a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; //uncat posts ?>
