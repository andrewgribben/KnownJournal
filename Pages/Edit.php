<?php

    namespace IdnoPlugins\Journal\Pages {

        use Idno\Core\Autosave;

        class Edit extends \Idno\Common\Page {

            function getContent() {

                $this->createGatekeeper();    // This functionality is for logged-in users only

                // Are we loading an entity?
                if (!empty($this->arguments)) {
                    $object = \IdnoPlugins\Journal\Journal::getByID($this->arguments[0]);
                } else {
                    $object = new \IdnoPlugins\Journal\Journal();
                }

                $t = \Idno\Core\site()->template();
                $body = $t->__(array(
                    'object' => $object
                ))->draw('entity/Journal/edit');

                if (empty($vars['object']->_id)) {
                    $title = 'Write a journal';
                } else {
                    $title = 'Edit journal';
                }

                if (!empty($this->xhr)) {
                    echo $body;
                } else {
                    $t->__(array('body' => $body, 'title' => $title))->drawPage();
                }
            }

            function postContent() {
                $this->createGatekeeper();

                $new = false;
                if (!empty($this->arguments)) {
                    $object = \IdnoPlugins\Journal\Journal::getByID($this->arguments[0]);
                }
                if (empty($object)) {
                    $object = new \IdnoPlugins\Journal\Journal();
                }

                if ($object->saveDataFromInput($this)) {
                    (new \Idno\Core\Autosave())->clearContext('journal');
                    $forward = $this->getInput('forward-to', $object->getDisplayURL());
                    $this->forward($forward);
                }

            }

        }

    }
