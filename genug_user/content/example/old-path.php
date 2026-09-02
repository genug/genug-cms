<?php

use genug\HttpResponceException\HttpPermanentRedirect;
use genug\Page\PageId;

throw new HttpPermanentRedirect(new PageId('/example/new-path'));