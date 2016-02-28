<?php

    namespace IdnoPlugins\Journal {

        class Main extends \Idno\Common\Plugin {

            function registerPages() {
                \Idno\Core\site()->addPageHandler('/journal/edit/?', '\IdnoPlugins\Journal\Pages\Edit');
                \Idno\Core\site()->addPageHandler('/journal/edit/([A-Za-z0-9]+)/?', '\IdnoPlugins\Journal\Pages\Edit');
                \Idno\Core\site()->addPageHandler('/journal/delete/([A-Za-z0-9]+)/?', '\IdnoPlugins\Journal\Pages\Delete');
                \Idno\Core\site()->addPageHandler('/journal/([A-Za-z0-9]+)/.*', '\Idno\Pages\Entity\View');
                \Idno\Core\site()->addPageHandler('/journal/callback/?', '\IdnoPlugins\Journal\Pages\Callback');
				
				\Idno\Core\site()->template()->extendTemplate('shell/head','journal/head');
				}

            /**
             * Get the total file usage
             * @param bool $user
             * @return int
             */
            function getFileUsage($user = false) {

                $total = 0;

                if (!empty($user)) {
                    $search = ['user' => $user];
                } else {
                    $search = [];
                }

                if ($journals = journal::get($search,[],9999,0)) {
                    foreach($journals as $journal) {
                        /* @var review $review */
                        if ($journal instanceof journal) {
                            if ($attachments = $journal->getAttachments()) {
                                foreach($attachments as $attachment) {
                                    $total += $attachment['length'];
                                }
                            }
                        }
                    }
                }

                return $total;
            }

        }

    }
