<?php

namespace PHPCentroid\Data;

enum PrivilegeTypeEnum: string
{
    case GLOBAL = 'global';
    case PARENT = 'parent';
    case ITEM = 'item';
    case SELF = 'self';
}
