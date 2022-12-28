<?php

declare(strict_types=1);

/*
 * This file is part of "genug".
 *
 * (c) David Schwarz / Ringsdorf
 * https://davidschwarz.eu
 *
 * License: MIT License
 */

namespace genug\Environment;

/**
 *
 * @author David Ringsdorf http://davidringsdorf.de
 * @license MIT License
 */
enum Preset: string
{
    #[Value(Type::Bool)]
    case GENUG_DEBUG = 'off';

    #[Value(Type::FilePath)]
    case GENUG_DEBUG_LOGFILE = './log/genug.debug.log';

    #[Value(Type::String)]
    case GENUG_CONTENT_TYPE = 'text/html; charset=UTF-8';

    #[Value(Type::IdString)]
    case GENUG_HOMEPAGE_ID = '/';

    #[Value(Type::IdString)]
    case GENUG_HTTP404PAGE_ID = '/http-404';

    #[Value(Type::FilePath)]
    case GENUG_VIEW_FILE = './genug_user/view/index.php';

    #[Value(Type::FilePath)]
    case GENUG_PERSISTENCE_CONTENT_DIR = './genug_user/content';

    #[Value(Type::String)]
    case GENUG_PERSISTENCE_GROUP_FILENAME = '_group.genug';

    #[Value(Type::String)]
    case GENUG_PERSISTENCE_PAGE_FILENAMEEXTENSION = 'page';

    #[Value(Type::String)]
    case GENUG_PERSISTENCE_PAGE_HOMEPAGE_FILENAME = '_home.page';
}
