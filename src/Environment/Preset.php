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
    case GENUG_DEBUG_LOGFILE = '../log/genug.debug.log';

    #[Value(Type::String)]
    case GENUG_CONTENT_TYPE = 'text/html; charset=UTF-8';

    #[Value(Type::IdString)]
    case GENUG_HOMEPAGE_ID = '/';

    #[Value(Type::IdString)]
    case GENUG_HTTP404PAGE_ID = '/http-404';
}
