<div class="zf-story_item">
    <h2 class="zf_title"><?php echo $story_data["embed_title"]; ?></h2>
    <?php
    if( $story_data["embed_url"] ) {
        ?>
        <figure class="zf-story_media zf-media-wrapper zf-type-embed">
            <div class="zf-embedded-url"><?php echo Zombify_BaseQuiz::renderEmbed( $story_data, true ); ?></div>
            <?php if (isset($story_data["embed_credit"]) && $story_data["embed_credit"] != '') { ?>
                <figcaption class="zf-figcaption"><cite><a href="<?php echo $story_data["embed_credit"]; ?>" target="_blank" class="zf-media_credit" rel="nofollow noopener"><?php echo $story_data["embed_credit_text"] ? $story_data["embed_credit_text"] : __('Credit', 'zombify'); ?></a></cite></figcaption>
            <?php } ?>
        </figure>
    <?php
    }

    if( $story_data["embed_description"] ) {
        ?>
        <div class="zf-list_description"><?php echo $story_data["embed_description"]; ?></div>
    <?php
    }
    ?>
</div>