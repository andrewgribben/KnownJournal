<?php

    namespace IdnoPlugins\Journal {

        class ContentType extends \Idno\Common\ContentType {

            public $title = 'Journal';
            public $category_title = 'Journals';
            public $entity_class = 'IdnoPlugins\\Journal\\Journal';
            public $logo = '<i class="icon-align-left"></i>';
            public $indieWebContentType = array('article','journal');

        }

    }
