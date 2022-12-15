<?php

declare(strict_types=1);

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

    #[Value(Type::IdString)]
    case GENUG_MAINGROUP_ID = 'site';

    #[Value(Type::IdString)]
    case GENUG_HOMEPAGE_ID = '/';

    #[Value(Type::IdString)]
    case GENUG_HTTP404PAGE_ID = '/http-404';
}
