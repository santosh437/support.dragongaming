<?php $num_comments = get_comments_number(); ?>
<?php if($num_comments>0): ?>
    <li class="hkb-meta__comments">
        <span class="ht-kb-comments-count" title="<?php printf( _n( '1 article comment', '%d article comments', $num_comments, 'helpguru' ), $num_comments ); ?>">
            <?php echo esc_html($num_comments); ?>
        </span>
    </li>
<?php endif; //end num_coments ?>