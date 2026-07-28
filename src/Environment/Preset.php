<?php

declare(strict_types=1);

/*
 * This file is part of "genug".
 *
 * (c) David J. Schwarz
 * https://davidschwarz.eu
 *
 * License: MIT License
 */

namespace genug\Environment;

/**
 *
 * @author David J. Schwarz <https://davidschwarz.eu/>
 * @license MIT License
 * @internal
 * @deprecated Use Config instead.
 */
enum Preset: string
{
    case GENUG_DEBUG = 'off';

    case GENUG_DEBUG_LOGFILE = './log/genug.debug.log';

    case GENUG_CONTENT_TYPE = 'text/html; charset=UTF-8';

    case GENUG_HOMEPAGE_ID = '/';

    case GENUG_HTTP404PAGE_ID = '/http-404';

    case GENUG_VIEW_FILE = './genug_user/view/index.php';

    case GENUG_PERSISTENCE_CONTENT_DIR = './genug_user/content';

    case GENUG_PERSISTENCE_GROUP_FILENAME = '_group.genug';

    case GENUG_PERSISTENCE_PAGE_FILENAMEEXTENSION = 'page';

    case GENUG_PERSISTENCE_PAGE_HOMEPAGE_FILENAME = '_home.page';
}
