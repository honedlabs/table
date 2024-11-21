<?php

namespace Honed\Table\Actions\Enums;

enum Context: string
{
    case Inline = 'inline';
    case Bulk = 'bulk';
    case Page = 'page';
}
