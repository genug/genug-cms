<?php

use genug\HttpResponceException\HttpPermanentRedirect;
use Uri\WhatWg\Url;

throw new HttpPermanentRedirect(new Url('https://example.com/'));