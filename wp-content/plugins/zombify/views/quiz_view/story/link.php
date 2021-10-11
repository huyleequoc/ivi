<div class="zf-story_item zf-link">
    <h2 class="zf_title"><?php echo $story_data["link_headline"]; ?></h2>
    <div class="zf-list_description"><?php echo $story_data["link_description"]; ?></div>
    <div class="zf-link">
        <strong><?php esc_html_e("Source", "zombify"); ?> </strong><a href="<?php echo $story_data["link_link"]; ?>" target="_blank" rel="noopener"><?php echo $story_data["link_link"]; ?></a>
    </div>
</div>