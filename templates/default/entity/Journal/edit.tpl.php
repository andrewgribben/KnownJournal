<?= $this->draw('entity/edit/header'); ?>
<?php

    $autosave = new \Idno\Core\Autosave();
    if (!empty($vars['object']->body)) {
        $body = $vars['object']->body;
    } else {
        $body = $autosave->getValue('journal', 'bodyautosave');
    }
    if (!empty($vars['object']->title)) {
        $title = $vars['object']->title;
    } else {
        $title = $autosave->getValue('journal', 'title');
    }
    if (!empty($vars['object']->journalDate)) {
        $journalDate = $vars['object']->journalDate;
    } else {
        $journalDate = $autosave->getValue('journal', 'journalDate');
    }
    if (empty($vars['object']->journalDate)) {
		date_default_timezone_set("Europe/Amsterdam"); 
        $journalDate = date('l d-m-Y H:i', time());
    }
    if (!empty($vars['object']->weather)) {
        $weather = $vars['object']->weather;
    } else {
        $weather = $autosave->getValue('journal', 'weather');
    }
	if (!empty($vars['object']->placename)) {
        $placename = $vars['object']->placename;
    } else {
        $placename = $autosave->getValue('journal', 'placename');
    }

	if (!empty($vars['object']->address)) {
        $address = $vars['object']->address;
    } else {
        $address = $autosave->getValue('journal', 'address');
    }
	
    if (!empty($vars['object'])) {
        $object = $vars['object'];
    } else {
        $object = false;
    }

    /* @var \Idno\Core\Template $this */

?>
    <form action="<?= $vars['object']->getURL() ?>" method="post" enctype="multipart/form-data">

        <div class="row">

            <div class="col-md-8 col-md-offset-2 edit-pane">


                <?php

                    if (empty($vars['object']->_id)) {

                        ?>
                        <h4>New Journal</h4>
                    <?php

                    } else {

                        ?>
                        <h4>Edit Journal</h4>
                    <?php

                    }

                ?>


                <div class="content-form">

                    <style>
                        .productCategory-block, .rating-block {
                            margin-bottom: 1em;
                        }
                    </style>
                    <label for="title">Title</label>
                    <input type="text" name="title" id="title" placeholder="Give your Journal a title" value="<?= htmlspecialchars($title) ?>" class="form-control"/>                    
                    
                    <label for="journalDate">Date</label>
                    <input type="text" name="journalDate" id="journalDate" placeholder="What is the date this journal is about?" value="<?= htmlspecialchars($journalDate) ?>" class="form-control"/>                    

					<label for="weather">Weather</label>
                    <input type="text" name="weather" id="weather" placeholder="What was the weather like?" value="<?= htmlspecialchars($weather) ?>" class="form-control"/>                    

					
            <div id="geoplaceholder">
                <p style="text-align: center; color: #4c93cb;">
                    Hang tight ... searching for your location.
                </p>

                <div class="geospinner">
		    <div class="rect1"></div>
		    <div class="rect2"></div>
		    <div class="rect3"></div>
		    <div class="rect4"></div>
		    <div class="rect5"></div>
		</div>
            </div>
            <div id="geofields" class="map" style="display:none">
                <div class="geolocation content-form">

                    <p>
                        <label for="placename">
                            Location<br>
                        </label>
                        <input type="text" name="placename" id="placename" class="form-control" placeholder="Where are you?" value="<?= htmlspecialchars($vars['object']->placename) ?>" />
                        <input type="hidden" name="lat" id="lat" value="<?= $vars['object']->lat ?>"/>
                        <input type="hidden" name="long" id="long" value="<?= $vars['object']->long ?>"/>
                    </p>

                    <p>
                        <label for="user_address">Address<br>
                            <small>You can edit the address if it's wrong.</small>
                        </label>
                        <input type="text" name="user_address" id="user_address" class="form-control" value="<?= htmlspecialchars($vars['object']->address) ?>"/>
                        <input type="hidden" name="address" id="address" />
                    </p>

                    <div id="checkinMap" style="height: 250px" ></div>
                </div>
            </div>

				<?php

                    if (empty($vars['object']->_id)) {

                        ?>
                        <div id="photo-preview"></div>
                        <p>
                                <span class="btn btn-primary btn-file">
                                        <i class="fa fa-camera"></i> <span
                                        id="photo-filename">Select a photo</span> <input type="file" name="photo"
                                                                                         id="photo"
                                                                                         class="col-md-9 form-control"
                                                                                         accept="image/*;capture=camera"
                                                                                         onchange="photoPreview(this)"/>

                                    </span>
                        </p>

                    <?php

                    }

                ?>
					
                <label for="body">Text</label>
                <?= $this->__([
                    'name' => 'body',
                    'value' => $body,
                    'object' => $object,
                    'wordcount' => true
                ])->draw('forms/input/richtext')?>
                <?= $this->draw('entity/tags/input'); ?>

				<?php echo $this->drawSyndication('article', $vars['object']->getPosseLinks()); ?>
                <?php if (empty($vars['object']->_id)) { ?><input type="hidden" name="forward-to" value="<?= \Idno\Core\site()->config()->getDisplayURL() . 'content/all/'; ?>" /><?php } ?>
                
                <?= $this->draw('content/access'); ?>

                <p class="button-bar ">
	                
                    <?= \Idno\Core\site()->actions()->signForm('/journal/edit') ?>
                    <input type="button" class="btn btn-cancel" value="Cancel" onclick="tinymce.EditorManager.execCommand('mceRemoveEditor',true, 'body'); hideContentCreateForm();"/>
                    <input type="submit" class="btn btn-primary" value="Publish"/>

                </p>

            </div>

        </div>
    </form>

    <script>
        //if (typeof photoPreview !== function) {
        function photoPreview(input) {

            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $('#photo-preview').html('<img src="" id="photopreview" style="display:none; width: 400px">');
                    $('#photo-filename').html('Choose different photo');
                    $('#photopreview').attr('src', e.target.result);
                    $('#photopreview').show();
                }

                reader.readAsDataURL(input.files[0]);
            }
        }
        //}
    </script>

    <div id="bodyautosave" style="display:none"></div>
<?= $this->draw('entity/edit/footer'); ?>

<script src="<?= \Idno\Core\Idno::site()->config()->getDisplayURL() ?>IdnoPlugins/Journal/journal.js"></script>