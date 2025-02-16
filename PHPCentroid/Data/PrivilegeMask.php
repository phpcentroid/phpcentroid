<?php

namespace PHPCentroid\Data;

enum PrivilegeMask: int
{
    case NONE = 0;
    case READ = 1;
    case CREATE = 2;
    case UPDATE = 4;
    case DELETE = 8;
    case EXECUTE = 16;
    case ALL = 31;
}
