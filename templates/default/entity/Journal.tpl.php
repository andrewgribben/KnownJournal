<article class="h-review known-journal">
    <?php
        if (\Idno\Core\site()->template()->getTemplateType() == 'default') {
            ?>
            <h2 class="p-name">
                <a class="u-url" href="<?= $vars['object']->getDisplayURL() ?>">
                    <?= htmlentities(strip_tags($vars['object']->getTitle()), ENT_QUOTES, 'UTF-8'); ?>
                </a>
			<span class="h-geo">
				<data class="p-latitude" value="<?= $object->lat ?>"></data>
				<data class="p-longitude" value="<?= $object->long ?>"></data>
			</span>
            </h2>
            <?php
        }
    ?>
            <div class=""><h4><span class="fa fa-calendar-o"></span><?= htmlentities(strip_tags($vars['object']->getJournalDate()), ENT_QUOTES, 'UTF-8'); ?></span></h4></div>

<?php if (!empty($vars['object']->getWeather())) {?>
			<div class=""><h4><span class="fa fa-cloud"></span><?= htmlentities(strip_tags($vars['object']->getWeather()), ENT_QUOTES, 'UTF-8'); ?></span></h4></div>
<?php } ?>
			
<?php if (!empty($vars['object']->getPlaceName())) {?>
			<div class=""><h4><span class="fa fa-map-marker"></span><?= htmlentities(strip_tags($vars['object']->getPlaceName()), ENT_QUOTES, 'UTF-8'); ?></span></h4></div>
<?php } ?>

<?php if (!empty($vars['object']->getLocation())) {?>
			<div class=""><h6><span class="fa fa-location-arrow"></span><?= htmlentities(strip_tags($vars['object']->getLocation()), ENT_QUOTES, 'UTF-8'); ?></span></h6></div>
<?php } ?>

<?php

    if (empty($vars['feed_view'])) {


	if (!empty($object->lat)) {
?>

        <div id="map_<?= $object->_id ?>" style="height: 200px;"></div>
<?php } ?>

	<?php
    }
    ?>
    <div class="p-map">
	    <?php
        if ($attachments = $vars['object']->getAttachments()) {
            foreach ($attachments as $attachment) {
                $mainsrc = $attachment['url'];
                if (!empty($vars['object']->thumbnail_large)) {
                    $src = $vars['object']->thumbnail_large;
                } else if (!empty($vars['object']->thumbnail)) { // Backwards compatibility
                    $src = $vars['object']->thumbnail;
                } else {
                    $src = $mainsrc;
                }
                
                // Patch to correct certain broken URLs caused by https://github.com/idno/known/issues/526
                $src = preg_replace('/^(https?:\/\/\/)/', \Idno\Core\site()->config()->getDisplayURL(), $src);
                $mainsrc = preg_replace('/^(https?:\/\/\/)/', \Idno\Core\site()->config()->getDisplayURL(), $mainsrc);
                
                ?>
                <p style="text-align: center">
                    <a href="<?= $this->makeDisplayURL($mainsrc) ?>"><img src="<?= $this->makeDisplayURL($src) ?>" class="u-photo"/></a>
                </p>
            </div>
            <?php
            }
        }
    ?>
	
        <?php
            if (!empty($object->body)) {
                echo $this->autop($this->parseURLs($this->parseHashtags($object->body)));
            }

            if (!empty($object->tags)) {
                ?>

                <p class="tag-row"><i class="icon-tag"></i> <?= $this->parseHashtags($object->tags) ?></p>

            <?php } ?>
    </div>
			
            <div class="p-item h-product">
                                

<?php

    if (empty($vars['feed_view'])) {

        ?>
        <script>
            var map<?=$object->_id?> = L.map('map_<?=$object->_id?>', {
                touchZoom: false,
                scrollWheelZoom: false
            }).setView([<?=$object->lat?>, <?=$object->long?>], 16);
            var layer<?=$object->_id?> = new L.StamenTileLayer("toner-lite");
            map<?=$object->_id?>.addLayer(layer<?=$object->_id?>);
            var marker<?=$object->_id?> = L.marker([<?=$object->lat?>, <?=$object->long?>]);
            marker<?=$object->_id?>.addTo(map<?=$object->_id?>);
            //map<?=$object->_id?>.zoomControl.disable();
            map<?=$object->_id?>.scrollWheelZoom.disable();
            map<?=$object->_id?>.touchZoom.disable();
            map<?=$object->_id?>.doubleClickZoom.disable();
        </script>
    <?php
    }?>            
            
            <div style="display: none;">
                <p class="h-card vcard p-reviewer">
                    <a href="<?= $vars['object']->getOwner()->getURL(); ?>" class="icon-container">
                        <img class="u-logo logo u-photo photo" src="<?= $vars['object']->getOwner()->getIcon(); ?>"/>
                    </a>
                    <a class="p-name fn u-url url" href="<?= $vars['object']->getOwner()->getURL(); ?>"><?= $vars['object']->getOwner()->getName(); ?></a>
                    <a class="u-url" href="<?= $vars['object']->getOwner()->getURL(); ?>">
                        <!-- This is here to force the hand of your MF2 parser --></a>
                </p>
            </div>
</article>
