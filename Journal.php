<?php

    namespace IdnoPlugins\Journal {

        use Idno\Core\Autosave;

        class Journal extends \Idno\Common\Entity
        {

            function getTitle()
            {
                if (empty($this->title)) return 'Untitled';

                return $this->title;
            }

            function getDescription()
            {
                if (!empty($this->body)) return $this->body;

                return '';
            }

			function getJournalDate()
            {
                if (!empty($this->journalDate)) return $this->journalDate;

                return '';
            }
			
			function getPlaceName()
            {
				if (!empty($this->placename)) return $this->placename;
            }
			
			function getLocation()
			{
				if (!empty($this->address)) return $this->address;
			}
			
			function getWeather()
			{
				if (!empty($this->weather)) return $this->weather;
			}

            function getURL()
            {

                // If we have a URL override, use it
                if (!empty($this->url)) {
                    return $this->url;
                }

                if (!empty($this->canonical)) {
                    return $this->canonical;
                }

                if (!$this->getSlug() && ($this->getID())) {
                    return \Idno\Core\site()->config()->url . 'journal/' . $this->getID() . '/' . $this->getPrettyURLTitle();
                } else {
                    return parent::getURL();
                }

            }

            /**
             * Journal objects have type 'journal'
             * @return 'journal'
             */
            function getActivityStreamsObjectType()
            {
                return 'journal';
            }

            /**
             * Retrieve icon
             * @return mixed|string
             */
            function getIcon()
            {
                $xpath = new \DOMXPath(@\DOMDocument::loadHTML($this->getDescription()));
                $src   = $xpath->evaluate("string(//img/@src)");
                if (!empty($src)) {
                    return $src;
                }

                return parent::getIcon();
            }

            function saveDataFromInput()
            {

                if (empty($this->_id)) {
                    $new = true;
                } else {
                    $new = false;
                }
				
                $body = \Idno\Core\site()->currentPage()->getInput('body');
                if (!empty($body)) {

                    $this->body            = $body;
                    $this->title           = \Idno\Core\site()->currentPage()->getInput('title');
					$this->journalDate	   = \Idno\Core\site()->currentPage()->getInput('journalDate');
					$this->weather	       = \Idno\Core\site()->currentPage()->getInput('weather');
					$lat                   = \Idno\Core\site()->currentPage()->getInput('lat');
					$long                  = \Idno\Core\site()->currentPage()->getInput('long');
					$user_address          = \Idno\Core\site()->currentPage()->getInput('user_address');
					$placename             = \Idno\Core\site()->currentPage()->getInput('placename');

					$this->lat = $lat;
					$this->long = $long;
					$this->address = $user_address;
					$this->placename = $placename;

                    $access                = \Idno\Core\site()->currentPage()->getInput('access');
                    $this->setAccess($access);

                    if ($time = \Idno\Core\site()->currentPage()->getInput('created')) {
                        if ($time = strtotime($time)) {
                            $this->created = $time;
                        }
                    }
    
                    if ($new) {
                        if (!empty($_FILES['photo']['tmp_name'])) {
                            if (\Idno\Entities\File::isImage($_FILES['photo']['tmp_name'])) {
                                
                                // Extract exif data so we can rotate
                                if (is_callable('exif_read_data') && $_FILES['photo']['type'] == 'image/jpeg') {
                                    try {
                                        if (function_exists('exif_read_data')) {
                                            if ($exif = exif_read_data($_FILES['photo']['tmp_name'])) {
                                                $this->exif = base64_encode(serialize($exif)); // Yes, this is rough, but exif contains binary data that can not be saved in mongo
                                            }
                                        }
                                    } catch (\Exception $e) {
                                        $exif = false;
                                    }
                                } else {
                                    $exif = false;
                                }
                                
                                if ($photo = \Idno\Entities\File::createFromFile($_FILES['photo']['tmp_name'], $_FILES['photo']['name'], $_FILES['photo']['type'], true, true)) {
                                    $this->attachFile($photo);

                                    // Now get some smaller thumbnails, with the option to override sizes
                                    $sizes = \Idno\Core\site()->events()->dispatch('photo/thumbnail/getsizes', new \Idno\Core\Event(array('sizes' => array('large' => 800, 'medium' => 400, 'small' => 200))));
                                    $eventdata = $sizes->data();
                                    foreach ($eventdata['sizes'] as $label => $size) {

                                        $filename = $_FILES['photo']['name'];

                                        if ($thumbnail = \Idno\Entities\File::createThumbnailFromFile($_FILES['photo']['tmp_name'], "{$filename}_{$label}", $size, false)) {
                                            $varname        = "thumbnail_{$label}";
                                            $this->$varname = \Idno\Core\site()->config()->url . 'file/' . $thumbnail;

                                            $varname        = "thumbnail_{$label}_id";
                                            $this->$varname = substr($thumbnail, 0, strpos($thumbnail, '/'));
                                        }
                                    }
                                }
                            } else {
                                \Idno\Core\site()->session()->addErrorMessage('This doesn\'t seem to be an image ..');
                            }
                        }
						
						if (!empty($lat) && !empty($long)) {
							$this->lat       = $lat;
							$this->long      = $long;
							$this->placename = $placename;
							$this->body      = $body;
							$this->address   = $user_address;
							$this->setAccess($access);
							$this->tags = $tags;
							if ($this->publish($new)) {
								if ($new && $access == 'PUBLIC') {
									\Idno\Core\Webmention::pingMentions($this->getURL(), \Idno\Core\Idno::site()->template()->parseURLs($this->getTitle() . ' ' . $this->getDescription()));
								}

								return true;
							}
						} else {
							\Idno\Core\Idno::site()->session()->addErrorMessage('You can\'t save an empty checkin.');
						}
                    }

                    if ($this->publish($new)) {

                        $autosave = new Autosave();
                        $autosave->clearContext('journal');

                        \Idno\Core\Webmention::pingMentions($this->getURL(), \Idno\Core\site()->template()->parseURLs($this->getTitle() . ' ' . $this->getDescription()));

                        return true;
                    }
                } else {
                    \Idno\Core\site()->session()->addErrorMessage('You can\'t save an empty entry.');
                }

                return false;
            }

			
			
            function deleteData()
            {
                \Idno\Core\Webmention::pingMentions($this->getURL(), \Idno\Core\site()->template()->parseURLs($this->getTitle() . ' ' . $this->getDescription()));
            }

			            /**
             * Given a latitude and longitude, reverse geocodes it into a structure including name, address,
             * city, etc
             *
             * @param $latitude
             * @param $longitude
             * @return bool|mixed
             */
            static function queryLatLong($latitude, $longitude)
            {

                $query    = self::getNominatimEndpoint() . "reverse?lat={$latitude}&lon={$longitude}&format=json&zoom=18";
                $response = array();

                $http_response = \Idno\Core\Webservice::get($query)['content'];

                if (!empty($http_response)) {
                    if ($contents = @json_decode($http_response)) {
                        if (!empty($contents->address)) {
                            $addr             = (array)$contents->address;
                            $response['name'] = implode(', ', array_slice($addr, 0, 1));
                        }
                        if (!empty($contents->display_name)) {
                            $response['display_name'] = $contents->display_name;
                        }

                        return $response;
                    }
                }

                return false;

            }

            /**
             * Takes an address and returns OpenStreetMap data via Nominatim, including latitude and longitude
             *
             * @param string $address
             * @return array|bool
             */
            static function queryAddress($address, $limit = 1)
            {

                $query = self::getNominatimEndpoint() . "search?q=" . urlencode($address) . "&format=json";

                $http_response = \Idno\Core\Webservice::get($query)['content'];

                if (!empty($http_response)) {
                    if ($contents = @json_decode($http_response)) {
                        $contents              = (array)$contents;
                        $contents              = (array)array_pop($contents); // This will have been an array wrapped in an array
                        $contents['latitude']  = $contents['lat'];
                        $contents['longitude'] = $contents['lon'];

                        return $contents;
                    }
                }

                return false;

            }

            /**
             * Returns the OpenStreetMap Nominatim endpoint that we should be using
             * @return string
             */
            static function getNominatimEndpoint()
            {
                if ($config = \Idno\Core\Idno::site()->config()->checkin) {
                    if (!empty($config['endpoint'])) {
                        return $config['endpoint'];
                    }
                }

                return 'http://nominatim.openstreetmap.org/';
            }

			
        }

    }
?>