<?php 
/*
*
* Breadcrumbs template
*
*/ 
?>


<?php if(hkb_show_knowledgebase_breadcrumbs()): ?>
<!-- .hkb-breadcrumbs -->
<?php $breadcrumbs_paths = ht_kb_get_ancestors(); ?>
    <?php foreach ($breadcrumbs_paths as $index => $paths): ?>
        <ol class="hkb-breadcrumbs" itemscope itemtype="http://schema.org/BreadcrumbList">
            <?php $last_item_index = count($paths)-1; ?>
            <?php foreach ($paths as $key => $component): ?>
                <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
                    <a itemprop="item" href="<?php echo esc_url($component['link']); ?>">
                        <span itemprop="name"><?php echo esc_html($component['label']); ?></span>
                    </a>
                    <meta itemprop="position" content="<?php echo esc_attr($key+1); ?>" />
                </li>               
            <?php endforeach; ?>
        </ol>
    <?php endforeach; ?>
<!-- /.hkb-breadcrumbs -->
<?php endif; ?>